<?php

namespace Database\Factories;

use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Country>
 */
class CountryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Country::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'country_name' => $this->faker->country(),
            'iso_code_2' => strtoupper($this->faker->countryCode()),
            'iso_code_3' => strtoupper($this->faker->randomElement([strtoupper($this->faker->lexify('???')), $this->faker->countryCode()])),
            'phone_code' => $this->faker->numberBetween(1, 999),
            'currency' => $this->faker->currencyCode(),
            'is_active' => true,
        ];
    }

    /**
     * State for Vietnam (Việt Nam) to ensure deterministic seeding.
     */
    public function vietnam()
    {
        return $this->state(function (array $attributes) {
            return [
                'country_name' => 'Việt Nam',
                'iso_code_2' => 'VN',
                'iso_code_3' => 'VNM',
                'phone_code' => 84,
                'currency' => 'VND',
                'is_active' => true,
            ];
        });
    }
}
