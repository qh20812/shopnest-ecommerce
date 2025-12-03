import { ShoppingBag, Heart, Search } from 'lucide-react';
import Input from './ui/input';
import { Link } from '@inertiajs/react';
import { useToast } from '../lib/toastContext';

export default function TopNav() {
    const { showError, showInfo } = useToast();

    return (
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
    );
}
