import { ShoppingBag, ChevronLeft, ChevronRight, MessageCircle, X, Minus, Heart, FolderOpen, Timer, Lightbulb, TrendingUp, Image } from 'lucide-react';
import { useEffect, useRef, useState } from 'react';
import { ToastProvider, useToast } from '../lib/toastContext';
import { SkeletonPage } from '../components/skeletons';
import { usePageLoading } from '../hooks/usePageLoading';
import TopNav from '../components/top-nav';
import Footer from '../components/footer';
import { usePage, Link } from '@inertiajs/react';

interface Category {
    id: number;
    name: string;
    slug?: string;
    icon: string;
    image_url?: string;
}

interface Product {
    id: number;
    name: string;
    image: string;
    price: number;
    oldPrice?: number;
    discount?: number;
    rating?: number;
    reviews?: number;
    category?: string;
    brand?: string;
}

interface FlashSaleEvent {
    id: number;
    name: string;
    start_time: string;
    end_time: string;
}

interface FlashSale {
    event: FlashSaleEvent;
    products: Product[];
}

interface PageProps extends Record<string, unknown> {
    categories: Category[];
    flashSale: FlashSale | null;
    suggestedProducts: Product[];
    bestSellers: Product[];
    banners?: Array<{ id: number; title?: string | null; subtitle?: string | null; link?: string | null; image: string; alt?: string | null; placement?: string | null }>;
    canRegister?: boolean;
}

function WelcomeContent() {
    const isLoading = usePageLoading({ delay: 300, minLoadingTime: 1500 });
    const { showInfo, showSuccess } = useToast();
    const { categories = [], flashSale = null, suggestedProducts = [], bestSellers = [], banners = [] } = usePage<PageProps>().props;
    
    const heroImages = banners.length > 0 ? banners.map((b) => b.image) : [
        "https://lh3.googleusercontent.com/aida-public/AB6AXuCJnd0a7KUIVBOrCCbTD_XI8zjrdFzSTEoaq6b4K3q-WNNZ7Q2Bkcw0YRwFrtyBeGOTDM5kMZeV_Dsx87yglrclO67vmbXOLiWdKSRNsRDazp8Z9RBcwMv21ccDiy7Ejn2xRN_TGm8jLkPPVhgUwBUR7T-IYai9T9Zyr0elbxlqZXV_Nd24e1s71UbqVJ4c0tevgmsrAc6DsnRS8Fxgk8XcjjO2uEtvshEWzXm5XhzWDHs0QAirR_c2Q2e-Q-QD-evUeWdCr7mUVtc",
        'https://lh3.googleusercontent.com/aida-public/AB6AXuBZ0SW5aKwDpK6K29I0-SDUGeyHd5U9d-oTB6nVby7PUkMp_xF33sURbT7peE7FmT8Zt7YVcOcEjwycU69a5yK1xnsm8F53Zg4XzdbW9uaOVrxIm8abstmpf0uSmb5fXmxzQrK3sicYxX_htS0-n7kj4AYKSb0gF4UDqHIKfPb2Oe1RF7IRm6qpoxckB_pwQzjRtqj9I6Z-rG6kH1kKcTD7BM-PyBW2cDgwUtbCZ57daKO8Ps11liX3yrCIM33KdCjb1EU8vWhh_Tw',
        'https://lh3.googleusercontent.com/aida-public/AB6AXuDhVzAsMXFeYJb0HP5k8OnmdwT2iGOTIN4CFswYpf6qb2wbzLbIo1quOJRd5j0RbiDls3gBRx2MapPg4c3M4iNkeU02ZNhxSshm4qJBK3WX3FVt1pbkc2CFr2GjigvzaXYQhxtCRptoOSnPgKmr4qiNKvgt3VdQkRth7LcpFeID2wd53twtEgslNxRpZeai5Or15GLacvyGv-vuAaWagDaMUDXoIpLom4Pz0xHUhvHWrwS3p5pYsZjkn-6cTC_AI5hfd3enk7SK21M',
    ];

    const [activeSlide, setActiveSlide] = useState(0);
    const [isPaused, setIsPaused] = useState(false);
    const autoplayRef = useRef<number | null>(null);
    
    // Use flash sale from controller
    const flashSaleProducts = flashSale?.products || [];
    const hasFlashSale = flashSale !== null && flashSaleProducts.length > 0;

    const bestSellerRef = useRef<HTMLDivElement | null>(null);
    const suggestedRef = useRef<HTMLDivElement | null>(null);
    const categoriesRef = useRef<HTMLDivElement | null>(null);

    const scrollBestSellers = (direction: 'left' | 'right') => {
        const node = bestSellerRef.current;
        if (!node) return;
        // Use the measured width of the first card plus gap so scrolling aligns correctly
        const firstChild = node.firstElementChild as HTMLElement | null;
        const cardWidth = firstChild ? Math.round(firstChild.getBoundingClientRect().width) : 280;
        const gap = 24; // gap-6 == 1.5rem == 24px
        const amount = cardWidth + gap;
        node.scrollBy({ left: direction === 'left' ? -amount : amount, behavior: 'smooth' });
    };

    const scrollSuggested = (direction: 'left' | 'right') => {
        const node = suggestedRef.current;
        if (!node) return;
        const firstChild = node.firstElementChild as HTMLElement | null;
        const cardWidth = firstChild ? Math.round(firstChild.getBoundingClientRect().width) : 280;
        const gap = 24; // gap-6 == 1.5rem == 24px
        const amount = cardWidth + gap;
        node.scrollBy({ left: direction === 'left' ? -amount : amount, behavior: 'smooth' });
    };

    const scrollCategories = (direction: 'left' | 'right') => {
        const node = categoriesRef.current;
        if (!node) return;
        const firstChild = node.firstElementChild as HTMLElement | null;
        const cardWidth = firstChild ? Math.round(firstChild.getBoundingClientRect().width) : 160;
        const gap = 16; // gap-4 == 1rem == 16px
        const amount = cardWidth + gap;
        node.scrollBy({ left: direction === 'left' ? -amount : amount, behavior: 'smooth' });
    };

    const formatPrice = (price: number) =>
        new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND',
        }).format(price);

    const renderStars = (rating: number) => {
        const stars = [];
        const fullStars = Math.floor(rating);
        const hasHalfStar = rating % 1 !== 0;

        for (let i = 0; i < fullStars; i++) {
            stars.push(
                <span key={`star-${i}`} className="text-warning">
                    ★
                </span>
            );
        }
        if (hasHalfStar) {
            stars.push(
                <span key="half-star" className="text-warning">
                    ★
                </span>
            );
        }
        for (let i = stars.length; i < 5; i++) {
            stars.push(
                <span key={`empty-${i}`} className="text-muted-foreground">
                    ★
                </span>
            );
        }
        return stars;
    };

    // Autoplay carousel
    useEffect(() => {
        if (autoplayRef.current) {
            window.clearInterval(autoplayRef.current);
            autoplayRef.current = null;
        }
        if (!isPaused) {
            autoplayRef.current = window.setInterval(() => {
                setActiveSlide((s) => (s + 1) % heroImages.length);
            }, 4000);
        }
        return () => {
            if (autoplayRef.current) {
                window.clearInterval(autoplayRef.current);
                autoplayRef.current = null;
            }
        };
    }, [isPaused, heroImages.length]);

    const prevSlide = () => setActiveSlide((s) => (s - 1 + heroImages.length) % heroImages.length);
    const nextSlide = () => setActiveSlide((s) => (s + 1) % heroImages.length);

    const [isChatOpen, setIsChatOpen] = useState(false);
    const [isChatMinimized, setIsChatMinimized] = useState(false);
    const [chatInput, setChatInput] = useState('');
    const [messages, setMessages] = useState<Array<{ id: number; from: 'bot' | 'user'; text: string; time?: string }>>([
        { id: 1, from: 'bot', text: 'Chào bạn! Mình là trợ lý ảo của ShopNest. Mình có thể giúp gì cho bạn hôm nay?', time: '10:30 AM' },
        { id: 2, from: 'user', text: 'Mình muốn tìm hiểu về chính sách đổi trả.', time: '10:31 AM' },
        { id: 3, from: 'bot', text: 'ShopNest hỗ trợ đổi trả sản phẩm trong vòng 30 ngày. Bạn có thể xem chi tiết tại đây.', time: '10:32 AM' },
    ]);

    const sendMessage = () => {
        if (!chatInput.trim()) return;
        const id = messages.length ? messages[messages.length - 1].id + 1 : 1;
        const now = new Date();
        const timeStr = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
        const userMessage = { id, from: 'user' as const, text: chatInput.trim(), time: timeStr };
        setMessages((prev) => [...prev, userMessage]);
        setChatInput('');
        showInfo('Tin nhắn đã gửi');
        // fake bot reply
        setTimeout(() => {
            const botId = id + 1;
            setMessages((prev) => [
                ...prev,
                { id: botId, from: 'bot' as const, text: 'Cảm ơn! Mình đã nhận thông tin. Hỗ trợ chi tiết sẽ được gửi sớm.', time: new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }) },
            ]);
        }, 800);
    };

    if (isLoading) {
        return <SkeletonPage variant="welcome" />;
    }

    return (
        <div className="relative flex min-h-screen w-full flex-col overflow-x-hidden bg-background text-foreground">
            {/* Header */}
            <TopNav />

            {/* Main Content */}
            <main className="flex-1">
                <div className="container mx-auto px-4 py-8 md:py-12">
                    {/* Hero Section */}
                    <section className="mb-12 md:mb-16">
                        {banners.length > 0 ? (
                            <div
                                className="group relative h-[500px] w-full overflow-hidden rounded-xl"
                                onMouseEnter={() => setIsPaused(true)}
                                onMouseLeave={() => setIsPaused(false)}
                            >
                                {/* Slides */}
                                {heroImages.map((src, idx) => (
                                    <div
                                        key={src}
                                        className={`absolute inset-0 bg-center bg-cover transition-opacity duration-700 ${
                                            idx === activeSlide ? 'opacity-100 z-10' : 'opacity-0 z-0'
                                        }`}
                                        style={{ backgroundImage: `url('${src}')` }}
                                    />
                                ))}

                                {/* Overlay content */}
                                <div className="absolute inset-0 flex items-center bg-black/40 z-20">
                                    <div className="w-full max-w-2xl px-8 text-white md:px-16">
                                        <h2 className="mb-4 text-4xl font-black leading-tight md:text-5xl">
                                            Siêu Sale Khai Trương - Giảm tới 50%
                                        </h2>
                                        <p className="mb-8 text-lg opacity-90">
                                            Khám phá các ưu đãi tuyệt vời trong ngày ra
                                            mắt của chúng tôi.
                                        </p>
                                        <button className="flex h-12 min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg bg-primary px-6 text-base font-bold leading-normal tracking-[0.015em] text-white transition-colors hover:bg-primary/90" onClick={() => showSuccess('Đã thêm vào giỏ hàng!')}>
                                            <span className="truncate">Mua sắm ngay</span>
                                        </button>
                                    </div>
                                </div>

                                {/* Prev / Next controls */}
                                <button
                                    aria-label="Previous slide"
                                    onClick={() => prevSlide()}
                                    className="absolute left-3 top-1/2 z-30 -translate-y-1/2 rounded-full bg-black/40 p-2 text-white transition-colors hover:bg-black/50"
                                >
                                    <ChevronLeft className="h-5 w-5" />
                                </button>
                                <button
                                    aria-label="Next slide"
                                    onClick={() => nextSlide()}
                                    className="absolute right-3 top-1/2 z-30 -translate-y-1/2 rounded-full bg-black/40 p-2 text-white transition-colors hover:bg-black/50"
                                >
                                    <ChevronRight className="h-5 w-5" />
                                </button>

                                {/* Indicators */}
                                <div className="absolute bottom-4 left-1/2 z-30 flex -translate-x-1/2 space-x-2">
                                    {heroImages.map((_, i) => (
                                        <button
                                            key={`dot-${i}`}
                                            onClick={() => setActiveSlide(i)}
                                            aria-label={`Go to slide ${i + 1}`}
                                            className={`h-2.5 w-2.5 rounded-full transition-opacity ${
                                                i === activeSlide ? 'bg-white opacity-90' : 'bg-white opacity-50'
                                            }`}
                                        />
                                    ))}
                                </div>
                            </div>
                        ) : (
                            <div className="relative w-full h-[500px] rounded-xl overflow-hidden bg-card flex flex-col items-center justify-center text-center p-8">
                                <div className="text-primary mb-4">
                                    <Image className="h-16 w-16 opacity-70" />
                                </div>
                                <h2 className="text-2xl md:text-3xl font-bold text-foreground mb-2">
                                    Chưa có banner nào để hiển thị
                                </h2>
                                <p className="text-lg text-muted-foreground mb-8 max-w-xl">
                                    Banner quảng cáo sẽ xuất hiện ở đây. Hãy quay lại sau để xem các chương trình khuyến mãi và sản phẩm mới nhất!
                                </p>
                                <button
                                    className="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-12 px-6 bg-primary text-white text-base font-bold leading-normal tracking-[0.015em] hover:bg-primary/90 transition-colors"
                                    onClick={() => showInfo('Khám phá cửa hàng')}
                                >
                                    <span className="truncate">Khám phá Cửa hàng</span>
                                </button>
                            </div>
                        )}
                    </section>

                    {/* Categories Section */}
                    <section className="mb-12 md:mb-16">
                        <h2 className="pb-0 text-[22px] font-bold leading-tight tracking-[-0.015em] text-foreground md:text-2xl mb-5">
                            Mua sắm theo Danh mục
                        </h2>
                        {categories.length > 0 ? (
                            <>
                                <div className="flex items-center justify-between mb-5">
                                    <div className="flex gap-2 ml-auto">
                                        <button
                                            aria-label="Previous categories"
                                            onClick={() => scrollCategories('left')}
                                            className="flex cursor-pointer items-center justify-center rounded-full h-10 w-10 bg-surface-light dark:bg-surface-dark text-foreground hover:bg-primary/10 hover:text-primary transition-colors"
                                        >
                                            <ChevronLeft className="h-5 w-5" />
                                        </button>
                                        <button
                                            aria-label="Next categories"
                                            onClick={() => scrollCategories('right')}
                                            className="flex cursor-pointer items-center justify-center rounded-full h-10 w-10 bg-surface-light dark:bg-surface-dark text-foreground hover:bg-primary/10 hover:text-primary transition-colors"
                                        >
                                            <ChevronRight className="h-5 w-5" />
                                        </button>
                                    </div>
                                </div>
                                <div className="-mx-2 px-2">
                                    <div
                                        ref={categoriesRef}
                                        className="flex items-stretch gap-4 pb-4 overflow-x-auto [-ms-scrollbar-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden snap-x snap-mandatory"
                                    >
                                        {categories.map((category) => (
                                            <div
                                                key={category.id}
                                                className="flex w-40 sm:w-44 md:w-48 lg:w-[calc((100%-96px)/7)] min-w-0 aspect-square cursor-pointer flex-col items-center justify-center gap-3 rounded-lg border border-border bg-card p-4 transition-all hover:-translate-y-1 hover:shadow-lg snap-start"
                                                style={{ flexShrink: 0 }}
                                            >
                                                <div className="text-4xl text-secondary">{category.icon}</div>
                                                <h3 className="text-center text-base font-bold leading-tight text-foreground">{category.name}</h3>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            </>
                        ) : (
                            <div className="flex flex-col items-center justify-center rounded-lg border border-dashed border-border bg-card p-8 min-h-[160px]">
                                <div className="text-secondary mb-3">
                                    <FolderOpen className="h-12 w-12 opacity-70" />
                                </div>
                                <h3 className="text-lg font-semibold text-foreground mb-1">
                                    Chưa có danh mục nào
                                </h3>
                                <p className="text-sm text-muted-foreground">
                                    Các danh mục sản phẩm sẽ sớm xuất hiện tại đây.
                                </p>
                            </div>
                        )}
                    </section>

                    {/* Flash Sale Section */}
                    <section className="mb-12 rounded-xl bg-card p-6 md:mb-16 md:p-8">
                        <div className="mb-6">
                            <h2 className="text-[22px] font-bold leading-tight tracking-[-0.015em] text-foreground md:text-2xl">
                                Flash Sale
                            </h2>
                        </div>
                        {hasFlashSale ? (
                        <>
                            <div className="mb-6 flex items-center gap-2 text-muted-foreground">
                                <span className="text-sm font-medium">
                                    Kết thúc trong:
                                </span>
                                <div className="flex items-center gap-1.5">
                                    <span className="flex h-10 w-10 items-center justify-center rounded-lg bg-primary text-lg font-bold text-white">
                                        08
                                    </span>
                                    <span className="font-bold text-primary">:</span>
                                    <span className="flex h-10 w-10 items-center justify-center rounded-lg bg-primary text-lg font-bold text-white">
                                        45
                                    </span>
                                    <span className="font-bold text-primary">:</span>
                                    <span className="flex h-10 w-10 items-center justify-center rounded-lg bg-primary text-lg font-bold text-white">
                                        22
                                    </span>
                                </div>
                            </div>
                            <div className="-mx-4 flex overflow-x-auto px-4 [-ms-scrollbar-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
                                <div className="flex items-stretch gap-4 pb-4">
                                    {flashSaleProducts.map((product) => (
                                        <div
                                            key={product.id}
                                            className="group flex min-w-[260px] w-[260px] flex-col overflow-hidden rounded-lg bg-background shadow-md"
                                        >
                                            <div
                                                className="relative flex aspect-square w-full flex-col bg-cover bg-center bg-no-repeat"
                                                style={{
                                                    backgroundImage: `url("${product.image}")`,
                                                }}
                                            >
                                                <span className="absolute left-3 top-3 rounded-full bg-primary px-2.5 py-1 text-xs font-bold text-white">
                                                    -{product.discount}%
                                                </span>
                                            </div>
                                            <div className="flex flex-1 flex-col gap-2 p-4">
                                                <p className="truncate text-base font-medium leading-normal text-foreground">
                                                    {product.name}
                                                </p>
                                                <div className="flex items-baseline gap-2">
                                                    <p className="text-lg font-bold text-primary">
                                                        {formatPrice(product.price)}
                                                    </p>
                                                    {product.oldPrice && (
                                                    <p className="text-sm font-normal text-muted-foreground line-through">
                                                        {formatPrice(product.oldPrice)}
                                                    </p>
                                                    )}
                                                </div>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        </>
                        ) : (
                            <div className="flex flex-col items-center justify-center text-center py-10 min-h-[250px]">
                                <div className="text-primary mb-4">
                                    <Timer className="h-14 w-14 opacity-70" />
                                </div>
                                <h3 className="text-xl font-semibold text-foreground mb-2">
                                    Chưa có Flash Sale nào đang diễn ra
                                </h3>
                                <p className="text-muted-foreground max-w-md mb-6">
                                    Hãy theo dõi trang của chúng tôi! Các ưu đãi chớp nhoáng sẽ sớm xuất hiện.
                                </p>
                                <button className="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-11 px-5 bg-primary text-white text-sm font-bold leading-normal tracking-[0.015em] hover:bg-primary/90 transition-colors">
                                    <span className="truncate">Xem tất cả ưu đãi</span>
                                </button>
                            </div>
                        )}
                    </section>

                    {/* Suggested Products Section */}
                    <section className="mb-12 md:mb-16">
                        <h2 className="pb-0 text-[22px] font-bold leading-tight tracking-[-0.015em] text-foreground md:text-2xl mb-5">
                            Gợi ý hôm nay
                        </h2>
                        {suggestedProducts.length > 0 ? (
                            <>
                                <div className="flex items-center justify-between mb-5">
                                    <div className="flex gap-2 ml-auto">
                                        <button
                                            aria-label="Previous suggested"
                                            onClick={() => scrollSuggested('left')}
                                            className="flex cursor-pointer items-center justify-center rounded-full h-10 w-10 bg-surface-light dark:bg-surface-dark text-foreground hover:bg-primary/10 hover:text-primary transition-colors"
                                        >
                                            <ChevronLeft className="h-5 w-5" />
                                        </button>
                                        <button
                                            aria-label="Next suggested"
                                            onClick={() => scrollSuggested('right')}
                                            className="flex cursor-pointer items-center justify-center rounded-full h-10 w-10 bg-surface-light dark:bg-surface-dark text-foreground hover:bg-primary/10 hover:text-primary transition-colors"
                                        >
                                            <ChevronRight className="h-5 w-5" />
                                        </button>
                                    </div>
                                </div>
                                <div className="-mx-2 px-2">
                                    <div
                                        ref={suggestedRef}
                                        className="flex items-stretch gap-6 pb-4 overflow-x-auto [-ms-scrollbar-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden"
                                    >
                                        {suggestedProducts.map((product) => (
                                            <Link
                                                key={product.id}
                                                href={`/product/${product.id}`}
                                                className="flex w-full sm:w-1/2 md:w-1/3 lg:w-[calc((100%-72px)/4)] min-w-0 flex-col rounded-lg border border-border bg-card overflow-hidden group"
                                                style={{ flexShrink: 0 }}
                                            >
                                        <div
                                            className="relative aspect-square w-full overflow-hidden bg-cover bg-center bg-no-repeat"
                                            style={{ backgroundImage: `url("${product.image}")` }}
                                        >
                                            <button onClick={(e) => e.preventDefault()} className="absolute right-3 top-3 flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-white/80 text-foreground backdrop-blur-sm transition-colors hover:text-primary dark:bg-card/80">
                                                <Heart className="h-5 w-5" />
                                            </button>
                                        </div>
                                        <div className="flex flex-col gap-4 p-4">
                                            <div>
                                                <p className="mb-1 text-base font-semibold leading-normal text-foreground">
                                                    {product.name}
                                                </p>
                                                <div className="flex items-center gap-1">
                                                    <div className="flex text-sm">
                                                        {renderStars(product.rating ?? 0)}
                                                    </div>
                                                    <span className="ml-1 text-xs text-muted-foreground">
                                                        ({product.reviews})
                                                    </span>
                                                </div>
                                            </div>
                                            <div className="flex items-center justify-between">
                                                <p className="text-lg font-bold text-foreground">
                                                    {formatPrice(product.price)}
                                                </p>
                                                <button className="flex h-10 w-10 cursor-pointer items-center justify-center rounded-lg bg-secondary/20 text-secondary transition-colors hover:bg-secondary hover:text-white" onClick={(e) => { e.preventDefault(); showSuccess('Đã thêm vào giỏ hàng!'); }}>
                                                    <ShoppingBag className="h-5 w-5" />
                                                </button>
                                            </div>
                                        </div>
                                    </Link>
                                        ))}
                                    </div>
                                </div>
                            </>
                        ) : (
                            <div className="flex flex-col items-center justify-center rounded-lg border border-dashed border-border bg-card p-8 min-h-[250px] text-center">
                                <div className="text-primary mb-3">
                                    <Lightbulb className="h-12 w-12 opacity-70" />
                                </div>
                                <h3 className="text-lg font-semibold text-foreground mb-1">
                                    Chưa có sản phẩm gợi ý
                                </h3>
                                <p className="text-sm text-muted-foreground max-w-sm mb-4">
                                    Hãy quay lại sau để khám phá các sản phẩm được tuyển chọn đặc biệt dành cho bạn.
                                </p>
                                <button className="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-4 bg-primary text-white text-sm font-bold leading-normal tracking-[0.015em] hover:bg-primary/90 transition-colors">
                                    <span className="truncate">Khám phá tất cả sản phẩm</span>
                                </button>
                            </div>
                        )}
                    </section>

                    {/* Best Sellers Section */}
                    <section className="mb-12 md:mb-16">
                        <div className="flex items-center justify-between mb-5">
                            <h2 className="text-[22px] font-bold leading-tight tracking-[-0.015em] text-foreground md:text-2xl">
                                Sản phẩm bán chạy
                            </h2>
                            {bestSellers.length > 0 && (
                                <div className="flex gap-2">
                                    <button
                                        aria-label="Previous best sellers"
                                        onClick={() => scrollBestSellers('left')}
                                        className="flex cursor-pointer items-center justify-center rounded-full h-10 w-10 bg-surface-light dark:bg-surface-dark text-foreground hover:bg-primary/10 hover:text-primary transition-colors"
                                    >
                                        <ChevronLeft className="h-5 w-5" />
                                    </button>
                                    <button
                                        aria-label="Next best sellers"
                                        onClick={() => scrollBestSellers('right')}
                                        className="flex cursor-pointer items-center justify-center rounded-full h-10 w-10 bg-surface-light dark:bg-surface-dark text-foreground hover:bg-primary/10 hover:text-primary transition-colors"
                                    >
                                        <ChevronRight className="h-5 w-5" />
                                    </button>
                                </div>
                            )}
                        </div>
                        {bestSellers.length > 0 ? (
                            <div className="-mx-2 px-2">
                                <div
                                    ref={bestSellerRef}
                                    className="flex items-stretch gap-6 pb-4 overflow-x-auto [-ms-scrollbar-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden"
                                >
                                    {bestSellers.map((product) => (
                                    <div
                                        key={product.id}
                                        className="flex w-full sm:w-1/2 md:w-1/3 lg:w-[calc((100%-72px)/4)] min-w-0 flex-col rounded-lg bg-background shadow-md overflow-hidden group"
                                        style={{
                                            // Ensure the items don't grow when container expands, so the 4-card layout remains stable on lg and above
                                            flexShrink: 0,
                                        }}
                                    >
                                        <div
                                            className="relative aspect-square w-full bg-cover bg-center bg-no-repeat"
                                            style={{ backgroundImage: `url("${product.image}")` }}
                                        >
                                            <button className="absolute top-3 right-3 flex h-8 w-8 items-center justify-center rounded-full bg-white/80 text-foreground backdrop-blur-sm transition-colors hover:text-primary dark:bg-card/80">
                                                <Heart className="h-5 w-5" />
                                            </button>
                                        </div>
                                        <div className="flex flex-1 flex-col gap-2 p-4">
                                            <p className="mb-1 text-base font-semibold leading-normal text-foreground truncate">
                                                {product.name}
                                            </p>
                                            <div className="flex items-center justify-between">
                                                <p className="text-lg font-bold text-foreground">{formatPrice(product.price)}</p>
                                                <button className="flex h-10 w-10 items-center justify-center rounded-lg bg-secondary/20 text-secondary hover:bg-secondary hover:text-white transition-colors" onClick={() => showSuccess('Đã thêm vào giỏ hàng!')}>
                                                    <ShoppingBag className="h-5 w-5" />
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    ))}
                                </div>
                            </div>
                        ) : (
                            <div className="flex flex-col items-center justify-center rounded-lg border border-dashed border-border bg-card p-8 min-h-[250px]">
                                <div className="text-secondary mb-3">
                                    <TrendingUp className="h-12 w-12 opacity-70" />
                                </div>
                                <h3 className="text-lg font-semibold text-foreground mb-1">
                                    Chưa có sản phẩm bán chạy
                                </h3>
                                <p className="text-sm text-muted-foreground">
                                    Khi có sản phẩm bán chạy, chúng sẽ được hiển thị ở đây.
                                </p>
                            </div>
                        )}
                    </section>
                </div>
            </main>

            {/* Footer */}
            <Footer />
            {/* Floating Chat Bubble */}
            <button
                aria-label="Open chat"
                aria-expanded={isChatOpen}
                aria-controls="chat-panel"
                onClick={() => setIsChatOpen((s) => !s)}
                className="fixed bottom-6 right-6 z-50 flex h-16 w-16 cursor-pointer items-center justify-center rounded-full bg-secondary text-white shadow-lg transition-transform hover:scale-110"
            >
                <MessageCircle className="h-7 w-7" />
            </button>

            {/* Chat panel, toggled by button */}
            {isChatOpen && (
                <>
                    {/* Minimized bar */}
                    {isChatMinimized ? (
                        <div className="fixed bottom-28 right-6 z-50 w-72 rounded-lg bg-card shadow-md flex items-center justify-between px-3 py-2">
                            <div className="flex items-center gap-3">
                                <div className="text-primary">
                                    <ShoppingBag className="h-5 w-5" />
                                </div>
                                <div className="text-sm font-semibold truncate">ShopNest Assistant</div>
                            </div>
                            <div className="flex gap-2">
                                <button
                                    aria-label="Restore" className="flex h-8 w-8 items-center justify-center rounded-lg text-muted-foreground hover:bg-surface hover:text-foreground"
                                    onClick={() => setIsChatMinimized(false)}
                                >
                                    <ChevronRight className="h-4 w-4" />
                                </button>
                                <button
                                    aria-label="Close" className="flex h-8 w-8 items-center justify-center rounded-lg text-muted-foreground hover:bg-surface hover:text-foreground"
                                    onClick={() => { setIsChatOpen(false); setIsChatMinimized(false); }}
                                >
                                    <X className="h-4 w-4" />
                                </button>
                            </div>
                        </div>
                    ) : (
                        <div
                            id="chat-panel"
                            role="dialog"
                            aria-label="Chat with ShopNest"
                            className="fixed bottom-28 right-6 z-50 w-96 max-w-[calc(100%-1rem)] rounded-xl shadow-2xl flex flex-col h-[600px] max-h-[calc(100vh-3rem)] bg-card">
                            <header className="flex items-center justify-between p-4 border-b border-border">
                                <div className="flex items-center gap-3">
                                    <div className="text-primary">
                                        <ShoppingBag className="h-6 w-6" />
                                    </div>
                                    <div>
                                        <div className="text-sm font-semibold">ShopNest Assistant</div>
                                        <div className="flex items-center gap-1.5 text-xs text-muted-foreground">
                                            <span className="w-2 h-2 rounded-full bg-green-500 inline-block" />
                                            <span>Online</span>
                                        </div>
                                    </div>
                                </div>
                                <div className="flex gap-1">
                                    <button
                                        aria-label="Minimize chat"
                                        onClick={() => setIsChatMinimized(true)}
                                        className="flex cursor-pointer items-center justify-center overflow-hidden rounded-lg h-8 w-8 text-muted-foreground hover:bg-surface hover:text-foreground transition-colors"
                                    >
                                        <Minus className="h-4 w-4" />
                                    </button>
                                    <button
                                        aria-label="Close chat"
                                        onClick={() => { setIsChatOpen(false); setIsChatMinimized(false); }}
                                        className="flex cursor-pointer items-center justify-center overflow-hidden rounded-lg h-8 w-8 text-muted-foreground hover:bg-surface hover:text-foreground transition-colors"
                                    >
                                        <X className="h-4 w-4" />
                                    </button>
                                </div>
                            </header>
                            <main className="flex-1 overflow-y-auto p-4 bg-muted/50 dark:bg-background-dark">
                                <div className="flex flex-col gap-4">
                                    {messages.map((m) => (
                                        <div key={m.id} className={`flex items-start gap-3 ${m.from === 'user' ? 'flex-row-reverse' : ''}`}>
                                            <div className={`w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 ${m.from === 'bot' ? 'bg-secondary text-white' : ''}`}>
                                                {m.from === 'bot' ? <MessageCircle className="h-4 w-4 text-white" /> : null}
                                            </div>
                                            <div className={`flex flex-col gap-1 ${m.from === 'user' ? 'items-end' : ''}`}>
                                                <div className={`rounded-lg p-3 max-w-xs ${m.from === 'bot' ? 'bg-background' : 'bg-primary text-white'}`}>
                                                    <p className={`text-sm ${m.from === 'bot' ? 'text-foreground' : ''}`}>{m.text}</p>
                                                </div>
                                                <span className="text-xs text-muted-foreground">{m.time ?? ''}</span>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </main>
                            <footer className="p-3 border-t border-border">
                                <div className="flex items-center gap-2">
                                    <input
                                        className="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg text-sm focus:outline-0 focus:ring-2 focus:ring-primary/50 border-none bg-surface px-4 py-2 placeholder:text-muted-foreground"
                                        placeholder="Nhập tin nhắn..."
                                        value={chatInput}
                                        onChange={(e) => setChatInput(e.target.value)}
                                        onKeyDown={(e) => {
                                            if (e.key === 'Enter') {
                                                e.preventDefault();
                                                sendMessage();
                                            }
                                        }}
                                    />
                                    <button
                                        className="flex h-11 w-11 cursor-pointer items-center justify-center rounded-lg bg-primary text-white shadow-sm transition-colors hover:bg-primary/90 flex-shrink-0"
                                        onClick={() => sendMessage()}
                                    >
                                        Gửi
                                    </button>
                                </div>
                            </footer>
                        </div>
                    )}
                </>
            )}
        </div>
    );
}

export default function Welcome() {
    return (
        <ToastProvider>
            <WelcomeContent />
        </ToastProvider>
    );
}
