<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IdeaRegistrationController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\MicrosoftAuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| AUTHENTICATION ROUTES
| ---------------------
| POST   /api/auth/google           - Đăng nhập bằng Google (sử dụng credential từ @react-oauth/google)
| POST   /api/auth/google/token     - Đăng nhập bằng Google (sử dụng access_token)
| POST   /api/auth/microsoft        - Đăng nhập bằng Microsoft (sử dụng access_token từ MSAL)
| POST   /api/auth/microsoft/code   - Đăng nhập bằng Microsoft (sử dụng authorization code)
| GET    /api/auth/microsoft/url    - Lấy URL đăng nhập Microsoft
| POST   /api/auth/logout           - Đăng xuất (cần auth)
| GET    /api/auth/me               - Lấy thông tin user hiện tại (cần auth)
|
| IDEA ROUTES
| -----------
| GET    /api/ideas             - Lấy danh sách ý tưởng
| POST   /api/ideas             - Tạo ý tưởng mới
| GET    /api/ideas/{id}        - Lấy chi tiết ý tưởng
|
*/

// ========================================================================
// AUTHENTICATION ROUTES (Public - không cần đăng nhập)
// ========================================================================
Route::prefix('auth')->group(function () {
    
    /**
     * Đăng nhập bằng Google
     * 
     * Method: POST
     * URL: /api/auth/google
     * 
     * Request Body:
     * {
     *     "credential": "Google ID Token từ @react-oauth/google"
     * }
     * 
     * Response:
     * {
     *     "success": true,
     *     "message": "Đăng nhập thành công!",
     *     "data": {
     *         "user": { ... },
     *         "token": "Bearer token",
     *         "token_type": "Bearer"
     *     }
     * }
     */
    Route::post('/google', [GoogleAuthController::class, 'verifyGoogleToken']);
    
    /**
     * Đăng nhập bằng Google Access Token
     * (Sử dụng khi dùng useGoogleLogin hook)
     */
    Route::post('/google/token', [GoogleAuthController::class, 'handleGoogleCallback']);

    /**
     * Đăng nhập bằng Microsoft
     * 
     * Method: POST
     * URL: /api/auth/microsoft
     * 
     * Request Body:
     * {
     *     "access_token": "Microsoft Access Token từ MSAL (@azure/msal-react)"
     * }
     * 
     * Response:
     * {
     *     "success": true,
     *     "message": "Đăng nhập thành công!",
     *     "data": {
     *         "user": { ... },
     *         "token": "Bearer token",
     *         "token_type": "Bearer"
     *     }
     * }
     */
    Route::post('/microsoft', [MicrosoftAuthController::class, 'handleMicrosoftLogin']);

    /**
     * Đăng nhập bằng Microsoft Authorization Code
     * (Sử dụng khi không dùng MSAL ở frontend)
     * 
     * Method: POST
     * URL: /api/auth/microsoft/code
     * 
     * Request Body:
     * {
     *     "code": "Authorization code từ Microsoft",
     *     "redirect_uri": "URI đã dùng khi redirect"
     * }
     */
    Route::post('/microsoft/code', [MicrosoftAuthController::class, 'handleAuthorizationCode']);

    /**
     * Lấy URL đăng nhập Microsoft
     * 
     * Method: GET
     * URL: /api/auth/microsoft/url
     * 
     * Query Params:
     * - redirect_uri: URI để redirect sau khi đăng nhập (optional)
     */
    Route::get('/microsoft/url', [MicrosoftAuthController::class, 'getAuthUrl']);
});

// ========================================================================
// PROTECTED ROUTES (Cần đăng nhập - Bearer Token)
// ========================================================================
Route::middleware('auth:sanctum')->group(function () {
    
    // --------------------------------------------------------------------
    // Auth Routes
    // --------------------------------------------------------------------
    Route::prefix('auth')->group(function () {
        /**
         * Lấy thông tin user hiện tại
         * 
         * Method: GET
         * URL: /api/auth/me
         * Headers: Authorization: Bearer {token}
         */
        Route::get('/me', [GoogleAuthController::class, 'me']);
        
        /**
         * Đăng xuất
         * 
         * Method: POST
         * URL: /api/auth/logout
         * Headers: Authorization: Bearer {token}
         */
        Route::post('/logout', [GoogleAuthController::class, 'logout']);
    });
    
    // --------------------------------------------------------------------
    // Thêm các protected routes khác ở đây
    // --------------------------------------------------------------------
    // Ví dụ:
    // Route::post('/ideas', [IdeaRegistrationController::class, 'store']);
});

// ========================================================================
// PUBLIC ROUTES (Không cần đăng nhập)
// ========================================================================

// API Routes cho Idea Registration
Route::prefix('ideas')->group(function () {
    // Đăng ký ý tưởng mới
    Route::post('/', [IdeaRegistrationController::class, 'store']);
    
    // Lấy danh sách tất cả ý tưởng
    Route::get('/', [IdeaRegistrationController::class, 'index']);
    
    // Lấy chi tiết một ý tưởng
    Route::get('/{id}', [IdeaRegistrationController::class, 'show']);
});
