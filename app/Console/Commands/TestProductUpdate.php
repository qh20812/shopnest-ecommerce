<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Models\Role;
use App\Services\ProductService;
use Illuminate\Console\Command;

class TestProductUpdate extends Command
{
    protected $signature = 'test:product-update {product_id?}';
    protected $description = 'Test product update functionality';

    public function handle(ProductService $productService)
    {
        $this->info('=== PRODUCT UPDATE TEST ===');
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

        $this->info("Testing update for Product #{$product->id}: {$product->product_name}");
        $this->newLine();

        // Store original data
        $originalData = [
            'name' => $product->product_name,
            'description' => $product->description,
            'price' => $product->base_price,
            'stock' => $product->total_quantity,
            'status' => $product->status->value,
        ];

        $this->info('Original Data:');
        foreach ($originalData as $key => $value) {
            $this->line("  - " . ucfirst($key) . ": {$value}");
        }
        $this->newLine();

        // Test 1: Update basic info
        $this->info('Test 1: Updating basic information...');
        
        $updateData = [
            'product_name' => $product->product_name . ' (UPDATED)',
            'description' => 'Updated description at ' . now(),
            'base_price' => (string)((int)$product->base_price + 10000),
            'stock_quantity' => (int)$product->total_quantity + 5,
            'status' => $product->status->value === 'active' ? 'inactive' : 'active',
            'category_id' => $product->category_id,
        ];

        try {
            $updated = $productService->updateProduct($product, $updateData);
            
            $this->info('✓ Update successful!');
            $this->line("  - New Name: {$updated->product_name}");
            $this->line("  - New Price: " . number_format($updated->base_price, 0, ',', '.') . 'đ');
            $this->line("  - New Stock: {$updated->total_quantity}");
            $this->line("  - New Status: {$updated->status->value}");
            
            // Verify changes
            $fresh = Product::find($product->id);
            if ($fresh->product_name === $updateData['product_name']) {
                $this->info('  ✓ Name updated correctly');
            } else {
                $this->error('  ✗ Name NOT updated');
            }
            
            if ($fresh->base_price == $updateData['base_price']) {
                $this->info('  ✓ Price updated correctly');
            } else {
                $this->error('  ✗ Price NOT updated');
            }
            
            if ($fresh->total_quantity == $updateData['stock_quantity']) {
                $this->info('  ✓ Stock updated correctly');
            } else {
                $this->error('  ✗ Stock NOT updated');
            }
            
        } catch (\Exception $e) {
            $this->error('✗ Update failed: ' . $e->getMessage());
            return 1;
        }

        $this->newLine();

        // Test 2: Update variants
        if ($product->variants->count() > 0) {
            $this->info('Test 2: Updating variants...');
            
            $variantData = $product->variants->map(function ($variant) {
                $attrs = json_decode($variant->attribute_values, true) ?? [];
                return [
                    'id' => $variant->id,
                    'size' => $attrs['size'] ?? '',
                    'color' => $attrs['color'] ?? '',
                    'stock_quantity' => $variant->stock_quantity + 10,
                ];
            })->toArray();

            // Add a new variant
            $variantData[] = [
                'size' => 'XXL',
                'color' => 'Purple',
                'stock_quantity' => 15,
            ];

            try {
                $updated = $productService->updateProduct($product, [
                    'product_name' => $product->product_name,
                    'base_price' => (string)$product->base_price,
                    'stock_quantity' => (int)$product->total_quantity,
                    'category_id' => $product->category_id,
                    'variants' => $variantData,
                ]);
                
                $this->info('✓ Variants updated successfully!');
                $this->line("  - Total variants: {$updated->variants()->count()}");
                
                foreach ($updated->variants as $variant) {
                    $attrs = json_decode($variant->attribute_values, true) ?? [];
                    $this->line("    • {$variant->variant_name} - Stock: {$variant->stock_quantity}");
                }
                
            } catch (\Exception $e) {
                $this->error('✗ Variant update failed: ' . $e->getMessage());
            }

            $this->newLine();
        }

        // Test 3: Rollback to original (optional)
        if ($this->confirm('Do you want to rollback to original values?', true)) {
            $this->info('Test 3: Rolling back changes...');
            
            try {
                $rolled = $productService->updateProduct($product, [
                    'product_name' => $originalData['name'],
                    'description' => $originalData['description'],
                    'base_price' => (string)$originalData['price'],
                    'stock_quantity' => (int)$originalData['stock'],
                    'status' => $originalData['status'],
                    'category_id' => $product->category_id,
                ]);
                
                $this->info('✓ Rollback successful!');
                $this->line("  - Name: {$rolled->product_name}");
                
            } catch (\Exception $e) {
                $this->error('✗ Rollback failed: ' . $e->getMessage());
            }
        }

        $this->newLine();
        $this->info('=== TEST COMPLETED ===');
        $this->info("Product ID: {$product->id}");
        $this->info("Check storage/logs/laravel.log for detailed logs");

        return 0;
    }
}
