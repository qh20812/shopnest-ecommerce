# Generate Models from Migrations

## 📋 Overview

Command tự động tạo Eloquent models từ các migrations hiện có trong dự án. Command này sẽ:

- Đọc schema definition với metadata về relationships
- Tạo models với fillable, casts, relationships
- Hỗ trợ SoftDeletes, HasFactory, Notifiable traits
- Tự động skip pivot tables
- Hỗ trợ morphTo relationships

## 🚀 Usage

### Generate All Models

```bash
php artisan db:generate-models
```

### Generate Specific Tables

```bash
php artisan db:generate-models --tables=users --tables=products --tables=orders
```

### Overwrite Existing Models

```bash
php artisan db:generate-models --force
```

## 📊 Results

Command đã tạo **49 models** từ migrations:

### Core Models
- Country
- AdministrativeDivision
- Role
- Permission
- Brand
- Category
- Attribute
- AttributeValue

### User & Auth
- User (with Notifiable)
- UserAddress

### Shop & Products
- Shop
- Hub
- Product (with SoftDeletes)
- ProductImage
- ProductVariant (with SoftDeletes)
- ProductQuestion
- ProductAnswer
- ProductView

### Sales & Promotions
- FlashSaleEvent
- FlashSaleProduct
- Promotion
- PromotionCode

### Orders & Transactions
- Order (with SoftDeletes)
- OrderItem
- Transaction
- ShippingDetail

### Cart & Wishlist
- CartItem
- Wishlist
- WishlistItem

### Reviews
- Review (with SoftDeletes)
- ReviewMedia

### Shipping & Logistics
- ShipperProfile
- ShipmentJourney
- ShipperRating

### Returns & Disputes
- Return (with SoftDeletes)
- ReturnItem
- Dispute
- DisputeMessage

### Chat & Notifications
- ChatRoom
- ChatMessage
- Notification

### Analytics
- UserEvent
- SearchHistory
- UserPreference
- AnalyticsReport

### International & 2FA
- InternationalAddress (with morphTo)
- TwoFactorAuthentication
- TwoFactorChallenge
- TwoFactorTrustedDevice

## 📝 Model Structure

Each generated model includes:

### 1. **Table Name**
```php
protected $table = 'products';
```

### 2. **Fillable Attributes**
```php
protected $fillable = [
    'shop_id',
    'category_id',
    'product_name',
    'slug',
    // ...
];
```

### 3. **Hidden Attributes** (for sensitive data)
```php
protected $hidden = [
    'password',
    'remember_token',
    'two_factor_secret',
];
```

### 4. **Casts**
```php
protected $casts = [
    'specifications' => 'json',
    'base_price' => 'decimal:2',
    'is_active' => 'boolean',
    'created_at' => 'datetime',
];
```

### 5. **Relationships**

#### BelongsTo
```php
public function shop()
{
    return $this->belongsTo(\App\Models\Shop::class);
}
```

#### HasMany
```php
public function variants()
{
    return $this->hasMany(\App\Models\ProductVariant::class);
}
```

#### BelongsToMany
```php
public function roles()
{
    return $this->belongsToMany(\App\Models\Role::class, 'role_user');
}
```

#### MorphTo
```php
public function addressable()
{
    return $this->morphTo();
}
```

### 6. **Scopes** (for specific models)
```php
public function scopeProvinces($query)
{
    return $query->where('division_type', 'province');
}

public function scopeWards($query)
{
    return $query->where('division_type', 'ward');
}
```

### 7. **Traits**
- `HasFactory` - All models
- `SoftDeletes` - Models with `deleted_at`
- `Notifiable` - User model

## 🔍 Example Models

### Product Model

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'products';

    protected $fillable = [
        'shop_id',
        'category_id',
        'brand_id',
        'product_name',
        'slug',
        'base_price',
        // ... more fields
    ];

    protected $casts = [
        'specifications' => 'json',
        'base_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function shop()
    {
        return $this->belongsTo(\App\Models\Shop::class);
    }

    public function category()
    {
        return $this->belongsTo(\App\Models\Category::class);
    }

    public function variants()
    {
        return $this->hasMany(\App\Models\ProductVariant::class);
    }

    public function reviews()
    {
        return $this->hasMany(\App\Models\Review::class);
    }
}
```

### User Model

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'email',
        'password',
        'full_name',
        // ...
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function roles()
    {
        return $this->belongsToMany(\App\Models\Role::class, 'role_user');
    }

    public function addresses()
    {
        return $this->hasMany(\App\Models\UserAddress::class);
    }

    public function orders()
    {
        return $this->hasMany(\App\Models\Order::class, 'customer_id');
    }
}
```

### AdministrativeDivision Model (with scopes)

```php
public function scopeProvinces($query)
{
    return $query->where('division_type', 'province');
}

public function scopeWards($query)
{
    return $query->where('division_type', 'ward');
}
```

## 🎯 Features

### ✅ Automatic Detection
- **SoftDeletes**: Auto-detected from `deleted_at` column
- **Timestamps**: Auto-handled by Laravel
- **JSON Casts**: Auto-detected for JSON columns
- **Decimal Precision**: Set to 2 decimals for prices
- **Boolean**: Auto-cast for boolean columns
- **DateTime**: Auto-cast for datetime/timestamp columns

### ✅ Relationship Types
- `belongsTo` - Foreign key relationships
- `hasMany` - One-to-many relationships
- `belongsToMany` - Many-to-many with pivot table
- `hasOne` - One-to-one relationships
- `morphTo` - Polymorphic relationships

### ✅ Pivot Tables (Auto-Skipped)
- `role_user`
- `permission_role`
- `attribute_value_product_variant`
- `order_promotion`
- `chat_participants`

### ✅ Special Models
- **User**: Extends `Authenticatable`, includes `Notifiable`
- **AdministrativeDivision**: Includes custom scopes
- **InternationalAddress**: Includes `morphTo` relationship

## 🛠️ Customization

Sau khi generate, bạn có thể customize models:

1. **Add accessors/mutators**
```php
public function getFormattedPriceAttribute()
{
    return number_format($this->base_price, 0, ',', '.') . ' VND';
}
```

2. **Add custom methods**
```php
public function isInStock()
{
    return $this->stock_quantity > 0;
}
```

3. **Add query scopes**
```php
public function scopeActive($query)
{
    return $query->where('is_active', true);
}
```

4. **Customize relationships**
```php
public function activeVariants()
{
    return $this->hasMany(ProductVariant::class)
        ->where('is_active', true);
}
```

## 📚 Usage Examples

### Query với Relationships

```php
// Eager loading
$products = Product::with(['shop', 'category', 'variants'])
    ->where('status', 'active')
    ->get();

// Nested eager loading
$orders = Order::with(['customer', 'items.productVariant'])
    ->where('status', 'delivered')
    ->get();

// Lazy eager loading
$product->load('reviews.user', 'reviews.media');
```

### Scopes

```php
// Administrative divisions
$provinces = AdministrativeDivision::provinces()->get();
$wards = AdministrativeDivision::wards()->get();
```

### Relationships

```php
// Create with relationship
$user = User::find(1);
$user->addresses()->create([
    'address_label' => 'Home',
    'recipient_name' => 'John Doe',
    // ...
]);

// Attach many-to-many
$user->roles()->attach([1, 2, 3]);

// Sync relationships
$product->variants()->sync([10, 20, 30]);
```

## 🔄 Regenerate Models

Nếu thay đổi schema hoặc relationships:

```bash
# Regenerate all models
php artisan db:generate-models --force

# Regenerate specific models
php artisan db:generate-models --tables=products --tables=orders --force
```

## ✅ Checklist

- [x] Generated 49 models from migrations
- [x] All relationships defined
- [x] Fillable attributes set
- [x] Casts configured
- [x] Traits added (HasFactory, SoftDeletes, Notifiable)
- [x] Pivot tables skipped
- [x] MorphTo relationships handled
- [x] Custom scopes for AdministrativeDivision
- [x] User model extends Authenticatable

## 📝 Next Steps

1. **Create Controllers**
   ```bash
   php artisan make:controller ProductController --resource
   ```

2. **Create Form Requests**
   ```bash
   php artisan make:request StoreProductRequest
   ```

3. **Create API Resources**
   ```bash
   php artisan make:resource ProductResource
   ```

4. **Test Relationships**
   ```bash
   php artisan tinker
   >>> User::with('roles', 'addresses')->first()
   >>> Product::with('shop', 'variants')->find(1)
   ```
