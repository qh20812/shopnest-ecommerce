<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributeValuesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();
        
        for ($i = 0; $i < 50; $i++) {
            try {
                DB::table('attribute_values')->insert([
                'attribute_id' => $faker->numberBetween(1, 100),
                'value' => $faker->word,
                'display_value' => $faker->word,
                'color_code' => $faker->hexColor,
                'display_order' => $faker->numberBetween(0, 50),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Exception $e) {
                // Skip duplicate entries
                continue;
            }
        }
        
        $this->command->info('✅ Seeded records in attribute_values table');
    }
}
