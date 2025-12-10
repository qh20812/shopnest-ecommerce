<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Review;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DetailTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Run migrations
        $this->artisan('migrate');
    }

    /**
     * Test product detail page loads successfully
     */
    public function test_product_detail_page_loads_successfully(): void
    {
        $product = $this->createTestProduct();

        $response = $this->get("/product/{$product->id}");

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('detail')
            ->has('product')
            ->has('rating')
            ->has('soldCount')
            ->has('relatedProducts')
            ->has('reviews')
        );
    }

    /**
     * Test product detail returns correct data structure
     */
    public function test_product_detail_returns_correct_data_structure(): void
    {
        $product = $this->createTestProduct();

        $response = $this->get("/product/{$product->id}");

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->where('product.id', $product->id)
            ->where('product.name', $product->product_name)
            ->has('product.images')
            ->has('product.variants')
            ->has('product.attributes')
        );
    }

    /**
     * Test inactive product returns 404
     */
    public function test_inactive_product_returns_404(): void
    {
        $product = $this->createTestProduct(['status' => 'inactive']);

        $response = $this->get("/product/{$product->id}");

        $response->assertStatus(404);
    }

    /**
     * Test non-existent product returns 404
     */
    public function test_non_existent_product_returns_404(): void
    {
        $response = $this->get('/product/99999');

        $response->assertStatus(404);
    }

    /**
     * Test add to cart requires authentication
     */
    public function test_add_to_cart_requires_authentication(): void
    {
        $product = $this->createTestProduct();
        $variant = $product->variants->first();

        $response = $this->postJson("/product/{$product->id}/add-to-cart", [
            'variant_id' => $variant->id,
            'quantity' => 1,
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'success' => false,
            'action' => 'login_required',
        ]);
    }

    /**
     * Test authenticated user can add product to cart
     */
    public function test_authenticated_user_can_add_product_to_cart(): void
    {
        $user = $this->createTestUser();
        $product = $this->createTestProduct();
        $variant = $product->variants->first();

        $response = $this->actingAs($user)->postJson("/product/{$product->id}/add-to-cart", [
            'variant_id' => $variant->id,
            'quantity' => 2,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'cartCount' => 2,
        ]);

        $this->assertDatabaseHas('cart_items', [
            'user_id' => $user->id,
            'product_variant_id' => $variant->id,
            'quantity' => 2,
        ]);
    }

    /**
     * Test add to cart with insufficient stock
     */
    public function test_add_to_cart_with_insufficient_stock(): void
    {
        $user = $this->createTestUser();
        $product = $this->createTestProduct();
        $variant = $product->variants->first();
        $variant->update(['stock_quantity' => 5]);

        $response = $this->actingAs($user)->postJson("/product/{$product->id}/add-to-cart", [
            'variant_id' => $variant->id,
            'quantity' => 10,
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);
    }

    /**
     * Test add to cart with invalid variant
     */
    public function test_add_to_cart_with_invalid_variant(): void
    {
        $user = $this->createTestUser();
        $product = $this->createTestProduct();

        $response = $this->actingAs($user)->postJson("/product/{$product->id}/add-to-cart", [
            'variant_id' => 99999,
            'quantity' => 1,
        ]);

        $response->assertStatus(422);
    }

    /**
     * Test add to cart updates existing cart item
     */
    public function test_add_to_cart_updates_existing_cart_item(): void
    {
        $user = $this->createTestUser();
        $product = $this->createTestProduct();
        $variant = $product->variants->first();

        // Add first time
        $this->actingAs($user)->postJson("/product/{$product->id}/add-to-cart", [
            'variant_id' => $variant->id,
            'quantity' => 2,
        ]);

        // Add second time
        $response = $this->actingAs($user)->postJson("/product/{$product->id}/add-to-cart", [
            'variant_id' => $variant->id,
            'quantity' => 3,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'cartCount' => 5,
        ]);

        $this->assertDatabaseHas('cart_items', [
            'user_id' => $user->id,
            'product_variant_id' => $variant->id,
            'quantity' => 5,
        ]);
    }

    /**
     * Test buy now requires authentication
     */
    public function test_buy_now_requires_authentication(): void
    {
        $product = $this->createTestProduct();
        $variant = $product->variants->first();

        $response = $this->postJson("/product/{$product->id}/buy-now", [
            'variant_id' => $variant->id,
            'quantity' => 1,
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'success' => false,
            'action' => 'login_required',
        ]);
    }

    /**
     * Test authenticated user can buy now
     */
    public function test_authenticated_user_can_buy_now(): void
    {
        $user = $this->createTestUser();
        $product = $this->createTestProduct();
        $variant = $product->variants->first();

        $response = $this->actingAs($user)->postJson("/product/{$product->id}/buy-now", [
            'variant_id' => $variant->id,
            'quantity' => 1,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
        $response->assertJsonStructure([
            'success',
            'redirect',
        ]);
    }

    /**
     * Test buy now with insufficient stock
     */
    public function test_buy_now_with_insufficient_stock(): void
    {
        $user = $this->createTestUser();
        $product = $this->createTestProduct();
        $variant = $product->variants->first();
        $variant->update(['stock_quantity' => 2]);

        $response = $this->actingAs($user)->postJson("/product/{$product->id}/buy-now", [
            'variant_id' => $variant->id,
            'quantity' => 5,
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
        ]);
    }

    /**
     * Test product with reviews shows correct rating
     */
    public function test_product_with_reviews_shows_correct_rating(): void
    {
        $product = $this->createTestProduct();
        
        // Create some reviews
        $user1 = $this->createTestUser();
        $user2 = $this->createTestUser();
        
        Review::create([
            'product_id' => $product->id,
            'user_id' => $user1->id,
            'rating' => 5,
            'comment' => 'Great product!',
            'is_approved' => true,
        ]);
        
        Review::create([
            'product_id' => $product->id,
            'user_id' => $user2->id,
            'rating' => 4,
            'comment' => 'Good quality',
            'is_approved' => true,
        ]);

        $response = $this->get("/product/{$product->id}");

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->where('rating.count', 2)
            ->where('rating.average', 4.5)
        );
    }

    /**
     * Test related products are returned
     */
    public function test_related_products_are_returned(): void
    {
        $product1 = $this->createTestProduct();
        $categoryId = $product1->category_id;
        $product2 = $this->createTestProduct(['category_id' => $categoryId]);

        $response = $this->get("/product/{$product1->id}");

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->has('relatedProducts')
        );
    }

    /**
     * Helper method to create a test product with all necessary relationships
     */
    protected function createTestProduct(array $attributes = []): Product
    {
        // Create category
        $category = Category::create([
            'category_name' => 'Test Category',
            'slug' => 'test-category-' . uniqid(),
            'is_active' => true,
        ]);

        // Create brand
        $brand = Brand::create([
            'brand_name' => 'Test Brand',
            'slug' => 'test-brand-' . uniqid(),
            'is_active' => true,
        ]);

        // Create a test user for shop
        $shopUser = User::create([
            'full_name' => 'Shop Owner',
            'email' => 'shopowner' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);

        // Create shop
        $shop = Shop::create([
            'shop_name' => 'Test Shop',
            'slug' => 'test-shop-' . uniqid(),
            'owner_id' => $shopUser->id,
            'is_active' => true,
        ]);

        // Create product
        $product = Product::create(array_merge([
            'product_name' => 'Test Product',
            'description' => 'Test Description',
            'slug' => 'test-product-' . uniqid(),
            'base_price' => 100000,
            'status' => 'active',
        ], $attributes, [
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'shop_id' => $shop->id,
            'seller_id' => $shopUser->id,
        ]));

        // Create a variant
        ProductVariant::create([
            'product_id' => $product->id,
            'variant_name' => 'Default',
            'sku' => 'TEST-SKU-' . uniqid(),
            'price' => 100000,
            'stock_quantity' => 50,
            'reserved_quantity' => 0,
        ]);

        // Create an image
        ProductImage::create([
            'product_id' => $product->id,
            'image_url' => 'https://via.placeholder.com/600x600',
            'alt_text' => 'Test Image',
            'display_order' => 1,
        ]);

        return $product->fresh(['variants', 'images', 'category', 'brand', 'shop']);
    }

    /**
     * Create a test user
     */
    protected function createTestUser(): User
    {
        return User::create([
            'full_name' => 'Test User',
            'email' => 'test' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);
    }

    /**
     * Test UI: product images are displayed correctly
     */
    public function test_ui_product_images_are_displayed(): void
    {
        $product = $this->createTestProduct();
        
        // Create multiple images
        ProductImage::create([
            'product_id' => $product->id,
            'image_url' => 'https://via.placeholder.com/600x601',
            'alt_text' => 'Test Image 2',
            'display_order' => 2,
        ]);
        
        ProductImage::create([
            'product_id' => $product->id,
            'image_url' => 'https://via.placeholder.com/600x602',
            'alt_text' => 'Test Image 3',
            'display_order' => 3,
        ]);

        $response = $this->get("/product/{$product->id}");

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->has('product.images', 3)
            ->where('product.images.0.url', 'https://via.placeholder.com/600x600')
            ->where('product.images.1.url', 'https://via.placeholder.com/600x601')
            ->where('product.images.2.url', 'https://via.placeholder.com/600x602')
        );
    }

    /**
     * Test UI: product pricing displays correctly
     */
    public function test_ui_product_pricing_displays_correctly(): void
    {
        $product = $this->createTestProduct();
        $variant = $product->variants->first();
        
        // Update variant with compare price
        $variant->update([
            'price' => 100000,
        ]);

        $response = $this->get("/product/{$product->id}");

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->has('product.variants')
            ->where('product.variants.0.final_price', 100000)
            ->where('product.variants.0.price', 100000)
        );
    }

    /**
     * Test UI: rating and reviews display correctly
     */
    public function test_ui_rating_and_reviews_display_correctly(): void
    {
        $product = $this->createTestProduct();
        $user1 = $this->createTestUser();
        $user2 = $this->createTestUser();
        $user3 = $this->createTestUser();
        
        // Create reviews with different ratings
        Review::create([
            'product_id' => $product->id,
            'user_id' => $user1->id,
            'rating' => 5,
            'comment' => 'Excellent!',
            'is_approved' => true,
        ]);
        
        Review::create([
            'product_id' => $product->id,
            'user_id' => $user2->id,
            'rating' => 4,
            'comment' => 'Very good',
            'is_approved' => true,
        ]);
        
        Review::create([
            'product_id' => $product->id,
            'user_id' => $user3->id,
            'rating' => 3,
            'comment' => 'Average',
            'is_approved' => true,
        ]);

        $response = $this->get("/product/{$product->id}");

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->where('rating.count', 3)
            ->where('rating.average', 4) // Average is returned as integer
            ->has('rating.breakdown', 5) // 5 stars breakdown
            ->has('reviews.data', 3)
            ->where('reviews.data.0.rating', 5)
            ->where('reviews.data.1.rating', 4)
            ->where('reviews.data.2.rating', 3)
        );
    }

    /**
     * Test UI: stock status displays correctly
     */
    public function test_ui_stock_status_displays_correctly(): void
    {
        $product = $this->createTestProduct();
        $variant = $product->variants->first();
        
        // Test in-stock variant
        $variant->update(['stock_quantity' => 10, 'reserved_quantity' => 0]);

        $response = $this->get("/product/{$product->id}");

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->where('product.variants.0.in_stock', true)
            ->where('product.variants.0.available_quantity', 10)
        );

        // Test out-of-stock variant
        $variant->update(['stock_quantity' => 5, 'reserved_quantity' => 5]);

        $response = $this->get("/product/{$product->id}");

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->where('product.variants.0.in_stock', false)
            ->where('product.variants.0.available_quantity', 0)
        );
    }

    /**
     * Test UI: related products display with correct structure
     */
    public function test_ui_related_products_display_correctly(): void
    {
        $product1 = $this->createTestProduct();
        $categoryId = $product1->category_id;
        
        // Create 5 related products in same category (should only return 4)
        for ($i = 0; $i < 5; $i++) {
            $this->createTestProduct(['category_id' => $categoryId]);
        }

        $response = $this->get("/product/{$product1->id}");

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->has('relatedProducts') // Has related products array
        );
        
        // Check that relatedProducts array has max 4 items
        $props = $response->viewData('page')['props'];
        $this->assertLessThanOrEqual(4, count($props['relatedProducts']));
        
        // If there are related products, check structure
        if (count($props['relatedProducts']) > 0) {
            $response->assertInertia(fn ($page) => $page
                ->has('relatedProducts.0', fn ($product) => $product
                    ->has('id')
                    ->has('name')
                    ->has('category')
                    ->has('image')
                    ->has('price')
                    ->has('rating')
                    ->has('reviewCount')
                    ->has('isWishlisted')
                )
            );
        }
    }

    /**
     * Test UI: shop information displays correctly
     */
    public function test_ui_shop_information_displays_correctly(): void
    {
        $product = $this->createTestProduct();

        $response = $this->get("/product/{$product->id}");

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->has('product.shop')
            ->where('product.shop.id', $product->shop->id)
            ->where('product.shop.name', $product->shop->shop_name)
            ->has('product.shop.rating')
        );
    }

    /**
     * Test UI: sold count displays correctly
     */
    public function test_ui_sold_count_displays_correctly(): void
    {
        $product = $this->createTestProduct();

        $response = $this->get("/product/{$product->id}");

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->has('soldCount')
            ->where('soldCount', 0) // Initially 0
        );
    }

    /**
     * Test UI: product with no reviews shows zero rating
     */
    public function test_ui_product_with_no_reviews_shows_zero_rating(): void
    {
        $product = $this->createTestProduct();

        $response = $this->get("/product/{$product->id}");

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->where('rating.count', 0)
            ->where('rating.average', 0)
            ->has('reviews.data', 0)
        );
    }

    /**
     * Test UI: product category and brand display correctly
     */
    public function test_ui_product_category_and_brand_display(): void
    {
        $product = $this->createTestProduct();

        $response = $this->get("/product/{$product->id}");

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->has('product.category')
            ->where('product.category.id', $product->category->id)
            ->where('product.category.name', $product->category->category_name)
            ->has('product.brand')
            ->where('product.brand.id', $product->brand->id)
            ->where('product.brand.name', $product->brand->brand_name)
        );
    }

    /**
     * Test UI: product variants with attributes display correctly
     */
    public function test_ui_product_variants_with_attributes_display(): void
    {
        $product = $this->createTestProduct();
        
        // Add attribute values to variant (would normally be done through pivot tables)
        // This tests the basic variant structure is correct
        $response = $this->get("/product/{$product->id}");

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->has('product.variants')
            ->has('product.variants.0', fn ($variant) => $variant
                ->has('variant_id')
                ->has('sku')
                ->has('price')
                ->has('compare_price')
                ->has('final_price')
                ->has('stock_quantity')
                ->has('available_quantity')
                ->has('attribute_values')
                ->has('in_stock')
                ->etc()
            )
        );
    }
}
