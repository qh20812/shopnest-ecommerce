<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductImagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();
        
        for ($i = 0; $i < 500; $i++) {
            try {
                DB::table('product_images')->insert([
                'product_id' => $faker->numberBetween(1, 100),
                'image_url' => $faker->imageUrl(800, 800, "products"),
                'thumbnail_url' => $faker->imageUrl(200, 200, "products"),
                'display_order' => $faker->numberBetween(0, 10),
                'is_primary' => $faker->boolean(20),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Exception $e) {
                // Skip duplicate entries
                continue;
            }
        }
        
        $this->command->info('✅ Seeded records in product_images table');
    }
}
