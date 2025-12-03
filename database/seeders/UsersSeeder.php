<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();
        
        for ($i = 0; $i < 100; $i++) {
            try {
                DB::table('users')->insert([
                'email' => $faker->unique()->safeEmail,
                'phone_number' => $faker->phoneNumber,
                'password' => bcrypt("password"),
                'full_name' => $faker->name,
                'date_of_birth' => $faker->date("Y-m-d", "-18 years"),
                'gender' => $faker->randomElement(["male", "female", "other"]),
                'avatar_url' => $faker->imageUrl(200, 200, "people"),
                'bio' => $faker->paragraph,
                'email_verified_at' => $faker->dateTime,
                'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Exception $e) {
                // Skip duplicate entries
                continue;
            }
        }
        
        $this->command->info('✅ Seeded records in users table');
    }
}
