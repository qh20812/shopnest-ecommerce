<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Shop;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class cartTest extends TestCase
{
    use RefreshDatabase;

    private function createTestData()
    {
        // Create category
        $category = Category::create([
            'category_name' => 'Electronics',
            'slug' => 'electronics',
            'is_active' => true,
        ]);

        // Create brand
        $brand = Brand::create([
            'brand_name' => 'Apple',
            'slug' => 'apple',
            'is_active' => true,
        ]);

        // Create seller
        $seller = User::factory()->create();

        // Create shop
        $shop = Shop::create([
            'owner_id' => $seller->id,
            'shop_name' => 'Test Shop',
            'slug' => 'test-shop',
            'is_active' => true,
        ]);

        // Create product
        $product = Product::create([
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'seller_id' => $seller->id,
            'shop_id' => $shop->id,
            'product_name' => 'iPhone 15',
            'slug' => 'iphone-15',
            'description' => 'Latest iPhone',
            'base_price' => 25000000,
            'status' => 'active',
        ]);

        // Create product image
        $image = ProductImage::create([
            'product_id' => $product->id,
            'image_url' => 'https://example.com/image.jpg',
            'is_primary' => true,
        ]);

        // Create product variant
        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'IP15-BLACK',
            'variant_name' => 'Black 128GB',
            'price' => 25000000,
            'stock_quantity' => 10,
            'image_id' => $image->id,
            'is_active' => true,
        ]);

        return [
            'category' => $category,
            'brand' => $brand,
            'seller' => $seller,
            'shop' => $shop,
            'product' => $product,
            'variant' => $variant,
            'image' => $image,
        ];
    }

    /** @test */
    public function guest_can_view_cart_page_with_empty_cart()
    {
        $response = $this->get('/cart');

        $response->assertStatus(200);
        $response->assertInertia(fn($page) => $page
            ->component('cart')
            ->has('cartItems', 0)
            ->where('subtotal', 0)
            ->where('total', 0)
        );
    }

    /** @test */
    public function authenticated_user_can_view_cart_page_with_cart_items()
    {
        $user = User::factory()->create();
        $data = $this->createTestData();

        CartItem::create([
            'user_id' => $user->id,
            'product_variant_id' => $data['variant']->id,
            'quantity' => 2,
        ]);

        $response = $this->actingAs($user)->get('/cart');

        $response->assertStatus(200);
        $response->assertInertia(fn($page) => $page
            ->component('cart')
            ->has('cartItems', 1)
            ->where('cartItems.0.name', 'iPhone 15')
            ->where('cartItems.0.quantity', 2)
            ->where('cartItems.0.price', 25000000)
            ->where('subtotal', 50000000)
        );
    }

    /** @test */
    public function guest_cannot_add_to_cart()
    {
        $data = $this->createTestData();

        $response = $this->postJson('/cart', [
            'product_variant_id' => $data['variant']->id,
            'quantity' => 1,
        ]);

        $response->assertStatus(401);
        $response->assertJson(['message' => 'Bạn cần đăng nhập để thêm vào giỏ hàng']);
    }

    /** @test */
    public function authenticated_user_can_add_to_cart()
    {
        $user = User::factory()->create();
        $data = $this->createTestData();

        $response = $this->actingAs($user)->postJson('/cart', [
            'product_variant_id' => $data['variant']->id,
            'quantity' => 2,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Đã thêm vào giỏ hàng']);

        $this->assertDatabaseHas('cart_items', [
            'user_id' => $user->id,
            'product_variant_id' => $data['variant']->id,
            'quantity' => 2,
        ]);
    }

    /** @test */
    public function adding_existing_item_increases_quantity()
    {
        $user = User::factory()->create();
        $data = $this->createTestData();

        CartItem::create([
            'user_id' => $user->id,
            'product_variant_id' => $data['variant']->id,
            'quantity' => 2,
        ]);

        $response = $this->actingAs($user)->postJson('/cart', [
            'product_variant_id' => $data['variant']->id,
            'quantity' => 3,
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('cart_items', [
            'user_id' => $user->id,
            'product_variant_id' => $data['variant']->id,
            'quantity' => 5,
        ]);
    }

    /** @test */
    public function cannot_add_more_than_stock_quantity()
    {
        $user = User::factory()->create();
        $data = $this->createTestData();

        $response = $this->actingAs($user)->postJson('/cart', [
            'product_variant_id' => $data['variant']->id,
            'quantity' => 15, // Stock is 10
        ]);

        $response->assertStatus(400);
        $response->assertJson(['message' => 'Số lượng vượt quá tồn kho']);
    }

    /** @test */
    public function cannot_add_inactive_product()
    {
        $user = User::factory()->create();
        $data = $this->createTestData();

        // Deactivate product
        $data['product']->status = 'inactive';
        $data['product']->save();
        $data['product']->refresh();

        $response = $this->actingAs($user)->postJson('/cart', [
            'product_variant_id' => $data['variant']->id,
            'quantity' => 1,
        ]);

        $response->assertStatus(404);
    }

    /** @test */
    public function authenticated_user_can_update_cart_item_quantity()
    {
        $user = User::factory()->create();
        $data = $this->createTestData();

        $cartItem = CartItem::create([
            'user_id' => $user->id,
            'product_variant_id' => $data['variant']->id,
            'quantity' => 2,
        ]);

        $response = $this->actingAs($user)->patchJson("/cart/{$cartItem->id}", [
            'quantity' => 5,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Đã cập nhật số lượng']);

        $this->assertDatabaseHas('cart_items', [
            'id' => $cartItem->id,
            'quantity' => 5,
        ]);
    }

    /** @test */
    public function cannot_update_cart_item_beyond_stock()
    {
        $user = User::factory()->create();
        $data = $this->createTestData();

        $cartItem = CartItem::create([
            'user_id' => $user->id,
            'product_variant_id' => $data['variant']->id,
            'quantity' => 2,
        ]);

        $response = $this->actingAs($user)->patchJson("/cart/{$cartItem->id}", [
            'quantity' => 15, // Stock is 10
        ]);

        $response->assertStatus(400);
        $response->assertJson(['message' => 'Số lượng vượt quá tồn kho']);
    }

    /** @test */
    public function user_can_only_update_own_cart_items()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $data = $this->createTestData();

        $cartItem = CartItem::create([
            'user_id' => $user1->id,
            'product_variant_id' => $data['variant']->id,
            'quantity' => 2,
        ]);

        $response = $this->actingAs($user2)->patchJson("/cart/{$cartItem->id}", [
            'quantity' => 5,
        ]);

        $response->assertStatus(404);
    }

    /** @test */
    public function authenticated_user_can_remove_cart_item()
    {
        $user = User::factory()->create();
        $data = $this->createTestData();

        $cartItem = CartItem::create([
            'user_id' => $user->id,
            'product_variant_id' => $data['variant']->id,
            'quantity' => 2,
        ]);

        $response = $this->actingAs($user)->deleteJson("/cart/{$cartItem->id}");

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Đã xóa sản phẩm khỏi giỏ hàng']);

        $this->assertDatabaseMissing('cart_items', [
            'id' => $cartItem->id,
        ]);
    }

    /** @test */
    public function user_can_only_remove_own_cart_items()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $data = $this->createTestData();

        $cartItem = CartItem::create([
            'user_id' => $user1->id,
            'product_variant_id' => $data['variant']->id,
            'quantity' => 2,
        ]);

        $response = $this->actingAs($user2)->deleteJson("/cart/{$cartItem->id}");

        $response->assertStatus(404);

        $this->assertDatabaseHas('cart_items', [
            'id' => $cartItem->id,
        ]);
    }

    /** @test */
    public function authenticated_user_can_clear_cart()
    {
        $user = User::factory()->create();
        $data = $this->createTestData();

        CartItem::create([
            'user_id' => $user->id,
            'product_variant_id' => $data['variant']->id,
            'quantity' => 2,
        ]);

        CartItem::create([
            'user_id' => $user->id,
            'product_variant_id' => $data['variant']->id,
            'quantity' => 1,
        ]);

        $response = $this->actingAs($user)->postJson('/cart/clear');

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Đã xóa tất cả sản phẩm khỏi giỏ hàng']);

        $this->assertDatabaseMissing('cart_items', [
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function guest_cannot_clear_cart()
    {
        $response = $this->postJson('/cart/clear');

        $response->assertStatus(401);
    }

    /** @test */
    public function cart_only_shows_active_products()
    {
        $user = User::factory()->create();
        $data = $this->createTestData();

        $cartItem = CartItem::create([
            'user_id' => $user->id,
            'product_variant_id' => $data['variant']->id,
            'quantity' => 2,
        ]);

        // Deactivate product
        $data['product']->status = 'inactive';
        $data['product']->save();
        $data['product']->refresh();

        $response = $this->actingAs($user)->get('/cart');

        $response->assertStatus(200);
        $response->assertInertia(fn($page) => $page
            ->component('cart')
            ->has('cartItems', 0)
        );
    }

    /** @test */
    public function apply_coupon_returns_invalid_message()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/cart/apply-coupon', [
            'coupon_code' => 'INVALID',
        ]);

        $response->assertStatus(400);
        $response->assertJson(['message' => 'Mã giảm giá không hợp lệ']);
    }

    /** @test */
    public function cart_validation_requires_product_variant_id()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/cart', [
            'quantity' => 1,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['product_variant_id']);
    }

    /** @test */
    public function cart_validation_requires_positive_quantity()
    {
        $user = User::factory()->create();
        $data = $this->createTestData();

        $response = $this->actingAs($user)->postJson('/cart', [
            'product_variant_id' => $data['variant']->id,
            'quantity' => 0,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['quantity']);
    }
}
