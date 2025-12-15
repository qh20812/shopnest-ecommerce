<?php

namespace App\Http\Requests\Sellers;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $product = $this->route('product');
        $user = $this->user();

        if (!$user || !$user->isSeller()) {
            return false;
        }

        // Check if the product belongs to the seller's shop
        $shop = $user->shops()->first();
        return $shop && $product->shop_id === $shop->id;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        \Log::info('UpdateProductRequest - Raw input data', [
            'all' => $this->all(),
            'files' => $this->allFiles(),
        ]);
        
        return [
            'product_name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'base_price' => ['nullable', 'string'],
            'compare_price' => ['nullable', 'string'],
            'stock_quantity' => ['nullable', 'integer', 'min:0'],
            'status' => ['nullable', 'string', 'in:active,inactive,draft'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'length' => ['nullable', 'numeric', 'min:0'],
            'width' => ['nullable', 'numeric', 'min:0'],
            'height' => ['nullable', 'numeric', 'min:0'],
            'is_featured' => ['nullable', 'boolean'],
            
            // Variants
            'variants' => ['nullable', 'array'],
            'variants.*.id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'variants.*.size' => ['nullable', 'string', 'max:50'],
            'variants.*.color' => ['nullable', 'string', 'max:50'],
            'variants.*.stock_quantity' => ['required_with:variants', 'integer', 'min:0'],
            'variants.*.price' => ['nullable', 'string'],
            'variants.*.sku' => ['nullable', 'string', 'max:100'],
            'variants.*.images' => ['nullable', 'array', 'max:5'],
            'variants.*.images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            
            // Images
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'], // 5MB
            
            // Image deletions
            'delete_images' => ['nullable', 'array'],
            'delete_images.*' => ['integer', 'exists:product_images,id'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'product_name' => 'tên sản phẩm',
            'description' => 'mô tả',
            'category_id' => 'danh mục',
            'base_price' => 'giá',
            'compare_price' => 'giá so sánh',
            'stock_quantity' => 'số lượng tồn kho',
            'status' => 'trạng thái',
            'weight' => 'trọng lượng',
            'length' => 'chiều dài',
            'width' => 'chiều rộng',
            'height' => 'chiều cao',
            'variants' => 'biến thể',
            'images' => 'hình ảnh',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'product_name.required' => 'Vui lòng nhập tên sản phẩm.',
            'product_name.max' => 'Tên sản phẩm không được vượt quá 255 ký tự.',
            'description.max' => 'Mô tả không được vượt quá 5000 ký tự.',
            'category_id.exists' => 'Danh mục không tồn tại.',
            'base_price.required' => 'Vui lòng nhập giá sản phẩm.',
            'stock_quantity.required' => 'Vui lòng nhập số lượng tồn kho.',
            'stock_quantity.min' => 'Số lượng tồn kho không được nhỏ hơn 0.',
            'status.required' => 'Vui lòng chọn trạng thái.',
            'status.in' => 'Trạng thái không hợp lệ.',
            'images.*.image' => 'File phải là hình ảnh.',
            'images.*.mimes' => 'Hình ảnh phải có định dạng: jpg, jpeg, png, webp.',
            'images.*.max' => 'Kích thước hình ảnh không được vượt quá 5MB.',
        ];
    }
}
