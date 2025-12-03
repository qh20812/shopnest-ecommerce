<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();
        
        for ($i = 0; $i < 5; $i++) {
            try {
                DB::table('roles')->insert([
                'role_name' => $faker->unique()->randomElement(["admin", "customer", "seller", "shipper", "moderator"]),
                'description' => $faker->sentence,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Exception $e) {
                // Skip duplicate entries
                continue;
            }
        }
        
        $this->command->info('✅ Seeded records in roles table');
    }
}
