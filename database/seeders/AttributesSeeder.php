<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();
        
        for ($i = 0; $i < 10; $i++) {
            try {
                DB::table('attributes')->insert([
                'attribute_name' => $faker->randomElement(["Size", "Color", "Material", "Storage", "RAM"]),
                'display_name' => $faker->word,
                'input_type' => $faker->randomElement(["select", "color", "text"]),
                'is_required' => $faker->boolean(30),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Exception $e) {
                // Skip duplicate entries
                continue;
            }
        }
        
        $this->command->info('✅ Seeded records in attributes table');
    }
}
