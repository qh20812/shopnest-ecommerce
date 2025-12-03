<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();
        
        for ($i = 0; $i < 40; $i++) {
            try {
                DB::table('categories')->insert([
                'parent_id' => $faker->optional()->numberBetween(1, 100),
                'category_name' => $faker->words(3, true),
                'slug' => $faker->unique()->slug,
                'description' => $faker->paragraph,
                'image_url' => $faker->imageUrl(400, 400, "categories"),
                'display_order' => $faker->numberBetween(0, 100),
                'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Exception $e) {
                // Skip duplicate entries
                continue;
            }
        }
        
        $this->command->info('✅ Seeded records in categories table');
    }
}
