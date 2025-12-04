import { GoogleLogin } from '@react-oauth/google'
import useAuth from '../hooks/useAuth.js'

function Login({ onLoginSuccess }) {
  const { loginWithGoogle, loginWithMicrosoft, loading, error } = useAuth()

  const handleGoogleSuccess = async (credentialResponse) => {
    console.log('Google Login Success:', credentialResponse)

    const result = await loginWithGoogle(credentialResponse.credential)

    if (result.success) {
      console.log('Backend Login Success:', result.user)
      if (onLoginSuccess) {
        onLoginSuccess(result.user)
      }
    } else {
      console.error('Backend Login Failed:', result.error)
    }
  }

  const handleGoogleError = () => {
    console.error('Google Login Failed')
  }

  const handleMicrosoftLogin = async () => {
    console.log('Microsoft login button clicked!')
    console.log('loginWithMicrosoft function:', loginWithMicrosoft)
    
    if (!loginWithMicrosoft) {
      console.error('loginWithMicrosoft is not available!')
      return
    }

    try {
      const result = await loginWithMicrosoft()
      console.log('Microsoft login result:', result)
      
      if (result && result.success) {
        console.log('Microsoft Login Success:', result.user)
        if (onLoginSuccess) {
          onLoginSuccess(result.user)
        }
      } else {
        console.error('Microsoft Login Failed:', result ? result.error : 'Unknown error')
      }
    } catch (err) {
      console.error('Microsoft login exception:', err)
    }
  }

  return (
    <section className="login-section">
      <div className="container">
        <div className="login-container">
          <div className="login-card">
            <div className="login-header">
              <img
                src="/utt-logo.png"
                alt="Trường Đại học Công nghệ Giao thông Vận Tải"
                className="login-logo"
              />
              <h2>Đăng nhập</h2>
              <p>Cuộc thi Thử thách Sáng tạo 4.0</p>
            </div>

            <div className="login-body">
              <p className="login-description">
                Đăng nhập để tham gia cuộc thi và quản lý ý tưởng của bạn
              </p>

              <div className="google-login-container">
                <button
                  type="button"
                  className="google-login-button"
                  onClick={() => {
                    const googleBtn = document.querySelector('[role="button"][aria-labelledby]');
                    if (googleBtn) googleBtn.click();
                  }}
                >
                  <span className="google-login-icon">
                    <svg viewBox="0 0 24 24" width="20" height="20">
                      <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                      <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                      <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                      <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                  </span>
                  <span>Đăng nhập bằng Google</span>
                </button>
                
                <div style={{ display: 'none' }}>
                  <GoogleLogin
                    onSuccess={handleGoogleSuccess}
                    onError={handleGoogleError}
                    useOneTap={false}
                  />
                </div>

                <button
                  type="button"
                  className="microsoft-login-button"
                  onClick={handleMicrosoftLogin}
                  disabled={loading}
                >
                  <span className="microsoft-login-icon">
                    <svg viewBox="0 0 23 23" width="20" height="20">
                      <rect x="1" y="1" width="9" height="9" fill="#f25022" rx="1" ry="1" />
                      <rect x="13" y="1" width="9" height="9" fill="#7fba00" rx="1" ry="1" />
                      <rect x="1" y="13" width="9" height="9" fill="#00a4ef" rx="1" ry="1" />
                      <rect x="13" y="13" width="9" height="9" fill="#ffb900" rx="1" ry="1" />
                    </svg>
                  </span>
                  <span>Đăng nhập bằng Microsoft</span>
                </button>

                {loading && <p className="loading-message">Đang xử lý...</p>}
                {error && <p className="error-message">{error}</p>}
              </div>

              <div className="login-divider">
                <span>hoặc</span>
              </div>

              <div className="login-info">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                  <circle cx="12" cy="12" r="10"></circle>
                  <line x1="12" y1="16" x2="12" y2="12"></line>
                  <line x1="12" y1="8" x2="12.01" y2="8"></line>
                </svg>
                <p>
                  Sử dụng tài khoản Google hoặc Microsoft để đăng nhập.
                  Thông tin của bạn sẽ được bảo mật và chỉ dùng cho mục đích tham gia cuộc thi.
                </p>
              </div>
            </div>

            <div className="login-footer">
              <p>
                Bằng việc đăng nhập, bạn đồng ý với{' '}
                <a href="#dieu-khoan">Điều khoản sử dụng</a> và{' '}
                <a href="#chinh-sach">Chính sách bảo mật</a>
              </p>
            </div>
          </div>

          <div className="login-features">
            <h3>Tại sao nên tham gia?</h3>
            <div className="feature-list">
              <div className="feature-item">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                  <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                  <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
                <div>
                  <h4>Nhận ngay 100.000 VNĐ</h4>
                  <p>Nếu ý tưởng đạt yêu cầu ngay từ Vòng 1</p>
                </div>
              </div>
              <div className="feature-item">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                  <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                  <path d="M2 17l10 5 10-5M2 12l10 5 10-5"></path>
                </svg>
                <div>
                  <h4>Hỗ trợ mentor chuyên môn</h4>
                  <p>Làm việc trực tiếp với đội ngũ chuyên gia</p>
                </div>
              </div>
              <div className="feature-item">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                  <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                  <circle cx="12" cy="7" r="4"></circle>
                </svg>
                <div>
                  <h4>Kết nối & hợp tác</h4>
                  <p>Tìm đồng đội và mở rộng mạng lưới</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  )
}

export default Login
