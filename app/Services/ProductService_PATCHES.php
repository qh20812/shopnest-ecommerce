<?php

namespace App\Services;

// Thêm các methods này vào ProductService.php

/**
 * Create product variants with images
 */
protected function createVariants(Product $product, array $variants): void
{
    foreach ($variants as $variantData) {
        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'variant_name' => $this->buildVariantName($variantData),
            'sku' => $variantData['sku'] ?? $this->generateSKU($product),
            'price' => isset($variantData['price']) ? $this->parsePrice($variantData['price']) : $product->base_price,
            'stock_quantity' => $variantData['stock_quantity'] ?? 0,
            'attribute_values' => $this->extractAttributeValues($variantData),
        ]);

        // Handle variant images
        if (!empty($variantData['images'])) {
            \Log::info("Uploading {count} images for variant {$variant->id}", ['count' => count($variantData['images'])]);
            $this->uploadVariantImages($variant, $variantData['images']);
        }
    }
}

/**
 * Update product variants with images
 */
protected function updateVariants(Product $product, array $variants): void
{
    $existingVariantIds = [];

    foreach ($variants as $variantData) {
        if (isset($variantData['id']) && $variantData['id']) {
            // Update existing variant
            $variant = ProductVariant::find($variantData['id']);
            if ($variant && $variant->product_id === $product->id) {
                $variant->update([
                    'variant_name' => $this->buildVariantName($variantData),
                    'price' => isset($variantData['price']) ? $this->parsePrice($variantData['price']) : $product->base_price,
                    'stock_quantity' => $variantData['stock_quantity'] ?? 0,
                    'attribute_values' => $this->extractAttributeValues($variantData),
                ]);

                // Handle new variant images
                if (!empty($variantData['images'])) {
                    $this->uploadVariantImages($variant, $variantData['images']);
                }

                // Handle image deletions
                if (!empty($variantData['delete_images'])) {
                    foreach ($variantData['delete_images'] as $imageId) {
                        $image = ProductImage::where('id', $imageId)
                            ->where('variant_id', $variant->id)
                            ->first();
                        if ($image) {
                            $this->deleteImageFile($image->image_url);
                            $image->delete();
                        }
                    }
                }

                $existingVariantIds[] = $variant->id;
            }
        } else {
            // Create new variant
            $newVariant = ProductVariant::create([
                'product_id' => $product->id,
                'variant_name' => $this->buildVariantName($variantData),
                'sku' => $variantData['sku'] ?? $this->generateSKU($product),
                'price' => isset($variantData['price']) ? $this->parsePrice($variantData['price']) : $product->base_price,
                'stock_quantity' => $variantData['stock_quantity'] ?? 0,
                'attribute_values' => $this->extractAttributeValues($variantData),
            ]);

            // Handle variant images for new variant
            if (!empty($variantData['images'])) {
                $this->uploadVariantImages($newVariant, $variantData['images']);
            }

            $existingVariantIds[] = $newVariant->id;
        }
    }

    // Delete variants that are not in the update list (cascade will delete their images)
    ProductVariant::where('product_id', $product->id)
        ->whereNotIn('id', $existingVariantIds)
        ->delete();
}

/**
 * Upload variant-specific images
 */
protected function uploadVariantImages(ProductVariant $variant, array $images): void
{
    foreach ($images as $index => $image) {
        try {
            // Store image in product folder (organized by variant)
            $path = $image->store("products/{$variant->product_id}/variants/{$variant->id}", 'public');

            // Save to database with variant_id
            ProductImage::create([
                'product_id' => $variant->product_id,
                'variant_id' => $variant->id, // Link to variant
                'image_url' => $path,
                'display_order' => $index,
                'is_primary' => false, // Variant images are not primary
            ]);

            \Log::info("Variant image uploaded", [
                'variant_id' => $variant->id,
                'path' => $path,
                'display_order' => $index,
            ]);
        } catch (\Exception $e) {
            \Log::error("Failed to upload variant image", [
                'variant_id' => $variant->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}

/**
 * Upload product-level images (not variant-specific)
 */
protected function uploadImages(Product $product, array $images): void
{
    foreach ($images as $index => $image) {
        try {
            // Store image
            $path = $image->store("products/{$product->id}", 'public');

            // Save to database with variant_id = NULL (product-level image)
            ProductImage::create([
                'product_id' => $product->id,
                'variant_id' => null, // Product-level, not variant-specific
                'image_url' => $path,
                'display_order' => $index,
                'is_primary' => $index === 0, // First image is primary
            ]);

            \Log::info("Product image uploaded", [
                'product_id' => $product->id,
                'path' => $path,
                'is_primary' => $index === 0,
            ]);
        } catch (\Exception $e) {
            \Log::error("Failed to upload product image", [
                'product_id' => $product->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}

/**
 * Update product - support partial updates (no mandatory fields)
 */
public function updateProduct(Product $product, array $data): Product
{
    return DB::transaction(function () use ($product, $data) {
        // Build update data array (only update provided fields)
        $updateData = [];

        if (isset($data['product_name'])) {
            $updateData['product_name'] = $data['product_name'];
            // Update slug if name changed
            if ($product->product_name !== $data['product_name']) {
                $updateData['slug'] = $this->generateUniqueSlug($data['product_name'], $product->id);
            }
        }

        if (isset($data['description'])) {
            $updateData['description'] = $data['description'];
        }

        if (isset($data['base_price'])) {
            $updateData['base_price'] = $this->parsePrice($data['base_price']);
        }

        if (isset($data['stock_quantity'])) {
            $updateData['total_quantity'] = $data['stock_quantity'];
        }

        if (isset($data['status'])) {
            $updateData['status'] = $data['status'];
        }

        if (isset($data['category_id'])) {
            $updateData['category_id'] = $data['category_id'];
        }

        if (isset($data['brand_id'])) {
            $updateData['brand_id'] = $data['brand_id'];
        }

        // Update optional fields
        foreach (['weight_grams', 'length_cm', 'width_cm', 'height_cm', 'currency'] as $field) {
            if (isset($data[$field])) {
                $updateData[$field] = $data[$field];
            }
        }

        if (isset($data['specifications'])) {
            $updateData['specifications'] = is_array($data['specifications']) 
                ? json_encode($data['specifications']) 
                : $data['specifications'];
        }

        // Only update if there's data to update
        if (!empty($updateData)) {
            $product->update($updateData);
        }

        // Handle variants update
        if (isset($data['variants'])) {
            $this->updateVariants($product, $data['variants']);
        }

        // Handle new product images
        if (!empty($data['images'])) {
            $this->uploadImages($product, $data['images']);
        }

        // Handle image deletions
        if (!empty($data['delete_images'])) {
            foreach ($data['delete_images'] as $imageId) {
                $image = ProductImage::where('id', $imageId)
                    ->where('product_id', $product->id)
                    ->whereNull('variant_id') // Only delete product-level images here
                    ->first();
                if ($image) {
                    $this->deleteImageFile($image->image_url);
                    $image->delete();
                }
            }
        }

        return $product->fresh(['images', 'variants.images', 'category']);
    });
}
