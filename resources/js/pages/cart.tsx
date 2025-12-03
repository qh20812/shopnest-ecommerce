import { useState } from 'react';
import { Minus, Plus, Trash2 } from 'lucide-react';
import { Link } from '@inertiajs/react';
import TopNav from '../components/top-nav';
import Footer from '../components/footer';
import { ToastProvider, useToast } from '../lib/toastContext';

interface CartItem {
    id: number;
    name: string;
    image: string;
    color: string;
    price: number;
    quantity: number;
}

function CartContent() {
    const { showSuccess, showInfo } = useToast();
    const [couponCode, setCouponCode] = useState('');
    
    // Sample cart items - replace with real data later
    const [cartItems, setCartItems] = useState<CartItem[]>([
        {
            id: 1,
            name: 'Tai nghe không dây',
            image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuDPHcVWSj_HTG2XqzClkE5SpZxqSTMbCcEnCYKEMa_xbDd4rJqjsJyrUJevOPfgYkEvjv6iNNHxjtRSiS7qcQPueRPlzF9Aue5LW_lr0FVXn9UIkKI5Bq21ItYV_Wn45xycvWOmMOz7RykU5KOzelN0VBsmfJQIcjJBIZSUuHr6KKCNQLeb6v8srFZ8c5EpdNRxF4Oh40SBSPsqewAUR19-X9PS4Xgc5v6l9-_LtFkR9wzP0ppQIVNI7QDXT0OBZxzUfHav61frpxs',
            color: 'Đen',
            price: 1250000,
            quantity: 1,
        },
        {
            id: 2,
            name: 'Đồng hồ thông minh',
            image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuCD9ncBBhzi2Db0DFaD7c6VNw9AijOwGBC5xZWwfTY8aPwW9Ntp16-9U3AdtDt8yOVptdyU_6B6vh5SeZJVzoMBayYjoltDSrnyFslFa7Cz0cu81NfJZ9H8cPII_bW4XmSswdeLw83LbAISWagZZEdCo3DCzkb009c3DKQm_507v0f4m04q7ohbP6VX3f7ApRIjCCKsQ3p3AVbGaOfqHwocFgiI-QCPchNYdA1g6a_mXAHKWE_e2ZGCv0Didsl9Ubni3guYhG-0GEI',
            color: 'Bạc',
            price: 3490000,
            quantity: 1,
        },
    ]);

    const formatPrice = (price: number) =>
        new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND',
        }).format(price);

    const updateQuantity = (id: number, delta: number) => {
        setCartItems((items) =>
            items.map((item) => {
                if (item.id === id) {
                    const newQuantity = Math.max(1, item.quantity + delta);
                    return { ...item, quantity: newQuantity };
                }
                return item;
            })
        );
    };

    const removeItem = (id: number) => {
        setCartItems((items) => items.filter((item) => item.id !== id));
        showSuccess('Đã xóa sản phẩm khỏi giỏ hàng');
    };

    const applyCoupon = () => {
        if (!couponCode.trim()) {
            showInfo('Vui lòng nhập mã giảm giá');
            return;
        }
        showInfo('Mã giảm giá không hợp lệ');
    };

    const subtotal = cartItems.reduce((sum, item) => sum + item.price * item.quantity, 0);
    const shipping = 0; // Free shipping
    const total = subtotal + shipping;

    const handleCheckout = () => {
        showSuccess('Đang chuyển đến trang thanh toán...');
    };

    return (
        <div className="relative flex min-h-screen w-full flex-col overflow-x-hidden bg-background text-foreground">
            <TopNav />
            
            <main className="flex-1">
                <div className="container mx-auto px-4 py-8 md:py-16">
                    <div className="flex flex-col items-start gap-8">
                        {/* Header */}
                        <div className="flex w-full flex-col items-start gap-4 md:flex-row md:items-center md:justify-between">
                            <h1 className="text-3xl font-bold text-foreground">
                                Giỏ hàng của bạn
                            </h1>
                            <Link
                                href="/"
                                className="text-sm font-medium text-primary hover:underline"
                            >
                                Tiếp tục mua sắm
                            </Link>
                        </div>

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
                                {cartItems.map((item, index) => (
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
                                        {index < cartItems.length - 1 && (
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
                                                {formatPrice(subtotal)}
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
                                            {formatPrice(total)}
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
                </div>
            </main>

            <Footer />
        </div>
    );
}

export default function Cart() {
    return (
        <ToastProvider>
            <CartContent />
        </ToastProvider>
    );
}
