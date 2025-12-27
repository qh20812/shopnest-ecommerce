import React, { useState } from 'react';
import SellerLayout from '../../../../layouts/seller-layout';
import { Plus, Edit, Trash2 } from 'lucide-react';
import DataTable, { Column, Action } from '../../../../components/datatable/DataTable';
import Filters, { FilterOption } from '../../../../components/datatable/Filters';

interface IndexProps {
  user?: {
    full_name: string;
    email: string;
    avatar_url?: string;
  };
}

interface Voucher extends Record<string, unknown> {
  id: string;
  code: string;
  name: string;
  value: string;
  condition: string;
  start_date: string;
  end_date: string;
  status: 'active' | 'expired';
}

export default function Index({ user }: IndexProps) {
  const [searchQuery, setSearchQuery] = useState('');
  const [selectedStatus, setSelectedStatus] = useState('all');
  const [selectedDate, setSelectedDate] = useState('');

  // Sample voucher data
  const vouchers: Voucher[] = [
    {
      id: '1',
      code: 'SNOWSALE20',
      name: 'Giảm 20% mừng Giáng Sinh',
      value: '20%',
      condition: 'Đơn hàng tối thiểu 500.000đ',
      start_date: '01/12/2024',
      end_date: '25/12/2024',
      status: 'active',
    },
    {
      id: '2',
      code: 'FREESHIPMAX',
      name: 'Miễn phí vận chuyển tối đa 30K',
      value: '30.000đ',
      condition: 'Mọi đơn hàng',
      start_date: '15/11/2024',
      end_date: '30/11/2024',
      status: 'active',
    },
    {
      id: '3',
      code: 'BACK2SCHOOL',
      name: 'Giảm 50K tựu trường',
      value: '50.000đ',
      condition: 'Đơn hàng tối thiểu 300.000đ',
      start_date: '01/09/2024',
      end_date: '15/09/2024',
      status: 'expired',
    },
    {
      id: '4',
      code: 'FLASHDEAL',
      name: 'Giảm 100K Flash Sale',
      value: '100.000đ',
      condition: 'Đơn hàng tối thiểu 1.000.000đ',
      start_date: '20/10/2024',
      end_date: '25/10/2024',
      status: 'expired',
    },
  ];

  const handleAddVoucher = () => {
    console.log('Add new voucher');
  };

  const handleEditVoucher = (id: string) => {
    console.log('Edit voucher:', id);
  };

  const handleDeleteVoucher = (id: string) => {
    console.log('Delete voucher:', id);
  };

  const getStatusBadge = (status: Voucher['status']) => {
    if (status === 'active') {
      return (
        <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
          Đang hoạt động
        </span>
      );
    }
    return (
      <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300">
        Đã hết hạn
      </span>
    );
  };

  // Define columns for DataTable
  const columns: Column<Voucher>[] = [
    {
      header: 'Mã voucher',
      accessor: 'code',
      className: 'px-4 py-4 font-medium text-text-primary-light dark:text-text-primary-dark',
      headerClassName: 'px-4 py-3 font-semibold text-sm text-text-secondary-light dark:text-text-secondary-dark',
    },
    {
      header: 'Tên khuyến mãi',
      accessor: 'name',
      className: 'px-4 py-4 text-text-secondary-light dark:text-text-secondary-dark',
      headerClassName: 'px-4 py-3 font-semibold text-sm text-text-secondary-light dark:text-text-secondary-dark',
    },
    {
      header: 'Giá trị',
      accessor: 'value',
      className: 'px-4 py-4 text-text-secondary-light dark:text-text-secondary-dark',
      headerClassName: 'px-4 py-3 font-semibold text-sm text-text-secondary-light dark:text-text-secondary-dark',
    },
    {
      header: 'Điều kiện áp dụng',
      accessor: 'condition',
      className: 'px-4 py-4 text-text-secondary-light dark:text-text-secondary-dark',
      headerClassName: 'px-4 py-3 font-semibold text-sm text-text-secondary-light dark:text-text-secondary-dark',
    },
    {
      header: 'Ngày bắt đầu',
      accessor: 'start_date',
      className: 'px-4 py-4 text-text-secondary-light dark:text-text-secondary-dark',
      headerClassName: 'px-4 py-3 font-semibold text-sm text-text-secondary-light dark:text-text-secondary-dark',
    },
    {
      header: 'Ngày kết thúc',
      accessor: 'end_date',
      className: 'px-4 py-4 text-text-secondary-light dark:text-text-secondary-dark',
      headerClassName: 'px-4 py-3 font-semibold text-sm text-text-secondary-light dark:text-text-secondary-dark',
    },
    {
      header: 'Trạng thái',
      accessor: (row) => getStatusBadge(row.status),
      className: 'px-4 py-4 text-center',
      headerClassName: 'px-4 py-3 font-semibold text-sm text-text-secondary-light dark:text-text-secondary-dark text-center',
    },
  ];

  // Define actions for DataTable
  const actions: Action<Voucher>[] = [
    {
      icon: Edit,
      onClick: (row) => handleEditVoucher(row.id),
      title: 'Chỉnh sửa',
      variant: 'success',
    },
    {
      icon: Trash2,
      onClick: (row) => handleDeleteVoucher(row.id),
      title: 'Xóa',
      variant: 'danger',
    },
  ];

  // Define filter options
  const statusOptions: FilterOption[] = [
    { value: 'all', label: 'Tất cả trạng thái' },
    { value: 'active', label: 'Đang hoạt động' },
    { value: 'expired', label: 'Đã hết hạn' },
  ];

  return (
    <SellerLayout activePage="promotions" user={user}>
      <div className="container mx-auto px-6 py-8">
        {/* Header */}
        <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6">
          <h2 className="text-3xl font-bold text-text-primary-light dark:text-text-primary-dark">
            Quản lý khuyến mãi
          </h2>
          <button
            onClick={handleAddVoucher}
            className="mt-4 sm:mt-0 flex items-center justify-center gap-2 h-10 px-5 bg-primary text-white rounded-lg font-semibold text-sm hover:bg-primary/90 transition-colors"
          >
            <Plus className="w-5 h-5" />
            <span>Thêm Voucher Mới</span>
          </button>
        </div>

        <div className="bg-surface-light dark:bg-surface-dark p-6 rounded-xl border border-border-light dark:border-border-dark">
          {/* Filters */}
          <Filters
            searchValue={searchQuery}
            onSearchChange={setSearchQuery}
            searchPlaceholder="Tìm kiếm voucher..."
            filters={[
              {
                value: selectedStatus,
                onChange: setSelectedStatus,
                options: statusOptions,
              },
            ]}
          />

          {/* Date Filter - Custom addition */}
          <div className="mb-6">
            <input
              type="date"
              value={selectedDate}
              onChange={(e) => setSelectedDate(e.target.value)}
              className="form-input h-10 px-3 rounded-lg bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark focus:ring-2 focus:ring-primary/50 focus:border-primary/50 w-full md:w-48"
            />
          </div>

          {/* Vouchers DataTable */}
          <DataTable<Voucher>
            columns={columns}
            data={vouchers}
            actions={actions}
            emptyMessage="Không có voucher nào"
          />

          {/* Simple Pagination (static for sample data) */}
          <div className="flex items-center justify-between pt-4">
            <p className="text-sm text-text-secondary-light dark:text-text-secondary-dark">
              Hiển thị 1-4 trên 4 voucher
            </p>
            <div className="flex items-center gap-2">
              <button
                disabled
                className="flex items-center justify-center h-8 w-8 rounded-lg text-text-secondary-light dark:text-text-secondary-dark bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark hover:bg-black/5 dark:hover:bg-white/5 transition-colors disabled:opacity-50"
              >
                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path
                    strokeLinecap="round"
                    strokeLinejoin="round"
                    strokeWidth={2}
                    d="M15 19l-7-7 7-7"
                  />
                </svg>
              </button>
              <button className="flex items-center justify-center h-8 w-8 rounded-lg font-semibold text-sm bg-primary text-white">
                1
              </button>
              <button className="flex items-center justify-center h-8 w-8 rounded-lg text-text-secondary-light dark:text-text-secondary-dark bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark hover:bg-black/5 dark:hover:bg-white/5 transition-colors">
                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path
                    strokeLinecap="round"
                    strokeLinejoin="round"
                    strokeWidth={2}
                    d="M9 5l7 7-7 7"
                  />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </SellerLayout>
  );
}
