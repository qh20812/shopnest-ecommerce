<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Role;
use App\Models\Shop;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        // Seed a default admin user
        $adminData = [
            'email' => 'shopnestAdmin@gmail.com',
            'phone_number' => '0123456789',
            'full_name' => 'ShopNest Admin',
            'password' => Hash::make('adminpassword'),
            'is_active' => true,
            'email_verified_at' => now(),
        ];

        // Seed a default seller user
        $sellerData = [
            'email' => 'shopnestSeller@gmail.com',
            'full_name' => 'ShopNest Seller',
            'phone_number' => '0987654321',
            'password' => Hash::make('sellerpassword'),
            'is_active' => true,
            'email_verified_at' => now(),
        ];
        
        // Create seeded admin
        try {
            $admin = User::firstOrCreate(
                ['email' => $adminData['email']],
                $adminData
            );

            $adminRole = Role::firstOrCreate(
                ['role_name' => 'admin'],
                ['description' => 'Administrator']
            );
            if (!$admin->roles()->where('role_id', $adminRole->id)->exists()) {
                $admin->roles()->attach($adminRole);
            }
        } catch (\Exception $e) {
            // ignore
        }

        // Create seeded seller
        try {
            $seller = User::firstOrCreate(
                ['email' => $sellerData['email']],
                $sellerData
            );

            $sellerRole = Role::firstOrCreate(
                ['role_name' => 'seller'],
                ['description' => 'Seller']
            );
            if (!$seller->roles()->where('role_id', $sellerRole->id)->exists()) {
                $seller->roles()->attach($sellerRole);
            }

            // Create a default shop for the seller if none exists
            if (!$seller->shops()->exists()) {
                Shop::create([
                    'owner_id' => $seller->id,
                    'shop_name' => 'ShopNest Seller Shop',
                    'slug' => 'shopnest-seller-' . uniqid(),
                    'is_active' => true,
                ]);
            }
        } catch (\Exception $e) {
            // ignore
        }

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
