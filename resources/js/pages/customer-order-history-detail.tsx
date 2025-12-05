import { ArrowLeft } from 'lucide-react';
import { Link } from '@inertiajs/react';
import CustomerLayout from '../layouts/customer-layout';

function CustomerOrderHistoryDetailContent() {
    // Sample order data
    const order = {
        orderNumber: '#SN123456',
        orderDate: '15/07/2024',
        status: 'delivered',
        statusLabel: 'Đã giao hàng',
        shippingInfo: {
            name: 'Người dùng',
            address: '123 Đường ABC, Phường XYZ, Quận 1, TP. Hồ Chí Minh',
            phone: '0987 654 321',
        },
        paymentMethod: 'Thanh toán khi nhận hàng (COD)',
        items: [
            {
                id: '1',
                name: 'Đồng hồ thông minh Pro X',
                quantity: 1,
                price: '850.000₫',
                image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuAMq2DD0H40DmhFa4PI32DpLwYpUKZP-J_wdHAH-p8kMccPZsKsEWR_3qGuwJp8c9BnwJX3i0uJMrEsz_LirTIRu53dOuY9Ui2zc_7F4BOWMwYSbXZFMpsWl1_coyz7PYcOuxA1fAiAqLa_Ic2G5q6jnqhMJ3J-m6rAYo6YL2ZCTqlMeE4kF1SDevc40ApkUtlIfEVjeXHpMa-9P1VJ3j94vM5sNMNfrzxCqYMeh076loMTVLWFWCEpfUVoUC-OHU5Zk-FIXfyuPOg',
            },
            {
                id: '2',
                name: 'Tai nghe không dây BassMax',
                quantity: 1,
                price: '400.000₫',
                image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuDQOYlaibfSUNPs1R7Ilrp7x55nAUJp8pgdaAERKERy5XPw7IZW4qmmFMGyzKainFgfjEZZ18bsjiEqJ_fOShAYd6kLYoemxlYl3CnmPf5YHQ-xKcXfbVy83EPtROV7kmnwHt3RYqBj9BO73fftmpjwPHtkLSmORfs0JFaad2xpzRo8CQMmezANEO3HYQAUq2cIz-BjRKFA9P8AhzwnSKgTzANIsYPHNlfW-sQC6aLVNFZ2wbn8AHwqtVfDgokp_7U29nqw50LqzPk',
            },
        ],
        subtotal: '1.250.000₫',
        shippingFee: '0₫',
        total: '1.250.000₫',
    };

    const getStatusStyles = (status: string) => {
        switch (status) {
            case 'delivered':
                return 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300';
            case 'processing':
                return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300';
            case 'cancelled':
                return 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300';
            default:
                return '';
        }
    };

    return (
        <div className="rounded-xl bg-background p-6 shadow-md md:p-8">
            {/* Header */}
            <div className="mb-6 flex flex-col justify-between gap-4 border-b border-border pb-6 md:flex-row md:items-center">
                <div>
                    <div className="flex items-center gap-3">
                        <Link
                            href="#"
                            className="text-muted-foreground transition-colors hover:text-primary"
                        >
                            <ArrowLeft className="h-6 w-6" />
                        </Link>
                        <h2 className="text-2xl font-bold text-foreground md:text-3xl">
                            Chi tiết đơn hàng
                        </h2>
                    </div>
                    <p className="mt-2 text-muted-foreground md:ml-10">
                        Mã đơn hàng:{' '}
                        <span className="font-semibold text-foreground">
                            {order.orderNumber}
                        </span>
                    </p>
                    <p className="text-muted-foreground md:ml-10">
                        Ngày đặt hàng: {order.orderDate}
                    </p>
                </div>
                <div className="flex items-center gap-2 self-start md:self-center">
                    <span className="text-sm font-medium">Trạng thái:</span>
                    <span
                        className={`rounded-full px-3 py-1 text-sm font-medium ${getStatusStyles(order.status)}`}
                    >
                        {order.statusLabel}
                    </span>
                </div>
            </div>

            {/* Shipping & Payment Info */}
            <div className="mb-8 grid grid-cols-1 gap-8 lg:grid-cols-3">
                <div className="space-y-4">
                    <h3 className="text-lg font-bold text-foreground">
                        Thông tin giao hàng
                    </h3>
                    <div className="space-y-1 text-sm text-muted-foreground">
                        <p className="font-semibold text-foreground">
                            {order.shippingInfo.name}
                        </p>
                        <p>{order.shippingInfo.address}</p>
                        <p>Điện thoại: {order.shippingInfo.phone}</p>
                    </div>
                </div>
                <div className="space-y-4">
                    <h3 className="text-lg font-bold text-foreground">
                        Phương thức thanh toán
                    </h3>
                    <div className="space-y-1 text-sm text-muted-foreground">
                        <p>{order.paymentMethod}</p>
                    </div>
                </div>
            </div>

            {/* Order Items */}
            <div className="mb-8">
                <h3 className="mb-4 text-lg font-bold text-foreground">
                    Các sản phẩm trong đơn hàng
                </h3>
                <div className="space-y-4">
                    {order.items.map((item) => (
                        <div
                            key={item.id}
                            className="flex items-center gap-4 rounded-lg bg-card/50 p-4"
                        >
                            <img
                                alt={item.name}
                                className="h-20 w-20 rounded-lg object-cover"
                                src={item.image}
                            />
                            <div className="flex-1">
                                <p className="font-semibold text-foreground">
                                    {item.name}
                                </p>
                                <p className="text-sm text-muted-foreground">
                                    Số lượng: {item.quantity}
                                </p>
                            </div>
                            <div className="text-right">
                                <p className="font-semibold text-foreground">
                                    {item.price}
                                </p>
                            </div>
                        </div>
                    ))}
                </div>
            </div>

            {/* Order Summary & Actions */}
            <div className="grid grid-cols-1 gap-8 md:grid-cols-2">
                <div className="space-y-4">
                    <h3 className="text-lg font-bold text-foreground">
                        Tổng kết đơn hàng
                    </h3>
                    <div className="space-y-2 text-sm">
                        <div className="flex justify-between">
                            <span className="text-muted-foreground">
                                Tổng tiền sản phẩm:
                            </span>
                            <span className="font-medium text-foreground">
                                {order.subtotal}
                            </span>
                        </div>
                        <div className="flex justify-between">
                            <span className="text-muted-foreground">
                                Phí vận chuyển:
                            </span>
                            <span className="font-medium text-foreground">
                                {order.shippingFee}
                            </span>
                        </div>
                        <div className="flex justify-between border-t border-border pt-2 text-base">
                            <span className="font-bold text-foreground">
                                Tổng cộng:
                            </span>
                            <span className="text-lg font-bold text-primary">
                                {order.total}
                            </span>
                        </div>
                    </div>
                </div>
                <div className="flex flex-col justify-end gap-4 md:items-end">
                    <div className="flex w-full flex-col gap-4 sm:flex-row md:w-auto">
                        <button
                            className="flex h-10 min-w-[84px] max-w-fit cursor-pointer items-center justify-center overflow-hidden rounded-lg border border-border bg-background px-4 text-sm font-bold leading-normal tracking-[0.015em] text-foreground transition-colors hover:bg-card"
                            type="button"
                        >
                            <span className="truncate">Liên hệ hỗ trợ</span>
                        </button>
                        <button
                            className="flex h-10 min-w-[84px] max-w-fit cursor-pointer items-center justify-center overflow-hidden rounded-lg bg-primary px-4 text-sm font-bold leading-normal tracking-[0.015em] text-white transition-colors hover:bg-primary/90"
                            type="button"
                        >
                            <span className="truncate">In hóa đơn</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );
}

export default function CustomerOrderHistoryDetail() {
    return (
        <CustomerLayout activePage="orders">
            <CustomerOrderHistoryDetailContent />
        </CustomerLayout>
    );
}
