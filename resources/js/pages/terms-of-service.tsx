import React from 'react';
import TopNav from '../components/top-nav';
import Footer from '../components/footer';
import { ToastProvider } from '../lib/toastContext';
import { FileText, Shield, Scale, AlertTriangle, Gavel, Mail } from 'lucide-react';

export default function TermsOfService() {
  return (
    <ToastProvider>
      <TopNav />
      <main className="flex-1">
        {/* Hero Section */}
        <div className="bg-surface-light dark:bg-surface-dark py-16 md:py-24">
          <div className="container mx-auto px-4">
            <div className="max-w-4xl mx-auto text-center">
              <h1 className="text-4xl md:text-5xl font-extrabold text-text-primary-light dark:text-text-primary-dark tracking-tighter">
                Điều khoản Dịch vụ
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
              {/* Section 1: Chấp thuận Điều khoản */}
              <section className="bg-white dark:bg-surface-dark p-6 rounded-xl border border-gray-200 dark:border-gray-800">
                <div className="flex items-start gap-4">
                  <div className="flex-shrink-0">
                    <FileText className="w-6 h-6 text-primary" />
                  </div>
                  <div>
                    <h2 className="text-2xl font-bold text-text-primary-light dark:text-text-primary-dark mb-4">
                      1. Chấp thuận Điều khoản
                    </h2>
                    <p className="text-text-secondary-light dark:text-text-secondary-dark leading-relaxed">
                      Bằng cách truy cập và sử dụng trang web ShopNest ("Dịch vụ"), bạn đồng ý bị ràng buộc bởi các Điều
                      khoản Dịch vụ này ("Điều khoản"). Nếu bạn không đồng ý với bất kỳ phần nào của điều khoản, bạn không
                      được phép truy cập Dịch vụ. Vui lòng đọc kỹ các điều khoản này trước khi sử dụng trang web của chúng
                      tôi.
                    </p>
                  </div>
                </div>
              </section>

              {/* Section 2: Quyền và Trách nhiệm */}
              <section className="bg-white dark:bg-surface-dark p-6 rounded-xl border border-gray-200 dark:border-gray-800">
                <div className="flex items-start gap-4">
                  <div className="flex-shrink-0">
                    <Shield className="w-6 h-6 text-primary" />
                  </div>
                  <div>
                    <h2 className="text-2xl font-bold text-text-primary-light dark:text-text-primary-dark mb-4">
                      2. Quyền và Trách nhiệm của Người dùng
                    </h2>
                    <p className="text-text-secondary-light dark:text-text-secondary-dark leading-relaxed">
                      Bạn có trách nhiệm duy trì tính bảo mật của tài khoản và mật khẩu của mình. Bạn đồng ý chấp nhận
                      trách nhiệm cho tất cả các hoạt động xảy ra dưới tài khoản hoặc mật khẩu của bạn. Bạn không được sử
                      dụng Dịch vụ cho bất kỳ mục đích bất hợp pháp hoặc trái phép nào. Bạn không được, trong quá trình sử
                      dụng Dịch vụ, vi phạm bất kỳ luật nào trong khu vực tài phán của bạn.
                    </p>
                  </div>
                </div>
              </section>

              {/* Section 3: Quyền Sở hữu Trí tuệ */}
              <section className="bg-white dark:bg-surface-dark p-6 rounded-xl border border-gray-200 dark:border-gray-800">
                <div className="flex items-start gap-4">
                  <div className="flex-shrink-0">
                    <Scale className="w-6 h-6 text-primary" />
                  </div>
                  <div>
                    <h2 className="text-2xl font-bold text-text-primary-light dark:text-text-primary-dark mb-4">
                      3. Quyền Sở hữu Trí tuệ
                    </h2>
                    <p className="text-text-secondary-light dark:text-text-secondary-dark leading-relaxed">
                      Dịch vụ và nội dung gốc, các tính năng và chức năng của nó là và sẽ vẫn là tài sản độc quyền của
                      ShopNest và các nhà cấp phép của nó. Dịch vụ được bảo vệ bởi bản quyền, nhãn hiệu và các luật khác của
                      cả Việt Nam và các quốc gia khác. Các nhãn hiệu và trang phục thương mại của chúng tôi không được sử
                      dụng liên quan đến bất kỳ sản phẩm hoặc dịch vụ nào mà không có sự đồng ý trước bằng văn bản của
                      ShopNest.
                    </p>
                  </div>
                </div>
              </section>

              {/* Section 4: Giới hạn Trách nhiệm */}
              <section className="bg-white dark:bg-surface-dark p-6 rounded-xl border border-gray-200 dark:border-gray-800">
                <div className="flex items-start gap-4">
                  <div className="flex-shrink-0">
                    <AlertTriangle className="w-6 h-6 text-primary" />
                  </div>
                  <div>
                    <h2 className="text-2xl font-bold text-text-primary-light dark:text-text-primary-dark mb-4">
                      4. Giới hạn Trách nhiệm
                    </h2>
                    <p className="text-text-secondary-light dark:text-text-secondary-dark leading-relaxed">
                      Trong mọi trường hợp, ShopNest, cũng như giám đốc, nhân viên, đối tác, đại lý, nhà cung cấp hoặc chi
                      nhánh của nó, sẽ không chịu trách nhiệm cho bất kỳ thiệt hại gián tiếp, ngẫu nhiên, đặc biệt, do hậu
                      quả hoặc trừng phạt nào, bao gồm nhưng không giới hạn ở việc mất lợi nhuận, dữ liệu, việc sử dụng,
                      thiện chí, hoặc các tổn thất vô hình khác, phát sinh từ (i) việc bạn truy cập hoặc sử dụng hoặc không
                      thể truy cập hoặc sử dụng Dịch vụ; (ii) bất kỳ hành vi hoặc nội dung nào của bất kỳ bên thứ ba nào
                      trên Dịch vụ.
                    </p>
                  </div>
                </div>
              </section>

              {/* Section 5: Giải quyết Tranh chấp */}
              <section className="bg-white dark:bg-surface-dark p-6 rounded-xl border border-gray-200 dark:border-gray-800">
                <div className="flex items-start gap-4">
                  <div className="flex-shrink-0">
                    <Gavel className="w-6 h-6 text-primary" />
                  </div>
                  <div>
                    <h2 className="text-2xl font-bold text-text-primary-light dark:text-text-primary-dark mb-4">
                      5. Quy định về Giải quyết Tranh chấp
                    </h2>
                    <p className="text-text-secondary-light dark:text-text-secondary-dark leading-relaxed">
                      Mọi tranh chấp phát sinh từ hoặc liên quan đến các Điều khoản này hoặc việc sử dụng Dịch vụ sẽ được
                      giải quyết thông qua thương lượng hòa giải. Nếu không thể giải quyết thông qua thương lượng, tranh
                      chấp sẽ được đưa ra giải quyết tại tòa án có thẩm quyền tại Thành phố Hồ Chí Minh theo quy định của
                      pháp luật Việt Nam.
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
                      Nếu bạn có bất kỳ câu hỏi nào về các Điều khoản Dịch vụ này, vui lòng liên hệ với chúng tôi:
                    </p>
                    <ul className="space-y-2 text-text-secondary-light dark:text-text-secondary-dark">
                      <li>
                        <strong className="text-text-primary-light dark:text-text-primary-dark">Email:</strong> support@shopnest.com
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
