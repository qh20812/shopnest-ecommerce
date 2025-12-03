<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShopsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();
        
        for ($i = 0; $i < 50; $i++) {
            try {
                DB::table('shops')->insert([
                'owner_id' => $faker->numberBetween(1, 100),
                'shop_name' => $faker->company,
                'slug' => $faker->unique()->slug,
                'description' => $faker->paragraph,
                'logo_url' => $faker->imageUrl(200, 200, "business"),
                'banner_url' => $faker->imageUrl(1200, 400, "business"),
                'rating' => $faker->randomFloat(2, 3, 5),
                'total_products' => $faker->numberBetween(0, 1000),
                'total_followers' => $faker->numberBetween(0, 10000),
                'is_verified' => $faker->boolean(70),
                'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Exception $e) {
                // Skip duplicate entries
                continue;
            }
        }
        
        $this->command->info('✅ Seeded records in shops table');
    }
}
