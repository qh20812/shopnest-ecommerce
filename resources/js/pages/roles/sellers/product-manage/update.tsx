import React, { useState } from 'react';
import SellerLayout from '../../../../layouts/seller-layout';
import { Save, X, PlusCircle, Trash2, Upload } from 'lucide-react';
import { router } from '@inertiajs/react';

interface UpdateProps {
  user?: {
    full_name: string;
    email: string;
    avatar_url?: string;
  };
  product?: {
    id: string;
    name: string;
    description: string;
    price: string;
    compare_price: string;
    stock: number;
    category: string;
    status: string;
    variants: ProductVariant[];
    images: string[];
  };
}

interface ProductVariant {
  id: string;
  size: string;
  color: string;
  stock: number;
}

export default function Update({ user, product }: UpdateProps) {
  const [productName, setProductName] = useState(product?.name || 'Tai nghe không dây');
  const [description, setDescription] = useState(
    product?.description ||
      'Tai nghe bluetooth cao cấp với chất lượng âm thanh vượt trội, pin sử dụng lên đến 24 giờ.'
  );
  const [price, setPrice] = useState(product?.price || '1,250,000đ');
  const [comparePrice, setComparePrice] = useState(product?.compare_price || '1,500,000đ');
  const [stock, setStock] = useState(product?.stock?.toString() || '120');
  const [category, setCategory] = useState(product?.category || 'electronics');
  const [status, setStatus] = useState(product?.status || 'active');
  const [variants, setVariants] = useState<ProductVariant[]>(
    product?.variants || [
      { id: '1', size: 'M', color: 'Đen', stock: 50 },
      { id: '2', size: 'L', color: 'Trắng', stock: 30 },
    ]
  );

  const handleAddVariant = () => {
    const newVariant: ProductVariant = {
      id: Date.now().toString(),
      size: '',
      color: '',
      stock: 0,
    };
    setVariants([...variants, newVariant]);
  };

  const handleRemoveVariant = (id: string) => {
    setVariants(variants.filter((v) => v.id !== id));
  };

  const handleVariantChange = (id: string, field: keyof ProductVariant, value: string | number) => {
    setVariants(
      variants.map((v) =>
        v.id === id ? { ...v, [field]: value } : v
      )
    );
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    // Handle form submission
    console.log({
      productName,
      description,
      price,
      comparePrice,
      stock,
      category,
      status,
      variants,
    });
  };

  const handleCancel = () => {
    router.visit('/sellerproduct');
  };

  return (
    <SellerLayout activePage="products" user={user}>
      <div className="container mx-auto px-6 py-8">
        {/* Header */}
        <div className="flex items-center justify-between mb-6">
          <h2 className="text-3xl font-bold text-text-primary-light dark:text-text-primary-dark">
            Chỉnh sửa sản phẩm
          </h2>
          <div className="flex items-center gap-4">
            <button
              onClick={handleCancel}
              type="button"
              className="h-10 px-5 text-text-secondary-light dark:text-text-secondary-dark bg-transparent hover:bg-black/5 dark:hover:bg-white/5 rounded-lg font-semibold text-sm transition-colors"
            >
              Hủy
            </button>
            <button
              onClick={handleSubmit}
              className="flex items-center justify-center gap-2 h-10 px-5 bg-primary text-white rounded-lg font-semibold text-sm hover:bg-primary/90 transition-colors"
            >
              <Save className="w-5 h-5" />
              <span>Cập nhật sản phẩm</span>
            </button>
          </div>
        </div>

        <div className="grid grid-cols-3 gap-8">
          {/* Left Column - Main Content */}
          <div className="col-span-2 space-y-8">
            {/* Basic Information */}
            <div className="bg-surface-light dark:bg-surface-dark p-6 rounded-xl border border-border-light dark:border-border-dark space-y-6">
              <div>
                <label
                  htmlFor="product-name"
                  className="block text-sm font-medium text-text-primary-light dark:text-text-primary-dark mb-2"
                >
                  Tên sản phẩm
                </label>
                <input
                  id="product-name"
                  type="text"
                  value={productName}
                  onChange={(e) => setProductName(e.target.value)}
                  className="form-input w-full h-10 px-4 rounded-lg bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark focus:ring-2 focus:ring-primary/50 focus:border-primary/50"
                  placeholder="Nhập tên sản phẩm..."
                />
              </div>
              <div>
                <label
                  htmlFor="product-description"
                  className="block text-sm font-medium text-text-primary-light dark:text-text-primary-dark mb-2"
                >
                  Mô tả
                </label>
                <textarea
                  id="product-description"
                  rows={5}
                  value={description}
                  onChange={(e) => setDescription(e.target.value)}
                  className="form-textarea w-full px-4 py-3 rounded-lg bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark focus:ring-2 focus:ring-primary/50 focus:border-primary/50"
                  placeholder="Nhập mô tả sản phẩm..."
                />
              </div>
            </div>

            {/* Price & Stock */}
            <div className="bg-surface-light dark:bg-surface-dark p-6 rounded-xl border border-border-light dark:border-border-dark">
              <h3 className="text-lg font-semibold mb-4 text-text-primary-light dark:text-text-primary-dark">
                Giá & Tồn kho
              </h3>
              <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                  <label
                    htmlFor="price"
                    className="block text-sm font-medium text-text-primary-light dark:text-text-primary-dark mb-2"
                  >
                    Giá bán
                  </label>
                  <input
                    id="price"
                    type="text"
                    value={price}
                    onChange={(e) => setPrice(e.target.value)}
                    className="form-input w-full h-10 px-4 rounded-lg bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark focus:ring-2 focus:ring-primary/50 focus:border-primary/50"
                    placeholder="0đ"
                  />
                </div>
                <div>
                  <label
                    htmlFor="compare-price"
                    className="block text-sm font-medium text-text-primary-light dark:text-text-primary-dark mb-2"
                  >
                    Giá so sánh
                  </label>
                  <input
                    id="compare-price"
                    type="text"
                    value={comparePrice}
                    onChange={(e) => setComparePrice(e.target.value)}
                    className="form-input w-full h-10 px-4 rounded-lg bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark focus:ring-2 focus:ring-primary/50 focus:border-primary/50"
                    placeholder="0đ"
                  />
                </div>
                <div>
                  <label
                    htmlFor="stock"
                    className="block text-sm font-medium text-text-primary-light dark:text-text-primary-dark mb-2"
                  >
                    Tồn kho
                  </label>
                  <input
                    id="stock"
                    type="number"
                    value={stock}
                    onChange={(e) => setStock(e.target.value)}
                    className="form-input w-full h-10 px-4 rounded-lg bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark focus:ring-2 focus:ring-primary/50 focus:border-primary/50"
                    placeholder="0"
                  />
                </div>
              </div>
            </div>

            {/* Product Variants */}
            <div className="bg-surface-light dark:bg-surface-dark p-6 rounded-xl border border-border-light dark:border-border-dark">
              <h3 className="text-lg font-semibold mb-4 text-text-primary-light dark:text-text-primary-dark">
                Biến thể sản phẩm
              </h3>
              <div className="space-y-4">
                {variants.map((variant, index) => (
                  <div key={variant.id} className="grid grid-cols-12 gap-4 items-end">
                    <div className="col-span-4">
                      {index === 0 && (
                        <label className="block text-sm font-medium text-text-primary-light dark:text-text-primary-dark mb-2">
                          Kích cỡ
                        </label>
                      )}
                      <input
                        type="text"
                        value={variant.size}
                        onChange={(e) => handleVariantChange(variant.id, 'size', e.target.value)}
                        className="form-input w-full h-10 px-4 rounded-lg bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark focus:ring-2 focus:ring-primary/50 focus:border-primary/50"
                        placeholder="M, L, XL..."
                      />
                    </div>
                    <div className="col-span-4">
                      {index === 0 && (
                        <label className="block text-sm font-medium text-text-primary-light dark:text-text-primary-dark mb-2">
                          Màu sắc
                        </label>
                      )}
                      <input
                        type="text"
                        value={variant.color}
                        onChange={(e) => handleVariantChange(variant.id, 'color', e.target.value)}
                        className="form-input w-full h-10 px-4 rounded-lg bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark focus:ring-2 focus:ring-primary/50 focus:border-primary/50"
                        placeholder="Đen, Trắng..."
                      />
                    </div>
                    <div className="col-span-3">
                      {index === 0 && (
                        <label className="block text-sm font-medium text-text-primary-light dark:text-text-primary-dark mb-2">
                          Tồn kho
                        </label>
                      )}
                      <input
                        type="number"
                        value={variant.stock}
                        onChange={(e) => handleVariantChange(variant.id, 'stock', parseInt(e.target.value) || 0)}
                        className="form-input w-full h-10 px-4 rounded-lg bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark focus:ring-2 focus:ring-primary/50 focus:border-primary/50"
                        placeholder="0"
                      />
                    </div>
                    <div className="col-span-1">
                      <button
                        type="button"
                        onClick={() => handleRemoveVariant(variant.id)}
                        className="flex items-center justify-center h-10 w-10 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                      >
                        <Trash2 className="w-5 h-5" />
                      </button>
                    </div>
                  </div>
                ))}
              </div>
              <button
                type="button"
                onClick={handleAddVariant}
                className="mt-4 flex items-center justify-center gap-2 h-10 px-4 bg-secondary/10 text-secondary rounded-lg font-semibold text-sm hover:bg-secondary/20 transition-colors"
              >
                <PlusCircle className="w-5 h-5" />
                <span>Thêm biến thể</span>
              </button>
            </div>
          </div>

          {/* Right Column - Sidebar */}
          <div className="col-span-1 space-y-8">
            {/* Category & Status */}
            <div className="bg-surface-light dark:bg-surface-dark p-6 rounded-xl border border-border-light dark:border-border-dark space-y-6">
              <div>
                <label
                  htmlFor="product-category"
                  className="block text-sm font-medium text-text-primary-light dark:text-text-primary-dark mb-2"
                >
                  Danh mục
                </label>
                <div className="relative">
                  <select
                    id="product-category"
                    value={category}
                    onChange={(e) => setCategory(e.target.value)}
                    className="form-select w-full h-10 px-3 pr-8 rounded-lg bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark focus:ring-2 focus:ring-primary/50 focus:border-primary/50 appearance-none"
                  >
                    <option value="electronics">Thiết bị điện tử</option>
                    <option value="fashion">Thời trang</option>
                    <option value="home">Đồ gia dụng</option>
                    <option value="accessories">Phụ kiện</option>
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
              </div>
              <div>
                <label
                  htmlFor="product-status"
                  className="block text-sm font-medium text-text-primary-light dark:text-text-primary-dark mb-2"
                >
                  Trạng thái
                </label>
                <div className="relative">
                  <select
                    id="product-status"
                    value={status}
                    onChange={(e) => setStatus(e.target.value)}
                    className="form-select w-full h-10 px-3 pr-8 rounded-lg bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark focus:ring-2 focus:ring-primary/50 focus:border-primary/50 appearance-none"
                  >
                    <option value="active">Đang hiển thị</option>
                    <option value="inactive">Đã ẩn</option>
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
              </div>
            </div>

            {/* Image Upload */}
            <div className="bg-surface-light dark:bg-surface-dark p-6 rounded-xl border border-border-light dark:border-border-dark">
              <label className="block text-sm font-medium text-text-primary-light dark:text-text-primary-dark mb-2">
                Hình ảnh sản phẩm
              </label>
              <div className="mt-2 flex justify-center rounded-lg border-2 border-dashed border-border-light dark:border-border-dark px-6 py-10 hover:border-primary transition-colors cursor-pointer">
                <div className="text-center">
                  <Upload className="mx-auto h-12 w-12 text-text-secondary-light dark:text-text-secondary-dark" />
                  <div className="mt-4 flex text-sm leading-6 text-text-secondary-light dark:text-text-secondary-dark">
                    <label
                      htmlFor="file-upload"
                      className="relative cursor-pointer rounded-md font-semibold text-primary hover:text-primary/80"
                    >
                      <span>Tải ảnh lên</span>
                      <input id="file-upload" name="file-upload" type="file" className="sr-only" multiple />
                    </label>
                    <p className="pl-1">hoặc kéo thả</p>
                  </div>
                  <p className="text-xs leading-5 text-text-secondary-light dark:text-text-secondary-dark">
                    PNG, JPG, GIF tối đa 10MB
                  </p>
                </div>
              </div>
              {/* Show existing images if any */}
              {product?.images && product.images.length > 0 && (
                <div className="mt-4 grid grid-cols-3 gap-2">
                  {product.images.map((img, idx) => (
                    <div key={idx} className="relative aspect-square">
                      <img
                        src={img}
                        alt={`Product ${idx + 1}`}
                        className="w-full h-full object-cover rounded-lg"
                      />
                      <button
                        type="button"
                        className="absolute -top-2 -right-2 p-1 bg-red-500 text-white rounded-full hover:bg-red-600 transition-colors"
                      >
                        <X className="w-4 h-4" />
                      </button>
                    </div>
                  ))}
                </div>
              )}
            </div>
          </div>
        </div>
      </div>
    </SellerLayout>
  );
}
