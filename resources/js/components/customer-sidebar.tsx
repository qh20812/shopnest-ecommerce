import { User, Package, Bell } from 'lucide-react';
import { Link } from '@inertiajs/react';

interface CustomerSidebarProps {
    activePage?: 'profile' | 'orders' | 'notifications';
}

export default function CustomerSidebar({ activePage = 'profile' }: CustomerSidebarProps) {
    const menuItems = [
        {
            icon: User,
            label: 'Thông tin cá nhân',
            href: '#',
            active: activePage === 'profile',
        },
        {
            icon: Package,
            label: 'Đơn hàng của tôi',
            href: '#',
            active: activePage === 'orders',
        },
        {
            icon: Bell,
            label: 'Thông báo',
            href: '#',
            active: activePage === 'notifications',
        },
    ];

    return (
        <aside className="w-full flex-shrink-0 md:w-64 lg:w-72">
            <div className="space-y-2 rounded-xl bg-background p-4 shadow-md">
                {menuItems.map((item, index) => (
                    <Link
                        key={index}
                        href={item.href}
                        className={`flex items-center gap-4 rounded-lg p-3 font-medium transition-colors ${
                            item.active
                                ? 'bg-primary/10 font-bold text-primary'
                                : 'text-muted-foreground hover:bg-card hover:text-foreground'
                        }`}
                    >
                        <item.icon className="h-5 w-5" />
                        <span>{item.label}</span>
                    </Link>
                ))}
            </div>
        </aside>
    );
}
