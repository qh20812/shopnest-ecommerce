<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductImage;
use App\Models\Shop;
use App\Services\ProductService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class TestProductManagement extends Command
{
    protected $signature = 'test:product-management {--reset : Reset test data}';
    protected $description = 'Comprehensive test for product management with variant images';

    public function handle(ProductService $productService)
    {
        $this->info('=== PRODUCT MANAGEMENT COMPREHENSIVE TEST ===');
        $this->newLine();

        if ($this->option('reset')) {
            $this->resetTestData();
        }

        // Get shop for testing
        $shop = Shop::first();
        if (!$shop) {
            $this->error('❌ No shop found. Please seed database first.');
            return 1;
        }

        $this->info("Testing with Shop: {$shop->shop_name} (ID: {$shop->id})");
        $this->newLine();

        // Test 1: Create product with images only (no variants)
        $this->test1CreateProductWithImages($productService, $shop);

        // Test 2: Create product with variants (each has images)
        $this->test2CreateProductWithVariantImages($productService, $shop);

        // Test 3: Update product - partial update (no mandatory fields)
        $this->test3PartialUpdate($productService);

        // Test 4: Update product - add variant images
        $this->test4AddVariantImages($productService);

        // Test 5: Verify database structure
        $this->test5VerifyDatabase();

        $this->newLine();
        $this->info('=== ALL TESTS COMPLETED ===');
        
        return 0;
    }

    private function test1CreateProductWithImages($service, $shop)
    {
        $this->info('Test 1: Create product with multiple product-level images');
        $this->line('-----------------------------------------------------------');

        $data = [
            'product_name' => 'Test Product - Product Images Only ' . time(),
            'description' => 'Product with 3 general images, no variants',
            'base_price' => '199000',
            'stock_quantity' => '50',
            'category_id' => 1,
            'status' => 'active',
            'images' => $this->createFakeImages(3),
            'variants' => [],
        ];

        try {
            $product = $service->createProduct($shop, $data);
            
            $this->line("✓ Product created: #{$product->id} - {$product->product_name}");
            $this->line("  - Product Images: " . $product->images()->productOnly()->count());
            $this->line("  - Variants: " . $product->variants->count());
            
            if ($product->images()->productOnly()->count() === 3) {
                $this->info('  ✓ PASSED: 3 product images saved');
            } else {
                $this->error('  ❌ FAILED: Expected 3 images, got ' . $product->images()->productOnly()->count());
            }
        } catch (\Exception $e) {
            $this->error("  ❌ FAILED: " . $e->getMessage());
        }

        $this->newLine();
    }

    private function test2CreateProductWithVariantImages($service, $shop)
    {
        $this->info('Test 2: Create product with variants (each has images)');
        $this->line('-----------------------------------------------------------');

        $data = [
            'product_name' => 'Test T-Shirt with Variant Images ' . time(),
            'description' => 'T-shirt with 2 variants, each has 2 images',
            'base_price' => '299000',
            'stock_quantity' => '100',
            'category_id' => 1,
            'status' => 'active',
            'images' => $this->createFakeImages(2), // 2 general product images
            'variants' => [
                [
                    'size' => 'M',
                    'color' => 'Red',
                    'stock_quantity' => 30,
                    'images' => $this->createFakeImages(2), // 2 images for Red variant
                ],
                [
                    'size' => 'L',
                    'color' => 'Blue',
                    'stock_quantity' => 40,
                    'images' => $this->createFakeImages(2), // 2 images for Blue variant
                ],
            ],
        ];

        try {
            $product = $service->createProduct($shop, $data);
            
            $this->line("✓ Product created: #{$product->id} - {$product->product_name}");
            $this->line("  - Product Images: " . $product->images()->productOnly()->count());
            $this->line("  - Variants: " . $product->variants->count());
            
            foreach ($product->variants as $variant) {
                $variantImgCount = $variant->images->count();
                $this->line("    • Variant: {$variant->variant_name} - Images: {$variantImgCount}");
                
                if ($variantImgCount === 2) {
                    $this->info("      ✓ PASSED");
                } else {
                    $this->error("      ❌ FAILED: Expected 2 images, got {$variantImgCount}");
                }
            }
        } catch (\Exception $e) {
            $this->error("  ❌ FAILED: " . $e->getMessage());
            $this->line("  Error: " . $e->getTraceAsString());
        }

        $this->newLine();
    }

    private function test3PartialUpdate($service)
    {
        $this->info('Test 3: Partial update (no mandatory validation)');
        $this->line('-----------------------------------------------------------');

        $product = Product::latest()->first();
        if (!$product) {
            $this->warn('  ⚠ Skipped: No product to test');
            return;
        }

        $this->line("Testing with Product #{$product->id}");

        // Test updating only description (no name, price, stock)
        $data = [
            'description' => 'Updated description via partial update test - ' . date('Y-m-d H:i:s'),
        ];

        try {
            $updated = $service->updateProduct($product, $data);
            $this->info('  ✓ PASSED: Partial update successful (description only)');
            $this->line("  - New description: " . $updated->description);
        } catch (\Exception $e) {
            $this->error('  ❌ FAILED: ' . $e->getMessage());
        }

        $this->newLine();
    }

    private function test4AddVariantImages($service)
    {
        $this->info('Test 4: Add images to existing variant');
        $this->line('-----------------------------------------------------------');

        $variant = ProductVariant::whereHas('images')->first();
        if (!$variant) {
            $this->warn('  ⚠ Skipped: No variant with images found');
            return;
        }

        $product = $variant->product;
        $initialCount = $variant->images->count();

        $this->line("Testing with Variant #{$variant->id} - {$variant->variant_name}");
        $this->line("  Initial images: {$initialCount}");

        // Simulate adding 1 more image to this variant
        $data = [
            'variants' => [
                [
                    'id' => $variant->id,
                    'size' => $variant->attribute_values['size'] ?? 'M',
                    'color' => $variant->attribute_values['color'] ?? 'Black',
                    'stock_quantity' => $variant->stock_quantity,
                    'images' => $this->createFakeImages(1), // Add 1 more image
                ],
            ],
        ];

        try {
            $service->updateProduct($product, $data);
            $variant->refresh();
            
            $newCount = $variant->images->count();
            $this->line("  New image count: {$newCount}");
            
            if ($newCount > $initialCount) {
                $this->info('  ✓ PASSED: Image added successfully');
            } else {
                $this->error('  ❌ FAILED: Image not added');
            }
        } catch (\Exception $e) {
            $this->error('  ❌ FAILED: ' . $e->getMessage());
        }

        $this->newLine();
    }

    private function test5VerifyDatabase()
    {
        $this->info('Test 5: Verify database structure');
        $this->line('-----------------------------------------------------------');

        // Check product_images table has variant_id column
        $hasVariantId = \Schema::hasColumn('product_images', 'variant_id');
        $this->line('  - product_images.variant_id exists: ' . ($hasVariantId ? '✓' : '❌'));

        // Check product_variants table does NOT have image_id
        $hasImageId = \Schema::hasColumn('product_variants', 'image_id');
        $this->line('  - product_variants.image_id removed: ' . (!$hasImageId ? '✓' : '❌'));

        // Count images by type
        $productImages = ProductImage::whereNull('variant_id')->count();
        $variantImages = ProductImage::whereNotNull('variant_id')->count();
        
        $this->line("  - Product-level images: {$productImages}");
        $this->line("  - Variant-specific images: {$variantImages}");

        if ($hasVariantId && !$hasImageId) {
            $this->info('  ✓ PASSED: Database structure correct');
        } else {
            $this->error('  ❌ FAILED: Database structure incorrect');
        }

        $this->newLine();
    }

    private function createFakeImages(int $count): array
    {
        $images = [];
        
        for ($i = 0; $i < $count; $i++) {
            // Create a simple 1x1 pixel PNG image
            $image = imagecreate(100, 100);
            imagecolorallocate($image, rand(0, 255), rand(0, 255), rand(0, 255));
            
            $tempFile = tempnam(sys_get_temp_dir(), 'test_img_') . '.png';
            imagepng($image, $tempFile);
            imagedestroy($image);

            $images[] = new UploadedFile(
                $tempFile,
                'test-image-' . ($i + 1) . '.png',
                'image/png',
                null,
                true
            );
        }

        return $images;
    }

    private function resetTestData()
    {
        $this->warn('Resetting test data...');
        
        // Delete test products (cascade will handle images and variants)
        Product::where('product_name', 'like', 'Test%')->delete();
        
        // Clean up storage
        Storage::disk('public')->deleteDirectory('products');
        
        $this->info('✓ Test data reset');
        $this->newLine();
    }
}
