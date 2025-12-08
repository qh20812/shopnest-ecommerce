import { ShoppingBag, Heart, Search, User, Package, Home, CreditCard, LogOut, Sun, Moon } from 'lucide-react';
import Input from './ui/input';
import { Link, usePage } from '@inertiajs/react';
import { useToast } from '../lib/toastContext';
import { useState, useRef, useEffect } from 'react';

type PageProps = {
    auth?: {
        user?: {
            id?: number;
            email?: string;
            full_name?: string;
            avatar_url?: string;
            avatar?: string;
        } | null;
    } | null;
    [key: string]: unknown;
};

export default function TopNav() {
    const { showError } = useToast();
    const page = usePage<PageProps>();
    const authUser = page.props?.auth?.user ?? null;
    const [isAvatarHover, setIsAvatarHover] = useState(false);
    const [isDropdownHover, setIsDropdownHover] = useState(false);
    const [isDropdownClick, setIsDropdownClick] = useState(false);
    const dropdownRef = useRef<HTMLDivElement | null>(null);

    const isDropdownOpen = isAvatarHover || isDropdownHover || isDropdownClick;

    // Get initial dark mode state
    const getInitialDarkMode = () => {
        if (typeof window === 'undefined') return false;
        const stored = localStorage.getItem('theme');
        const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        return stored === 'dark' || (!stored && systemPrefersDark);
    };

    const [isDarkMode, setIsDarkMode] = useState(getInitialDarkMode);

    // Apply dark mode class on mount and when it changes
    useEffect(() => {
        if (isDarkMode) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    }, [isDarkMode]);

    // Toggle dark mode
    const toggleDarkMode = () => {
        const newMode = !isDarkMode;
        setIsDarkMode(newMode);
        localStorage.setItem('theme', newMode ? 'dark' : 'light');
    };

    // Close dropdown when clicking outside or pressing Escape
    useEffect(() => {
        function onDocumentClick(e: MouseEvent) {
            if (!dropdownRef.current) return;
            if (!isDropdownOpen) return;
            // If click is outside dropdownRef, close it
            if (e.target instanceof Node && !dropdownRef.current.contains(e.target)) {
                setIsDropdownClick(false);
                setIsAvatarHover(false);
                setIsDropdownHover(false);
            }
        }

        function onKeyDown(e: KeyboardEvent) {
            if (e.key === 'Escape') {
                setIsDropdownClick(false);
                setIsAvatarHover(false);
                setIsDropdownHover(false);
            }
        }

        document.addEventListener('mousedown', onDocumentClick);
        document.addEventListener('keydown', onKeyDown);
        return () => {
            document.removeEventListener('mousedown', onDocumentClick);
            document.removeEventListener('keydown', onKeyDown);
        };
    }, [isDropdownOpen]);

    return (
        <>
            <header className="fixed inset-x-0 top-0 z-50 w-full border-b border-border bg-background/80 backdrop-blur-sm">
                <div className="container mx-auto px-4">
                    <div className="flex h-20 items-center justify-between whitespace-nowrap">
                        <div className="flex items-center gap-8">
                            <div className="flex items-center gap-2">
                                <img src="/ShopNest2.png" alt="ShopNest Logo" className='h-10 w-10' />
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
                                    Sản phẩm
                                </Link>
                                <Link
                                    href="#"
                                    className="text-sm font-medium leading-normal text-muted-foreground transition-colors hover:text-primary"
                                >
                                    Ưu đãi
                                </Link>
                            </nav>
                        </div>
                        <div className="flex flex-1 items-center justify-end gap-4">
                            <label className="hidden lg:flex flex-col min-w-40 !h-10 max-w-64">
                                <div className="flex h-full w-full items-stretch rounded-lg">
                                    <div className="flex items-center justify-center rounded-l-lg bg-card pl-3 text-muted-foreground h-full">
                                        <Search className="h-5 w-5" />
                                    </div>
                                    <Input
                                        className="h-full rounded-l-none border-none bg-card px-4 text-sm font-normal leading-normal text-foreground placeholder:text-muted-foreground focus:outline-0 focus:ring-2 focus:ring-primary/50"
                                        placeholder="Tìm kiếm sản phẩm..."
                                    />
                                </div>
                            </label>
                            <div className="flex gap-2">
                                <button
                                    className="flex h-10 w-10 cursor-pointer items-center justify-center overflow-hidden rounded-lg bg-card text-foreground transition-colors hover:bg-primary/10 hover:text-primary"
                                    onClick={() => showError('Vui lòng đăng nhập để thêm sản phẩm yêu thích')}
                                >
                                    <Heart className="h-5 w-5" />
                                </button>
                                <button className="flex h-10 w-10 cursor-pointer items-center justify-center overflow-hidden rounded-lg bg-card text-foreground transition-colors hover:bg-primary/10 hover:text-primary">
                                    <ShoppingBag className="h-5 w-5" />
                                </button>
                                <button
                                    className="flex h-10 w-10 cursor-pointer items-center justify-center overflow-hidden rounded-lg bg-card text-foreground transition-colors hover:bg-primary/10 hover:text-primary"
                                    onClick={toggleDarkMode}
                                    aria-label={isDarkMode ? 'Switch to light mode' : 'Switch to dark mode'}
                                >
                                    {isDarkMode ? (
                                        <Sun className="h-5 w-5" />
                                    ) : (
                                        <Moon className="h-5 w-5" />
                                    )}
                                </button>
                            </div>

                            {/* User Avatar Dropdown */}
                            {!authUser ? (
                                <Link
                                    href="/login"
                                    className="hidden md:inline-flex items-center rounded-lg border border-border bg-card px-4 py-2 text-sm font-medium text-foreground hover:bg-primary/10 hover:text-primary"
                                >
                                    Đăng nhập
                                </Link>
                            ) : (
                            <div
                                className="relative group"
                                onMouseEnter={() => setIsAvatarHover(true)}
                                onMouseLeave={() => setIsAvatarHover(false)}
                                ref={dropdownRef}
                            >
                                <button
                                    className="h-10 w-10 cursor-pointer rounded-full bg-cover bg-center"
                                    style={{
                                        backgroundImage:
                                            `url("${authUser.avatar_url ?? authUser.avatar ?? '/default-avatar.png'}")`,
                                    }}
                                    onClick={() => setIsDropdownClick((v) => !v)}
                                />
                                <div
                                    className={`absolute right-0 top-full z-10 mt-2 w-64 rounded-xl border border-border bg-background p-2 shadow-lg transition-opacity duration-200 ease-in-out ${isDropdownOpen
                                            ? 'pointer-events-auto opacity-100'
                                            : 'pointer-events-none opacity-0'
                                        }`}
                                    onMouseEnter={() => setIsDropdownHover(true)}
                                    onMouseLeave={() => setIsDropdownHover(false)}
                                >
                                    {/* User Info */}
                                    <div className="mb-2 flex items-center gap-3 p-2">
                                        <div
                                            className="h-12 w-12 rounded-full bg-cover bg-center"
                                            style={{
                                                backgroundImage:
                                                    `url("${authUser.avatar_url ?? authUser.avatar ?? '/default-avatar.png'}")`,
                                            }}
                                        />
                                        <div>
                                            <p className="font-bold text-foreground">{authUser.full_name ?? authUser.email ?? 'Người dùng'}</p>
                                            <p className="text-sm text-muted-foreground">{authUser.email ?? ''}</p>
                                        </div>
                                    </div>

                                    {/* Menu Items */}
                                    <Link
                                        href="/account"
                                        className="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-foreground transition-colors hover:bg-card"
                                    >
                                        <User className="h-5 w-5 text-muted-foreground" />
                                        <span>Thông tin cá nhân</span>
                                    </Link>
                                    <Link
                                        href="#"
                                        className="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-foreground transition-colors hover:bg-card"
                                    >
                                        <Package className="h-5 w-5 text-muted-foreground" />
                                        <span>Đơn hàng của tôi</span>
                                    </Link>
                                    <Link
                                        href="#"
                                        className="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-foreground transition-colors hover:bg-card"
                                    >
                                        <Heart className="h-5 w-5 text-muted-foreground" />
                                        <span>Danh sách yêu thích</span>
                                    </Link>
                                    <Link
                                        href="#"
                                        className="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-foreground transition-colors hover:bg-card"
                                    >
                                        <Home className="h-5 w-5 text-muted-foreground" />
                                        <span>Địa chỉ giao hàng</span>
                                    </Link>
                                    <Link
                                        href="#"
                                        className="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-foreground transition-colors hover:bg-card"
                                    >
                                        <CreditCard className="h-5 w-5 text-muted-foreground" />
                                        <span>Phương thức thanh toán</span>
                                    </Link>

                                    <hr className="my-2 border-border" />

                                    <Link
                                        href="/logout"
                                        method="post"
                                        className="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-primary transition-colors hover:bg-primary/10"
                                    >
                                        <LogOut className="h-5 w-5" />
                                        <span>Đăng xuất</span>
                                    </Link>
                                </div>
                            </div>
                            )}
                        </div>
                    </div>
                </div>
            </header>
            {/* Spacer to offset fixed header so page content is not hidden */}
            <div aria-hidden="true" className="h-20" />
        </>
    );
}
