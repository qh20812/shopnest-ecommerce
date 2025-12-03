<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WishlistsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();
        
        for ($i = 0; $i < 100; $i++) {
            try {
                DB::table('wishlists')->insert([
                'user_id' => $faker->numberBetween(1, 100),
                'name' => $faker->words(2, true),
                'is_public' => $faker->boolean(30),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Exception $e) {
                // Skip duplicate entries
                continue;
            }
        }
        
        $this->command->info('✅ Seeded records in wishlists table');
    }
}
