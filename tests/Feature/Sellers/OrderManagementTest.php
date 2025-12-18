<?php

namespace Tests\Feature\Sellers;

use App\Enums\DivisionType;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\AdministrativeDivision;
use App\Models\Country;
use App\Models\Order;
use App\Models\Role;
use App\Models\Shop;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $seller;
    protected User $customer;
    protected Shop $shop;
    protected Order $order;

    protected function setUp(): void
    {
        parent::setUp();

        // Create country for addresses
        $country = Country::create([
            'country_name' => 'Vietnam',
            'country_code' => 'VN',
            'iso_code_2' => 'VN',
            'iso_code_3' => 'VNM',
            'phone_code' => '+84',
        ]);

        // Create seller with role
        $this->seller = User::factory()->create();
        $sellerRole = Role::factory()->create(['role_name' => 'seller']);
        $this->seller->roles()->attach($sellerRole->id);

        // Create shop
        $this->shop = Shop::factory()->create(['owner_id' => $this->seller->id]);

        // Create customer
        $this->customer = User::factory()->create();
        $customerRole = Role::factory()->create(['role_name' => 'customer']);
        $this->customer->roles()->attach($customerRole->id);

        // Create administrative divisions for address
        $province = AdministrativeDivision::create([
            'country_id' => $country->id,
            'parent_id' => null,
            'division_name' => 'Test Province',
            'division_type' => DivisionType::PROVINCE,
            'code' => '01',
            'codename' => 'test-province',
        ]);
        
        $district = AdministrativeDivision::create([
            'country_id' => $country->id,
            'parent_id' => $province->id,
            'division_name' => 'Test District',
            'division_type' => DivisionType::WARD,
            'code' => '01-01',
            'codename' => 'test-district',
        ]);
        
        $ward = AdministrativeDivision::create([
            'country_id' => $country->id,
            'parent_id' => $district->id,
            'division_name' => 'Test Ward',
            'division_type' => DivisionType::WARD,
            'code' => '01-01-01',
            'codename' => 'test-ward',
        ]);
        
        // Create shipping address
        $shippingAddress = UserAddress::factory()->create([
            'user_id' => $this->customer->id,
            'country_id' => $country->id,
            'province_id' => $province->id,
            'district_id' => $district->id,
            'ward_id' => $ward->id,
        ]);

        // Create order
        $this->order = Order::factory()->create([
            'shop_id' => $this->shop->id,
            'customer_id' => $this->customer->id,
            'shipping_address_id' => $shippingAddress->id,
            'status' => OrderStatus::PENDING,
            'payment_method' => PaymentMethod::COD,
            'payment_status' => PaymentStatus::UNPAID,
        ]);
    }

    /** @test */
    public function seller_can_view_orders_index()
    {
        $response = $this->actingAs($this->seller)
            ->get(route('seller.orders.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->component('roles/sellers/order-manage/index')
                ->has('orders.data')
                ->has('filters')
        );
    }

    /** @test */
    public function seller_can_filter_orders_by_status()
    {
        // Create another order with different status
        $deliveredOrder = Order::factory()->create([
            'shop_id' => $this->shop->id,
            'customer_id' => $this->customer->id,
            'status' => OrderStatus::DELIVERED,
        ]);

        $response = $this->actingAs($this->seller)
            ->get(route('seller.orders.index', ['status' => 'delivered']));

        $response->assertStatus(200);
    }

    /** @test */
    public function seller_can_search_orders()
    {
        $response = $this->actingAs($this->seller)
            ->get(route('seller.orders.index', ['search' => $this->order->order_number]));

        $response->assertStatus(200);
    }

    /** @test */
    public function seller_can_filter_orders_by_date()
    {
        $response = $this->actingAs($this->seller)
            ->get(route('seller.orders.index', ['date' => now()->format('Y-m-d')]));

        $response->assertStatus(200);
    }

    /** @test */
    public function seller_can_view_own_order_details()
    {
        $response = $this->actingAs($this->seller)
            ->get(route('seller.orders.show', $this->order->id));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->component('roles/sellers/order-manage/read')
                ->has('order')
                ->where('order.id', '#' . $this->order->order_number)
        );
    }

    /** @test */
    public function seller_cannot_view_other_shop_order()
    {
        // Create another shop and order
        $otherSeller = User::factory()->create();
        $sellerRole = Role::where('role_name', 'seller')->first();
        $otherSeller->roles()->attach($sellerRole->id);
        
        $otherShop = Shop::factory()->create(['owner_id' => $otherSeller->id]);
        $otherOrder = Order::factory()->create([
            'shop_id' => $otherShop->id,
            'customer_id' => $this->customer->id,
        ]);

        $response = $this->actingAs($this->seller)
            ->get(route('seller.orders.show', $otherOrder->id));

        $response->assertStatus(403);
    }

    /** @test */
    public function seller_can_update_order_status()
    {
        $response = $this->actingAs($this->seller)
            ->put(route('seller.orders.updateStatus', $this->order->id), [
                'status' => OrderStatus::CONFIRMED->value,
            ]);

        $response->assertRedirect(route('seller.orders.show', $this->order->id));
        $response->assertSessionHas('success');

        $this->order->refresh();
        $this->assertEquals(OrderStatus::CONFIRMED->value, $this->order->status->value);
        $this->assertNotNull($this->order->confirmed_at);
    }

    /** @test */
    public function seller_can_update_order_status_to_delivered()
    {
        $response = $this->actingAs($this->seller)
            ->put(route('seller.orders.updateStatus', $this->order->id), [
                'status' => OrderStatus::DELIVERED->value,
            ]);

        $response->assertRedirect();

        $this->order->refresh();
        $this->assertEquals(OrderStatus::DELIVERED->value, $this->order->status->value);
        $this->assertNotNull($this->order->delivered_at);
    }

    /** @test */
    public function seller_can_cancel_order()
    {
        $response = $this->actingAs($this->seller)
            ->put(route('seller.orders.updateStatus', $this->order->id), [
                'status' => OrderStatus::CANCELLED->value,
            ]);

        $response->assertRedirect();

        $this->order->refresh();
        $this->assertEquals(OrderStatus::CANCELLED->value, $this->order->status->value);
        $this->assertNotNull($this->order->cancelled_at);
    }

    /** @test */
    public function seller_cannot_update_status_with_invalid_value()
    {
        $response = $this->actingAs($this->seller)
            ->put(route('seller.orders.updateStatus', $this->order->id), [
                'status' => 'invalid_status',
            ]);

        $response->assertSessionHasErrors('status');
    }

    /** @test */
    public function seller_cannot_update_other_shop_order_status()
    {
        // Create another shop and order
        $otherSeller = User::factory()->create();
        $sellerRole = Role::where('role_name', 'seller')->first();
        $otherSeller->roles()->attach($sellerRole->id);
        
        $otherShop = Shop::factory()->create(['owner_id' => $otherSeller->id]);
        $otherOrder = Order::factory()->create([
            'shop_id' => $otherShop->id,
            'customer_id' => $this->customer->id,
        ]);

        $response = $this->actingAs($this->seller)
            ->put(route('seller.orders.updateStatus', $otherOrder->id), [
                'status' => OrderStatus::CONFIRMED->value,
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function non_seller_cannot_access_seller_orders()
    {
        $regularUser = User::factory()->create();

        $response = $this->actingAs($regularUser)
            ->get(route('seller.orders.index'));

        $response->assertStatus(403);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_seller_orders()
    {
        $response = $this->get(route('seller.orders.index'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function order_list_is_paginated()
    {
        // Create 20 orders
        Order::factory(20)->create([
            'shop_id' => $this->shop->id,
            'customer_id' => $this->customer->id,
        ]);

        $response = $this->actingAs($this->seller)
            ->get(route('seller.orders.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->has('orders.data', 15) // Default pagination is 15
                ->has('orders.current_page')
                ->has('orders.last_page')
        );
    }

    /** @test */
    public function seller_without_shop_sees_empty_orders()
    {
        // Create a seller without a shop
        $sellerWithoutShop = User::factory()->create();
        $sellerRole = Role::where('role_name', 'seller')->first();
        $sellerWithoutShop->roles()->attach($sellerRole->id);

        $response = $this->actingAs($sellerWithoutShop)
            ->get(route('seller.orders.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->has('orders.data', 0)
        );
    }
}
