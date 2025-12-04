function ContestOverview({ onNavigate = () => {} }) {
  return (
    <>
      <section className="hero-section" id="gioi-thieu">
        <div className="container">
          <h2>THỬ THÁCH SÁNG TẠO 4.0</h2>
          <p>Biến ý tưởng thành sản phẩm thực tế – Nhận đầu tư tối đa 100 TRIỆU ĐỒNG</p>
          <div className="hero-decorations">
            <img 
              src="https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=600&h=400&fit=crop&auto=format" 
              alt="Technology" 
              className="hero-image hero-image-1"
            />
            <img 
              src="https://images.unsplash.com/photo-1518770660439-4636190af475?w=600&h=400&fit=crop&auto=format" 
              alt="Innovation" 
              className="hero-image hero-image-2"
            />
            <img 
              src="https://images.unsplash.com/photo-1485827404703-89b55fcc595e?w=600&h=400&fit=crop&auto=format" 
              alt="Startup" 
              className="hero-image hero-image-3"
            />
          </div>
        </div>
      </section>

      <section className="content-sections" id="quy-dinh">
        <div className="container">
          <div className="content-grid">
            <div className="content-card">
              <svg
                className="icon-svg"
                width="60"
                height="60"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                strokeWidth="2"
                strokeLinecap="round"
                strokeLinejoin="round"
              >
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
                <line x1="16" y1="13" x2="8" y2="13"></line>
                <line x1="16" y1="17" x2="8" y2="17"></line>
                <polyline points="10 9 9 9 8 9"></polyline>
              </svg>
              <h3>TỔNG QUAN CUỘC THI</h3>
              <p>
               "Thử thách Sáng tạo 4.0" là sân chơi công nghệ trọng điểm dành cho toàn thể Cán bộ, Giảng viên và Sinh viên UTT. Khác với các cuộc thi phong trào, chúng tôi tập trung nguồn lực để biến ý tưởng trên giấy thành sản phẩm thực tế thông qua 03 vòng thi: Vòng Ý tưởng – Vòng Demo – Vòng Ươm tạo & Phát triển.
              </p>
            </div>

            <div className="content-card">
              <svg
                className="icon-svg"
                width="60"
                height="60"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                strokeWidth="2"
                strokeLinecap="round"
                strokeLinejoin="round"
              >
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
              </svg>
              <h3>QUYỀN LỢI</h3>
              <ul>
                <li>Cấp vốn thực tế để phát triển sản phẩm mẫu (từ 100k → 100 triệu)</li>
                <li>Làm việc trực tiếp với đội ngũ chuyên gia/mentor kỹ thuật</li>
                <li>Sử dụng miễn phí phòng Lab và trang thiết bị của Nhà trường</li>
                <li>Định hướng phát triển thành Đề tài NCKH cấp Trường hoặc dự án khởi nghiệp</li>
              </ul>
            </div>

            <div className="content-card">
              <svg
                className="icon-svg"
                width="60"
                height="60"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                strokeWidth="2"
                strokeLinecap="round"
                strokeLinejoin="round"
              >
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
              </svg>
              <h3>HUB KẾT NỐI & GHÉP ĐỘI</h3>
              <ul>
                <li>Đăng tin tìm đồng đội</li>
                <li>Tìm kiếm Mentor/Cố vấn chuyên môn cho dự án</li>
                <li>Ứng tuyển vào các đội thi tiềm năng đang tuyển thành viên</li>
              </ul>
            </div>

            <div className="content-card">
              <svg
                className="icon-svg"
                width="60"
                height="60"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                strokeWidth="2"
                strokeLinecap="round"
                strokeLinejoin="round"
              >
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
              </svg>
              <h3>HẠN CUỐI</h3>
              <p>
                Thời gian nộp ý tưởng: <strong>Ngày 31/12/2025.</strong>
              </p>
              <p>
                Đừng bỏ lỡ cơ hội vàng để biến ý tưởng của bạn thành hiện thực và chinh phục những giải thưởng
                giá trị!
              </p>
            </div>
          </div>
        </div>
      </section>

      <section className="cta-section">
        <div className="container">
          <button
            type="button"
            className="button primary-button"
            onClick={() => onNavigate('registration')}
          >
            Đăng ký ngay
          </button>
        </div>
      </section>
    </>
  )
}

export default ContestOverview
