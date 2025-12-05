import { useState } from 'react';
import CustomerLayout from '../layouts/customer-layout';

type OrderStatus = 'all' | 'processing' | 'delivered' | 'cancelled';

interface Order {
    id: string;
    orderNumber: string;
    orderDate: string;
    total: string;
    status: 'delivered' | 'processing' | 'cancelled';
    statusLabel: string;
}

function CustomerOrderHistoryContent() {
    const [activeFilter, setActiveFilter] = useState<OrderStatus>('all');

    // Sample orders data
    const orders: Order[] = [
        {
            id: '1',
            orderNumber: '#SN123456',
            orderDate: '15/07/2024',
            total: '1.250.000₫',
            status: 'delivered',
            statusLabel: 'Đã giao hàng',
        },
        {
            id: '2',
            orderNumber: '#SN123455',
            orderDate: '12/07/2024',
            total: '780.000₫',
            status: 'processing',
            statusLabel: 'Đang xử lý',
        },
        {
            id: '3',
            orderNumber: '#SN123450',
            orderDate: '05/07/2024',
            total: '350.000₫',
            status: 'cancelled',
            statusLabel: 'Đã hủy',
        },
    ];

    const filterButtons: { label: string; value: OrderStatus }[] = [
        { label: 'Tất cả', value: 'all' },
        { label: 'Đang xử lý', value: 'processing' },
        { label: 'Đã giao hàng', value: 'delivered' },
        { label: 'Đã hủy', value: 'cancelled' },
    ];

    const getStatusStyles = (status: Order['status']) => {
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

    const filteredOrders =
        activeFilter === 'all'
            ? orders
            : orders.filter((order) => order.status === activeFilter);

    return (
        <div className="rounded-xl bg-background p-6 shadow-md md:p-8">
                <h2 className="mb-2 text-2xl font-bold text-foreground md:text-3xl">
                    Đơn hàng của tôi
                </h2>
                <p className="mb-8 text-muted-foreground">
                    Theo dõi và quản lý lịch sử mua sắm của bạn tại đây.
                </p>

                {/* Filter Buttons */}
                <div className="mb-6 flex flex-wrap gap-2 border-b border-border pb-4">
                    {filterButtons.map((button) => (
                        <button
                            key={button.value}
                            onClick={() => setActiveFilter(button.value)}
                            className={`rounded-full px-4 py-2 text-sm font-semibold transition-colors ${
                                activeFilter === button.value
                                    ? 'bg-primary text-primary-foreground'
                                    : 'bg-card text-muted-foreground hover:bg-primary/10 hover:text-primary'
                            }`}
                        >
                            {button.label}
                        </button>
                    ))}
                </div>

                {/* Orders List */}
                <div className="space-y-6">
                    {filteredOrders.map((order) => (
                        <div
                            key={order.id}
                            className="rounded-lg bg-card/50 p-4"
                        >
                            <div className="mb-4 flex flex-col items-start justify-between border-b border-border pb-4 sm:flex-row sm:items-center">
                                <div>
                                    <p className="font-bold text-foreground">
                                        Mã đơn hàng: {order.orderNumber}
                                    </p>
                                    <p className="text-sm text-muted-foreground">
                                        Ngày đặt hàng: {order.orderDate}
                                    </p>
                                </div>
                                <div
                                    className={`mt-2 rounded-full px-2 py-1 text-xs font-medium sm:mt-0 ${getStatusStyles(order.status)}`}
                                >
                                    {order.statusLabel}
                                </div>
                            </div>
                            <div className="flex flex-col items-start justify-between sm:flex-row sm:items-end">
                                <div>
                                    <p className="text-sm text-muted-foreground">
                                        Tổng cộng
                                    </p>
                                    <p className="text-lg font-bold text-foreground">
                                        {order.total}
                                    </p>
                                </div>
                                <button
                                    className={`mt-4 flex h-10 min-w-[84px] max-w-fit cursor-pointer items-center justify-center overflow-hidden rounded-lg px-4 text-sm font-bold leading-normal tracking-[0.015em] transition-colors sm:mt-0 ${
                                        order.status === 'cancelled'
                                            ? 'border border-border bg-background text-foreground hover:bg-card'
                                            : 'bg-primary text-primary-foreground hover:bg-primary/90'
                                    }`}
                                    type="button"
                                >
                                    <span className="truncate">Xem chi tiết</span>
                                </button>
                            </div>
                        </div>
                    ))}
                </div>
            </div>
    );
}

export default function CustomerOrderHistory() {
    return (
        <CustomerLayout activePage="orders">
            <CustomerOrderHistoryContent />
        </CustomerLayout>
    );
}
