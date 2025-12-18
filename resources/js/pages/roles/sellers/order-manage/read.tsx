import React, { useState } from 'react';
import SellerLayout from '../../../../layouts/seller-layout';
import { ArrowLeft, Phone, Printer, RefreshCw, CheckCircle } from 'lucide-react';
import { router } from '@inertiajs/react';
import { index as ordersIndexRoute, updateStatus as updateStatusRoute } from '../../../../routes/seller/orders';

interface ReadProps {
  user?: {
    full_name: string;
    email: string;
    avatar_url?: string;
  };
  order: {
    id: string;
    date: string;
    customer: {
      name: string;
      email: string;
      phone: string;
      address: string;
    };
    products: OrderProduct[];
    subtotal: string;
    shipping: string;
    discount: string;
    total: string;
    payment_method: string;
    payment_status: 'paid' | 'unpaid' | 'cod';
    status: string;
  };
}

interface OrderProduct {
  id: string | number;
  name: string;
  image: string;
  variant: string;
  quantity: number;
  price: string;
  total: string;
}

export default function Read({ user, order }: ReadProps) {
  const [orderStatus, setOrderStatus] = useState(order.status);
  const [isUpdating, setIsUpdating] = useState(false);

  const handleBack = () => {
    router.visit(ordersIndexRoute.url());
  };

  const handleContactCustomer = () => {
    window.location.href = `tel:${order.customer.phone}`;
  };

  const handlePrintInvoice = () => {
    window.print();
  };

  const handleUpdateStatus = () => {
    if (isUpdating || orderStatus === order.status) return;

    const orderId = order.id.replace('#', '').replace(/^[A-Z]+-/, '');
    
    setIsUpdating(true);
    router.put(updateStatusRoute.url({ order: orderId }), {
      status: orderStatus,
    }, {
      preserveScroll: true,
      onSuccess: () => {
        setIsUpdating(false);
      },
      onError: (errors) => {
        setIsUpdating(false);
        console.error('Validation errors:', errors);
      },
    });
  };

  return (
    <SellerLayout activePage="orders" user={user}>
      <div className="container mx-auto px-6 py-8">
        {/* Header */}
        <div className="flex flex-col lg:flex-row items-start justify-between gap-6 mb-6">
          <div className="flex-1">
            <button
              onClick={handleBack}
              className="flex items-center gap-2 text-text-secondary-light dark:text-text-secondary-dark hover:text-primary dark:hover:text-primary transition-colors mb-4"
            >
              <ArrowLeft className="w-5 h-5" />
              <span className="font-medium">Quay lại Đơn hàng</span>
            </button>
            <h2 className="text-3xl font-bold text-text-primary-light dark:text-text-primary-dark">
              Đơn hàng {order.id}
            </h2>
            <p className="text-text-secondary-light dark:text-text-secondary-dark mt-1">
              Ngày đặt: {order.date}
            </p>
          </div>
          <div className="flex items-center gap-3 flex-wrap">
            <button
              onClick={handleContactCustomer}
              className="inline-flex items-center justify-center gap-2 h-10 px-4 rounded-lg bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark text-sm font-medium hover:bg-black/5 dark:hover:bg-white/5 transition-colors"
            >
              <Phone className="w-5 h-5" />
              <span>Liên hệ khách hàng</span>
            </button>
            <button
              onClick={handlePrintInvoice}
              className="inline-flex items-center justify-center gap-2 h-10 px-4 rounded-lg bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark text-sm font-medium hover:bg-black/5 dark:hover:bg-white/5 transition-colors"
            >
              <Printer className="w-5 h-5" />
              <span>In hóa đơn</span>
            </button>
            <button
              onClick={handleUpdateStatus}
              disabled={isUpdating || orderStatus === order.status}
              className="inline-flex items-center justify-center gap-2 h-10 px-4 rounded-lg bg-primary text-white text-sm font-medium hover:bg-primary/90 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <RefreshCw className={`w-5 h-5 ${isUpdating ? 'animate-spin' : ''}`} />
              <span>{isUpdating ? 'Đang cập nhật...' : 'Cập nhật trạng thái'}</span>
            </button>
          </div>
        </div>

        <div className="grid grid-cols-1 xl:grid-cols-3 gap-6">
          {/* Left Column - Products */}
          <div className="xl:col-span-2 space-y-6">
            <div className="bg-surface-light dark:bg-surface-dark p-6 rounded-xl border border-border-light dark:border-border-dark">
              <h3 className="text-lg font-semibold mb-4">Sản phẩm trong đơn hàng</h3>
              <div className="divide-y divide-border-light dark:divide-border-dark">
                {order.products.map((product) => (
                  <div key={product.id} className="flex items-center gap-4 py-4">
                    <img
                      src={product.image}
                      alt={product.name}
                      className="w-16 h-16 object-cover rounded-lg border border-border-light dark:border-border-dark"
                    />
                    <div className="flex-1 grid grid-cols-2 sm:grid-cols-4 gap-4 items-center">
                      <div>
                        <p className="font-medium">{product.name}</p>
                        <p className="text-sm text-text-secondary-light dark:text-text-secondary-dark">
                          {product.variant}
                        </p>
                      </div>
                      <p className="text-sm text-center">x {product.quantity}</p>
                      <p className="text-sm text-center">{product.price}</p>
                      <p className="font-medium text-right">{product.total}</p>
                    </div>
                  </div>
                ))}
              </div>

              {/* Pricing Summary */}
              <div className="mt-6 pt-4 border-t border-border-light dark:border-border-dark space-y-2 text-right">
                <div className="flex justify-between items-center text-sm">
                  <p className="text-text-secondary-light dark:text-text-secondary-dark">
                    Tổng tiền sản phẩm
                  </p>
                  <p className="font-medium">{order.subtotal}</p>
                </div>
                <div className="flex justify-between items-center text-sm">
                  <p className="text-text-secondary-light dark:text-text-secondary-dark">Phí vận chuyển</p>
                  <p className="font-medium">{order.shipping}</p>
                </div>
                <div className="flex justify-between items-center text-sm">
                  <p className="text-text-secondary-light dark:text-text-secondary-dark">Giảm giá</p>
                  <p className="font-medium text-primary">- {order.discount}</p>
                </div>
                <div className="flex justify-between items-center font-semibold text-lg mt-2">
                  <p>Tổng cộng</p>
                  <p>{order.total}</p>
                </div>
              </div>
            </div>
          </div>

          {/* Right Column - Customer Info & Status */}
          <div className="space-y-6">
            {/* Customer Information */}
            <div className="bg-surface-light dark:bg-surface-dark p-6 rounded-xl border border-border-light dark:border-border-dark">
              <h3 className="text-lg font-semibold mb-4">Thông tin khách hàng</h3>
              <div className="space-y-3 text-sm">
                <div className="flex">
                  <p className="w-28 text-text-secondary-light dark:text-text-secondary-dark shrink-0">
                    Tên KH
                  </p>
                  <p className="font-medium">{order.customer.name}</p>
                </div>
                <div className="flex">
                  <p className="w-28 text-text-secondary-light dark:text-text-secondary-dark shrink-0">Email</p>
                  <p className="font-medium break-all">{order.customer.email}</p>
                </div>
                <div className="flex">
                  <p className="w-28 text-text-secondary-light dark:text-text-secondary-dark shrink-0">
                    Điện thoại
                  </p>
                  <p className="font-medium">{order.customer.phone}</p>
                </div>
                <div className="flex">
                  <p className="w-28 text-text-secondary-light dark:text-text-secondary-dark shrink-0">
                    Địa chỉ
                  </p>
                  <p className="font-medium">{order.customer.address}</p>
                </div>
              </div>
            </div>

            {/* Order Status */}
            <div className="bg-surface-light dark:bg-surface-dark p-6 rounded-xl border border-border-light dark:border-border-dark">
              <h3 className="text-lg font-semibold mb-4">Trạng thái đơn hàng</h3>
              <div className="space-y-3">
                <div>
                  <label
                    htmlFor="order-status"
                    className="font-medium text-sm text-text-secondary-light dark:text-text-secondary-dark"
                  >
                    Trạng thái hiện tại
                  </label>
                  <div className="relative mt-1">
                    <select
                      id="order-status"
                      value={orderStatus}
                      onChange={(e) => setOrderStatus(e.target.value)}
                      className="form-select w-full h-10 px-3 pr-8 rounded-lg bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark focus:ring-2 focus:ring-primary/50 focus:border-primary/50 appearance-none"
                    >
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
                  <p className="font-medium text-sm text-text-secondary-light dark:text-text-secondary-dark">
                    Thanh toán
                  </p>
                  <div className="flex items-center gap-2 mt-1">
                    <CheckCircle className="w-5 h-5 text-green-500" />
                    <p className="font-medium">{order.payment_method}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </SellerLayout>
  );
}
