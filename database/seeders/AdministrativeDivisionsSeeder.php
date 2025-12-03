<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdministrativeDivisionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();
        
        for ($i = 0; $i < 50; $i++) {
            try {
                DB::table('administrative_divisions')->insert([
                'country_id' => $faker->numberBetween(1, 100),
                'parent_id' => $faker->optional()->numberBetween(1, 100),
                'division_name' => $faker->city,
                'division_type' => $faker->randomElement(["province", "district", "ward"]),
                'code' => $faker->postcode,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Exception $e) {
                // Skip duplicate entries
                continue;
            }
        }
        
        $this->command->info('✅ Seeded records in administrative_divisions table');
    }
}
