<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();
        
        for ($i = 0; $i < 20; $i++) {
            try {
                DB::table('countries')->insert([
                'country_name' => $faker->country,
                'iso_code_2' => $faker->countryCode,
                'iso_code_3' => $faker->unique()->countryISOAlpha3,
                'phone_code' => $faker->numberBetween(1, 999),
                'currency' => $faker->currencyCode,
                'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Exception $e) {
                // Skip duplicate entries
                continue;
            }
        }
        
        $this->command->info('✅ Seeded records in countries table');
    }
}
