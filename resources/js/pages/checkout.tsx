import React, { useState } from 'react';
import { router } from '@inertiajs/react';
import TopNav from '../components/top-nav';
import Footer from '../components/footer';
import Input from '../components/ui/input';
import { ToastProvider, useToast } from '../lib/toastContext';
import { Truck, CreditCard, Wallet } from 'lucide-react';

interface CartItem {
    id: number;
    product_id: number;
    variant_id: number;
    name: string;
    image: string;
    color: string;
    price: number;
    quantity: number;
}

interface Address {
    id: number;
    label: string;
    recipient_name: string;
    phone_number: string;
    address_line1: string;
    address_line2: string;
    is_default: boolean;
}

interface CheckoutProps {
    cartItems: CartItem[];
    subtotal: number;
    shipping: number;
    total: number;
    addresses: Address[];
}

function CheckoutContent({ cartItems, subtotal, shipping, total, addresses }: CheckoutProps) {
    const { showSuccess, showError } = useToast();

    // Find default address or use first address
    const defaultAddress = addresses.find(addr => addr.is_default) || addresses[0];

    const [shippingMethod, setShippingMethod] = useState<'standard' | 'express'>('standard');
    const [paymentMethod, setPaymentMethod] = useState<'cod' | 'credit_card' | 'e_wallet'>('cod');
    const [selectedAddressId, setSelectedAddressId] = useState<number | null>(defaultAddress?.id || null);
    const [voucherCode, setVoucherCode] = useState('');
    const [note, setNote] = useState('');
    const [isSubmitting, setIsSubmitting] = useState(false);

    // Calculate dynamic shipping based on method
    const shippingFee = shippingMethod === 'standard' ? 30000 : 50000;
    const finalTotal = subtotal + shippingFee;

    const formatPrice = (price: number) => {
        return new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND',
        }).format(price);
    };

    const handleApplyVoucher = () => {
        const v = voucherCode.trim();
        if (!v) {
            showError('Vui lòng nhập mã voucher');
            return;
        }
        // For now we just show a success message — backend validation could be added later
        showSuccess(`Đã áp dụng mã ${v}`);
    };

    const handleCheckout = () => {
        if (cartItems.length === 0) {
            showError('Giỏ hàng trống');
            return;
        }

        if (!selectedAddressId && addresses.length === 0) {
            showError('Vui lòng thêm địa chỉ giao hàng');
            return;
        }

        setIsSubmitting(true);

        router.post('/checkout', {
            shipping_address_id: selectedAddressId || defaultAddress?.id,
            payment_method: paymentMethod,
            shipping_method: shippingMethod,
            note: note.trim() || null,
        }, {
            preserveScroll: true,
            onSuccess: () => {
                showSuccess('Đơn hàng của bạn đã được xác nhận!');
            },
            onError: (errors) => {
                const errorMessage = Object.values(errors)[0] as string;
                showError(errorMessage || 'Có lỗi xảy ra, vui lòng thử lại');
            },
            onFinish: () => {
                setIsSubmitting(false);
            },
        });
    };

    return (
        <div className="flex min-h-screen flex-col">
            <TopNav />
            <main className="flex-1">
                <div className="container mx-auto px-4 py-8 md:py-16">
                    <div className="flex flex-col items-start gap-8">
                        {/* Header */}
                        <div className="flex w-full flex-col items-center gap-4">
                            <h1 className="text-3xl font-bold text-foreground">Thanh toán</h1>
                            <div className="flex items-center gap-2 text-sm text-muted-foreground">
                                <a className="flex items-center gap-2 hover:text-primary" href="#">
                                    <span>Giỏ hàng</span>
                                    <span className="text-base">&gt;</span>
                                </a>
                                <span className="font-medium text-foreground">Thông tin đơn hàng</span>
                            </div>
                        </div>

                        {/* Main Content */}
                        <div className="grid w-full grid-cols-1 gap-12 lg:grid-cols-3">
                            {/* Left Column - Forms */}
                            <div className="flex flex-col gap-8 lg:col-span-2">
                                {/* Shipping Information */}
                                <div className="flex flex-col gap-6 rounded-xl border border-border bg-card p-6">
                                    <h2 className="text-xl font-bold">Thông tin giao hàng</h2>
                                    <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                        <div className="sm:col-span-2">
                                            <label className="mb-2 block text-sm font-medium" htmlFor="name">
                                                Họ và tên
                                            </label>
                                            <Input
                                                className="h-12"
                                                id="name"
                                                type="text"
                                                value={defaultAddress?.recipient_name || ''}
                                                readOnly
                                                placeholder="Chưa có địa chỉ"
                                            />
                                        </div>
                                        <div className="sm:col-span-2">
                                            <label className="mb-2 block text-sm font-medium" htmlFor="address">
                                                Địa chỉ
                                            </label>
                                            <Input
                                                className="h-12"
                                                id="address"
                                                type="text"
                                                value={[defaultAddress?.address_line1, defaultAddress?.address_line2].filter(Boolean).join(', ') || ''}
                                                readOnly
                                                placeholder="Chưa có địa chỉ"
                                            />
                                        </div>
                                        <div>
                                            <label className="mb-2 block text-sm font-medium" htmlFor="city">
                                                Tỉnh / Thành phố
                                            </label>
                                            <Input
                                                className="h-12"
                                                id="city"
                                                type="text"
                                                value=""
                                                readOnly
                                                placeholder="Chưa có thông tin"
                                            />
                                        </div>
                                        <div>
                                            <label className="mb-2 block text-sm font-medium" htmlFor="district">
                                                Quận / Huyện
                                            </label>
                                            <Input
                                                className="h-12"
                                                id="district"
                                                type="text"
                                                value=""
                                                readOnly
                                                placeholder="Chưa có thông tin"
                                            />
                                        </div>
                                        <div>
                                            <label className="mb-2 block text-sm font-medium" htmlFor="phone">
                                                Số điện thoại
                                            </label>
                                            <Input
                                                className="h-12"
                                                id="phone"
                                                type="tel"
                                                value={defaultAddress?.phone_number || ''}
                                                readOnly
                                                placeholder="Chưa có số điện thoại"
                                            />
                                        </div>
                                        <div className="sm:col-span-2">
                                            <label className="mb-2 block text-sm font-medium" htmlFor="notes">
                                                Ghi chú (tùy chọn)
                                            </label>
                                            <Input
                                                as="textarea"
                                                className="min-h-[72px]"
                                                id="notes"
                                                rows={3}
                                                value={note}
                                                onChange={(e) => setNote(e.target.value)}
                                            />
                                        </div>
                                    </div>
                                </div>

                                {/* Shipping Method */}
                                <div className="flex flex-col gap-6 rounded-xl border border-border bg-card p-6">
                                    <h2 className="text-xl font-bold">Phương thức vận chuyển</h2>
                                    <div className="flex flex-col gap-4">
                                        <label
                                            className={`flex cursor-pointer items-center gap-4 rounded-lg border p-4 transition-colors ${
                                                shippingMethod === 'standard'
                                                    ? 'border-primary bg-primary/5'
                                                    : 'border-border bg-background hover:border-gray-300'
                                            }`}
                                        >
                                            <input
                                                checked={shippingMethod === 'standard'}
                                                className="form-radio h-5 w-5 text-primary focus:ring-primary/50"
                                                name="shipping-method"
                                                type="radio"
                                                onChange={() => setShippingMethod('standard')}
                                            />
                                            <div className="flex flex-1 flex-col sm:flex-row sm:items-center sm:justify-between">
                                                <div>
                                                    <p className="font-medium">Giao hàng tiêu chuẩn</p>
                                                    <p className="text-sm text-muted-foreground">
                                                        Ước tính: 3-5 ngày làm việc
                                                    </p>
                                                </div>
                                                <span className="mt-2 font-medium text-foreground sm:mt-0">
                                                    30.000₫
                                                </span>
                                            </div>
                                        </label>
                                        <label
                                            className={`flex cursor-pointer items-center gap-4 rounded-lg border p-4 transition-colors ${
                                                shippingMethod === 'express'
                                                    ? 'border-primary bg-primary/5'
                                                    : 'border-border bg-background hover:border-gray-300'
                                            }`}
                                        >
                                            <input
                                                checked={shippingMethod === 'express'}
                                                className="form-radio h-5 w-5 text-primary focus:ring-primary/50"
                                                name="shipping-method"
                                                type="radio"
                                                onChange={() => setShippingMethod('express')}
                                            />
                                            <div className="flex flex-1 flex-col sm:flex-row sm:items-center sm:justify-between">
                                                <div>
                                                    <p className="font-medium">Giao hàng nhanh</p>
                                                    <p className="text-sm text-muted-foreground">
                                                        Ước tính: 1-2 ngày làm việc
                                                    </p>
                                                </div>
                                                <span className="mt-2 font-medium text-foreground sm:mt-0">
                                                    50.000₫
                                                </span>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                {/* Payment Method */}
                                <div className="flex flex-col gap-6 rounded-xl border border-border bg-card p-6">
                                    <h2 className="text-xl font-bold">Phương thức thanh toán</h2>
                                    <div className="flex flex-col gap-4">
                                        <label
                                            className={`flex cursor-pointer items-center gap-4 rounded-lg border p-4 transition-colors ${
                                                paymentMethod === 'cod'
                                                    ? 'border-primary bg-primary/5'
                                                    : 'border-border bg-background hover:border-gray-300'
                                            }`}
                                        >
                                            <input
                                                checked={paymentMethod === 'cod'}
                                                className="form-radio h-5 w-5 text-primary focus:ring-primary/50"
                                                name="payment-method"
                                                type="radio"
                                                onChange={() => setPaymentMethod('cod')}
                                            />
                                            <div className="flex flex-1 flex-col sm:flex-row sm:items-center sm:justify-between">
                                                <span className="font-medium">Thanh toán khi nhận hàng (COD)</span>
                                                <Truck className="h-6 w-6 text-secondary" />
                                            </div>
                                        </label>
                                        <label
                                            className={`flex cursor-pointer items-center gap-4 rounded-lg border p-4 transition-colors ${
                                                paymentMethod === 'credit_card'
                                                    ? 'border-primary bg-primary/5'
                                                    : 'border-border bg-background hover:border-gray-300'
                                            }`}
                                        >
                                            <input
                                                checked={paymentMethod === 'credit_card'}
                                                className="form-radio h-5 w-5 text-primary focus:ring-primary/50"
                                                name="payment-method"
                                                type="radio"
                                                onChange={() => setPaymentMethod('credit_card')}
                                            />
                                            <div className="flex flex-1 flex-col sm:flex-row sm:items-center sm:justify-between">
                                                <span className="font-medium">Thẻ tín dụng / Ghi nợ</span>
                                                <CreditCard className="h-6 w-6 text-secondary" />
                                            </div>
                                        </label>
                                        <label
                                            className={`flex cursor-pointer items-center gap-4 rounded-lg border p-4 transition-colors ${
                                                paymentMethod === 'e_wallet'
                                                    ? 'border-primary bg-primary/5'
                                                    : 'border-border bg-background hover:border-gray-300'
                                            }`}
                                        >
                                            <input
                                                checked={paymentMethod === 'e_wallet'}
                                                className="form-radio h-5 w-5 text-primary focus:ring-primary/50"
                                                name="payment-method"
                                                type="radio"
                                                onChange={() => setPaymentMethod('e_wallet')}
                                            />
                                            <div className="flex flex-1 flex-col sm:flex-row sm:items-center sm:justify-between">
                                                <span className="font-medium">Ví điện tử</span>
                                                <Wallet className="h-6 w-6 text-secondary" />
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            {/* Right Column - Order Summary */}
                            <div className="lg:col-span-1">
                                <div className="sticky top-24 rounded-xl border border-border bg-card p-6">
                                    <h2 className="mb-6 text-xl font-bold">Tóm tắt đơn hàng</h2>
                                    <div className="mb-6 flex flex-col gap-4">
                                        {cartItems.map((item) => (
                                            <div key={item.id} className="flex items-center gap-4">
                                                <div className="relative flex-shrink-0">
                                                    <div className="h-16 w-16 overflow-hidden rounded-lg bg-background">
                                                        <img
                                                            alt={item.name}
                                                            className="h-full w-full object-cover"
                                                            src={item.image}
                                                        />
                                                    </div>
                                                    <div className="absolute -right-2 -top-2 flex h-6 w-6 items-center justify-center rounded-full bg-primary text-xs font-bold text-white">
                                                        {item.quantity}
                                                    </div>
                                                </div>
                                                <div className="flex-1">
                                                    <h3 className="font-medium text-foreground">{item.name}</h3>
                                                    <p className="text-sm text-muted-foreground">Màu: {item.color}</p>
                                                </div>
                                                <p className="font-medium">{formatPrice(item.price)}</p>
                                            </div>
                                        ))}
                                    </div>
                                    <hr className="mb-4 border-border" />
                                    <div className="mb-6 flex flex-col gap-3">
                                        <div className="flex justify-between text-muted-foreground">
                                            <span>Tạm tính</span>
                                            <span className="font-medium text-foreground">
                                                {formatPrice(subtotal)}
                                            </span>
                                        </div>
                                        <div className="mb-4 flex flex-col gap-4">
                                            <div className="flex items-end gap-2">
                                                <div className="flex-1">
                                                    <label className="mb-2 block text-sm font-medium" htmlFor="voucher">
                                                        Mã khuyến mãi
                                                    </label>
                                                    <Input
                                                        className="h-10"
                                                        id="voucher"
                                                        placeholder="Nhập mã voucher"
                                                        value={voucherCode}
                                                        onChange={(e) => setVoucherCode(e.target.value)}
                                                    />
                                                </div>
                                                <button
                                                    type="button"
                                                    onClick={handleApplyVoucher}
                                                    className="flex h-10 cursor-pointer items-center justify-center overflow-hidden rounded-lg bg-primary px-4 text-white text-sm font-bold transition-colors hover:bg-secondary/90"
                                                >
                                                    Áp dụng
                                                </button>
                                            </div>
                                        </div>
                                        <div className="flex justify-between text-muted-foreground">
                                            <span>Phí vận chuyển</span>
                                            <span className="font-medium text-foreground">
                                                {formatPrice(shippingFee)}
                                            </span>
                                        </div>
                                    </div>
                                    <hr className="mb-6 border-border" />
                                    <div className="mb-6 flex justify-between">
                                        <span className="text-lg font-bold">Tổng cộng</span>
                                        <span className="text-xl font-bold text-primary">{formatPrice(finalTotal)}</span>
                                    </div>
                                    <button
                                        onClick={handleCheckout}
                                        disabled={isSubmitting || cartItems.length === 0}
                                        className="flex h-12 w-full max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg bg-primary px-6 text-base font-bold leading-normal tracking-[0.015em] text-white transition-colors hover:bg-primary/90 disabled:cursor-not-allowed disabled:opacity-50"
                                    >
                                        <span className="truncate">{isSubmitting ? 'Đang xử lý...' : 'Xác nhận thanh toán'}</span>
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

export default function Checkout(props: CheckoutProps) {
    return (
        <ToastProvider>
            <CheckoutContent {...props} />
        </ToastProvider>
    );
}
