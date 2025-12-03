<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductVariantsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();
        
        for ($i = 0; $i < 400; $i++) {
            try {
                DB::table('product_variants')->insert([
                'product_id' => $faker->numberBetween(1, 100),
                'sku' => $faker->unique()->bothify("SKU-####-####"),
                'variant_name' => $faker->words(2, true),
                'price' => $faker->randomFloat(2, 10, 10000),
                'stock_quantity' => $faker->numberBetween(0, 1000),
                'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Exception $e) {
                // Skip duplicate entries
                continue;
            }
        }
        
        $this->command->info('✅ Seeded records in product_variants table');
    }
}
