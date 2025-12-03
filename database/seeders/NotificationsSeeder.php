<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();
        
        for ($i = 0; $i < 500; $i++) {
            try {
                DB::table('notifications')->insert([
                'user_id' => $faker->numberBetween(1, 100),
                'type' => $faker->randomElement(["order", "promotion", "system", "message"]),
                'title' => $faker->sentence,
                'message' => $faker->paragraph,
                'read_at' => $faker->optional()->dateTime,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Exception $e) {
                // Skip duplicate entries
                continue;
            }
        }
        
        $this->command->info('✅ Seeded records in notifications table');
    }
}
