<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Shop;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductService
{
    public function __construct(
        protected ImageValidationService $imageValidationService
    ) {}

    /**
     * Get paginated products for a shop with filters
     */
    public function getProducts(Shop $shop, array $filters = [], int $perPage = 15)
    {
        $query = Product::where('shop_id', $shop->id)
            ->with(['images', 'category', 'variants']);

        // Search filter
        if (!empty($filters['search'])) {
            $query->where('product_name', 'like', '%' . $filters['search'] . '%');
        }

        // Category filter
        if (!empty($filters['category']) && $filters['category'] !== 'all') {
            $query->where('category_id', $filters['category']);
        }

        // Status filter
        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        // Order by latest
        $query->orderBy('created_at', 'desc');

        return $query->paginate($perPage);
    }

    /**
     * Create a new product with variants and images
     */
    public function createProduct(Shop $shop, array $data): Product
    {
        return DB::transaction(function () use ($shop, $data) {
            Log::info('Starting product creation transaction', [
                'shop_id' => $shop->id,
                'product_name' => $data['product_name'] ?? 'N/A',
                'variants_count' => !empty($data['variants']) ? count($data['variants']) : 0,
                'variants_data' => !empty($data['variants']) ? array_map(function($v) {
                    return [
                        'size' => $v['size'] ?? null,
                        'color' => $v['color'] ?? null,
                        'has_images' => !empty($v['images']),
                        'images_count' => !empty($v['images']) ? count($v['images']) : 0,
                    ];
                }, $data['variants']) : [],
            ]);

            // Create product
            $product = Product::create([
                'shop_id' => $shop->id,
                'seller_id' => $shop->owner_id,
                'category_id' => $data['category_id'],
                'brand_id' => $data['brand_id'] ?? null,
                'product_name' => $data['product_name'],
                'slug' => $this->generateUniqueSlug($data['product_name']),
                'description' => $data['description'] ?? null,
                'specifications' => isset($data['specifications']) ? json_encode($data['specifications']) : null,
                'base_price' => $this->parsePrice($data['base_price']),
                'currency' => $data['currency'] ?? 'VND',
                'weight_grams' => $data['weight_grams'] ?? null,
                'length_cm' => $data['length_cm'] ?? null,
                'width_cm' => $data['width_cm'] ?? null,
                'height_cm' => $data['height_cm'] ?? null,
                'status' => $data['status'] ?? 'draft',
                'total_quantity' => $data['stock_quantity'] ?? 0,
                'total_sold' => 0,
                'rating' => 0,
                'review_count' => 0,
                'view_count' => 0,
            ]);

            Log::info('Product created in database', ['product_id' => $product->id]);

            // Handle variants if provided
            if (!empty($data['variants'])) {
                Log::info('Creating variants', ['count' => count($data['variants'])]);
                $this->createVariants($product, $data['variants']);
            }

            // Handle images if provided
            if (!empty($data['images'])) {
                Log::info('Uploading images', ['count' => count($data['images'])]);
                $this->uploadImages($product, $data['images']);
            }

            Log::info('Product creation transaction completed successfully');
            return $product->fresh(['images', 'variants', 'category']);
        });
    }

    /**
     * Update an existing product
     */
    public function updateProduct(Product $product, array $data): Product
    {
        return DB::transaction(function () use ($product, $data) {
            Log::info('Updating product', [
                'product_id' => $product->id,
                'data' => $data,
            ]);

            // Update basic product info - only update fields that are provided
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

            // Update category if provided
            if (isset($data['category_id'])) {
                $updateData['category_id'] = $data['category_id'];
            }

            // Update brand if provided
            if (isset($data['brand_id'])) {
                $updateData['brand_id'] = $data['brand_id'];
            }

            // Update optional fields
            foreach (['weight_grams', 'length_cm', 'width_cm', 'height_cm', 'specifications', 'currency'] as $field) {
                if (isset($data[$field])) {
                    $updateData[$field] = $field === 'specifications' && is_array($data[$field]) 
                        ? json_encode($data[$field]) 
                        : $data[$field];
                }
            }

            Log::info('Updating product with data', ['updateData' => $updateData]);

            $product->update($updateData);

            // Handle variants update
            if (isset($data['variants'])) {
                $this->updateVariants($product, $data['variants']);
            }

            // Handle new images
            if (!empty($data['images'])) {
                $this->uploadImages($product, $data['images']);
            }

            // Handle image deletions
            if (!empty($data['delete_images'])) {
                $this->deleteImages($data['delete_images']);
            }

            return $product->fresh(['images', 'variants', 'category']);
        });
    }

    /**
     * Delete a product and its related data
     */
    public function deleteProduct(Product $product): bool
    {
        return DB::transaction(function () use ($product) {
            // Delete all product images from storage
            foreach ($product->images as $image) {
                $this->deleteImageFile($image->image_url);
            }

            // Delete product (cascade will handle variants and images)
            return $product->delete();
        });
    }

    /**
     * Create product variants
     */
    protected function createVariants(Product $product, array $variants): void
    {
        foreach ($variants as $index => $variantData) {
            Log::info("Creating variant {$index}", [
                'size' => $variantData['size'] ?? null,
                'color' => $variantData['color'] ?? null,
                'has_images' => !empty($variantData['images']),
                'images_count' => !empty($variantData['images']) ? count($variantData['images']) : 0,
                'images_type' => !empty($variantData['images']) ? gettype($variantData['images']) : null,
            ]);

            $variant = ProductVariant::create([
                'product_id' => $product->id,
                'variant_name' => $this->buildVariantName($variantData),
                'sku' => $variantData['sku'] ?? $this->generateSKU($product),
                'price' => isset($variantData['price']) ? $this->parsePrice($variantData['price']) : $product->base_price,
                'stock_quantity' => $variantData['stock_quantity'] ?? 0,
                'attribute_values' => $this->extractAttributeValues($variantData),
            ]);

            Log::info("Variant created with ID: {$variant->id}");

            // Handle variant images if provided
            if (!empty($variantData['images']) && is_array($variantData['images'])) {
                Log::info("Processing variant images for variant {$variant->id}", [
                    'images_count' => count($variantData['images']),
                ]);
                $this->uploadVariantImages($product, $variant, $variantData['images']);
            } else {
                Log::warning("No images found for variant {$variant->id}");
            }
        }
    }

    /**
     * Update product variants
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
                    $existingVariantIds[] = $variant->id;

                    // Handle variant images if provided
                    if (!empty($variantData['images']) && is_array($variantData['images'])) {
                        $this->uploadVariantImages($product, $variant, $variantData['images']);
                    }
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
                $existingVariantIds[] = $newVariant->id;

                // Handle variant images if provided
                if (!empty($variantData['images']) && is_array($variantData['images'])) {
                    $this->uploadVariantImages($product, $newVariant, $variantData['images']);
                }
            }
        }

        // Delete variants that are not in the update list
        ProductVariant::where('product_id', $product->id)
            ->whereNotIn('id', $existingVariantIds)
            ->delete();
    }

    /**
     * Upload and save product images
     */
    protected function uploadImages(Product $product, array $images): void
    {
        foreach ($images as $index => $image) {
            try {
                // Validate image - skip strict validation for now
                // $this->imageValidationService->validate($image, [
                //     'required_ratio' => 1, // Square 1:1
                //     'min_width' => 800,
                //     'min_height' => 800,
                // ]);

                // Store image
                $path = $image->store('products/' . $product->id, 'public');

                // Create image record
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_url' => Storage::url($path),
                    'display_order' => $product->images()->count() + $index,
                    'is_primary' => $product->images()->count() === 0 && $index === 0,
                ]);
            } catch (\Exception $e) {
                // Log error but don't fail the entire transaction
                Log::error('Failed to upload image: ' . $e->getMessage());
                throw $e; // Re-throw to rollback transaction
            }
        }
    }

    /**
     * Delete specific images
     */
    protected function deleteImages(array $imageIds): void
    {
        $images = ProductImage::whereIn('id', $imageIds)->get();

        foreach ($images as $image) {
            $this->deleteImageFile($image->image_url);
            $image->delete();
        }
    }

    /**
     * Delete image file from storage
     */
    protected function deleteImageFile(string $url): void
    {
        $path = str_replace('/storage/', '', parse_url($url, PHP_URL_PATH));
        Storage::disk('public')->delete($path);
    }

    /**
     * Generate unique slug for product
     */
    protected function generateUniqueSlug(string $name, ?int $excludeId = null): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (true) {
            $query = Product::where('slug', $slug);
            
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }

            if (!$query->exists()) {
                break;
            }

            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Generate SKU for product variant
     */
    protected function generateSKU(Product $product): string
    {
        return 'PRD-' . $product->id . '-' . strtoupper(Str::random(6));
    }

    /**
     * Build variant name from attributes
     */
    protected function buildVariantName(array $variantData): string
    {
        $parts = [];

        if (!empty($variantData['size'])) {
            $parts[] = $variantData['size'];
        }

        if (!empty($variantData['color'])) {
            $parts[] = $variantData['color'];
        }

        return !empty($parts) ? implode(' - ', $parts) : 'Mặc định';
    }

    /**
     * Extract attribute values as JSON
     */
    protected function extractAttributeValues(array $variantData): ?string
    {
        $attributes = [];

        if (!empty($variantData['size'])) {
            $attributes['size'] = $variantData['size'];
        }

        if (!empty($variantData['color'])) {
            $attributes['color'] = $variantData['color'];
        }

        return !empty($attributes) ? json_encode($attributes) : null;
    }

    /**
     * Upload and save variant images
     */
    protected function uploadVariantImages(Product $product, ProductVariant $variant, array $images): void
    {
        foreach ($images as $index => $image) {
            try {
                // Store image
                $path = $image->store('products/' . $product->id . '/variants/' . $variant->id, 'public');

                // Create image record linked to variant
                ProductImage::create([
                    'product_id' => $product->id,
                    'variant_id' => $variant->id,
                    'image_url' => Storage::url($path),
                    'display_order' => $variant->images()->count() + $index,
                    'is_primary' => false, // Variant images are not primary
                ]);

                Log::info('Variant image uploaded successfully', [
                    'variant_id' => $variant->id,
                    'path' => $path,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to upload variant image', [
                    'variant_id' => $variant->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Parse price string to integer (remove formatting)
     */
    protected function parsePrice(string|int $price): int
    {
        if (is_int($price)) {
            return $price;
        }

        // Remove currency symbols and formatting
        $price = preg_replace('/[^\d]/', '', $price);
        
        return (int) $price;
    }
}
