import React, { useState } from 'react';
import SellerLayout from '../../../../layouts/seller-layout';
import { Search, Plus, Edit, Trash2 } from 'lucide-react';

interface IndexProps {
  user?: {
    full_name: string;
    email: string;
    avatar_url?: string;
  };
}

interface Voucher {
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

  return (
    <SellerLayout activePage="promotions" user={user}>
      <div className="container mx-auto px-6 py-8">
        <div className="bg-surface-light dark:bg-surface-dark p-6 rounded-xl border border-border-light dark:border-border-dark">
          {/* Filters and Actions */}
          <div className="flex flex-col md:flex-row items-center justify-between gap-4 mb-6">
            <div className="flex-1 flex flex-col md:flex-row items-center gap-4 w-full">
              {/* Search */}
              <div className="relative w-full md:max-w-xs">
                <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-text-secondary-light dark:text-text-secondary-dark" />
                <input
                  type="text"
                  value={searchQuery}
                  onChange={(e) => setSearchQuery(e.target.value)}
                  className="form-input w-full h-10 pl-10 pr-3 rounded-lg bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark focus:ring-2 focus:ring-primary/50 focus:border-primary/50"
                  placeholder="Tìm kiếm voucher..."
                />
              </div>

              {/* Status Filter */}
              <div className="relative w-full md:w-48">
                <select
                  value={selectedStatus}
                  onChange={(e) => setSelectedStatus(e.target.value)}
                  className="form-select w-full h-10 px-3 pr-8 rounded-lg bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark focus:ring-2 focus:ring-primary/50 focus:border-primary/50 appearance-none"
                >
                  <option value="all">Tất cả trạng thái</option>
                  <option value="active">Đang hoạt động</option>
                  <option value="expired">Đã hết hạn</option>
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
                className="form-input h-10 px-3 rounded-lg bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark focus:ring-2 focus:ring-primary/50 focus:border-primary/50 w-full md:w-48"
              />
            </div>

            {/* Add Button */}
            <div className="w-full md:w-auto">
              <button
                onClick={handleAddVoucher}
                className="flex items-center justify-center w-full md:w-auto h-10 px-5 bg-primary text-white rounded-lg font-semibold text-sm hover:bg-primary/90 transition-colors gap-2"
              >
                <Plus className="w-5 h-5" />
                <span>Thêm Voucher Mới</span>
              </button>
            </div>
          </div>

          {/* Vouchers Table */}
          <div className="overflow-x-auto">
            <table className="w-full text-left">
              <thead>
                <tr className="bg-background-light dark:bg-background-dark border-b border-border-light dark:border-border-dark">
                  <th className="px-4 py-3 font-semibold text-sm text-text-secondary-light dark:text-text-secondary-dark">
                    Mã voucher
                  </th>
                  <th className="px-4 py-3 font-semibold text-sm text-text-secondary-light dark:text-text-secondary-dark">
                    Tên khuyến mãi
                  </th>
                  <th className="px-4 py-3 font-semibold text-sm text-text-secondary-light dark:text-text-secondary-dark">
                    Giá trị
                  </th>
                  <th className="px-4 py-3 font-semibold text-sm text-text-secondary-light dark:text-text-secondary-dark">
                    Điều kiện áp dụng
                  </th>
                  <th className="px-4 py-3 font-semibold text-sm text-text-secondary-light dark:text-text-secondary-dark">
                    Ngày bắt đầu
                  </th>
                  <th className="px-4 py-3 font-semibold text-sm text-text-secondary-light dark:text-text-secondary-dark">
                    Ngày kết thúc
                  </th>
                  <th className="px-4 py-3 font-semibold text-sm text-text-secondary-light dark:text-text-secondary-dark text-center">
                    Trạng thái
                  </th>
                  <th className="px-4 py-3 font-semibold text-sm text-text-secondary-light dark:text-text-secondary-dark text-right">
                    Hành động
                  </th>
                </tr>
              </thead>
              <tbody className="divide-y divide-border-light dark:divide-border-dark">
                {vouchers.map((voucher) => (
                  <tr key={voucher.id}>
                    <td className="px-4 py-4 font-medium text-text-primary-light dark:text-text-primary-dark">
                      {voucher.code}
                    </td>
                    <td className="px-4 py-4 text-text-secondary-light dark:text-text-secondary-dark">
                      {voucher.name}
                    </td>
                    <td className="px-4 py-4 text-text-secondary-light dark:text-text-secondary-dark">
                      {voucher.value}
                    </td>
                    <td className="px-4 py-4 text-text-secondary-light dark:text-text-secondary-dark">
                      {voucher.condition}
                    </td>
                    <td className="px-4 py-4 text-text-secondary-light dark:text-text-secondary-dark">
                      {voucher.start_date}
                    </td>
                    <td className="px-4 py-4 text-text-secondary-light dark:text-text-secondary-dark">
                      {voucher.end_date}
                    </td>
                    <td className="px-4 py-4 text-center">{getStatusBadge(voucher.status)}</td>
                    <td className="px-4 py-4 text-right">
                      <div className="flex items-center justify-end gap-2">
                        <button
                          onClick={() => handleEditVoucher(voucher.id)}
                          className="flex items-center justify-center h-8 w-8 rounded-lg text-text-secondary-light dark:text-text-secondary-dark hover:bg-black/5 dark:hover:bg-white/5 hover:text-primary transition-colors"
                        >
                          <Edit className="w-5 h-5" />
                        </button>
                        <button
                          onClick={() => handleDeleteVoucher(voucher.id)}
                          className="flex items-center justify-center h-8 w-8 rounded-lg text-text-secondary-light dark:text-text-secondary-dark hover:bg-black/5 dark:hover:bg-white/5 hover:text-red-500 transition-colors"
                        >
                          <Trash2 className="w-5 h-5" />
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
              Hiển thị 1-4 trên 4 voucher
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
