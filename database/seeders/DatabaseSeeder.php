<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting database seeding...');
        
        // Priority: Reference data from Vietnam provinces.json
        $this->call(CountriesSeeder::class);
        $this->call(AdministrativeDivisionSeeder::class); // Real Vietnam data
        $this->call(RolesSeeder::class);
        $this->call(PermissionsSeeder::class);
        $this->call(BrandsSeeder::class);
        $this->call(CategoriesSeeder::class);
        $this->call(AttributesSeeder::class);
        $this->call(AttributeValuesSeeder::class);
        $this->call(UsersSeeder::class);
        $this->call(UserAddressesSeeder::class);
        $this->call(ShopsSeeder::class);
        $this->call(HubsSeeder::class);
        $this->call(ProductsSeeder::class);
        $this->call(ProductImagesSeeder::class);
        $this->call(ProductVariantsSeeder::class);
        $this->call(OrdersSeeder::class);
        $this->call(OrderItemsSeeder::class);
        $this->call(CartItemsSeeder::class);
        $this->call(ReviewsSeeder::class);
        $this->call(WishlistsSeeder::class);
        $this->call(WishlistItemsSeeder::class);
        $this->call(NotificationsSeeder::class);
        
        $this->command->info('✅ Database seeding completed!');
    }
}
