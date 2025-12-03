# Administrative Divisions - Vietnam Structure

## 📋 Overview

Dự án đã được cập nhật để phản ánh cấu trúc hành chính mới của Việt Nam sau khi sáp nhập và bỏ cấp huyện. Hiện tại chỉ còn **2 cấp**:

- **Tỉnh/Thành phố** (Province)
- **Xã/Phường/Thị trấn** (Ward)

## 🗂️ Database Structure

### Migration: `administrative_divisions`

```php
Schema::create('administrative_divisions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('country_id')->constrained('countries');
    $table->foreignId('parent_id')->nullable()->constrained('administrative_divisions');
    $table->string('division_name', 100);
    $table->enum('division_type', ['province', 'ward']);
    $table->string('code', 20)->nullable();
    $table->string('codename', 100)->nullable();
    $table->string('short_codename', 100)->nullable();
    $table->string('phone_code', 10)->nullable();
    $table->timestamps();
    
    // Indexes
    $table->index(['country_id']);
    $table->index(['parent_id']);
    $table->index(['division_type']);
    $table->index(['code']);
    $table->index(['codename']);
});
```

### Key Fields

| Field | Type | Description |
|-------|------|-------------|
| `division_type` | enum | `province` hoặc `ward` |
| `parent_id` | foreignId | NULL cho tỉnh, ID tỉnh cho xã |
| `code` | string | Mã đơn vị hành chính |
| `codename` | string | Tên không dấu (vd: `ha_noi`) |
| `short_codename` | string | Tên rút gọn |
| `phone_code` | string | Mã vùng điện thoại (chỉ cho tỉnh) |

## 📊 Data Statistics

Sau khi seed dữ liệu từ `provinces.json`:

- **34 Tỉnh/Thành phố**
- **3,321 Xã/Phường/Thị trấn**
- **Tổng: 3,355 đơn vị hành chính**

## 🚀 Usage

### 1. Run Migrations

```bash
php artisan migrate:fresh
```

### 2. Seed Vietnam Country

```bash
php artisan tinker --execute="DB::table('countries')->insert([
    'country_name' => 'Việt Nam',
    'iso_code_2' => 'VN',
    'iso_code_3' => 'VNM',
    'phone_code' => '84',
    'currency' => 'VND',
    'is_active' => true,
    'created_at' => now(),
    'updated_at' => now()
]);"
```

### 3. Seed Administrative Divisions

```bash
php artisan db:seed --class=AdministrativeDivisionSeeder
```

Seeder sẽ tự động:
- Đọc file `public/data/provinces.json`
- Tìm Vietnam trong bảng `countries`
- Import 34 tỉnh
- Import 3,321 xã/phường (nested trong tỉnh)

### 4. Seed All Data

```bash
php artisan db:seed
```

## 🏗️ Model Usage

### Query Examples

```php
use App\Models\AdministrativeDivision;

// Lấy tất cả tỉnh
$provinces = AdministrativeDivision::provinces()->get();

// Lấy tất cả xã
$wards = AdministrativeDivision::wards()->get();

// Lấy Hà Nội và các phường/xã
$hanoi = AdministrativeDivision::where('codename', 'ha_noi')->first();
$hanoiWards = $hanoi->wards; // hoặc $hanoi->children

// Lấy tỉnh của một xã
$ward = AdministrativeDivision::find(100);
$province = $ward->parent;

// Tìm theo mã
$province = AdministrativeDivision::where('code', '1')->first(); // Hà Nội
```

### Factory Usage

```php
// Tạo tỉnh
AdministrativeDivision::factory()->province()->create();

// Tạo xã (thuộc tỉnh ID 1)
AdministrativeDivision::factory()->ward()->create([
    'parent_id' => 1
]);

// Tạo 10 tỉnh với các xã
AdministrativeDivision::factory()->province()->count(10)->create()->each(function ($province) {
    AdministrativeDivision::factory()->ward()->count(5)->create([
        'parent_id' => $province->id
    ]);
});
```

## 📁 Files Created/Updated

### Migrations
- ✅ `2024_01_01_000002_create_administrative_divisions_table.php`
  - Updated: Changed `enum` from `['province', 'district', 'ward']` to `['province', 'ward']`
  - Added: `codename`, `short_codename`, `phone_code` fields

### Models
- ✅ `app/Models/AdministrativeDivision.php`
  - Relationships: `country()`, `parent()`, `children()`, `wards()`
  - Scopes: `provinces()`, `wards()`

### Factories
- ✅ `database/factories/AdministrativeDivisionFactory.php`
  - States: `province()`, `ward()`

### Seeders
- ✅ `database/seeders/AdministrativeDivisionSeeder.php`
  - Imports from `public/data/provinces.json`
  - Auto-creates hierarchy (province → wards)
  - Handles duplicates gracefully

### Schema Generators
- ✅ `app/Console/Commands/GenerateMigrationsFromSchema.php`
- ✅ `app/Console/Commands/GenerateSeedersFromSchema.php`

## 🧪 Testing

Test script: `test_divisions.php`

```bash
php test_divisions.php
```

Output:
```
=== ADMINISTRATIVE DIVISIONS TEST ===

📊 Statistics:
  Provinces: 34
  Wards: 3321
  Total: 3355

📍 Sample Provinces:
  - Thành phố Hà Nội (Code: 1, Phone: 24)
      └─ Phường Hoàn Kiếm (70)
      └─ Phường Cửa Nam (73)
      └─ Phường Ba Đình (4)
  - Cao Bằng (Code: 4, Phone: 206)
      └─ Phường Thục Phán (1273)
      └─ Phường Nùng Trí Cao (1279)
      └─ Phường Tân Giang (1288)
...
```

## 🔄 Data Source

Dữ liệu được import từ: `public/data/provinces.json`

Format:
```json
[
  {
    "name": "Thành phố Hà Nội",
    "code": 1,
    "codename": "ha_noi",
    "division_type": "thành phố trung ương",
    "phone_code": 24,
    "wards": [
      {
        "name": "Phường Hoàn Kiếm",
        "code": 70,
        "codename": "hoan_kiem",
        "short_codename": "hoan_kiem"
      },
      ...
    ]
  },
  ...
]
```

## 📝 Notes

1. **Mã vùng điện thoại** (`phone_code`): Chỉ tỉnh/thành phố mới có, xã/phường không có
2. **Hierarchy**: Xã luôn có `parent_id` trỏ đến tỉnh, tỉnh có `parent_id = NULL`
3. **Unique constraints**: Không có constraint unique cho `code` vì có thể trùng giữa các cấp
4. **Error handling**: Seeder có try-catch để skip duplicates khi chạy lại

## ✅ Checklist

- [x] Updated migration to 2-level structure
- [x] Created AdministrativeDivision model with relationships
- [x] Created AdministrativeDivisionFactory with states
- [x] Created AdministrativeDivisionSeeder importing from JSON
- [x] Updated schema generators
- [x] Tested with real Vietnam data (34 provinces, 3321 wards)
- [x] Updated DatabaseSeeder
- [x] Created documentation
