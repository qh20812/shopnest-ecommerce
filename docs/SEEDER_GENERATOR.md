# Database Seeder Generator

Công cụ tự động generate seeders cho toàn bộ database schema.

## 🚀 Cách sử dụng

### 1. Generate seeders cho tất cả bảng

```bash
php artisan db:generate-seeders
```

Lệnh này sẽ tạo seeders cho tất cả các bảng đã định nghĩa trong schema với số lượng mặc định (10 records/table).

### 2. Tùy chỉnh số lượng records

```bash
php artisan db:generate-seeders --count=50
```

Tạo 50 records cho mỗi bảng.

### 3. Generate seeders cho bảng cụ thể

```bash
php artisan db:generate-seeders --tables=countries --tables=users --tables=products
```

Chỉ tạo seeders cho các bảng được chỉ định.

### 4. Overwrite seeders đã tồn tại

```bash
php artisan db:generate-seeders --force
```

Ghi đè các seeder files đã tồn tại.

### 5. Kết hợp options

```bash
php artisan db:generate-seeders --tables=users --tables=shops --count=100 --force
```

## 📊 Chạy seeders

### Chạy tất cả seeders

```bash
php artisan db:seed
```

Lệnh này sẽ chạy `DatabaseSeeder.php` - tự động gọi tất cả seeders theo thứ tự dependency.

### Chạy seeder cụ thể

```bash
php artisan db:seed --class=UsersSeeder
```

### Fresh migration + seed

```bash
php artisan migrate:fresh --seed
```

Xóa toàn bộ database, chạy lại migrations và seeders.

## 📋 Danh sách seeders được tạo

Command tự động tạo seeders cho các bảng sau (theo thứ tự dependency):

### 1. Reference Data (Priority)
- `CountriesSeeder` - 20 countries
- `AdministrativeDivisionsSeeder` - 50 provinces/districts/wards
- `RolesSeeder` - 5 roles (admin, customer, seller, shipper, moderator)
- `PermissionsSeeder` - 20 permissions
- `BrandsSeeder` - 30 brands
- `CategoriesSeeder` - 40 categories (with hierarchy)
- `AttributesSeeder` - 10 attributes (Size, Color, etc.)
- `AttributeValuesSeeder` - 50 attribute values

### 2. Users & Auth
- `UsersSeeder` - 100 users
- `UserAddressesSeeder` - 200 addresses

### 3. Business Data
- `ShopsSeeder` - 50 shops
- `HubsSeeder` - 20 distribution hubs
- `ProductsSeeder` - 200 products
- `ProductImagesSeeder` - 500 product images
- `ProductVariantsSeeder` - 400 product variants

### 4. Transactions
- `OrdersSeeder` - 300 orders
- `OrderItemsSeeder` - 600 order items

### 5. User Interactions
- `CartItemsSeeder` - 200 cart items
- `ReviewsSeeder` - 500 reviews
- `WishlistsSeeder` - 100 wishlists
- `WishlistItemsSeeder` - 300 wishlist items
- `NotificationsSeeder` - 500 notifications

## 🎯 Tính năng

### ✅ Auto-generate faker data
- Tự động chọn faker methods phù hợp dựa trên tên column và type
- Hỗ trợ foreign keys với random IDs
- Xử lý unique constraints (skip duplicates)
- Hỗ trợ nullable columns với `optional()`

### ✅ Smart type mapping
```php
// String columns
'email' => $faker->safeEmail
'phone_number' => $faker->phoneNumber
'slug' => $faker->slug
'url' => $faker->url
'address' => $faker->address
'name' => $faker->name

// Numeric columns
'price' => $faker->randomFloat(2, 10, 10000)
'quantity' => $faker->numberBetween(0, 1000)
'rating' => $faker->randomFloat(2, 0, 5)

// Date/Time columns
'date_of_birth' => $faker->date("Y-m-d", "-18 years")
'created_at' => now()

// Boolean columns
'is_active' => true // or $faker->boolean

// Enum columns
'status' => $faker->randomElement(['active', 'inactive'])
```

### ✅ Foreign key handling
```php
// Tự động reference đến bảng parent
'user_id' => $faker->numberBetween(1, 100)
'shop_id' => $faker->numberBetween(1, 50)

// Nullable foreign keys
'parent_id' => $faker->optional()->numberBetween(1, 100)
```

### ✅ Error handling
- Skip duplicate entries (unique constraints)
- Retry logic for foreign key violations
- Transaction support

## 🔧 Tùy chỉnh seeders

### Thay đổi số lượng records cho bảng cụ thể

Sửa file `GenerateSeedersFromSchema.php`, tìm bảng trong method `defineSchema()`:

```php
'users' => [
    'columns' => [...],
    'count' => 500, // Thay đổi từ 100 -> 500
],
```

### Thêm custom faker logic

Sửa file seeder đã generate:

```php
public function run(): void
{
    $faker = \Faker\Factory::create();
    
    for ($i = 0; $i < 100; $i++) {
        try {
            DB::table('users')->insert([
                'email' => $faker->unique()->safeEmail,
                'password' => bcrypt('password123'), // Custom password
                'full_name' => $faker->firstName() . ' ' . $faker->lastName(), // Custom name
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            continue;
        }
    }
}
```

## 📈 Performance Tips

### 1. Batch insert cho bảng lớn

```php
// Thay vì insert từng record
$data = [];
for ($i = 0; $i < 1000; $i++) {
    $data[] = [/* ... */];
}
DB::table('products')->insert($data);
```

### 2. Disable foreign key checks

```php
DB::statement('SET FOREIGN_KEY_CHECKS=0;');
// Seed data
DB::statement('SET FOREIGN_KEY_CHECKS=1;');
```

### 3. Sử dụng database transactions

```php
DB::transaction(function () {
    // Seed data
});
```

## 🐛 Troubleshooting

### Lỗi: "SQLSTATE[23000]: Integrity constraint violation"

**Nguyên nhân:** Foreign key reference không tồn tại hoặc unique constraint violation.

**Giải pháp:**
1. Chạy seeders theo đúng thứ tự (DatabaseSeeder đã xử lý)
2. Đảm bảo bảng parent đã có data trước
3. Command tự động skip duplicates với try-catch

### Lỗi: "Class 'XXXSeeder' not found"

**Giải pháp:**
```bash
composer dump-autoload
```

### Data không đúng format

**Giải pháp:** Tùy chỉnh faker methods trong file seeder hoặc schema definition.

## 📚 Tham khảo

- [Laravel Seeding Documentation](https://laravel.com/docs/seeding)
- [Faker PHP Documentation](https://fakerphp.github.io/)
- [Database Testing](https://laravel.com/docs/database-testing)

## 🎉 Kết quả

Sau khi chạy `php artisan db:seed`, database sẽ có:
- ✅ 20 countries
- ✅ 50 administrative divisions
- ✅ 5 roles
- ✅ 20 permissions
- ✅ 30 brands
- ✅ 40 categories
- ✅ 100 users
- ✅ 200 user addresses
- ✅ 50 shops
- ✅ 200 products
- ✅ 500 product images
- ✅ 400 product variants
- ✅ 300 orders
- ✅ 600 order items
- ✅ 500 reviews
- ✅ **Total: ~3,000+ records** 🚀

Perfect cho development và testing!
