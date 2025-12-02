import { ShoppingBag, Heart, Search, ChevronLeft, ChevronRight, MessageCircle, X, Minus } from 'lucide-react';
import { Link } from '@inertiajs/react';
import { useEffect, useRef, useState } from 'react';
import { ToastProvider, useToast } from '../lib/toastContext';
import { SkeletonPage } from '../components/skeletons';
import { usePageLoading } from '../hooks/usePageLoading';

function WelcomeContent() {
    const isLoading = usePageLoading({ delay: 300, minLoadingTime: 1500 });
    const { showSuccess, showError, showInfo } = useToast();
    
    const heroImages = [
        "https://lh3.googleusercontent.com/aida-public/AB6AXuCJnd0a7KUIVBOrCCbTD_XI8zjrdFzSTEoaq6b4K3q-WNNZ7Q2Bkcw0YRwFrtyBeGOTDM5kMZeV_Dsx87yglrclO67vmbXOLiWdKSRNsRDazp8Z9RBcwMv21ccDiy7Ejn2xRN_TGm8jLkPPVhgUwBUR7T-IYai9T9Zyr0elbxlqZXV_Nd24e1s71UbqVJ4c0tevgmsrAc6DsnRS8Fxgk8XcjjO2uEtvshEWzXm5XhzWDHs0QAirR_c2Q2e-Q-QD-evUeWdCr7mUVtc",
        'https://lh3.googleusercontent.com/aida-public/AB6AXuBZ0SW5aKwDpK6K29I0-SDUGeyHd5U9d-oTB6nVby7PUkMp_xF33sURbT7peE7FmT8Zt7YVcOcEjwycU69a5yK1xnsm8F53Zg4XzdbW9uaOVrxIm8abstmpf0uSmb5fXmxzQrK3sicYxX_htS0-n7kj4AYKSb0gF4UDqHIKfPb2Oe1RF7IRm6qpoxckB_pwQzjRtqj9I6Z-rG6kH1kKcTD7BM-PyBW2cDgwUtbCZ57daKO8Ps11liX3yrCIM33KdCjb1EU8vWhh_Tw',
        'https://lh3.googleusercontent.com/aida-public/AB6AXuDhVzAsMXFeYJb0HP5k8OnmdwT2iGOTIN4CFswYpf6qb2wbzLbIo1quOJRd5j0RbiDls3gBRx2MapPg4c3M4iNkeU02ZNhxSshm4qJBK3WX3FVt1pbkc2CFr2GjigvzaXYQhxtCRptoOSnPgKmr4qiNKvgt3VdQkRth7LcpFeID2wd53twtEgslNxRpZeai5Or15GLacvyGv-vuAaWagDaMUDXoIpLom4Pz0xHUhvHWrwS3p5pYsZjkn-6cTC_AI5hfd3enk7SK21M',
    ];

    const [activeSlide, setActiveSlide] = useState(0);
    const [isPaused, setIsPaused] = useState(false);
    const autoplayRef = useRef<number | null>(null);
    const categories = [
        { icon: '💻', name: 'Điện tử', key: 'electronics' },
        { icon: '👔', name: 'Thời trang', key: 'fashion' },
        { icon: '🌿', name: 'Nhà & Vườn', key: 'home' },
        { icon: '✨', name: 'Sắc đẹp', key: 'beauty' },
        { icon: '⚽', name: 'Thể thao', key: 'sports' },
        { icon: '🧸', name: 'Đồ chơi', key: 'toys' },
    ];

    const flashSaleProducts = [
        {
            id: 1,
            name: 'Tai nghe không dây chống ồn',
            price: 1250000,
            oldPrice: 1790000,
            discount: 30,
            image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuBZ0SW5aKwDpK6K29I0-SDUGeyHd5U9d-oTB6nVby7PUkMp_xF33sURbT7peE7FmT8Zt7YVcOcEjwycU69a5yK1xnsm8F53Zg4XzdbW9uaOVrxIm8abstmpf0uSmb5fXmxzQrK3sicYxX_htS0-n7kj4AYKSb0gF4UDqHIKfPb2Oe1RF7IRm6qpoxckB_pwQzjRtqj9I6Z-rG6kH1kKcTD7BM-PyBW2cDgwUtbCZ57daKO8Ps11liX3yrCIM33KdCjb1EU8vWhh_Tw',
        },
        {
            id: 2,
            name: 'Đồng hồ thông minh Pro X',
            price: 2490000,
            oldPrice: 4990000,
            discount: 50,
            image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuDhVzAsMXFeYJb0HP5k8OnmdwT2iGOTIN4CFswYpf6qb2wbzLbIo1quOJRd5j0RbiDls3gBRx2MapPg4c3M4iNkeU02ZNhxSshm4qJBK3WX3FVt1pbkc2CFr2GjigvzaXYQhxtCRptoOSnPgKmr4qiNKvgt3VdQkRth7LcpFeID2wd53twtEgslNxRpZeai5Or15GLacvyGv-vuAaWagDaMUDXoIpLom4Pz0xHUhvHWrwS3p5pYsZjkn-6cTC_AI5hfd3enk7SK21M',
        },
        {
            id: 3,
            name: 'Giày chạy bộ UltraBoost',
            price: 950000,
            oldPrice: 1200000,
            discount: 25,
            image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuDSqiGwtcxQ1Y3jFnW6oEyGugSS6aVcgArNNtrmu7dMMsRK-kVF5DTEuywLg29Rvm_y5dmesgNuSuRR4ye_U87iTo5GafL9vNyhcrewOGAM_8kJk4tB6OR1BLJMmcbg9FXL1kjhNfhI3-3NWTfY_DGcPSnHQPgkBPTYUtDffiDuew-JxjN9blOdCzOrSBbMGlXl9hvywQEf2kUkMmbcmEt4H_jYZNDyBG3BCkJeAOZgH2qLIH1b_BL0qwlLxlRid6aj4u5hhlpSC80',
        },
        {
            id: 4,
            name: 'Tay cầm chơi game thế hệ mới',
            price: 890000,
            oldPrice: 1490000,
            discount: 40,
            image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuA8ALdYt5REDJVcApU3zRrY0RTaG8rwMnjt4huLTL7SynRtn_vxf7zul4WBjcUC5GvUChjamdxwOtmTK9Gpyv0r6emm6uukJCcHiduds8wV9Y5yyOauv6ieEDOrcK_AWBXDx3hz_L7QojEhbAH4ORZa_HXzV_mrBxJHE_Of9Zo07DlPus-F18ooLRBGyRCFQJwe31E8pKF7AS0wXVHuw2t8epL-7tbydh0xmIjjY-OBwxs-iQwbK88AvyEUc4aYhj6cCKdkVcxtGZY',
        },
    ];

    const suggestedProducts = [
        {
            id: 1,
            name: 'Ghế công thái học ErgoFlex',
            price: 3200000,
            rating: 4.5,
            reviews: 124,
            image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuA_3Hg8MXQwtiERSX43afboTM1H1-41w4KBQxetVoKydoputfI3u-8Vr6lMZPGmmgK3Vj2a8FocftUvTeNfDtyuDk6vulQgy2XQ6sJD-C6NSeXYh2CYNUSAGjsHDVlALoSmMzJp2YqQhCYDHCKO7d1_eS61l4Ddw8y6y5pss7Yg3BsRbqUordq7yP-W65WHG38K4MqowehadHRhDBne6loSh9agztSqMoxAn5DQ2Ydqg9M5b4sBAP2tnRwZIE3rmUdiQ7c_TkDRAB4',
        },
        {
            id: 2,
            name: 'Balo du lịch chống nước',
            price: 850000,
            rating: 5,
            reviews: 305,
            image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuAPisgy3KZaPmgOSMmh9sqM49-D8D7yiH8T_Kt_mJWPnXtCcQLjHDsov7tlNdKZR1GkS87Lfefou5CCJFEmBS2owo8stPVx_XXkHyQjdC0IY7mJQ9TJysBiirwxJ65XeIz_lkMcPuRKURv-EwbLHd5yvpWx2NyexTj4fHwtkP2BzKsdHfL6Nq-JhcrlYJstUiODv9638JXtmN7k7NoqwpuQcf4q0YIf56ecOwMDSZV9LcXGX12VZAfPrWPMND7nXDjBM7E-D1DQCk4',
        },
        {
            id: 3,
            name: 'Bộ cốc sứ tối giản (4 cái)',
            price: 420000,
            rating: 4,
            reviews: 98,
            image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuB0Dx4PFCAlmfHbtZ9gWiy5A4YWIhbVWT0zGZ_PTYfWgZ_bDm0UyQS0MKko39pu3odEXa9wb2Tkh2JEqu66uUHOKkPUkkJjG_Rce83f9jWwegBC95wzkgFJPnD3iDmEJlfrRpsLvwQR9L4pDCjE5F0ZEsp1DoT6BOe1ZSxT28oR_RA-0e5c4eU9YN8NsGdQZyrwwPhi9KbR54oJu66zfhOGeBmUhAM1kUfkeL8IK45jkLlGwYjeoi65L8y3qNl4Zy-oo2064pw3KKo',
        },
        {
            id: 4,
            name: 'Loa Bluetooth di động BassMax',
            price: 1100000,
            rating: 4.5,
            reviews: 211,
            image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuALOGGvwadBs8vhjIFBZyG4TjM7DyT6Wzqn7eGOl5dssX7O0GeYHMprL5MYWxiyeY843X4LnczV7YowlUoEDA7uozJO5NiMMLnQk1SX6EbqpUlUCcbOqxRh8034s3AzsdPhIur7N4JCSMaxXm1vqiUeDKSQBeeTdMWBO9n7-UVkhFKfV7Xpp7Gjbp_pRXuKJvbliG5BxKFDNRUAT5dPILqfPj7_51E33Zvnf_Vn4RYjQ1kr9EQuVHmSTfd9aERR6yqXoyt_O8lH3b0',
        },
    ];

    const bestSellers = [
        {
            id: 1,
            name: 'Máy ảnh chuyên nghiệp ProShot',
            price: 11500000,
            image:
                'https://lh3.googleusercontent.com/aida-public/AB6AXuDkOT3NXftcHBYXJWdTJQoBIeEU5MFdARt6E2VA-vX8-RzpULjYGjqM5vkZD7728lKkHbXLaJYkjMeRC_HA1Yrpy7-E57-n3zCp0lDvZrGES07xg0ZDnfko8k96BKQIIDrhoTAYK1-HCMTEgBRB4S4PQ8yOEyVwKsL6oy55qXQpW6FOA2Y-7-6bLUBgVIJe74FNMvpiIdk0OqRTbwSsrEQd6002owa3TH0dYP3nE-SfbXorxPdHht1mAL2UDU_7Whrzyvv2L3L_tlI',
        },
        {
            id: 2,
            name: 'Máy pha cà phê tự động',
            price: 2490000,
            image:
                'https://lh3.googleusercontent.com/aida-public/AB6AXuD0ywpht2YQSrweNwuUan-skozVSnnD2O767vO8LEwBf_nTGJzsQEX_pSbnOrbhjrZAcJX_lUE3wF9E826v6KbxgW7GdgxmLS2oIJGuTWpUsmVkmGOlynzkZkZ5LZcxWM3Us5Usrx-hagsCKRZdzZ22mbpdXUzxzPknF8H4YKwgwtza1tKGqnBHXkJ6m6CLwjRd-L0gus_grmfHEwU8oVVPRGVWCpyyJUUUk5KkxMjBOZ-0WZ-JvdQMAgX-9TcdeqQv52T3ZCFQdw0',
        },
        {
            id: 3,
            name: 'Ví da cao cấp',
            price: 799000,
            image:
                'https://lh3.googleusercontent.com/aida-public/AB6AXuCWvt3xvZ6SVWDoiGKuZg5xOu8LLcWsLCxXLuaCdSXwxx9XBmDBdxMCo3_VIcbhH02H8V7WJnW_eOsaBX9q7885Jh5rdZ9RvrJWAF4962fTYuZ95-46bEdiEpVjvOXNV8YyklcYogdJy8tBhIkei19xOy5KCiLGy5As-dsJWifcv1hj9lJY7hBpTcX_SOmQl7ytPTVdJYxhQw5uAHPRe6t4WE9Z50LKXhTKjIYTaEqn_qWXQac1AARTTdRnPOJmtIiWTPp2J9iJmC8',
        },
        {
            id: 4,
            name: 'Drone quay phim 4K',
            price: 5390000,
            image:
                'https://lh3.googleusercontent.com/aida-public/AB6AXuA-wf6SYi2PJda5x7DCS37sG9frqxOqBLr86mgQgbbgelI-vd4rYPZv4ZaCXVEIzw4Buw8fPCcDi2K2Rx0L5aiX7VCs3aPVdtHls7nSKLph6OUJ6yrR5mrySESTknczklTPlvA86cozIK2ukJo48mjicwlqZUb98REoYxdv_VZNlF2KcbJ-TcajXFBsIda_TUf3teCFSxvgSzQdMmq5U6MEUL5kBqIuMDRwRNfFqfMmwzy_3_daSJf0M-pZ1aJHLDVsFMzZ4F43XcE',
        },
    ];

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
            <header className="sticky top-0 z-50 w-full border-b border-border bg-background/80 backdrop-blur-sm">
                <div className="container mx-auto px-4">
                    <div className="flex h-20 items-center justify-between whitespace-nowrap">
                        <div className="flex items-center gap-8">
                            <div className="flex items-center gap-2">
                                <div className="text-primary">
                                    <ShoppingBag className="h-10 w-10" />
                                </div>
                                <h1 className="text-2xl font-bold leading-tight tracking-[-0.015em]">
                                    ShopNest
                                </h1>
                            </div>
                            <nav className="hidden items-center gap-9 md:flex">
                                <Link
                                    href="#"
                                    className="text-sm font-medium leading-normal text-foreground transition-colors hover:text-primary"
                                >
                                    Trang chủ
                                </Link>
                                <Link
                                    href="#"
                                    className="text-sm font-medium leading-normal text-muted-foreground transition-colors hover:text-primary"
                                >
                                    Cửa hàng
                                </Link>
                                <Link
                                    href="#"
                                    className="text-sm font-medium leading-normal text-muted-foreground transition-colors hover:text-primary"
                                >
                                    Ưu đãi
                                </Link>
                                <Link
                                    href="#"
                                    className="text-sm font-medium leading-normal text-muted-foreground transition-colors hover:text-primary"
                                >
                                    Liên hệ
                                </Link>
                            </nav>
                        </div>
                        <div className="flex flex-1 items-center justify-end gap-4">
                            <label className="hidden lg:flex flex-col min-w-40 !h-10 max-w-64">
                                <div className="flex h-full w-full items-stretch rounded-lg">
                                    <div className="flex items-center justify-center rounded-l-lg bg-card pl-3 text-muted-foreground h-full">
                                        <Search className="h-5 w-5" />
                                    </div>
                                    <input
                                        className="form-input flex w-full min-w-0 flex-1 h-full rounded-lg rounded-l-none border-none bg-card px-4 text-sm font-normal leading-normal text-foreground placeholder:text-muted-foreground focus:outline-0 focus:ring-2 focus:ring-primary/50"
                                        placeholder="Tìm kiếm sản phẩm..."
                                    />
                                </div>
                            </label>
                            <div className="flex gap-2">
                                <button className="flex h-10 w-10 cursor-pointer items-center justify-center overflow-hidden rounded-lg bg-card text-foreground transition-colors hover:bg-primary/10 hover:text-primary" onClick={() => showError('Vui lòng đăng nhập để thêm sản phẩm yêu thích') }>
                                    <Heart className="h-5 w-5" />
                                </button>
                                <button className="flex h-10 w-10 cursor-pointer items-center justify-center overflow-hidden rounded-lg bg-card text-foreground transition-colors hover:bg-primary/10 hover:text-primary">
                                    <ShoppingBag className="h-5 w-5" />
                                </button>
                            </div>
                            <Link
                                href="#"
                                className="flex h-10 items-center justify-center rounded-lg bg-card px-4 text-sm font-medium text-foreground transition-colors hover:bg-primary/10 hover:text-primary"
                                onClick={(e) => { e.preventDefault(); showInfo('Vui lòng đăng nhập để truy cập tài khoản'); }}
                            >
                                Tài khoản của tôi
                            </Link>
                        </div>
                    </div>
                </div>
            </header>

            {/* Main Content */}
            <main className="flex-1">
                <div className="container mx-auto px-4 py-8 md:py-12">
                    {/* Hero Section */}
                    <section className="mb-12 md:mb-16">
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
                    </section>

                    {/* Categories Section */}
                    <section className="mb-12 md:mb-16">
                        <div className="flex items-center justify-between mb-5">
                            <h2 className="pb-0 text-[22px] font-bold leading-tight tracking-[-0.015em] text-foreground md:text-2xl">Mua sắm theo Danh mục</h2>
                            <div className="flex gap-2">
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
                                        key={category.key}
                                        className="flex w-40 sm:w-44 md:w-48 lg:w-[calc((100%-96px)/7)] min-w-0 aspect-square cursor-pointer flex-col items-center justify-center gap-3 rounded-lg border border-border bg-card p-4 transition-all hover:-translate-y-1 hover:shadow-lg snap-start"
                                        style={{ flexShrink: 0 }}
                                    >
                                        <div className="text-4xl text-secondary">{category.icon}</div>
                                        <h3 className="text-center text-base font-bold leading-tight text-foreground">{category.name}</h3>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </section>

                    {/* Flash Sale Section */}
                    <section className="mb-12 rounded-xl bg-card p-6 md:mb-16 md:p-8">
                        <div className="mb-6 flex flex-col items-start justify-between gap-4 md:flex-row md:items-center">
                            <h2 className="text-[22px] font-bold leading-tight tracking-[-0.015em] text-foreground md:text-2xl">
                                Flash Sale
                            </h2>
                            <div className="flex items-center gap-2 text-muted-foreground">
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
                                                <p className="text-sm font-normal text-muted-foreground line-through">
                                                    {formatPrice(product.oldPrice)}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </section>

                    {/* Suggested Products Section */}
                    <section className="mb-12 md:mb-16">
                        <div className="flex items-center justify-between mb-5">
                            <h2 className="pb-0 text-[22px] font-bold leading-tight tracking-[-0.015em] text-foreground md:text-2xl">
                                Gợi ý hôm nay
                            </h2>
                            <div className="flex gap-2">
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
                                    <div
                                        key={product.id}
                                        className="flex w-full sm:w-1/2 md:w-1/3 lg:w-[calc((100%-72px)/4)] min-w-0 flex-col rounded-lg border border-border bg-card overflow-hidden group"
                                        style={{ flexShrink: 0 }}
                                    >
                                        <div
                                            className="relative aspect-square w-full overflow-hidden bg-cover bg-center bg-no-repeat"
                                            style={{ backgroundImage: `url("${product.image}")` }}
                                        >
                                            <button className="absolute right-3 top-3 flex h-8 w-8 cursor-pointer items-center justify-center rounded-full bg-white/80 text-foreground backdrop-blur-sm transition-colors hover:text-primary dark:bg-card/80">
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
                                                        {renderStars(product.rating)}
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
                                                <button className="flex h-10 w-10 cursor-pointer items-center justify-center rounded-lg bg-secondary/20 text-secondary transition-colors hover:bg-secondary hover:text-white" onClick={() => showSuccess('Đã thêm vào giỏ hàng!')}>
                                                    <ShoppingBag className="h-5 w-5" />
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </section>

                    {/* Best Sellers Section */}
                    <section className="mb-12 md:mb-16">
                        <div className="flex items-center justify-between mb-5">
                            <h2 className="text-[22px] font-bold leading-tight tracking-[-0.015em] text-foreground md:text-2xl">
                                Sản phẩm bán chạy
                            </h2>
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
                        </div>
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
                    </section>
                </div>
            </main>

            {/* Footer */}
            <footer className="bg-card">
                <div className="container mx-auto px-4 py-12">
                    <div className="grid grid-cols-1 gap-8 md:grid-cols-4">
                        <div className="col-span-1 md:col-span-2 lg:col-span-1">
                            <div className="mb-4 flex items-center gap-2 text-foreground">
                                <div className="text-primary">
                                    <ShoppingBag className="h-8 w-8" />
                                </div>
                                <h1 className="text-xl font-bold">ShopNest</h1>
                            </div>
                            <p className="text-sm text-muted-foreground">
                                Điểm đến mua sắm trực tuyến của bạn cho mọi thứ bạn
                                cần.
                            </p>
                        </div>
                        <div>
                            <h4 className="mb-4 font-bold text-foreground">
                                Về chúng tôi
                            </h4>
                            <ul className="space-y-2">
                                <li>
                                    <Link
                                        href="#"
                                        className="text-sm text-muted-foreground hover:text-primary"
                                    >
                                        Câu chuyện
                                    </Link>
                                </li>
                                <li>
                                    <Link
                                        href="#"
                                        className="text-sm text-muted-foreground hover:text-primary"
                                    >
                                        Tuyển dụng
                                    </Link>
                                </li>
                                <li>
                                    <Link
                                        href="#"
                                        className="text-sm text-muted-foreground hover:text-primary"
                                    >
                                        Báo chí
                                    </Link>
                                </li>
                            </ul>
                        </div>
                        <div>
                            <h4 className="mb-4 font-bold text-foreground">
                                Chăm sóc khách hàng
                            </h4>
                            <ul className="space-y-2">
                                <li>
                                    <Link
                                        href="#"
                                        className="text-sm text-muted-foreground hover:text-primary"
                                    >
                                        Liên hệ
                                    </Link>
                                </li>
                                <li>
                                    <Link
                                        href="#"
                                        className="text-sm text-muted-foreground hover:text-primary"
                                    >
                                        Câu hỏi thường gặp
                                    </Link>
                                </li>
                                <li>
                                    <Link
                                        href="#"
                                        className="text-sm text-muted-foreground hover:text-primary"
                                    >
                                        Chính sách đổi trả
                                    </Link>
                                </li>
                            </ul>
                        </div>
                        <div>
                            <h4 className="mb-4 font-bold text-foreground">
                                Chính sách
                            </h4>
                            <ul className="space-y-2">
                                <li>
                                    <Link
                                        href="#"
                                        className="text-sm text-muted-foreground hover:text-primary"
                                    >
                                        Điều khoản dịch vụ
                                    </Link>
                                </li>
                                <li>
                                    <Link
                                        href="#"
                                        className="text-sm text-muted-foreground hover:text-primary"
                                    >
                                        Chính sách bảo mật
                                    </Link>
                                </li>
                                <li>
                                    <Link
                                        href="#"
                                        className="text-sm text-muted-foreground hover:text-primary"
                                    >
                                        Chính sách Cookie
                                    </Link>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div className="mt-12 flex flex-col items-center justify-between border-t border-border pt-8 text-sm text-muted-foreground md:flex-row">
                        <p>© 2024 ShopNest. Đã đăng ký bản quyền.</p>
                        <div className="mt-4 flex space-x-4 md:mt-0">
                            <Link href="#" className="hover:text-primary">
                                FB
                            </Link>
                            <Link href="#" className="hover:text-primary">
                                IG
                            </Link>
                            <Link href="#" className="hover:text-primary">
                                TW
                            </Link>
                        </div>
                    </div>
                </div>
            </footer>
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
