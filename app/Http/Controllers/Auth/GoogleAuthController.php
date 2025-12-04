<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

/**
 * ====================================================================
 * GOOGLE AUTHENTICATION CONTROLLER
 * ====================================================================
 * 
 * Controller xử lý đăng nhập bằng Google OAuth 2.0
 * 
 * FLOW ĐĂNG NHẬP:
 * 1. Frontend gửi Google ID Token đến API
 * 2. Backend xác thực token với Google
 * 3. Tạo account mới hoặc lấy account đã tồn tại
 * 4. Trả về API Token (Sanctum) cho frontend
 * 
 * ====================================================================
 */
class GoogleAuthController extends Controller
{
    /**
     * ----------------------------------------------------------------
     * ĐĂNG NHẬP BẰNG GOOGLE (Dành cho React Frontend)
     * ----------------------------------------------------------------
     * 
     * Endpoint: POST /api/auth/google
     * 
     * Frontend sử dụng @react-oauth/google để lấy credential (ID Token)
     * sau đó gửi lên endpoint này để xác thực và lấy API token
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function handleGoogleCallback(Request $request): JsonResponse
    {
        // ============================================================
        // BƯỚC 1: Validate request
        // ============================================================
        $request->validate([
            'credential' => 'required|string', // Google ID Token từ frontend
        ]);

        try {
            // ============================================================
            // BƯỚC 2: Xác thực Google ID Token
            // ============================================================
            // Sử dụng Socialite để verify token với Google
            $googleUser = Socialite::driver('google')
                ->stateless()
                ->userFromToken($this->getAccessTokenFromIdToken($request->credential));

            // ============================================================
            // BƯỚC 3: Tìm hoặc tạo account
            // ============================================================
            $account = $this->findOrCreateAccount($googleUser);

            // ============================================================
            // BƯỚC 4: Tạo API Token (Sanctum)
            // ============================================================
            // Xóa các token cũ (optional - để đảm bảo chỉ có 1 session)
            $account->tokens()->delete();
            
            // Tạo token mới
            $token = $account->createToken('google-auth-token')->plainTextToken;

            // ============================================================
            // BƯỚC 5: Trả về response
            // ============================================================
            return response()->json([
                'success' => true,
                'message' => 'Đăng nhập thành công!',
                'data' => [
                    'user' => [
                        'id' => $account->id,
                        'name' => $account->name,
                        'email' => $account->email,
                        'avatar' => $account->avatar,
                        'created_at' => $account->created_at,
                    ],
                    'token' => $token,
                    'token_type' => 'Bearer',
                ],
            ], 200);

        } catch (\Exception $e) {
            // Log lỗi để debug
            Log::error('Google Auth Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Đăng nhập thất bại. Vui lòng thử lại.',
                'error' => config('app.debug') ? $e->getMessage() : 'Authentication failed',
            ], 401);
        }
    }

    /**
     * ----------------------------------------------------------------
     * ĐĂNG NHẬP BẰNG GOOGLE (Verify ID Token trực tiếp)
     * ----------------------------------------------------------------
     * 
     * Endpoint: POST /api/auth/google
     * 
     * Phương thức này verify ID Token trực tiếp với Google API
     * mà không cần chuyển đổi sang Access Token
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function verifyGoogleToken(Request $request): JsonResponse
    {
        $request->validate([
            'credential' => 'required|string',
        ]);

        try {
            // ============================================================
            // Verify ID Token trực tiếp với Google
            // ============================================================
            $client = new \Google\Client(['client_id' => config('services.google.client_id')]);
            $payload = $client->verifyIdToken($request->credential);

            if (!$payload) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token không hợp lệ',
                ], 401);
            }

            // ============================================================
            // Lấy thông tin từ payload
            // ============================================================
            $googleId = $payload['sub'];
            $email = $payload['email'];
            $name = $payload['name'];
            $avatar = $payload['picture'] ?? null;

            // ============================================================
            // Tìm hoặc tạo account
            // ============================================================
            $account = Account::where('google_id', $googleId)->first();

            if (!$account) {
                // Kiểm tra email đã tồn tại chưa
                $account = Account::where('email', $email)->first();
                
                if ($account) {
                    // Liên kết Google với account hiện có
                    $account->update([
                        'google_id' => $googleId,
                        'avatar' => $avatar,
                    ]);
                } else {
                    // Tạo account mới
                    $account = Account::create([
                        'google_id' => $googleId,
                        'name' => $name,
                        'email' => $email,
                        'avatar' => $avatar,
                        'email_verified_at' => now(),
                    ]);
                }
            } else {
                // Cập nhật thông tin
                $account->update([
                    'name' => $name,
                    'avatar' => $avatar,
                ]);
            }

            // ============================================================
            // Tạo token
            // ============================================================
            $account->tokens()->delete();
            $token = $account->createToken('google-auth-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Đăng nhập thành công!',
                'data' => [
                    'user' => [
                        'id' => $account->id,
                        'name' => $account->name,
                        'email' => $account->email,
                        'avatar' => $account->avatar,
                        'role' => $account->role,
                        'email_verified_at' => $account->email_verified_at,
                    ],
                    'token' => $token,
                    'token_type' => 'Bearer',
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Google Token Verification Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Xác thực thất bại',
                'error' => config('app.debug') ? $e->getMessage() : 'Verification failed',
            ], 401);
        }
    }

    /**
     * ----------------------------------------------------------------
     * ĐĂNG XUẤT
     * ----------------------------------------------------------------
     * 
     * Endpoint: POST /api/auth/logout
     * 
     * Xóa token hiện tại của account
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        // Xóa token hiện tại
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đăng xuất thành công!',
        ]);
    }

    /**
     * ----------------------------------------------------------------
     * LẤY THÔNG TIN USER HIỆN TẠI
     * ----------------------------------------------------------------
     * 
     * Endpoint: GET /api/auth/me
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function me(Request $request): JsonResponse
    {
        $account = $request->user();
        
        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $account->id,
                    'name' => $account->name,
                    'email' => $account->email,
                    'avatar' => $account->avatar,
                    'created_at' => $account->created_at,
                ],
            ],
        ]);
    }

    /**
     * ================================================================
     * PRIVATE METHODS
     * ================================================================
     */

    /**
     * Tìm account đã tồn tại hoặc tạo account mới
     *
     * @param \Laravel\Socialite\Contracts\User $googleUser
     * @return Account
     */
    private function findOrCreateAccount($googleUser): Account
    {
        // Tìm account theo google_id
        $account = Account::where('google_id', $googleUser->getId())->first();

        if ($account) {
            // Cập nhật thông tin nếu có thay đổi
            $account->update([
                'name' => $googleUser->getName(),
                'avatar' => $googleUser->getAvatar(),
            ]);
            return $account;
        }

        // Tìm account theo email (account đã đăng ký bằng email thường)
        $account = Account::where('email', $googleUser->getEmail())->first();

        if ($account) {
            // Liên kết tài khoản Google với tài khoản đã tồn tại
            $account->update([
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
            ]);
            return $account;
        }

        // Tạo account mới
        return Account::create([
            'google_id' => $googleUser->getId(),
            'name' => $googleUser->getName(),
            'email' => $googleUser->getEmail(),
            'avatar' => $googleUser->getAvatar(),
        ]);
    }

    /**
     * Chuyển đổi ID Token thành Access Token
     *
     * @param string $idToken
     * @return string
     */
    private function getAccessTokenFromIdToken(string $idToken): string
    {
        return $idToken;
    }
}
