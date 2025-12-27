import React, { useState } from 'react';
import SellerLayout from '../../../../layouts/seller-layout';
import { Eye } from 'lucide-react';
import { router } from '@inertiajs/react';
import { index as ordersIndexRoute, show as ordersShowRoute } from '../../../../routes/seller/orders';
import DataTable, { Column, Action } from '../../../../components/datatable/DataTable';
import Filters, { FilterOption } from '../../../../components/datatable/Filters';

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
  [key: string]: unknown;
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

  // Define columns for DataTable
  const columns: Column<Order>[] = [
    {
      header: 'Mã đơn hàng',
      accessor: 'id',
      className: 'px-6 py-4 whitespace-nowrap',
      headerClassName: 'px-6 py-3 text-xs font-medium text-text-secondary-light dark:text-text-secondary-dark uppercase tracking-wider',
    },
    {
      header: 'Khách hàng',
      accessor: 'customer',
      className: 'px-6 py-4 whitespace-nowrap',
      headerClassName: 'px-6 py-3 text-xs font-medium text-text-secondary-light dark:text-text-secondary-dark uppercase tracking-wider',
    },
    {
      header: 'Sản phẩm',
      accessor: (row) => (
        <span className="text-sm text-text-secondary-light dark:text-text-secondary-dark line-clamp-2">
          {row.products}
        </span>
      ),
      className: 'px-6 py-4',
      headerClassName: 'px-6 py-3 text-xs font-medium text-text-secondary-light dark:text-text-secondary-dark uppercase tracking-wider',
    },
    {
      header: 'Tổng tiền',
      accessor: (row) => (
        <span className="text-sm font-semibold text-text-primary-light dark:text-text-primary-dark">
          {row.total}
        </span>
      ),
      className: 'px-6 py-4 whitespace-nowrap',
      headerClassName: 'px-6 py-3 text-xs font-medium text-text-secondary-light dark:text-text-secondary-dark uppercase tracking-wider',
    },
    {
      header: 'Thanh toán',
      accessor: 'payment_method',
      className: 'px-6 py-4 whitespace-nowrap',
      headerClassName: 'px-6 py-3 text-xs font-medium text-text-secondary-light dark:text-text-secondary-dark uppercase tracking-wider',
    },
    {
      header: 'Trạng thái',
      accessor: (row) => getStatusBadge(row.status),
      className: 'px-6 py-4 whitespace-nowrap',
      headerClassName: 'px-6 py-3 text-xs font-medium text-text-secondary-light dark:text-text-secondary-dark uppercase tracking-wider',
    },
    {
      header: 'Ngày đặt',
      accessor: 'date',
      className: 'px-6 py-4 whitespace-nowrap',
      headerClassName: 'px-6 py-3 text-xs font-medium text-text-secondary-light dark:text-text-secondary-dark uppercase tracking-wider',
    },
  ];

  // Define actions for DataTable
  const actions: Action<Order>[] = [
    {
      icon: Eye,
      onClick: (row) => handleViewOrder(row.actual_id),
      title: 'Xem chi tiết',
      variant: 'primary',
    },
  ];

  // Define filter options
  const statusOptions: FilterOption[] = [
    { value: 'all', label: 'Tất cả trạng thái' },
    { value: 'pending', label: 'Chờ xác nhận' },
    { value: 'confirmed', label: 'Đã xác nhận' },
    { value: 'processing', label: 'Đang xử lý' },
    { value: 'shipping', label: 'Đang giao hàng' },
    { value: 'delivered', label: 'Đã giao hàng' },
    { value: 'cancelled', label: 'Đã hủy' },
  ];

  return (
    <SellerLayout activePage="orders" user={user}>
      <div className="container mx-auto px-6 py-8">
        {/* Header */}
        <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6">
          <h2 className="text-3xl font-bold text-text-primary-light dark:text-text-primary-dark">
            Quản lý đơn hàng
          </h2>
        </div>

        <div className="bg-surface-light dark:bg-surface-dark p-6 rounded-xl border border-border-light dark:border-border-dark">
          {/* Filters */}
          <Filters
            searchValue={searchQuery}
            onSearchChange={setSearchQuery}
            onSearch={applyFilters}
            searchPlaceholder="Tìm kiếm đơn hàng..."
            filters={[
              {
                value: selectedStatus,
                onChange: (value) => {
                  setSelectedStatus(value);
                  router.get(
                    ordersIndexRoute.url(),
                    { search: searchQuery, status: value, date: selectedDate },
                    { preserveState: true, preserveScroll: true }
                  );
                },
                options: statusOptions,
              },
            ]}
          />

          {/* Date Filter - Custom addition */}
          <div className="mb-6">
            <input
              type="date"
              value={selectedDate}
              onChange={(e) => {
                setSelectedDate(e.target.value);
                router.get(
                  ordersIndexRoute.url(),
                  { search: searchQuery, status: selectedStatus, date: e.target.value },
                  { preserveState: true, preserveScroll: true }
                );
              }}
              className="form-input h-10 px-3 rounded-lg bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark focus:ring-2 focus:ring-primary/50 focus:border-primary/50 w-full sm:w-48"
            />
          </div>

          {/* Orders DataTable */}
          <DataTable<Order>
            columns={columns}
            data={orders}
            pagination={ordersData}
            actions={actions}
            emptyMessage="Không có đơn hàng nào"
            onPageChange={(url) =>
              router.visit(url, { preserveState: true, preserveScroll: true })
            }
          />
        </div>
      </div>
    </SellerLayout>
  );
}
