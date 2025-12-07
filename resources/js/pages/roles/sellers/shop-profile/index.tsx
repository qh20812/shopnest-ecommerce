import React, { useState } from 'react';
import SellerLayout from '../../../../layouts/seller-layout';
import { Save } from 'lucide-react';

interface IndexProps {
  user?: {
    full_name: string;
    email: string;
    avatar_url?: string;
  };
  shop?: {
    name: string;
    description: string;
    logo: string;
    cover: string;
    address: string;
    phone: string;
    email: string;
    hours: string;
    shipping_policy: string;
    return_policy: string;
    payment_policy: string;
  };
}

export default function Index({ user, shop }: IndexProps) {
  const [shopName, setShopName] = useState(shop?.name || 'Shop Thời Trang NestStyle');
  const [description, setDescription] = useState(
    shop?.description ||
      'Chào mừng bạn đến với NestStyle! Chúng tôi chuyên cung cấp các sản phẩm thời trang nam nữ chất lượng cao, cập nhật xu hướng mới nhất.'
  );
  const [logo, setLogo] = useState(
    shop?.logo ||
      'https://images.unsplash.com/photo-1472851294608-062f824d29cc?w=200&h=200&fit=crop'
  );
  const [cover, setCover] = useState(
    shop?.cover ||
      'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=1200&h=300&fit=crop'
  );
  const [address, setAddress] = useState(
    shop?.address || '123 Đường Thời Trang, Quận 1, TP. Hồ Chí Minh'
  );
  const [phone, setPhone] = useState(shop?.phone || '0987 654 321');
  const [email, setEmail] = useState(shop?.email || 'hotro@neststyle.com');
  const [hours, setHours] = useState(shop?.hours || 'Thứ Hai - Thứ Bảy: 9:00 - 21:00');
  const [shippingPolicy, setShippingPolicy] = useState(
    shop?.shipping_policy ||
      'Miễn phí vận chuyển cho đơn hàng từ 500.000đ. Thời gian giao hàng dự kiến từ 2-5 ngày làm việc.'
  );
  const [returnPolicy, setReturnPolicy] = useState(
    shop?.return_policy ||
      'Chấp nhận đổi trả trong vòng 7 ngày kể từ khi nhận hàng. Sản phẩm phải còn nguyên nhãn mác và chưa qua sử dụng.'
  );
  const [paymentPolicy, setPaymentPolicy] = useState(
    shop?.payment_policy ||
      'Chấp nhận thanh toán qua thẻ tín dụng, chuyển khoản ngân hàng, ví điện tử và COD.'
  );

  const handleLogoUpload = () => {
    // Handle logo upload
    console.log('Upload logo');
  };

  const handleCoverUpload = () => {
    // Handle cover upload
    console.log('Upload cover');
  };

  const handleSave = () => {
    console.log({
      shopName,
      description,
      logo,
      cover,
      address,
      phone,
      email,
      hours,
      shippingPolicy,
      returnPolicy,
      paymentPolicy,
    });
    // Handle save logic
  };

  return (
    <SellerLayout activePage="shop-settings" user={user}>
      <div className="container mx-auto px-6 py-8">
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
          {/* Left Column - Description */}
          <div className="lg:col-span-1">
            <h3 className="text-xl font-semibold text-text-primary-light dark:text-text-primary-dark">
              Cài đặt Shop
            </h3>
            <p className="text-text-secondary-light dark:text-text-secondary-dark mt-1">
              Quản lý thông tin công khai và các chính sách của cửa hàng.
            </p>
          </div>

          {/* Right Column - Forms */}
          <div className="lg:col-span-2 space-y-8">
            {/* Basic Information */}
            <div className="bg-surface-light dark:bg-surface-dark p-6 rounded-xl border border-border-light dark:border-border-dark">
              <div className="flex items-center justify-between">
                <h4 className="text-lg font-semibold text-text-primary-light dark:text-text-primary-dark">
                  Thông tin cơ bản
                </h4>
              </div>
              <hr className="my-4 border-border-light dark:border-border-dark" />
              <div className="space-y-6">
                {/* Shop Name */}
                <div className="grid grid-cols-1 md:grid-cols-3 gap-2 items-center">
                  <label
                    htmlFor="shop-name"
                    className="text-sm font-medium text-text-secondary-light dark:text-text-secondary-dark"
                  >
                    Tên cửa hàng
                  </label>
                  <input
                    id="shop-name"
                    type="text"
                    value={shopName}
                    onChange={(e) => setShopName(e.target.value)}
                    className="form-input w-full md:col-span-2 h-10 px-3 rounded-lg bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark focus:ring-2 focus:ring-primary/50 focus:border-primary/50 text-sm"
                  />
                </div>

                {/* Shop Description */}
                <div className="grid grid-cols-1 md:grid-cols-3 gap-2 items-start">
                  <label
                    htmlFor="shop-description"
                    className="text-sm font-medium text-text-secondary-light dark:text-text-secondary-dark pt-2"
                  >
                    Mô tả cửa hàng
                  </label>
                  <textarea
                    id="shop-description"
                    value={description}
                    onChange={(e) => setDescription(e.target.value)}
                    className="form-textarea w-full md:col-span-2 min-h-[120px] px-3 py-2 rounded-lg bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark focus:ring-2 focus:ring-primary/50 focus:border-primary/50 text-sm"
                  />
                </div>

                {/* Logo & Cover */}
                <div className="grid grid-cols-1 md:grid-cols-3 gap-2 items-start pt-2">
                  <label className="text-sm font-medium text-text-secondary-light dark:text-text-secondary-dark pt-2">
                    Logo & Ảnh bìa
                  </label>
                  <div className="md:col-span-2 space-y-4">
                    {/* Logo */}
                    <div className="flex items-center gap-4">
                      <img
                        src={logo}
                        alt="Shop Logo"
                        className="w-16 h-16 object-cover rounded-full"
                      />
                      <div>
                        <button
                          type="button"
                          onClick={handleLogoUpload}
                          className="inline-flex items-center justify-center h-9 px-3 rounded-lg border border-border-light dark:border-border-dark bg-surface-light dark:bg-surface-dark text-sm font-medium hover:bg-black/5 dark:hover:bg-white/5 transition-colors"
                        >
                          Đổi logo
                        </button>
                        <p className="text-xs text-text-secondary-light dark:text-text-secondary-dark mt-2">
                          Vuông, tối thiểu 200x200px.
                        </p>
                      </div>
                    </div>

                    {/* Cover */}
                    <div className="flex items-center gap-4">
                      <img
                        src={cover}
                        alt="Shop Cover"
                        className="w-32 h-16 object-cover rounded-lg"
                      />
                      <div>
                        <button
                          type="button"
                          onClick={handleCoverUpload}
                          className="inline-flex items-center justify-center h-9 px-3 rounded-lg border border-border-light dark:border-border-dark bg-surface-light dark:bg-surface-dark text-sm font-medium hover:bg-black/5 dark:hover:bg-white/5 transition-colors"
                        >
                          Đổi ảnh bìa
                        </button>
                        <p className="text-xs text-text-secondary-light dark:text-text-secondary-dark mt-2">
                          Chữ nhật, tối thiểu 1200x300px.
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            {/* Contact Information */}
            <div className="bg-surface-light dark:bg-surface-dark p-6 rounded-xl border border-border-light dark:border-border-dark">
              <h4 className="text-lg font-semibold text-text-primary-light dark:text-text-primary-dark">
                Thông tin liên hệ & Địa chỉ
              </h4>
              <hr className="my-4 border-border-light dark:border-border-dark" />
              <div className="space-y-6">
                {/* Address */}
                <div className="grid grid-cols-1 md:grid-cols-3 gap-2 items-center">
                  <label
                    htmlFor="shop-address"
                    className="text-sm font-medium text-text-secondary-light dark:text-text-secondary-dark"
                  >
                    Địa chỉ cửa hàng
                  </label>
                  <input
                    id="shop-address"
                    type="text"
                    value={address}
                    onChange={(e) => setAddress(e.target.value)}
                    className="form-input w-full md:col-span-2 h-10 px-3 rounded-lg bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark focus:ring-2 focus:ring-primary/50 focus:border-primary/50 text-sm"
                  />
                </div>

                {/* Phone */}
                <div className="grid grid-cols-1 md:grid-cols-3 gap-2 items-center">
                  <label
                    htmlFor="shop-phone"
                    className="text-sm font-medium text-text-secondary-light dark:text-text-secondary-dark"
                  >
                    Số điện thoại
                  </label>
                  <input
                    id="shop-phone"
                    type="tel"
                    value={phone}
                    onChange={(e) => setPhone(e.target.value)}
                    className="form-input w-full md:col-span-2 h-10 px-3 rounded-lg bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark focus:ring-2 focus:ring-primary/50 focus:border-primary/50 text-sm"
                  />
                </div>

                {/* Email */}
                <div className="grid grid-cols-1 md:grid-cols-3 gap-2 items-center">
                  <label
                    htmlFor="shop-email"
                    className="text-sm font-medium text-text-secondary-light dark:text-text-secondary-dark"
                  >
                    Email cửa hàng
                  </label>
                  <input
                    id="shop-email"
                    type="email"
                    value={email}
                    onChange={(e) => setEmail(e.target.value)}
                    className="form-input w-full md:col-span-2 h-10 px-3 rounded-lg bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark focus:ring-2 focus:ring-primary/50 focus:border-primary/50 text-sm"
                  />
                </div>

                {/* Hours */}
                <div className="grid grid-cols-1 md:grid-cols-3 gap-2 items-center">
                  <label
                    htmlFor="shop-hours"
                    className="text-sm font-medium text-text-secondary-light dark:text-text-secondary-dark"
                  >
                    Giờ mở cửa
                  </label>
                  <input
                    id="shop-hours"
                    type="text"
                    value={hours}
                    onChange={(e) => setHours(e.target.value)}
                    className="form-input w-full md:col-span-2 h-10 px-3 rounded-lg bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark focus:ring-2 focus:ring-primary/50 focus:border-primary/50 text-sm"
                  />
                </div>
              </div>
            </div>

            {/* Shop Policies */}
            <div className="bg-surface-light dark:bg-surface-dark p-6 rounded-xl border border-border-light dark:border-border-dark">
              <h4 className="text-lg font-semibold text-text-primary-light dark:text-text-primary-dark">
                Chính sách cửa hàng
              </h4>
              <hr className="my-4 border-border-light dark:border-border-dark" />
              <div className="space-y-6">
                {/* Shipping Policy */}
                <div className="grid grid-cols-1 md:grid-cols-3 gap-2 items-start">
                  <label
                    htmlFor="shipping-policy"
                    className="text-sm font-medium text-text-secondary-light dark:text-text-secondary-dark pt-2"
                  >
                    Chính sách vận chuyển
                  </label>
                  <textarea
                    id="shipping-policy"
                    value={shippingPolicy}
                    onChange={(e) => setShippingPolicy(e.target.value)}
                    className="form-textarea w-full md:col-span-2 min-h-[120px] px-3 py-2 rounded-lg bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark focus:ring-2 focus:ring-primary/50 focus:border-primary/50 text-sm"
                  />
                </div>

                {/* Return Policy */}
                <div className="grid grid-cols-1 md:grid-cols-3 gap-2 items-start">
                  <label
                    htmlFor="return-policy"
                    className="text-sm font-medium text-text-secondary-light dark:text-text-secondary-dark pt-2"
                  >
                    Chính sách đổi trả
                  </label>
                  <textarea
                    id="return-policy"
                    value={returnPolicy}
                    onChange={(e) => setReturnPolicy(e.target.value)}
                    className="form-textarea w-full md:col-span-2 min-h-[120px] px-3 py-2 rounded-lg bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark focus:ring-2 focus:ring-primary/50 focus:border-primary/50 text-sm"
                  />
                </div>

                {/* Payment Policy */}
                <div className="grid grid-cols-1 md:grid-cols-3 gap-2 items-start">
                  <label
                    htmlFor="payment-policy"
                    className="text-sm font-medium text-text-secondary-light dark:text-text-secondary-dark pt-2"
                  >
                    Chính sách thanh toán
                  </label>
                  <textarea
                    id="payment-policy"
                    value={paymentPolicy}
                    onChange={(e) => setPaymentPolicy(e.target.value)}
                    className="form-textarea w-full md:col-span-2 min-h-[120px] px-3 py-2 rounded-lg bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark focus:ring-2 focus:ring-primary/50 focus:border-primary/50 text-sm"
                  />
                </div>
              </div>
            </div>

            {/* Save Button */}
            <div className="flex justify-end">
              <button
                onClick={handleSave}
                className="inline-flex items-center justify-center gap-2 h-10 px-6 bg-primary text-white rounded-lg font-semibold text-sm hover:bg-primary/90 transition-colors"
              >
                <Save className="w-5 h-5" />
                <span>Lưu thay đổi</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </SellerLayout>
  );
}
