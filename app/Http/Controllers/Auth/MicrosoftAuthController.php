<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * ====================================================================
 * MICROSOFT AUTHENTICATION CONTROLLER
 * ====================================================================
 * 
 * Controller xử lý đăng nhập bằng Microsoft OAuth 2.0
 * 
 * FLOW ĐĂNG NHẬP:
 * 1. Frontend gửi Microsoft Access Token đến API
 * 2. Backend xác thực token với Microsoft Graph API
 * 3. Tạo account mới hoặc lấy account đã tồn tại
 * 4. Trả về API Token (Sanctum) cho frontend
 * 
 * AZURE AD SETUP:
 * 1. Vào Azure Portal > Azure Active Directory > App registrations
 * 2. Tạo app mới với redirect URI
 * 3. Thêm API permissions: Microsoft Graph > User.Read
 * 4. Lấy Client ID và tạo Client Secret
 * 
 * ====================================================================
 */
class MicrosoftAuthController extends Controller
{
    /**
     * Microsoft Graph API endpoint
     */
    private const MICROSOFT_GRAPH_URL = 'https://graph.microsoft.com/v1.0/me';
    
    /**
     * Microsoft OAuth URLs
     */
    private const MICROSOFT_OAUTH_URL = 'https://login.microsoftonline.com';

    /**
     * ----------------------------------------------------------------
     * ĐĂNG NHẬP BẰNG MICROSOFT (Verify Access Token)
     * ----------------------------------------------------------------
     * 
     * Endpoint: POST /api/auth/microsoft
     * 
     * Frontend sử dụng @azure/msal-react hoặc @azure/msal-browser 
     * để lấy access_token, sau đó gửi lên endpoint này
     * 
     * Request Body:
     * {
     *     "access_token": "Microsoft Access Token từ MSAL"
     * }
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function handleMicrosoftLogin(Request $request): JsonResponse
    {
        // ============================================================
        // BƯỚC 1: Validate request
        // ============================================================
        $request->validate([
            'access_token' => 'required|string',
        ]);

        try {
            // ============================================================
            // BƯỚC 2: Lấy thông tin user từ Microsoft Graph API
            // ============================================================
            $microsoftUser = $this->getMicrosoftUser($request->access_token);

            if (!$microsoftUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token không hợp lệ hoặc đã hết hạn',
                ], 401);
            }

            // ============================================================
            // BƯỚC 3: Tìm hoặc tạo account
            // ============================================================
            $account = $this->findOrCreateAccount($microsoftUser);

            // ============================================================
            // BƯỚC 4: Tạo API Token (Sanctum)
            // ============================================================
            $account->tokens()->delete();
            $token = $account->createToken('microsoft-auth-token')->plainTextToken;

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
                        'role' => $account->role,
                        'email_verified_at' => $account->email_verified_at,
                        'created_at' => $account->created_at,
                    ],
                    'token' => $token,
                    'token_type' => 'Bearer',
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Microsoft Auth Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Đăng nhập thất bại. Vui lòng thử lại.',
                'error' => config('app.debug') ? $e->getMessage() : 'Authentication failed',
            ], 401);
        }
    }

    /**
     * ----------------------------------------------------------------
     * ĐĂNG NHẬP BẰNG MICROSOFT (Authorization Code Flow)
     * ----------------------------------------------------------------
     * 
     * Endpoint: POST /api/auth/microsoft/code
     * 
     * Sử dụng khi frontend gửi authorization code thay vì access token
     * Backend sẽ đổi code lấy access token
     * 
     * Request Body:
     * {
     *     "code": "Authorization code từ Microsoft",
     *     "redirect_uri": "URI đã dùng khi lấy code"
     * }
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function handleAuthorizationCode(Request $request): JsonResponse
    {
        $request->validate([
            'code' => 'required|string',
            'redirect_uri' => 'required|string',
        ]);

        try {
            // ============================================================
            // BƯỚC 1: Đổi authorization code lấy access token
            // ============================================================
            $tokenData = $this->exchangeCodeForToken(
                $request->code,
                $request->redirect_uri
            );

            if (!$tokenData || !isset($tokenData['access_token'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể xác thực với Microsoft',
                ], 401);
            }

            // ============================================================
            // BƯỚC 2: Lấy thông tin user từ Microsoft Graph
            // ============================================================
            $microsoftUser = $this->getMicrosoftUser($tokenData['access_token']);

            if (!$microsoftUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể lấy thông tin người dùng',
                ], 401);
            }

            // ============================================================
            // BƯỚC 3: Tìm hoặc tạo account
            // ============================================================
            $account = $this->findOrCreateAccount($microsoftUser);

            // ============================================================
            // BƯỚC 4: Tạo API Token
            // ============================================================
            $account->tokens()->delete();
            $token = $account->createToken('microsoft-auth-token')->plainTextToken;

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
                        'created_at' => $account->created_at,
                    ],
                    'token' => $token,
                    'token_type' => 'Bearer',
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Microsoft Auth Code Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Đăng nhập thất bại',
                'error' => config('app.debug') ? $e->getMessage() : 'Authentication failed',
            ], 401);
        }
    }

    /**
     * ----------------------------------------------------------------
     * LẤY URL ĐĂNG NHẬP MICROSOFT
     * ----------------------------------------------------------------
     * 
     * Endpoint: GET /api/auth/microsoft/url
     * 
     * Trả về URL để redirect user đến trang đăng nhập Microsoft
     * Sử dụng khi không dùng MSAL ở frontend
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function getAuthUrl(Request $request): JsonResponse
    {
        $redirectUri = $request->query('redirect_uri', config('services.microsoft.redirect'));
        $tenant = config('services.microsoft.tenant', 'common');
        
        $params = [
            'client_id' => config('services.microsoft.client_id'),
            'response_type' => 'code',
            'redirect_uri' => $redirectUri,
            'response_mode' => 'query',
            'scope' => 'openid profile email User.Read',
            'state' => csrf_token(),
        ];

        $authUrl = self::MICROSOFT_OAUTH_URL . "/{$tenant}/oauth2/v2.0/authorize?" . http_build_query($params);

        return response()->json([
            'success' => true,
            'data' => [
                'auth_url' => $authUrl,
            ],
        ]);
    }

    /**
     * ================================================================
     * PRIVATE METHODS
     * ================================================================
     */

    /**
     * Lấy thông tin user từ Microsoft Graph API
     *
     * @param string $accessToken
     * @return array|null
     */
    private function getMicrosoftUser(string $accessToken): ?array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->get(self::MICROSOFT_GRAPH_URL);

            if ($response->successful()) {
                $userData = $response->json();
                
                // Lấy avatar từ Microsoft Graph (nếu có)
                $avatar = $this->getMicrosoftAvatar($accessToken);
                
                return [
                    'id' => $userData['id'],
                    'name' => $userData['displayName'] ?? $userData['givenName'] . ' ' . $userData['surname'],
                    'email' => $userData['mail'] ?? $userData['userPrincipalName'],
                    'avatar' => $avatar,
                ];
            }

            Log::warning('Microsoft Graph API Error: ' . $response->body());
            return null;

        } catch (\Exception $e) {
            Log::error('Microsoft Graph Request Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Lấy avatar từ Microsoft Graph API
     *
     * @param string $accessToken
     * @return string|null
     */
    private function getMicrosoftAvatar(string $accessToken): ?string
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
            ])->get(self::MICROSOFT_GRAPH_URL . '/photo/$value');

            if ($response->successful()) {
                // Chuyển đổi binary thành base64 data URI
                $imageData = base64_encode($response->body());
                $contentType = $response->header('Content-Type') ?? 'image/jpeg';
                return "data:{$contentType};base64,{$imageData}";
            }

            return null;

        } catch (\Exception $e) {
            // Không có avatar - không phải lỗi nghiêm trọng
            return null;
        }
    }

    /**
     * Đổi authorization code lấy access token
     *
     * @param string $code
     * @param string $redirectUri
     * @return array|null
     */
    private function exchangeCodeForToken(string $code, string $redirectUri): ?array
    {
        $tenant = config('services.microsoft.tenant', 'common');
        $tokenUrl = self::MICROSOFT_OAUTH_URL . "/{$tenant}/oauth2/v2.0/token";

        try {
            $response = Http::asForm()->post($tokenUrl, [
                'client_id' => config('services.microsoft.client_id'),
                'client_secret' => config('services.microsoft.client_secret'),
                'code' => $code,
                'redirect_uri' => $redirectUri,
                'grant_type' => 'authorization_code',
                'scope' => 'openid profile email User.Read',
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning('Microsoft Token Exchange Error: ' . $response->body());
            return null;

        } catch (\Exception $e) {
            Log::error('Microsoft Token Exchange Request Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Tìm account đã tồn tại hoặc tạo account mới
     *
     * @param array $microsoftUser
     * @return Account
     */
    private function findOrCreateAccount(array $microsoftUser): Account
    {
        $microsoftId = $microsoftUser['id'];
        $email = $microsoftUser['email'];
        $name = $microsoftUser['name'];
        $avatar = $microsoftUser['avatar'];

        // Tìm account theo microsoft_id
        $account = Account::where('microsoft_id', $microsoftId)->first();

        if ($account) {
            // Cập nhật thông tin nếu có thay đổi
            $account->update([
                'name' => $name,
                'avatar' => $avatar,
            ]);
            return $account;
        }

        // Tìm account theo email (account đã đăng ký bằng email/Google)
        $account = Account::where('email', $email)->first();

        if ($account) {
            // Liên kết tài khoản Microsoft với tài khoản đã tồn tại
            $account->update([
                'microsoft_id' => $microsoftId,
                'avatar' => $avatar ?? $account->avatar,
            ]);
            return $account;
        }

        // Tạo account mới
        return Account::create([
            'microsoft_id' => $microsoftId,
            'name' => $name,
            'email' => $email,
            'avatar' => $avatar,
        ]);
    }
}
