import React, { useState, useEffect } from 'react';
import { Trash2, Upload, X } from 'lucide-react';
import type { Attribute, AttributeValue } from './AttributeSelector';

interface VariantCombination {
  attributes: Record<string, AttributeValue>;
  display_name: string;
}

interface ProductVariant {
  attributes: Record<string, AttributeValue>;
  price: string;
  stock_quantity: number;
  sku?: string;
  images: File[];
}

interface VariantMatrixGeneratorProps {
  variantAttributes: Attribute[];
  basePrice: string;
  variants: ProductVariant[];
  onChange: (variants: ProductVariant[]) => void;
}

export default function VariantMatrixGenerator({
  variantAttributes,
  basePrice,
  variants,
  onChange,
}: VariantMatrixGeneratorProps) {
  const [selectedOptions, setSelectedOptions] = useState<Record<number, number[]>>({});
  const [isGenerating, setIsGenerating] = useState(false);
  const [imagePreviewUrls, setImagePreviewUrls] = useState<Record<number, string[]>>({});

  // Initialize selected options from existing variants
  useEffect(() => {
    if (variants.length > 0 && Object.keys(selectedOptions).length === 0) {
      const options: Record<number, number[]> = {};
      
      variants.forEach(variant => {
        Object.entries(variant.attributes).forEach(([attrId, value]) => {
          const attributeId = parseInt(attrId);
          if (value.attribute_option_id) {
            if (!options[attributeId]) {
              options[attributeId] = [];
            }
            if (!options[attributeId].includes(value.attribute_option_id)) {
              options[attributeId].push(value.attribute_option_id);
            }
          }
        });
      });
      
      setSelectedOptions(options);
    }
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [variants]);

  const handleOptionToggle = (attributeId: number, optionId: number) => {
    setSelectedOptions(prev => {
      const current = prev[attributeId] || [];
      const isSelected = current.includes(optionId);
      
      return {
        ...prev,
        [attributeId]: isSelected
          ? current.filter(id => id !== optionId)
          : [...current, optionId],
      };
    });
  };

  const generateVariants = async () => {
    setIsGenerating(true);
    
    try {
      // Call API to generate combinations
      const response = await fetch('/api/attributes/generate-variants', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')?.content || '',
        },
        body: JSON.stringify({
          variant_attributes: selectedOptions,
        }),
      });

      if (!response.ok) {
        throw new Error('Failed to generate variants');
      }

      const data = await response.json();
      const combinations: VariantCombination[] = data.combinations;

      // Create new variants from combinations, preserving existing variant data if combination exists
      const newVariants: ProductVariant[] = combinations.map(combo => {
        // Check if this combination already exists
        const existing = variants.find(v => {
          return Object.entries(combo.attributes).every(([attrId, value]) => {
            const vAttr = v.attributes[attrId];
            return vAttr?.attribute_option_id === value.attribute_option_id;
          });
        });

        if (existing) {
          // Keep existing data
          return existing;
        }

        // Create new variant
        return {
          attributes: combo.attributes,
          price: basePrice || '0',
          stock_quantity: 0,
          images: [],
        };
      });

      onChange(newVariants);
    } catch (error) {
      console.error('Error generating variants:', error);
      alert('Không thể tạo biến thể. Vui lòng thử lại.');
    } finally {
      setIsGenerating(false);
    }
  };

  const handleVariantChange = (index: number, field: 'price' | 'stock_quantity' | 'sku', value: string | number) => {
    const updated = [...variants];
    updated[index] = { ...updated[index], [field]: value };
    onChange(updated);
  };

  const handleVariantImageChange = (index: number, e: React.ChangeEvent<HTMLInputElement>) => {
    if (e.target.files) {
      const files = Array.from(e.target.files);
      const updated = [...variants];
      updated[index] = {
        ...updated[index],
        images: [...updated[index].images, ...files],
      };
      onChange(updated);

      // Create preview URLs
      const newPreviewUrls = files.map(file => URL.createObjectURL(file));
      setImagePreviewUrls(prev => ({
        ...prev,
        [index]: [...(prev[index] || []), ...newPreviewUrls],
      }));
    }
  };

  const handleRemoveVariantImage = (variantIndex: number, imageIndex: number) => {
    const updated = [...variants];
    
    // Revoke URL
    const previewUrl = imagePreviewUrls[variantIndex]?.[imageIndex];
    if (previewUrl) {
      URL.revokeObjectURL(previewUrl);
    }

    // Remove image
    updated[variantIndex] = {
      ...updated[variantIndex],
      images: updated[variantIndex].images.filter((_, i) => i !== imageIndex),
    };
    onChange(updated);

    // Update preview URLs
    setImagePreviewUrls(prev => ({
      ...prev,
      [variantIndex]: (prev[variantIndex] || []).filter((_, i) => i !== imageIndex),
    }));
  };

  const handleRemoveVariant = (index: number) => {
    // Revoke all URLs for this variant
    const urls = imagePreviewUrls[index] || [];
    urls.forEach(url => URL.revokeObjectURL(url));

    const updated = variants.filter((_, i) => i !== index);
    onChange(updated);

    // Clean up preview URLs
    setImagePreviewUrls(prev => {
      const newState = { ...prev };
      delete newState[index];
      return newState;
    });
  };

  const getOptionLabel = (attributeId: number, optionId: number): string => {
    const attr = variantAttributes.find(a => a.id === attributeId);
    const option = attr?.options.find(o => o.id === optionId);
    return option?.label || option?.value || 'N/A';
  };

  const getVariantDisplayName = (variant: ProductVariant): string => {
    const names = Object.entries(variant.attributes).map(([attrId, value]) => {
      if (value.attribute_option_id) {
        return getOptionLabel(parseInt(attrId), value.attribute_option_id);
      }
      return value.text_value || '';
    });
    return names.filter(Boolean).join(' - ') || 'Mặc định';
  };

  if (variantAttributes.length === 0) {
    return (
      <div className="text-sm text-text-secondary-light dark:text-text-secondary-dark">
        Danh mục này không có thuộc tính biến thể
      </div>
    );
  }

  return (
    <div className="space-y-6">
      {/* Attribute Options Selection */}
      <div className="space-y-4">
        <h4 className="text-sm font-semibold text-text-primary-light dark:text-text-primary-dark">
          Chọn các tùy chọn cho mỗi thuộc tính
        </h4>
        {variantAttributes.map(attribute => (
          <div key={attribute.id} className="space-y-2">
            <label className="block text-sm font-medium text-text-primary-light dark:text-text-primary-dark">
              {attribute.name}
            </label>
            <div className="flex flex-wrap gap-2">
              {attribute.options.map(option => (
                <button
                  key={option.id}
                  type="button"
                  onClick={() => handleOptionToggle(attribute.id, option.id)}
                  className={`px-3 py-1.5 rounded-lg text-sm font-medium transition-colors ${
                    selectedOptions[attribute.id]?.includes(option.id)
                      ? 'bg-primary text-white'
                      : 'bg-background-light dark:bg-background-dark border border-border-light dark:border-border-dark text-text-primary-light dark:text-text-primary-dark hover:bg-black/5 dark:hover:bg-white/5'
                  }`}
                >
                  {option.label}
                  {option.color_code && (
                    <span
                      className="inline-block w-3 h-3 rounded-full ml-2"
                      style={{ backgroundColor: option.color_code }}
                    />
                  )}
                </button>
              ))}
            </div>
          </div>
        ))}
      </div>

      {/* Generate Button */}
      <button
        type="button"
        onClick={generateVariants}
        disabled={isGenerating || Object.values(selectedOptions).every(arr => arr.length === 0)}
        className="w-full h-10 px-4 bg-primary text-white rounded-lg font-semibold text-sm hover:bg-primary/90 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
      >
        {isGenerating ? 'Đang tạo...' : 'Tạo biến thể'}
      </button>

      {/* Variant Matrix */}
      {variants.length > 0 && (
        <div className="space-y-4">
          <h4 className="text-sm font-semibold text-text-primary-light dark:text-text-primary-dark">
            Danh sách biến thể ({variants.length})
          </h4>
          <div className="space-y-3">
            {variants.map((variant, index) => (
              <div
                key={index}
                className="p-4 bg-background-light dark:bg-background-dark rounded-lg border border-border-light dark:border-border-dark space-y-3"
              >
                {/* Variant Name */}
                <div className="flex items-center justify-between">
                  <h5 className="font-medium text-text-primary-light dark:text-text-primary-dark">
                    {getVariantDisplayName(variant)}
                  </h5>
                  <button
                    type="button"
                    onClick={() => handleRemoveVariant(index)}
                    className="p-1 text-red-500 hover:bg-red-500/10 rounded"
                  >
                    <Trash2 className="w-4 h-4" />
                  </button>
                </div>

                {/* Price, Stock, SKU */}
                <div className="grid grid-cols-3 gap-3">
                  <div>
                    <label className="block text-xs font-medium text-text-secondary-light dark:text-text-secondary-dark mb-1">
                      Giá
                    </label>
                    <input
                      type="text"
                      value={variant.price}
                      onChange={(e) => handleVariantChange(index, 'price', e.target.value)}
                      className="form-input w-full h-9 px-3 text-sm rounded-lg bg-surface-light dark:bg-surface-dark border border-border-light dark:border-border-dark"
                      placeholder="0"
                    />
                  </div>
                  <div>
                    <label className="block text-xs font-medium text-text-secondary-light dark:text-text-secondary-dark mb-1">
                      Tồn kho
                    </label>
                    <input
                      type="number"
                      value={variant.stock_quantity}
                      onChange={(e) => handleVariantChange(index, 'stock_quantity', parseInt(e.target.value) || 0)}
                      className="form-input w-full h-9 px-3 text-sm rounded-lg bg-surface-light dark:bg-surface-dark border border-border-light dark:border-border-dark"
                      placeholder="0"
                    />
                  </div>
                  <div>
                    <label className="block text-xs font-medium text-text-secondary-light dark:text-text-secondary-dark mb-1">
                      SKU (tùy chọn)
                    </label>
                    <input
                      type="text"
                      value={variant.sku || ''}
                      onChange={(e) => handleVariantChange(index, 'sku', e.target.value)}
                      className="form-input w-full h-9 px-3 text-sm rounded-lg bg-surface-light dark:bg-surface-dark border border-border-light dark:border-border-dark"
                      placeholder="SKU"
                    />
                  </div>
                </div>

                {/* Images */}
                <div>
                  <label className="block text-xs font-medium text-text-secondary-light dark:text-text-secondary-dark mb-2">
                    Hình ảnh
                  </label>
                  <div className="flex items-center gap-2 flex-wrap">
                    {/* Existing images */}
                    {(imagePreviewUrls[index] || []).map((url, imgIdx) => (
                      <div key={imgIdx} className="relative group w-20 h-20">
                        <img
                          src={url}
                          alt={`Variant ${index} image ${imgIdx}`}
                          className="w-full h-full object-cover rounded-lg border border-border-light dark:border-border-dark"
                        />
                        <button
                          type="button"
                          onClick={() => handleRemoveVariantImage(index, imgIdx)}
                          className="absolute -top-1 -right-1 p-1 bg-red-500 text-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity"
                        >
                          <X className="w-3 h-3" />
                        </button>
                      </div>
                    ))}

                    {/* Upload button */}
                    <label className="w-20 h-20 flex flex-col items-center justify-center border-2 border-dashed border-border-light dark:border-border-dark rounded-lg cursor-pointer hover:bg-black/5 dark:hover:bg-white/5 transition-colors">
                      <Upload className="w-5 h-5 text-text-secondary-light dark:text-text-secondary-dark" />
                      <span className="text-xs text-text-secondary-light dark:text-text-secondary-dark mt-1">
                        Thêm
                      </span>
                      <input
                        type="file"
                        multiple
                        accept="image/*"
                        onChange={(e) => handleVariantImageChange(index, e)}
                        className="hidden"
                      />
                    </label>
                  </div>
                </div>
              </div>
            ))}
          </div>
        </div>
      )}
    </div>
  );
}
