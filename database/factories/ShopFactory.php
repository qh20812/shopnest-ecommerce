<?php

namespace Database\Factories;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Shop>
 */
class ShopFactory extends Factory
{
    protected $model = Shop::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'owner_id' => User::factory(),
            'shop_name' => $this->faker->company(),
            'slug' => $this->faker->unique()->slug(),
            'description' => $this->faker->paragraph(),
            'logo_url' => $this->faker->imageUrl(200, 200, 'business'),
            'banner_url' => $this->faker->imageUrl(1200, 400, 'business'),
            'is_active' => true,
            'is_verified' => true,
            'rating' => $this->faker->randomFloat(2, 3, 5),
            'total_products' => $this->faker->numberBetween(0, 1000),
            'total_followers' => $this->faker->numberBetween(0, 10000),
            'total_orders' => $this->faker->numberBetween(0, 5000),
        ];
    }

    /**
     * Indicate that the shop is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the shop is unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_verified' => false,
        ]);
    }
}
