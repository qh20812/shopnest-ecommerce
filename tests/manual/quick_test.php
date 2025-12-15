<?php

/**
 * Quick Test Script - Run in tinker
 * php artisan tinker
 * Then paste this code
 */

use App\Models\User;
use App\Models\Shop;
use App\Models\Category;
use App\Services\ProductService;

echo "\n=== QUICK PRODUCT CREATION TEST ===\n\n";

// Find or create test data
$seller = User::whereHas('roles', function($q) {
    $q->where('role_name', 'seller');
})->first();

if (!$seller) {
    echo "❌ No seller found in database\n";
    echo "Creating test seller...\n";
    $seller = User::factory()->create();
    $sellerRole = \App\Models\Role::where('role_name', 'seller')->first();
    if (!$sellerRole) {
        $sellerRole = \App\Models\Role::create([
            'role_name' => 'seller',
            'description' => 'Seller role'
        ]);
    }
    $seller->roles()->attach($sellerRole);
}

echo "✓ Seller: {$seller->full_name} (ID: {$seller->id})\n";

$shop = $seller->shops()->first();
if (!$shop) {
    echo "❌ No shop found. Creating one...\n";
    $shop = Shop::create([
        'owner_id' => $seller->id,
        'shop_name' => 'Test Shop',
        'slug' => 'test-shop-' . time(),
        'status' => 'active',
    ]);
}

echo "✓ Shop: {$shop->shop_name} (ID: {$shop->id})\n";

$category = Category::first();
if (!$category) {
    echo "❌ No category found. Creating one...\n";
    $category = Category::create([
        'category_name' => 'Test Category',
        'slug' => 'test-category',
    ]);
}

echo "✓ Category: {$category->category_name} (ID: {$category->id})\n\n";

// Test 1: Simple product without images
echo "Test 1: Creating simple product (no images, no variants)...\n";

$productService = app(ProductService::class);

$simpleData = [
    'product_name' => 'Test Product ' . time(),
    'description' => 'Simple test product',
    'base_price' => '100000',
    'stock_quantity' => 50,
    'category_id' => $category->id,
    'status' => 'active',
];

try {
    $product = $productService->createProduct($shop, $simpleData);
    echo "✓ Product created successfully!\n";
    echo "  - ID: {$product->id}\n";
    echo "  - Name: {$product->product_name}\n";
    echo "  - Price: {$product->base_price}\n";
    echo "  - Stock: {$product->total_quantity}\n";
    echo "  - Status: {$product->status}\n";
    
    // Verify in database
    $dbProduct = \App\Models\Product::find($product->id);
    if ($dbProduct) {
        echo "  ✓ Product found in database!\n\n";
    } else {
        echo "  ❌ Product NOT found in database!\n\n";
    }
} catch (\Exception $e) {
    echo "❌ Error: {$e->getMessage()}\n";
    echo "Stack trace:\n{$e->getTraceAsString()}\n\n";
}

// Test 2: Product with variants
echo "Test 2: Creating product with variants...\n";

$variantData = [
    'product_name' => 'T-Shirt ' . time(),
    'description' => 'T-shirt with variants',
    'base_price' => '150000',
    'stock_quantity' => 100,
    'category_id' => $category->id,
    'status' => 'active',
    'variants' => [
        [
            'size' => 'M',
            'color' => 'Red',
            'stock_quantity' => 30,
        ],
        [
            'size' => 'L',
            'color' => 'Blue',
            'stock_quantity' => 40,
        ],
    ],
];

try {
    $product = $productService->createProduct($shop, $variantData);
    echo "✓ Product with variants created!\n";
    echo "  - ID: {$product->id}\n";
    echo "  - Variants: {$product->variants()->count()}\n";
    
    foreach ($product->variants as $variant) {
        $attrs = json_decode($variant->attribute_values, true);
        echo "    • {$variant->variant_name}: " . json_encode($attrs) . "\n";
    }
    
    // Verify in database
    $dbProduct = \App\Models\Product::find($product->id);
    if ($dbProduct) {
        echo "  ✓ Product found in database!\n\n";
    } else {
        echo "  ❌ Product NOT found in database!\n\n";
    }
} catch (\Exception $e) {
    echo "❌ Error: {$e->getMessage()}\n";
    echo "Stack trace:\n{$e->getTraceAsString()}\n\n";
}

echo "=== TEST COMPLETED ===\n";
echo "Check storage/logs/laravel.log for detailed logs\n\n";
