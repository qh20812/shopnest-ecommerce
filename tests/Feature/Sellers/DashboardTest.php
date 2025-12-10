<?php

namespace Tests\Feature\Sellers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Shop;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create required geographic data for addresses
        \App\Models\Country::create([
            'id' => 1,
            'country_name' => 'Test Country',
            'iso_code_2' => 'TC',
            'iso_code_3' => 'TST',
            'is_active' => true,
        ]);
        
        \App\Models\AdministrativeDivision::create([
            'id' => 1,
            'division_name' => 'Test Division',
            'division_type' => 'province',
            'country_id' => 1,
            'is_active' => true,
        ]);
        
        // Set a fixed test time (middle of December 2025)
        Carbon::setTestNow(Carbon::create(2025, 12, 15, 12, 0, 0));
    }
    
    protected function tearDown(): void
    {
        Carbon::setTestNow(); // Reset time
        parent::tearDown();
    }

    private function createSellerWithShop()
    {
        $seller = User::create([
            'full_name' => 'Test Seller',
            'email' => 'seller' . uniqid() . '@test.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $shop = Shop::create([
            'owner_id' => $seller->id,
            'shop_name' => 'Test Shop',
            'slug' => 'test-shop-' . uniqid(),
            'is_active' => true,
        ]);

        return ['seller' => $seller, 'shop' => $shop];
    }

    private function createProduct($shop, $totalSold = 0, $basePrice = 100000)
    {
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

        $product = Product::create([
            'shop_id' => $shop->id,
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'seller_id' => $shop->owner_id,
            'product_name' => 'Test Product ' . uniqid(),
            'slug' => 'test-product-' . uniqid(),
            'description' => 'Test description',
            'base_price' => $basePrice,
            'status' => 'active',
            'total_sold' => $totalSold,
            'total_quantity' => 100,
        ]);

        ProductImage::create([
            'product_id' => $product->id,
            'image_url' => 'https://via.placeholder.com/100',
            'display_order' => 1,
        ]);

        return $product;
    }

    private function createOrder($shop, $customerId, $totalAmount, $paymentStatus = 'paid', $createdAt = null)
    {
        // Create a shipping address for the order with ALL required fields
        $address = \App\Models\UserAddress::create([
            'user_id' => $customerId,
            'address_label' => 'Test Address',
            'recipient_name' => 'Test User',
            'phone_number' => '0123456789',
            'address_line1' => '123 Test Street',
            'country_id' => 1,
            'province_id' => 1,
            'district_id' => 1,
            'ward_id' => 1,
            'is_default' => false,
        ]);

        return Order::create([
            'order_number' => 'ORD-' . uniqid(),
            'customer_id' => $customerId,
            'shop_id' => $shop->id,
            'shipping_address_id' => $address->id,
            'status' => 'delivered',
            'payment_status' => $paymentStatus,
            'subtotal' => $totalAmount,
            'discount_amount' => 0,
            'shipping_fee' => 0,
            'tax_amount' => 0,
            'total_amount' => $totalAmount,
            'payment_method' => 'cod',
            'created_at' => $createdAt ?? now(),
        ]);
    }

    /** @test */
    public function guest_cannot_access_dashboard(): void
    {
        $response = $this->get('/seller/dashboard');
        $response->assertRedirect('/login');
    }

    /** @test */
    public function authenticated_seller_can_access_dashboard(): void
    {
        $data = $this->createSellerWithShop();
        
        $response = $this->actingAs($data['seller'])->get('/seller/dashboard');
        
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('roles/sellers/dashboard'));
    }

    /** @test */
    public function dashboard_returns_correct_structure(): void
    {
        $data = $this->createSellerWithShop();
        
        $response = $this->actingAs($data['seller'])->get('/seller/dashboard');
        
        $response->assertInertia(fn ($page) => 
            $page->has('stats')
                ->has('stats.revenue')
                ->has('stats.revenue_change')
                ->has('stats.orders')
                ->has('stats.orders_change')
                ->has('stats.products')
                ->has('stats.products_change')
                ->has('stats.customers')
                ->has('stats.customers_change')
                ->has('sales_chart')
                ->has('sales_chart.labels')
                ->has('sales_chart.data')
                ->has('top_products')
        );
    }

    /** @test */
    public function dashboard_calculates_revenue_correctly(): void
    {
        $data = $this->createSellerWithShop();
        $customer = User::factory()->create();

        // Create orders this month
        $this->createOrder($data['shop'], $customer->id, 1000000, 'paid');
        $this->createOrder($data['shop'], $customer->id, 500000, 'paid');
        
        $response = $this->actingAs($data['seller'])->get('/seller/dashboard');
        
        $response->assertInertia(fn ($page) => 
            $page->where('stats.revenue', 1500000)
        );
    }

    /** @test */
    public function dashboard_excludes_unpaid_orders_from_revenue(): void
    {
        $data = $this->createSellerWithShop();
        $customer = User::factory()->create();

        $this->createOrder($data['shop'], $customer->id, 1000000, 'paid');
        $this->createOrder($data['shop'], $customer->id, 500000, 'unpaid');
        
        $response = $this->actingAs($data['seller'])->get('/seller/dashboard');
        
        $response->assertInertia(fn ($page) => 
            $page->where('stats.revenue', 1000000)
        );
    }

    /** @test */
    public function dashboard_calculates_revenue_change_correctly(): void
    {
        $data = $this->createSellerWithShop();
        $customer = User::factory()->create();

        // Last month: 1,000,000
        $this->createOrder($data['shop'], $customer->id, 1000000, 'paid', now()->subMonths(1)->startOfMonth()->addDays(10));
        
        // This month: 1,500,000
        $this->createOrder($data['shop'], $customer->id, 1500000, 'paid', now()->startOfMonth()->addDays(10));
        
        $response = $this->actingAs($data['seller'])->get('/seller/dashboard');
        
        $response->assertInertia(fn ($page) => 
            $page->where('stats.revenue_change', 50)
        );
    }

    /** @test */
    public function dashboard_calculates_orders_count_correctly(): void
    {
        $data = $this->createSellerWithShop();
        $customer = User::factory()->create();

        $this->createOrder($data['shop'], $customer->id, 1000000);
        $this->createOrder($data['shop'], $customer->id, 500000);
        $this->createOrder($data['shop'], $customer->id, 750000);
        
        $response = $this->actingAs($data['seller'])->get('/seller/dashboard');
        
        $response->assertInertia(fn ($page) => 
            $page->where('stats.orders', 3)
        );
    }

    /** @test */
    public function dashboard_calculates_orders_change_correctly(): void
    {
        $data = $this->createSellerWithShop();
        $customer = User::factory()->create();

        // Last month: 1 order
        $this->createOrder($data['shop'], $customer->id, 1000000, 'paid', now()->subMonths(1)->startOfMonth()->addDays(10));
        $this->createOrder($data['shop'], $customer->id, 500000, 'paid', now()->subMonths(1)->startOfMonth()->addDays(11));
        
        // This month: 4 orders
        $this->createOrder($data['shop'], $customer->id, 1000000, 'paid', now()->startOfMonth()->addDays(10));
        $this->createOrder($data['shop'], $customer->id, 500000, 'paid', now()->startOfMonth()->addDays(11));
        $this->createOrder($data['shop'], $customer->id, 750000, 'paid', now()->startOfMonth()->addDays(12));
        $this->createOrder($data['shop'], $customer->id, 250000, 'paid', now()->startOfMonth()->addDays(13));
        
        $response = $this->actingAs($data['seller'])->get('/seller/dashboard');
        
        $response->assertInertia(fn ($page) => 
            $page->where('stats.orders_change', 100)
        );
    }

    /** @test */
    public function dashboard_counts_active_products_correctly(): void
    {
        $data = $this->createSellerWithShop();

        $this->createProduct($data['shop']);
        $this->createProduct($data['shop']);
        $this->createProduct($data['shop']);
        
        $response = $this->actingAs($data['seller'])->get('/seller/dashboard');
        
        $response->assertInertia(fn ($page) => 
            $page->where('stats.products', 3)
        );
    }

    /** @test */
    public function dashboard_counts_unique_customers_correctly(): void
    {
        $data = $this->createSellerWithShop();
        $customer1 = User::factory()->create();
        $customer2 = User::factory()->create();

        // Customer 1 orders twice
        $this->createOrder($data['shop'], $customer1->id, 1000000);
        $this->createOrder($data['shop'], $customer1->id, 500000);
        
        // Customer 2 orders once
        $this->createOrder($data['shop'], $customer2->id, 750000);
        
        $response = $this->actingAs($data['seller'])->get('/seller/dashboard');
        
        $response->assertInertia(fn ($page) => 
            $page->where('stats.customers', 2)
        );
    }

    /** @test */
    public function dashboard_calculates_customers_change_correctly(): void
    {
        $data = $this->createSellerWithShop();
        $customer1 = User::factory()->create();
        $customer2 = User::factory()->create();
        $customer3 = User::factory()->create();

        // Last month: 1 unique customer
        $this->createOrder($data['shop'], $customer1->id, 1000000, 'paid', now()->subMonths(1)->startOfMonth()->addDays(10));
        
        // This month: 2 unique customers
        $this->createOrder($data['shop'], $customer2->id, 500000, 'paid', now()->startOfMonth()->addDays(10));
        $this->createOrder($data['shop'], $customer3->id, 750000, 'paid', now()->startOfMonth()->addDays(11));
        
        $response = $this->actingAs($data['seller'])->get('/seller/dashboard');
        
        $response->assertInertia(fn ($page) => 
            $page->where('stats.customers_change', 100)
        );
    }

    /** @test */
    public function dashboard_returns_sales_chart_with_7_months(): void
    {
        $data = $this->createSellerWithShop();
        
        $response = $this->actingAs($data['seller'])->get('/seller/dashboard');
        
        $response->assertInertia(fn ($page) => 
            $page->where('sales_chart.labels', fn ($labels) => count($labels) === 7)
                ->where('sales_chart.data', fn ($data) => count($data) === 7)
        );
    }

    /** @test */
    public function dashboard_sales_chart_calculates_monthly_revenue(): void
    {
        $data = $this->createSellerWithShop();
        $customer = User::factory()->create();

        // Create orders in current month
        $this->createOrder($data['shop'], $customer->id, 5000000, 'paid', now()->startOfMonth()->addDays(10));
        $this->createOrder($data['shop'], $customer->id, 3000000, 'paid', now()->startOfMonth()->addDays(11));
        
        $response = $this->actingAs($data['seller'])->get('/seller/dashboard');
        
        // Current month should have 8 million (8.0 in chart since it's in millions)
        $response->assertInertia(fn ($page) => 
            $page->where('sales_chart.data.6', 8) // Last index is current month
        );
    }

    /** @test */
    public function dashboard_returns_top_5_products(): void
    {
        $data = $this->createSellerWithShop();

        // Create 7 products with different sales
        $this->createProduct($data['shop'], 100);
        $this->createProduct($data['shop'], 200);
        $this->createProduct($data['shop'], 300);
        $this->createProduct($data['shop'], 400);
        $this->createProduct($data['shop'], 500);
        $this->createProduct($data['shop'], 50);
        $this->createProduct($data['shop'], 75);
        
        $response = $this->actingAs($data['seller'])->get('/seller/dashboard');
        
        $response->assertInertia(fn ($page) => 
            $page->where('top_products', fn ($products) => count($products) === 5)
        );
    }

    /** @test */
    public function dashboard_top_products_ordered_by_sales_desc(): void
    {
        $data = $this->createSellerWithShop();

        $this->createProduct($data['shop'], 100);
        $this->createProduct($data['shop'], 500);
        $this->createProduct($data['shop'], 300);
        
        $response = $this->actingAs($data['seller'])->get('/seller/dashboard');
        
        $response->assertInertia(fn ($page) => 
            $page->where('top_products.0.sales', 500)
                ->where('top_products.1.sales', 300)
                ->where('top_products.2.sales', 100)
        );
    }

    /** @test */
    public function dashboard_top_products_include_correct_data(): void
    {
        $data = $this->createSellerWithShop();

        $this->createProduct($data['shop'], 100, 50000);
        
        $response = $this->actingAs($data['seller'])->get('/seller/dashboard');
        
        $response->assertInertia(fn ($page) => 
            $page->has('top_products.0.name')
                ->has('top_products.0.sales')
                ->has('top_products.0.revenue')
                ->has('top_products.0.image')
        );
    }

    /** @test */
    public function dashboard_handles_seller_with_no_shop(): void
    {
        $seller = User::create([
            'full_name' => 'Seller Without Shop',
            'email' => 'noseller@test.com',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
        
        $response = $this->actingAs($seller)->get('/seller/dashboard');
        
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->where('stats.revenue', 0)
                ->where('stats.orders', 0)
                ->where('stats.products', 0)
                ->where('stats.customers', 0)
        );
    }

    /** @test */
    public function dashboard_shows_zero_change_when_no_last_month_data(): void
    {
        $data = $this->createSellerWithShop();
        $customer = User::factory()->create();

        // Only create orders this month
        $this->createOrder($data['shop'], $customer->id, 1000000, 'paid', now()->startOfMonth()->addDays(10));
        
        $response = $this->actingAs($data['seller'])->get('/seller/dashboard');
        
        $response->assertInertia(fn ($page) => 
            $page->where('stats.revenue_change', 100) // 100% increase from 0
                ->where('stats.orders_change', 100)
        );
    }

    /** @test */
    public function dashboard_handles_negative_changes(): void
    {
        $data = $this->createSellerWithShop();
        $customer = User::factory()->create();

        // Last month: 2,000,000
        $this->createOrder($data['shop'], $customer->id, 2000000, 'paid', now()->subMonths(1)->startOfMonth()->addDays(10));
        
        // This month: 1,000,000
        $this->createOrder($data['shop'], $customer->id, 1000000, 'paid', now()->startOfMonth()->addDays(10));
        
        $response = $this->actingAs($data['seller'])->get('/seller/dashboard');
        
        $response->assertInertia(fn ($page) => 
            $page->where('stats.revenue_change', -50)
        );
    }

    /** @test */
    public function dashboard_only_shows_sellers_own_data(): void
    {
        $data1 = $this->createSellerWithShop();
        $data2 = $this->createSellerWithShop();
        
        $customer = User::factory()->create();

        // Create orders for seller 1
        $this->createOrder($data1['shop'], $customer->id, 1000000);
        
        // Create orders for seller 2
        $this->createOrder($data2['shop'], $customer->id, 2000000);
        
        // Seller 1 should only see their own data
        $response = $this->actingAs($data1['seller'])->get('/seller/dashboard');
        
        $response->assertInertia(fn ($page) => 
            $page->where('stats.revenue', 1000000)
                ->where('stats.orders', 1)
        );
    }
}
