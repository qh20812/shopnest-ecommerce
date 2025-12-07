import { CheckCircle, Clock, RefreshCw, XCircle, CreditCard, MessageCircle } from 'lucide-react';
import TopNav from '../components/top-nav';
import Footer from '../components/footer';
import { ToastProvider } from '../lib/toastContext';

export default function ReturnPolicy() {
    return (
        <ToastProvider>
            <div className="relative flex min-h-screen w-full flex-col overflow-x-hidden bg-background">
                <TopNav />

            <main className="flex-1">
                <div className="container mx-auto px-4 py-12 md:py-16">
                    <div className="mx-auto max-w-4xl">
                        {/* Header */}
                        <div className="mb-12 text-center">
                            <h1 className="mb-4 text-4xl font-extrabold tracking-tight text-foreground md:text-5xl">
                                Chính sách đổi trả
                            </h1>
                            <p className="mx-auto max-w-3xl text-lg text-muted-foreground">
                                Tại ShopNest, sự hài lòng của bạn là ưu tiên hàng đầu của chúng tôi. Dưới đây là các quy định về việc đổi trả sản phẩm để đảm bảo quyền lợi của khách hàng.
                            </p>
                        </div>

                        {/* Policy Sections */}
                        <div className="space-y-10 leading-relaxed text-muted-foreground">
                            {/* Điều kiện đổi trả */}
                            <div className="rounded-xl bg-card p-8">
                                <h2 className="mb-4 flex items-center gap-3 text-2xl font-bold text-foreground">
                                    <CheckCircle className="h-7 w-7 text-primary" />
                                    Điều kiện đổi trả
                                </h2>
                                <p className="mb-4">
                                    Để được chấp nhận đổi trả, sản phẩm của bạn phải đáp ứng các điều kiện sau:
                                </p>
                                <ul className="list-inside list-disc space-y-2 pl-4">
                                    <li>Sản phẩm còn nguyên vẹn, chưa qua sử dụng, giặt ủi.</li>
                                    <li>Sản phẩm còn đầy đủ tem, nhãn mác và bao bì gốc.</li>
                                    <li>Sản phẩm không bị dơ bẩn, trầy xước, hư hỏng bởi những tác nhân bên ngoài.</li>
                                    <li>Có hóa đơn mua hàng hoặc biên nhận giao hàng từ ShopNest.</li>
                                </ul>
                            </div>

                            {/* Thời gian đổi trả */}
                            <div className="rounded-xl bg-card p-8">
                                <h2 className="mb-4 flex items-center gap-3 text-2xl font-bold text-foreground">
                                    <Clock className="h-7 w-7 text-primary" />
                                    Thời gian đổi trả
                                </h2>
                                <p>
                                    Khách hàng có{' '}
                                    <span className="font-bold text-foreground">30 ngày</span> kể từ ngày nhận hàng để yêu cầu đổi hoặc trả sản phẩm. Sau thời gian này, chúng tôi rất tiếc không thể hỗ trợ yêu cầu của bạn.
                                </p>
                            </div>

                            {/* Quy trình đổi trả */}
                            <div className="rounded-xl bg-card p-8">
                                <h2 className="mb-4 flex items-center gap-3 text-2xl font-bold text-foreground">
                                    <RefreshCw className="h-7 w-7 text-primary" />
                                    Quy trình đổi trả
                                </h2>
                                <p className="mb-4">Vui lòng thực hiện theo các bước sau để đổi trả sản phẩm:</p>
                                <ol className="list-inside list-decimal space-y-3 pl-4">
                                    <li>
                                        <span className="font-medium text-foreground">Liên hệ Chăm sóc khách hàng:</span>{' '}
                                        Gửi email đến support@shopnest.com hoặc gọi hotline của chúng tôi để thông báo về yêu cầu đổi trả. Cung cấp mã đơn hàng và lý do đổi trả.
                                    </li>
                                    <li>
                                        <span className="font-medium text-foreground">Đóng gói sản phẩm:</span>{' '}
                                        Đóng gói sản phẩm cẩn thận cùng với hóa đơn và các phụ kiện đi kèm (nếu có).
                                    </li>
                                    <li>
                                        <span className="font-medium text-foreground">Gửi hàng về ShopNest:</span>{' '}
                                        Gửi sản phẩm về địa chỉ được cung cấp bởi nhân viên chăm sóc khách hàng. Vui lòng giữ lại biên nhận gửi hàng.
                                    </li>
                                    <li>
                                        <span className="font-medium text-foreground">Xử lý và hoàn tiền/đổi hàng:</span>{' '}
                                        Sau khi nhận và kiểm tra sản phẩm, chúng tôi sẽ tiến hành đổi sản phẩm mới hoặc hoàn tiền cho bạn trong vòng 5-7 ngày làm việc.
                                    </li>
                                </ol>
                            </div>

                            {/* Sản phẩm không được đổi trả */}
                            <div className="rounded-xl bg-card p-8">
                                <h2 className="mb-4 flex items-center gap-3 text-2xl font-bold text-foreground">
                                    <XCircle className="h-7 w-7 text-primary" />
                                    Sản phẩm không được đổi trả
                                </h2>
                                <p className="mb-4">
                                    Một số sản phẩm không được áp dụng chính sách đổi trả, bao gồm:
                                </p>
                                <ul className="list-inside list-disc space-y-2 pl-4">
                                    <li>Sản phẩm trong chương trình giảm giá, khuyến mãi cuối cùng.</li>
                                    <li>Đồ lót, đồ bơi vì lý do vệ sinh.</li>
                                    <li>Sản phẩm được đặt hàng theo yêu cầu riêng.</li>
                                    <li>Phiếu quà tặng (gift cards).</li>
                                </ul>
                            </div>

                            {/* Phương thức hoàn tiền */}
                            <div className="rounded-xl bg-card p-8">
                                <h2 className="mb-4 flex items-center gap-3 text-2xl font-bold text-foreground">
                                    <CreditCard className="h-7 w-7 text-primary" />
                                    Phương thức hoàn tiền
                                </h2>
                                <p>
                                    Chúng tôi sẽ hoàn tiền cho bạn qua phương thức thanh toán ban đầu. Nếu bạn thanh toán bằng COD, chúng tôi sẽ hoàn tiền qua chuyển khoản ngân hàng. Thời gian nhận được tiền hoàn có thể thay đổi tùy thuộc vào ngân hàng của bạn.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <Footer />

            {/* Floating Chat Button */}
            <button
                className="fixed bottom-6 right-6 flex h-16 w-16 cursor-pointer items-center justify-center rounded-full bg-secondary text-white shadow-lg transition-transform hover:scale-110"
                aria-label="Chat support"
            >
                <MessageCircle className="h-8 w-8" fill="currentColor" />
            </button>
            </div>
        </ToastProvider>
    );
}
