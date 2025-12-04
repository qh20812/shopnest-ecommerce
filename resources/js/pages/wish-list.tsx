import { useState } from 'react';
import { Trash2, ShoppingCart } from 'lucide-react';
import { Link } from '@inertiajs/react';
import TopNav from '../components/top-nav';
import Footer from '../components/footer';
import { ToastProvider, useToast } from '../lib/toastContext';
import { PageHeader } from '../components/ui/page-header';

interface WishListItem {
    id: number;
    name: string;
    image: string;
    price: number;
}

function WishListContent() {
    const { showSuccess, showInfo } = useToast();

    // Sample wishlist items - replace with real data later
    const [wishListItems, setWishListItems] = useState<WishListItem[]>([
        {
            id: 1,
            name: 'Ghế sofa hiện đại',
            image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuA9uGUJM3fOiuvfyaQBWOm22jgY1q5pysCTNfHVYTUJZK5PRh0WMJkzxTBq0DFnSIujRMboVW7Cw1CWVEjVOIxGkElzFfag9xgMyfPPBgjoDST2mxglOz70oqHPwzmBXwnAzbr2hV98rDNZ4HEOoj2yfMYlc3tEFTYer-UUmVXykCYu5wxwFe47rsq-rSClwvzrlux2IKatu94GMsfXXcD7uVvzfbbjjiqSa5Bmx0SNqnq8pECwHe_L6mnDjed0CHoGb1EqaW2puh8',
            price: 2500000,
        },
        {
            id: 2,
            name: 'Đèn bàn tối giản',
            image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuA9uGUJM3fOiuvfyaQBWOm22jgY1q5pysCTNfHVYTUJZK5PRh0WMJkzxTBq0DFnSIujRMboVW7Cw1CWVEjVOIxGkElzFfag9xgMyfPPBgjoDST2mxglOz70oqHPwzmBXwnAzbr2hV98rDNZ4HEOoj2yfMYlc3tEFTYer-UUmVXykCYu5wxwFe47rsq-rSClwvzrlux2IKatu94GMsfXXcD7uVvzfbbjjiqSa5Bmx0SNqnq8pECwHe_L6mnDjed0CHoGb1EqaW2puh8',
            price: 750000,
        },
        {
            id: 3,
            name: 'Tai nghe không dây',
            image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuA9uGUJM3fOiuvfyaQBWOm22jgY1q5pysCTNfHVYTUJZK5PRh0WMJkzxTBq0DFnSIujRMboVW7Cw1CWVEjVOIxGkElzFfag9xgMyfPPBgjoDST2mxglOz70oqHPwzmBXwnAzbr2hV98rDNZ4HEOoj2yfMYlc3tEFTYer-UUmVXykCYu5wxwFe47rsq-rSClwvzrlux2IKatu94GMsfXXcD7uVvzfbbjjiqSa5Bmx0SNqnq8pECwHe_L6mnDjed0CHoGb1EqaW2puh8',
            price: 3200000,
        },
        {
            id: 4,
            name: 'Đồng hồ thông minh',
            image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuA9uGUJM3fOiuvfyaQBWOm22jgY1q5pysCTNfHVYTUJZK5PRh0WMJkzxTBq0DFnSIujRMboVW7Cw1CWVEjVOIxGkElzFfag9xgMyfPPBgjoDST2mxglOz70oqHPwzmBXwnAzbr2hV98rDNZ4HEOoj2yfMYlc3tEFTYer-UUmVXykCYu5wxwFe47rsq-rSClwvzrlux2IKatu94GMsfXXcD7uVvzfbbjjiqSa5Bmx0SNqnq8pECwHe_L6mnDjed0CHoGb1EqaW2puh8',
            price: 5500000,
        },
    ]);

    const formatPrice = (price: number) =>
        new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND',
        }).format(price);

    const removeItem = (id: number) => {
        setWishListItems((items) => items.filter((item) => item.id !== id));
        showSuccess('Đã xóa sản phẩm khỏi danh sách yêu thích');
    };

    const removeAll = () => {
        if (wishListItems.length === 0) {
            showInfo('Danh sách yêu thích trống');
            return;
        }
        setWishListItems([]);
        showSuccess('Đã xóa tất cả sản phẩm khỏi danh sách yêu thích');
    };

    const addToCart = () => {
        showSuccess('Đã thêm sản phẩm vào giỏ hàng');
    };

    const addAllToCart = () => {
        if (wishListItems.length === 0) {
            showInfo('Danh sách yêu thích trống');
            return;
        }
        showSuccess(`Đã thêm ${wishListItems.length} sản phẩm vào giỏ hàng`);
    };

    return (
        <div className="relative flex min-h-screen w-full flex-col overflow-x-hidden bg-background text-foreground">
            <TopNav />

            <main className="flex-1">
                <div className="container mx-auto px-4 py-8 md:py-16">
                    <PageHeader
                        title="Danh sách yêu thích"
                        actions={[
                            {
                                label: 'Xóa tất cả',
                                icon: Trash2,
                                onClick: removeAll,
                                variant: 'secondary'
                            },
                            {
                                label: 'Thêm tất cả vào giỏ',
                                icon: ShoppingCart,
                                onClick: addAllToCart,
                                variant: 'default'
                            }
                        ]}
                    />

                    {/* Product Grid */}
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        {wishListItems.map((item) => (
                            <div
                                key={item.id}
                                className="flex flex-col overflow-hidden rounded-xl bg-card border border-border"
                            >
                                {/* Product Image */}
                                <Link href="#" className="relative block group">
                                    <div
                                        className="aspect-[4/3] bg-cover bg-center bg-gray-200"
                                        style={{ backgroundImage: `url("${item.image}")` }}
                                    />
                                    <div className="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                        <button className="flex cursor-pointer items-center justify-center overflow-hidden rounded-lg h-12 px-6 bg-primary text-white text-sm font-bold leading-normal tracking-[0.015em] shadow-sm hover:bg-primary/90 transition-colors">
                                            Xem chi tiết
                                        </button>
                                    </div>
                                </Link>

                                {/* Product Info */}
                                <div className="p-4 flex-1 flex flex-col">
                                    <h3 className="font-bold text-lg text-foreground flex-1">
                                        {item.name}
                                    </h3>
                                    <p className="text-primary font-bold text-xl my-2">
                                        {formatPrice(item.price)}
                                    </p>

                                    {/* Actions */}
                                    <div className="flex flex-col gap-2 mt-2">
                                        <button
                                            onClick={addToCart}
                                            className="flex w-full min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-11 px-4 bg-primary text-white text-sm font-bold leading-normal tracking-[0.015em] hover:bg-primary/90 transition-colors"
                                        >
                                            <ShoppingCart className="mr-2 h-5 w-5" />
                                            <span className="truncate">Thêm vào giỏ hàng</span>
                                        </button>
                                        <button
                                            onClick={() => removeItem(item.id)}
                                            className="flex w-full min-w-[84px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-11 px-4 bg-transparent text-muted-foreground text-sm font-bold leading-normal tracking-[0.015em] hover:bg-primary/10 hover:text-primary transition-colors"
                                        >
                                            <Trash2 className="mr-2 h-5 w-5" />
                                            <span className="truncate">Xóa khỏi danh sách</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </main>

            <Footer />
        </div>
    );
}

export default function WishList() {
    return (
        <ToastProvider>
            <WishListContent />
        </ToastProvider>
    );
}
