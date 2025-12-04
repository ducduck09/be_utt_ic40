import { useEffect, useState } from 'react'

const NAV_LABELS = {
  contest: 'Trang giới thiệu',
  registration: 'Đăng ký ý tưởng',
}

function SiteHeader({ activePage, onNavigate, user, onLogout }) {
  const [isScrolled, setIsScrolled] = useState(false)

  useEffect(() => {
    const handleScroll = () => setIsScrolled(window.scrollY > 50)
    window.addEventListener('scroll', handleScroll)
    return () => window.removeEventListener('scroll', handleScroll)
  }, [])

  const handleNavigate = (page) => {
    if (activePage === page) {
      return
    }
    onNavigate(page)
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }

  return (
    <header className={`site-header ${isScrolled ? 'scrolled' : ''}`}>
      <div className="container header-container">
        <button
          type="button"
          className="logo-brand"
          onClick={() => handleNavigate('contest')}
          aria-label="Về trang chủ"
        >
          <img
            src="/utt-logo.png"
            alt="Trường Đại học Công nghệ Giao thông Vận Tải"
            className="utt-logo"
          />
          <span className="sr-only">Cuộc thi Thử thách Sáng tạo 4.0</span>
        </button>

        <nav className="main-nav">
          {Object.entries(NAV_LABELS).map(([pageKey, label]) => (
            <button
              key={pageKey}
              type="button"
              className={`nav-link ${activePage === pageKey ? 'active' : ''}`}
              onClick={() => handleNavigate(pageKey)}
              aria-pressed={activePage === pageKey}
            >
              {label}
            </button>
          ))}
          <a className="nav-link" href="#lien-he">
            Liên hệ
          </a>
          
          {user ? (
            <div className="user-menu">
              <span className="user-name">{user.name}</span>
              <button
                type="button"
                className="nav-link logout-button"
                onClick={onLogout}
              >
                Đăng xuất
              </button>
            </div>
          ) : (
            <button
              type="button"
              className="nav-link login-button"
              onClick={() => handleNavigate('login')}
            >
              Đăng nhập
            </button>
          )}
        </nav>
      </div>
    </header>
  )
}

export default SiteHeader
