# Role-Based Access Control System

## Overview
This document explains the role-based access control (RBAC) system implemented in ShopNest.

## Components Created

### 1. Enum: `RoleType`
**Location:** `app/Enums/RoleType.php`

Defines the four main roles in the system:
- `ADMIN` - Quản trị viên
- `CUSTOMER` - Khách hàng
- `SELLER` - Người bán
- `SHIPPER` - Nhân viên giao hàng

### 2. Pivot Models

#### RoleUser
**Location:** `app/Models/RoleUser.php`
- Manages the `role_user` pivot table
- Links users to roles (many-to-many relationship)

#### PermissionRole
**Location:** `app/Models/PermissionRole.php`
- Manages the `permission_role` pivot table
- Links roles to permissions (many-to-many relationship)

#### OrderPromotion
**Location:** `app/Models/OrderPromotion.php`
- Manages the `order_promotion` pivot table
- Links orders to promotions with discount amount
- Includes `discount_amount` field

### 3. Middleware

Four middleware classes for role-based access control:

#### IsCustomer
**Location:** `app/Http/Middleware/Auth/IsCustomer.php`
**Alias:** `is.customer`
- Ensures only users with 'customer' role can access customer-specific routes

#### IsAdmin
**Location:** `app/Http/Middleware/Auth/IsAdmin.php`
**Alias:** `is.admin`
- Ensures only users with 'admin' role can access admin panel

#### IsSeller
**Location:** `app/Http/Middleware/Auth/IsSeller.php`
**Alias:** `is.seller`
- Ensures only users with 'seller' role can access seller dashboard

#### IsShipper
**Location:** `app/Http/Middleware/Auth/IsShipper.php`
**Alias:** `is.shipper`
- Ensures only users with 'shipper' role can access shipper routes

### 4. User Model Updates

**Location:** `app/Models/User.php`

Now extends `Illuminate\Foundation\Auth\User as Authenticatable` instead of `Model`.

Added helper methods:
```php
// Check specific role
$user->isCustomer();  // returns bool
$user->isAdmin();     // returns bool
$user->isSeller();    // returns bool
$user->isShipper();   // returns bool

// Check if user has a role
$user->hasRole('customer');  // returns bool

// Check if user has any of given roles
$user->hasAnyRole(['admin', 'seller']);  // returns bool

// Check if user has all of given roles
$user->hasAllRoles(['customer', 'seller']);  // returns bool
```

### 5. Updated Relationships

All many-to-many relationships now use custom pivot models:

**User ↔ Role** (via RoleUser)
```php
$user->roles()->attach($roleId);
$user->roles()->detach($roleId);
```

**Role ↔ Permission** (via PermissionRole)
```php
$role->permissions()->attach($permissionId);
```

**Order ↔ Promotion** (via OrderPromotion)
```php
$order->promotions()->attach($promotionId, ['discount_amount' => 50000]);
$order->promotions->first()->pivot->discount_amount;
```

## Usage Examples

### 1. Protecting Routes

In your routes file (`routes/web.php`):

```php
use Illuminate\Support\Facades\Route;

// Customer routes
Route::middleware(['auth', 'is.customer'])->group(function () {
    Route::get('/customer/orders', [OrderController::class, 'index']);
    Route::get('/customer/profile', [ProfileController::class, 'show']);
    Route::get('/customer/addresses', [AddressController::class, 'index']);
});

// Admin routes
Route::middleware(['auth', 'is.admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index']);
    Route::resource('users', AdminUserController::class);
});

// Seller routes
Route::middleware(['auth', 'is.seller'])->prefix('seller')->group(function () {
    Route::get('/dashboard', [SellerDashboardController::class, 'index']);
    Route::resource('products', SellerProductController::class);
});

// Shipper routes
Route::middleware(['auth', 'is.shipper'])->prefix('shipper')->group(function () {
    Route::get('/deliveries', [DeliveryController::class, 'index']);
    Route::patch('/deliveries/{id}/status', [DeliveryController::class, 'updateStatus']);
});
```

### 2. Assigning Roles to Users

```php
use App\Models\User;
use App\Models\Role;

// Get user
$user = User::find(1);

// Get role
$customerRole = Role::where('role_name', 'customer')->first();

// Attach role to user
$user->roles()->attach($customerRole->id);

// Or using sync (replaces all existing roles)
$user->roles()->sync([$customerRole->id]);

// Detach role
$user->roles()->detach($customerRole->id);
```

### 3. Checking Roles in Controllers

```php
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        
        if ($user->isCustomer()) {
            return view('customer.profile', ['user' => $user]);
        }
        
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        
        abort(403, 'Unauthorized access');
    }
}
```

### 4. Checking Roles in Blade Views

```blade
@if(Auth::user()->isCustomer())
    <a href="{{ route('customer.orders') }}">Đơn hàng của tôi</a>
@endif

@if(Auth::user()->isAdmin())
    <a href="{{ route('admin.dashboard') }}">Quản trị</a>
@endif

@if(Auth::user()->hasAnyRole(['admin', 'seller']))
    <a href="{{ route('dashboard') }}">Dashboard</a>
@endif
```

### 5. Working with Pivot Data

```php
// Access OrderPromotion pivot
$order = Order::with('promotions')->find(1);
foreach ($order->promotions as $promotion) {
    echo $promotion->pivot->discount_amount;
    echo $promotion->pivot->created_at;
}

// Attach with pivot data
$order->promotions()->attach($promotionId, [
    'discount_amount' => 50000
]);

// Update pivot data
$order->promotions()->updateExistingPivot($promotionId, [
    'discount_amount' => 75000
]);
```

## Database Seeding Example

Create a seeder to populate roles:

```php
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            ['role_name' => 'admin', 'description' => 'Quản trị viên hệ thống'],
            ['role_name' => 'customer', 'description' => 'Khách hàng'],
            ['role_name' => 'seller', 'description' => 'Người bán hàng'],
            ['role_name' => 'shipper', 'description' => 'Nhân viên giao hàng'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['role_name' => $role['role_name']],
                ['description' => $role['description']]
            );
        }
    }
}
```

Run the seeder:
```bash
php artisan db:seed --class=RoleSeeder
```

## Error Handling

The middleware automatically redirects unauthorized users:

1. **Not authenticated:** Redirects to login with message "Vui lòng đăng nhập để tiếp tục."
2. **Wrong role:** Redirects to home with message indicating no access to that area.

Example error messages:
- Customer middleware: "Bạn không có quyền truy cập vào khu vực này."
- Admin middleware: "Bạn không có quyền truy cập vào khu vực quản trị."
- Seller middleware: "Bạn không có quyền truy cập vào khu vực người bán."
- Shipper middleware: "Bạn không có quyền truy cập vào khu vực giao hàng."

## Testing

Example test for middleware:

```php
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;

class CustomerMiddlewareTest extends TestCase
{
    public function test_customer_can_access_customer_routes()
    {
        $user = User::factory()->create();
        $customerRole = Role::where('role_name', 'customer')->first();
        $user->roles()->attach($customerRole->id);

        $response = $this->actingAs($user)
            ->get('/customer/orders');

        $response->assertStatus(200);
    }

    public function test_non_customer_cannot_access_customer_routes()
    {
        $user = User::factory()->create();
        $adminRole = Role::where('role_name', 'admin')->first();
        $user->roles()->attach($adminRole->id);

        $response = $this->actingAs($user)
            ->get('/customer/orders');

        $response->assertRedirect('/');
    }
}
```

## Summary

The RBAC system is now fully implemented with:
- ✅ 4 role types (admin, customer, seller, shipper)
- ✅ 3 pivot models (RoleUser, PermissionRole, OrderPromotion)
- ✅ 4 middleware classes with aliases
- ✅ User helper methods for role checking
- ✅ Updated relationships with pivot models
- ✅ Middleware registered in `bootstrap/app.php`

All models are properly configured to match the database schema.
