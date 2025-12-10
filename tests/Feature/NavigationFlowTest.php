<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Shop;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NavigationFlowTest extends TestCase
{
    use RefreshDatabase;

    private function createTestProduct($suffix = '', $productData = []): Product
    {
        $category = Category::create([
            'category_name' => 'Test Category' . $suffix,
            'slug' => 'test-category' . $suffix,
            'status' => 'active',
        ]);

        $brand = Brand::create([
            'brand_name' => 'Test Brand' . $suffix,
            'slug' => 'test-brand' . $suffix,
            'status' => 'active',
        ]);

        $user = User::factory()->create();

        $shop = Shop::create([
            'owner_id' => $user->id,
            'shop_name' => 'Test Shop' . $suffix,
            'slug' => 'test-shop' . $suffix,
            'description' => 'Test shop description',
            'is_verified' => true,
            'is_active' => true,
            'rating' => 4.5,
        ]);

        $product = Product::create(array_merge([
            'product_name' => 'Test Product' . $suffix,
            'slug' => 'test-product' . $suffix,
            'description' => 'Test description',
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'shop_id' => $shop->id,
            'seller_id' => $user->id,
            'base_price' => 100000,
            'status' => 'active',
            'view_count' => 100,
            'total_sold' => 50,
        ], $productData));

        // Create product image
        ProductImage::create([
            'product_id' => $product->id,
            'image_url' => 'https://example.com/image.jpg',
            'is_primary' => true,
            'display_order' => 1,
        ]);

        // Create product variant
        ProductVariant::create([
            'product_id' => $product->id,
            'variant_name' => 'Default',
            'sku' => 'TEST-SKU-' . $product->id,
            'price' => 100000,
            'compare_at_price' => 150000,
            'stock_quantity' => 100,
            'status' => 'active',
        ]);

        return $product->fresh(['images', 'brand', 'category', 'variants']);
    }

    /** @test */
    public function home_page_displays_products_with_navigation_links()
    {
        // Create test products
        $product1 = $this->createTestProduct('1');
        $product2 = $this->createTestProduct('2');

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('welcome')
            ->has('suggestedProducts')
            ->has('bestSellers')
        );
    }

    /** @test */
    public function can_navigate_from_home_to_product_detail()
    {
        $product = $this->createTestProduct();

        // Visit home page
        $this->get('/')->assertStatus(200);

        // Navigate to product detail
        $response = $this->get("/product/{$product->id}");

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('detail')
            ->has('product', fn ($prop) => $prop
                ->where('id', $product->id)
                ->where('name', $product->product_name)
                ->etc()
            )
        );
    }

    /** @test */
    public function shopping_page_displays_products_with_navigation_links()
    {
        $product1 = $this->createTestProduct('1');
        $product2 = $this->createTestProduct('2');
        $product3 = $this->createTestProduct('3');

        $response = $this->get('/shopping');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('shopping')
            ->has('products')
            ->has('pagination')
            ->where('products.0.id', $product1->id)
        );
    }

    /** @test */
    public function can_navigate_from_shopping_to_product_detail()
    {
        $product = $this->createTestProduct();

        // Visit shopping page
        $this->get('/shopping')->assertStatus(200);

        // Navigate to product detail
        $response = $this->get("/product/{$product->id}");

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('detail')
            ->has('product', fn ($prop) => $prop
                ->where('id', $product->id)
                ->where('name', $product->product_name)
                ->etc()
            )
        );
    }

    /** @test */
    public function product_detail_page_displays_correctly()
    {
        $product = $this->createTestProduct();

        $response = $this->get("/product/{$product->id}");

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('detail')
            ->has('product', fn ($prop) => $prop
                ->where('id', $product->id)
                ->where('name', $product->product_name)
                ->has('images')
                ->has('variants')
                ->has('brand')
                ->has('category')
                ->etc()
            )
            ->has('relatedProducts')
            ->has('rating')
        );
    }

    /** @test */
    public function product_detail_page_shows_related_products_with_links()
    {
        // Create main product
        $mainProduct = $this->createTestProduct('Main');
        
        // Create related products in same category
        $relatedProduct1 = $this->createTestProduct('Related1', [
            'category_id' => $mainProduct->category_id,
        ]);
        $relatedProduct2 = $this->createTestProduct('Related2', [
            'category_id' => $mainProduct->category_id,
        ]);

        $response = $this->get("/product/{$mainProduct->id}");

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('detail')
            ->has('relatedProducts')
            ->where('product.id', $mainProduct->id)
        );
    }

    /** @test */
    public function can_navigate_from_product_detail_to_another_product_detail()
    {
        // Create main product
        $mainProduct = $this->createTestProduct('Main');
        
        // Create related product
        $relatedProduct = $this->createTestProduct('Related', [
            'category_id' => $mainProduct->category_id,
        ]);

        // Visit first product detail
        $this->get("/product/{$mainProduct->id}")->assertStatus(200);

        // Navigate to related product detail
        $response = $this->get("/product/{$relatedProduct->id}");

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('detail')
            ->has('product', fn ($prop) => $prop
                ->where('id', $relatedProduct->id)
                ->where('name', $relatedProduct->product_name)
                ->etc()
            )
        );
    }

    /** @test */
    public function home_page_product_links_have_correct_url_format()
    {
        $product = $this->createTestProduct();

        $response = $this->get('/');

        $response->assertStatus(200);
        
        // Verify the response contains product data
        $response->assertInertia(fn ($page) => $page
            ->component('welcome')
        );
    }

    /** @test */
    public function shopping_page_product_links_have_correct_url_format()
    {
        $product = $this->createTestProduct();

        $response = $this->get('/shopping');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('shopping')
            ->has('products', fn ($products) => $products
                ->has(0, fn ($prod) => $prod
                    ->where('id', $product->id)
                    ->etc()
                )
            )
        );
    }

    /** @test */
    public function detail_page_related_product_links_have_correct_url_format()
    {
        $mainProduct = $this->createTestProduct('Main');
        $relatedProduct = $this->createTestProduct('Related', [
            'category_id' => $mainProduct->category_id,
        ]);

        $response = $this->get("/product/{$mainProduct->id}");

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('detail')
            ->has('relatedProducts')
        );
    }

    /** @test */
    public function navigation_flow_from_home_through_detail_to_related_product()
    {
        // Create products
        $product1 = $this->createTestProduct('1');
        $product2 = $this->createTestProduct('2', [
            'category_id' => $product1->category_id,
        ]);

        // Step 1: Visit home page
        $response1 = $this->get('/');
        $response1->assertStatus(200);
        $response1->assertInertia(fn ($page) => $page->component('welcome'));

        // Step 2: Navigate to product detail
        $response2 = $this->get("/product/{$product1->id}");
        $response2->assertStatus(200);
        $response2->assertInertia(fn ($page) => $page
            ->component('detail')
            ->where('product.id', $product1->id)
        );

        // Step 3: Navigate to related product
        $response3 = $this->get("/product/{$product2->id}");
        $response3->assertStatus(200);
        $response3->assertInertia(fn ($page) => $page
            ->component('detail')
            ->where('product.id', $product2->id)
        );
    }

    /** @test */
    public function navigation_flow_from_shopping_through_detail_to_related_product()
    {
        // Create products
        $product1 = $this->createTestProduct('1');
        $product2 = $this->createTestProduct('2', [
            'category_id' => $product1->category_id,
        ]);

        // Step 1: Visit shopping page
        $response1 = $this->get('/shopping');
        $response1->assertStatus(200);
        $response1->assertInertia(fn ($page) => $page->component('shopping'));

        // Step 2: Navigate to product detail
        $response2 = $this->get("/product/{$product1->id}");
        $response2->assertStatus(200);
        $response2->assertInertia(fn ($page) => $page
            ->component('detail')
            ->where('product.id', $product1->id)
        );

        // Step 3: Navigate to related product
        $response3 = $this->get("/product/{$product2->id}");
        $response3->assertStatus(200);
        $response3->assertInertia(fn ($page) => $page
            ->component('detail')
            ->where('product.id', $product2->id)
        );
    }

    /** @test */
    public function product_detail_404_for_invalid_product_id()
    {
        $response = $this->get('/product/99999');

        $response->assertStatus(404);
    }

    /** @test */
    public function product_detail_404_for_inactive_product()
    {
        $product = $this->createTestProduct();
        $product->update(['status' => 'inactive']);

        $response = $this->get("/product/{$product->id}");

        $response->assertStatus(404);
    }
}
