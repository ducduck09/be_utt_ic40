function SiteFooter() {
  return (
    <footer id="lien-he" className="site-footer">
      <div className="container">
        <div className="footer-content">
          <div className="footer-contact">
            <p>
              <strong>Trường Đại học Công nghệ GTVT</strong>
            </p>
            <p>Địa chỉ: Số 54 Triều Khúc, Thanh Liệt, Hà Nội</p>
            <p>Mọi thắc mắc liên hệ fanpage I4T hoặc Trung tâm BIM&AI</p>
          </div>

          <div className="social-links">
            <a href="https://www.facebook.com/utt.edu.vn" aria-label="Facebook">
              <svg viewBox="0 0 24 24" aria-hidden="true">
                <path
                  d="M15 3h3V0h-3c-2.8 0-5 2.2-5 5v3H7v3h3v10h3V11h3l1-3h-4V5c0-1.1.9-2 2-2Z"
                  fill="currentColor"
                />
              </svg>
            </a>
            {/* <a href="#" aria-label="Instagram">
              <svg viewBox="0 0 24 24" aria-hidden="true">
                <path
                  d="M7 2C4.2 2 2 4.2 2 7v10c0 2.8 2.2 5 5 5h10c2.8 0 5-2.2 5-5V7c0-2.8-2.2-5-5-5H7zm0 2h10c1.7 0 3 1.3 3 3v10c0 1.7-1.3 3-3 3H7c-1.7 0-3-1.3-3-3V7c0-1.7 1.3-3 3-3zm9 2a1 1 0 100 2 1 1 0 000-2zm-5 1.5A4.5 4.5 0 1015.5 12 4.5 4.5 0 0011 7.5zm0 2A2.5 2.5 0 1113.5 12 2.5 2.5 0 0111 9.5z"
                  fill="currentColor"
                />
              </svg>
            </a> */}
            {/* <a href="#" aria-label="LinkedIn">
              <svg viewBox="0 0 24 24" aria-hidden="true">
                <path
                  d="M4.8 8.6H2V22h2.8zm.1-4.3a1.7 1.7 0 1 0-3.4 0 1.7 1.7 0 0 0 3.4 0zM22 13.2c0-2.9-1.9-4.1-3.9-4.1a4.1 4.1 0 0 0-3.7 2h-.1V8.6h-2.7V22h2.8v-7.1c0-1.9.7-3.1 2.2-3.1 1.3 0 2 .9 2 2.9V22H22z"
                  fill="currentColor"
                />
              </svg>
            </a> */}
          </div>

          <div className="footer-copyright">
            <p>#UTT #ThửTháchSángTạo40 #I4T #BIMAI #NCKH #InnovationChallenge © 2025 Trường Đại học Công nghệ GTVT</p>
          </div>
        </div>
      </div>
    </footer>
  )
}

export default SiteFooter
