<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HubsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();
        
        for ($i = 0; $i < 20; $i++) {
            try {
                DB::table('hubs')->insert([
                'hub_name' => $faker->city . " Hub",
                'hub_code' => $faker->unique()->bothify("HUB-###"),
                'address' => $faker->address,
                'ward_id' => $faker->numberBetween(1, 100),
                'latitude' => $faker->latitude,
                'longitude' => $faker->longitude,
                'capacity' => $faker->numberBetween(1000, 10000),
                'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Exception $e) {
                // Skip duplicate entries
                continue;
            }
        }
        
        $this->command->info('✅ Seeded records in hubs table');
    }
}
