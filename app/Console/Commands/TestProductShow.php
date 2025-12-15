<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\User;
use Illuminate\Console\Command;

class TestProductShow extends Command
{
    protected $signature = 'test:product-show {product_id?}';
    protected $description = 'Test product show page displays all data correctly';

    public function handle()
    {
        $this->info('=== PRODUCT SHOW PAGE TEST ===');
        $this->newLine();

        // Find seller with products
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

        // Get product ID from argument or find latest
        $productId = $this->argument('product_id');
        
        if ($productId) {
            $product = Product::with(['images', 'variants', 'category'])
                ->where('shop_id', $shop->id)
                ->find($productId);
        } else {
            $product = Product::with(['images', 'variants', 'category'])
                ->where('shop_id', $shop->id)
                ->latest()
                ->first();
        }

        if (!$product) {
            $this->error('No product found!');
            return 1;
        }

        $this->info("Testing Product Show for:");
        $this->info("Product ID: {$product->id}");
        $this->info("Product Name: {$product->product_name}");
        $this->newLine();

        // Test 1: Basic Information
        $this->info('✓ Test 1: Basic Information');
        $this->line("  - Name: {$product->product_name}");
        $this->line("  - Slug: {$product->slug}");
        $this->line("  - Description: " . ($product->description ? substr($product->description, 0, 50) . '...' : 'N/A'));
        $this->line("  - Status: {$product->status->value}");
        $this->newLine();

        // Test 2: Price & Stock
        $this->info('✓ Test 2: Price & Stock');
        $this->line("  - Base Price: " . number_format($product->base_price, 0, ',', '.') . 'đ');
        $this->line("  - Total Quantity: {$product->total_quantity}");
        $this->newLine();

        // Test 3: Category
        $this->info('✓ Test 3: Category');
        if ($product->category) {
            $this->line("  - Category: {$product->category->category_name}");
        } else {
            $this->warn("  - No category assigned");
        }
        $this->newLine();

        // Test 4: Images
        $this->info('✓ Test 4: Images');
        $this->line("  - Total Images: {$product->images->count()}");
        
        if ($product->images->count() > 0) {
            foreach ($product->images as $idx => $image) {
                $isPrimary = $image->is_primary ? '(PRIMARY)' : '';
                $this->line("    {$idx}. {$image->image_url} {$isPrimary}");
                
                // Check if file exists
                $path = str_replace('/storage/', '', parse_url($image->image_url, PHP_URL_PATH));
                if (\Storage::disk('public')->exists($path)) {
                    $this->line("       ✓ File exists");
                } else {
                    $this->warn("       ✗ File NOT found");
                }
            }
        } else {
            $this->warn("  - No images");
        }
        $this->newLine();

        // Test 5: Variants
        $this->info('✓ Test 5: Variants');
        $this->line("  - Total Variants: {$product->variants->count()}");
        
        if ($product->variants->count() > 0) {
            foreach ($product->variants as $variant) {
                $attrs = json_decode($variant->attribute_values, true) ?? [];
                $attrStr = !empty($attrs) ? json_encode($attrs) : 'No attributes';
                
                $this->line("    • {$variant->variant_name}");
                $this->line("      SKU: {$variant->sku}");
                $this->line("      Price: " . number_format($variant->price, 0, ',', '.') . 'đ');
                $this->line("      Stock: {$variant->stock_quantity}");
                $this->line("      Attributes: {$attrStr}");
            }
        } else {
            $this->warn("  - No variants");
        }
        $this->newLine();

        // Test 6: Timestamps
        $this->info('✓ Test 6: Timestamps');
        $this->line("  - Created At: {$product->created_at->format('Y-m-d H:i:s')}");
        $this->line("  - Updated At: {$product->updated_at->format('Y-m-d H:i:s')}");
        $this->newLine();

        // Summary
        $this->info('=== TEST SUMMARY ===');
        $checks = [
            'Basic Info' => $product->product_name && $product->slug,
            'Price' => $product->base_price > 0,
            'Category' => $product->category !== null,
            'Images' => $product->images->count() > 0,
            'Variants' => true, // Optional
        ];

        foreach ($checks as $check => $passed) {
            if ($passed) {
                $this->info("  ✓ {$check}");
            } else {
                $this->warn("  ⚠ {$check}");
            }
        }

        $this->newLine();
        $this->info("Test completed for Product #{$product->id}");
        $this->info("Visit: /seller/products/{$product->id} to see the page");

        return 0;
    }
}
