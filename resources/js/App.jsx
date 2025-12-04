import { useState, useEffect } from 'react'
import ContestOverview from './pages/ContestOverview.jsx'
import IdeaRegistration from './pages/IdeaRegistration.jsx'
import Login from './pages/Login.jsx'
import SiteHeader from './components/SiteHeader.jsx'
import SiteFooter from './components/SiteFooter.jsx'
import AuthProvider from './contexts/AuthProvider.jsx'
import useAuth from './hooks/useAuth.js'

const PAGES = {
  contest: 'contest',
  registration: 'registration',
  login: 'login',
}

function AppContent() {
  const [activePage, setActivePage] = useState(PAGES.contest)
  const { user, isAuthenticated, logout, loading } = useAuth()

  const handleLoginSuccess = () => {
    setActivePage(PAGES.contest)
  }

  const handleLogout = async () => {
    await logout()
    setActivePage(PAGES.contest)
  }

  // Redirect to login if trying to access registration without auth
  useEffect(() => {
    if (activePage === PAGES.registration && !isAuthenticated && !loading) {
      setActivePage(PAGES.login)
    }
  }, [activePage, isAuthenticated, loading])

  const renderPage = () => {
    if (activePage === PAGES.login) {
      // Redirect to contest if already logged in
      if (isAuthenticated) {
        setActivePage(PAGES.contest)
        return <ContestOverview onNavigate={setActivePage} />
      }
      return <Login onLoginSuccess={handleLoginSuccess} />
    }
    if (activePage === PAGES.registration) {
      if (!isAuthenticated) {
        return <Login onLoginSuccess={handleLoginSuccess} />
      }
      return <IdeaRegistration />
    }
    return <ContestOverview onNavigate={setActivePage} />
  }

  if (loading) {
    return (
      <div className="app-shell">
        <div className="loading-container">
          <p>Đang tải...</p>
        </div>
      </div>
    )
  }

  return (
    <div className="app-shell">
      <SiteHeader 
        activePage={activePage} 
        onNavigate={setActivePage} 
        user={user}
        onLogout={handleLogout}
      />
      <main className="page-content">{renderPage()}</main>
      <SiteFooter />
    </div>
  )
}

function App() {
  return (
    <AuthProvider>
      <AppContent />
    </AuthProvider>
  )
}

export default App
