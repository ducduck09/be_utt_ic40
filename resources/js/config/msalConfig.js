/**
 * MSAL Configuration cho Microsoft Login
 * 
 * Cần cấu hình Azure AD App Registration:
 * 1. Vào Azure Portal > Azure Active Directory > App registrations
 * 2. Tạo app mới
 * 3. Chọn "Accounts in any organizational directory and personal Microsoft accounts"
 * 4. Redirect URI: http://localhost:5173 (Web - SPA)
 * 5. Lấy Application (client) ID
 */

// Microsoft Client ID từ biến môi trường
const MICROSOFT_CLIENT_ID = import.meta.env.VITE_MICROSOFT_CLIENT_ID || '';

export const msalConfig = {
  auth: {
    clientId: MICROSOFT_CLIENT_ID,
    authority: 'https://login.microsoftonline.com/common',
    redirectUri: window.location.origin, // http://localhost:5173 trong dev
    postLogoutRedirectUri: window.location.origin,
    navigateToLoginRequestUrl: true,
  },
  cache: {
    cacheLocation: 'localStorage', // hoặc 'sessionStorage'
    storeAuthStateInCookie: false,
  },
};

// Scopes cần thiết để lấy thông tin user
export const loginRequest = {
  scopes: ['User.Read', 'openid', 'profile', 'email'],
};

// Scopes cho Microsoft Graph API
export const graphConfig = {
  graphMeEndpoint: 'https://graph.microsoft.com/v1.0/me',
};

export default msalConfig;
