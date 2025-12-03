<?php

namespace Database\Factories;

use App\Models\AdministrativeDivision;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AdministrativeDivision>
 */
class AdministrativeDivisionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AdministrativeDivision::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $divisionType = $this->faker->randomElement(['province', 'ward']);
        
        return [
            'country_id' => 1, // Default to first country (Vietnam)
            'parent_id' => null,
            'division_name' => $this->faker->city(),
            'division_type' => $divisionType,
            'code' => $this->faker->unique()->numerify('####'),
            'codename' => $this->faker->slug(2),
            'short_codename' => $this->faker->slug(1),
            'phone_code' => $divisionType === 'province' ? $this->faker->numberBetween(200, 299) : null,
        ];
    }

    /**
     * Indicate that the division is a province.
     */
    public function province(): static
    {
        return $this->state(fn (array $attributes) => [
            'division_type' => 'province',
            'parent_id' => null,
            'phone_code' => $this->faker->numberBetween(200, 299),
        ]);
    }

    /**
     * Indicate that the division is a ward.
     */
    public function ward(): static
    {
        return $this->state(fn (array $attributes) => [
            'division_type' => 'ward',
            'parent_id' => $this->faker->numberBetween(1, 63), // Assume 63 provinces
            'phone_code' => null,
        ]);
    }
}
