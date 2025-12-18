import React, { useState } from 'react';
import SellerLayout from '../../../../layouts/seller-layout';
import { Search, Eye, Package, Truck, X } from 'lucide-react';
import { router } from '@inertiajs/react';
import { index as ordersIndexRoute, show as ordersShowRoute } from '../../../../routes/seller/orders';

interface IndexProps {
  user?: {
    full_name: string;
    email: string;
    avatar_url?: string;
  };
  orders: {
    data: Order[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number;
    to: number;
  };
  filters: {
    search: string;
    status: string;
    date: string;
  };
}

interface Order {
  id: string;
  actual_id: number;
  customer: string;
  products: string;
  total: string;
  payment_method: string;
  status: 'pending' | 'confirmed' | 'processing' | 'shipping' | 'delivered' | 'cancelled';
  date: string;
}

export default function Index({ user, orders: ordersData, filters: initialFilters }: IndexProps) {
  const [searchQuery, setSearchQuery] = useState(initialFilters.search || '');
  const [selectedStatus, setSelectedStatus] = useState(initialFilters.status || 'all');
  const [selectedDate, setSelectedDate] = useState(initialFilters.date || '');

  const orders = ordersData?.data || [];

  const applyFilters = () => {
    router.get(ordersIndexRoute.url(), {
      search: searchQuery,
      status: selectedStatus,
      date: selectedDate,
    }, {
      preserveState: true,
      preserveScroll: true,
    });
  };

  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault();
    applyFilters();
  };

  const handleViewOrder = (orderId: number) => {
    router.visit(ordersShowRoute.url({ order: orderId }));
  };

  const getStatusBadge = (status: Order['status']) => {
    const statusConfig = {
      pending: {
        label: 'Chờ xác nhận',
        className: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
      },
      confirmed: {
        label: 'Đã xác nhận',
        className: 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
      },
      processing: {
        label: 'Đang xử lý',
        className: 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
      },
      shipping: {
        label: 'Đang giao hàng',
        className: 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400',
      },
      delivered: {
        label: 'Đã giao hàng',
        className: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
      },
      cancelled: {
        label: 'Đã hủy',
        className: 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
      },
    };

    const config = statusConfig[status] || statusConfig.pending;
    return (
      <span className={`px-2.5 py-1 rounded-full text-xs font-medium ${config.className}`}>
        {config.label}
      </span>
    );
  };

  return (
    <SellerLayout activePage="orders" user={user}>
      <div className="container mx-auto px-6 py-8">
        <div className="bg-surface-light dark:bg-surface-dark p-6 rounded-xl border border-border-light dark:border-border-dark">
          {/* Filters */}
          <form onSubmit={handleSearch} className="flex flex-col md:flex-row items-center justify-between gap-4 mb-6">
            {/* Search */}
            <div className="relative w-full md:w-auto md:flex-1 max-w-md">
              <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-text-secondary-light dark:text-text-secondary-dark" />
              <input
                type="text"
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                onKeyDown={(e) => e.key === 'Enter' && applyFilters()}
                className="form-input w-full h-10 pl-10 pr-3 rounded-lg bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark focus:ring-2 focus:ring-primary/50 focus:border-primary/50"
                placeholder="Tìm kiếm đơn hàng..."
              />
            </div>

            {/* Filters */}
            <div className="flex items-center gap-4 w-full md:w-auto">
              {/* Status Filter */}
              <div className="relative w-full sm:w-48">
                <select
                  value={selectedStatus}
                  onChange={(e) => {
                    setSelectedStatus(e.target.value);
                    router.get(ordersIndexRoute.url(), { search: searchQuery, status: e.target.value, date: selectedDate }, { preserveState: true, preserveScroll: true });
                  }}
                  className="form-select w-full h-10 px-3 pr-8 rounded-lg bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark focus:ring-2 focus:ring-primary/50 focus:border-primary/50 appearance-none"
                >
                  <option value="all">Tất cả trạng thái</option>
                  <option value="pending">Chờ xác nhận</option>
                  <option value="confirmed">Đã xác nhận</option>
                  <option value="processing">Đang xử lý</option>
                  <option value="shipping">Đang giao hàng</option>
                  <option value="delivered">Đã giao hàng</option>
                  <option value="cancelled">Đã hủy</option>
                </select>
                <svg
                  className="absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none w-5 h-5 text-text-secondary-light dark:text-text-secondary-dark"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" />
                </svg>
              </div>

              {/* Date Filter */}
              <input
                type="date"
                value={selectedDate}
                onChange={(e) => {
                  setSelectedDate(e.target.value);
                  router.get(ordersIndexRoute.url(), { search: searchQuery, status: selectedStatus, date: e.target.value }, { preserveState: true, preserveScroll: true });
                }}
                className="form-input h-10 px-3 rounded-lg bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark focus:ring-2 focus:ring-primary/50 focus:border-primary/50 w-full sm:w-48"
              />
            </div>
          </form>

          {/* Orders Table */}
          <div className="overflow-x-auto">
            <table className="w-full text-left">
              <thead>
                <tr className="bg-background-light dark:bg-background-dark border-b border-border-light dark:border-border-dark">
                  <th className="px-6 py-3 text-xs font-medium text-text-secondary-light dark:text-text-secondary-dark uppercase tracking-wider">
                    Mã đơn hàng
                  </th>
                  <th className="px-6 py-3 text-xs font-medium text-text-secondary-light dark:text-text-secondary-dark uppercase tracking-wider">
                    Khách hàng
                  </th>
                  <th className="px-6 py-3 text-xs font-medium text-text-secondary-light dark:text-text-secondary-dark uppercase tracking-wider">
                    Sản phẩm
                  </th>
                  <th className="px-6 py-3 text-xs font-medium text-text-secondary-light dark:text-text-secondary-dark uppercase tracking-wider">
                    Tổng tiền
                  </th>
                  <th className="px-6 py-3 text-xs font-medium text-text-secondary-light dark:text-text-secondary-dark uppercase tracking-wider">
                    Thanh toán
                  </th>
                  <th className="px-6 py-3 text-xs font-medium text-text-secondary-light dark:text-text-secondary-dark uppercase tracking-wider">
                    Trạng thái
                  </th>
                  <th className="px-6 py-3 text-xs font-medium text-text-secondary-light dark:text-text-secondary-dark uppercase tracking-wider">
                    Ngày đặt
                  </th>
                  <th className="px-6 py-3 text-xs font-medium text-text-secondary-light dark:text-text-secondary-dark uppercase tracking-wider text-center">
                    Hành động
                  </th>
                </tr>
              </thead>
              <tbody className="divide-y divide-border-light dark:divide-border-dark">
                {orders.map((order) => (
                  <tr
                    key={order.id}
                    className="hover:bg-background-light dark:hover:bg-background-dark transition-colors"
                  >
                    <td className="px-6 py-4 whitespace-nowrap">
                      <span className="text-sm font-medium text-text-primary-light dark:text-text-primary-dark">
                        {order.id}
                      </span>
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap">
                      <span className="text-sm text-text-primary-light dark:text-text-primary-dark">
                        {order.customer}
                      </span>
                    </td>
                    <td className="px-6 py-4">
                      <span className="text-sm text-text-secondary-light dark:text-text-secondary-dark line-clamp-2">
                        {order.products}
                      </span>
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap">
                      <span className="text-sm font-semibold text-text-primary-light dark:text-text-primary-dark">
                        {order.total}
                      </span>
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap">
                      <span className="text-sm text-text-secondary-light dark:text-text-secondary-dark">
                        {order.payment_method}
                      </span>
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap">{getStatusBadge(order.status)}</td>
                    <td className="px-6 py-4 whitespace-nowrap">
                      <span className="text-sm text-text-secondary-light dark:text-text-secondary-dark">
                        {order.date}
                      </span>
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap text-center">
                      <div className="flex items-center justify-center gap-2">
                        <button 
                          onClick={() => handleViewOrder(order.actual_id)}
                          className="p-2 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors group"
                          title="Xem chi tiết"
                        >
                          <Eye className="w-4 h-4 text-text-secondary-light dark:text-text-secondary-dark group-hover:text-blue-500" />
                        </button>
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>

          {/* Pagination */}
          {ordersData && ordersData.last_page > 1 && (
            <div className="flex items-center justify-between mt-6">
              <p className="text-sm text-text-secondary-light dark:text-text-secondary-dark">
                Hiển thị {ordersData.from || 0}-{ordersData.to || 0} trên {ordersData.total || 0} đơn hàng
              </p>
              <div className="flex items-center gap-2">
                <button
                  disabled={ordersData.current_page === 1}
                  onClick={() => router.get(ordersIndexRoute.url(), { ...initialFilters, page: ordersData.current_page - 1 }, { preserveState: true })}
                  className="flex items-center justify-center h-8 w-8 rounded-lg text-text-secondary-light dark:text-text-secondary-dark bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark hover:bg-black/5 dark:hover:bg-white/5 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
                  </svg>
                </button>
                {Array.from({ length: Math.min(5, ordersData.last_page) }, (_, i) => i + 1).map((page) => (
                  <button
                    key={page}
                    onClick={() => router.get(ordersIndexRoute.url(), { ...initialFilters, page }, { preserveState: true })}
                    className={`flex items-center justify-center h-8 w-8 rounded-lg font-semibold text-sm ${
                      page === ordersData.current_page
                        ? 'bg-primary text-white'
                        : 'text-text-secondary-light dark:text-text-secondary-dark hover:bg-black/5 dark:hover:bg-white/5'
                    }`}
                  >
                    {page}
                  </button>
                ))}
                {ordersData.last_page > 5 && <span className="text-text-secondary-light dark:text-text-secondary-dark">...</span>}
                <button
                  disabled={ordersData.current_page === ordersData.last_page}
                  onClick={() => router.get(ordersIndexRoute.url(), { ...initialFilters, page: ordersData.current_page + 1 }, { preserveState: true })}
                  className="flex items-center justify-center h-8 w-8 rounded-lg text-text-secondary-light dark:text-text-secondary-dark bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark hover:bg-black/5 dark:hover:bg-white/5 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                  </svg>
                </button>
              </div>
            </div>
          )}
        </div>
      </div>
    </SellerLayout>
  );
}
