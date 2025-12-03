# Enum Generator - ShopNest E-commerce

## Tổng quan

Command `db:generate-enums` tự động tạo các PHP enum classes từ các cột enum trong database migrations và tự động cập nhật models để sử dụng enum casting.

## Tạo ra

✅ **20 Enum Classes** đã được tạo tại `app/Enums/`:

### Core Enums
1. **DivisionType** - Loại đơn vị hành chính (Tỉnh, Xã)
2. **Gender** - Giới tính (Nam, Nữ, Khác)
3. **AttributeInputType** - Loại input thuộc tính (Select, Color, Text)
4. **Theme** - Giao diện (Sáng, Tối, Tự động)

### Product & Promotion
5. **ProductStatus** - Trạng thái sản phẩm (Nháp, Đang hoạt động, Tạm ngưng, Hết hàng)
6. **PromotionType** - Loại khuyến mãi (Phần trăm, Số tiền cố định, Miễn phí ship, Mua X tặng Y)

### Order & Payment
7. **OrderStatus** - Trạng thái đơn hàng (Chờ xác nhận, Đã xác nhận, Đang xử lý, v.v.)
8. **PaymentStatus** - Trạng thái thanh toán (Chưa thanh toán, Đã thanh toán, v.v.)
9. **PaymentMethod** - Phương thức thanh toán (COD, Thẻ tín dụng, Ví điện tử, Chuyển khoản)
10. **TransactionStatus** - Trạng thái giao dịch (Đang xử lý, Thành công, Thất bại)

### Shipping & Logistics
11. **ShippingStatus** - Trạng thái vận chuyển (Chờ lấy hàng, Đã lấy hàng, Đang vận chuyển, v.v.)
12. **ShipmentJourneyStatus** - Trạng thái hành trình (Đã lấy hàng, Tại trung tâm, v.v.)
13. **VehicleType** - Loại phương tiện (Xe máy, Ô tô, Xe tải)

### Returns & Disputes
14. **ReturnStatus** - Trạng thái trả hàng (Yêu cầu, Đã chấp nhận, Từ chối, v.v.)
15. **DisputeStatus** - Trạng thái tranh chấp (Đang mở, Đang xem xét, Đã giải quyết, Đã đóng)

### Communication
16. **ChatRoomType** - Loại phòng chat (Khách hàng - Người bán, Khách hàng - Hỗ trợ)
17. **MessageType** - Loại tin nhắn (Văn bản, Hình ảnh, Link sản phẩm)

### Media & Reviews
18. **ReviewMediaType** - Loại media đánh giá (Hình ảnh, Video)

### Security
19. **TwoFactorMethod** - Phương thức xác thực 2 yếu tố (Authenticator, SMS, Email)
20. **TwoFactorChallengeMethod** - Phương thức thử thách 2FA (Authenticator, SMS, Email, Mã dự phòng)

## Cách sử dụng Command

### Generate tất cả enums
```bash
php artisan db:generate-enums
```

### Generate cho tables cụ thể
```bash
php artisan db:generate-enums --tables=orders,products,users
```

### Overwrite enums đã tồn tại
```bash
php artisan db:generate-enums --force
```

## Cấu trúc Enum

Mỗi enum được tạo ra có cấu trúc:

```php
<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case PROCESSING = 'processing';
    // ... more cases

    private const LABELS = [
        self::PENDING->value => 'Chờ xác nhận',
        self::CONFIRMED->value => 'Đã xác nhận',
        // ... more labels
    ];

    /**
     * Get the label for the enum case
     */
    public function label(): string
    {
        return self::LABELS[$this->value];
    }

    /**
     * Get all enum values with their labels
     */
    public static function options(): array
    {
        return array_map(
            fn(self $enum) => ['value' => $enum->value, 'label' => $enum->label()],
            self::cases()
        );
    }
}
```

## Cách sử dụng trong Code

### 1. Trong Models

Models đã được tự động cập nhật với enum casting:

```php
// app/Models/Order.php
namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\PaymentMethod;

class Order extends Model
{
    protected $casts = [
        'status' => OrderStatus::class,
        'payment_status' => PaymentStatus::class,
        'payment_method' => PaymentMethod::class,
    ];
}
```

### 2. Tạo và lưu dữ liệu

```php
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;

// Tạo order mới
$order = Order::create([
    'order_number' => 'ORD-001',
    'status' => OrderStatus::PENDING,
    'payment_method' => PaymentMethod::COD,
    'total_amount' => 1000000,
]);

// Cập nhật status
$order->status = OrderStatus::CONFIRMED;
$order->save();
```

### 3. Truy vấn với Enum

```php
use App\Enums\OrderStatus;

// Tìm orders theo status
$pendingOrders = Order::where('status', OrderStatus::PENDING)->get();

// Dùng value
$pendingOrders = Order::where('status', OrderStatus::PENDING->value)->get();

// Multiple statuses
$orders = Order::whereIn('status', [
    OrderStatus::PENDING->value,
    OrderStatus::CONFIRMED->value,
])->get();
```

### 4. Hiển thị label

```php
// Trong controller hoặc view
$order = Order::find(1);

echo $order->status->value;  // Output: 'pending'
echo $order->status->label(); // Output: 'Chờ xác nhận'
echo $order->status->name;    // Output: 'PENDING'
```

### 5. So sánh Enum

```php
$order = Order::find(1);

if ($order->status === OrderStatus::PENDING) {
    // Do something
}

// Check nhiều trường hợp
if (in_array($order->status, [OrderStatus::PENDING, OrderStatus::CONFIRMED])) {
    // Do something
}
```

### 6. Tạo Enum từ string

```php
use App\Enums\OrderStatus;

// from() - Throw exception nếu không tìm thấy
$status = OrderStatus::from('pending'); // Returns OrderStatus::PENDING

// tryFrom() - Return null nếu không tìm thấy
$status = OrderStatus::tryFrom('invalid'); // Returns null
$status = OrderStatus::tryFrom('pending'); // Returns OrderStatus::PENDING
```

### 7. Lấy tất cả cases

```php
use App\Enums\OrderStatus;

// Get all enum cases
$allStatuses = OrderStatus::cases();
// Returns: [OrderStatus::PENDING, OrderStatus::CONFIRMED, ...]

// Get all values
$values = array_map(fn($case) => $case->value, OrderStatus::cases());
// Returns: ['pending', 'confirmed', 'processing', ...]
```

### 8. Sử dụng trong Forms (API Response)

```php
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;

// Get options for select dropdown
$statusOptions = OrderStatus::options();
// Returns:
// [
//     ['value' => 'pending', 'label' => 'Chờ xác nhận'],
//     ['value' => 'confirmed', 'label' => 'Đã xác nhận'],
//     ...
// ]

// Return in API
return response()->json([
    'order' => $order,
    'available_statuses' => OrderStatus::options(),
    'payment_methods' => PaymentMethod::options(),
]);
```

### 9. Validation

```php
use App\Enums\OrderStatus;
use Illuminate\Validation\Rules\Enum;

// Trong Form Request hoặc Validator
$validated = $request->validate([
    'status' => ['required', new Enum(OrderStatus::class)],
]);

// Hoặc với Rule::enum() (Laravel 10+)
$validated = $request->validate([
    'status' => ['required', Rule::enum(OrderStatus::class)],
]);
```

### 10. Trong Seeders

```php
use App\Enums\ProductStatus;
use App\Models\Product;

Product::create([
    'product_name' => 'iPhone 15',
    'status' => ProductStatus::ACTIVE,
    'base_price' => 20000000,
]);
```

### 11. Switch Statement

```php
$order = Order::find(1);

$message = match($order->status) {
    OrderStatus::PENDING => 'Order is waiting for confirmation',
    OrderStatus::CONFIRMED => 'Order has been confirmed',
    OrderStatus::SHIPPING => 'Order is being shipped',
    OrderStatus::DELIVERED => 'Order has been delivered',
    default => 'Unknown status',
};
```

### 12. Blade Templates (Frontend)

```blade
<!-- Display status label -->
<span class="badge">
    {{ $order->status->label() }}
</span>

<!-- Select dropdown -->
<select name="status">
    @foreach(App\Enums\OrderStatus::cases() as $status)
        <option value="{{ $status->value }}" 
                {{ $order->status === $status ? 'selected' : '' }}>
            {{ $status->label() }}
        </option>
    @endforeach
</select>

<!-- Status badge with color -->
@php
    $badgeClass = match($order->status) {
        App\Enums\OrderStatus::PENDING => 'badge-warning',
        App\Enums\OrderStatus::CONFIRMED => 'badge-info',
        App\Enums\OrderStatus::DELIVERED => 'badge-success',
        App\Enums\OrderStatus::CANCELLED => 'badge-danger',
        default => 'badge-secondary',
    };
@endphp
<span class="badge {{ $badgeClass }}">
    {{ $order->status->label() }}
</span>
```

## Lợi ích của Enum

✅ **Type Safety**: IDE autocomplete và type checking  
✅ **Consistency**: Giá trị enum nhất quán trong toàn bộ codebase  
✅ **Readability**: Code dễ đọc và maintain hơn  
✅ **Refactoring**: Dễ dàng refactor khi cần thay đổi  
✅ **Labels**: Hỗ trợ đa ngôn ngữ với labels tiếng Việt  
✅ **Validation**: Tự động validate với Enum rule  
✅ **Documentation**: Self-documenting code

## Testing

Chạy test script để xem enum hoạt động:

```bash
php test_enums.php
```

## Models đã được cập nhật

Tất cả 20 models sau đã được tự động cập nhật với enum casting:

1. AdministrativeDivision
2. Attribute
3. User
4. Product
5. Promotion
6. Order (3 enums)
7. Transaction (2 enums)
8. ShippingDetail
9. ReviewMedia
10. ShipperProfile
11. ShipmentJourney
12. Return
13. Dispute
14. ChatRoom
15. ChatMessage
16. UserPreference
17. TwoFactorAuthentication
18. TwoFactorChallenge

## Thêm Enum mới

Để thêm enum mới cho table khác, chỉnh sửa method `defineEnumMetadata()` trong command:

```php
protected function defineEnumMetadata()
{
    $this->enumDefinitions = [
        // ... existing definitions
        
        'your_table_name' => [
            [
                'column' => 'your_column',
                'enum_name' => 'YourEnumName',
                'cases' => [
                    'CASE_1' => 'value_1',
                    'CASE_2' => 'value_2',
                ],
                'labels' => [
                    'CASE_1' => 'Label 1',
                    'CASE_2' => 'Label 2',
                ],
            ],
        ],
    ];
}
```

Sau đó chạy lại command:

```bash
php artisan db:generate-enums --force
```

## Best Practices

1. **Luôn sử dụng enum constants** thay vì hardcode strings:
   ```php
   // ✅ Good
   $order->status = OrderStatus::PENDING;
   
   // ❌ Bad
   $order->status = 'pending';
   ```

2. **Sử dụng label() cho UI**:
   ```php
   // ✅ Good
   echo $order->status->label();
   
   // ❌ Bad
   echo $order->status->value;
   ```

3. **Sử dụng match() thay vì switch**:
   ```php
   // ✅ Good (PHP 8+)
   $color = match($status) {
       OrderStatus::PENDING => 'yellow',
       OrderStatus::DELIVERED => 'green',
   };
   
   // ❌ Old way
   switch($status) {
       case OrderStatus::PENDING:
           $color = 'yellow';
           break;
       case OrderStatus::DELIVERED:
           $color = 'green';
           break;
   }
   ```

4. **Import enum ở đầu file**:
   ```php
   use App\Enums\OrderStatus;
   use App\Enums\PaymentMethod;
   ```

## Tóm tắt

- ✅ 20 enum classes được tạo
- ✅ 20 models được cập nhật với enum casting
- ✅ Mỗi enum có label() method cho hiển thị
- ✅ Mỗi enum có options() method cho forms
- ✅ Type-safe và IDE-friendly
- ✅ Dễ dàng mở rộng và maintain
