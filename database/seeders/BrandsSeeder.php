<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BrandsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();
        
        for ($i = 0; $i < 30; $i++) {
            try {
                DB::table('brands')->insert([
                'brand_name' => $faker->company,
                'slug' => $faker->unique()->slug,
                'logo_url' => $faker->imageUrl(200, 200, "brands"),
                'description' => $faker->paragraph,
                'website' => $faker->url,
                'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Exception $e) {
                // Skip duplicate entries
                continue;
            }
        }
        
        $this->command->info('✅ Seeded records in brands table');
    }
}
