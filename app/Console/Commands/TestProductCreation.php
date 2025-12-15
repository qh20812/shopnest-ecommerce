<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use App\Models\Role;
use App\Services\ProductService;
use Illuminate\Console\Command;

class TestProductCreation extends Command
{
    protected $signature = 'test:product-creation';
    protected $description = 'Test product creation flow';

    public function handle(ProductService $productService)
    {
        $this->info('=== PRODUCT CREATION TEST ===');
        $this->newLine();

        // Find seller
        $seller = User::whereHas('roles', function($q) {
            $q->where('role_name', 'seller');
        })->first();

        if (!$seller) {
            $this->warn('No seller found. Creating one...');
            $seller = User::factory()->create();
            $sellerRole = Role::firstOrCreate(['role_name' => 'seller'], ['description' => 'Seller']);
            $seller->roles()->attach($sellerRole);
        }

        $this->info("Seller: {$seller->full_name} (ID: {$seller->id})");

        // Find or create shop
        $shop = $seller->shops()->first();
        if (!$shop) {
            $this->warn('No shop found. Creating one...');
            $shop = Shop::create([
                'owner_id' => $seller->id,
                'shop_name' => 'Test Shop',
                'slug' => 'test-shop-' . time(),
                'status' => 'active',
            ]);
        }

        $this->info("Shop: {$shop->shop_name} (ID: {$shop->id})");

        // Find or create category
        $category = Category::first();
        if (!$category) {
            $this->warn('No category found. Creating one...');
            $category = Category::create([
                'category_name' => 'Test Category',
                'slug' => 'test-category',
            ]);
        }

        $this->info("Category: {$category->category_name} (ID: {$category->id})");
        $this->newLine();

        // Test 1: Simple product
        $this->info('Test 1: Creating simple product...');
        
        $productData = [
            'product_name' => 'Test Product ' . time(),
            'description' => 'This is a test product',
            'base_price' => '100000',
            'stock_quantity' => 50,
            'category_id' => $category->id,
            'status' => 'active',
        ];

        try {
            $product = $productService->createProduct($shop, $productData);
            
            $this->info("✓ Product created: {$product->product_name} (ID: {$product->id})");
            $this->info("  Price: {$product->base_price}");
            $this->info("  Stock: {$product->total_quantity}");
            $this->info("  Status: {$product->status->value}");
            
            // Verify in DB
            $dbCheck = Product::find($product->id);
            if ($dbCheck) {
                $this->info("  ✓ Verified in database!");
            } else {
                $this->error("  ✗ NOT found in database!");
            }
        } catch (\Exception $e) {
            $this->error("✗ Failed: " . $e->getMessage());
            $this->error("  Check logs: storage/logs/laravel.log");
        }

        $this->newLine();

        // Test 2: Product with variants
        $this->info('Test 2: Creating product with variants...');
        
        $variantData = [
            'product_name' => 'T-Shirt ' . time(),
            'description' => 'T-shirt with size and color variants',
            'base_price' => '150000',
            'stock_quantity' => 100,
            'category_id' => $category->id,
            'status' => 'active',
            'variants' => [
                ['size' => 'M', 'color' => 'Red', 'stock_quantity' => 30],
                ['size' => 'L', 'color' => 'Blue', 'stock_quantity' => 40],
                ['size' => 'XL', 'color' => 'Black', 'stock_quantity' => 30],
            ],
        ];

        try {
            $product = $productService->createProduct($shop, $variantData);
            
            $this->info("✓ Product created: {$product->product_name} (ID: {$product->id})");
            $this->info("  Variants: {$product->variants()->count()}");
            
            foreach ($product->variants as $variant) {
                $attrs = json_decode($variant->attribute_values, true) ?? [];
                $attrDisplay = !empty($attrs) ? json_encode($attrs) : 'No attributes';
                $this->info("    • {$variant->variant_name} - {$attrDisplay} (Stock: {$variant->stock_quantity})");
            }
            
            // Verify in DB
            $dbCheck = Product::find($product->id);
            if ($dbCheck) {
                $this->info("  ✓ Verified in database!");
            } else {
                $this->error("  ✗ NOT found in database!");
            }
        } catch (\Exception $e) {
            $this->error("✗ Failed: " . $e->getMessage());
            $this->error("  Check logs: storage/logs/laravel.log");
        }

        $this->newLine();
        $this->info('=== TEST COMPLETED ===');
        $this->info('Total products in shop: ' . Product::where('shop_id', $shop->id)->count());
        $this->newLine();

        return 0;
    }
}
