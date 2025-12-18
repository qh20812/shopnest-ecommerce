import React, { useState } from 'react';
import SellerLayout from '../../../../layouts/seller-layout';
import { Save, X, PlusCircle, Trash2, Upload, Image as ImageIcon } from 'lucide-react';
import { router, useForm } from '@inertiajs/react';
import { index, update } from '../../../../routes/seller/products';

interface Category {
  id: number;
  category_name: string;
  slug: string;
}

interface ProductImage {
  id: number;
  url: string;
  is_primary: boolean;
}

interface ProductVariant {
  id?: number;
  size: string;
  color: string;
  stock_quantity: number;
  images?: File[];
  existing_images?: Array<{ id: number; url: string }>;
}

interface Product {
  id: string;
  name: string;
  description: string;
  price: string;
  stock: number;
  category: number;
  status: string;
  variants: ProductVariant[];
  images: ProductImage[];
}

interface UpdateProps {
  user?: {
    full_name: string;
    email: string;
    avatar_url?: string;
  };
  product: Product;
  categories: Category[];
}

export default function Update({ user, product, categories }: UpdateProps) {
  const productId = parseInt(product.id.replace('#', '').replace(/^0+/, ''), 10);
  
  // Ensure product.images and product.variants are always arrays
  const productImages = Array.isArray(product.images) ? product.images : [];
  const productVariants = Array.isArray(product.variants) ? product.variants : [];
  
  const { data, setData, processing, errors } = useForm({
    product_name: product.name || '',
    description: product.description || '',
    base_price: product.price.replace(/[^0-9]/g, '') || '',
    stock_quantity: product.stock?.toString() || '',
    category_id: product.category || categories[0]?.id || '',
    status: product.status || 'active',
    variants: productVariants,
    images: [] as File[],
    delete_images: [] as number[],
  });

  const [variants, setVariants] = useState<ProductVariant[]>(productVariants);
  const [imagesToDelete, setImagesToDelete] = useState<number[]>([]);
  const [imagePreviewUrls, setImagePreviewUrls] = useState<string[]>([]);

  const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    if (e.target.files) {
      const files = Array.from(e.target.files);
      console.debug('Selected files (update)', files.map((f) => f.name));
      
      // Create preview URLs
      const newPreviewUrls = files.map(file => URL.createObjectURL(file));
      setImagePreviewUrls(prev => [...prev, ...newPreviewUrls]);
      
      // Add to existing images
      setData('images', [...data.images, ...files]);
    }
  };

  const handleRemoveNewImage = (index: number) => {
    // Revoke the preview URL to free memory
    URL.revokeObjectURL(imagePreviewUrls[index]);
    
    // Remove from previews
    setImagePreviewUrls(prev => prev.filter((_, i) => i !== index));
    
    // Remove from data
    setData('images', data.images.filter((_, i) => i !== index));
  };

  const handleDeleteImage = (imageId: number) => {
    const updatedDeleteImages = [...imagesToDelete, imageId];
    setImagesToDelete(updatedDeleteImages);
    setData('delete_images', updatedDeleteImages);
  };

  const handleVariantImageChange = (variantIndex: number, e: React.ChangeEvent<HTMLInputElement>) => {
    if (e.target.files) {
      const files = Array.from(e.target.files);
      const updatedVariants = variants.map((v, i) => 
        i === variantIndex ? { ...v, images: [...(v.images || []), ...files] } : v
      );
      setVariants(updatedVariants);
      setData('variants', updatedVariants);
    }
  };

  const handleRemoveVariantImage = (variantIndex: number, imageIndex: number) => {
    const updatedVariants = variants.map((v, i) => {
      if (i === variantIndex) {
        const newImages = (v.images || []).filter((_, idx) => idx !== imageIndex);
        return { ...v, images: newImages };
      }
      return v;
    });
    setVariants(updatedVariants);
    setData('variants', updatedVariants);
  };

  const handleRemoveExistingVariantImage = (variantIndex: number, imageId: number) => {
    const updatedVariants = variants.map((v, i) => {
      if (i === variantIndex) {
        const newExistingImages = (v.existing_images || []).filter(img => img.id !== imageId);
        return { ...v, existing_images: newExistingImages };
      }
      return v;
    });
    setVariants(updatedVariants);
    setData('variants', updatedVariants);
    
    // Also add to delete_images list
    const updatedDeleteImages = [...imagesToDelete, imageId];
    setImagesToDelete(updatedDeleteImages);
    setData('delete_images', updatedDeleteImages);
  };

  const handleAddVariant = () => {
    const newVariant: ProductVariant = {
      size: '',
      color: '',
      stock_quantity: 0,
    };
    const updatedVariants = [...variants, newVariant];
    setVariants(updatedVariants);
    setData('variants', updatedVariants);
  };

  const handleRemoveVariant = (index: number) => {
    const updatedVariants = variants.filter((_, i) => i !== index);
    setVariants(updatedVariants);
    setData('variants', updatedVariants);
  };

  const handleVariantChange = (index: number, field: keyof ProductVariant, value: string | number) => {
    const updatedVariants = variants.map((v, i) =>
      i === index ? { ...v, [field]: value } : v
    );
    setVariants(updatedVariants);
    setData('variants', updatedVariants);
  };

  const handleSubmit = (e?: React.FormEvent) => {
    if (e) {
      e.preventDefault();
    }
    
    // Sync local state to form data before submitting
    const submitData = {
      product_name: data.product_name,
      description: data.description,
      base_price: data.base_price,
      stock_quantity: data.stock_quantity,
      category_id: data.category_id,
      status: data.status,
      variants: data.variants,
      images: data.images,
      delete_images: data.delete_images,
    };
    
    console.debug('Submitting product update', submitData);
    console.debug('Form data:', {
      product_name: submitData.product_name,
      base_price: submitData.base_price,
      stock_quantity: submitData.stock_quantity,
      category_id: submitData.category_id,
      status: submitData.status,
      variants_count: submitData.variants.length,
      images_count: submitData.images.length,
      delete_images_count: submitData.delete_images.length,
    });
    
    // Log detailed variant information
    console.debug('Detailed variants info:', submitData.variants.map((v, i) => ({
      index: i,
      id: v.id,
      size: v.size,
      color: v.color,
      stock_quantity: v.stock_quantity,
      has_images: !!v.images,
      images_count: v.images?.length || 0,
      images_names: v.images?.map(img => img.name) || [],
      has_existing_images: !!v.existing_images,
      existing_images_count: v.existing_images?.length || 0,
    })));
    
    // Build a FormData payload so nested objects and files are appended correctly
    const formData = new FormData();
    formData.append('_method', 'put');
    formData.append('product_name', String(submitData.product_name));
    formData.append('description', String(submitData.description));
    formData.append('base_price', String(submitData.base_price));
    formData.append('stock_quantity', String(submitData.stock_quantity));
    formData.append('category_id', String(submitData.category_id));
    formData.append('status', String(submitData.status));
    
    // Append delete images
    submitData.delete_images.forEach((id, idx) => {
      formData.append(`delete_images[${idx}]`, String(id));
    });
    
    // Append new product images
    submitData.images.forEach((file) => {
      formData.append('images[]', file);
    });
    
    // Append variants (nested fields + variant images)
    submitData.variants.forEach((v, vi) => {
      if (v.id !== undefined) {
        formData.append(`variants[${vi}][id]`, String(v.id));
      }
      formData.append(`variants[${vi}][size]`, String(v.size ?? ''));
      formData.append(`variants[${vi}][color]`, String(v.color ?? ''));
      formData.append(`variants[${vi}][stock_quantity]`, String(v.stock_quantity ?? 0));
      
      // existing image ids (if any) so backend can keep them
      (v.existing_images || []).forEach((ei, eidx) => {
        formData.append(`variants[${vi}][existing_images][${eidx}]`, String(ei.id));
      });
      
      // new images for this variant
      (v.images || []).forEach((file) => {
        formData.append(`variants[${vi}][images][]`, file);
      });
    });
    
    // Send FormData directly (typed as FormData)
    router.post(update.url({ product: productId }), formData as FormData, {
      onSuccess: () => {
        console.log('Product updated successfully!');
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
              type="button"
              onClick={() => handleSubmit()}
              disabled={processing}
              className="flex items-center justify-center gap-2 h-10 px-5 bg-primary text-white rounded-lg font-semibold text-sm hover:bg-primary/90 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <Save className="w-5 h-5" />
              <span>{processing ? 'Đang cập nhật...' : 'Cập nhật sản phẩm'}</span>
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
              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
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
              <h3 className="text-lg font-semibold mb-4 text-text-primary-light dark:text-text-primary-dark">
                Biến thể sản phẩm
              </h3>
              <div className="space-y-6">
                {variants.map((variant, index) => (
                  <div key={index} className="p-4 bg-background-light dark:bg-background-dark rounded-lg border border-border-light dark:border-border-dark">
                    <div className="grid grid-cols-12 gap-4 items-end mb-4">
                      <div className="col-span-4">
                        {index === 0 && (
                          <label className="block text-sm font-medium text-text-primary-light dark:text-text-primary-dark mb-2">
                            Kích cỡ
                          </label>
                        )}
                        <input
                          type="text"
                          value={variant.size}
                          onChange={(e) => handleVariantChange(index, 'size', e.target.value)}
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
                          onChange={(e) => handleVariantChange(index, 'color', e.target.value)}
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
                          value={variant.stock_quantity}
                          onChange={(e) => handleVariantChange(index, 'stock_quantity', parseInt(e.target.value) || 0)}
                          className="form-input w-full h-10 px-4 rounded-lg bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark focus:ring-2 focus:ring-primary/50 focus:border-primary/50"
                          placeholder="0"
                        />
                      </div>
                      <div className="col-span-1">
                        <button
                          type="button"
                          onClick={() => handleRemoveVariant(index)}
                          className="flex items-center justify-center h-10 w-10 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                        >
                          <Trash2 className="w-5 h-5" />
                        </button>
                      </div>
                    </div>
                    
                    {/* Variant Images */}
                    <div className="mt-3">
                      <label className="block text-sm font-medium text-text-primary-light dark:text-text-primary-dark mb-2">
                        Hình ảnh biến thể (giúp khách hàng dễ dàng nhận diện)
                      </label>
                      <div className="flex flex-wrap gap-2">
                        {/* Existing variant images */}
                        {variant.existing_images && variant.existing_images.map((img) => (
                          <div key={`existing-${img.id}`} className="relative w-20 h-20">
                            <img
                              src={img.url}
                              alt={`Variant ${index} - Existing`}
                              className="w-full h-full object-cover rounded-lg border-2 border-border-light dark:border-border-dark"
                              onError={(e) => {
                                e.currentTarget.src = 'https://via.placeholder.com/80?text=No+Image';
                              }}
                            />
                            <button
                              type="button"
                              onClick={() => handleRemoveExistingVariantImage(index, img.id)}
                              className="absolute -top-2 -right-2 p-1 bg-red-500 text-white rounded-full hover:bg-red-600 transition-colors"
                            >
                              <X className="w-3 h-3" />
                            </button>
                          </div>
                        ))}
                        {/* New variant images */}
                        {variant.images && variant.images.map((img, imgIdx) => (
                          <div key={`new-${imgIdx}`} className="relative w-20 h-20">
                            <img
                              src={URL.createObjectURL(img)}
                              alt={`Variant ${index} - New ${imgIdx}`}
                              className="w-full h-full object-cover rounded-lg border-2 border-border-light dark:border-border-dark"
                            />
                            <button
                              type="button"
                              onClick={() => handleRemoveVariantImage(index, imgIdx)}
                              className="absolute -top-2 -right-2 p-1 bg-red-500 text-white rounded-full hover:bg-red-600 transition-colors"
                            >
                              <X className="w-3 h-3" />
                            </button>
                          </div>
                        ))}
                        <label className="w-20 h-20 flex items-center justify-center border-2 border-dashed border-border-light dark:border-border-dark rounded-lg cursor-pointer hover:border-primary transition-colors">
                          <ImageIcon className="w-6 h-6 text-text-secondary-light dark:text-text-secondary-dark" />
                          <input
                            type="file"
                            multiple
                            accept="image/*"
                            className="sr-only"
                            onChange={(e) => handleVariantImageChange(index, e)}
                          />
                        </label>
                      </div>
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
              
              {/* Show existing images and new images */}
              {(productImages.length > 0 || imagePreviewUrls.length > 0) && (
                <div className="mt-4 grid grid-cols-3 gap-2">
                  {/* Existing images */}
                  {productImages
                    .filter((img) => !imagesToDelete.includes(img.id))
                    .map((img, idx) => (
                      <div key={`existing-${img.id}`} className="relative aspect-square group">
                        <img
                          src={img.url}
                          alt={`Product ${idx + 1}`}
                          className="w-full h-full object-cover rounded-lg border-2 border-border-light dark:border-border-dark"
                          onError={(e) => {
                            e.currentTarget.src = 'https://via.placeholder.com/300x300?text=No+Image';
                          }}
                        />
                        {img.is_primary && (
                          <span className="absolute top-2 left-2 px-2 py-1 bg-primary text-white text-xs font-semibold rounded">
                            Chính
                          </span>
                        )}
                        <button
                          type="button"
                          onClick={() => handleDeleteImage(img.id)}
                          className="absolute -top-2 -right-2 p-1.5 bg-red-500 text-white rounded-full hover:bg-red-600 transition-colors opacity-0 group-hover:opacity-100"
                        >
                          <X className="w-4 h-4" />
                        </button>
                      </div>
                    ))}
                  
                  {/* New image previews */}
                  {imagePreviewUrls.map((url, idx) => {
                    const isFirstNewImage = productImages.filter((img) => !imagesToDelete.includes(img.id)).length === 0 && idx === 0;
                    return (
                      <div key={`new-${idx}`} className="relative aspect-square group">
                        <img
                          src={url}
                          alt={`New Preview ${idx + 1}`}
                          className="w-full h-full object-cover rounded-lg border-2 border-border-light dark:border-border-dark"
                        />
                        {isFirstNewImage && (
                          <span className="absolute top-2 left-2 px-2 py-1 bg-primary text-white text-xs font-semibold rounded">
                            Chính
                          </span>
                        )}
                        <button
                          type="button"
                          onClick={() => handleRemoveNewImage(idx)}
                          className="absolute -top-2 -right-2 p-1.5 bg-red-500 text-white rounded-full hover:bg-red-600 transition-colors opacity-0 group-hover:opacity-100"
                        >
                          <X className="w-4 h-4" />
                        </button>
                        <div className="absolute bottom-0 left-0 right-0 bg-black/50 text-white text-xs p-1 text-center rounded-b-lg opacity-0 group-hover:opacity-100 transition-opacity truncate">
                          {data.images[idx]?.name}
                        </div>
                      </div>
                    );
                  })}
                </div>
              )}
              
              {(data.images.length > 0 || productImages.filter((img) => !imagesToDelete.includes(img.id)).length > 0) && (
                <p className="mt-3 text-sm text-text-secondary-light dark:text-text-secondary-dark">
                  {productImages.filter((img) => !imagesToDelete.includes(img.id)).length > 0 && (
                    <span>{productImages.filter((img) => !imagesToDelete.includes(img.id)).length} ảnh hiện có. </span>
                  )}
                  {data.images.length > 0 && (
                    <span>{data.images.length} ảnh mới được chọn. </span>
                  )}
                  Ảnh đầu tiên sẽ là ảnh chính.
                </p>
              )}
            </div>
          </div>
        </form>
      </div>
    </SellerLayout>
  );
}
