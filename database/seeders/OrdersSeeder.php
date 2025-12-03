<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrdersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();
        
        for ($i = 0; $i < 300; $i++) {
            try {
                DB::table('orders')->insert([
                'order_number' => $faker->unique()->bothify("ORD-########"),
                'customer_id' => $faker->numberBetween(1, 100),
                'shop_id' => $faker->numberBetween(1, 100),
                'status' => $faker->randomElement(["pending", "confirmed", "processing", "shipping", "delivered", "cancelled"]),
                'payment_status' => $faker->randomElement(["unpaid", "paid", "refunded"]),
                'subtotal' => $faker->randomFloat(2, 50, 5000),
                'discount_amount' => $faker->randomFloat(2, 0, 500),
                'shipping_fee' => $faker->randomFloat(2, 10, 50),
                'total_amount' => $faker->randomFloat(2, 50, 5000),
                'shipping_address_id' => $faker->numberBetween(1, 100),
                'payment_method' => $faker->randomElement(["cod", "credit_card", "e_wallet", "bank_transfer"]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Exception $e) {
                // Skip duplicate entries
                continue;
            }
        }
        
        $this->command->info('✅ Seeded records in orders table');
    }
}
