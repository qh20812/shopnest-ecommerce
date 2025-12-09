<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductImage;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Shop;
use App\Models\Wishlist;
use App\Models\WishlistItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;

class WishlistTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Run migrations
        $this->artisan('migrate');
    }

    /**
     * Helper to create a test user
     */
    private function createUser()
    {
        return User::create([
            'full_name' => 'Test User',
            'email' => 'test' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);
    }

    /**
     * Helper to create test product with all dependencies
     */
    private function createProduct($status = 'active', $userId = null)
    {
        if (!$userId) {
            $user = $this->createUser();
            $userId = $user->id;
        }

        $category = Category::create([
            'category_name' => 'Test Category ' . uniqid(),
            'slug' => 'test-category-' . uniqid(),
            'is_active' => true,
        ]);

        $brand = Brand::create([
            'brand_name' => 'Test Brand ' . uniqid(),
            'slug' => 'test-brand-' . uniqid(),
            'is_active' => true,
        ]);

        $shop = Shop::create([
            'owner_id' => $userId,
            'shop_name' => 'Test Shop ' . uniqid(),
            'slug' => 'test-shop-' . uniqid(),
            'is_active' => true,
        ]);

        $product = Product::create([
            'shop_id' => $shop->id,
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'seller_id' => $userId,
            'product_name' => 'Test Product ' . uniqid(),
            'slug' => 'test-product-' . uniqid(),
            'description' => 'Test description',
            'base_price' => 100000,
            'status' => $status,
        ]);

        ProductVariant::create([
            'product_id' => $product->id,
            'variant_name' => 'Default',
            'sku' => 'SKU' . uniqid(),
            'price' => 100000,
            'stock_quantity' => 10,
            'is_active' => true,
        ]);

        ProductImage::create([
            'product_id' => $product->id,
            'image_url' => 'https://example.com/image.jpg',
            'is_primary' => true,
        ]);

        return $product;
    }

    /**
     * Test wishlist page loads successfully for guest
     */
    public function test_wishlist_page_loads_for_guest(): void
    {
        $response = $this->get(route('wish-list'));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => 
            $page->component('wish-list')
                ->has('wishlistItems', 0)
        );
    }

    /**
     * Test wishlist page loads successfully for authenticated user
     */
    public function test_wishlist_page_loads_for_authenticated_user(): void
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->get(route('wish-list'));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => 
            $page->component('wish-list')
                ->has('wishlistItems')
        );
    }

    /**
     * Test wishlist shows correct data structure
     */
    public function test_wishlist_returns_correct_data_structure(): void
    {
        $user = $this->createUser();
        $product = $this->createProduct('active', $user->id);

        // Create wishlist
        $wishlist = Wishlist::create([
            'user_id' => $user->id,
            'name' => 'My Wishlist',
            'is_public' => false,
        ]);

        WishlistItem::create([
            'wishlist_id' => $wishlist->id,
            'product_id' => $product->id,
        ]);

        $response = $this->actingAs($user)->get(route('wish-list'));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => 
            $page->component('wish-list')
                ->has('wishlistItems', 1)
                ->has('wishlistItems.0', fn (Assert $item) =>
                    $item->where('product_id', $product->id)
                        ->where('name', $product->product_name)
                        ->has('image')
                        ->has('price')
                        ->has('id')
                )
        );
    }

    /**
     * Test adding product to wishlist requires authentication
     */
    public function test_add_to_wishlist_requires_authentication(): void
    {
        $product = $this->createProduct();

        $response = $this->postJson(route('wishlist.store'), [
            'product_id' => $product->id,
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'success' => false,
            'message' => 'Vui lòng đăng nhập để thêm vào danh sách yêu thích',
        ]);
    }

    /**
     * Test authenticated user can add product to wishlist
     */
    public function test_authenticated_user_can_add_product_to_wishlist(): void
    {
        $user = $this->createUser();
        $product = $this->createProduct('active', $user->id);

        $response = $this->actingAs($user)->postJson(route('wishlist.store'), [
            'product_id' => $product->id,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Đã thêm vào danh sách yêu thích',
        ]);

        $this->assertDatabaseHas('wishlists', [
            'user_id' => $user->id,
        ]);

        $wishlist = Wishlist::where('user_id', $user->id)->first();
        $this->assertDatabaseHas('wishlist_items', [
            'wishlist_id' => $wishlist->id,
            'product_id' => $product->id,
        ]);
    }

    /**
     * Test cannot add inactive product to wishlist
     */
    public function test_cannot_add_inactive_product_to_wishlist(): void
    {
        $user = $this->createUser();
        $product = $this->createProduct('inactive', $user->id);

        $response = $this->actingAs($user)->postJson(route('wishlist.store'), [
            'product_id' => $product->id,
        ]);

        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'message' => 'Sản phẩm không tồn tại hoặc không còn hoạt động',
        ]);
    }

    /**
     * Test cannot add duplicate product to wishlist
     */
    public function test_cannot_add_duplicate_product_to_wishlist(): void
    {
        $user = $this->createUser();
        $product = $this->createProduct('active', $user->id);

        // Add first time
        $wishlist = Wishlist::create([
            'user_id' => $user->id,
            'name' => 'My Wishlist',
            'is_public' => false,
        ]);

        WishlistItem::create([
            'wishlist_id' => $wishlist->id,
            'product_id' => $product->id,
        ]);

        // Try to add again
        $response = $this->actingAs($user)->postJson(route('wishlist.store'), [
            'product_id' => $product->id,
        ]);

        $response->assertStatus(409);
        $response->assertJson([
            'success' => false,
            'message' => 'Sản phẩm đã có trong danh sách yêu thích',
        ]);
    }

    /**
     * Test removing product from wishlist requires authentication
     */
    public function test_remove_from_wishlist_requires_authentication(): void
    {
        $response = $this->deleteJson(route('wishlist.destroy', 1));

        $response->assertStatus(401);
        $response->assertJson([
            'success' => false,
            'message' => 'Vui lòng đăng nhập',
        ]);
    }

    /**
     * Test authenticated user can remove product from wishlist
     */
    public function test_authenticated_user_can_remove_product_from_wishlist(): void
    {
        $user = $this->createUser();
        $product = $this->createProduct('active', $user->id);

        $wishlist = Wishlist::create([
            'user_id' => $user->id,
            'name' => 'My Wishlist',
            'is_public' => false,
        ]);

        $item = WishlistItem::create([
            'wishlist_id' => $wishlist->id,
            'product_id' => $product->id,
        ]);

        $response = $this->actingAs($user)->deleteJson(route('wishlist.destroy', $item->id));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Đã xóa khỏi danh sách yêu thích',
        ]);

        $this->assertDatabaseMissing('wishlist_items', [
            'id' => $item->id,
        ]);
    }

    /**
     * Test cannot remove item from another user's wishlist
     */
    public function test_cannot_remove_item_from_another_users_wishlist(): void
    {
        $user1 = $this->createUser();
        $user2 = $this->createUser();
        $product = $this->createProduct('active', $user1->id);

        $wishlist = Wishlist::create([
            'user_id' => $user1->id,
            'name' => 'My Wishlist',
            'is_public' => false,
        ]);

        $item = WishlistItem::create([
            'wishlist_id' => $wishlist->id,
            'product_id' => $product->id,
        ]);

        $response = $this->actingAs($user2)->deleteJson(route('wishlist.destroy', $item->id));

        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'message' => 'Không tìm thấy danh sách yêu thích',
        ]);
    }

    /**
     * Test clearing wishlist requires authentication
     */
    public function test_clear_wishlist_requires_authentication(): void
    {
        $response = $this->postJson(route('wishlist.clear'));

        $response->assertStatus(401);
        $response->assertJson([
            'success' => false,
            'message' => 'Vui lòng đăng nhập',
        ]);
    }

    /**
     * Test authenticated user can clear wishlist
     */
    public function test_authenticated_user_can_clear_wishlist(): void
    {
        $user = $this->createUser();

        $wishlist = Wishlist::create([
            'user_id' => $user->id,
            'name' => 'My Wishlist',
            'is_public' => false,
        ]);

        // Add multiple products
        for ($i = 0; $i < 3; $i++) {
            $product = $this->createProduct('active', $user->id);

            WishlistItem::create([
                'wishlist_id' => $wishlist->id,
                'product_id' => $product->id,
            ]);
        }

        $response = $this->actingAs($user)->postJson(route('wishlist.clear'));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'deleted_count' => 3,
        ]);

        $this->assertEquals(0, WishlistItem::where('wishlist_id', $wishlist->id)->count());
    }

    /**
     * Test add all to cart requires authentication
     */
    public function test_add_all_to_cart_requires_authentication(): void
    {
        $response = $this->postJson(route('wishlist.addAllToCart'));

        $response->assertStatus(401);
        $response->assertJson([
            'success' => false,
            'message' => 'Vui lòng đăng nhập',
        ]);
    }

    /**
     * Test authenticated user can add all wishlist items to cart
     */
    public function test_authenticated_user_can_add_all_to_cart(): void
    {
        $user = $this->createUser();

        $wishlist = Wishlist::create([
            'user_id' => $user->id,
            'name' => 'My Wishlist',
            'is_public' => false,
        ]);

        // Add multiple products with variants
        $productsCount = 2;
        for ($i = 0; $i < $productsCount; $i++) {
            $product = $this->createProduct('active', $user->id);

            WishlistItem::create([
                'wishlist_id' => $wishlist->id,
                'product_id' => $product->id,
            ]);
        }

        $response = $this->actingAs($user)->postJson(route('wishlist.addAllToCart'));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'added_count' => $productsCount,
        ]);

        $this->assertEquals($productsCount, DB::table('cart_items')->where('user_id', $user->id)->count());
    }

    /**
     * Test cannot add all to cart if wishlist is empty
     */
    public function test_cannot_add_all_to_cart_if_wishlist_is_empty(): void
    {
        $user = $this->createUser();

        $wishlist = Wishlist::create([
            'user_id' => $user->id,
            'name' => 'My Wishlist',
            'is_public' => false,
        ]);

        $response = $this->actingAs($user)->postJson(route('wishlist.addAllToCart'));

        $response->assertStatus(400);
        $response->assertJson([
            'success' => false,
            'message' => 'Danh sách yêu thích trống',
        ]);
    }

    /**
     * Test wishlist filters out inactive products
     */
    public function test_wishlist_filters_out_inactive_products(): void
    {
        $user = $this->createUser();

        $wishlist = Wishlist::create([
            'user_id' => $user->id,
            'name' => 'My Wishlist',
            'is_public' => false,
        ]);

        // Add active product
        $activeProduct = $this->createProduct('active', $user->id);
        WishlistItem::create([
            'wishlist_id' => $wishlist->id,
            'product_id' => $activeProduct->id,
        ]);

        // Add inactive product
        $inactiveProduct = $this->createProduct('inactive', $user->id);
        WishlistItem::create([
            'wishlist_id' => $wishlist->id,
            'product_id' => $inactiveProduct->id,
        ]);

        $response = $this->actingAs($user)->get(route('wish-list'));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => 
            $page->component('wish-list')
                ->has('wishlistItems', 1)
                ->where('wishlistItems.0.product_id', $activeProduct->id)
        );
    }
}
