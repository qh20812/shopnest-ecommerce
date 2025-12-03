<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();
        
        for ($i = 0; $i < 200; $i++) {
            try {
                DB::table('products')->insert([
                'shop_id' => $faker->numberBetween(1, 100),
                'category_id' => $faker->numberBetween(1, 100),
                'brand_id' => $faker->numberBetween(1, 100),
                'seller_id' => $faker->numberBetween(1, 100),
                'product_name' => $faker->words(4, true),
                'slug' => $faker->unique()->slug,
                'description' => $faker->paragraphs(3, true),
                'base_price' => $faker->randomFloat(2, 10, 10000),
                'status' => $faker->randomElement(["draft", "active", "inactive", "out_of_stock"]),
                'rating' => $faker->randomFloat(2, 0, 5),
                'review_count' => $faker->numberBetween(0, 500),
                'view_count' => $faker->numberBetween(0, 10000),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Exception $e) {
                // Skip duplicate entries
                continue;
            }
        }
        
        $this->command->info('✅ Seeded records in products table');
    }
}
