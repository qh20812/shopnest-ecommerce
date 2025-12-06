import React from 'react';
import TopNav from '../components/top-nav';
import Footer from '../components/footer';
import { ToastProvider } from '../lib/toastContext';
import { Cookie, Settings, BarChart3, Palette, FileEdit, Mail } from 'lucide-react';

export default function ChinhSachCookie() {
  return (
    <ToastProvider>
      <TopNav />
      <main className="flex-1">
        {/* Hero Section */}
        <div className="bg-surface-light dark:bg-surface-dark py-16 md:py-24">
          <div className="container mx-auto px-4">
            <div className="max-w-4xl mx-auto text-center">
              <h1 className="text-4xl md:text-5xl font-extrabold text-text-primary-light dark:text-text-primary-dark tracking-tighter">
                Chính sách Cookie
              </h1>
              <p className="mt-4 text-lg text-text-secondary-light dark:text-text-secondary-dark">
                Cập nhật lần cuối: 24/07/2024
              </p>
            </div>
          </div>
        </div>

        {/* Content Section */}
        <div className="container mx-auto px-4 py-12 md:py-16">
          <div className="max-w-4xl mx-auto">
            <div className="space-y-8">
              {/* Section 1: Cookie là gì? */}
              <section className="bg-white dark:bg-surface-dark p-6 rounded-xl border border-gray-200 dark:border-gray-800">
                <div className="flex items-start gap-4">
                  <div className="flex-shrink-0">
                    <Cookie className="w-6 h-6 text-primary" />
                  </div>
                  <div>
                    <h2 className="text-2xl font-bold text-text-primary-light dark:text-text-primary-dark mb-4">
                      1. Cookie là gì?
                    </h2>
                    <p className="text-text-secondary-light dark:text-text-secondary-dark leading-relaxed">
                      Cookie là các tệp văn bản nhỏ được đặt trên thiết bị của bạn bởi các trang web mà bạn truy cập. Chúng
                      được sử dụng rộng rãi để làm cho các trang web hoạt động, hoặc hoạt động hiệu quả hơn, cũng như để
                      cung cấp thông tin cho chủ sở hữu trang web. Cookie giúp chúng tôi nhận ra thiết bị của bạn và ghi nhớ
                      thông tin về sở thích hoặc các hành động trong quá khứ của bạn.
                    </p>
                  </div>
                </div>
              </section>

              {/* Section 2: Cách sử dụng cookie */}
              <section className="bg-white dark:bg-surface-dark p-6 rounded-xl border border-gray-200 dark:border-gray-800">
                <div className="flex items-start gap-4">
                  <div className="flex-shrink-0">
                    <Settings className="w-6 h-6 text-primary" />
                  </div>
                  <div>
                    <h2 className="text-2xl font-bold text-text-primary-light dark:text-text-primary-dark mb-4">
                      2. Cách chúng tôi sử dụng cookie
                    </h2>
                    <p className="text-text-secondary-light dark:text-text-secondary-dark leading-relaxed">
                      Chúng tôi sử dụng cookie để nâng cao trải nghiệm duyệt web của bạn bằng cách: ghi nhớ các tùy chọn
                      của bạn, cung cấp nội dung được cá nhân hóa, phân tích lưu lượng truy cập trang web để chúng tôi có
                      thể cải thiện dịch vụ của mình, và cho mục đích quảng cáo. Chúng tôi không sử dụng cookie để thu thập
                      thông tin nhận dạng cá nhân mà không có sự đồng ý của bạn.
                    </p>
                  </div>
                </div>
              </section>

              {/* Section 3: Các loại cookie */}
              <section className="bg-white dark:bg-surface-dark p-6 rounded-xl border border-gray-200 dark:border-gray-800">
                <div className="flex items-start gap-4">
                  <div className="flex-shrink-0">
                    <BarChart3 className="w-6 h-6 text-primary" />
                  </div>
                  <div>
                    <h2 className="text-2xl font-bold text-text-primary-light dark:text-text-primary-dark mb-4">
                      3. Các loại cookie chúng tôi sử dụng
                    </h2>
                    <p className="text-text-secondary-light dark:text-text-secondary-dark leading-relaxed mb-4">
                      Chúng tôi sử dụng các loại cookie sau trên trang web của mình:
                    </p>
                    <ul className="space-y-3 text-text-secondary-light dark:text-text-secondary-dark">
                      <li className="flex gap-3">
                        <span className="text-primary mt-1.5">•</span>
                        <div>
                          <strong className="text-text-primary-light dark:text-text-primary-dark">Cookie thiết yếu:</strong> Những cookie này là cần thiết để trang web hoạt động và không
                          thể tắt trong hệ thống của chúng tôi. Chúng thường chỉ được đặt để đáp ứng các hành động do bạn thực
                          hiện, chẳng hạn như đặt tùy chọn bảo mật, đăng nhập hoặc điền vào biểu mẫu.
                        </div>
                      </li>
                      <li className="flex gap-3">
                        <span className="text-primary mt-1.5">•</span>
                        <div>
                          <strong className="text-text-primary-light dark:text-text-primary-dark">Cookie hiệu suất:</strong> Những cookie này cho phép chúng tôi đếm số lần truy cập và
                          nguồn lưu lượng truy cập để chúng tôi có thể đo lường và cải thiện hiệu suất của trang web. Chúng
                          giúp chúng tôi biết trang nào là phổ biến nhất và ít phổ biến nhất và xem khách truy cập di chuyển
                          xung quanh trang web như thế nào.
                        </div>
                      </li>
                      <li className="flex gap-3">
                        <span className="text-primary mt-1.5">•</span>
                        <div>
                          <strong className="text-text-primary-light dark:text-text-primary-dark">Cookie chức năng:</strong> Những cookie này cho phép trang web cung cấp chức năng và cá
                          nhân hóa nâng cao. Chúng có thể được đặt bởi chúng tôi hoặc bởi các nhà cung cấp bên thứ ba có dịch
                          vụ mà chúng tôi đã thêm vào các trang của mình.
                        </div>
                      </li>
                      <li className="flex gap-3">
                        <span className="text-primary mt-1.5">•</span>
                        <div>
                          <strong className="text-text-primary-light dark:text-text-primary-dark">Cookie quảng cáo:</strong> Những cookie này có thể được đặt thông qua trang web của chúng
                          tôi bởi các đối tác quảng cáo của chúng tôi. Chúng có thể được các công ty đó sử dụng để xây dựng hồ
                          sơ về sở thích của bạn và hiển thị cho bạn các quảng cáo có liên quan trên các trang web khác.
                        </div>
                      </li>
                    </ul>
                  </div>
                </div>
              </section>

              {/* Section 4: Quản lý cookie */}
              <section className="bg-white dark:bg-surface-dark p-6 rounded-xl border border-gray-200 dark:border-gray-800">
                <div className="flex items-start gap-4">
                  <div className="flex-shrink-0">
                    <Palette className="w-6 h-6 text-primary" />
                  </div>
                  <div>
                    <h2 className="text-2xl font-bold text-text-primary-light dark:text-text-primary-dark mb-4">
                      4. Cách quản lý tùy chọn cookie của bạn
                    </h2>
                    <p className="text-text-secondary-light dark:text-text-secondary-dark leading-relaxed">
                      Bạn có thể kiểm soát và/hoặc xóa cookie theo ý muốn. Hầu hết các trình duyệt web cho phép một số
                      quyền kiểm soát hầu hết các cookie thông qua cài đặt trình duyệt. Để tìm hiểu thêm về cookie, bao gồm
                      cách xem cookie nào đã được đặt, hãy truy cập www.aboutcookies.org hoặc www.allaboutcookies.org. Xin
                      lưu ý rằng nếu bạn tắt cookie, một số tính năng của trang web này có thể không hoạt động như dự định.
                    </p>
                  </div>
                </div>
              </section>

              {/* Section 5: Thay đổi Chính sách */}
              <section className="bg-white dark:bg-surface-dark p-6 rounded-xl border border-gray-200 dark:border-gray-800">
                <div className="flex items-start gap-4">
                  <div className="flex-shrink-0">
                    <FileEdit className="w-6 h-6 text-primary" />
                  </div>
                  <div>
                    <h2 className="text-2xl font-bold text-text-primary-light dark:text-text-primary-dark mb-4">
                      5. Thay đổi Chính sách
                    </h2>
                    <p className="text-text-secondary-light dark:text-text-secondary-dark leading-relaxed">
                      Chính sách Cookie của chúng tôi có thể thay đổi theo thời gian. Chúng tôi sẽ đăng bất kỳ thay đổi
                      chính sách cookie nào trên trang này và, nếu những thay đổi đó quan trọng, chúng tôi sẽ cung cấp thông
                      báo nổi bật hơn. Chúng tôi khuyến khích bạn xem lại chính sách này định kỳ để được thông báo về cách
                      chúng tôi sử dụng cookie.
                    </p>
                  </div>
                </div>
              </section>

              {/* Section 6: Thông tin Liên hệ */}
              <section className="bg-white dark:bg-surface-dark p-6 rounded-xl border border-gray-200 dark:border-gray-800">
                <div className="flex items-start gap-4">
                  <div className="flex-shrink-0">
                    <Mail className="w-6 h-6 text-primary" />
                  </div>
                  <div>
                    <h2 className="text-2xl font-bold text-text-primary-light dark:text-text-primary-dark mb-4">
                      6. Thông tin Liên hệ
                    </h2>
                    <p className="text-text-secondary-light dark:text-text-secondary-dark leading-relaxed mb-4">
                      Nếu bạn có bất kỳ câu hỏi nào về Chính sách Cookie này, vui lòng liên hệ với chúng tôi:
                    </p>
                    <ul className="space-y-2 text-text-secondary-light dark:text-text-secondary-dark">
                      <li>
                        <strong className="text-text-primary-light dark:text-text-primary-dark">Email:</strong> privacy@shopnest.com
                      </li>
                      <li>
                        <strong className="text-text-primary-light dark:text-text-primary-dark">Điện thoại:</strong> 1900 123 456
                      </li>
                      <li>
                        <strong className="text-text-primary-light dark:text-text-primary-dark">Địa chỉ:</strong> 123 Đường ABC, Quận 1, Thành phố Hồ Chí Minh, Việt Nam
                      </li>
                    </ul>
                  </div>
                </div>
              </section>
            </div>
          </div>
        </div>
      </main>
      <Footer />
    </ToastProvider>
  );
}
