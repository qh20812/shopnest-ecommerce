<?php

require __DIR__ . '/vendor/autoload.php';

use App\Enums\OrderStatus;
use App\Enums\ProductStatus;
use App\Enums\PaymentMethod;
use App\Enums\Gender;

echo "=== Testing Enum Values ===\n\n";

// Test OrderStatus
echo "OrderStatus cases:\n";
foreach (OrderStatus::cases() as $status) {
    echo "  - {$status->name}: {$status->value} => {$status->label()}\n";
}

echo "\nOrderStatus options (for forms):\n";
print_r(OrderStatus::options());

// Test ProductStatus
echo "\nProductStatus cases:\n";
foreach (ProductStatus::cases() as $status) {
    echo "  - {$status->name}: {$status->value} => {$status->label()}\n";
}

// Test PaymentMethod
echo "\nPaymentMethod cases:\n";
foreach (PaymentMethod::cases() as $method) {
    echo "  - {$method->name}: {$method->value} => {$method->label()}\n";
}

// Test Gender
echo "\nGender cases:\n";
foreach (Gender::cases() as $gender) {
    echo "  - {$gender->name}: {$gender->value} => {$gender->label()}\n";
}

echo "\n=== Testing Enum Usage ===\n\n";

// Create instance
$status = OrderStatus::PENDING;
echo "Status: {$status->value}\n";
echo "Label: {$status->label()}\n";
echo "Name: {$status->name}\n";

// Comparison
echo "\nComparison:\n";
echo "Is PENDING? " . ($status === OrderStatus::PENDING ? 'Yes' : 'No') . "\n";
echo "Is CONFIRMED? " . ($status === OrderStatus::CONFIRMED ? 'Yes' : 'No') . "\n";

// From value
echo "\nFrom value:\n";
$fromValue = OrderStatus::from('confirmed');
echo "Created from 'confirmed': {$fromValue->value} => {$fromValue->label()}\n";

// Try from (returns null if not found)
$tryFromValue = OrderStatus::tryFrom('invalid');
echo "Try from 'invalid': " . ($tryFromValue === null ? 'null (not found)' : $tryFromValue->value) . "\n";

echo "\n=== Enum in Model Context ===\n\n";
echo "When you use enums in models with casts:\n";
echo "  \$order->status = OrderStatus::PENDING;\n";
echo "  \$order->save();\n";
echo "  // Database stores: 'pending'\n\n";
echo "  \$order = Order::find(1);\n";
echo "  \$order->status; // Returns: OrderStatus::PENDING (enum instance)\n";
echo "  \$order->status->label(); // Returns: 'Chờ xác nhận'\n";
echo "  \$order->status->value; // Returns: 'pending'\n";

echo "\n✅ Enum testing completed!\n";
