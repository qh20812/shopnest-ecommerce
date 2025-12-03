<?php

namespace Database\Factories;

use App\Models\Banner;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BannerFactory extends Factory
{
    protected $model = Banner::class;

    public function definition(): array
    {
        $placementOptions = ['hero', 'promo', 'sidebar'];
        $start = $this->faker->optional(0.6)->dateTimeBetween('-1 month', '+1 week');
        $end = $start ? $this->faker->dateTimeBetween($start, '+3 months') : null;

        return [
            'title' => $this->faker->sentence(3),
            'subtitle' => $this->faker->optional()->sentence(6),
            'link' => $this->faker->optional()->url(),
            'image_url' => $this->faker->imageUrl(1200, 600, 'business', true, 'banner'),
            'alt_text' => $this->faker->optional()->sentence(4),
            'placement' => $this->faker->randomElement($placementOptions),
            'start_time' => $start,
            'end_time' => $end,
            'is_active' => $this->faker->boolean(80),
            'display_order' => $this->faker->numberBetween(0, 100),
        ];
    }
}
