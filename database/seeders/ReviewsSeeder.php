<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReviewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();
        
        for ($i = 0; $i < 500; $i++) {
            try {
                DB::table('reviews')->insert([
                'product_id' => $faker->numberBetween(1, 100),
                'user_id' => $faker->numberBetween(1, 100),
                'rating' => $faker->numberBetween(1, 5),
                'title' => $faker->sentence,
                'comment' => $faker->paragraph,
                'is_verified_purchase' => $faker->boolean(80),
                'helpful_count' => $faker->numberBetween(0, 100),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Exception $e) {
                // Skip duplicate entries
                continue;
            }
        }
        
        $this->command->info('✅ Seeded records in reviews table');
    }
}
