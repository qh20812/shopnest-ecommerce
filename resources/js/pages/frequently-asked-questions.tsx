import { useState } from 'react';
import { ChevronDown, Search, MessageCircle } from 'lucide-react';
import TopNav from '../components/top-nav';
import Footer from '../components/footer';
import { ToastProvider } from '../lib/toastContext';

interface FAQItem {
    question: string;
    answer: string;
}

interface FAQSection {
    title: string;
    items: FAQItem[];
}

export default function FrequentlyAskedQuestions() {
    const [searchQuery, setSearchQuery] = useState('');
    const [openItems, setOpenItems] = useState<Set<string>>(new Set());

    const faqSections: FAQSection[] = [
        {
            title: 'Đơn hàng',
            items: [
                {
                    question: 'Làm thế nào để tôi theo dõi đơn hàng của mình?',
                    answer: 'Bạn có thể theo dõi đơn hàng của mình bằng cách truy cập vào trang \'Đơn hàng của tôi\' trong tài khoản của bạn. Chúng tôi cũng sẽ gửi email cập nhật trạng thái đơn hàng cho bạn.',
                },
                {
                    question: 'Tôi có thể thay đổi hoặc hủy đơn hàng không?',
                    answer: 'Nếu đơn hàng của bạn chưa được vận chuyển, bạn có thể yêu cầu thay đổi hoặc hủy bỏ. Vui lòng liên hệ với bộ phận chăm sóc khách hàng của chúng tôi càng sớm càng tốt để được hỗ trợ.',
                },
            ],
        },
        {
            title: 'Thanh toán',
            items: [
                {
                    question: 'ShopNest chấp nhận những phương thức thanh toán nào?',
                    answer: 'Chúng tôi chấp nhận nhiều phương thức thanh toán, bao gồm thẻ tín dụng/ghi nợ (Visa, MasterCard), chuyển khoản ngân hàng, và thanh toán khi nhận hàng (COD).',
                },
                {
                    question: 'Thanh toán trên trang web của bạn có an toàn không?',
                    answer: 'Tuyệt đối an toàn. Chúng tôi sử dụng mã hóa SSL để bảo vệ thông tin cá nhân và thanh toán của bạn. Mọi giao dịch đều được xử lý thông qua các cổng thanh toán an toàn và uy tín.',
                },
            ],
        },
        {
            title: 'Vận chuyển',
            items: [
                {
                    question: 'Mất bao lâu để tôi nhận được hàng?',
                    answer: 'Thời gian giao hàng tiêu chuẩn là từ 2-5 ngày làm việc, tùy thuộc vào địa chỉ nhận hàng của bạn. Bạn sẽ nhận được thông tin ước tính thời gian giao hàng cụ thể khi đặt hàng.',
                },
            ],
        },
        {
            title: 'Trả hàng & Hoàn tiền',
            items: [
                {
                    question: 'Chính sách trả hàng của ShopNest là gì?',
                    answer: 'Chúng tôi cho phép trả hàng trong vòng 30 ngày kể từ ngày nhận hàng đối với hầu hết các sản phẩm, miễn là chúng còn nguyên vẹn, chưa qua sử dụng và còn đầy đủ tem mác. Vui lòng tham khảo trang Chính sách Đổi trả của chúng tôi để biết thêm chi tiết.',
                },
            ],
        },
    ];

    const toggleItem = (sectionTitle: string, question: string) => {
        const key = `${sectionTitle}-${question}`;
        const newOpenItems = new Set(openItems);
        if (newOpenItems.has(key)) {
            newOpenItems.delete(key);
        } else {
            newOpenItems.add(key);
        }
        setOpenItems(newOpenItems);
    };

    const isItemOpen = (sectionTitle: string, question: string) => {
        return openItems.has(`${sectionTitle}-${question}`);
    };

    const filteredSections = faqSections
        .map(section => ({
            ...section,
            items: section.items.filter(
                item =>
                    item.question.toLowerCase().includes(searchQuery.toLowerCase()) ||
                    item.answer.toLowerCase().includes(searchQuery.toLowerCase())
            ),
        }))
        .filter(section => section.items.length > 0);

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
                                Câu hỏi thường gặp
                            </h1>
                            <p className="mx-auto max-w-2xl text-lg text-muted-foreground">
                                Chúng tôi đã tổng hợp các câu hỏi thường gặp nhất để giúp bạn.
                            </p>
                        </div>

                        {/* Search */}
                        <div className="relative mb-12">
                            <div className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                <Search className="h-5 w-5 text-muted-foreground" />
                            </div>
                            <input
                                type="search"
                                className="h-14 w-full rounded-xl border border-border bg-card pl-12 pr-4 text-foreground placeholder:text-muted-foreground focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary transition-colors"
                                placeholder="Tìm kiếm câu hỏi..."
                                value={searchQuery}
                                onChange={(e) => setSearchQuery(e.target.value)}
                            />
                        </div>

                        {/* FAQ Sections */}
                        <div className="space-y-10">
                            {filteredSections.length > 0 ? (
                                filteredSections.map((section, sectionIndex) => (
                                    <div key={sectionIndex}>
                                        <h2 className="mb-6 border-b-2 border-primary pb-2 text-2xl font-bold text-foreground">
                                            {section.title}
                                        </h2>
                                        <div className="space-y-4">
                                            {section.items.map((item, itemIndex) => {
                                                const isOpen = isItemOpen(section.title, item.question);
                                                return (
                                                    <div
                                                        key={itemIndex}
                                                        className="cursor-pointer rounded-lg bg-card p-6"
                                                    >
                                                        <button
                                                            onClick={() => toggleItem(section.title, item.question)}
                                                            className="flex w-full items-center justify-between text-left"
                                                        >
                                                            <span className="text-lg font-medium text-foreground">
                                                                {item.question}
                                                            </span>
                                                            <ChevronDown
                                                                className={`h-5 w-5 text-foreground transition-transform duration-300 ${isOpen ? 'rotate-180' : ''
                                                                    }`}
                                                            />
                                                        </button>
                                                        {isOpen && (
                                                            <p className="mt-4 leading-relaxed text-muted-foreground">
                                                                {item.answer}
                                                            </p>
                                                        )}
                                                    </div>
                                                );
                                            })}
                                        </div>
                                    </div>
                                ))
                            ) : (
                                <div className="py-12 text-center">
                                    <p className="text-lg text-muted-foreground">
                                        Không tìm thấy câu hỏi phù hợp. Vui lòng thử từ khóa khác.
                                    </p>
                                </div>
                            )}
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
