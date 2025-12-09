<?php

namespace Tests\Feature;

use App\Models\AdministrativeDivision;
use App\Models\Brand;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Country;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Shop;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class checkoutTest extends TestCase
{
    use RefreshDatabase;

    private function createTestData($suffix = '')
    {
        $seller = User::create([
            'full_name' => 'Seller' . $suffix,
            'email' => 'seller' . $suffix . '@example.com',
            'password' => bcrypt('password'),
        ]);

        $category = Category::create([
            'category_name' => 'Electronics' . $suffix,
            'slug' => 'electronics-' . uniqid(),
            'is_active' => true,
        ]);

        $brand = Brand::create([
            'brand_name' => 'Apple' . $suffix,
            'slug' => 'apple-' . uniqid(),
            'is_active' => true,
        ]);

        $shop = Shop::create([
            'owner_id' => $seller->id,
            'shop_name' => 'Test Shop' . $suffix,
            'slug' => 'test-shop-' . uniqid(),
            'is_active' => true,
        ]);

        // Create country
        $uniqueId = substr(md5(uniqid() . $suffix), 0, 10);
        $country = Country::create([
            'country_name' => 'Vietnam' . $suffix,
            'iso_code_2' => substr($uniqueId, 0, 2),
            'iso_code_3' => substr($uniqueId, 0, 3),
            'phone_code' => '+84',
            'currency' => 'VND',
            'is_active' => true,
        ]);

        // Create province
        $province = AdministrativeDivision::create([
            'country_id' => $country->id,
            'division_name' => 'Ho Chi Minh',
            'code' => 'HCM',
            'division_type' => 'province',
            'level' => 1,
        ]);

        // Create district
        $district = AdministrativeDivision::create([
            'country_id' => $country->id,
            'parent_id' => $province->id,
            'division_name' => 'District 1',
            'code' => 'D1',
            'division_type' => 'province', // Using province as district type
            'level' => 2,
        ]);

        // Create ward
        $ward = AdministrativeDivision::create([
            'country_id' => $country->id,
            'parent_id' => $district->id,
            'division_name' => 'Ward 1',
            'code' => 'W1',
            'division_type' => 'ward',
            'level' => 3,
        ]);

        return compact('seller', 'category', 'brand', 'shop', 'country', 'province', 'district', 'ward');
    }

    private function createProduct($data, $productData = [])
    {
        return Product::create(array_merge([
            'category_id' => $data['category']->id,
            'brand_id' => $data['brand']->id,
            'seller_id' => $data['seller']->id,
            'shop_id' => $data['shop']->id,
            'product_name' => 'Test Product',
            'slug' => 'test-product-' . uniqid(),
            'base_price' => 100000,
            'status' => 'active',
        ], $productData));
    }

    private function createAddress($userId, $data)
    {
        return UserAddress::create([
            'address_label' => 'Home',
            'country_id' => $data['country']->id,
            'province_id' => $data['province']->id,
            'district_id' => $data['district']->id,
            'ward_id' => $data['ward']->id,
            'user_id' => $userId,
            'recipient_name' => 'John Doe',
            'phone_number' => '0123456789',
            'address_line1' => '123 Main St',
            'is_default' => true,
        ]);
    }

    public function test_guest_cannot_access_checkout()
    {
        $response = $this->get('/checkout');
        $response->assertStatus(200);
        $response->assertInertia(fn($page) => 
            $page->component('checkout')
                ->has('cartItems', 0)
                ->where('subtotal', 0)
        );
    }

    public function test_user_can_view_checkout_with_cart_items()
    {
        $data = $this->createTestData();
        $user = User::create([
            'full_name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $product = $this->createProduct($data);
        $variant = ProductVariant::create([
            'sku' => 'SKU-' . uniqid(),
            'variant_name' => 'Standard',
            'product_id' => $product->id,
            'price' => 100000,
            'stock_quantity' => 10,
            'is_active' => true,
        ]);

        CartItem::create([
            'user_id' => $user->id,
            'product_variant_id' => $variant->id,
            'quantity' => 2,
        ]);

        $response = $this->actingAs($user)->get('/checkout');
        
        $response->assertStatus(200);
        $response->assertInertia(fn($page) => 
            $page->component('checkout')
                ->has('cartItems', 1)
                ->where('subtotal', 200000)
                ->where('shipping', 30000)
                ->where('total', 230000)
        );
    }

    public function test_user_can_view_checkout_with_addresses()
    {
        $data = $this->createTestData();
        $user = User::create([
            'full_name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $address = $this->createAddress($user->id, $data);

        $response = $this->actingAs($user)->get('/checkout');
        
        $response->assertStatus(200);
        $response->assertInertia(fn($page) => 
            $page->component('checkout')
                ->has('addresses', 1)
                ->where('addresses.0.recipient_name', 'John Doe')
        );
    }

    public function test_guest_cannot_create_order()
    {
        $response = $this->postJson('/checkout', [
            'shipping_address_id' => 1,
            'payment_method' => 'cod',
            'shipping_method' => 'standard',
        ]);

        $response->assertStatus(401);
        $response->assertJson(['message' => 'Bạn cần đăng nhập']);
    }

    public function test_cannot_checkout_with_empty_cart()
    {
        $data = $this->createTestData();
        $user = User::create([
            'full_name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $address = $this->createAddress($user->id, $data);

        $response = $this->actingAs($user)->postJson('/checkout', [
            'shipping_address_id' => $address->id,
            'payment_method' => 'cod',
            'shipping_method' => 'standard',
        ]);

        $response->assertStatus(400);
        $response->assertJson(['message' => 'Giỏ hàng trống']);
    }

    public function test_cannot_checkout_with_invalid_address()
    {
        $data = $this->createTestData();
        $user = User::create([
            'full_name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $otherUser = User::create([
            'full_name' => 'Other User',
            'email' => 'other@example.com',
            'password' => bcrypt('password'),
        ]);

        $address = $this->createAddress($otherUser->id, $data);

        $response = $this->actingAs($user)->postJson('/checkout', [
            'shipping_address_id' => $address->id,
            'payment_method' => 'cod',
            'shipping_method' => 'standard',
        ]);

        $response->assertStatus(422);
        $response->assertJson(['message' => 'Địa chỉ không hợp lệ']);
    }

    public function test_user_can_create_order_successfully()
    {
        $data = $this->createTestData();
        $user = User::create([
            'full_name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $product = $this->createProduct($data);
        $variant = ProductVariant::create([
            'sku' => 'SKU-' . uniqid(),
            'variant_name' => 'Standard',
            'product_id' => $product->id,
            'price' => 100000,
            'stock_quantity' => 10,
            'is_active' => true,
        ]);

        CartItem::create([
            'user_id' => $user->id,
            'product_variant_id' => $variant->id,
            'quantity' => 2,
        ]);

        $address = $this->createAddress($user->id, $data);

        $response = $this->actingAs($user)->postJson('/checkout', [
            'shipping_address_id' => $address->id,
            'payment_method' => 'cod',
            'shipping_method' => 'standard',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Đơn hàng đã được tạo thành công']);
        
        $this->assertDatabaseHas('orders', [
            'customer_id' => $user->id,
            'shop_id' => $data['shop']->id,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'payment_method' => 'cod',
            'shipping_address_id' => $address->id,
        ]);

        $this->assertDatabaseHas('order_items', [
            'product_variant_id' => $variant->id,
            'quantity' => 2,
            'unit_price' => 100000,
        ]);

        $this->assertDatabaseCount('cart_items', 0);
    }

    public function test_order_reduces_stock_quantity()
    {
        $data = $this->createTestData();
        $user = User::create([
            'full_name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $product = $this->createProduct($data);
        $variant = ProductVariant::create([
            'sku' => 'SKU-' . uniqid(),
            'variant_name' => 'Standard',
            'product_id' => $product->id,
            'price' => 100000,
            'stock_quantity' => 10,
            'is_active' => true,
        ]);

        CartItem::create([
            'user_id' => $user->id,
            'product_variant_id' => $variant->id,
            'quantity' => 3,
        ]);

        $address = $this->createAddress($user->id, $data);

        $this->actingAs($user)->postJson('/checkout', [
            'shipping_address_id' => $address->id,
            'payment_method' => 'cod',
            'shipping_method' => 'standard',
        ]);

        $variant->refresh();
        $this->assertEquals(7, $variant->stock_quantity);
    }

    public function test_checkout_with_express_shipping()
    {
        $data = $this->createTestData();
        $user = User::create([
            'full_name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $product = $this->createProduct($data);
        $variant = ProductVariant::create([
            'sku' => 'SKU-' . uniqid(),
            'variant_name' => 'Standard',
            'product_id' => $product->id,
            'price' => 100000,
            'stock_quantity' => 10,
            'is_active' => true,
        ]);

        CartItem::create([
            'user_id' => $user->id,
            'product_variant_id' => $variant->id,
            'quantity' => 1,
        ]);

        $address = $this->createAddress($user->id, $data);

        $this->actingAs($user)->postJson('/checkout', [
            'shipping_address_id' => $address->id,
            'payment_method' => 'cod',
            'shipping_method' => 'express',
        ]);

        $this->assertDatabaseHas('orders', [
            'customer_id' => $user->id,
            'shipping_fee' => 50000,
        ]);
    }

    public function test_checkout_with_multiple_shops_creates_multiple_orders()
    {
        $data1 = $this->createTestData('1');
        $data2 = $this->createTestData('2');

        $user = User::create([
            'full_name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $product1 = $this->createProduct($data1, ['product_name' => 'Product 1', 'slug' => 'product-1']);
        $product2 = $this->createProduct($data2, ['product_name' => 'Product 2', 'slug' => 'product-2']);

        $variant1 = ProductVariant::create([
            'sku' => 'SKU-' . uniqid(),
            'variant_name' => 'Standard',
            'product_id' => $product1->id,
            'price' => 100000,
            'stock_quantity' => 10,
            'is_active' => true,
        ]);

        $variant2 = ProductVariant::create([
            'sku' => 'SKU-' . uniqid(),
            'variant_name' => 'Standard',
            'product_id' => $product2->id,
            'price' => 200000,
            'stock_quantity' => 10,
            'is_active' => true,
        ]);

        CartItem::create([
            'user_id' => $user->id,
            'product_variant_id' => $variant1->id,
            'quantity' => 1,
        ]);

        CartItem::create([
            'user_id' => $user->id,
            'product_variant_id' => $variant2->id,
            'quantity' => 1,
        ]);

        $address = $this->createAddress($user->id, $data1);

        $response = $this->actingAs($user)->postJson('/checkout', [
            'shipping_address_id' => $address->id,
            'payment_method' => 'cod',
            'shipping_method' => 'standard',
        ]);

        $response->assertStatus(200);
        $response->assertJsonCount(2, 'orders');
        
        $this->assertDatabaseHas('orders', ['shop_id' => $data1['shop']->id]);
        $this->assertDatabaseHas('orders', ['shop_id' => $data2['shop']->id]);
    }

    public function test_checkout_validates_required_fields()
    {
        $user = User::create([
            'full_name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->actingAs($user)->postJson('/checkout', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['shipping_address_id', 'payment_method', 'shipping_method']);
    }

    public function test_checkout_validates_payment_method()
    {
        $data = $this->createTestData();
        $user = User::create([
            'full_name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $address = $this->createAddress($user->id, $data);

        $response = $this->actingAs($user)->postJson('/checkout', [
            'shipping_address_id' => $address->id,
            'payment_method' => 'invalid_method',
            'shipping_method' => 'standard',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['payment_method']);
    }

    public function test_checkout_with_note()
    {
        $data = $this->createTestData();
        $user = User::create([
            'full_name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $product = $this->createProduct($data);
        $variant = ProductVariant::create([
            'sku' => 'SKU-' . uniqid(),
            'variant_name' => 'Standard',
            'product_id' => $product->id,
            'price' => 100000,
            'stock_quantity' => 10,
            'is_active' => true,
        ]);

        CartItem::create([
            'user_id' => $user->id,
            'product_variant_id' => $variant->id,
            'quantity' => 1,
        ]);

        $address = $this->createAddress($user->id, $data);

        $this->actingAs($user)->postJson('/checkout', [
            'shipping_address_id' => $address->id,
            'payment_method' => 'cod',
            'shipping_method' => 'standard',
            'note' => 'Please deliver after 6 PM',
        ]);

        $this->assertDatabaseHas('orders', [
            'customer_id' => $user->id,
            'note' => 'Please deliver after 6 PM',
        ]);
    }

    public function test_order_number_is_unique()
    {
        $data = $this->createTestData();
        $user = User::create([
            'full_name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $product = $this->createProduct($data);
        $variant = ProductVariant::create([
            'sku' => 'SKU-' . uniqid(),
            'variant_name' => 'Standard',
            'product_id' => $product->id,
            'price' => 100000,
            'stock_quantity' => 20,
            'is_active' => true,
        ]);

        $address = $this->createAddress($user->id, $data);

        // Create first order
        CartItem::create([
            'user_id' => $user->id,
            'product_variant_id' => $variant->id,
            'quantity' => 1,
        ]);

        $this->actingAs($user)->postJson('/checkout', [
            'shipping_address_id' => $address->id,
            'payment_method' => 'cod',
            'shipping_method' => 'standard',
        ]);

        $firstOrderNumber = Order::first()->order_number;

        // Create second order
        CartItem::create([
            'user_id' => $user->id,
            'product_variant_id' => $variant->id,
            'quantity' => 1,
        ]);

        $this->actingAs($user)->postJson('/checkout', [
            'shipping_address_id' => $address->id,
            'payment_method' => 'cod',
            'shipping_method' => 'standard',
        ]);

        $secondOrderNumber = Order::latest()->first()->order_number;

        $this->assertNotEquals($firstOrderNumber, $secondOrderNumber);
    }

    public function test_free_shipping_for_orders_over_one_million()
    {
        $data = $this->createTestData();
        $user = User::create([
            'full_name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $product = $this->createProduct($data, [
            'product_name' => 'Expensive Product',
            'slug' => 'expensive-product',
            'base_price' => 1500000,
        ]);

        $variant = ProductVariant::create([
            'sku' => 'SKU-' . uniqid(),
            'variant_name' => 'Standard',
            'product_id' => $product->id,
            'price' => 1500000,
            'stock_quantity' => 10,
            'is_active' => true,
        ]);

        CartItem::create([
            'user_id' => $user->id,
            'product_variant_id' => $variant->id,
            'quantity' => 1,
        ]);

        $response = $this->actingAs($user)->get('/checkout');
        
        $response->assertInertia(fn($page) => 
            $page->where('shipping', 0)
                ->where('total', 1500000)
        );
    }

    public function test_checkout_filters_inactive_products()
    {
        $data = $this->createTestData();
        $user = User::create([
            'full_name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $product = $this->createProduct($data, [
            'product_name' => 'Inactive Product',
            'slug' => 'inactive-product',
            'status' => 'inactive',
        ]);

        $variant = ProductVariant::create([
            'sku' => 'SKU-' . uniqid(),
            'variant_name' => 'Standard',
            'product_id' => $product->id,
            'price' => 100000,
            'stock_quantity' => 10,
            'is_active' => true,
        ]);

        CartItem::create([
            'user_id' => $user->id,
            'product_variant_id' => $variant->id,
            'quantity' => 1,
        ]);

        $response = $this->actingAs($user)->get('/checkout');
        
        $response->assertInertia(fn($page) => 
            $page->has('cartItems', 0)
        );
    }
}


