import { useState } from 'react'

const FIELD_OPTIONS = ['Giải pháp, công nghệ, ý tưởng đổi mới sáng tạo phục vụ UTT', 'Công nghệ 4.0', 'Xây dựng - Công trình', 'Công nghệ thông tin - Cơ điện tử - Viễn thông', 'Kinh tế - Quản trị','Khoa học ứng dụng','Cơ khí - Động lực','Luật - Chính trị - Ngôn ngữ','Khác']
const SUPPORT_OPTIONS = [
  { value: 'yes', label: 'Có' },
  { value: 'no', label: 'Không' },
]

// URL API - Thay đổi nếu cần
const API_URL = 'http://127.0.0.1:8000/api/ideas'

function IdeaRegistration() {
  const [researchField, setResearchField] = useState('')
  const [isSubmitted, setIsSubmitted] = useState(false)
  const [isLoading, setIsLoading] = useState(false)
  const [error, setError] = useState(null)

  const handleSubmit = async (event) => {
    event.preventDefault()
    const form = event.currentTarget

    if (!form.checkValidity()) {
      form.reportValidity()
      return
    }

    setIsLoading(true)
    setError(null)

    // Lấy dữ liệu từ form
    const formData = new FormData(form)
    const data = {
      research_field: formData.get('research_field'),
      other_field: formData.get('other_field'),
      fullname: formData.get('fullname'),
      phone: formData.get('phone'),
      student_code: formData.get('student_code'),
      bank_account: formData.get('bank_account'),
      bank_name: formData.get('bank_name'),
      idea_name: formData.get('idea_name'),
      idea_description: formData.get('idea_description'),
      expected_products: formData.get('expected_products'),
      urgency: formData.get('urgency'),
      innovation: formData.get('innovation'),
      feasibility: formData.get('feasibility'),
      potential: formData.get('potential'),
      support_need: formData.get('support_need'),
    }

    try {
      const response = await fetch(API_URL, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: JSON.stringify(data),
      })

      const result = await response.json()

      if (!response.ok) {
        throw new Error(result.message || 'Có lỗi xảy ra khi đăng ký')
      }

      setIsSubmitted(true)
      window.scrollTo({ top: 0, behavior: 'smooth' })
    } catch (err) {
      setError(err.message)
      window.scrollTo({ top: 0, behavior: 'smooth' })
    } finally {
      setIsLoading(false)
    }
  }

  if (isSubmitted) {
    return (
      <section className="form-content">
        <div className="container">
          <div className="success-page">
            <div className="success-icon">
              <svg
                width="100"
                height="100"
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
            </div>
            <h2>Đăng ký thành công!</h2>
            <p className="success-message">
              Cảm ơn bạn đã tham gia <strong>Cuộc thi Thử thách Sáng tạo 4.0</strong>
            </p>
            <div className="success-details">
              <p>
                Ý tưởng của bạn đã được ghi nhận. Ban Tổ chức sẽ xem xét và phản hồi trong thời gian sớm nhất.
              </p>
              <p>
                Nếu ý tưởng đạt yêu cầu, bạn sẽ nhận được <strong>100.000 VNĐ</strong> ngay từ Vòng 1.
              </p>
              <p className="check-email">
                Vui lòng kiểm tra email để nhận thông tin chi tiết về kết quả xét duyệt.
              </p>
            </div>
            <button
              type="button"
              className="button primary-button"
              onClick={() => {
                setIsSubmitted(false)
                setError(null)
              }}
            >
              Đăng ký ý tưởng khác
            </button>
          </div>
        </div>
      </section>
    )
  }

  return (
    <section className="form-content">
      <div className="container">
        {error && (
          <div className="error-message" style={{
            backgroundColor: '#fee',
            border: '1px solid #fcc',
            padding: '15px',
            borderRadius: '5px',
            marginBottom: '20px',
            color: '#c33'
          }}>
            <strong>Lỗi:</strong> {error}
          </div>
        )}
        <div className="form-title-section">
          <h2>CUỘC THI THỬ THÁCH SÁNG TẠO 4.0 </h2>
          <p>VÒNG 1: ĐỀ XUẤT Ý TƯỞNG</p>
        </div>

        <div className="form-container">
          <div className="form-intro-section">
            <h3>Cuộc thi chào mừng 80 năm thành lập Trường Đại học Công nghệ GTVT</h3>
            <p>
              Biểu mẫu này dành cho <strong>Vòng 1 (Vòng Ý tưởng)</strong>. Nhà trường khuyến khích mọi ý tưởng mới, 
              tính ứng dụng cao phục vụ Sinh viên, Cán bộ Giảng viên hoặc ứng dụng trong các lĩnh vực: ITS, AI, 
              Robotic, Công nghệ Xây dựng, EdTech, Tài chính, Quản trị, Luật & Ngôn ngữ và các lĩnh vực khác.
            </p>
            
            <div className="intro-benefits">
              <h4>QUYỀN LỢI & LƯU Ý:</h4>
              <ol>
                <li>
                  <strong>Hỗ trợ kinh phí:</strong> Nhận ngay <strong>100.000 VNĐ/ý tưởng</strong> nếu đề xuất đạt 
                  yêu cầu (xét duyệt ngay từ Vòng 1). Hàng ngày sẽ có danh mục các ý tưởng đạt yêu cầu gửi tới đại 
                  diện các nhóm đề xuất.
                </li>
                <li>
                  <strong>Đối tượng:</strong> Toàn thể Cán bộ Giảng viên, Viên chức, Người lao động, Học viên, 
                  Sinh viên UTT.
                </li>
                <li>
                  <strong>Yêu cầu:</strong> Trình bày ngắn gọn, rõ tính mới và kết quả dự kiến.
                </li>
              </ol>
              <p className="deadline-notice">
                <strong>Hạn cuối đề xuất: 31/12/2025</strong>
              </p>
            </div>

            <p className="form-call-to-action">
              <strong>KÍNH MỜI TOÀN THỂ CÁC THẦY CÔ VÀ CÁC BẠN SINH VIÊN ĐIỀN THÔNG TIN BÊN DƯỚI</strong>
            </p>
          </div>

          <form className="idea-form" onSubmit={handleSubmit} noValidate>
            <div className="form-section">
              <h3>Phần I: THÔNG TIN CÁ NHÂN/NHÓM</h3>
              <p className="form-instruction">(Vui lòng điền đầy đủ thông tin của Người đại diện Nhóm !)</p>

              <div className="form-group">
                <label htmlFor="research_field">
                  Lĩnh vực nghiên cứu <span className="required">*</span>
                </label>
                <select
                  id="research_field"
                  name="research_field"
                  required
                  value={researchField}
                  onChange={(event) => setResearchField(event.target.value)}
                >
                  <option value="">-- Chọn lĩnh vực --</option>
                  {FIELD_OPTIONS.map((option) => (
                    <option key={option} value={option}>
                      {option}
                    </option>
                  ))}
                </select>
              </div>

              {researchField === 'Khác' && (
                <div className="form-group">
                  <label htmlFor="other_field_input">
                    Ghi rõ lĩnh vực <span className="required">*</span>
                  </label>
                  <input
                    type="text"
                    id="other_field_input"
                    name="other_field"
                    placeholder="Vui lòng ghi rõ lĩnh vực"
                    required
                  />
                </div>
              )}

              <div className="form-group">
                <label htmlFor="fullname">
                  Họ và tên đại diện nhóm <span className="required">*</span>
                </label>
                <input type="text" id="fullname" name="fullname" placeholder="Nhập họ và tên đầy đủ" required />
              </div>

              <div className="form-group">
                <label htmlFor="phone">
                  Số điện thoại <span className="required">*</span>
                </label>
                <input
                  type="tel"
                  id="phone"
                  name="phone"
                  placeholder="Nhập số điện thoại"
                  pattern="[0-9]{10,11}"
                  required
                />
              </div>

              <div className="form-group">
                <label htmlFor="student_code">
                  Mã cán bộ, sinh viên <span className="required">*</span>
                </label>
                <input type="text" id="student_code" name="student_code" placeholder="Nhập mã số" required />
              </div>

              <div className="form-group">
                <label htmlFor="bank_account">
                  Số tài khoản ngân hàng (Yêu cầu nhập chính xác) <span className="required">*</span>
                </label>
                <input
                  type="text"
                  id="bank_account"
                  name="bank_account"
                  placeholder="Nhập số tài khoản"
                  required
                />
                <p className="form-note">Yêu cầu nhập chính xác</p>
              </div>

              <div className="form-group">
                <label htmlFor="bank_name">
                  Tên ngân hàng <span className="required">*</span>
                </label>
                <input type="text" id="bank_name" name="bank_name" placeholder="Nhập tên ngân hàng" required />
              </div>
            </div>

            <div className="form-section">
              <h3>Phần II: THÔNG TIN Ý TƯỞNG DỰ THI</h3>
              <p className="form-instruction">
                (Vòng 1: yêu cầu tóm tắt ý tưởng, trong đó nêu rõ tính mới, tính sáng tạo, khả năng ứng dụng và các
                kết quả dự kiến)
              </p>

              <div className="form-group">
                <label htmlFor="idea_name">
                  Tên của ý tưởng <span className="required">*</span>
                </label>
                <input type="text" id="idea_name" name="idea_name" placeholder="Nhập tên ý tưởng" required />
              </div>

              <div className="form-group">
                <label htmlFor="idea_description">
                  Mô tả cụ thể ý tưởng <span className="required">*</span>
                </label>
                <textarea
                  id="idea_description"
                  name="idea_description"
                  placeholder="Mô tả chi tiết ý tưởng của bạn..."
                  required
                ></textarea>
              </div>

              <div className="form-group">
                <label htmlFor="expected_products">
                  Các sản phẩm dự kiến đạt được <span className="required">*</span>
                </label>
                <textarea
                  id="expected_products"
                  name="expected_products"
                  placeholder="Liệt kê các sản phẩm hoặc kết quả dự kiến..."
                  required
                  style={{ minHeight: '100px' }}
                ></textarea>
              </div>
            </div>

            <div className="form-section">
              <h3>Phần III: ĐÁNH GIÁ VÀ NHU CẦU HỖ TRỢ</h3>
              <p className="form-instruction">Khuyến khích viết một cách chi tiết, đầy đủ</p>

              <div className="form-group">
                <label htmlFor="urgency">
                  Tính cấp thiết của ý tưởng <span className="required">*</span>
                </label>
                <textarea
                  id="urgency"
                  name="urgency"
                  placeholder="Tại sao ý tưởng này cần được thực hiện ngay?..."
                  required
                ></textarea>
              </div>

              <div className="form-group">
                <label htmlFor="innovation">
                  Miêu tả sơ bộ tính mới &amp; sáng tạo của ý tưởng <span className="required">*</span>
                </label>
                <textarea
                  id="innovation"
                  name="innovation"
                  placeholder="Điểm mới và sáng tạo của ý tưởng là gì?..."
                  required
                ></textarea>
              </div>

              <div className="form-group">
                <label htmlFor="feasibility">
                  Tính khả thi của ý tưởng <span className="required">*</span>
                </label>
                <textarea
                  id="feasibility"
                  name="feasibility"
                  placeholder="Đánh giá khả năng thực hiện ý tưởng..."
                  required
                ></textarea>
              </div>

              <div className="form-group">
                <label htmlFor="potential">
                  Tiềm năng phát triển của ý tưởng <span className="required">*</span>
                </label>
                <textarea
                  id="potential"
                  name="potential"
                  placeholder="Ý tưởng có thể phát triển như thế nào trong tương lai?..."
                  required
                ></textarea>
              </div>

              <div className="form-group">
                <label htmlFor="support_need">
                  Nhóm có cần thành viên hỗ trợ hiện thực hóa sản phẩm ở Vòng 2 không?
                  <span className="required">*</span>
                </label>
                <select id="support_need" name="support_need" required defaultValue="">
                  <option value="" disabled>
                    -- Chọn --
                  </option>
                  {SUPPORT_OPTIONS.map((option) => (
                    <option key={option.value} value={option.value}>
                      {option.label}
                    </option>
                  ))}
                </select>
              </div>
            </div>

            <div className="submit-section">
              <button type="submit" className="submit-button" disabled={isLoading}>
                {isLoading ? 'Đang gửi...' : 'Gửi Đăng ký'}
              </button>
            </div>
          </form>
        </div>
      </div>
    </section>
  )
}

export default IdeaRegistration
