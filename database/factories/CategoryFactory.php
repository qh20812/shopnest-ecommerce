<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->randomElement([
            'Electronics',
            'Clothing',
            'Books',
            'Home & Garden',
            'Sports',
            'Toys',
            'Beauty',
            'Automotive',
            'Food',
            'Health',
        ]);

        return [
            'category_name' => $name,
            'slug' => \Str::slug($name),
            'description' => $this->faker->paragraph(),
            'parent_id' => null,
            'image_url' => $this->faker->imageUrl(400, 400, 'business'),
            'is_active' => true,
            'display_order' => $this->faker->numberBetween(1, 100),
        ];
    }

    /**
     * Indicate that the category is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
