import { useState } from 'react';
import { Trash2, ShoppingCart } from 'lucide-react';
import { Link, router, usePage } from '@inertiajs/react';
import TopNav from '../components/top-nav';
import Footer from '../components/footer';
import { ToastProvider, useToast } from '../lib/toastContext';
import { PageHeader } from '../components/ui/page-header';

interface WishListItem {
    id: number;
    product_id: number;
    name: string;
    image: string | null;
    price: number;
}

interface WishListPageProps {
    wishlistItems: WishListItem[];
    [key: string]: unknown;
}

function WishListContent() {
    const { showSuccess, showInfo } = useToast();
    const { wishlistItems: initialItems } = usePage<WishListPageProps>().props;
    const [wishListItems, setWishListItems] = useState<WishListItem[]>(initialItems || []);

    const formatPrice = (price: number) =>
        new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND',
        }).format(price);

    const removeItem = async (id: number) => {
        try {
            await router.delete(`/wish-list/${id}`, {
                preserveScroll: true,
                onSuccess: () => {
                    setWishListItems((items) => items.filter((item) => item.id !== id));
                    showSuccess('Đã xóa sản phẩm khỏi danh sách yêu thích');
                },
                onError: () => {
                    showInfo('Không thể xóa sản phẩm');
                },
            });
        } catch (error) {
            showInfo('Đã có lỗi xảy ra');
        }
    };

    const removeAll = async () => {
        if (wishListItems.length === 0) {
            showInfo('Danh sách yêu thích trống');
            return;
        }
        try {
            await router.post('/wish-list/clear', {}, {
                preserveScroll: true,
                onSuccess: () => {
                    setWishListItems([]);
                    showSuccess('Đã xóa tất cả sản phẩm khỏi danh sách yêu thích');
                },
                onError: () => {
                    showInfo('Không thể xóa danh sách');
                },
            });
        } catch {
            showInfo('Đã có lỗi xảy ra');
        }
    };

    const addToCart = (productId: number) => {
        // Navigate to product detail page where user can select variant and add to cart
        router.visit(`/product/${productId}`);
    };

    const addAllToCart = async () => {
        if (wishListItems.length === 0) {
            showInfo('Danh sách yêu thích trống');
            return;
        }
        try {
            await router.post('/wish-list/add-all-to-cart', {}, {
                preserveScroll: true,
                onSuccess: () => {
                    showSuccess(`Đã thêm ${wishListItems.length} sản phẩm vào giỏ hàng`);
                },
                onError: () => {
                    showInfo('Không thể thêm vào giỏ hàng');
                },
            });
        } catch {
            showInfo('Đã có lỗi xảy ra');
        }
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

                    {/* Empty State */}
                    {wishListItems.length === 0 ? (
                        <div className="text-center py-16">
                            <p className="text-muted-foreground text-lg mb-4">Danh sách yêu thích của bạn đang trống</p>
                            <Link
                                href="/"
                                className="inline-flex items-center justify-center rounded-lg h-11 px-6 bg-primary text-white text-sm font-bold hover:bg-primary/90 transition-colors"
                            >
                                Khám phá sản phẩm
                            </Link>
                        </div>
                    ) : (
                        /* Product Grid */
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        {wishListItems.map((item) => (
                            <div
                                key={item.id}
                                className="flex flex-col overflow-hidden rounded-xl bg-card border border-border"
                            >
                                {/* Product Image */}
                                <Link href={`/product/${item.product_id}`} className="relative block group">
                                    <div
                                        className="aspect-[4/3] bg-cover bg-center bg-gray-200"
                                        style={{ backgroundImage: item.image ? `url("${item.image}")` : 'none' }}
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
                                            onClick={() => addToCart(item.product_id)}
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
                    )}
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
