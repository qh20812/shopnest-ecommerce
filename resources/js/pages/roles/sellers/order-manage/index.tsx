import React, { useState } from 'react';
import SellerLayout from '../../../../layouts/seller-layout';
import { Search, Eye, Package, Truck, X } from 'lucide-react';

interface IndexProps {
  user?: {
    full_name: string;
    email: string;
    avatar_url?: string;
  };
}

interface Order {
  id: string;
  customer: string;
  products: string;
  total: string;
  payment_method: string;
  status: 'processing' | 'shipped' | 'delivered' | 'cancelled';
  date: string;
}

export default function Index({ user }: IndexProps) {
  const [searchQuery, setSearchQuery] = useState('');
  const [selectedStatus, setSelectedStatus] = useState('all');
  const [selectedDate, setSelectedDate] = useState('');

  // Sample order data
  const orders: Order[] = [
    {
      id: '#ORD-2024-0001',
      customer: 'Nguyễn Văn A',
      products: 'Tai nghe không dây, Đồng hồ thông minh',
      total: '3,740,000đ',
      payment_method: 'Thẻ tín dụng',
      status: 'processing',
      date: '2024-12-07',
    },
    {
      id: '#ORD-2024-0002',
      customer: 'Trần Thị B',
      products: 'Giày thể thao nam',
      total: '890,000đ',
      payment_method: 'COD',
      status: 'shipped',
      date: '2024-12-06',
    },
    {
      id: '#ORD-2024-0003',
      customer: 'Lê Văn C',
      products: 'Balo laptop, Bình giữ nhiệt',
      total: '770,000đ',
      payment_method: 'Chuyển khoản',
      status: 'delivered',
      date: '2024-12-05',
    },
    {
      id: '#ORD-2024-0004',
      customer: 'Phạm Thị D',
      products: 'Tai nghe không dây',
      total: '1,250,000đ',
      payment_method: 'Ví điện tử',
      status: 'processing',
      date: '2024-12-07',
    },
    {
      id: '#ORD-2024-0005',
      customer: 'Hoàng Văn E',
      products: 'Đồng hồ thông minh, Balo laptop',
      total: '2,940,000đ',
      payment_method: 'Thẻ tín dụng',
      status: 'cancelled',
      date: '2024-12-04',
    },
  ];

  const getStatusBadge = (status: Order['status']) => {
    const statusConfig = {
      processing: {
        label: 'Đang xử lý',
        className: 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
      },
      shipped: {
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

    const config = statusConfig[status];
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
          <div className="flex flex-col md:flex-row items-center justify-between gap-4 mb-6">
            {/* Search */}
            <div className="relative w-full md:w-auto md:flex-1 max-w-md">
              <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-text-secondary-light dark:text-text-secondary-dark" />
              <input
                type="text"
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
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
                  onChange={(e) => setSelectedStatus(e.target.value)}
                  className="form-select w-full h-10 px-3 pr-8 rounded-lg bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark focus:ring-2 focus:ring-primary/50 focus:border-primary/50 appearance-none"
                >
                  <option value="all">Tất cả trạng thái</option>
                  <option value="processing">Đang xử lý</option>
                  <option value="shipped">Đang giao hàng</option>
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
                onChange={(e) => setSelectedDate(e.target.value)}
                className="form-input h-10 px-3 rounded-lg bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark focus:ring-2 focus:ring-primary/50 focus:border-primary/50 w-full sm:w-48"
              />
            </div>
          </div>

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
                        <button className="p-2 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors group">
                          <Eye className="w-4 h-4 text-text-secondary-light dark:text-text-secondary-dark group-hover:text-blue-500" />
                        </button>
                        <button className="p-2 hover:bg-green-50 dark:hover:bg-green-900/20 rounded-lg transition-colors group">
                          <Package className="w-4 h-4 text-text-secondary-light dark:text-text-secondary-dark group-hover:text-green-500" />
                        </button>
                        <button className="p-2 hover:bg-purple-50 dark:hover:bg-purple-900/20 rounded-lg transition-colors group">
                          <Truck className="w-4 h-4 text-text-secondary-light dark:text-text-secondary-dark group-hover:text-purple-500" />
                        </button>
                        <button className="p-2 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors group">
                          <X className="w-4 h-4 text-text-secondary-light dark:text-text-secondary-dark group-hover:text-red-500" />
                        </button>
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>

          {/* Pagination */}
          <div className="flex items-center justify-between mt-6">
            <p className="text-sm text-text-secondary-light dark:text-text-secondary-dark">
              Hiển thị 1-5 trên 50 đơn hàng
            </p>
            <div className="flex items-center gap-2">
              <button
                disabled
                className="flex items-center justify-center h-8 w-8 rounded-lg text-text-secondary-light dark:text-text-secondary-dark bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark hover:bg-black/5 dark:hover:bg-white/5 transition-colors disabled:opacity-50"
              >
                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
                </svg>
              </button>
              <button className="flex items-center justify-center h-8 w-8 rounded-lg font-semibold text-sm bg-primary text-white">
                1
              </button>
              <button className="flex items-center justify-center h-8 w-8 rounded-lg font-semibold text-sm text-text-secondary-light dark:text-text-secondary-dark hover:bg-black/5 dark:hover:bg-white/5">
                2
              </button>
              <button className="flex items-center justify-center h-8 w-8 rounded-lg font-semibold text-sm text-text-secondary-light dark:text-text-secondary-dark hover:bg-black/5 dark:hover:bg-white/5">
                3
              </button>
              <span className="text-text-secondary-light dark:text-text-secondary-dark">...</span>
              <button className="flex items-center justify-center h-8 w-8 rounded-lg font-semibold text-sm text-text-secondary-light dark:text-text-secondary-dark hover:bg-black/5 dark:hover:bg-white/5">
                10
              </button>
              <button className="flex items-center justify-center h-8 w-8 rounded-lg text-text-secondary-light dark:text-text-secondary-dark bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark hover:bg-black/5 dark:hover:bg-white/5 transition-colors">
                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </SellerLayout>
  );
}
