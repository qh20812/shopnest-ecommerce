<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserAddressesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();
        
        for ($i = 0; $i < 200; $i++) {
            try {
                DB::table('user_addresses')->insert([
                'user_id' => $faker->numberBetween(1, 100),
                'address_label' => $faker->randomElement(["Home", "Office", "Other"]),
                'recipient_name' => $faker->name,
                'phone_number' => $faker->phoneNumber,
                'address_line1' => $faker->streetAddress,
                'address_line2' => $faker->secondaryAddress,
                'country_id' => $faker->numberBetween(1, 100),
                'province_id' => $faker->numberBetween(1, 100),
                'district_id' => $faker->numberBetween(1, 100),
                'ward_id' => $faker->numberBetween(1, 100),
                'postal_code' => $faker->postcode,
                'latitude' => $faker->latitude,
                'longitude' => $faker->longitude,
                'is_default' => $faker->boolean(20),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Exception $e) {
                // Skip duplicate entries
                continue;
            }
        }
        
        $this->command->info('✅ Seeded records in user_addresses table');
    }
}
