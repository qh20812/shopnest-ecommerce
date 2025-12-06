import React from 'react';
import TopNav from '../components/top-nav';
import Footer from '../components/footer';
import { ToastProvider } from '../lib/toastContext';
import { Database, Target, Share2, ShieldCheck, Cookie, FileEdit, Mail } from 'lucide-react';

export default function ChinhSachBaoMat() {
  return (
    <ToastProvider>
      <TopNav />
      <main className="flex-1">
        {/* Hero Section */}
        <div className="bg-surface-light dark:bg-surface-dark py-16 md:py-24">
          <div className="container mx-auto px-4">
            <div className="max-w-4xl mx-auto text-center">
              <h1 className="text-4xl md:text-5xl font-extrabold text-text-primary-light dark:text-text-primary-dark tracking-tighter">
                Chính sách Bảo mật
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
              {/* Section 1: Dữ liệu thu thập */}
              <section className="bg-white dark:bg-surface-dark p-6 rounded-xl border border-gray-200 dark:border-gray-800">
                <div className="flex items-start gap-4">
                  <div className="flex-shrink-0">
                    <Database className="w-6 h-6 text-primary" />
                  </div>
                  <div>
                    <h2 className="text-2xl font-bold text-text-primary-light dark:text-text-primary-dark mb-4">
                      1. Dữ liệu chúng tôi thu thập
                    </h2>
                    <p className="text-text-secondary-light dark:text-text-secondary-dark leading-relaxed">
                      Chúng tôi thu thập thông tin để cung cấp dịch vụ tốt hơn cho tất cả người dùng. Các loại dữ liệu cá nhân chúng tôi thu thập bao gồm: thông tin bạn cung cấp cho chúng tôi (như tên, email, địa chỉ giao hàng), thông tin chúng tôi nhận được từ việc bạn sử dụng dịch vụ của chúng tôi (như lịch sử tìm kiếm, dữ liệu vị trí), và thông tin từ các nguồn của bên thứ ba.
                    </p>
                  </div>
                </div>
              </section>

              {/* Section 2: Mục đích sử dụng */}
              <section className="bg-white dark:bg-surface-dark p-6 rounded-xl border border-gray-200 dark:border-gray-800">
                <div className="flex items-start gap-4">
                  <div className="flex-shrink-0">
                    <Target className="w-6 h-6 text-primary" />
                  </div>
                  <div>
                    <h2 className="text-2xl font-bold text-text-primary-light dark:text-text-primary-dark mb-4">
                      2. Mục đích sử dụng dữ liệu
                    </h2>
                    <p className="text-text-secondary-light dark:text-text-secondary-dark leading-relaxed">
                      Dữ liệu của bạn được sử dụng để xử lý đơn hàng, cung cấp, duy trì và cải thiện dịch vụ của chúng tôi. Chúng tôi cũng sử dụng thông tin này để liên lạc với bạn, cá nhân hóa trải nghiệm của bạn và đảm bảo an toàn, bảo mật. Chúng tôi sẽ không sử dụng thông tin của bạn cho các mục đích khác mà không có sự đồng ý của bạn.
                    </p>
                  </div>
                </div>
              </section>

              {/* Section 3: Chia sẻ dữ liệu */}
              <section className="bg-white dark:bg-surface-dark p-6 rounded-xl border border-gray-200 dark:border-gray-800">
                <div className="flex items-start gap-4">
                  <div className="flex-shrink-0">
                    <Share2 className="w-6 h-6 text-primary" />
                  </div>
                  <div>
                    <h2 className="text-2xl font-bold text-text-primary-light dark:text-text-primary-dark mb-4">
                      3. Chia sẻ dữ liệu
                    </h2>
                    <p className="text-text-secondary-light dark:text-text-secondary-dark leading-relaxed">
                      ShopNest không chia sẻ thông tin cá nhân của bạn với các công ty, tổ chức hoặc cá nhân bên ngoài trừ các trường hợp sau: có sự đồng ý của bạn, cho các đối tác xử lý bên ngoài đáng tin cậy của chúng tôi, hoặc vì lý do pháp lý. Chúng tôi cam kết bảo vệ dữ liệu của bạn và yêu cầu các bên thứ ba tuân thủ các biện pháp bảo mật nghiêm ngặt.
                    </p>
                  </div>
                </div>
              </section>

              {/* Section 4: Quyền của người dùng */}
              <section className="bg-white dark:bg-surface-dark p-6 rounded-xl border border-gray-200 dark:border-gray-800">
                <div className="flex items-start gap-4">
                  <div className="flex-shrink-0">
                    <ShieldCheck className="w-6 h-6 text-primary" />
                  </div>
                  <div>
                    <h2 className="text-2xl font-bold text-text-primary-light dark:text-text-primary-dark mb-4">
                      4. Quyền của người dùng
                    </h2>
                    <p className="text-text-secondary-light dark:text-text-secondary-dark leading-relaxed">
                      Bạn có quyền truy cập, sửa đổi, xóa hoặc hạn chế việc xử lý dữ liệu cá nhân của mình. Bạn cũng có quyền phản đối việc xử lý dữ liệu và quyền di chuyển dữ liệu. Để thực hiện các quyền này, vui lòng liên hệ với chúng tôi qua thông tin được cung cấp bên dưới. Chúng tôi sẽ phản hồi yêu cầu của bạn trong thời gian hợp lý.
                    </p>
                  </div>
                </div>
              </section>

              {/* Section 5: Cookie và Công nghệ Theo dõi */}
              <section className="bg-white dark:bg-surface-dark p-6 rounded-xl border border-gray-200 dark:border-gray-800">
                <div className="flex items-start gap-4">
                  <div className="flex-shrink-0">
                    <Cookie className="w-6 h-6 text-primary" />
                  </div>
                  <div>
                    <h2 className="text-2xl font-bold text-text-primary-light dark:text-text-primary-dark mb-4">
                      5. Cookie và Công nghệ Theo dõi
                    </h2>
                    <p className="text-text-secondary-light dark:text-text-secondary-dark leading-relaxed">
                      Chúng tôi sử dụng cookie và các công nghệ theo dõi tương tự để thu thập và lưu trữ thông tin khi bạn truy cập dịch vụ của chúng tôi. Điều này giúp chúng tôi phân tích lưu lượng truy cập, cá nhân hóa nội dung và quảng cáo, và cải thiện trải nghiệm tổng thể của bạn. Bạn có thể kiểm soát việc sử dụng cookie thông qua cài đặt trình duyệt của mình.
                    </p>
                  </div>
                </div>
              </section>

              {/* Section 6: Thay đổi Chính sách */}
              <section className="bg-white dark:bg-surface-dark p-6 rounded-xl border border-gray-200 dark:border-gray-800">
                <div className="flex items-start gap-4">
                  <div className="flex-shrink-0">
                    <FileEdit className="w-6 h-6 text-primary" />
                  </div>
                  <div>
                    <h2 className="text-2xl font-bold text-text-primary-light dark:text-text-primary-dark mb-4">
                      6. Thay đổi Chính sách
                    </h2>
                    <p className="text-text-secondary-light dark:text-text-secondary-dark leading-relaxed">
                      Chính sách Bảo mật của chúng tôi có thể thay đổi theo thời gian. Chúng tôi sẽ đăng bất kỳ thay đổi chính sách bảo mật nào trên trang này và, nếu những thay đổi đó quan trọng, chúng tôi sẽ cung cấp thông báo nổi bật hơn (bao gồm, đối với một số dịch vụ nhất định, thông báo qua email về các thay đổi chính sách bảo mật).
                    </p>
                  </div>
                </div>
              </section>

              {/* Section 7: Thông tin Liên hệ */}
              <section className="bg-white dark:bg-surface-dark p-6 rounded-xl border border-gray-200 dark:border-gray-800">
                <div className="flex items-start gap-4">
                  <div className="flex-shrink-0">
                    <Mail className="w-6 h-6 text-primary" />
                  </div>
                  <div>
                    <h2 className="text-2xl font-bold text-text-primary-light dark:text-text-primary-dark mb-4">
                      7. Thông tin Liên hệ
                    </h2>
                    <p className="text-text-secondary-light dark:text-text-secondary-dark leading-relaxed mb-4">
                      Nếu bạn có bất kỳ câu hỏi nào về Chính sách Bảo mật này, vui lòng liên hệ với chúng tôi:
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
