import React from 'react';

export interface AttributeOption {
  id: number;
  value: string;
  label: string;
  color_code?: string;
}

export interface Attribute {
  id: number;
  name: string;
  slug: string;
  input_type: 'select' | 'text' | 'number' | 'boolean' | 'date' | 'textarea' | 'color';
  description?: string;
  is_required: boolean;
  options: AttributeOption[];
}

export interface AttributeValue {
  attribute_option_id?: number;
  text_value?: string;
}

interface AttributeSelectorProps {
  attributes: Attribute[];
  values: Record<string, AttributeValue>;
  onChange: (attributeId: number, value: AttributeValue) => void;
  errors?: Record<string, string>;
}

export default function AttributeSelector({
  attributes,
  values,
  onChange,
  errors = {},
}: AttributeSelectorProps) {
  if (attributes.length === 0) {
    return (
      <div className="text-sm text-text-secondary-light dark:text-text-secondary-dark">
        Chọn danh mục để hiển thị thuộc tính
      </div>
    );
  }

  const renderInput = (attribute: Attribute) => {
    const value = values[attribute.id] || {};
    const error = errors[`attributes.${attribute.id}`];

    switch (attribute.input_type) {
      case 'select':
        return (
          <div key={attribute.id} className="space-y-2">
            <label className="block text-sm font-medium text-text-primary-light dark:text-text-primary-dark">
              {attribute.name}
              {attribute.is_required && <span className="text-red-500 ml-1">*</span>}
            </label>
            {attribute.description && (
              <p className="text-xs text-text-secondary-light dark:text-text-secondary-dark">
                {attribute.description}
              </p>
            )}
            <select
              value={value.attribute_option_id || ''}
              onChange={(e) =>
                onChange(attribute.id, {
                  attribute_option_id: e.target.value ? parseInt(e.target.value) : undefined,
                })
              }
              className={`form-select w-full h-10 px-4 rounded-lg bg-background-light dark:bg-background-dark border ${
                error ? 'border-red-500' : 'border-border-light dark:border-border-dark'
              } focus:ring-2 focus:ring-primary/50 focus:border-primary/50`}
            >
              <option value="">-- Chọn {attribute.name.toLowerCase()} --</option>
              {attribute.options.map((option) => (
                <option key={option.id} value={option.id}>
                  {option.label}
                </option>
              ))}
            </select>
            {error && <p className="text-xs text-red-500">{error}</p>}
          </div>
        );

      case 'text':
      case 'number':
        return (
          <div key={attribute.id} className="space-y-2">
            <label className="block text-sm font-medium text-text-primary-light dark:text-text-primary-dark">
              {attribute.name}
              {attribute.is_required && <span className="text-red-500 ml-1">*</span>}
            </label>
            {attribute.description && (
              <p className="text-xs text-text-secondary-light dark:text-text-secondary-dark">
                {attribute.description}
              </p>
            )}
            <input
              type={attribute.input_type}
              value={value.text_value || ''}
              onChange={(e) =>
                onChange(attribute.id, {
                  text_value: e.target.value,
                })
              }
              className={`form-input w-full h-10 px-4 rounded-lg bg-background-light dark:bg-background-dark border ${
                error ? 'border-red-500' : 'border-border-light dark:border-border-dark'
              } focus:ring-2 focus:ring-primary/50 focus:border-primary/50`}
              placeholder={`Nhập ${attribute.name.toLowerCase()}...`}
            />
            {error && <p className="text-xs text-red-500">{error}</p>}
          </div>
        );

      case 'textarea':
        return (
          <div key={attribute.id} className="space-y-2">
            <label className="block text-sm font-medium text-text-primary-light dark:text-text-primary-dark">
              {attribute.name}
              {attribute.is_required && <span className="text-red-500 ml-1">*</span>}
            </label>
            {attribute.description && (
              <p className="text-xs text-text-secondary-light dark:text-text-secondary-dark">
                {attribute.description}
              </p>
            )}
            <textarea
              rows={3}
              value={value.text_value || ''}
              onChange={(e) =>
                onChange(attribute.id, {
                  text_value: e.target.value,
                })
              }
              className={`form-textarea w-full px-4 py-2 rounded-lg bg-background-light dark:bg-background-dark border ${
                error ? 'border-red-500' : 'border-border-light dark:border-border-dark'
              } focus:ring-2 focus:ring-primary/50 focus:border-primary/50`}
              placeholder={`Nhập ${attribute.name.toLowerCase()}...`}
            />
            {error && <p className="text-xs text-red-500">{error}</p>}
          </div>
        );

      case 'boolean':
        return (
          <div key={attribute.id} className="flex items-center space-x-3">
            <input
              type="checkbox"
              id={`attr-${attribute.id}`}
              checked={value.text_value === 'true' || value.text_value === '1'}
              onChange={(e) =>
                onChange(attribute.id, {
                  text_value: e.target.checked ? '1' : '0',
                })
              }
              className="form-checkbox h-4 w-4 text-primary rounded border-border-light dark:border-border-dark focus:ring-primary"
            />
            <label
              htmlFor={`attr-${attribute.id}`}
              className="text-sm font-medium text-text-primary-light dark:text-text-primary-dark"
            >
              {attribute.name}
              {attribute.is_required && <span className="text-red-500 ml-1">*</span>}
            </label>
            {attribute.description && (
              <p className="text-xs text-text-secondary-light dark:text-text-secondary-dark">
                ({attribute.description})
              </p>
            )}
            {error && <p className="text-xs text-red-500">{error}</p>}
          </div>
        );

      case 'date':
        return (
          <div key={attribute.id} className="space-y-2">
            <label className="block text-sm font-medium text-text-primary-light dark:text-text-primary-dark">
              {attribute.name}
              {attribute.is_required && <span className="text-red-500 ml-1">*</span>}
            </label>
            {attribute.description && (
              <p className="text-xs text-text-secondary-light dark:text-text-secondary-dark">
                {attribute.description}
              </p>
            )}
            <input
              type="date"
              value={value.text_value || ''}
              onChange={(e) =>
                onChange(attribute.id, {
                  text_value: e.target.value,
                })
              }
              className={`form-input w-full h-10 px-4 rounded-lg bg-background-light dark:bg-background-dark border ${
                error ? 'border-red-500' : 'border-border-light dark:border-border-dark'
              } focus:ring-2 focus:ring-primary/50 focus:border-primary/50`}
            />
            {error && <p className="text-xs text-red-500">{error}</p>}
          </div>
        );

      case 'color':
        return (
          <div key={attribute.id} className="space-y-2">
            <label className="block text-sm font-medium text-text-primary-light dark:text-text-primary-dark">
              {attribute.name}
              {attribute.is_required && <span className="text-red-500 ml-1">*</span>}
            </label>
            {attribute.description && (
              <p className="text-xs text-text-secondary-light dark:text-text-secondary-dark">
                {attribute.description}
              </p>
            )}
            <div className="flex items-center space-x-3">
              <input
                type="color"
                value={value.text_value || '#000000'}
                onChange={(e) =>
                  onChange(attribute.id, {
                    text_value: e.target.value,
                  })
                }
                className="h-10 w-20 rounded-lg border border-border-light dark:border-border-dark cursor-pointer"
              />
              <input
                type="text"
                value={value.text_value || ''}
                onChange={(e) =>
                  onChange(attribute.id, {
                    text_value: e.target.value,
                  })
                }
                className={`form-input flex-1 h-10 px-4 rounded-lg bg-background-light dark:bg-background-dark border ${
                  error ? 'border-red-500' : 'border-border-light dark:border-border-dark'
                } focus:ring-2 focus:ring-primary/50 focus:border-primary/50`}
                placeholder="#000000"
              />
            </div>
            {error && <p className="text-xs text-red-500">{error}</p>}
          </div>
        );

      default:
        return null;
    }
  };

  return (
    <div className="space-y-4">
      {attributes.map((attribute) => renderInput(attribute))}
    </div>
  );
}
