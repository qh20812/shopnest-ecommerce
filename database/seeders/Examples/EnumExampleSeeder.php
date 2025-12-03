<?php

namespace Database\Seeders\Examples;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\ProductStatus;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Example seeder demonstrating enum usage
 * 
 * This is a demonstration file showing how to use enums in seeders.
 * You can use these patterns in your real seeders.
 */
class EnumExampleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Example 1: Create products with different statuses
        $this->seedProducts();

        // Example 2: Create orders with various payment methods and statuses
        $this->seedOrders();
    }

    /**
     * Seed products with different enum statuses
     */
    protected function seedProducts(): void
    {
        $this->command->info('Seeding products with enum statuses...');

        $products = [
            [
                'product_name' => 'iPhone 15 Pro',
                'slug' => 'iphone-15-pro',
                'status' => ProductStatus::ACTIVE,
                'base_price' => 28990000,
            ],
            [
                'product_name' => 'Samsung Galaxy S24 (Coming Soon)',
                'slug' => 'samsung-galaxy-s24',
                'status' => ProductStatus::DRAFT,
                'base_price' => 22990000,
            ],
            [
                'product_name' => 'MacBook Pro M3 (Discontinued)',
                'slug' => 'macbook-pro-m3',
                'status' => ProductStatus::INACTIVE,
                'base_price' => 45990000,
            ],
            [
                'product_name' => 'AirPods Pro 2 (Out of Stock)',
                'slug' => 'airpods-pro-2',
                'status' => ProductStatus::OUT_OF_STOCK,
                'base_price' => 6490000,
            ],
        ];

        foreach ($products as $productData) {
            Product::create([
                'shop_id' => 1,
                'category_id' => 1,
                'brand_id' => 1,
                'product_name' => $productData['product_name'],
                'slug' => $productData['slug'],
                'status' => $productData['status'], // Using enum
                'base_price' => $productData['base_price'],
                'description' => 'Sample product description',
                'currency' => 'VND',
            ]);

            $this->command->line("   ✓ Created: {$productData['product_name']} ({$productData['status']->label()})");
        }
    }

    /**
     * Seed orders with different enum statuses and payment methods
     */
    protected function seedOrders(): void
    {
        $this->command->info('Seeding orders with enum statuses...');

        // Get a customer for orders
        $customer = User::where('email', 'customer@example.com')->first();
        
        if (!$customer) {
            $this->command->warn('   ⚠️  No customer found. Skipping order seeding.');
            return;
        }

        $orders = [
            [
                'order_number' => 'ORD-2024-001',
                'status' => OrderStatus::PENDING,
                'payment_status' => PaymentStatus::UNPAID,
                'payment_method' => PaymentMethod::COD,
                'total_amount' => 1500000,
            ],
            [
                'order_number' => 'ORD-2024-002',
                'status' => OrderStatus::CONFIRMED,
                'payment_status' => PaymentStatus::PAID,
                'payment_method' => PaymentMethod::E_WALLET,
                'total_amount' => 2800000,
            ],
            [
                'order_number' => 'ORD-2024-003',
                'status' => OrderStatus::SHIPPING,
                'payment_status' => PaymentStatus::PAID,
                'payment_method' => PaymentMethod::CREDIT_CARD,
                'total_amount' => 5200000,
            ],
            [
                'order_number' => 'ORD-2024-004',
                'status' => OrderStatus::DELIVERED,
                'payment_status' => PaymentStatus::PAID,
                'payment_method' => PaymentMethod::BANK_TRANSFER,
                'total_amount' => 3600000,
            ],
            [
                'order_number' => 'ORD-2024-005',
                'status' => OrderStatus::CANCELLED,
                'payment_status' => PaymentStatus::REFUNDED,
                'payment_method' => PaymentMethod::E_WALLET,
                'total_amount' => 1200000,
            ],
        ];

        foreach ($orders as $orderData) {
            Order::create([
                'order_number' => $orderData['order_number'],
                'customer_id' => $customer->id,
                'shop_id' => 1,
                'status' => $orderData['status'], // Using enum
                'payment_status' => $orderData['payment_status'], // Using enum
                'payment_method' => $orderData['payment_method'], // Using enum
                'subtotal' => $orderData['total_amount'],
                'total_amount' => $orderData['total_amount'],
            ]);

            $this->command->line(
                "   ✓ Created: {$orderData['order_number']} - " .
                "Status: {$orderData['status']->label()}, " .
                "Payment: {$orderData['payment_method']->label()}"
            );
        }
    }

    /**
     * Example: Generate random orders with enum statuses
     */
    protected function seedRandomOrders(int $count = 10): void
    {
        $this->command->info("Generating {$count} random orders...");

        $customers = User::limit(5)->get();
        
        if ($customers->isEmpty()) {
            $this->command->warn('   ⚠️  No customers found.');
            return;
        }

        for ($i = 1; $i <= $count; $i++) {
            // Random enum values
            $status = OrderStatus::cases()[array_rand(OrderStatus::cases())];
            $paymentMethod = PaymentMethod::cases()[array_rand(PaymentMethod::cases())];
            
            // Match payment status with order status
            $paymentStatus = match($status) {
                OrderStatus::PENDING => PaymentStatus::UNPAID,
                OrderStatus::CANCELLED => PaymentStatus::REFUNDED,
                default => PaymentStatus::PAID,
            };

            Order::create([
                'order_number' => 'ORD-' . now()->format('Ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'customer_id' => $customers->random()->id,
                'shop_id' => 1,
                'status' => $status,
                'payment_status' => $paymentStatus,
                'payment_method' => $paymentMethod,
                'subtotal' => rand(100000, 5000000),
                'total_amount' => rand(100000, 5000000),
            ]);
        }

        $this->command->info("   ✓ Created {$count} random orders");
    }

    /**
     * Example: Seed all possible enum combinations
     */
    protected function seedAllEnumCombinations(): void
    {
        $this->command->info('Creating orders with all enum combinations...');

        $customer = User::first();
        if (!$customer) {
            $this->command->warn('   ⚠️  No customer found.');
            return;
        }

        $counter = 0;
        
        // Create order for each status
        foreach (OrderStatus::cases() as $status) {
            // Create order for each payment method
            foreach (PaymentMethod::cases() as $paymentMethod) {
                $counter++;
                
                Order::create([
                    'order_number' => 'COMBO-' . str_pad($counter, 4, '0', STR_PAD_LEFT),
                    'customer_id' => $customer->id,
                    'shop_id' => 1,
                    'status' => $status,
                    'payment_status' => PaymentStatus::PAID,
                    'payment_method' => $paymentMethod,
                    'subtotal' => 1000000,
                    'total_amount' => 1000000,
                ]);
            }
        }

        $this->command->info("   ✓ Created {$counter} orders with all enum combinations");
    }
}
