<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Shop;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = $this->faker->numberBetween(10000, 500000);
        $shippingFee = $this->faker->numberBetween(1000, 5000);
        $taxAmount = $subtotal * 0.1;
        $discountAmount = 0;
        $totalAmount = $subtotal + $shippingFee + $taxAmount - $discountAmount;

        return [
            'order_number' => 'ORD-' . strtoupper($this->faker->unique()->bothify('???-########')),
            'customer_id' => User::factory(),
            'shop_id' => Shop::factory(),
            'status' => OrderStatus::PENDING,
            'payment_status' => PaymentStatus::UNPAID,
            'subtotal' => $subtotal,
            'discount_amount' => $discountAmount,
            'shipping_fee' => $shippingFee,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
            'currency' => 'VND',
            'shipping_address_id' => UserAddress::factory(),
            'payment_method' => PaymentMethod::COD,
            'note' => $this->faker->optional()->sentence(),
            'cancelled_reason' => null,
            'cancelled_at' => null,
            'confirmed_at' => null,
            'delivered_at' => null,
        ];
    }

    /**
     * Indicate that the order is confirmed.
     */
    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderStatus::CONFIRMED,
            'confirmed_at' => now(),
        ]);
    }

    /**
     * Indicate that the order is delivered.
     */
    public function delivered(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderStatus::DELIVERED,
            'confirmed_at' => now()->subDays(5),
            'delivered_at' => now(),
        ]);
    }

    /**
     * Indicate that the order is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderStatus::CANCELLED,
            'cancelled_at' => now(),
            'cancelled_reason' => $this->faker->sentence(),
        ]);
    }
}
