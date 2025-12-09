<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Shop;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShoppingTest extends TestCase
{
    use RefreshDatabase;

    private function createTestData($suffix = '')
    {
        $user = User::create([
            'full_name' => 'Test Seller ' . $suffix,
            'email' => 'seller' . $suffix . '@test.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);

        $shop = Shop::create([
            'owner_id' => $user->id,
            'shop_name' => 'Test Shop ' . $suffix,
            'slug' => 'test-shop-' . $suffix,
            'description' => 'Test description',
            'is_active' => true,
        ]);

        $category = Category::create([
            'category_name' => 'Test Category ' . $suffix,
            'slug' => 'test-category-' . $suffix,
            'is_active' => true,
        ]);

        $brand = Brand::create([
            'brand_name' => 'Test Brand ' . $suffix,
            'slug' => 'test-brand-' . $suffix,
            'is_active' => true,
        ]);

        return compact('user', 'shop', 'category', 'brand');
    }

    private function createProduct($data, $productData = [])
    {
        $product = Product::create(array_merge([
            'shop_id' => $data['shop']->id,
            'category_id' => $data['category']->id,
            'brand_id' => $data['brand']->id,
            'seller_id' => $data['user']->id,
            'product_name' => 'Test Product',
            'slug' => 'test-product-' . uniqid(),
            'description' => 'Test description',
            'base_price' => 100000,
            'status' => 'active',
            'total_quantity' => 100,
            'rating' => 4.5,
            'review_count' => 10,
        ], $productData));

        ProductImage::create([
            'product_id' => $product->id,
            'image_url' => 'https://example.com/image.jpg',
            'display_order' => 1,
        ]);

        return $product;
    }

    /** @test */
    public function guest_can_access_shopping_page()
    {
        $response = $this->get('/shopping');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('shopping')
            ->has('products')
            ->has('pagination')
            ->has('categories')
            ->has('brands')
            ->has('filters')
        );
    }

    /** @test */
    public function shopping_page_displays_active_products()
    {
        $data = $this->createTestData();
        $activeProduct = $this->createProduct($data, ['product_name' => 'Active Product']);
        $inactiveProduct = $this->createProduct($data, ['product_name' => 'Inactive Product', 'status' => 'inactive']);

        $response = $this->get('/shopping');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->where('products.0.name', 'Active Product')
            ->where('products', fn ($products) => count($products) === 1)
        );
    }

    /** @test */
    public function shopping_page_returns_correct_product_structure()
    {
        $data = $this->createTestData();
        $product = $this->createProduct($data, [
            'product_name' => 'Test Product',
            'base_price' => 250000,
            'rating' => 4.8,
            'review_count' => 50,
        ]);

        $response = $this->get('/shopping');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->has('products.0', fn ($product) => $product
                ->has('id')
                ->has('name')
                ->has('image')
                ->has('price')
                ->has('originalPrice')
                ->has('rating')
                ->has('reviews')
                ->has('brand')
            )
        );
    }

    /** @test */
    public function can_filter_products_by_category()
    {
        $data1 = $this->createTestData('1');
        $data2 = $this->createTestData('2');
        
        $product1 = $this->createProduct($data1, ['product_name' => 'Product Cat 1']);
        $product2 = $this->createProduct($data2, ['product_name' => 'Product Cat 2']);

        $response = $this->get('/shopping?category=' . $data1['category']->id);

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->where('products.0.name', 'Product Cat 1')
            ->where('products', fn ($products) => count($products) === 1)
        );
    }

    /** @test */
    public function can_filter_products_by_min_price()
    {
        $data = $this->createTestData();
        $cheapProduct = $this->createProduct($data, ['product_name' => 'Cheap Product', 'base_price' => 50000]);
        $expensiveProduct = $this->createProduct($data, ['product_name' => 'Expensive Product', 'base_price' => 500000]);

        $response = $this->get('/shopping?min_price=100000');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->where('products.0.name', 'Expensive Product')
            ->where('products', fn ($products) => count($products) === 1)
        );
    }

    /** @test */
    public function can_filter_products_by_max_price()
    {
        $data = $this->createTestData();
        $cheapProduct = $this->createProduct($data, ['product_name' => 'Cheap Product', 'base_price' => 50000]);
        $expensiveProduct = $this->createProduct($data, ['product_name' => 'Expensive Product', 'base_price' => 500000]);

        $response = $this->get('/shopping?max_price=100000');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->where('products.0.name', 'Cheap Product')
            ->where('products', fn ($products) => count($products) === 1)
        );
    }

    /** @test */
    public function can_filter_products_by_price_range()
    {
        $data = $this->createTestData();
        $this->createProduct($data, ['product_name' => 'Cheap', 'base_price' => 50000]);
        $this->createProduct($data, ['product_name' => 'Medium', 'base_price' => 150000]);
        $this->createProduct($data, ['product_name' => 'Expensive', 'base_price' => 500000]);

        $response = $this->get('/shopping?min_price=100000&max_price=200000');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->where('products.0.name', 'Medium')
            ->where('products', fn ($products) => count($products) === 1)
        );
    }

    /** @test */
    public function can_filter_products_by_brand()
    {
        $data1 = $this->createTestData('1');
        $data2 = $this->createTestData('2');
        
        $product1 = $this->createProduct($data1, ['product_name' => 'Product Brand 1']);
        $product2 = $this->createProduct($data2, ['product_name' => 'Product Brand 2']);

        $response = $this->get('/shopping?brands[]=' . $data1['brand']->id);

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->where('products.0.name', 'Product Brand 1')
            ->where('products', fn ($products) => count($products) === 1)
        );
    }

    /** @test */
    public function can_filter_products_by_multiple_brands()
    {
        $data1 = $this->createTestData('1');
        $data2 = $this->createTestData('2');
        $data3 = $this->createTestData('3');
        
        $product1 = $this->createProduct($data1, ['product_name' => 'Product Brand 1']);
        $product2 = $this->createProduct($data2, ['product_name' => 'Product Brand 2']);
        $product3 = $this->createProduct($data3, ['product_name' => 'Product Brand 3']);

        $response = $this->get('/shopping?brands[]=' . $data1['brand']->id . '&brands[]=' . $data2['brand']->id);

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->where('products', fn ($products) => count($products) === 2)
        );
    }

    /** @test */
    public function can_sort_products_by_popular()
    {
        $data = $this->createTestData();
        $lessPopular = $this->createProduct($data, ['product_name' => 'Less Popular', 'total_sold' => 10, 'view_count' => 100]);
        $morePopular = $this->createProduct($data, ['product_name' => 'More Popular', 'total_sold' => 50, 'view_count' => 500]);

        $response = $this->get('/shopping?sort_by=popular');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->where('products.0.name', 'More Popular')
        );
    }

    /** @test */
    public function can_sort_products_by_newest()
    {
        $data = $this->createTestData();
        
        // Create older product first
        $older = Product::create([
            'shop_id' => $data['shop']->id,
            'category_id' => $data['category']->id,
            'brand_id' => $data['brand']->id,
            'seller_id' => $data['user']->id,
            'product_name' => 'Older Product',
            'slug' => 'older-product-' . uniqid(),
            'description' => 'Test description',
            'base_price' => 100000,
            'status' => 'active',
            'total_quantity' => 100,
            'rating' => 4.5,
            'review_count' => 10,
            'created_at' => now()->subDays(5),
            'updated_at' => now()->subDays(5),
        ]);
        ProductImage::create(['product_id' => $older->id, 'image_url' => 'https://example.com/old.jpg', 'display_order' => 1]);
        
        // Wait a moment and create newer product
        sleep(1);
        $newer = Product::create([
            'shop_id' => $data['shop']->id,
            'category_id' => $data['category']->id,
            'brand_id' => $data['brand']->id,
            'seller_id' => $data['user']->id,
            'product_name' => 'Newer Product',
            'slug' => 'newer-product-' . uniqid(),
            'description' => 'Test description',
            'base_price' => 100000,
            'status' => 'active',
            'total_quantity' => 100,
            'rating' => 4.5,
            'review_count' => 10,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        ProductImage::create(['product_id' => $newer->id, 'image_url' => 'https://example.com/new.jpg', 'display_order' => 1]);

        $response = $this->get('/shopping?sort_by=newest');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->where('products.0.name', 'Newer Product')
        );
    }

    /** @test */
    public function can_sort_products_by_price_ascending()
    {
        $data = $this->createTestData();
        $expensive = $this->createProduct($data, ['product_name' => 'Expensive', 'base_price' => 500000]);
        $cheap = $this->createProduct($data, ['product_name' => 'Cheap', 'base_price' => 100000]);

        $response = $this->get('/shopping?sort_by=price-asc');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->where('products.0.name', 'Cheap')
        );
    }

    /** @test */
    public function can_sort_products_by_price_descending()
    {
        $data = $this->createTestData();
        $cheap = $this->createProduct($data, ['product_name' => 'Cheap', 'base_price' => 100000]);
        $expensive = $this->createProduct($data, ['product_name' => 'Expensive', 'base_price' => 500000]);

        $response = $this->get('/shopping?sort_by=price-desc');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->where('products.0.name', 'Expensive')
        );
    }

    /** @test */
    public function pagination_works_correctly()
    {
        $data = $this->createTestData();
        
        // Create 25 products
        for ($i = 1; $i <= 25; $i++) {
            $this->createProduct($data, ['product_name' => 'Product ' . $i]);
        }

        $response = $this->get('/shopping?per_page=10');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->where('pagination.per_page', 10)
            ->where('pagination.total', 25)
            ->where('pagination.last_page', 3)
            ->where('pagination.current_page', 1)
            ->where('products', fn ($products) => count($products) === 10)
        );
    }

    /** @test */
    public function can_navigate_to_second_page()
    {
        $data = $this->createTestData();
        
        // Create 15 products
        for ($i = 1; $i <= 15; $i++) {
            $this->createProduct($data, ['product_name' => 'Product ' . $i]);
        }

        $response = $this->get('/shopping?page=2&per_page=10');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->where('pagination.current_page', 2)
            ->where('products', fn ($products) => count($products) === 5)
        );
    }

    /** @test */
    public function categories_include_all_option()
    {
        $data = $this->createTestData();

        $response = $this->get('/shopping');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->where('categories.0.id', 'all')
            ->where('categories.0.name', 'Tất cả sản phẩm')
        );
    }

    /** @test */
    public function only_active_categories_are_returned()
    {
        Category::create([
            'category_name' => 'Active Category',
            'slug' => 'active-category',
            'is_active' => true,
        ]);

        Category::create([
            'category_name' => 'Inactive Category',
            'slug' => 'inactive-category',
            'is_active' => false,
        ]);

        $response = $this->get('/shopping');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->where('categories', fn ($categories) => count($categories) === 2) // 'all' + 1 active
        );
    }

    /** @test */
    public function only_active_brands_are_returned()
    {
        Brand::create([
            'brand_name' => 'Active Brand',
            'slug' => 'active-brand',
            'is_active' => true,
        ]);

        Brand::create([
            'brand_name' => 'Inactive Brand',
            'slug' => 'inactive-brand',
            'is_active' => false,
        ]);

        $response = $this->get('/shopping');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->where('brands', fn ($brands) => count($brands) === 1)
        );
    }

    /** @test */
    public function filters_are_returned_correctly()
    {
        $data = $this->createTestData();

        $response = $this->get('/shopping?category=1&min_price=100000&max_price=500000&brands[]=1&sort_by=newest');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->where('filters.category', '1')
            ->where('filters.min_price', '100000')
            ->where('filters.max_price', '500000')
            ->where('filters.selected_brands', [1])
            ->where('filters.sort_by', 'newest')
        );
    }

    /** @test */
    public function combined_filters_work_together()
    {
        $data1 = $this->createTestData('1');
        $data2 = $this->createTestData('2');
        
        // Product that should match all filters
        $matchingProduct = $this->createProduct($data1, [
            'product_name' => 'Matching Product',
            'base_price' => 250000,
        ]);

        // Products that should not match
        $this->createProduct($data2, ['product_name' => 'Wrong Category', 'base_price' => 250000]);
        $this->createProduct($data1, ['product_name' => 'Wrong Price', 'base_price' => 50000]);
        $this->createProduct($data1, ['product_name' => 'Inactive', 'base_price' => 250000, 'status' => 'inactive']);

        $response = $this->get('/shopping?category=' . $data1['category']->id . '&min_price=200000&max_price=300000&brands[]=' . $data1['brand']->id);

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->where('products.0.name', 'Matching Product')
            ->where('products', fn ($products) => count($products) === 1)
        );
    }
}
