<?php

/**
 * Manual Test Script for Product Creation
 * 
 * This script helps you manually test the product creation flow.
 * Run: php artisan tinker < tests/manual/test_product_creation.php
 * Or copy and paste into tinker session
 */

use App\Models\Category;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use App\Services\ProductService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

echo "\n=== PRODUCT CREATION MANUAL TEST ===\n\n";

// 1. Setup test data
echo "1. Setting up test data...\n";

$seller = User::where('role', 'seller')->first();
if (!$seller) {
    echo "❌ No seller found. Creating one...\n";
    $seller = User::factory()->create(['role' => 'seller']);
}
echo "✓ Seller: {$seller->full_name} (ID: {$seller->id})\n";

$shop = $seller->shops()->first();
if (!$shop) {
    echo "❌ No shop found for seller. Creating one...\n";
    $shop = Shop::factory()->create(['owner_id' => $seller->id, 'status' => 'active']);
}
echo "✓ Shop: {$shop->shop_name} (ID: {$shop->id})\n";

$category = Category::first();
if (!$category) {
    echo "❌ No category found. Creating one...\n";
    $category = Category::factory()->create(['category_name' => 'Test Category']);
}
echo "✓ Category: {$category->category_name} (ID: {$category->id})\n\n";

// 2. Test basic product creation
echo "2. Testing basic product creation...\n";

$productService = app(ProductService::class);

$basicProductData = [
    'product_name' => 'Test Product ' . time(),
    'description' => 'This is a test product created manually',
    'base_price' => '100000',
    'stock_quantity' => 50,
    'category_id' => $category->id,
    'status' => 'active',
];

try {
    $basicProduct = $productService->createProduct($shop, $basicProductData);
    echo "✓ Basic product created successfully!\n";
    echo "  - ID: {$basicProduct->id}\n";
    echo "  - Name: {$basicProduct->product_name}\n";
    echo "  - Price: {$basicProduct->base_price}\n";
    echo "  - Stock: {$basicProduct->total_quantity}\n";
    echo "  - Slug: {$basicProduct->slug}\n\n";
} catch (\Exception $e) {
    echo "❌ Failed to create basic product: {$e->getMessage()}\n\n";
}

// 3. Test product with variants
echo "3. Testing product creation with variants...\n";

$variantProductData = [
    'product_name' => 'T-Shirt with Variants ' . time(),
    'description' => 'T-shirt with multiple size and color options',
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
        [
            'size' => 'XL',
            'color' => 'Black',
            'stock_quantity' => 30,
        ],
    ],
];

try {
    $variantProduct = $productService->createProduct($shop, $variantProductData);
    echo "✓ Product with variants created successfully!\n";
    echo "  - ID: {$variantProduct->id}\n";
    echo "  - Name: {$variantProduct->product_name}\n";
    echo "  - Variants count: {$variantProduct->variants()->count()}\n";
    
    foreach ($variantProduct->variants as $variant) {
        $attrs = json_decode($variant->attribute_values, true);
        echo "    • {$variant->variant_name} - Stock: {$variant->stock_quantity}\n";
        echo "      Attributes: " . json_encode($attrs) . "\n";
    }
    echo "\n";
} catch (\Exception $e) {
    echo "❌ Failed to create product with variants: {$e->getMessage()}\n\n";
}

// 4. Test price parsing
echo "4. Testing price parsing...\n";

$priceTestData = [
    'product_name' => 'Price Test Product ' . time(),
    'description' => 'Testing various price formats',
    'base_price' => '1.500.000đ', // Vietnamese format
    'stock_quantity' => 10,
    'category_id' => $category->id,
    'status' => 'active',
];

try {
    $priceProduct = $productService->createProduct($shop, $priceTestData);
    echo "✓ Price parsing successful!\n";
    echo "  - Input: 1.500.000đ\n";
    echo "  - Parsed: {$priceProduct->base_price}\n";
    echo "  - Expected: 1500000\n";
    if ($priceProduct->base_price == 1500000) {
        echo "  ✓ Price parsed correctly!\n\n";
    } else {
        echo "  ❌ Price parsing incorrect!\n\n";
    }
} catch (\Exception $e) {
    echo "❌ Failed to test price parsing: {$e->getMessage()}\n\n";
}

// 5. Test slug generation
echo "5. Testing slug generation...\n";

$slugTestData = [
    'product_name' => 'Áo Thun Nam Cao Cấp ' . time(),
    'description' => 'Testing Vietnamese slug generation',
    'base_price' => '200000',
    'stock_quantity' => 25,
    'category_id' => $category->id,
    'status' => 'active',
];

try {
    $slugProduct = $productService->createProduct($shop, $slugTestData);
    echo "✓ Slug generation successful!\n";
    echo "  - Product name: {$slugProduct->product_name}\n";
    echo "  - Generated slug: {$slugProduct->slug}\n\n";
} catch (\Exception $e) {
    echo "❌ Failed to test slug generation: {$e->getMessage()}\n\n";
}

// 6. Summary
echo "=== TEST SUMMARY ===\n";
$totalProducts = Product::where('shop_id', $shop->id)->count();
echo "Total products in shop: {$totalProducts}\n";
echo "Test completed!\n\n";

// Cleanup instructions
echo "To cleanup test data, run:\n";
echo "Product::where('shop_id', {$shop->id})->where('product_name', 'like', '%Test%')->delete();\n";
echo "\n";
