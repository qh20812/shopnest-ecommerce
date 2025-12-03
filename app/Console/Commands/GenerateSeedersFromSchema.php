<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GenerateSeedersFromSchema extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:generate-seeders 
                            {--tables=* : Specific tables to generate seeders for}
                            {--count=10 : Number of records per table}
                            {--force : Overwrite existing seeders}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Laravel seeders from schema definition';

    /**
     * Schema definition
     */
    protected array $schema = [];

    /**
     * Number of records to generate per table
     */
    protected int $count = 10;

    /**
     * Seeders that need to run first (reference data)
     */
    protected array $priorityTables = [
        'countries',
        'administrative_divisions',
        'roles',
        'permissions',
        'brands',
        'categories',
        'attributes',
        'attribute_values',
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting seeder generation...');
        
        $this->count = (int) $this->option('count');
        $this->defineSchema();
        
        $specificTables = $this->option('tables');
        $tablesToGenerate = !empty($specificTables) ? $specificTables : array_keys($this->schema);
        
        $generated = 0;
        $skipped = 0;
        
        foreach ($tablesToGenerate as $tableName) {
            if (!isset($this->schema[$tableName])) {
                $this->warn("⚠️  Table '{$tableName}' not found in schema");
                continue;
            }
            
            $result = $this->generateSeeder($tableName, $this->schema[$tableName]);
            if ($result) {
                $generated++;
            } else {
                $skipped++;
            }
        }
        
        $this->newLine();
        $this->info("✅ Seeder generation completed!");
        $this->info("📊 Generated: {$generated} seeders");
        if ($skipped > 0) {
            $this->info("⏭️  Skipped: {$skipped} seeders (already exist)");
        }
        
        // Generate DatabaseSeeder
        if (empty($specificTables)) {
            $this->generateDatabaseSeeder();
        }
        
        $this->newLine();
        $this->info('💡 Run: php artisan db:seed');
    }

    /**
     * Define schema (copied from GenerateMigrationsFromSchema)
     */
    protected function defineSchema(): void
    {
        $this->schema = [
            'countries' => [
                'columns' => [
                    'id' => ['type' => 'id'],
                    'country_name' => ['type' => 'string', 'faker' => 'country'],
                    'iso_code_2' => ['type' => 'string', 'faker' => 'countryCode'],
                    'iso_code_3' => ['type' => 'string', 'faker' => 'unique()->countryISOAlpha3'],
                    'phone_code' => ['type' => 'string', 'faker' => 'numberBetween(1, 999)'],
                    'currency' => ['type' => 'string', 'faker' => 'currencyCode'],
                    'is_active' => ['type' => 'boolean', 'default' => true],
                ],
                'count' => 20,
            ],

            'administrative_divisions' => [
                'columns' => [
                    'id' => ['type' => 'id'],
                    'country_id' => ['type' => 'foreignId', 'references' => 'countries'],
                    'parent_id' => ['type' => 'foreignId', 'references' => 'administrative_divisions', 'nullable' => true],
                    'division_name' => ['type' => 'string', 'faker' => 'city'],
                    'division_type' => ['type' => 'enum', 'values' => ['province', 'ward']],
                    'code' => ['type' => 'string', 'faker' => 'postcode'],
                    'codename' => ['type' => 'string', 'faker' => 'slug'],
                    'short_codename' => ['type' => 'string', 'faker' => 'slug'],
                    'phone_code' => ['type' => 'string', 'faker' => 'optional()->numberBetween(200, 299)'],
                ],
                'count' => 50,
            ],

            'roles' => [
                'columns' => [
                    'id' => ['type' => 'id'],
                    'role_name' => ['type' => 'string', 'faker' => 'unique()->randomElement(["admin", "customer", "seller", "shipper", "moderator"])'],
                    'description' => ['type' => 'text', 'faker' => 'sentence'],
                ],
                'count' => 5,
            ],

            'permissions' => [
                'columns' => [
                    'id' => ['type' => 'id'],
                    'permission_name' => ['type' => 'string', 'faker' => 'unique()->word'],
                    'description' => ['type' => 'text', 'faker' => 'sentence'],
                ],
                'count' => 20,
            ],

            'brands' => [
                'columns' => [
                    'id' => ['type' => 'id'],
                    'brand_name' => ['type' => 'string', 'faker' => 'company'],
                    'slug' => ['type' => 'string', 'faker' => 'unique()->slug'],
                    'logo_url' => ['type' => 'string', 'faker' => 'imageUrl(200, 200, "brands")'],
                    'description' => ['type' => 'text', 'faker' => 'paragraph'],
                    'website' => ['type' => 'string', 'faker' => 'url'],
                    'is_active' => ['type' => 'boolean', 'default' => true],
                ],
                'count' => 30,
            ],

            'categories' => [
                'columns' => [
                    'id' => ['type' => 'id'],
                    'parent_id' => ['type' => 'foreignId', 'references' => 'categories', 'nullable' => true],
                    'category_name' => ['type' => 'string', 'faker' => 'words(3, true)'],
                    'slug' => ['type' => 'string', 'faker' => 'unique()->slug'],
                    'description' => ['type' => 'text', 'faker' => 'paragraph'],
                    'image_url' => ['type' => 'string', 'faker' => 'imageUrl(400, 400, "categories")'],
                    'display_order' => ['type' => 'integer', 'faker' => 'numberBetween(0, 100)'],
                    'is_active' => ['type' => 'boolean', 'default' => true],
                ],
                'count' => 40,
            ],

            'attributes' => [
                'columns' => [
                    'id' => ['type' => 'id'],
                    'attribute_name' => ['type' => 'string', 'faker' => 'randomElement(["Size", "Color", "Material", "Storage", "RAM"])'],
                    'display_name' => ['type' => 'string', 'faker' => 'word'],
                    'input_type' => ['type' => 'enum', 'faker' => 'randomElement(["select", "color", "text"])'],
                    'is_required' => ['type' => 'boolean', 'faker' => 'boolean(30)'],
                ],
                'count' => 10,
            ],

            'attribute_values' => [
                'columns' => [
                    'id' => ['type' => 'id'],
                    'attribute_id' => ['type' => 'foreignId', 'references' => 'attributes'],
                    'value' => ['type' => 'string', 'faker' => 'word'],
                    'display_value' => ['type' => 'string', 'faker' => 'word'],
                    'color_code' => ['type' => 'string', 'faker' => 'hexColor'],
                    'display_order' => ['type' => 'integer', 'faker' => 'numberBetween(0, 50)'],
                ],
                'count' => 50,
            ],

            'users' => [
                'columns' => [
                    'id' => ['type' => 'id'],
                    'email' => ['type' => 'string', 'faker' => 'unique()->safeEmail'],
                    'phone_number' => ['type' => 'string', 'faker' => 'phoneNumber'],
                    'password' => ['type' => 'string', 'faker' => 'bcrypt("password")'],
                    'full_name' => ['type' => 'string', 'faker' => 'name'],
                    'date_of_birth' => ['type' => 'date', 'faker' => 'date("Y-m-d", "-18 years")'],
                    'gender' => ['type' => 'enum', 'faker' => 'randomElement(["male", "female", "other"])'],
                    'avatar_url' => ['type' => 'string', 'faker' => 'imageUrl(200, 200, "people")'],
                    'bio' => ['type' => 'text', 'faker' => 'paragraph'],
                    'email_verified_at' => ['type' => 'timestamp', 'faker' => 'dateTime'],
                    'is_active' => ['type' => 'boolean', 'default' => true],
                ],
                'count' => 100,
            ],

            'user_addresses' => [
                'columns' => [
                    'id' => ['type' => 'id'],
                    'user_id' => ['type' => 'foreignId', 'references' => 'users'],
                    'address_label' => ['type' => 'string', 'faker' => 'randomElement(["Home", "Office", "Other"])'],
                    'recipient_name' => ['type' => 'string', 'faker' => 'name'],
                    'phone_number' => ['type' => 'string', 'faker' => 'phoneNumber'],
                    'address_line1' => ['type' => 'string', 'faker' => 'streetAddress'],
                    'address_line2' => ['type' => 'string', 'faker' => 'secondaryAddress'],
                    'country_id' => ['type' => 'foreignId', 'references' => 'countries'],
                    'province_id' => ['type' => 'foreignId', 'references' => 'administrative_divisions'],
                    'district_id' => ['type' => 'foreignId', 'references' => 'administrative_divisions'],
                    'ward_id' => ['type' => 'foreignId', 'references' => 'administrative_divisions'],
                    'postal_code' => ['type' => 'string', 'faker' => 'postcode'],
                    'latitude' => ['type' => 'decimal', 'faker' => 'latitude'],
                    'longitude' => ['type' => 'decimal', 'faker' => 'longitude'],
                    'is_default' => ['type' => 'boolean', 'faker' => 'boolean(20)'],
                ],
                'count' => 200,
            ],

            'shops' => [
                'columns' => [
                    'id' => ['type' => 'id'],
                    'owner_id' => ['type' => 'foreignId', 'references' => 'users'],
                    'shop_name' => ['type' => 'string', 'faker' => 'company'],
                    'slug' => ['type' => 'string', 'faker' => 'unique()->slug'],
                    'description' => ['type' => 'text', 'faker' => 'paragraph'],
                    'logo_url' => ['type' => 'string', 'faker' => 'imageUrl(200, 200, "business")'],
                    'banner_url' => ['type' => 'string', 'faker' => 'imageUrl(1200, 400, "business")'],
                    'rating' => ['type' => 'decimal', 'faker' => 'randomFloat(2, 3, 5)'],
                    'total_products' => ['type' => 'integer', 'faker' => 'numberBetween(0, 1000)'],
                    'total_followers' => ['type' => 'integer', 'faker' => 'numberBetween(0, 10000)'],
                    'is_verified' => ['type' => 'boolean', 'faker' => 'boolean(70)'],
                    'is_active' => ['type' => 'boolean', 'default' => true],
                ],
                'count' => 50,
            ],

            'hubs' => [
                'columns' => [
                    'id' => ['type' => 'id'],
                    'hub_name' => ['type' => 'string', 'faker' => 'city . " Hub"'],
                    'hub_code' => ['type' => 'string', 'faker' => 'unique()->bothify("HUB-###")'],
                    'address' => ['type' => 'string', 'faker' => 'address'],
                    'ward_id' => ['type' => 'foreignId', 'references' => 'administrative_divisions'],
                    'latitude' => ['type' => 'decimal', 'faker' => 'latitude'],
                    'longitude' => ['type' => 'decimal', 'faker' => 'longitude'],
                    'capacity' => ['type' => 'integer', 'faker' => 'numberBetween(1000, 10000)'],
                    'is_active' => ['type' => 'boolean', 'default' => true],
                ],
                'count' => 20,
            ],

            'products' => [
                'columns' => [
                    'id' => ['type' => 'id'],
                    'shop_id' => ['type' => 'foreignId', 'references' => 'shops'],
                    'category_id' => ['type' => 'foreignId', 'references' => 'categories'],
                    'brand_id' => ['type' => 'foreignId', 'references' => 'brands'],
                    'seller_id' => ['type' => 'foreignId', 'references' => 'users'],
                    'product_name' => ['type' => 'string', 'faker' => 'words(4, true)'],
                    'slug' => ['type' => 'string', 'faker' => 'unique()->slug'],
                    'description' => ['type' => 'text', 'faker' => 'paragraphs(3, true)'],
                    'base_price' => ['type' => 'decimal', 'faker' => 'randomFloat(2, 10, 10000)'],
                    'status' => ['type' => 'enum', 'faker' => 'randomElement(["draft", "active", "inactive", "out_of_stock"])'],
                    'rating' => ['type' => 'decimal', 'faker' => 'randomFloat(2, 0, 5)'],
                    'review_count' => ['type' => 'integer', 'faker' => 'numberBetween(0, 500)'],
                    'view_count' => ['type' => 'integer', 'faker' => 'numberBetween(0, 10000)'],
                ],
                'count' => 200,
            ],

            'product_images' => [
                'columns' => [
                    'id' => ['type' => 'id'],
                    'product_id' => ['type' => 'foreignId', 'references' => 'products'],
                    'image_url' => ['type' => 'string', 'faker' => 'imageUrl(800, 800, "products")'],
                    'thumbnail_url' => ['type' => 'string', 'faker' => 'imageUrl(200, 200, "products")'],
                    'display_order' => ['type' => 'integer', 'faker' => 'numberBetween(0, 10)'],
                    'is_primary' => ['type' => 'boolean', 'faker' => 'boolean(20)'],
                ],
                'count' => 500,
            ],

            'product_variants' => [
                'columns' => [
                    'id' => ['type' => 'id'],
                    'product_id' => ['type' => 'foreignId', 'references' => 'products'],
                    'sku' => ['type' => 'string', 'faker' => 'unique()->bothify("SKU-####-####")'],
                    'variant_name' => ['type' => 'string', 'faker' => 'words(2, true)'],
                    'price' => ['type' => 'decimal', 'faker' => 'randomFloat(2, 10, 10000)'],
                    'stock_quantity' => ['type' => 'integer', 'faker' => 'numberBetween(0, 1000)'],
                    'is_active' => ['type' => 'boolean', 'default' => true],
                ],
                'count' => 400,
            ],

            'orders' => [
                'columns' => [
                    'id' => ['type' => 'id'],
                    'order_number' => ['type' => 'string', 'faker' => 'unique()->bothify("ORD-########")'],
                    'customer_id' => ['type' => 'foreignId', 'references' => 'users'],
                    'shop_id' => ['type' => 'foreignId', 'references' => 'shops'],
                    'status' => ['type' => 'enum', 'faker' => 'randomElement(["pending", "confirmed", "processing", "shipping", "delivered", "cancelled"])'],
                    'payment_status' => ['type' => 'enum', 'faker' => 'randomElement(["unpaid", "paid", "refunded"])'],
                    'subtotal' => ['type' => 'decimal', 'faker' => 'randomFloat(2, 50, 5000)'],
                    'discount_amount' => ['type' => 'decimal', 'faker' => 'randomFloat(2, 0, 500)'],
                    'shipping_fee' => ['type' => 'decimal', 'faker' => 'randomFloat(2, 10, 50)'],
                    'total_amount' => ['type' => 'decimal', 'faker' => 'randomFloat(2, 50, 5000)'],
                    'shipping_address_id' => ['type' => 'foreignId', 'references' => 'user_addresses'],
                    'payment_method' => ['type' => 'enum', 'faker' => 'randomElement(["cod", "credit_card", "e_wallet", "bank_transfer"])'],
                ],
                'count' => 300,
            ],

            'order_items' => [
                'columns' => [
                    'id' => ['type' => 'id'],
                    'order_id' => ['type' => 'foreignId', 'references' => 'orders'],
                    'product_variant_id' => ['type' => 'foreignId', 'references' => 'product_variants'],
                    'product_name' => ['type' => 'string', 'faker' => 'words(3, true)'],
                    'variant_name' => ['type' => 'string', 'faker' => 'word'],
                    'sku' => ['type' => 'string', 'faker' => 'bothify("SKU-####")'],
                    'quantity' => ['type' => 'integer', 'faker' => 'numberBetween(1, 10)'],
                    'unit_price' => ['type' => 'decimal', 'faker' => 'randomFloat(2, 10, 1000)'],
                    'subtotal' => ['type' => 'decimal', 'faker' => 'randomFloat(2, 10, 1000)'],
                    'total_price' => ['type' => 'decimal', 'faker' => 'randomFloat(2, 10, 1000)'],
                ],
                'count' => 600,
            ],

            'cart_items' => [
                'columns' => [
                    'id' => ['type' => 'id'],
                    'user_id' => ['type' => 'foreignId', 'references' => 'users'],
                    'product_variant_id' => ['type' => 'foreignId', 'references' => 'product_variants'],
                    'quantity' => ['type' => 'integer', 'faker' => 'numberBetween(1, 5)'],
                ],
                'count' => 200,
            ],

            'reviews' => [
                'columns' => [
                    'id' => ['type' => 'id'],
                    'product_id' => ['type' => 'foreignId', 'references' => 'products'],
                    'user_id' => ['type' => 'foreignId', 'references' => 'users'],
                    'rating' => ['type' => 'tinyInteger', 'faker' => 'numberBetween(1, 5)'],
                    'title' => ['type' => 'string', 'faker' => 'sentence'],
                    'comment' => ['type' => 'text', 'faker' => 'paragraph'],
                    'is_verified_purchase' => ['type' => 'boolean', 'faker' => 'boolean(80)'],
                    'helpful_count' => ['type' => 'integer', 'faker' => 'numberBetween(0, 100)'],
                ],
                'count' => 500,
            ],

            'wishlists' => [
                'columns' => [
                    'id' => ['type' => 'id'],
                    'user_id' => ['type' => 'foreignId', 'references' => 'users'],
                    'name' => ['type' => 'string', 'faker' => 'words(2, true)'],
                    'is_public' => ['type' => 'boolean', 'faker' => 'boolean(30)'],
                ],
                'count' => 100,
            ],

            'wishlist_items' => [
                'columns' => [
                    'id' => ['type' => 'id'],
                    'wishlist_id' => ['type' => 'foreignId', 'references' => 'wishlists'],
                    'product_id' => ['type' => 'foreignId', 'references' => 'products'],
                ],
                'count' => 300,
            ],

            'notifications' => [
                'columns' => [
                    'id' => ['type' => 'id'],
                    'user_id' => ['type' => 'foreignId', 'references' => 'users'],
                    'type' => ['type' => 'string', 'faker' => 'randomElement(["order", "promotion", "system", "message"])'],
                    'title' => ['type' => 'string', 'faker' => 'sentence'],
                    'message' => ['type' => 'text', 'faker' => 'paragraph'],
                    'read_at' => ['type' => 'timestamp', 'faker' => 'optional()->dateTime'],
                ],
                'count' => 500,
            ],
        ];
    }

    /**
     * Generate seeder for a table
     */
    protected function generateSeeder(string $tableName, array $definition): bool
    {
        $className = Str::studly($tableName) . 'Seeder';
        $filePath = database_path("seeders/{$className}.php");
        
        // Check if file exists
        if (File::exists($filePath) && !$this->option('force')) {
            $this->warn("⏭️  Skipped: {$className} (already exists, use --force to overwrite)");
            return false;
        }
        
        $content = $this->generateSeederContent($tableName, $definition, $className);
        File::put($filePath, $content);
        
        $this->info("✅ Created: {$className}");
        return true;
    }

    /**
     * Generate seeder file content
     */
    protected function generateSeederContent(string $tableName, array $definition, string $className): string
    {
        $modelName = Str::studly(Str::singular($tableName));
        $count = $definition['count'] ?? $this->count;
        
        $factoryAttributes = $this->generateFactoryAttributes($definition['columns']);
        
        return <<<PHP
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class {$className} extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \$faker = \Faker\Factory::create();
        
        for (\$i = 0; \$i < {$count}; \$i++) {
            try {
                DB::table('{$tableName}')->insert([
{$factoryAttributes}
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Exception \$e) {
                // Skip duplicate entries
                continue;
            }
        }
        
        \$this->command->info('✅ Seeded records in {$tableName} table');
    }
}

PHP;
    }

    /**
     * Generate factory attributes
     */
    protected function generateFactoryAttributes(array $columns): string
    {
        $attributes = [];
        
        foreach ($columns as $name => $config) {
            // Skip auto-generated columns
            if (in_array($name, ['id', 'created_at', 'updated_at', 'deleted_at'])) {
                continue;
            }
            
            $faker = $config['faker'] ?? null;
            
            if ($faker) {
                // Handle special faker methods
                if (Str::startsWith($faker, 'bcrypt(')) {
                    $attributes[] = "                '{$name}' => {$faker},";
                } elseif (Str::contains($faker, '->')) {
                    $attributes[] = "                '{$name}' => \$faker->{$faker},";
                } else {
                    $attributes[] = "                '{$name}' => \$faker->{$faker},";
                }
            } elseif (isset($config['type'])) {
                // Auto-generate based on type
                $fakerMethod = $this->inferFakerMethod($name, $config);
                $attributes[] = "                '{$name}' => {$fakerMethod},";
            }
        }
        
        return implode("\n", $attributes);
    }

    /**
     * Infer faker method from column name and type
     */
    protected function inferFakerMethod(string $name, array $config): string
    {
        $type = $config['type'];
        
        // Foreign keys
        if ($type === 'foreignId' && isset($config['references'])) {
            $table = $config['references'];
            if (isset($config['nullable']) && $config['nullable']) {
                return "\$faker->optional()->numberBetween(1, 100)";
            }
            return "\$faker->numberBetween(1, 100)";
        }
        
        // Handle different types
        switch ($type) {
            case 'string':
                if (Str::contains($name, ['email'])) return "\$faker->safeEmail";
                if (Str::contains($name, ['phone'])) return "\$faker->phoneNumber";
                if (Str::contains($name, ['url', 'link'])) return "\$faker->url";
                if (Str::contains($name, ['slug'])) return "\$faker->slug";
                if (Str::contains($name, ['address'])) return "\$faker->address";
                if (Str::contains($name, ['name'])) return "\$faker->name";
                if (Str::contains($name, ['title'])) return "\$faker->sentence";
                if (Str::contains($name, ['code'])) return "\$faker->bothify('???-###')";
                return "\$faker->word";
                
            case 'text':
            case 'longtext':
                return "\$faker->paragraph";
                
            case 'integer':
            case 'bigint':
            case 'tinyInteger':
                return "\$faker->numberBetween(0, 100)";
                
            case 'decimal':
                return "\$faker->randomFloat(2, 0, 1000)";
                
            case 'boolean':
                if (isset($config['default'])) {
                    $default = $config['default'] ? 'true' : 'false';
                    return $default;
                }
                return "\$faker->boolean";
                
            case 'date':
                return "\$faker->date()";
                
            case 'dateTime':
            case 'timestamp':
                if (isset($config['nullable']) && $config['nullable']) {
                    return "\$faker->optional()->dateTime";
                }
                return "\$faker->dateTime";
                
            case 'enum':
                if (isset($config['values'])) {
                    $values = implode('", "', $config['values']);
                    return "\$faker->randomElement([\"{$values}\"])";
                }
                return "\$faker->word";
                
            case 'json':
                return "json_encode([\$faker->word => \$faker->sentence])";
                
            default:
                return "\$faker->word";
        }
    }

    /**
     * Generate DatabaseSeeder
     */
    protected function generateDatabaseSeeder(): void
    {
        $seederCalls = [];
        
        // Priority tables first
        foreach ($this->priorityTables as $table) {
            if (isset($this->schema[$table])) {
                $className = Str::studly($table) . 'Seeder';
                $seederCalls[] = "        \$this->call({$className}::class);";
            }
        }
        
        // Other tables
        foreach (array_keys($this->schema) as $table) {
            if (!in_array($table, $this->priorityTables)) {
                $className = Str::studly($table) . 'Seeder';
                $seederCalls[] = "        \$this->call({$className}::class);";
            }
        }
        
        $calls = implode("\n", $seederCalls);
        
        $content = <<<PHP
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
        \$this->command->info('🌱 Starting database seeding...');
        
{$calls}
        
        \$this->command->info('✅ Database seeding completed!');
    }
}

PHP;
        
        File::put(database_path('seeders/DatabaseSeeder.php'), $content);
        $this->info("✅ Updated: DatabaseSeeder.php");
    }
}
