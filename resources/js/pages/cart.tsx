import { useState } from 'react';
import { Home, Minus, Plus, Trash2 } from 'lucide-react';
import { router } from '@inertiajs/react';
import TopNav from '../components/top-nav';
import Footer from '../components/footer';
import { ToastProvider, useToast } from '../lib/toastContext';
import { PageHeader } from '../components/ui/page-header';

interface CartItem {
    id: number;
    product_id: number;
    variant_id: number;
    name: string;
    image: string;
    color: string;
    price: number;
    compare_at_price: number;
    quantity: number;
    stock_quantity: number;
}

interface CartProps {
    cartItems: CartItem[];
    subtotal: number;
    shipping: number;
    total: number;
}

function CartContent({ cartItems: initialCartItems, subtotal: initialSubtotal, shipping: initialShipping, total: initialTotal }: CartProps) {
    const { showSuccess, showInfo } = useToast();
    const [couponCode, setCouponCode] = useState('');
    const [isUpdating, setIsUpdating] = useState(false);

    const formatPrice = (price: number) =>
        new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND',
        }).format(price);

    const updateQuantity = (id: number, delta: number) => {
        const item = initialCartItems.find(i => i.id === id);
        if (!item) return;

        const newQuantity = Math.max(1, item.quantity + delta);
        
        if (newQuantity > item.stock_quantity) {
            showInfo(`Số lượng tối đa: ${item.stock_quantity}`);
            return;
        }

        setIsUpdating(true);
        router.patch(`/cart/${id}`, { quantity: newQuantity }, {
            preserveScroll: true,
            onSuccess: () => {
                showSuccess('Đã cập nhật số lượng');
                setIsUpdating(false);
            },
            onError: () => {
                showInfo('Không thể cập nhật số lượng');
                setIsUpdating(false);
            }
        });
    };

    const removeItem = (id: number) => {
        router.delete(`/cart/${id}`, {
            preserveScroll: true,
            onSuccess: () => {
                showSuccess('Đã xóa sản phẩm khỏi giỏ hàng');
            }
        });
    };

    const applyCoupon = () => {
        if (!couponCode.trim()) {
            showInfo('Vui lòng nhập mã giảm giá');
            return;
        }
        
        router.post('/cart/apply-coupon', { coupon_code: couponCode }, {
            preserveScroll: true,
            onError: () => {
                showInfo('Mã giảm giá không hợp lệ');
            }
        });
    };

    const removeAll = () => {
        if (initialCartItems.length === 0) {
            showInfo('Giỏ hàng trống');
            return;
        }
        
        router.post('/cart/clear', {}, {
            preserveScroll: true,
            onSuccess: () => {
                showSuccess('Đã xóa tất cả sản phẩm khỏi giỏ hàng');
            }
        });
    };

    const handleCheckout = () => {
        showSuccess('Đang chuyển đến trang thanh toán...');
    };

    return (
        <div className="relative flex min-h-screen w-full flex-col overflow-x-hidden bg-background text-foreground">
            <TopNav />

            <main className="flex-1">
                <div className="container mx-auto px-4 py-8 md:py-16">

                    <PageHeader
                        title="Giỏ hàng của tôi"
                        actions={[
                            {
                                label: 'Xóa tất cả',
                                icon: Trash2,
                                onClick: removeAll,
                                variant: 'secondary'
                            },
                            {
                                label: 'Thêm tất cả vào giỏ',
                                icon: Home,
                                onClick: () => router.visit('/'),
                                variant: 'default'
                            }
                        ]}
                    />

                    {/* Cart Grid */}
                    <div className="grid w-full grid-cols-1 gap-8 lg:grid-cols-3">
                        {/* Cart Items */}
                        <div className="flex flex-col gap-6 lg:col-span-2">
                            {/* Table Header - Hidden on mobile */}
                            <div className="hidden grid-cols-6 gap-4 border-b border-border pb-4 text-sm font-medium text-muted-foreground md:grid">
                                <div className="col-span-3">Sản phẩm</div>
                                <div className="col-span-1 text-center">Số lượng</div>
                                <div className="col-span-1 text-right">Giá</div>
                                <div className="col-span-1 text-right">Tổng</div>
                            </div>

                            {/* Cart Items List */}
                            {initialCartItems.map((item, index) => (
                                <div key={item.id}>
                                    <div className="grid grid-cols-1 gap-6 md:grid-cols-6 md:items-center">
                                        {/* Product Info */}
                                        <div className="flex items-center gap-4 md:col-span-3">
                                            <div className="h-24 w-24 flex-shrink-0 overflow-hidden rounded-lg bg-card">
                                                <img
                                                    alt={item.name}
                                                    className="h-full w-full object-cover"
                                                    src={item.image}
                                                />
                                            </div>
                                            <div>
                                                <h3 className="font-bold text-foreground">
                                                    {item.name}
                                                </h3>
                                                <p className="text-sm text-muted-foreground">
                                                    Màu: {item.color}
                                                </p>
                                            </div>
                                        </div>

                                        {/* Quantity */}
                                        <div className="flex items-center justify-between md:col-span-1 md:justify-center">
                                            <span className="font-medium text-muted-foreground md:hidden">
                                                Số lượng
                                            </span>
                                            <div className="flex items-center gap-2 rounded-lg border border-border bg-background p-1">
                                                <button
                                                    onClick={() => updateQuantity(item.id, -1)}
                                                    className="flex h-8 w-8 items-center justify-center rounded-md hover:bg-card"
                                                >
                                                    <Minus className="h-4 w-4" />
                                                </button>
                                                <span className="w-8 text-center font-medium">
                                                    {item.quantity}
                                                </span>
                                                <button
                                                    onClick={() => updateQuantity(item.id, 1)}
                                                    className="flex h-8 w-8 items-center justify-center rounded-md hover:bg-card"
                                                >
                                                    <Plus className="h-4 w-4" />
                                                </button>
                                            </div>
                                        </div>

                                        {/* Price */}
                                        <div className="flex items-center justify-between md:col-span-1 md:justify-end">
                                            <span className="font-medium text-muted-foreground md:hidden">
                                                Giá
                                            </span>
                                            <p className="font-medium">{formatPrice(item.price)}</p>
                                        </div>

                                        {/* Total & Delete */}
                                        <div className="flex items-center justify-between md:col-span-1 md:justify-end">
                                            <span className="font-medium text-muted-foreground md:hidden">
                                                Tổng
                                            </span>
                                            <p className="font-bold">
                                                {formatPrice(item.price * item.quantity)}
                                            </p>
                                            <button
                                                onClick={() => removeItem(item.id)}
                                                className="ml-4 flex h-10 w-10 items-center justify-center rounded-lg text-muted-foreground hover:bg-card hover:text-red-500 transition-colors"
                                            >
                                                <Trash2 className="h-5 w-5" />
                                            </button>
                                        </div>
                                    </div>
                                    {index < initialCartItems.length - 1 && (
                                        <hr className="mt-6 border-border" />
                                    )}
                                </div>
                            ))}
                        </div>

                        {/* Order Summary */}
                        <div className="lg:col-span-1">
                            <div className="sticky top-24 rounded-xl border border-border bg-card p-6">
                                <h2 className="mb-6 text-xl font-bold">Tóm tắt đơn hàng</h2>

                                {/* Subtotal & Shipping */}
                                <div className="mb-4 flex flex-col gap-3">
                                    <div className="flex justify-between text-muted-foreground">
                                        <span>Tạm tính</span>
                                        <span className="font-medium text-foreground">
                                            {formatPrice(initialSubtotal)}
                                        </span>
                                    </div>
                                    <div className="flex justify-between text-muted-foreground">
                                        <span>Phí vận chuyển</span>
                                        <span className="font-medium text-foreground">
                                            Miễn phí
                                        </span>
                                    </div>
                                </div>

                                {/* Coupon Code */}
                                <div className="mb-6">
                                    <p className="mb-2 text-sm font-medium">Mã giảm giá</p>
                                    <div className="flex gap-2">
                                        <input
                                            type="text"
                                            className="form-input w-full flex-1 rounded-lg border-border bg-background placeholder:text-muted-foreground focus:border-primary focus:ring-primary/50 py-2 px-3 h-12"
                                            placeholder="Nhập mã giảm giá"
                                            value={couponCode}
                                            onChange={(e) => setCouponCode(e.target.value)}
                                        />
                                        <button
                                            onClick={applyCoupon}
                                            className="flex min-w-[84px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-12 px-5 bg-primary/20 text-primary text-base font-bold leading-normal tracking-[0.015em] hover:bg-primary/30 transition-colors"
                                        >
                                            Áp dụng
                                        </button>
                                    </div>
                                </div>

                                <hr className="mb-6 border-border" />

                                {/* Total */}
                                <div className="mb-6 flex justify-between">
                                    <span className="text-lg font-bold">Tổng cộng</span>
                                    <span className="text-xl font-bold text-primary">
                                        {formatPrice(initialTotal)}
                                    </span>
                                </div>

                                {/* Checkout Button */}
                                <button
                                    onClick={handleCheckout}
                                    className="flex w-full min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg h-12 px-6 bg-primary text-white text-base font-bold leading-normal tracking-[0.015em] hover:bg-primary/90 transition-colors"
                                >
                                    <span className="truncate">Tiến hành thanh toán</span>
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </main>

            <Footer />
        </div>
    );
}

export default function Cart({ cartItems = [], subtotal = 0, shipping = 0, total = 0 }: CartProps) {
    return (
        <ToastProvider>
            <CartContent 
                cartItems={cartItems}
                subtotal={subtotal}
                shipping={shipping}
                total={total}
            />
        </ToastProvider>
    );
}
