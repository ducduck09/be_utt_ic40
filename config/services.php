<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Google OAuth Configuration
    |--------------------------------------------------------------------------
    |
    | Cấu hình đăng nhập Google OAuth 2.0
    | Lấy credentials từ: https://console.cloud.google.com/apis/credentials
    |
    */
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI', 'http://localhost:8000/api/auth/google/callback'),
    ],

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Microsoft OAuth Configuration
    |--------------------------------------------------------------------------
    |
    | Cấu hình đăng nhập Microsoft OAuth 2.0
    | Lấy credentials từ: https://portal.azure.com/#blade/Microsoft_AAD_RegisteredApps/ApplicationsListBlade
    |
    | Các bước tạo credentials:
    | 1. Đăng nhập Azure Portal
    | 2. Vào Azure Active Directory > App registrations
    | 3. Click "New registration"
    | 4. Điền tên app, chọn "Accounts in any organizational directory and personal Microsoft accounts"
    | 5. Redirect URI: http://localhost:8000/api/auth/microsoft/callback (Web)
    | 6. Lấy Application (client) ID và tạo Client secret
    |
    */
    'microsoft' => [
        'client_id' => env('MICROSOFT_CLIENT_ID'),
        'client_secret' => env('MICROSOFT_CLIENT_SECRET'),
        'redirect' => env('MICROSOFT_REDIRECT_URI', 'http://localhost:8000/api/auth/microsoft/callback'),
        'tenant' => env('MICROSOFT_TENANT_ID', 'common'), // 'common' cho tất cả accounts
    ],

];
