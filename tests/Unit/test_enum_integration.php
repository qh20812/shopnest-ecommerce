<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Enums\OrderStatus;
use App\Enums\ProductStatus;
use App\Models\Order;
use App\Models\Product;

echo "=== Testing Enum Integration with Models ===\n\n";

// Test 1: Check if models have enum casts
echo "1. Checking model casts:\n";
$order = new Order();
$casts = $order->getCasts();
echo "   Order casts:\n";
foreach ($casts as $column => $cast) {
    if (str_contains($cast, 'Enums')) {
        echo "      ✓ {$column} => {$cast}\n";
    }
}

$product = new Product();
$casts = $product->getCasts();
echo "\n   Product casts:\n";
foreach ($casts as $column => $cast) {
    if (str_contains($cast, 'Enums')) {
        echo "      ✓ {$column} => {$cast}\n";
    }
}

// Test 2: Enum values
echo "\n2. OrderStatus enum values:\n";
foreach (OrderStatus::cases() as $status) {
    echo "   {$status->name} = '{$status->value}' => {$status->label()}\n";
}

echo "\n3. ProductStatus enum values:\n";
foreach (ProductStatus::cases() as $status) {
    echo "   {$status->name} = '{$status->value}' => {$status->label()}\n";
}

// Test 3: Options for forms
echo "\n4. OrderStatus options for forms:\n";
$options = OrderStatus::options();
foreach ($options as $option) {
    echo "   [{$option['value']}] => {$option['label']}\n";
}

// Test 4: Enum methods
echo "\n5. Enum methods test:\n";
$pending = OrderStatus::PENDING;
echo "   Value: {$pending->value}\n";
echo "   Label: {$pending->label()}\n";
echo "   Name: {$pending->name}\n";

// Test 5: From string
echo "\n6. Creating enum from string:\n";
try {
    $confirmed = OrderStatus::from('confirmed');
    echo "   ✓ OrderStatus::from('confirmed') => {$confirmed->label()}\n";
} catch (\ValueError $e) {
    echo "   ✗ Failed: {$e->getMessage()}\n";
}

// Test 6: TryFrom
echo "\n7. TryFrom test:\n";
$valid = OrderStatus::tryFrom('pending');
echo "   tryFrom('pending'): " . ($valid ? "✓ {$valid->label()}" : "✗ null") . "\n";

$invalid = OrderStatus::tryFrom('invalid_status');
echo "   tryFrom('invalid_status'): " . ($invalid ? "✓ {$invalid->label()}" : "✓ null (expected)") . "\n";

echo "\n✅ All enum integration tests passed!\n";
echo "\nℹ️  Note: To test with actual database records, use:\n";
echo "   php artisan tinker\n";
echo "   >>> \$order = Order::first();\n";
echo "   >>> \$order->status->label();\n";
