<?php

namespace Tests\Feature;

use App\Enums\ProductStatus;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\FlashSaleEvent;
use App\Models\FlashSaleProduct;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WelcomeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Run migrations
        $this->artisan('migrate');
    }

    /**
     * Test welcome page renders successfully
     */
    public function test_welcome_page_renders(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('welcome')
            ->has('categories')
            ->has('suggestedProducts')
            ->has('bestSellers')
        );
    }

    /**
     * Test welcome page route is named 'home'
     */
    public function test_welcome_route_name(): void
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
    }

    /**
     * Test categories are displayed on welcome page
     */
    public function test_categories_are_displayed(): void
    {
        // Create active categories
        Category::create([
            'category_name' => 'Điện tử',
            'slug' => 'dien-tu',
            'is_active' => true,
        ]);

        Category::create([
            'category_name' => 'Thời trang',
            'slug' => 'thoi-trang',
            'is_active' => true,
        ]);

        // Create inactive category (should not appear)
        Category::create([
            'category_name' => 'Inactive',
            'slug' => 'inactive',
            'is_active' => false,
        ]);

        $response = $this->get('/');

        $response->assertInertia(fn ($page) => $page
            ->component('welcome')
            ->where('categories', fn ($categories) => 
                count($categories) === 2
            )
        );
    }

    /**
     * Test banners are displayed on welcome page
     */
    public function test_banners_are_displayed(): void
    {
        Banner::create([
            'title' => 'Test Banner',
            'subtitle' => 'Test Subtitle',
            'image_url' => 'https://example.com/banner.jpg',
            'is_active' => true,
            'display_order' => 1,
        ]);

        Banner::create([
            'title' => 'Inactive Banner',
            'image_url' => 'https://example.com/banner2.jpg',
            'is_active' => false,
            'display_order' => 2,
        ]);

        $response = $this->get('/');

        $response->assertInertia(fn ($page) => $page
            ->component('welcome')
            ->has('banners', 1) // Only active banner
        );
    }

    /**
     * Test flash sale is displayed when active
     */
    public function test_active_flash_sale_is_displayed(): void
    {
        // Create necessary dependencies
        $seller = User::create([
            'full_name' => 'Test Seller',
            'email' => 'seller@example.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $shop = Shop::create([
            'shop_name' => 'Test Shop',
            'slug' => 'test-shop',
            'owner_id' => $seller->id,
            'is_active' => true,
        ]);

        $category = Category::create([
            'category_name' => 'Test Category',
            'slug' => 'test-category',
            'is_active' => true,
        ]);

        $brand = Brand::create([
            'brand_name' => 'Test Brand',
            'slug' => 'test-brand',
            'is_active' => true,
        ]);

        $product = Product::create([
            'shop_id' => $shop->id,
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'seller_id' => $seller->id,
            'product_name' => 'Test Product',
            'slug' => 'test-product',
            'description' => 'Test description',
            'base_price' => 100000,
            'status' => ProductStatus::ACTIVE,
            'is_active' => true,
        ]);

        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'variant_name' => 'Default',
            'sku' => 'TEST-SKU-001',
            'price' => 100000,
            'stock_quantity' => 100,
            'is_active' => true,
        ]);

        ProductImage::create([
            'product_id' => $product->id,
            'image_url' => 'https://example.com/product.jpg',
            'is_primary' => true,
            'display_order' => 1,
        ]);

        $flashSaleEvent = FlashSaleEvent::create([
            'event_name' => 'Test Flash Sale',
            'start_time' => now()->subHour(),
            'end_time' => now()->addHour(),
            'is_active' => true,
        ]);

        FlashSaleProduct::create([
            'flash_sale_event_id' => $flashSaleEvent->id,
            'product_variant_id' => $variant->id,
            'flash_price' => 80000,
            'quantity_limit' => 50,
            'sold_quantity' => 0,
        ]);

        $response = $this->get('/');

        $response->assertInertia(fn ($page) => $page
            ->component('welcome')
            ->has('flashSale')
            ->has('flashSale.event')
            ->has('flashSale.products')
        );
    }

    /**
     * Test flash sale is null when no active event
     */
    public function test_no_flash_sale_when_inactive(): void
    {
        $response = $this->get('/');

        $response->assertInertia(fn ($page) => $page
            ->component('welcome')
            ->where('flashSale', null)
        );
    }

    /**
     * Test suggested products are displayed
     */
    public function test_suggested_products_are_displayed(): void
    {
        // Create necessary dependencies
        $seller = User::create([
            'full_name' => 'Test Seller',
            'email' => 'seller@example.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $shop = Shop::create([
            'shop_name' => 'Test Shop',
            'slug' => 'test-shop',
            'owner_id' => $seller->id,
            'is_active' => true,
        ]);

        $category = Category::create([
            'category_name' => 'Test Category',
            'slug' => 'test-category',
            'is_active' => true,
        ]);

        $brand = Brand::create([
            'brand_name' => 'Test Brand',
            'slug' => 'test-brand',
            'is_active' => true,
        ]);

        $product = Product::create([
            'shop_id' => $shop->id,
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'seller_id' => $seller->id,
            'product_name' => 'Test Product',
            'slug' => 'test-product',
            'description' => 'Test description',
            'base_price' => 100000,
            'status' => ProductStatus::ACTIVE,
            'is_active' => true,
        ]);

        ProductVariant::create([
            'product_id' => $product->id,
            'variant_name' => 'Default',
            'sku' => 'TEST-SKU-001',
            'price' => 100000,
            'stock_quantity' => 100,
            'is_active' => true,
        ]);

        ProductImage::create([
            'product_id' => $product->id,
            'image_url' => 'https://example.com/product.jpg',
            'is_primary' => true,
            'display_order' => 1,
        ]);

        $response = $this->get('/');

        $response->assertInertia(fn ($page) => $page
            ->component('welcome')
            ->has('suggestedProducts')
        );
    }

    /**
     * Test inactive products are not displayed
     */
    public function test_inactive_products_not_displayed(): void
    {
        // Create necessary dependencies
        $seller = User::create([
            'full_name' => 'Test Seller',
            'email' => 'seller@example.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $shop = Shop::create([
            'shop_name' => 'Test Shop',
            'slug' => 'test-shop',
            'owner_id' => $seller->id,
            'is_active' => true,
        ]);

        $category = Category::create([
            'category_name' => 'Test Category',
            'slug' => 'test-category',
            'is_active' => true,
        ]);

        $brand = Brand::create([
            'brand_name' => 'Test Brand',
            'slug' => 'test-brand',
            'is_active' => true,
        ]);

        // Create inactive product
        Product::create([
            'shop_id' => $shop->id,
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'seller_id' => $seller->id,
            'product_name' => 'Inactive Product',
            'slug' => 'inactive-product',
            'description' => 'Test description',
            'base_price' => 100000,
            'status' => ProductStatus::INACTIVE,
            'is_active' => false,
        ]);

        $response = $this->get('/');

        $response->assertInertia(fn ($page) => $page
            ->component('welcome')
            ->where('suggestedProducts', [])
        );
    }

    /**
     * Test welcome page handles errors gracefully
     */
    public function test_welcome_page_handles_errors_gracefully(): void
    {
        // Even if there's an issue, page should still render
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('welcome')
        );
    }

    /**
     * Test can_register prop is set
     */
    public function test_can_register_prop_is_set(): void
    {
        $response = $this->get('/');

        $response->assertInertia(fn ($page) => $page
            ->component('welcome')
            ->where('canRegister', true)
        );
    }
}
