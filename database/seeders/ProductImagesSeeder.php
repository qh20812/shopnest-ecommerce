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
                'image_url' => "https://dummyimage.com/300x300/666266/ffffff",
                'thumbnail_url' => "https://dummyimage.com/100x100/666266/ffffff",
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
