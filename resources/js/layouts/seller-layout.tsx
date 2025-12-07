import React from 'react';
import DashboardSidebar from '../components/ui/dashboard-sidebar';
import DashboardNavbar from '../components/ui/dashboard-navbar';
import { LayoutDashboard, Package, ShoppingCart, BarChart3, Settings, Store, Tag } from 'lucide-react';

interface SellerLayoutProps {
  children: React.ReactNode;
  activePage?: string;
  user?: {
    full_name: string;
    email: string;
    avatar_url?: string;
  };
}

export default function SellerLayout({ children, activePage = 'dashboard', user }: SellerLayoutProps) {
  const menuItems = [
    {
      label: 'Tổng quan',
      icon: <LayoutDashboard className="w-5 h-5" />,
      href: '/seller/dashboard',
      key: 'dashboard',
    },
    {
      label: 'Quản lý sản phẩm',
      icon: <Package className="w-5 h-5" />,
      href: '/seller/products',
      key: 'products',
    },
    {
      label: 'Quản lý đơn hàng',
      icon: <ShoppingCart className="w-5 h-5" />,
      href: '/seller/orders',
      key: 'orders',
    },
    {
      label: 'Quản lý ưu đãi',
      icon: <Tag className="w-5 h-5" />,
      href: '/seller/promotions',
      key: 'promotions',
    },
    {
      label: 'Thống kê doanh số',
      icon: <BarChart3 className="w-5 h-5" />,
      href: '/seller/statistics',
      key: 'statistics',
    },
    {
      label: 'Cài đặt cửa hàng',
      icon: <Store className="w-5 h-5" />,
      href: '/seller/shop-settings',
      key: 'shop-settings',
    },
    {
      label: 'Cài đặt tài khoản',
      icon: <Settings className="w-5 h-5" />,
      href: '/seller/settings',
      key: 'settings',
    },
  ];

  return (
    <div className="flex h-screen w-full">
      <DashboardSidebar activePage={activePage} menuItems={menuItems} />
      <div className="flex-1 flex flex-col overflow-hidden">
        <DashboardNavbar user={user} />
        <main className="flex-1 overflow-y-auto">
          {children}
        </main>
      </div>
    </div>
  );
}
