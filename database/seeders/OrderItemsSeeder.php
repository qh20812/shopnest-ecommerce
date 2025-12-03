<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();
        
        for ($i = 0; $i < 600; $i++) {
            try {
                DB::table('order_items')->insert([
                'order_id' => $faker->numberBetween(1, 100),
                'product_variant_id' => $faker->numberBetween(1, 100),
                'product_name' => $faker->words(3, true),
                'variant_name' => $faker->word,
                'sku' => $faker->bothify("SKU-####"),
                'quantity' => $faker->numberBetween(1, 10),
                'unit_price' => $faker->randomFloat(2, 10, 1000),
                'subtotal' => $faker->randomFloat(2, 10, 1000),
                'total_price' => $faker->randomFloat(2, 10, 1000),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Exception $e) {
                // Skip duplicate entries
                continue;
            }
        }
        
        $this->command->info('✅ Seeded records in order_items table');
    }
}
