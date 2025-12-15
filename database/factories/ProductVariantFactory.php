<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductVariant>
 */
class ProductVariantFactory extends Factory
{
    protected $model = ProductVariant::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $size = $this->faker->randomElement(['S', 'M', 'L', 'XL', 'XXL']);
        $color = $this->faker->randomElement(['Red', 'Blue', 'Green', 'Black', 'White']);
        
        return [
            'product_id' => Product::factory(),
            'variant_name' => "$size - $color",
            'sku' => 'VAR-' . strtoupper($this->faker->unique()->bothify('???###')),
            'price' => $this->faker->numberBetween(100000, 5000000),
            'compare_at_price' => null,
            'cost_per_item' => null,
            'stock_quantity' => $this->faker->numberBetween(0, 100),
            'reserved_quantity' => 0,
            'image_id' => null,
            'weight_grams' => $this->faker->numberBetween(100, 5000),
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the variant has a specific price.
     */
    public function withPrice(int $price): static
    {
        return $this->state(fn (array $attributes) => [
            'price' => $price,
        ]);
    }

    /**
     * Indicate that the variant is out of stock.
     */
    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock_quantity' => 0,
        ]);
    }
}
