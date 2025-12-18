<?php

namespace Database\Factories;

use App\Models\AdministrativeDivision;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserAddress>
 */
class UserAddressFactory extends Factory
{
    protected $model = UserAddress::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {        
        return [
            'user_id' => User::factory(),
            'address_label' => $this->faker->randomElement(['Home', 'Office', 'Other']),
            'recipient_name' => $this->faker->name(),
            'phone_number' => $this->faker->phoneNumber(),
            'address_line1' => $this->faker->streetAddress(),
            'address_line2' => $this->faker->optional()->secondaryAddress(),
            // These will be overridden in tests that need specific values
            // Don't set defaults that create new models - let tests control the relationships
            'postal_code' => $this->faker->postcode(),
            'latitude' => $this->faker->optional()->latitude(),
            'longitude' => $this->faker->optional()->longitude(),
            'is_default' => false,
        ];
    }

    /**
     * Indicate that the address is default.
     */
    public function default(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_default' => true,
        ]);
    }
}
