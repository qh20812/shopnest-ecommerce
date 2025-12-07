import React, { useState } from 'react';
import SellerLayout from '../../../../layouts/seller-layout';
import { Save } from 'lucide-react';
import { router } from '@inertiajs/react';

interface UpdateProps {
  user?: {
    full_name: string;
    email: string;
    avatar_url?: string;
  };
  voucher?: {
    code: string;
    name: string;
    discount_type: string;
    discount_value: string;
    condition: string;
    start_date: string;
    end_date: string;
    status: string;
  };
}

export default function Update({ user, voucher }: UpdateProps) {
  const [voucherCode, setVoucherCode] = useState(voucher?.code || 'SNOWSALE20');
  const [voucherName, setVoucherName] = useState(voucher?.name || 'Giảm 20% mừng Giáng Sinh');
  const [discountType, setDiscountType] = useState(voucher?.discount_type || 'percentage');
  const [discountValue, setDiscountValue] = useState(voucher?.discount_value || '20');
  const [condition, setCondition] = useState(voucher?.condition || 'Đơn hàng tối thiểu 500.000đ');
  const [startDate, setStartDate] = useState(voucher?.start_date || '2024-12-01');
  const [endDate, setEndDate] = useState(voucher?.end_date || '2024-12-25');
  const [status, setStatus] = useState(voucher?.status || 'active');

  const handleCancel = () => {
    router.visit('/sellerpromotion');
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    console.log({
      voucherCode,
      voucherName,
      discountType,
      discountValue,
      condition,
      startDate,
      endDate,
      status,
    });
    // Handle form submission
  };

  return (
    <SellerLayout activePage="promotions" user={user}>
      <div className="container mx-auto px-6 py-8">
        <div className="max-w-4xl mx-auto">
          <h2 className="text-3xl font-bold text-text-primary-light dark:text-text-primary-dark mb-6">
            Chỉnh sửa Voucher
          </h2>
          <div className="bg-surface-light dark:bg-surface-dark p-6 sm:p-8 rounded-xl border border-border-light dark:border-border-dark">
            <form onSubmit={handleSubmit} className="space-y-6">
              {/* Voucher Code & Name */}
              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label
                    htmlFor="voucher-code"
                    className="block text-sm font-medium text-text-primary-light dark:text-text-primary-dark mb-2"
                  >
                    Mã voucher
                  </label>
                  <input
                    id="voucher-code"
                    type="text"
                    value={voucherCode}
                    onChange={(e) => setVoucherCode(e.target.value.toUpperCase())}
                    className="form-input w-full h-10 px-3 rounded-lg bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark focus:ring-2 focus:ring-primary/50 focus:border-primary/50 text-sm"
                    placeholder="VD: SNOWSALE20"
                  />
                </div>
                <div>
                  <label
                    htmlFor="voucher-name"
                    className="block text-sm font-medium text-text-primary-light dark:text-text-primary-dark mb-2"
                  >
                    Tên khuyến mãi
                  </label>
                  <input
                    id="voucher-name"
                    type="text"
                    value={voucherName}
                    onChange={(e) => setVoucherName(e.target.value)}
                    className="form-input w-full h-10 px-3 rounded-lg bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark focus:ring-2 focus:ring-primary/50 focus:border-primary/50 text-sm"
                    placeholder="VD: Giảm 20% mừng Giáng Sinh"
                  />
                </div>
              </div>

              {/* Discount Type & Value */}
              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label
                    htmlFor="discount-type"
                    className="block text-sm font-medium text-text-primary-light dark:text-text-primary-dark mb-2"
                  >
                    Loại giảm giá
                  </label>
                  <div className="relative">
                    <select
                      id="discount-type"
                      value={discountType}
                      onChange={(e) => setDiscountType(e.target.value)}
                      className="form-select w-full h-10 px-3 pr-8 rounded-lg bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark focus:ring-2 focus:ring-primary/50 focus:border-primary/50 text-sm appearance-none"
                    >
                      <option value="percentage">Phần trăm (%)</option>
                      <option value="fixed">Số tiền cố định (đ)</option>
                    </select>
                    <svg
                      className="absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none w-5 h-5 text-text-secondary-light dark:text-text-secondary-dark"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        strokeLinecap="round"
                        strokeLinejoin="round"
                        strokeWidth={2}
                        d="M19 9l-7 7-7-7"
                      />
                    </svg>
                  </div>
                </div>
                <div>
                  <label
                    htmlFor="discount-value"
                    className="block text-sm font-medium text-text-primary-light dark:text-text-primary-dark mb-2"
                  >
                    Giá trị giảm
                  </label>
                  <input
                    id="discount-value"
                    type="text"
                    value={discountValue}
                    onChange={(e) => setDiscountValue(e.target.value)}
                    className="form-input w-full h-10 px-3 rounded-lg bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark focus:ring-2 focus:ring-primary/50 focus:border-primary/50 text-sm"
                    placeholder={discountType === 'percentage' ? 'VD: 20' : 'VD: 50000'}
                  />
                </div>
              </div>

              {/* Condition */}
              <div>
                <label
                  htmlFor="condition"
                  className="block text-sm font-medium text-text-primary-light dark:text-text-primary-dark mb-2"
                >
                  Điều kiện áp dụng
                </label>
                <input
                  id="condition"
                  type="text"
                  value={condition}
                  onChange={(e) => setCondition(e.target.value)}
                  className="form-input w-full h-10 px-3 rounded-lg bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark focus:ring-2 focus:ring-primary/50 focus:border-primary/50 text-sm"
                  placeholder="VD: Đơn hàng tối thiểu 500.000đ"
                />
              </div>

              {/* Start & End Date */}
              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label
                    htmlFor="start-date"
                    className="block text-sm font-medium text-text-primary-light dark:text-text-primary-dark mb-2"
                  >
                    Ngày bắt đầu
                  </label>
                  <input
                    id="start-date"
                    type="date"
                    value={startDate}
                    onChange={(e) => setStartDate(e.target.value)}
                    className="form-input w-full h-10 px-3 rounded-lg bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark focus:ring-2 focus:ring-primary/50 focus:border-primary/50 text-sm"
                  />
                </div>
                <div>
                  <label
                    htmlFor="end-date"
                    className="block text-sm font-medium text-text-primary-light dark:text-text-primary-dark mb-2"
                  >
                    Ngày kết thúc
                  </label>
                  <input
                    id="end-date"
                    type="date"
                    value={endDate}
                    onChange={(e) => setEndDate(e.target.value)}
                    className="form-input w-full h-10 px-3 rounded-lg bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark focus:ring-2 focus:ring-primary/50 focus:border-primary/50 text-sm"
                  />
                </div>
              </div>

              {/* Status */}
              <div>
                <label
                  htmlFor="status"
                  className="block text-sm font-medium text-text-primary-light dark:text-text-primary-dark mb-2"
                >
                  Trạng thái
                </label>
                <div className="relative">
                  <select
                    id="status"
                    value={status}
                    onChange={(e) => setStatus(e.target.value)}
                    className="form-select w-full h-10 px-3 pr-8 rounded-lg bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark focus:ring-2 focus:ring-primary/50 focus:border-primary/50 text-sm appearance-none"
                  >
                    <option value="active">Đang hoạt động</option>
                    <option value="inactive">Đã hết hạn</option>
                  </select>
                  <svg
                    className="absolute right-2 top-1/2 -translate-y-1/2 pointer-events-none w-5 h-5 text-text-secondary-light dark:text-text-secondary-dark"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      strokeLinecap="round"
                      strokeLinejoin="round"
                      strokeWidth={2}
                      d="M19 9l-7 7-7-7"
                    />
                  </svg>
                </div>
              </div>

              {/* Actions */}
              <div className="flex items-center justify-end gap-4 pt-4">
                <button
                  type="button"
                  onClick={handleCancel}
                  className="h-10 px-5 rounded-lg border border-border-light dark:border-border-dark text-text-secondary-light dark:text-text-secondary-dark hover:bg-black/5 dark:hover:bg-white/5 transition-colors font-medium text-sm"
                >
                  Hủy
                </button>
                <button
                  type="submit"
                  className="flex items-center justify-center gap-2 h-10 px-5 bg-primary text-white rounded-lg font-semibold text-sm hover:bg-primary/90 transition-colors"
                >
                  <Save className="w-5 h-5" />
                  <span>Cập nhật Voucher</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </SellerLayout>
  );
}
