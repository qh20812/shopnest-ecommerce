import React from 'react';
import SellerLayout from '../../../../layouts/seller-layout';
import { ArrowLeft, Edit, Package, DollarSign, Tag, Image as ImageIcon } from 'lucide-react';
import { router } from '@inertiajs/react';
import { index, edit } from '../../../../routes/seller/products';

interface Category {
  category_name: string;
}

interface ProductImage {
  id: number;
  image_url: string;
  is_primary: boolean;
  display_order: number;
}

interface ProductVariant {
  variant_id: number;
  variant_name: string;
  sku: string;
  price: string;
  stock_quantity: number;
  attribute_values: string | null;
  images?: Array<{ id: number; image_url: string }>;
}

interface Product {
  product_id: number;
  product_name: string;
  slug: string;
  description: string | null;
  base_price: string;
  total_quantity: number;
  status: string;
  created_at: string;
  category: Category | null;
  images: ProductImage[];
  variants: ProductVariant[];
}

interface ShowProps {
  user?: {
    full_name: string;
    email: string;
    avatar_url?: string;
  };
  product: Product;
}

export default function Show({ user, product }: ShowProps) {
  const handleBack = () => {
    router.visit(index.url());
  };

  const handleEdit = () => {
    router.visit(edit.url({ product: product.product_id }));
  };

  const formatPrice = (price: string | number) => {
    const numPrice = typeof price === 'string' ? parseFloat(price) : price;
    return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(numPrice);
  };

  const getStatusBadge = (status: string) => {
    const statusConfig: Record<string, { label: string; className: string }> = {
      active: {
        label: 'Đang hiển thị',
        className: 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
      },
      inactive: {
        label: 'Đã ẩn',
        className: 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-400',
      },
      draft: {
        label: 'Bản nháp',
        className: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
      },
    };

    const config = statusConfig[status] || statusConfig.draft;
    
    return (
      <span className={`px-3 py-1 rounded-full text-sm font-medium ${config.className}`}>
        {config.label}
      </span>
    );
  };

  const parseAttributeValues = (attrStr: string | null) => {
    if (!attrStr) return null;
    try {
      return JSON.parse(attrStr);
    } catch {
      return null;
    }
  };

  const primaryImage = product.images.find(img => img.is_primary) || product.images[0];
  const otherImages = product.images.filter(img => !img.is_primary || img.id !== primaryImage?.id);

  return (
    <SellerLayout activePage="products" user={user}>
      <div className="container mx-auto px-6 py-8">
        {/* Header */}
        <div className="flex items-center justify-between mb-6">
          <div className="flex items-center gap-4">
            <button
              onClick={handleBack}
              className="p-2 hover:bg-surface-light dark:hover:bg-surface-dark rounded-lg transition-colors"
            >
              <ArrowLeft className="w-6 h-6 text-text-primary-light dark:text-text-primary-dark" />
            </button>
            <div>
              <h2 className="text-3xl font-bold text-text-primary-light dark:text-text-primary-dark">
                Chi tiết sản phẩm
              </h2>
              <p className="text-sm text-text-secondary-light dark:text-text-secondary-dark mt-1">
                #{String(product.product_id).padStart(5, '0')}
              </p>
            </div>
          </div>
          <button
            onClick={handleEdit}
            className="flex items-center justify-center gap-2 h-10 px-5 bg-primary text-white rounded-lg font-semibold text-sm hover:bg-primary/90 transition-colors"
          >
            <Edit className="w-5 h-5" />
            <span>Chỉnh sửa</span>
          </button>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
          {/* Left Column - Images */}
          <div className="lg:col-span-1">
            <div className="bg-surface-light dark:bg-surface-dark p-6 rounded-xl border border-border-light dark:border-border-dark sticky top-6">
              <h3 className="text-lg font-semibold mb-4 text-text-primary-light dark:text-text-primary-dark flex items-center gap-2">
                <ImageIcon className="w-5 h-5" />
                Hình ảnh sản phẩm
              </h3>
              
              {primaryImage ? (
                <div className="space-y-4">
                  {/* Primary Image */}
                  <div className="aspect-square rounded-lg overflow-hidden border-2 border-primary">
                    <img
                      src={primaryImage.image_url.startsWith('http') ? primaryImage.image_url : primaryImage.image_url}
                      alt={product.product_name}
                      className="w-full h-full object-cover"
                      onError={(e) => {
                        const target = e.target as HTMLImageElement;
                        target.src = 'https://via.placeholder.com/400?text=No+Image';
                      }}
                    />
                  </div>

                  {/* Other Images */}
                  {otherImages.length > 0 && (
                    <div className="grid grid-cols-3 gap-2">
                      {otherImages.map((img) => (
                        <div key={img.id} className="aspect-square rounded-lg overflow-hidden border border-border-light dark:border-border-dark">
                          <img
                            src={img.image_url}
                            alt={`${product.product_name} - ${img.display_order}`}
                            className="w-full h-full object-cover"
                            onError={(e) => {
                              const target = e.target as HTMLImageElement;
                              target.src = 'https://via.placeholder.com/100?text=No+Image';
                            }}
                          />
                        </div>
                      ))}
                    </div>
                  )}
                </div>
              ) : (
                <div className="aspect-square rounded-lg bg-background-light dark:bg-background-dark flex items-center justify-center">
                  <div className="text-center">
                    <ImageIcon className="w-16 h-16 mx-auto text-text-secondary-light dark:text-text-secondary-dark mb-2" />
                    <p className="text-sm text-text-secondary-light dark:text-text-secondary-dark">
                      Chưa có hình ảnh
                    </p>
                  </div>
                </div>
              )}

              <div className="mt-4 text-xs text-text-secondary-light dark:text-text-secondary-dark">
                Tổng số ảnh: {product.images.length}
              </div>
            </div>
          </div>

          {/* Right Column - Product Info */}
          <div className="lg:col-span-2 space-y-6">
            {/* Basic Info */}
            <div className="bg-surface-light dark:bg-surface-dark p-6 rounded-xl border border-border-light dark:border-border-dark">
              <div className="flex items-start justify-between mb-4">
                <div className="flex-1">
                  <h3 className="text-2xl font-bold text-text-primary-light dark:text-text-primary-dark mb-2">
                    {product.product_name}
                  </h3>
                  <p className="text-sm text-text-secondary-light dark:text-text-secondary-dark">
                    Slug: <span className="font-mono">{product.slug}</span>
                  </p>
                </div>
                {getStatusBadge(product.status)}
              </div>

              {product.description && (
                <div className="mt-4">
                  <h4 className="text-sm font-semibold text-text-primary-light dark:text-text-primary-dark mb-2">
                    Mô tả:
                  </h4>
                  <p className="text-text-secondary-light dark:text-text-secondary-dark whitespace-pre-wrap">
                    {product.description}
                  </p>
                </div>
              )}
            </div>

            {/* Price & Stock */}
            <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
              <div className="bg-surface-light dark:bg-surface-dark p-6 rounded-xl border border-border-light dark:border-border-dark">
                <div className="flex items-center gap-3 mb-2">
                  <div className="p-2 bg-primary/10 rounded-lg">
                    <DollarSign className="w-5 h-5 text-primary" />
                  </div>
                  <h4 className="text-sm font-medium text-text-secondary-light dark:text-text-secondary-dark">
                    Giá bán
                  </h4>
                </div>
                <p className="text-2xl font-bold text-text-primary-light dark:text-text-primary-dark">
                  {formatPrice(product.base_price)}
                </p>
              </div>

              <div className="bg-surface-light dark:bg-surface-dark p-6 rounded-xl border border-border-light dark:border-border-dark">
                <div className="flex items-center gap-3 mb-2">
                  <div className="p-2 bg-secondary/10 rounded-lg">
                    <Package className="w-5 h-5 text-secondary" />
                  </div>
                  <h4 className="text-sm font-medium text-text-secondary-light dark:text-text-secondary-dark">
                    Tồn kho
                  </h4>
                </div>
                <p className="text-2xl font-bold text-text-primary-light dark:text-text-primary-dark">
                  {product.total_quantity}
                </p>
              </div>

              <div className="bg-surface-light dark:bg-surface-dark p-6 rounded-xl border border-border-light dark:border-border-dark">
                <div className="flex items-center gap-3 mb-2">
                  <div className="p-2 bg-accent/10 rounded-lg">
                    <Tag className="w-5 h-5 text-accent" />
                  </div>
                  <h4 className="text-sm font-medium text-text-secondary-light dark:text-text-secondary-dark">
                    Danh mục
                  </h4>
                </div>
                <p className="text-lg font-semibold text-text-primary-light dark:text-text-primary-dark">
                  {product.category?.category_name || 'N/A'}
                </p>
              </div>
            </div>

            {/* Variants */}
            {product.variants && product.variants.length > 0 && (
              <div className="bg-surface-light dark:bg-surface-dark p-6 rounded-xl border border-border-light dark:border-border-dark">
                <h3 className="text-lg font-semibold mb-4 text-text-primary-light dark:text-text-primary-dark">
                  Biến thể sản phẩm ({product.variants.length})
                </h3>
                <div className="overflow-x-auto">
                  <table className="w-full text-sm">
                    <thead className="text-xs text-text-secondary-light dark:text-text-secondary-dark uppercase bg-background-light dark:bg-background-dark">
                      <tr>
                        <th className="px-4 py-3 text-left">Hình ảnh</th>
                        <th className="px-4 py-3 text-left">Tên biến thể</th>
                        <th className="px-4 py-3 text-left">SKU</th>
                        <th className="px-4 py-3 text-left">Thuộc tính</th>
                        <th className="px-4 py-3 text-right">Giá</th>
                        <th className="px-4 py-3 text-right">Tồn kho</th>
                      </tr>
                    </thead>
                    <tbody className="divide-y divide-border-light dark:divide-border-dark">
                      {product.variants.map((variant) => {
                        const attrs = parseAttributeValues(variant.attribute_values);
                        return (
                          <tr key={variant.variant_id} className="hover:bg-background-light dark:hover:bg-background-dark">
                            <td className="px-4 py-3">
                              {variant.images && variant.images.length > 0 ? (
                                <div className="flex gap-1">
                                  {variant.images.slice(0, 3).map((img, idx) => (
                                    <img
                                      key={img.id}
                                      src={img.image_url}
                                      alt={`${variant.variant_name} - ${idx + 1}`}
                                      className="w-12 h-12 object-cover rounded border border-border-light dark:border-border-dark"
                                      onError={(e) => {
                                        const target = e.target as HTMLImageElement;
                                        target.src = 'https://via.placeholder.com/48?text=No+Image';
                                      }}
                                    />
                                  ))}
                                  {variant.images.length > 3 && (
                                    <div className="w-12 h-12 flex items-center justify-center bg-background-light dark:bg-background-dark rounded border border-border-light dark:border-border-dark text-xs font-medium">
                                      +{variant.images.length - 3}
                                    </div>
                                  )}
                                </div>
                              ) : (
                                <div className="w-12 h-12 flex items-center justify-center bg-background-light dark:bg-background-dark rounded border border-border-light dark:border-border-dark">
                                  <ImageIcon className="w-5 h-5 text-text-secondary-light dark:text-text-secondary-dark" />
                                </div>
                              )}
                            </td>
                            <td className="px-4 py-3 font-medium text-text-primary-light dark:text-text-primary-dark">
                              {variant.variant_name}
                            </td>
                            <td className="px-4 py-3 text-text-secondary-light dark:text-text-secondary-dark font-mono text-xs">
                              {variant.sku}
                            </td>
                            <td className="px-4 py-3">
                              {attrs ? (
                                <div className="flex flex-wrap gap-1">
                                  {Object.entries(attrs).map(([key, value]) => (
                                    <span
                                      key={key}
                                      className="px-2 py-0.5 bg-primary/10 text-primary rounded text-xs"
                                    >
                                      {key}: {String(value)}
                                    </span>
                                  ))}
                                </div>
                              ) : (
                                <span className="text-text-secondary-light dark:text-text-secondary-dark text-xs">-</span>
                              )}
                            </td>
                            <td className="px-4 py-3 text-right font-semibold text-text-primary-light dark:text-text-primary-dark">
                              {formatPrice(variant.price)}
                            </td>
                            <td className="px-4 py-3 text-right">
                              <span className={`px-2 py-1 rounded-full text-xs font-medium ${
                                variant.stock_quantity > 10
                                  ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400'
                                  : variant.stock_quantity > 0
                                  ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400'
                                  : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'
                              }`}>
                                {variant.stock_quantity}
                              </span>
                            </td>
                          </tr>
                        );
                      })}
                    </tbody>
                  </table>
                </div>
              </div>
            )}

            {/* Additional Info */}
            <div className="bg-surface-light dark:bg-surface-dark p-6 rounded-xl border border-border-light dark:border-border-dark">
              <h3 className="text-lg font-semibold mb-4 text-text-primary-light dark:text-text-primary-dark">
                Thông tin bổ sung
              </h3>
              <dl className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <dt className="text-sm font-medium text-text-secondary-light dark:text-text-secondary-dark">
                    Ngày tạo:
                  </dt>
                  <dd className="mt-1 text-sm text-text-primary-light dark:text-text-primary-dark">
                    {new Date(product.created_at).toLocaleDateString('vi-VN', {
                      year: 'numeric',
                      month: 'long',
                      day: 'numeric',
                      hour: '2-digit',
                      minute: '2-digit',
                    })}
                  </dd>
                </div>
                <div>
                  <dt className="text-sm font-medium text-text-secondary-light dark:text-text-secondary-dark">
                    ID Sản phẩm:
                  </dt>
                  <dd className="mt-1 text-sm text-text-primary-light dark:text-text-primary-dark font-mono">
                    #{String(product.product_id).padStart(5, '0')}
                  </dd>
                </div>
              </dl>
            </div>
          </div>
        </div>
      </div>
    </SellerLayout>
  );
}
