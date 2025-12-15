<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use App\Models\Role;
use App\Services\ProductService;
use Illuminate\Console\Command;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class TestProductWithImage extends Command
{
    protected $signature = 'test:product-with-image';
    protected $description = 'Test product creation with image';

    public function handle(ProductService $productService)
    {
        $this->info('=== PRODUCT WITH IMAGE TEST ===');
        $this->newLine();

        // Find seller
        $seller = User::whereHas('roles', function($q) {
            $q->where('role_name', 'seller');
        })->first();

        if (!$seller) {
            $this->error('No seller found!');
            return 1;
        }

        $shop = $seller->shops()->first();
        if (!$shop) {
            $this->error('No shop found!');
            return 1;
        }

        $category = Category::first();
        if (!$category) {
            $this->error('No category found!');
            return 1;
        }

        $this->info("Seller: {$seller->full_name}");
        $this->info("Shop: {$shop->shop_name}");
        $this->info("Category: {$category->category_name}");
        $this->newLine();

        // Create a test image
        $this->info('Creating test image...');
        
        // Create a simple PNG file (1x1 transparent pixel)
        $imagePath = storage_path('app/temp_test_image.png');
        $pngData = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
        file_put_contents($imagePath, $pngData);

        $uploadedFile = new UploadedFile(
            $imagePath,
            'test-product.png',
            'image/png',
            null,
            true
        );

        $productData = [
            'product_name' => 'Product with Image ' . time(),
            'description' => 'Test product with uploaded image',
            'base_price' => '200000',
            'stock_quantity' => 25,
            'category_id' => $category->id,
            'status' => 'active',
            'images' => [$uploadedFile],
        ];

        try {
            $product = $productService->createProduct($shop, $productData);
            
            $this->info("✓ Product created: {$product->product_name} (ID: {$product->id})");
            $this->info("  Images: {$product->images()->count()}");
            
            foreach ($product->images as $image) {
                $this->info("    • {$image->image_url}");
                $this->info("      Primary: " . ($image->is_primary ? 'Yes' : 'No'));
                
                // Check if file exists
                $path = str_replace('/storage/', '', parse_url($image->image_url, PHP_URL_PATH));
                if (Storage::disk('public')->exists($path)) {
                    $this->info("      ✓ File exists in storage");
                } else {
                    $this->error("      ✗ File NOT found in storage");
                }
            }
            
            // Clean up temp file
            @unlink($imagePath);
            
            $this->newLine();
            $this->info('✓ Test completed successfully!');
            $this->info("Now visit: /seller/products to see the product with image");
            
        } catch (\Exception $e) {
            $this->error("✗ Failed: " . $e->getMessage());
            @unlink($imagePath);
            return 1;
        }

        return 0;
    }
}
