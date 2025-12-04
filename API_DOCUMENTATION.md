# API Documentation - Idea Registration

## Base URL
```
http://127.0.0.1:8000/api
```

---

## Authentication Endpoints

### 1. Đăng nhập bằng Google
**POST** `/auth/google`

#### Request Body:
```json
{
  "credential": "Google ID Token từ @react-oauth/google"
}
```

#### Response Success (200):
```json
{
  "success": true,
  "message": "Đăng nhập thành công!",
  "data": {
    "user": {
      "id": 1,
      "name": "Nguyễn Văn A",
      "email": "user@gmail.com",
      "avatar": "https://...",
      "role": "user",
      "email_verified_at": "2025-12-04T00:00:00.000000Z"
    },
    "token": "1|abcdef123456...",
    "token_type": "Bearer"
  }
}
```

---

### 2. Đăng nhập bằng Microsoft
**POST** `/auth/microsoft`

#### Request Body:
```json
{
  "access_token": "Microsoft Access Token từ MSAL (@azure/msal-react)"
}
```

#### Response Success (200):
```json
{
  "success": true,
  "message": "Đăng nhập thành công!",
  "data": {
    "user": {
      "id": 1,
      "name": "Nguyễn Văn A",
      "email": "user@outlook.com",
      "avatar": "data:image/jpeg;base64,...",
      "role": "user",
      "email_verified_at": "2025-12-04T00:00:00.000000Z"
    },
    "token": "1|abcdef123456...",
    "token_type": "Bearer"
  }
}
```

---

### 3. Đăng nhập bằng Microsoft (Authorization Code)
**POST** `/auth/microsoft/code`

#### Request Body:
```json
{
  "code": "Authorization code từ Microsoft OAuth redirect",
  "redirect_uri": "http://localhost:3000/callback"
}
```

#### Response: Same as `/auth/microsoft`

---

### 4. Lấy URL đăng nhập Microsoft
**GET** `/auth/microsoft/url`

#### Query Parameters:
- `redirect_uri` (optional): URI để redirect sau khi đăng nhập

#### Response (200):
```json
{
  "success": true,
  "data": {
    "auth_url": "https://login.microsoftonline.com/common/oauth2/v2.0/authorize?..."
  }
}
```

---

### 5. Lấy thông tin user hiện tại
**GET** `/auth/me`

#### Headers:
```
Authorization: Bearer {token}
```

#### Response (200):
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "name": "Nguyễn Văn A",
      "email": "user@example.com",
      "avatar": "https://...",
      "role": "user",
      "email_verified_at": "2025-12-04T00:00:00.000000Z",
      "created_at": "2025-12-04T00:00:00.000000Z"
    }
  }
}
```

---

### 6. Đăng xuất
**POST** `/auth/logout`

#### Headers:
```
Authorization: Bearer {token}
```

#### Response (200):
```json
{
  "success": true,
  "message": "Đăng xuất thành công!"
}
```

---

## Idea Endpoints

### 1. Đăng ký ý tưởng mới
**POST** `/ideas`

#### Request Body:
```json
{
  "research_field": "Công nghệ thông tin",
  "other_field": null,
  "fullname": "Nguyễn Văn A",
  "phone": "0123456789",
  "student_code": "SV001",
  "bank_account": "123456789",
  "bank_name": "Vietcombank",
  "idea_name": "Ứng dụng quản lý học tập",
  "idea_description": "Mô tả chi tiết ý tưởng...",
  "expected_products": "Sản phẩm mobile app...",
  "urgency": "Cấp thiết vì...",
  "innovation": "Tính sáng tạo...",
  "feasibility": "Khả thi vì...",
  "potential": "Tiềm năng phát triển...",
  "support_need": "yes"
}
```

#### Response Success (201):
```json
{
  "success": true,
  "message": "Đăng ký ý tưởng thành công",
  "data": {
    "idea_id": 1,
    "user_id": 1
  }
}
```

#### Response Error (422):
```json
{
  "success": false,
  "message": "Dữ liệu không hợp lệ",
  "errors": {
    "fullname": ["Họ và tên là bắt buộc"]
  }
}
```

### 2. Lấy danh sách tất cả ý tưởng
**GET** `/ideas`

#### Response (200):
```json
{
  "success": true,
  "data": [
    {
      "idea_id": 1,
      "user_id": 1,
      "research_field": "Công nghệ thông tin",
      "idea_title": "Ứng dụng quản lý học tập",
      "full_name": "Nguyễn Văn A",
      "phone": "0123456789",
      "bank_name": "Vietcombank"
    }
  ]
}
```

### 3. Lấy chi tiết một ý tưởng
**GET** `/ideas/{id}`

#### Response (200):
```json
{
  "success": true,
  "data": {
    "idea_id": 1,
    "user_id": 1,
    "research_field": "Công nghệ thông tin",
    "idea_title": "Ứng dụng quản lý học tập",
    "idea_description": "Mô tả chi tiết...",
    "full_name": "Nguyễn Văn A",
    "phone": "0123456789"
  }
}
```

## Validation Rules

- **research_field**: Bắt buộc, tối đa 255 ký tự
- **phone**: Bắt buộc, 10-11 chữ số
- **student_code**: Bắt buộc, tối đa 50 ký tự
- **bank_account**: Bắt buộc, tối đa 50 ký tự
- **support_need**: Bắt buộc, giá trị "yes" hoặc "no"

## Test với Postman hoặc cURL

### Test Microsoft Login
```bash
# Đăng nhập bằng Microsoft Access Token
curl -X POST http://127.0.0.1:8000/api/auth/microsoft \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "access_token": "YOUR_MICROSOFT_ACCESS_TOKEN"
  }'

# Lấy URL đăng nhập Microsoft
curl -X GET "http://127.0.0.1:8000/api/auth/microsoft/url?redirect_uri=http://localhost:3000/callback"
```

### Test Idea Registration
```bash
curl -X POST http://127.0.0.1:8000/api/ideas \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "research_field": "Công nghệ thông tin",
    "fullname": "Nguyễn Văn A",
    "phone": "0123456789",
    "student_code": "SV001",
    "bank_account": "123456789",
    "bank_name": "Vietcombank",
    "idea_name": "Test Idea",
    "idea_description": "Test description",
    "expected_products": "Test products",
    "urgency": "Test urgency",
    "innovation": "Test innovation",
    "feasibility": "Test feasibility",
    "potential": "Test potential",
    "support_need": "yes"
  }'
```

---

## Frontend Integration Guide

### Microsoft Login với @azure/msal-react

1. **Cài đặt MSAL:**
```bash
npm install @azure/msal-browser @azure/msal-react
```

2. **Cấu hình MSAL:**
```javascript
// msalConfig.js
export const msalConfig = {
  auth: {
    clientId: "YOUR_AZURE_CLIENT_ID",
    authority: "https://login.microsoftonline.com/common",
    redirectUri: "http://localhost:3000",
  },
};

export const loginRequest = {
  scopes: ["User.Read", "openid", "profile", "email"],
};
```

3. **Sử dụng trong component:**
```javascript
import { useMsal } from "@azure/msal-react";
import { loginRequest } from "./msalConfig";

function LoginButton() {
  const { instance, accounts } = useMsal();

  const handleMicrosoftLogin = async () => {
    try {
      const response = await instance.loginPopup(loginRequest);
      const accessToken = response.accessToken;
      
      // Gửi token đến backend
      const res = await fetch('/api/auth/microsoft', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ access_token: accessToken }),
      });
      
      const data = await res.json();
      // Lưu token và user info
      localStorage.setItem('token', data.data.token);
    } catch (error) {
      console.error(error);
    }
  };

  return <button onClick={handleMicrosoftLogin}>Đăng nhập với Microsoft</button>;
}
```
