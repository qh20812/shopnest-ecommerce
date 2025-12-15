<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->words(3, true);
        $shop = Shop::factory()->create();
        
        return [
            'shop_id' => $shop->id,
            'seller_id' => $shop->owner_id,
            'category_id' => Category::factory(),
            'brand_id' => null,
            'product_name' => ucfirst($name),
            'slug' => \Str::slug($name) . '-' . $this->faker->unique()->numberBetween(1000, 9999),
            'description' => $this->faker->paragraph(5),
            'specifications' => null,
            'base_price' => $this->faker->numberBetween(100000, 10000000),
            'currency' => 'VND',
            'weight_grams' => $this->faker->numberBetween(100, 5000),
            'length_cm' => $this->faker->numberBetween(10, 100),
            'width_cm' => $this->faker->numberBetween(10, 100),
            'height_cm' => $this->faker->numberBetween(10, 100),
            'status' => 'active',
            'total_quantity' => $this->faker->numberBetween(0, 1000),
            'total_sold' => $this->faker->numberBetween(0, 500),
            'rating' => $this->faker->randomFloat(2, 0, 5),
            'review_count' => $this->faker->numberBetween(0, 100),
            'view_count' => $this->faker->numberBetween(0, 10000),
        ];
    }

    /**
     * Indicate that the product is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }

    /**
     * Indicate that the product is a draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }

    /**
     * Indicate that the product is featured.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }

    /**
     * Indicate that the product has a compare price.
     */
    public function withComparePrice(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'specifications' => json_encode([
                    'compare_price' => $attributes['base_price'] + $this->faker->numberBetween(100000, 500000),
                ]),
            ];
        });
    }

    /**
     * Indicate that the product is out of stock.
     */
    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'total_quantity' => 0,
            'status' => 'out_of_stock',
        ]);
    }
}
