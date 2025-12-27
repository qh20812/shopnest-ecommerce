<?php

namespace App\Services;

use App\Models\Attribute;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttributeValue;
use App\Models\ProductVariant;
use App\Models\ProductVariantAttributeValue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AttributeService
{
    /**
     * Get attributes for a specific category with options.
     */
    public function getCategoryAttributes(int $categoryId): Collection
    {
        $category = Category::with([
            'attributes' => function ($query) {
                $query->active()->with(['options' => function ($q) {
                    $q->active()->orderBy('sort_order');
                }]);
            }
        ])->find($categoryId);

        if (!$category) {
            return collect();
        }

        return $category->attributes->map(function ($attribute) {
            return [
                'id' => $attribute->id,
                'name' => $attribute->name,
                'slug' => $attribute->slug,
                'input_type' => $attribute->input_type,
                'description' => $attribute->description,
                'is_variant' => $attribute->pivot->is_variant,
                'is_required' => $attribute->pivot->is_required,
                'is_filterable' => $attribute->pivot->is_filterable,
                'sort_order' => $attribute->pivot->sort_order,
                'options' => $attribute->options->map(function ($option) {
                    return [
                        'id' => $option->id,
                        'value' => $option->value,
                        'label' => $option->label ?? $option->value,
                        'color_code' => $option->color_code,
                    ];
                }),
            ];
        });
    }

    /**
     * Get variant attributes for a category.
     */
    public function getCategoryVariantAttributes(int $categoryId): Collection
    {
        $category = Category::with([
            'attributes' => function ($query) {
                $query->active()
                    ->wherePivot('is_variant', true)
                    ->with(['options' => function ($q) {
                        $q->active()->orderBy('sort_order');
                    }]);
            }
        ])->find($categoryId);

        if (!$category) {
            return collect();
        }

        return $category->attributes;
    }

    /**
     * Get specification attributes for a category.
     */
    public function getCategorySpecificationAttributes(int $categoryId): Collection
    {
        $category = Category::with([
            'attributes' => function ($query) {
                $query->active()
                    ->wherePivot('is_variant', false)
                    ->with(['options' => function ($q) {
                        $q->active()->orderBy('sort_order');
                    }]);
            }
        ])->find($categoryId);

        if (!$category) {
            return collect();
        }

        return $category->attributes;
    }

    /**
     * Save product attribute values (specifications).
     */
    public function saveProductAttributes(Product $product, array $attributes): void
    {
        DB::transaction(function () use ($product, $attributes) {
            // Delete existing attributes
            $product->attributeValues()->delete();

            // Save new attributes
            foreach ($attributes as $attributeId => $value) {
                if (empty($value)) {
                    continue;
                }

                $attribute = Attribute::find($attributeId);
                if (!$attribute) {
                    continue;
                }

                $data = [
                    'product_id' => $product->id,
                    'attribute_id' => $attributeId,
                    'value' => is_array($value) ? $value['value'] : $value,
                ];

                // If select type and option_id provided
                if ($attribute->isSelectType() && is_array($value) && isset($value['option_id'])) {
                    $data['attribute_option_id'] = $value['option_id'];
                }

                ProductAttributeValue::create($data);
            }
        });
    }

    /**
     * Save variant attribute values.
     */
    public function saveVariantAttributes(ProductVariant $variant, array $attributes): void
    {
        DB::transaction(function () use ($variant, $attributes) {
            // Delete existing variant attributes
            $variant->variantAttributeValues()->delete();

            // Save new attributes
            foreach ($attributes as $attributeId => $value) {
                if (empty($value)) {
                    continue;
                }

                $attribute = Attribute::find($attributeId);
                if (!$attribute) {
                    continue;
                }

                $data = [
                    'product_variant_id' => $variant->id,
                    'attribute_id' => $attributeId,
                    'value' => is_array($value) ? $value['value'] : $value,
                ];

                // If select type and option_id provided
                if ($attribute->isSelectType() && is_array($value) && isset($value['option_id'])) {
                    $data['attribute_option_id'] = $value['option_id'];
                }

                ProductVariantAttributeValue::create($data);
            }
        });
    }

    /**
     * Generate variant combinations from selected attribute options.
     * 
     * @param array $variantAttributeOptions [attribute_id => [option_ids]]
     * @return array
     */
    public function generateVariantCombinations(array $variantAttributeOptions): array
    {
        if (empty($variantAttributeOptions)) {
            return [];
        }

        // Get all attribute names and option values
        $attributeData = [];
        foreach ($variantAttributeOptions as $attributeId => $optionIds) {
            $attribute = Attribute::with('options')->find($attributeId);
            if (!$attribute) {
                continue;
            }

            $options = $attribute->options->whereIn('id', $optionIds)->all();
            $attributeData[$attributeId] = [
                'name' => $attribute->name,
                'slug' => $attribute->slug,
                'options' => collect($options)->map(fn($opt) => [
                    'id' => $opt->id,
                    'value' => $opt->value,
                    'label' => $opt->label ?? $opt->value,
                ])->all(),
            ];
        }

        // Generate cartesian product
        $combinations = $this->cartesianProduct($attributeData);

        return $combinations;
    }

    /**
     * Generate cartesian product of attributes.
     */
    private function cartesianProduct(array $attributeData): array
    {
        $result = [[]];

        foreach ($attributeData as $attributeId => $data) {
            $temp = [];
            foreach ($result as $combination) {
                foreach ($data['options'] as $option) {
                    $newCombination = $combination;
                    $newCombination[$attributeId] = [
                        'attribute_name' => $data['name'],
                        'attribute_slug' => $data['slug'],
                        'option_id' => $option['id'],
                        'value' => $option['value'],
                        'label' => $option['label'],
                    ];
                    $temp[] = $newCombination;
                }
            }
            $result = $temp;
        }

        return $result;
    }

    /**
     * Get display name for variant from attributes.
     */
    public function getVariantDisplayName(array $attributeValues): string
    {
        $values = collect($attributeValues)->pluck('value')->toArray();
        return implode(' / ', $values);
    }

    /**
     * Validate if variant combination already exists for a product.
     */
    public function variantCombinationExists(Product $product, array $attributeCombination, ?int $excludeVariantId = null): bool
    {
        $query = $product->variants()
            ->whereHas('variantAttributeValues', function ($q) use ($attributeCombination) {
                foreach ($attributeCombination as $attributeId => $optionId) {
                    $q->where(function ($subQuery) use ($attributeId, $optionId) {
                        $subQuery->where('attribute_id', $attributeId)
                            ->where('attribute_option_id', $optionId);
                    });
                }
            }, '=', count($attributeCombination));

        if ($excludeVariantId) {
            $query->where('id', '!=', $excludeVariantId);
        }

        return $query->exists();
    }
}
