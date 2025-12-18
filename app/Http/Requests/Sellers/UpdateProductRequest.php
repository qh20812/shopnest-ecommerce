<?php

namespace App\Http\Requests\Sellers;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

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

        // Check if the product belongs to any of the seller's shops
        // This is safer when a seller may have multiple shops
        if (!$product) {
            return false;
        }

        return $user->shops()->where('id', $product->shop_id)->exists();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        // Reduce verbosity for production; use debug and only log counts
        Log::debug('UpdateProductRequest - input summary', [
            'keys' => array_keys($this->all()),
            'images_count' => $this->hasFile('images') ? count($this->file('images')) : 0,
            'variants_count' => is_array($this->all()['variants'] ?? null) ? count($this->all()['variants']) : 0,
        ]);

        $product = $this->route('product');
        $productId = $product->id ?? null;
        
        return [
            'product_name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            // Prices: normalize to numeric values in prepareForValidation
            'base_price' => ['nullable', 'numeric', 'min:0'],
            'compare_price' => ['nullable', 'numeric', 'min:0'],
            'stock_quantity' => ['nullable', 'integer', 'min:0'],
            'status' => ['nullable', 'string', 'in:active,inactive,draft'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'length' => ['nullable', 'numeric', 'min:0'],
            'width' => ['nullable', 'numeric', 'min:0'],
            'height' => ['nullable', 'numeric', 'min:0'],
            'is_featured' => ['nullable', 'boolean'],
            
            // Variants
            'variants' => ['nullable', 'array', 'max:10'],
            'variants.*.id' => [
                'nullable',
                'integer',
                // ensure variant belongs to the product being updated
                Rule::exists('product_variants', 'id')->where('product_id', $productId),
            ],
            'variants.*.size' => ['nullable', 'string', 'max:50'],
            'variants.*.color' => ['nullable', 'string', 'max:50'],
            'variants.*.stock_quantity' => ['sometimes', 'required', 'integer', 'min:0'],
            'variants.*.price' => ['nullable', 'numeric', 'min:0'],
            'variants.*.sku' => ['nullable', 'string', 'max:100'],
            'variants.*.images' => ['nullable', 'array', 'max:5'],
            'variants.*.images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:10240'], // 10MB
            
            // Images
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:10240'], // 10MB
            
            // Image deletions
            'delete_images' => ['nullable', 'array'],
            'delete_images.*' => [
                'integer',
                Rule::exists('product_images', 'id')->where('product_id', $productId),
            ],
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
            'variants.*.stock_quantity' => 'số lượng biến thể',
            'variants.*.images.*' => 'hình ảnh biến thể',
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
            'images.*.max' => 'Kích thước hình ảnh không được vượt quá 10MB.',
            'variants.*.images.*.mimes' => 'Hình ảnh biến thể phải có định dạng: jpg, jpeg, png, webp.',
            'variants.*.images.*.max' => 'Kích thước hình ảnh biến thể không được vượt quá 10MB.',
            'variants.*.stock_quantity.required' => 'Vui lòng nhập số lượng tồn kho cho biến thể.',
        ];
    }

    /**
     * Prepare the data for validation.
     * Normalize price inputs (remove formatting like commas/currency symbols).
     */
    protected function prepareForValidation(): void
    {
        $input = $this->all();

        if (isset($input['base_price'])) {
            $input['base_price'] = $this->normalizePrice($input['base_price']);
        }

        if (isset($input['compare_price'])) {
            $input['compare_price'] = $this->normalizePrice($input['compare_price']);
        }

        // Normalize variant prices if provided
        if (!empty($input['variants']) && is_array($input['variants'])) {
            foreach ($input['variants'] as $i => $variant) {
                if (isset($variant['price'])) {
                    $input['variants'][$i]['price'] = $this->normalizePrice($variant['price']);
                }
            }
        }

        $this->merge($input);
    }

    /**
     * Remove any non-numeric characters except dot to convert to float-like string
     */
    private function normalizePrice($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        // remove everything except digits (strip thousand separators and currency symbols)
        $clean = preg_replace('/[^0-9]/', '', (string) $value);

        return $clean === '' ? null : $clean;
    }
}
