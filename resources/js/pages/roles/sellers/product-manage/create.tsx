import React, { useState } from 'react';
import SellerLayout from '../../../../layouts/seller-layout';
import { Save, PlusCircle, Trash2, Upload, X } from 'lucide-react';
import { router, useForm } from '@inertiajs/react';
import { index, store } from '../../../../routes/seller/products';

interface Category {
  id: number;
  category_name: string;
  slug: string;
}

interface CreateProps {
  user?: {
    full_name: string;
    email: string;
    avatar_url?: string;
  };
  categories: Category[];
}

interface ProductVariant {
  variant_name: string;
  price: string;
  stock_quantity: string;
  sku: string;
  images: File[];
}

export default function Create({ user, categories }: CreateProps) {
  const { data, setData, post, processing, errors } = useForm({
    product_name: '',
    description: '',
    base_price: '',
    stock_quantity: '',
    category_id: categories[0]?.id || '',
    status: 'active',
    variants: [] as ProductVariant[],
    images: [] as File[],
  });

  const [imagePreviewUrls, setImagePreviewUrls] = useState<string[]>([]);
  const [variantImagePreviews, setVariantImagePreviews] = useState<Record<number, string[]>>({});

  const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    if (e.target.files) {
      const files = Array.from(e.target.files);
      console.debug('Selected files', files.map((f) => f.name));
      
      // Create preview URLs
      const newPreviewUrls = files.map(file => URL.createObjectURL(file));
      setImagePreviewUrls(prev => [...prev, ...newPreviewUrls]);
      
      // Add to existing images
      setData('images', [...data.images, ...files]);
    }
  };

  const handleRemoveImage = (index: number) => {
    // Revoke the preview URL to free memory
    URL.revokeObjectURL(imagePreviewUrls[index]);
    
    // Remove from previews
    setImagePreviewUrls(prev => prev.filter((_, i) => i !== index));
    
    // Remove from data
    setData('images', data.images.filter((_, i) => i !== index));
  };

  const handleAddVariant = () => {
    setData('variants', [
      ...data.variants,
      {
        variant_name: '',
        price: data.base_price,
        stock_quantity: '',
        sku: '',
        images: [],
      },
    ]);
  };

  const handleRemoveVariant = (index: number) => {
    // Clean up image previews
    if (variantImagePreviews[index]) {
      variantImagePreviews[index].forEach(url => URL.revokeObjectURL(url));
    }
    
    const newPreviews = { ...variantImagePreviews };
    delete newPreviews[index];
    setVariantImagePreviews(newPreviews);
    
    setData('variants', data.variants.filter((_, i) => i !== index));
  };

  const handleVariantChange = (index: number, field: keyof ProductVariant, value: string | File[]) => {
    const newVariants = [...data.variants];
    newVariants[index] = { ...newVariants[index], [field]: value } as ProductVariant;
    setData('variants', newVariants);
  };

  const handleVariantImageChange = (index: number, e: React.ChangeEvent<HTMLInputElement>) => {
    if (e.target.files) {
      const files = Array.from(e.target.files);
      
      // Create preview URLs
      const newPreviewUrls = files.map(file => URL.createObjectURL(file));
      setVariantImagePreviews(prev => ({
        ...prev,
        [index]: [...(prev[index] || []), ...newPreviewUrls],
      }));
      
      // Add to variant images
      const currentImages = data.variants[index].images || [];
      handleVariantChange(index, 'images', [...currentImages, ...files]);
    }
  };

  const handleRemoveVariantImage = (variantIndex: number, imageIndex: number) => {
    // Revoke preview URL
    const previews = variantImagePreviews[variantIndex];
    if (previews && previews[imageIndex]) {
      URL.revokeObjectURL(previews[imageIndex]);
    }
    
    // Remove from previews
    setVariantImagePreviews(prev => ({
      ...prev,
      [variantIndex]: (prev[variantIndex] || []).filter((_, i) => i !== imageIndex),
    }));
    
    // Remove from variant images
    const currentImages = data.variants[variantIndex].images;
    handleVariantChange(variantIndex, 'images', currentImages.filter((_, i) => i !== imageIndex));
  };

  const handleSubmit = (e?: React.FormEvent) => {
    if (e) {
      e.preventDefault();
    }
    console.debug('Submitting product create', data);
    console.debug('Form data:', {
      product_name: data.product_name,
      description: data.description,
      base_price: data.base_price,
      stock_quantity: data.stock_quantity,
      category_id: data.category_id,
      status: data.status,
      variants: data.variants,
      images_count: data.images.length,
    });
    
    // Log detailed variant information
    console.debug('Detailed variants info:', data.variants.map((v, i) => ({
      index: i,
      variant_name: v.variant_name,
      price: v.price,
      stock_quantity: v.stock_quantity,
      sku: v.sku,
      has_images: !!v.images,
      images_count: v.images?.length || 0,
      images_names: v.images?.map(img => img.name) || [],
    })));
    
    post(store.url(), {
      forceFormData: true, // ensure files are sent as multipart
      onSuccess: () => {
        console.log('Product created successfully!');
      },
      onError: (errors) => {
        console.error('Validation errors:', errors);
      },
    });
  };

  const handleCancel = () => {
    router.visit(index.url());
  };

  return (
    <SellerLayout activePage="products" user={user}>
      <div className="container mx-auto px-6 py-8">
        {/* Header */}
        <div className="flex items-center justify-between mb-6">
          <h2 className="text-3xl font-bold text-text-primary-light dark:text-text-primary-dark">
            Thêm sản phẩm mới
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
              type="button"
              onClick={() => handleSubmit()}
              disabled={processing}
              className="flex items-center justify-center gap-2 h-10 px-5 bg-primary text-white rounded-lg font-semibold text-sm hover:bg-primary/90 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <Save className="w-5 h-5" />
              <span>{processing ? 'Đang lưu...' : 'Lưu sản phẩm'}</span>
            </button>
          </div>
        </div>

        <form
          className="grid grid-cols-3 gap-8"
          onSubmit={handleSubmit}
          encType="multipart/form-data"
        >
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
                  value={data.product_name}
                  onChange={(e) => setData('product_name', e.target.value)}
                  className={`form-input w-full h-10 px-4 rounded-lg bg-background-light dark:bg-background-dark border ${errors.product_name ? 'border-red-500' : 'border-border-light dark:border-border-dark'} focus:ring-2 focus:ring-primary/50 focus:border-primary/50`}
                  placeholder="Nhập tên sản phẩm..."
                />
                {errors.product_name && <p className="mt-1 text-sm text-red-500">{errors.product_name}</p>}
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
                  value={data.description}
                  onChange={(e) => setData('description', e.target.value)}
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
                    value={data.base_price}
                    onChange={(e) => setData('base_price', e.target.value)}
                    className={`form-input w-full h-10 px-4 rounded-lg bg-background-light dark:bg-background-dark border ${errors.base_price ? 'border-red-500' : 'border-border-light dark:border-border-dark'} focus:ring-2 focus:ring-primary/50 focus:border-primary/50`}
                    placeholder="0đ"
                  />
                  {errors.base_price && <p className="mt-1 text-sm text-red-500">{errors.base_price}</p>}
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
                    value={data.stock_quantity}
                    onChange={(e) => setData('stock_quantity', e.target.value)}
                    className={`form-input w-full h-10 px-4 rounded-lg bg-background-light dark:bg-background-dark border ${errors.stock_quantity ? 'border-red-500' : 'border-border-light dark:border-border-dark'} focus:ring-2 focus:ring-primary/50 focus:border-primary/50`}
                    placeholder="0"
                  />
                  {errors.stock_quantity && <p className="mt-1 text-sm text-red-500">{errors.stock_quantity}</p>}
                </div>
              </div>
            </div>

            {/* Product Variants */}
            <div className="bg-surface-light dark:bg-surface-dark p-6 rounded-xl border border-border-light dark:border-border-dark">
              <div className="flex items-center justify-between mb-4">
                <h3 className="text-lg font-semibold text-text-primary-light dark:text-text-primary-dark">
                  Biến thể sản phẩm
                </h3>
                <button
                  type="button"
                  onClick={handleAddVariant}
                  className="flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors text-sm font-medium"
                >
                  <PlusCircle className="w-4 h-4" />
                  Thêm biến thể
                </button>
              </div>

              {data.variants.length === 0 ? (
                <div className="text-center py-8 text-text-secondary-light dark:text-text-secondary-dark">
                  <p>Chưa có biến thể nào. Nhấn "Thêm biến thể" để tạo biến thể mới.</p>
                  <p className="text-sm mt-2">Ví dụ: Màu trắng, RAM 16GB, Model AC120, v.v.</p>
                </div>
              ) : (
                <div className="space-y-4">
                  {data.variants.map((variant, index) => (
                    <div
                      key={index}
                      className="p-4 bg-background-light dark:bg-background-dark rounded-lg border border-border-light dark:border-border-dark space-y-4"
                    >
                      <div className="flex items-center justify-between">
                        <h4 className="font-medium text-text-primary-light dark:text-text-primary-dark">
                          Biến thể #{index + 1}
                        </h4>
                        <button
                          type="button"
                          onClick={() => handleRemoveVariant(index)}
                          className="p-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                        >
                          <Trash2 className="w-4 h-4" />
                        </button>
                      </div>

                      <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {/* Variant Name */}
                        <div className="md:col-span-2">
                          <label className="block text-sm font-medium text-text-primary-light dark:text-text-primary-dark mb-2">
                            Tên biến thể <span className="text-red-500">*</span>
                          </label>
                          <input
                            type="text"
                            value={variant.variant_name}
                            onChange={(e) => handleVariantChange(index, 'variant_name', e.target.value)}
                            placeholder="Ví dụ: Màu trắng, RAM 16GB, Model AC120..."
                            className="form-input w-full h-10 px-4 rounded-lg bg-surface-light dark:bg-surface-dark border border-border-light dark:border-border-dark focus:ring-2 focus:ring-primary/50 focus:border-primary/50"
                          />
                        </div>

                        {/* Price */}
                        <div>
                          <label className="block text-sm font-medium text-text-primary-light dark:text-text-primary-dark mb-2">
                            Giá bán <span className="text-red-500">*</span>
                          </label>
                          <input
                            type="text"
                            value={variant.price}
                            onChange={(e) => handleVariantChange(index, 'price', e.target.value)}
                            placeholder="0đ"
                            className="form-input w-full h-10 px-4 rounded-lg bg-surface-light dark:bg-surface-dark border border-border-light dark:border-border-dark focus:ring-2 focus:ring-primary/50 focus:border-primary/50"
                          />
                        </div>

                        {/* Stock */}
                        <div>
                          <label className="block text-sm font-medium text-text-primary-light dark:text-text-primary-dark mb-2">
                            Tồn kho <span className="text-red-500">*</span>
                          </label>
                          <input
                            type="number"
                            value={variant.stock_quantity}
                            onChange={(e) => handleVariantChange(index, 'stock_quantity', e.target.value)}
                            placeholder="0"
                            className="form-input w-full h-10 px-4 rounded-lg bg-surface-light dark:bg-surface-dark border border-border-light dark:border-border-dark focus:ring-2 focus:ring-primary/50 focus:border-primary/50"
                          />
                        </div>

                        {/* SKU */}
                        <div className="md:col-span-2">
                          <label className="block text-sm font-medium text-text-primary-light dark:text-text-primary-dark mb-2">
                            SKU (Mã sản phẩm)
                          </label>
                          <input
                            type="text"
                            value={variant.sku}
                            onChange={(e) => handleVariantChange(index, 'sku', e.target.value)}
                            placeholder="Ví dụ: SHIRT-WHITE-001"
                            className="form-input w-full h-10 px-4 rounded-lg bg-surface-light dark:bg-surface-dark border border-border-light dark:border-border-dark focus:ring-2 focus:ring-primary/50 focus:border-primary/50"
                          />
                        </div>

                        {/* Variant Images */}
                        <div className="md:col-span-2">
                          <label className="block text-sm font-medium text-text-primary-light dark:text-text-primary-dark mb-2">
                            Hình ảnh biến thể
                          </label>
                          <div className="flex items-center gap-4">
                            <label className="flex items-center gap-2 px-4 py-2 bg-surface-light dark:bg-surface-dark border border-border-light dark:border-border-dark rounded-lg hover:border-primary transition-colors cursor-pointer text-sm">
                              <Upload className="w-4 h-4" />
                              <span>Tải ảnh lên</span>
                              <input
                                type="file"
                                multiple
                                accept="image/*"
                                className="sr-only"
                                onChange={(e) => handleVariantImageChange(index, e)}
                              />
                            </label>
                            {variant.images.length > 0 && (
                              <span className="text-sm text-text-secondary-light dark:text-text-secondary-dark">
                                {variant.images.length} ảnh
                              </span>
                            )}
                          </div>

                          {/* Image Previews */}
                          {variantImagePreviews[index] && variantImagePreviews[index].length > 0 && (
                            <div className="mt-3 grid grid-cols-4 gap-2">
                              {variantImagePreviews[index].map((url, imgIdx) => (
                                <div key={imgIdx} className="relative aspect-square group">
                                  <img
                                    src={url}
                                    alt={`Variant ${index + 1} - Image ${imgIdx + 1}`}
                                    className="w-full h-full object-cover rounded-lg border-2 border-border-light dark:border-border-dark"
                                  />
                                  <button
                                    type="button"
                                    onClick={() => handleRemoveVariantImage(index, imgIdx)}
                                    className="absolute -top-2 -right-2 p-1 bg-red-500 text-white rounded-full hover:bg-red-600 transition-colors opacity-0 group-hover:opacity-100"
                                  >
                                    <X className="w-3 h-3" />
                                  </button>
                                </div>
                              ))}
                            </div>
                          )}
                        </div>
                      </div>
                    </div>
                  ))}
                </div>
              )}
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
                    value={data.category_id}
                    onChange={(e) => setData('category_id', e.target.value)}
                    className={`form-select w-full h-10 px-3 pr-8 rounded-lg bg-background-light dark:bg-background-dark border ${errors.category_id ? 'border-red-500' : 'border-border-light dark:border-border-dark'} focus:ring-2 focus:ring-primary/50 focus:border-primary/50 appearance-none`}
                  >
                    {categories.map((cat) => (
                      <option key={cat.id} value={cat.id}>
                        {cat.category_name}
                      </option>
                    ))}
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
                {errors.category_id && <p className="mt-1 text-sm text-red-500">{errors.category_id}</p>}
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
                    value={data.status}
                    onChange={(e) => setData('status', e.target.value)}
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
                Hình ảnh sản phẩm chung
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
                      <input 
                        id="file-upload" 
                        name="file-upload" 
                        type="file" 
                        className="sr-only" 
                        multiple 
                        accept="image/*"
                        onChange={handleFileChange}
                      />
                    </label>
                    <p className="pl-1">hoặc kéo thả</p>
                  </div>
                  <p className="text-xs leading-5 text-text-secondary-light dark:text-text-secondary-dark">
                    PNG, JPG, GIF tối đa 10MB mỗi ảnh
                  </p>
                </div>
              </div>
              
              {/* Image Preview Grid */}
              {imagePreviewUrls.length > 0 && (
                <div className="mt-4 grid grid-cols-3 gap-2">
                  {imagePreviewUrls.map((url, idx) => (
                    <div key={idx} className="relative aspect-square group">
                      <img
                        src={url}
                        alt={`Preview ${idx + 1}`}
                        className="w-full h-full object-cover rounded-lg border-2 border-border-light dark:border-border-dark"
                      />
                      {idx === 0 && (
                        <span className="absolute top-2 left-2 px-2 py-1 bg-primary text-white text-xs font-semibold rounded">
                          Chính
                        </span>
                      )}
                      <button
                        type="button"
                        onClick={() => handleRemoveImage(idx)}
                        className="absolute -top-2 -right-2 p-1.5 bg-red-500 text-white rounded-full hover:bg-red-600 transition-colors opacity-0 group-hover:opacity-100"
                      >
                        <X className="w-4 h-4" />
                      </button>
                      <div className="absolute bottom-0 left-0 right-0 bg-black/50 text-white text-xs p-1 text-center rounded-b-lg opacity-0 group-hover:opacity-100 transition-opacity truncate">
                        {data.images[idx]?.name}
                      </div>
                    </div>
                  ))}
                </div>
              )}
              
              {data.images.length > 0 && (
                <p className="mt-3 text-sm text-text-secondary-light dark:text-text-secondary-dark">
                  Đã chọn {data.images.length} ảnh. Ảnh đầu tiên sẽ là ảnh chính.
                </p>
              )}
            </div>
          </div>
        </form>
      </div>
    </SellerLayout>
  );
}
