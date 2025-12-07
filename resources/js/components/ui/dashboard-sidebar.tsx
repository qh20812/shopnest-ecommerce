import React from 'react';
import { Link } from '@inertiajs/react';
import { LogOut } from 'lucide-react';

interface DashboardSidebarProps {
    activePage?: string;
    menuItems: Array<{
        label: string;
        icon: React.ReactNode;
        href: string;
        key: string;
    }>;
}

export default function DashboardSidebar({ activePage = 'dashboard', menuItems }: DashboardSidebarProps) {
    return (
        <aside className="w-64 flex-shrink-0 bg-surface-light dark:bg-surface-dark border-r border-border-light dark:border-border-dark flex flex-col">
            {/* Logo */}
            <div className="flex items-center gap-2 h-20 border-b border-border-light dark:border-border-dark px-6">
                <img src="/ShopNest3.png" alt="ShopNest Logo" className='h-12 w-auto' />
                <h1 className="text-2xl font-bold leading-tight tracking-[-0.015em] text-text-primary-light dark:text-text-primary-dark">
                    ShopNest
                </h1>
            </div>

            {/* Navigation Menu */}
            <nav className="flex-1 px-4 py-6 space-y-2">
                {menuItems.map((item) => (
                    <Link
                        key={item.key}
                        href={item.href}
                        className={`flex items-center gap-3 px-4 py-2.5 rounded-lg font-medium transition-colors ${activePage === item.key
                                ? 'bg-primary/10 text-primary font-semibold'
                                : 'text-text-secondary-light dark:text-text-secondary-dark hover:bg-black/5 dark:hover:bg-white/5 hover:text-text-primary-light dark:hover:text-text-primary-dark'
                            }`}
                    >
                        {activePage === item.key ? (
                            <span className="w-6 h-6 flex items-center justify-center">{item.icon}</span>
                        ) : (
                            <span className="w-6 h-6 flex items-center justify-center">{item.icon}</span>
                        )}
                        <span>{item.label}</span>
                    </Link>
                ))}
            </nav>

            {/* Logout */}
            <div className="px-4 py-6 border-t border-border-light dark:border-border-dark">
                <Link
                    href="/logout"
                    method="post"
                    as="button"
                    className="w-full flex items-center gap-3 px-4 py-2.5 rounded-lg text-text-secondary-light dark:text-text-secondary-dark hover:bg-black/5 dark:hover:bg-white/5 hover:text-text-primary-light dark:hover:text-text-primary-dark transition-colors font-medium"
                >
                    <LogOut className="w-6 h-6" />
                    <span>Đăng xuất</span>
                </Link>
            </div>
        </aside>
    );
}
