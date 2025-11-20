<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 2 specific admins
        $admin1 = new User([
            'name' => 'Admin 1',
            'username' => 'admin1',
            'email' => 'admin1@example.com',
            'phone_number' => '123-456-7890',
            'role' => Role::ADMIN->value,
            'password' => bcrypt('@12345Admin'),
            'email_verified_at' => now(),
        ]);
        $admin1->save();

        $admin2 = new User([
            'name' => 'Admin 2',
            'username' => 'admin2',
            'phone_number' => '123-456-7891',
            'email' => 'admin2@example.com',
            'role' => Role::ADMIN->value,
            'password' => bcrypt('@12345Admin'),
            'email_verified_at' => now(),
        ]);
        $admin2->save();

        // Create 10 sellers
        User::factory(10)->seller()->create();

        // Create 10 customers
        User::factory(10)->create();
    }
}
