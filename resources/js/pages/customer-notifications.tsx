import { useState } from 'react';
import { Truck, Tag, Clock, Info, Trash2 } from 'lucide-react';
import CustomerLayout from '../layouts/customer-layout';

type NotificationFilter = 'all' | 'unread';

interface Notification {
    id: string;
    type: 'shipping' | 'promotion' | 'order' | 'info';
    title: string;
    message: string;
    time: string;
    isUnread: boolean;
}

function CustomerNotificationsContent() {
    const [activeFilter, setActiveFilter] = useState<NotificationFilter>('all');

    // Sample notifications data
    const notifications: Notification[] = [
        {
            id: '1',
            type: 'shipping',
            title: 'Đơn hàng #SN123456 đã được giao',
            message: 'Cảm ơn bạn đã mua sắm tại ShopNest! Hy vọng bạn hài lòng với sản phẩm.',
            time: '2 giờ trước',
            isUnread: true,
        },
        {
            id: '2',
            type: 'promotion',
            title: 'Ưu đãi giữa tháng đang diễn ra!',
            message: 'Giảm giá lên đến 50% cho các mặt hàng điện tử. Đừng bỏ lỡ!',
            time: '1 ngày trước',
            isUnread: false,
        },
        {
            id: '3',
            type: 'order',
            title: 'Đơn hàng #SN123455 đang được xử lý',
            message: 'Đơn hàng của bạn đang được chuẩn bị và sẽ sớm được giao đi.',
            time: '3 ngày trước',
            isUnread: true,
        },
        {
            id: '4',
            type: 'info',
            title: 'Cập nhật chính sách bảo mật',
            message: 'Chúng tôi đã cập nhật chính sách bảo mật để phục vụ bạn tốt hơn.',
            time: '1 tuần trước',
            isUnread: false,
        },
    ];

    const getNotificationIcon = (type: Notification['type']) => {
        switch (type) {
            case 'shipping':
                return <Truck className="h-6 w-6" />;
            case 'promotion':
                return <Tag className="h-6 w-6" />;
            case 'order':
                return <Clock className="h-6 w-6" />;
            case 'info':
                return <Info className="h-6 w-6" />;
            default:
                return <Info className="h-6 w-6" />;
        }
    };

    const getNotificationIconColor = (type: Notification['type']) => {
        switch (type) {
            case 'shipping':
            case 'order':
                return 'text-primary';
            case 'promotion':
                return 'text-secondary';
            case 'info':
                return 'text-muted-foreground';
            default:
                return 'text-muted-foreground';
        }
    };

    const filteredNotifications =
        activeFilter === 'all'
            ? notifications
            : notifications.filter((notification) => notification.isUnread);

    return (
        <div className="rounded-xl bg-background p-6 shadow-md md:p-8">
            <h2 className="mb-2 text-2xl font-bold text-foreground md:text-3xl">Thông báo</h2>
            <p className="mb-8 text-muted-foreground">
                Kiểm tra các cập nhật quan trọng về tài khoản và đơn hàng của bạn.
            </p>

            {/* Filter Buttons */}
            <div className="mb-6 flex flex-wrap gap-2 border-b border-border pb-4">
                <button
                    onClick={() => setActiveFilter('all')}
                    className={`rounded-full px-4 py-2 text-sm font-semibold transition-colors ${
                        activeFilter === 'all'
                            ? 'bg-primary text-primary-foreground'
                            : 'bg-card text-muted-foreground hover:bg-primary/10 hover:text-primary'
                    }`}
                >
                    Tất cả
                </button>
                <button
                    onClick={() => setActiveFilter('unread')}
                    className={`rounded-full px-4 py-2 text-sm font-semibold transition-colors ${
                        activeFilter === 'unread'
                            ? 'bg-primary text-primary-foreground'
                            : 'bg-card text-muted-foreground hover:bg-primary/10 hover:text-primary'
                    }`}
                >
                    Chưa đọc
                </button>
                <button className="flex items-center gap-1.5 rounded-full px-4 py-2 text-sm font-semibold text-muted-foreground transition-colors hover:bg-primary/10 hover:text-primary">
                    <Trash2 className="h-4 w-4" /> Xóa tất cả
                </button>
            </div>

            {/* Notifications List */}
            <div className="space-y-4">
                {filteredNotifications.map((notification) => (
                    <div
                        key={notification.id}
                        className={`flex items-start gap-4 rounded-lg bg-card/50 p-4 ${
                            notification.isUnread ? 'border-l-4 border-primary' : ''
                        }`}
                    >
                        <div className={`mt-1 ${getNotificationIconColor(notification.type)}`}>
                            {getNotificationIcon(notification.type)}
                        </div>
                        <div className="flex-1">
                            <div className="flex items-start justify-between">
                                <div>
                                    <h3
                                        className={`${
                                            notification.isUnread
                                                ? 'font-bold text-foreground'
                                                : 'font-medium text-muted-foreground'
                                        }`}
                                    >
                                        {notification.title}
                                    </h3>
                                    <p className="mt-1 text-sm text-muted-foreground">
                                        {notification.message}
                                    </p>
                                </div>
                                <span className="ml-4 whitespace-nowrap text-xs text-muted-foreground">
                                    {notification.time}
                                </span>
                            </div>
                        </div>
                        {notification.isUnread && (
                            <div
                                className="mt-1.5 h-3 w-3 flex-shrink-0 rounded-full bg-primary"
                                title="Chưa đọc"
                            />
                        )}
                    </div>
                ))}
            </div>
        </div>
    );
}

export default function CustomerNotifications() {
    return (
        <CustomerLayout activePage="notifications">
            <CustomerNotificationsContent />
        </CustomerLayout>
    );
}
