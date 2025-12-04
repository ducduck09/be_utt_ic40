import React, { createContext, useContext, useState, useEffect } from 'react';
import { useMsal } from '@azure/msal-react';
import { loginRequest } from '../config/msalConfig';
import axios from 'axios';

// API Base URL từ biến môi trường
const API_URL = import.meta.env.VITE_API_URL || 'http://127.0.0.1:8000/api';

// Tạo Axios instance với cấu hình mặc định
const api = axios.create({
  baseURL: API_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
  withCredentials: true,
});

// Interceptor để tự động thêm token vào header
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('auth_token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Context
export const AuthContext = createContext(null);

// Hook để sử dụng AuthContext
export const useAuth = () => {
  const context = useContext(AuthContext);
  if (!context) {
    throw new Error('useAuth must be used within an AuthProvider');
  }
  return context;
};

// Provider Component
export const AuthProvider = ({ children }) => {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  
  // MSAL hooks
  const { instance, accounts } = useMsal();

  // Kiểm tra token khi app load
  useEffect(() => {
    checkAuth();
  }, []);

  // Kiểm tra user đã đăng nhập chưa
  const checkAuth = async () => {
    const token = localStorage.getItem('auth_token');
    if (!token) {
      setLoading(false);
      return;
    }

    try {
      const response = await api.get('/auth/me');
      if (response.data.success) {
        setUser(response.data.data.user);
      }
    } catch (error) {
      console.error('Auth check failed:', error);
      localStorage.removeItem('auth_token');
    } finally {
      setLoading(false);
    }
  };

  // Đăng nhập bằng Google
  const loginWithGoogle = async (credential) => {
    try {
      setError(null);
      setLoading(true);

      const response = await api.post('/auth/google', {
        credential: credential,
      });

      if (response.data.success) {
        const { user, token } = response.data.data;
        localStorage.setItem('auth_token', token);
        setUser(user);
        return { success: true, user };
      }
    } catch (error) {
      const message = error.response?.data?.message || 'Đăng nhập thất bại';
      setError(message);
      return { success: false, error: message };
    } finally {
      setLoading(false);
    }
  };

  // Đăng nhập bằng Microsoft (sử dụng MSAL popup)
  const loginWithMicrosoft = async () => {
    try {
      setError(null);
      setLoading(true);

      // Mở popup đăng nhập Microsoft
      const msalResponse = await instance.loginPopup(loginRequest);
      
      console.log('Microsoft Login Success:', msalResponse);

      // Lấy access token để gửi đến backend
      const accessToken = msalResponse.accessToken;

      // Gửi access token đến backend để xác thực
      const response = await api.post('/auth/microsoft', {
        access_token: accessToken,
      });

      if (response.data.success) {
        const { user, token } = response.data.data;
        localStorage.setItem('auth_token', token);
        setUser(user);
        return { success: true, user };
      }
    } catch (error) {
      console.error('Microsoft Login Error:', error);
      
      // Xử lý các loại lỗi khác nhau
      let message = 'Đăng nhập Microsoft thất bại';
      
      if (error.errorCode === 'user_cancelled') {
        message = 'Đăng nhập đã bị hủy';
      } else if (error.response?.data?.message) {
        message = error.response.data.message;
      } else if (error.message) {
        message = error.message;
      }
      
      setError(message);
      return { success: false, error: message };
    } finally {
      setLoading(false);
    }
  };

  // Đăng xuất
  const logout = async () => {
    try {
      // Logout từ backend
      await api.post('/auth/logout');
      
      // Logout từ MSAL nếu đã đăng nhập bằng Microsoft
      if (accounts.length > 0) {
        await instance.logoutPopup({
          postLogoutRedirectUri: window.location.origin,
        });
      }
    } catch (error) {
      console.error('Logout error:', error);
    } finally {
      localStorage.removeItem('auth_token');
      setUser(null);
    }
  };

  const value = {
    user,
    loading,
    error,
    isAuthenticated: !!user,
    loginWithGoogle,
    loginWithMicrosoft,
    logout,
    checkAuth,
  };

  return (
    <AuthContext.Provider value={value}>
      {children}
    </AuthContext.Provider>
  );
};

export default AuthContext;
