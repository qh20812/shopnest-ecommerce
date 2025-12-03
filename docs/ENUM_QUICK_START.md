# Quick Start - Using Enums

## Generate Enums

```bash
# Generate all enums
php artisan db:generate-enums

# Generate specific tables
php artisan db:generate-enums --tables=orders,products

# Overwrite existing
php artisan db:generate-enums --force
```

## Basic Usage

```php
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;

// Create with enum
$order = Order::create([
    'status' => OrderStatus::PENDING,
    'payment_method' => PaymentMethod::COD,
]);

// Get value & label
echo $order->status->value;  // 'pending'
echo $order->status->label(); // 'Chờ xác nhận'

// Comparison
if ($order->status === OrderStatus::PENDING) {
    // Do something
}

// Update
$order->status = OrderStatus::CONFIRMED;
$order->save();
```

## For Forms/API

```php
// Get all options
$statuses = OrderStatus::options();
// Returns: [
//   ['value' => 'pending', 'label' => 'Chờ xác nhận'],
//   ['value' => 'confirmed', 'label' => 'Đã xác nhận'],
//   ...
// ]

// Get all cases
$allStatuses = OrderStatus::cases();

// From string
$status = OrderStatus::from('pending');
$status = OrderStatus::tryFrom('invalid'); // Returns null if not found
```

## Validation

```php
use Illuminate\Validation\Rules\Enum;

$request->validate([
    'status' => ['required', new Enum(OrderStatus::class)],
]);
```

## Available Enums

- **OrderStatus** - pending, confirmed, processing, shipping, delivered, cancelled, refunded
- **PaymentStatus** - unpaid, paid, partially_refunded, refunded
- **PaymentMethod** - cod, credit_card, e_wallet, bank_transfer
- **ProductStatus** - draft, active, inactive, out_of_stock
- **ShippingStatus** - pending, picked_up, in_transit, out_for_delivery, delivered, failed, returned
- And 15 more...

See full documentation: [docs/ENUM_GENERATOR.md](docs/ENUM_GENERATOR.md)
