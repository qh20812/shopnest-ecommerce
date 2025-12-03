<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GenerateMigrationsFromSchema extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:generate-migrations 
                            {--schema= : Path to DBML schema file}
                            {--start=14 : Starting migration number}
                            {--date=2024_01_01 : Base date for migrations}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Laravel migrations from DBML schema (1 table = 1 file)';

    /**
     * Schema definition parsed from DBML
     */
    protected array $schema = [];
    
    /**
     * Counter for migration numbering
     */
    protected int $counter = 14;

    /**
     * Base date for migrations
     */
    protected string $baseDate = '2024_01_01';

    /**
     * Type mappings from DBML to Laravel
     */
    protected array $typeMap = [
        'varchar' => 'string',
        'text' => 'text',
        'longtext' => 'longText',
        'int' => 'integer',
        'bigint' => 'bigInteger',
        'unsignedBigInteger' => 'unsignedBigInteger',
        'tinyint' => 'tinyInteger',
        'smallint' => 'smallInteger',
        'decimal' => 'decimal',
        'boolean' => 'boolean',
        'datetime' => 'dateTime',
        'timestamp' => 'timestamp',
        'date' => 'date',
        'json' => 'json',
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting migration generation...');
        
        $this->counter = (int) $this->option('start');
        $this->baseDate = $this->option('date');
        
        // Define schema inline or load from file
        $schemaPath = $this->option('schema');
        if ($schemaPath && File::exists($schemaPath)) {
            $this->parseSchemaFromFile($schemaPath);
        } else {
            $this->defineSchemaManually();
        }
        
        // Generate migrations for each table
        $this->generateMigrations();
        
        $this->info('✅ Migration generation completed!');
        $this->info("📁 Generated migrations in: database/migrations/");
        $this->newLine();
        $this->info('Run: php artisan migrate');
    }

    /**
     * Define schema manually (hardcoded from DBML)
     */
    protected function defineSchemaManually(): void
    {
        $this->schema = [
            // Level 0: Reference Data
            'countries' => [
                'columns' => [
                    ['name' => 'id', 'type' => 'id'],
                    ['name' => 'country_name', 'type' => 'string', 'length' => 100],
                    ['name' => 'iso_code_2', 'type' => 'string', 'length' => 2, 'unique' => true],
                    ['name' => 'iso_code_3', 'type' => 'string', 'length' => 3, 'unique' => true],
                    ['name' => 'phone_code', 'type' => 'string', 'length' => 10, 'nullable' => true],
                    ['name' => 'currency', 'type' => 'string', 'length' => 3, 'nullable' => true],
                    ['name' => 'is_active', 'type' => 'boolean', 'default' => true],
                    ['name' => 'created_at', 'type' => 'timestamp', 'nullable' => true],
                    ['name' => 'updated_at', 'type' => 'timestamp', 'nullable' => true],
                ],
                'indexes' => [
                    ['columns' => ['iso_code_2']],
                ],
                'comment' => 'Country master data with ISO codes',
            ],

            'administrative_divisions' => [
                'columns' => [
                    ['name' => 'id', 'type' => 'id'],
                    ['name' => 'country_id', 'type' => 'foreignId', 'references' => 'countries'],
                    ['name' => 'parent_id', 'type' => 'foreignId', 'references' => 'administrative_divisions', 'nullable' => true],
                    ['name' => 'division_name', 'type' => 'string', 'length' => 100],
                    ['name' => 'division_type', 'type' => 'enum', 'values' => ['province', 'ward']],
                    ['name' => 'code', 'type' => 'string', 'length' => 20, 'nullable' => true],
                    ['name' => 'codename', 'type' => 'string', 'length' => 100, 'nullable' => true],
                    ['name' => 'short_codename', 'type' => 'string', 'length' => 100, 'nullable' => true],
                    ['name' => 'phone_code', 'type' => 'string', 'length' => 10, 'nullable' => true],
                    ['name' => 'created_at', 'type' => 'timestamp', 'nullable' => true],
                    ['name' => 'updated_at', 'type' => 'timestamp', 'nullable' => true],
                ],
                'indexes' => [
                    ['columns' => ['country_id']],
                    ['columns' => ['parent_id']],
                    ['columns' => ['division_type']],
                    ['columns' => ['code']],
                    ['columns' => ['codename']],
                ],
                'comment' => 'Hierarchical administrative divisions (provinces/wards - Vietnam structure)',
            ],

            'roles' => [
                'columns' => [
                    ['name' => 'id', 'type' => 'id'],
                    ['name' => 'role_name', 'type' => 'string', 'length' => 50, 'unique' => true],
                    ['name' => 'description', 'type' => 'text', 'nullable' => true],
                    ['name' => 'created_at', 'type' => 'timestamp', 'nullable' => true],
                    ['name' => 'updated_at', 'type' => 'timestamp', 'nullable' => true],
                ],
                'indexes' => [
                    ['columns' => ['role_name']],
                ],
                'comment' => 'User roles (admin, customer, seller, shipper)',
            ],

            'permissions' => [
                'columns' => [
                    ['name' => 'id', 'type' => 'id'],
                    ['name' => 'permission_name', 'type' => 'string', 'length' => 100, 'unique' => true],
                    ['name' => 'description', 'type' => 'text', 'nullable' => true],
                    ['name' => 'created_at', 'type' => 'timestamp', 'nullable' => true],
                    ['name' => 'updated_at', 'type' => 'timestamp', 'nullable' => true],
                ],
                'indexes' => [
                    ['columns' => ['permission_name']],
                ],
                'comment' => 'System permissions for RBAC',
            ],

            'brands' => [
                'columns' => [
                    ['name' => 'id', 'type' => 'id'],
                    ['name' => 'brand_name', 'type' => 'string', 'length' => 100],
                    ['name' => 'slug', 'type' => 'string', 'unique' => true],
                    ['name' => 'logo_url', 'type' => 'string', 'nullable' => true],
                    ['name' => 'description', 'type' => 'text', 'nullable' => true],
                    ['name' => 'website', 'type' => 'string', 'nullable' => true],
                    ['name' => 'is_active', 'type' => 'boolean', 'default' => true],
                    ['name' => 'created_at', 'type' => 'timestamp', 'nullable' => true],
                    ['name' => 'updated_at', 'type' => 'timestamp', 'nullable' => true],
                    ['name' => 'deleted_at', 'type' => 'timestamp', 'nullable' => true],
                ],
                'indexes' => [
                    ['columns' => ['slug']],
                ],
                'comment' => 'Product brands',
            ],

            'categories' => [
                'columns' => [
                    ['name' => 'id', 'type' => 'id'],
                    ['name' => 'parent_id', 'type' => 'foreignId', 'references' => 'categories', 'nullable' => true],
                    ['name' => 'category_name', 'type' => 'string', 'length' => 100],
                    ['name' => 'slug', 'type' => 'string', 'unique' => true],
                    ['name' => 'description', 'type' => 'text', 'nullable' => true],
                    ['name' => 'image_url', 'type' => 'string', 'nullable' => true],
                    ['name' => 'display_order', 'type' => 'integer', 'default' => 0],
                    ['name' => 'is_active', 'type' => 'boolean', 'default' => true],
                    ['name' => 'created_at', 'type' => 'timestamp', 'nullable' => true],
                    ['name' => 'updated_at', 'type' => 'timestamp', 'nullable' => true],
                    ['name' => 'deleted_at', 'type' => 'timestamp', 'nullable' => true],
                ],
                'indexes' => [
                    ['columns' => ['parent_id']],
                    ['columns' => ['slug']],
                ],
                'comment' => 'Product categories with hierarchy support',
            ],

            'attributes' => [
                'columns' => [
                    ['name' => 'id', 'type' => 'id'],
                    ['name' => 'attribute_name', 'type' => 'string', 'length' => 50],
                    ['name' => 'display_name', 'type' => 'string', 'length' => 100],
                    ['name' => 'input_type', 'type' => 'enum', 'values' => ['select', 'color', 'text']],
                    ['name' => 'is_required', 'type' => 'boolean', 'default' => false],
                    ['name' => 'created_at', 'type' => 'timestamp', 'nullable' => true],
                    ['name' => 'updated_at', 'type' => 'timestamp', 'nullable' => true],
                ],
                'comment' => 'Product attribute types (Size, Color, Storage)',
            ],

            'attribute_values' => [
                'columns' => [
                    ['name' => 'id', 'type' => 'id'],
                    ['name' => 'attribute_id', 'type' => 'foreignId', 'references' => 'attributes', 'onDelete' => 'cascade'],
                    ['name' => 'value', 'type' => 'string', 'length' => 100],
                    ['name' => 'display_value', 'type' => 'string', 'length' => 100],
                    ['name' => 'color_code', 'type' => 'string', 'length' => 7, 'nullable' => true],
                    ['name' => 'display_order', 'type' => 'integer', 'default' => 0],
                    ['name' => 'created_at', 'type' => 'timestamp', 'nullable' => true],
                    ['name' => 'updated_at', 'type' => 'timestamp', 'nullable' => true],
                ],
                'indexes' => [
                    ['columns' => ['attribute_id']],
                ],
                'comment' => 'Attribute values (Red, Blue, Large, Small, etc.)',
            ],

            // Level 2: Users & Auth
            'users' => [
                'columns' => [
                    ['name' => 'id', 'type' => 'id', 'primary' => true],
                    ['name' => 'email', 'type' => 'string', 'unique' => true],
                    ['name' => 'phone_number', 'type' => 'string', 'length' => 20, 'nullable' => true],
                    ['name' => 'password', 'type' => 'string'],
                    ['name' => 'full_name', 'type' => 'string', 'length' => 100],
                    ['name' => 'date_of_birth', 'type' => 'date', 'nullable' => true],
                    ['name' => 'gender', 'type' => 'enum', 'values' => ['male', 'female', 'other'], 'nullable' => true],
                    ['name' => 'avatar_url', 'type' => 'string', 'nullable' => true],
                    ['name' => 'bio', 'type' => 'text', 'nullable' => true],
                    ['name' => 'default_address_id', 'type' => 'unsignedBigInteger', 'nullable' => true], // FK added in separate migration
                    ['name' => 'email_verified_at', 'type' => 'timestamp', 'nullable' => true],
                    ['name' => 'phone_verified_at', 'type' => 'timestamp', 'nullable' => true],
                    ['name' => 'two_factor_secret', 'type' => 'text', 'nullable' => true],
                    ['name' => 'two_factor_recovery_codes', 'type' => 'text', 'nullable' => true],
                    ['name' => 'two_factor_confirmed_at', 'type' => 'timestamp', 'nullable' => true],
                    ['name' => 'remember_token', 'type' => 'rememberToken'],
                    ['name' => 'last_login_at', 'type' => 'timestamp', 'nullable' => true],
                    ['name' => 'is_active', 'type' => 'boolean', 'default' => true],
                    ['name' => 'created_at', 'type' => 'timestamp', 'nullable' => true],
                    ['name' => 'updated_at', 'type' => 'timestamp', 'nullable' => true],
                ],
                'indexes' => [
                    ['columns' => ['email']],
                    ['columns' => ['phone_number']],
                ],
                'comment' => 'User accounts with authentication and profile information',
            ],
            
            'user_addresses' => [
                'columns' => [
                    ['name' => 'id', 'type' => 'id', 'primary' => true],
                    ['name' => 'user_id', 'type' => 'foreignId', 'references' => 'users', 'onDelete' => 'cascade'],
                    ['name' => 'address_label', 'type' => 'string', 'length' => 50],
                    ['name' => 'recipient_name', 'type' => 'string', 'length' => 100],
                    ['name' => 'phone_number', 'type' => 'string', 'length' => 20],
                    ['name' => 'address_line1', 'type' => 'string'],
                    ['name' => 'address_line2', 'type' => 'string', 'nullable' => true],
                    ['name' => 'country_id', 'type' => 'foreignId', 'references' => 'countries'],
                    ['name' => 'province_id', 'type' => 'foreignId', 'references' => 'administrative_divisions'],
                    ['name' => 'district_id', 'type' => 'foreignId', 'references' => 'administrative_divisions'],
                    ['name' => 'ward_id', 'type' => 'foreignId', 'references' => 'administrative_divisions'],
                    ['name' => 'postal_code', 'type' => 'string', 'length' => 20, 'nullable' => true],
                    ['name' => 'latitude', 'type' => 'decimal', 'precision' => [10, 8], 'nullable' => true],
                    ['name' => 'longitude', 'type' => 'decimal', 'precision' => [11, 8], 'nullable' => true],
                    ['name' => 'is_default', 'type' => 'boolean', 'default' => false],
                    ['name' => 'created_at', 'type' => 'timestamp', 'nullable' => true],
                    ['name' => 'updated_at', 'type' => 'timestamp', 'nullable' => true],
                ],
                'indexes' => [
                    ['columns' => ['user_id']],
                    ['columns' => ['country_id', 'province_id']],
                ],
                'comment' => 'User shipping addresses with full geographic information',
            ],

            'role_user' => [
                'columns' => [
                    ['name' => 'user_id', 'type' => 'foreignId', 'references' => 'users', 'onDelete' => 'cascade'],
                    ['name' => 'role_id', 'type' => 'foreignId', 'references' => 'roles', 'onDelete' => 'cascade'],
                    ['name' => 'created_at', 'type' => 'timestamp', 'nullable' => true],
                ],
                'primary' => ['user_id', 'role_id'],
                'indexes' => [
                    ['columns' => ['role_id']],
                ],
                'comment' => 'Pivot table for user-role many-to-many relationship',
            ],

            'permission_role' => [
                'columns' => [
                    ['name' => 'role_id', 'type' => 'foreignId', 'references' => 'roles', 'onDelete' => 'cascade'],
                    ['name' => 'permission_id', 'type' => 'foreignId', 'references' => 'permissions', 'onDelete' => 'cascade'],
                    ['name' => 'created_at', 'type' => 'timestamp', 'nullable' => true],
                ],
                'primary' => ['role_id', 'permission_id'],
                'indexes' => [
                    ['columns' => ['permission_id']],
                ],
                'comment' => 'Pivot table for role-permission many-to-many relationship',
            ],

            // Level 3: Shops & Hubs
            'shops' => [
                'columns' => [
                    ['name' => 'id', 'type' => 'id', 'primary' => true],
                    ['name' => 'owner_id', 'type' => 'foreignId', 'references' => 'users', 'onDelete' => 'cascade'],
                    ['name' => 'shop_name', 'type' => 'string', 'length' => 100],
                    ['name' => 'slug', 'type' => 'string', 'unique' => true],
                    ['name' => 'description', 'type' => 'text', 'nullable' => true],
                    ['name' => 'logo_url', 'type' => 'string', 'nullable' => true],
                    ['name' => 'banner_url', 'type' => 'string', 'nullable' => true],
                    ['name' => 'rating', 'type' => 'decimal', 'precision' => [3, 2], 'default' => 0],
                    ['name' => 'total_products', 'type' => 'integer', 'default' => 0],
                    ['name' => 'total_followers', 'type' => 'integer', 'default' => 0],
                    ['name' => 'total_orders', 'type' => 'integer', 'default' => 0],
                    ['name' => 'response_rate', 'type' => 'decimal', 'precision' => [5, 2], 'nullable' => true],
                    ['name' => 'response_time_hours', 'type' => 'integer', 'nullable' => true],
                    ['name' => 'is_verified', 'type' => 'boolean', 'default' => false],
                    ['name' => 'is_active', 'type' => 'boolean', 'default' => true],
                    ['name' => 'created_at', 'type' => 'timestamp', 'nullable' => true],
                    ['name' => 'updated_at', 'type' => 'timestamp', 'nullable' => true],
                ],
                'indexes' => [
                    ['columns' => ['owner_id']],
                    ['columns' => ['slug']],
                    ['columns' => ['is_verified', 'is_active']],
                ],
                'comment' => 'Seller shops with metrics and verification status',
            ],

            'hubs' => [
                'columns' => [
                    ['name' => 'id', 'type' => 'id', 'primary' => true],
                    ['name' => 'hub_name', 'type' => 'string', 'length' => 100],
                    ['name' => 'hub_code', 'type' => 'string', 'length' => 20, 'unique' => true],
                    ['name' => 'address', 'type' => 'string'],
                    ['name' => 'ward_id', 'type' => 'foreignId', 'references' => 'administrative_divisions'],
                    ['name' => 'latitude', 'type' => 'decimal', 'precision' => [10, 8]],
                    ['name' => 'longitude', 'type' => 'decimal', 'precision' => [11, 8]],
                    ['name' => 'capacity', 'type' => 'integer'],
                    ['name' => 'is_active', 'type' => 'boolean', 'default' => true],
                    ['name' => 'created_at', 'type' => 'timestamp', 'nullable' => true],
                    ['name' => 'updated_at', 'type' => 'timestamp', 'nullable' => true],
                ],
                'indexes' => [
                    ['columns' => ['hub_code']],
                    ['columns' => ['ward_id']],
                ],
                'comment' => 'Distribution hubs for logistics and shipping',
            ],

            // Level 4: Products
            'products' => [
                'columns' => [
                    ['name' => 'id', 'type' => 'id', 'primary' => true],
                    ['name' => 'shop_id', 'type' => 'foreignId', 'references' => 'shops', 'onDelete' => 'cascade'],
                    ['name' => 'category_id', 'type' => 'foreignId', 'references' => 'categories'],
                    ['name' => 'brand_id', 'type' => 'foreignId', 'references' => 'brands', 'nullable' => true],
                    ['name' => 'seller_id', 'type' => 'foreignId', 'references' => 'users'],
                    ['name' => 'product_name', 'type' => 'string'],
                    ['name' => 'slug', 'type' => 'string', 'unique' => true],
                    ['name' => 'description', 'type' => 'text', 'nullable' => true],
                    ['name' => 'specifications', 'type' => 'json', 'nullable' => true],
                    ['name' => 'base_price', 'type' => 'decimal', 'precision' => [15, 2]],
                    ['name' => 'currency', 'type' => 'string', 'length' => 3, 'default' => 'VND'],
                    ['name' => 'weight_grams', 'type' => 'integer', 'nullable' => true],
                    ['name' => 'length_cm', 'type' => 'integer', 'nullable' => true],
                    ['name' => 'width_cm', 'type' => 'integer', 'nullable' => true],
                    ['name' => 'height_cm', 'type' => 'integer', 'nullable' => true],
                    ['name' => 'status', 'type' => 'enum', 'values' => ['draft', 'active', 'inactive', 'out_of_stock'], 'default' => 'draft'],
                    ['name' => 'total_quantity', 'type' => 'integer', 'default' => 0],
                    ['name' => 'total_sold', 'type' => 'integer', 'default' => 0],
                    ['name' => 'rating', 'type' => 'decimal', 'precision' => [3, 2], 'default' => 0],
                    ['name' => 'review_count', 'type' => 'integer', 'default' => 0],
                    ['name' => 'view_count', 'type' => 'integer', 'default' => 0],
                    ['name' => 'created_at', 'type' => 'timestamp', 'nullable' => true],
                    ['name' => 'updated_at', 'type' => 'timestamp', 'nullable' => true],
                    ['name' => 'deleted_at', 'type' => 'timestamp', 'nullable' => true],
                ],
                'indexes' => [
                    ['columns' => ['shop_id']],
                    ['columns' => ['category_id']],
                    ['columns' => ['brand_id']],
                    ['columns' => ['seller_id']],
                    ['columns' => ['slug']],
                    ['columns' => ['status']],
                    ['columns' => ['rating', 'review_count']],
                ],
                'comment' => 'Product master table with variants support',
            ],

            'product_images' => [
                'columns' => [
                    ['name' => 'id', 'type' => 'id', 'primary' => true],
                    ['name' => 'product_id', 'type' => 'foreignId', 'references' => 'products', 'onDelete' => 'cascade'],
                    ['name' => 'image_url', 'type' => 'string'],
                    ['name' => 'thumbnail_url', 'type' => 'string', 'nullable' => true],
                    ['name' => 'alt_text', 'type' => 'string', 'nullable' => true],
                    ['name' => 'display_order', 'type' => 'integer', 'default' => 0],
                    ['name' => 'is_primary', 'type' => 'boolean', 'default' => false],
                    ['name' => 'created_at', 'type' => 'timestamp', 'nullable' => true],
                ],
                'indexes' => [
                    ['columns' => ['product_id', 'display_order']],
                ],
                'comment' => 'Product images gallery',
            ],

            'product_variants' => [
                'columns' => [
                    ['name' => 'id', 'type' => 'id', 'primary' => true],
                    ['name' => 'product_id', 'type' => 'foreignId', 'references' => 'products', 'onDelete' => 'cascade'],
                    ['name' => 'sku', 'type' => 'string', 'length' => 100, 'unique' => true],
                    ['name' => 'variant_name', 'type' => 'string'],
                    ['name' => 'price', 'type' => 'decimal', 'precision' => [15, 2]],
                    ['name' => 'compare_at_price', 'type' => 'decimal', 'precision' => [15, 2], 'nullable' => true],
                    ['name' => 'cost_per_item', 'type' => 'decimal', 'precision' => [15, 2], 'nullable' => true],
                    ['name' => 'stock_quantity', 'type' => 'integer', 'default' => 0],
                    ['name' => 'reserved_quantity', 'type' => 'integer', 'default' => 0],
                    ['name' => 'image_id', 'type' => 'foreignId', 'references' => 'product_images', 'nullable' => true, 'onDelete' => 'set null'],
                    ['name' => 'weight_grams', 'type' => 'integer', 'nullable' => true],
                    ['name' => 'is_active', 'type' => 'boolean', 'default' => true],
                    ['name' => 'created_at', 'type' => 'timestamp', 'nullable' => true],
                    ['name' => 'updated_at', 'type' => 'timestamp', 'nullable' => true],
                    ['name' => 'deleted_at', 'type' => 'timestamp', 'nullable' => true],
                ],
                'indexes' => [
                    ['columns' => ['product_id']],
                    ['columns' => ['sku']],
                    ['columns' => ['image_id']],
                ],
                'comment' => 'Product variants (SKUs) with pricing and inventory',
            ],

            'attribute_value_product_variant' => [
                'columns' => [
                    ['name' => 'product_variant_id', 'type' => 'foreignId', 'references' => 'product_variants', 'onDelete' => 'cascade'],
                    ['name' => 'attribute_value_id', 'type' => 'foreignId', 'references' => 'attribute_values', 'onDelete' => 'cascade'],
                ],
                'primary' => ['product_variant_id', 'attribute_value_id'],
                'indexes' => [
                    ['columns' => ['attribute_value_id']],
                ],
                'comment' => 'Pivot table linking variants to attribute values',
            ],

            'product_questions' => [
                'columns' => [
                    ['name' => 'id', 'type' => 'id', 'primary' => true],
                    ['name' => 'product_id', 'type' => 'foreignId', 'references' => 'products', 'onDelete' => 'cascade'],
                    ['name' => 'user_id', 'type' => 'foreignId', 'references' => 'users', 'onDelete' => 'cascade'],
                    ['name' => 'question_text', 'type' => 'text'],
                    ['name' => 'is_answered', 'type' => 'boolean', 'default' => false],
                    ['name' => 'created_at', 'type' => 'timestamp', 'nullable' => true],
                    ['name' => 'updated_at', 'type' => 'timestamp', 'nullable' => true],
                ],
                'indexes' => [
                    ['columns' => ['product_id']],
                    ['columns' => ['user_id']],
                ],
                'comment' => 'Customer questions about products',
            ],

            'product_answers' => [
                'columns' => [
                    ['name' => 'id', 'type' => 'id', 'primary' => true],
                    ['name' => 'question_id', 'type' => 'foreignId', 'references' => 'product_questions', 'onDelete' => 'cascade'],
                    ['name' => 'user_id', 'type' => 'foreignId', 'references' => 'users', 'onDelete' => 'cascade'],
                    ['name' => 'answer_text', 'type' => 'text'],
                    ['name' => 'is_seller', 'type' => 'boolean', 'default' => false],
                    ['name' => 'created_at', 'type' => 'timestamp', 'nullable' => true],
                    ['name' => 'updated_at', 'type' => 'timestamp', 'nullable' => true],
                ],
                'indexes' => [
                    ['columns' => ['question_id']],
                    ['columns' => ['user_id']],
                ],
                'comment' => 'Answers to product questions',
            ],

            'product_views' => [
                'columns' => [
                    ['name' => 'id', 'type' => 'id', 'primary' => true],
                    ['name' => 'product_id', 'type' => 'foreignId', 'references' => 'products', 'onDelete' => 'cascade'],
                    ['name' => 'user_id', 'type' => 'foreignId', 'references' => 'users', 'nullable' => true, 'onDelete' => 'set null'],
                    ['name' => 'session_id', 'type' => 'string', 'nullable' => true],
                    ['name' => 'ip_address', 'type' => 'string', 'length' => 45, 'nullable' => true],
                    ['name' => 'user_agent', 'type' => 'text', 'nullable' => true],
                    ['name' => 'viewed_at', 'type' => 'dateTime'],
                ],
                'indexes' => [
                    ['columns' => ['product_id', 'viewed_at']],
                    ['columns' => ['user_id']],
                ],
                'comment' => 'Product view tracking for analytics',
            ],

            // Level 5: Flash Sales & Promotions
            'flash_sale_events' => [
                'columns' => [
                    ['name' => 'id', 'type' => 'id'],
                    ['name' => 'event_name', 'type' => 'string', 'length' => 100],
                    ['name' => 'description', 'type' => 'text', 'nullable' => true],
                    ['name' => 'start_time', 'type' => 'dateTime'],
                    ['name' => 'end_time', 'type' => 'dateTime'],
                    ['name' => 'is_active', 'type' => 'boolean', 'default' => true],
                    ['name' => 'created_at', 'type' => 'timestamp', 'nullable' => true],
                    ['name' => 'updated_at', 'type' => 'timestamp', 'nullable' => true],
                ],
                'indexes' => [
                    ['columns' => ['start_time', 'end_time']],
                ],
                'comment' => 'Flash sale event campaigns',
            ],

            'flash_sale_products' => [
                'columns' => [
                    ['name' => 'id', 'type' => 'id'],
                    ['name' => 'flash_sale_event_id', 'type' => 'foreignId', 'references' => 'flash_sale_events', 'onDelete' => 'cascade'],
                    ['name' => 'product_variant_id', 'type' => 'foreignId', 'references' => 'product_variants', 'onDelete' => 'cascade'],
                    ['name' => 'flash_price', 'type' => 'decimal', 'precision' => [15, 2]],
                    ['name' => 'quantity_limit', 'type' => 'integer'],
                    ['name' => 'sold_quantity', 'type' => 'integer', 'default' => 0],
                    ['name' => 'max_purchase_per_user', 'type' => 'integer', 'default' => 1],
                    ['name' => 'created_at', 'type' => 'timestamp', 'nullable' => true],
                ],
                'indexes' => [
                    ['columns' => ['flash_sale_event_id']],
                    ['columns' => ['product_variant_id']],
                ],
                'comment' => 'Products participating in flash sales',
            ],

            'promotions' => [
                'columns' => [
                    ['name' => 'id', 'type' => 'id'],
                    ['name' => 'shop_id', 'type' => 'foreignId', 'references' => 'shops', 'nullable' => true, 'onDelete' => 'cascade'],
                    ['name' => 'promotion_name', 'type' => 'string', 'length' => 100],
                    ['name' => 'description', 'type' => 'text', 'nullable' => true],
                    ['name' => 'promotion_type', 'type' => 'enum', 'values' => ['percentage', 'fixed_amount', 'free_shipping', 'buy_x_get_y']],
                    ['name' => 'discount_value', 'type' => 'decimal', 'precision' => [15, 2]],
                    ['name' => 'min_order_value', 'type' => 'decimal', 'precision' => [15, 2], 'nullable' => true],
                    ['name' => 'max_discount_amount', 'type' => 'decimal', 'precision' => [15, 2], 'nullable' => true],
                    ['name' => 'usage_limit', 'type' => 'integer', 'nullable' => true],
                    ['name' => 'used_count', 'type' => 'integer', 'default' => 0],
                    ['name' => 'customer_eligibility', 'type' => 'json', 'nullable' => true],
                    ['name' => 'start_date', 'type' => 'dateTime'],
                    ['name' => 'end_date', 'type' => 'dateTime'],
                    ['name' => 'is_active', 'type' => 'boolean', 'default' => true],
                    ['name' => 'created_at', 'type' => 'timestamp', 'nullable' => true],
                    ['name' => 'updated_at', 'type' => 'timestamp', 'nullable' => true],
                ],
                'indexes' => [
                    ['columns' => ['shop_id']],
                    ['columns' => ['start_date', 'end_date']],
                ],
                'comment' => 'Promotion campaigns and discount rules',
            ],

            'promotion_codes' => [
                'columns' => [
                    ['name' => 'id', 'type' => 'id'],
                    ['name' => 'promotion_id', 'type' => 'foreignId', 'references' => 'promotions', 'onDelete' => 'cascade'],
                    ['name' => 'code', 'type' => 'string', 'length' => 50, 'unique' => true],
                    ['name' => 'usage_limit', 'type' => 'integer', 'nullable' => true],
                    ['name' => 'used_count', 'type' => 'integer', 'default' => 0],
                    ['name' => 'created_at', 'type' => 'timestamp', 'nullable' => true],
                ],
                'indexes' => [
                    ['columns' => ['promotion_id']],
                    ['columns' => ['code']],
                ],
                'comment' => 'Promotion coupon codes',
            ],

            // Level 6: Orders & Transactions
            'orders' => [
                'columns' => [
                    ['name' => 'id', 'type' => 'id'],
                    ['name' => 'order_number', 'type' => 'string', 'length' => 50, 'unique' => true],
                    ['name' => 'customer_id', 'type' => 'foreignId', 'references' => 'users', 'onDelete' => 'cascade'],
                    ['name' => 'shop_id', 'type' => 'foreignId', 'references' => 'shops', 'onDelete' => 'cascade'],
                    ['name' => 'status', 'type' => 'enum', 'values' => ['pending', 'confirmed', 'processing', 'shipping', 'delivered', 'cancelled', 'refunded'], 'default' => 'pending'],
                    ['name' => 'payment_status', 'type' => 'enum', 'values' => ['unpaid', 'paid', 'partially_refunded', 'refunded'], 'default' => 'unpaid'],
                    ['name' => 'subtotal', 'type' => 'decimal', 'precision' => [15, 2]],
                    ['name' => 'discount_amount', 'type' => 'decimal', 'precision' => [15, 2], 'default' => 0],
                    ['name' => 'shipping_fee', 'type' => 'decimal', 'precision' => [15, 2], 'default' => 0],
                    ['name' => 'tax_amount', 'type' => 'decimal', 'precision' => [15, 2], 'default' => 0],
                    ['name' => 'total_amount', 'type' => 'decimal', 'precision' => [15, 2]],
                    ['name' => 'currency', 'type' => 'string', 'length' => 3, 'default' => 'VND'],
                    ['name' => 'shipping_address_id', 'type' => 'foreignId', 'references' => 'user_addresses'],
                    ['name' => 'payment_method', 'type' => 'enum', 'values' => ['cod', 'credit_card', 'e_wallet', 'bank_transfer']],
                    ['name' => 'note', 'type' => 'text', 'nullable' => true],
                    ['name' => 'cancelled_reason', 'type' => 'text', 'nullable' => true],
                    ['name' => 'cancelled_at', 'type' => 'dateTime', 'nullable' => true],
                    ['name' => 'confirmed_at', 'type' => 'dateTime', 'nullable' => true],
                    ['name' => 'delivered_at', 'type' => 'dateTime', 'nullable' => true],
                    ['name' => 'created_at', 'type' => 'timestamp', 'nullable' => true],
                    ['name' => 'updated_at', 'type' => 'timestamp', 'nullable' => true],
                    ['name' => 'deleted_at', 'type' => 'timestamp', 'nullable' => true],
                ],
                'indexes' => [
                    ['columns' => ['order_number']],
                    ['columns' => ['customer_id']],
                    ['columns' => ['shop_id']],
                    ['columns' => ['status']],
                    ['columns' => ['created_at']],
                ],
                'comment' => 'Customer orders with full transaction details',
            ],

            'order_items' => [
                'columns' => [
                    ['name' => 'id', 'type' => 'id'],
                    ['name' => 'order_id', 'type' => 'foreignId', 'references' => 'orders', 'onDelete' => 'cascade'],
                    ['name' => 'product_variant_id', 'type' => 'foreignId', 'references' => 'product_variants'],
                    ['name' => 'product_name', 'type' => 'string'],
                    ['name' => 'variant_name', 'type' => 'string'],
                    ['name' => 'sku', 'type' => 'string', 'length' => 100],
                    ['name' => 'quantity', 'type' => 'integer'],
                    ['name' => 'unit_price', 'type' => 'decimal', 'precision' => [15, 2]],
                    ['name' => 'subtotal', 'type' => 'decimal', 'precision' => [15, 2]],
                    ['name' => 'discount_amount', 'type' => 'decimal', 'precision' => [15, 2], 'default' => 0],
                    ['name' => 'total_price', 'type' => 'decimal', 'precision' => [15, 2]],
                    ['name' => 'created_at', 'type' => 'timestamp', 'nullable' => true],
                ],
                'indexes' => [
                    ['columns' => ['order_id']],
                    ['columns' => ['product_variant_id']],
                ],
                'comment' => 'Line items in orders',
            ],

            'order_promotion' => [
                'columns' => [
                    ['name' => 'order_id', 'type' => 'foreignId', 'references' => 'orders', 'onDelete' => 'cascade'],
                    ['name' => 'promotion_id', 'type' => 'foreignId', 'references' => 'promotions', 'onDelete' => 'cascade'],
                    ['name' => 'discount_amount', 'type' => 'decimal', 'precision' => [15, 2]],
                    ['name' => 'created_at', 'type' => 'timestamp', 'nullable' => true],
                ],
                'primary' => ['order_id', 'promotion_id'],
                'comment' => 'Promotions applied to orders',
            ],

            'transactions' => [
                'columns' => [
                    ['name' => 'id', 'type' => 'id'],
                    ['name' => 'order_id', 'type' => 'foreignId', 'references' => 'orders', 'onDelete' => 'cascade'],
                    ['name' => 'transaction_number', 'type' => 'string', 'length' => 50, 'unique' => true],
                    ['name' => 'payment_method', 'type' => 'enum', 'values' => ['cod', 'credit_card', 'e_wallet', 'bank_transfer']],
                    ['name' => 'amount', 'type' => 'decimal', 'precision' => [15, 2]],
                    ['name' => 'currency', 'type' => 'string', 'length' => 3, 'default' => 'VND'],
                    ['name' => 'status', 'type' => 'enum', 'values' => ['pending', 'success', 'failed'], 'default' => 'pending'],
                    ['name' => 'gateway_transaction_id', 'type' => 'string', 'nullable' => true],
                    ['name' => 'gateway_response', 'type' => 'json', 'nullable' => true],
                    ['name' => 'paid_at', 'type' => 'dateTime', 'nullable' => true],
                    ['name' => 'created_at', 'type' => 'timestamp', 'nullable' => true],
                    ['name' => 'updated_at', 'type' => 'timestamp', 'nullable' => true],
                ],
                'indexes' => [
                    ['columns' => ['order_id']],
                    ['columns' => ['transaction_number']],
                    ['columns' => ['status']],
                ],
                'comment' => 'Payment transactions for orders',
            ],

            'shipping_details' => [
                'columns' => [
                    ['name' => 'id', 'type' => 'id'],
                    ['name' => 'order_id', 'type' => 'foreignId', 'references' => 'orders', 'onDelete' => 'cascade'],
                    ['name' => 'shipper_id', 'type' => 'foreignId', 'references' => 'users', 'nullable' => true],
                    ['name' => 'tracking_number', 'type' => 'string', 'length' => 100, 'nullable' => true],
                    ['name' => 'carrier', 'type' => 'string', 'length' => 100, 'nullable' => true],
                    ['name' => 'status', 'type' => 'enum', 'values' => ['pending', 'picked_up', 'in_transit', 'out_for_delivery', 'delivered', 'failed', 'returned'], 'default' => 'pending'],
                    ['name' => 'estimated_delivery', 'type' => 'dateTime', 'nullable' => true],
                    ['name' => 'actual_delivery', 'type' => 'dateTime', 'nullable' => true],
                    ['name' => 'notes', 'type' => 'text', 'nullable' => true],
                    ['name' => 'created_at', 'type' => 'timestamp', 'nullable' => true],
                    ['name' => 'updated_at', 'type' => 'timestamp', 'nullable' => true],
                ],
                'indexes' => [
                    ['columns' => ['order_id']],
                    ['columns' => ['shipper_id']],
                    ['columns' => ['tracking_number']],
                ],
                'comment' => 'Shipping and delivery information',
            ],

            // Level 7: Cart & Wishlist
            'cart_items' => [
                'columns' => [
                    ['name' => 'id', 'type' => 'id'],
                    ['name' => 'user_id', 'type' => 'foreignId', 'references' => 'users', 'onDelete' => 'cascade'],
                    ['name' => 'product_variant_id', 'type' => 'foreignId', 'references' => 'product_variants', 'onDelete' => 'cascade'],
                    ['name' => 'quantity', 'type' => 'integer', 'default' => 1],
                    ['name' => 'created_at', 'type' => 'timestamp', 'nullable' => true],
                    ['name' => 'updated_at', 'type' => 'timestamp', 'nullable' => true],
                ],
                'indexes' => [
                    ['columns' => ['user_id']],
                    ['columns' => ['product_variant_id']],
                ],
                'comment' => 'Shopping cart items',
            ],

            'wishlists' => [
                'columns' => [
                    ['name' => 'id', 'type' => 'id'],
                    ['name' => 'user_id', 'type' => 'foreignId', 'references' => 'users', 'onDelete' => 'cascade'],
                    ['name' => 'name', 'type' => 'string', 'length' => 100, 'default' => 'My Wishlist'],
                    ['name' => 'is_public', 'type' => 'boolean', 'default' => false],
                    ['name' => 'created_at', 'type' => 'timestamp', 'nullable' => true],
                    ['name' => 'updated_at', 'type' => 'timestamp', 'nullable' => true],
                ],
                'indexes' => [
                    ['columns' => ['user_id']],
                ],
                'comment' => 'User wishlist collections',
            ],

            'wishlist_items' => [
                'columns' => [
                    ['name' => 'id', 'type' => 'id'],
                    ['name' => 'wishlist_id', 'type' => 'foreignId', 'references' => 'wishlists', 'onDelete' => 'cascade'],
                    ['name' => 'product_id', 'type' => 'foreignId', 'references' => 'products', 'onDelete' => 'cascade'],
                    ['name' => 'created_at', 'type' => 'timestamp', 'nullable' => true],
                ],
                'indexes' => [
                    ['columns' => ['wishlist_id']],
                    ['columns' => ['product_id']],
                ],
                'comment' => 'Products in wishlists',
            ],

            // Level 8: Reviews & Q&A
            'reviews' => [
                'columns' => [
                    ['name' => 'id', 'type' => 'id'],
                    ['name' => 'product_id', 'type' => 'foreignId', 'references' => 'products', 'onDelete' => 'cascade'],
                    ['name' => 'user_id', 'type' => 'foreignId', 'references' => 'users', 'onDelete' => 'cascade'],
                    ['name' => 'order_item_id', 'type' => 'foreignId', 'references' => 'order_items', 'nullable' => true],
                    ['name' => 'rating', 'type' => 'tinyInteger'],
                    ['name' => 'title', 'type' => 'string', 'nullable' => true],
                    ['name' => 'comment', 'type' => 'text', 'nullable' => true],
                    ['name' => 'is_verified_purchase', 'type' => 'boolean', 'default' => false],
                    ['name' => 'helpful_count', 'type' => 'integer', 'default' => 0],
                    ['name' => 'created_at', 'type' => 'timestamp', 'nullable' => true],
                    ['name' => 'updated_at', 'type' => 'timestamp', 'nullable' => true],
                    ['name' => 'deleted_at', 'type' => 'timestamp', 'nullable' => true],
                ],
                'indexes' => [
                    ['columns' => ['product_id']],
                    ['columns' => ['user_id']],
                    ['columns' => ['rating']],
                ],
                'comment' => 'Product reviews and ratings',
            ],

            'review_media' => [
                'columns' => [
                    ['name' => 'id', 'type' => 'id'],
                    ['name' => 'review_id', 'type' => 'foreignId', 'references' => 'reviews', 'onDelete' => 'cascade'],
                    ['name' => 'media_type', 'type' => 'enum', 'values' => ['image', 'video']],
                    ['name' => 'media_url', 'type' => 'string'],
                    ['name' => 'thumbnail_url', 'type' => 'string', 'nullable' => true],
                    ['name' => 'display_order', 'type' => 'integer', 'default' => 0],
                    ['name' => 'created_at', 'type' => 'timestamp', 'nullable' => true],
                ],
                'indexes' => [
                    ['columns' => ['review_id']],
                ],
                'comment' => 'Media attachments for reviews',
            ],

            // Level 9: Shipping & Logistics
            'shipper_profiles' => [
                'columns' => [
                    ['name' => 'id', 'type' => 'id'],
                    ['name' => 'user_id', 'type' => 'foreignId', 'references' => 'users', 'onDelete' => 'cascade'],
                    ['name' => 'vehicle_type', 'type' => 'enum', 'values' => ['motorcycle', 'car', 'truck']],
                    ['name' => 'vehicle_number', 'type' => 'string', 'length' => 50],
                    ['name' => 'license_number', 'type' => 'string', 'length' => 50],
                    ['name' => 'current_hub_id', 'type' => 'foreignId', 'references' => 'hubs', 'nullable' => true],
                    ['name' => 'rating', 'type' => 'decimal', 'precision' => [3, 2], 'default' => 0],
                    ['name' => 'total_deliveries', 'type' => 'integer', 'default' => 0],
                    ['name' => 'is_active', 'type' => 'boolean', 'default' => true],
                    ['name' => 'created_at', 'type' => 'timestamp', 'nullable' => true],
                    ['name' => 'updated_at', 'type' => 'timestamp', 'nullable' => true],
                ],
                'indexes' => [
                    ['columns' => ['user_id']],
                    ['columns' => ['current_hub_id']],
                ],
                'comment' => 'Shipper/driver profiles',
            ],

            'shipment_journeys' => [
                'columns' => [
                    ['name' => 'id', 'type' => 'id'],
                    ['name' => 'shipping_detail_id', 'type' => 'foreignId', 'references' => 'shipping_details', 'onDelete' => 'cascade'],
                    ['name' => 'hub_id', 'type' => 'foreignId', 'references' => 'hubs', 'nullable' => true],
                    ['name' => 'status', 'type' => 'enum', 'values' => ['picked_up', 'at_hub', 'in_transit', 'out_for_delivery', 'delivered', 'failed']],
                    ['name' => 'location', 'type' => 'string', 'nullable' => true],
                    ['name' => 'latitude', 'type' => 'decimal', 'precision' => [10, 8], 'nullable' => true],
                    ['name' => 'longitude', 'type' => 'decimal', 'precision' => [11, 8], 'nullable' => true],
                    ['name' => 'notes', 'type' => 'text', 'nullable' => true],
                    ['name' => 'created_at', 'type' => 'timestamp', 'nullable' => true],
                ],
                'indexes' => [
                    ['columns' => ['shipping_detail_id']],
                    ['columns' => ['hub_id']],
                ],
                'comment' => 'Shipment tracking journey points',
            ],

            'shipper_ratings' => [
                'columns' => [
                    ['name' => 'id', 'type' => 'id'],
                    ['name' => 'order_id', 'type' => 'foreignId', 'references' => 'orders', 'onDelete' => 'cascade'],
                    ['name' => 'shipper_id', 'type' => 'foreignId', 'references' => 'users', 'onDelete' => 'cascade'],
                    ['name' => 'customer_id', 'type' => 'foreignId', 'references' => 'users', 'onDelete' => 'cascade'],
                    ['name' => 'rating', 'type' => 'tinyInteger'],
                    ['name' => 'comment', 'type' => 'text', 'nullable' => true],
                    ['name' => 'created_at', 'type' => 'timestamp', 'nullable' => true],
                ],
                'indexes' => [
                    ['columns' => ['shipper_id']],
                    ['columns' => ['order_id']],
                ],
                'comment' => 'Ratings for shippers from customers',
            ],

            // Level 10: Returns & Disputes
            'returns' => [
                'columns' => [
                    ['name' => 'id', 'type' => 'id'],
                    ['name' => 'order_id', 'type' => 'foreignId', 'references' => 'orders', 'onDelete' => 'cascade'],
                    ['name' => 'customer_id', 'type' => 'foreignId', 'references' => 'users', 'onDelete' => 'cascade'],
                    ['name' => 'return_number', 'type' => 'string', 'length' => 50, 'unique' => true],
                    ['name' => 'reason', 'type' => 'text'],
                    ['name' => 'status', 'type' => 'enum', 'values' => ['requested', 'approved', 'rejected', 'received', 'refunded'], 'default' => 'requested'],
                    ['name' => 'refund_amount', 'type' => 'decimal', 'precision' => [15, 2], 'nullable' => true],
                    ['name' => 'admin_note', 'type' => 'text', 'nullable' => true],
                    ['name' => 'created_at', 'type' => 'timestamp', 'nullable' => true],
                    ['name' => 'updated_at', 'type' => 'timestamp', 'nullable' => true],
                    ['name' => 'deleted_at', 'type' => 'timestamp', 'nullable' => true],
                ],
                'indexes' => [
                    ['columns' => ['order_id']],
                    ['columns' => ['customer_id']],
                    ['columns' => ['return_number']],
                ],
                'comment' => 'Product return requests',
            ],

            'return_items' => [
                'columns' => [
                    ['name' => 'id', 'type' => 'id'],
                    ['name' => 'return_id', 'type' => 'foreignId', 'references' => 'returns', 'onDelete' => 'cascade'],
                    ['name' => 'order_item_id', 'type' => 'foreignId', 'references' => 'order_items', 'onDelete' => 'cascade'],
                    ['name' => 'quantity', 'type' => 'integer'],
                    ['name' => 'reason', 'type' => 'text'],
                    ['name' => 'created_at', 'type' => 'timestamp', 'nullable' => true],
                ],
                'indexes' => [
                    ['columns' => ['return_id']],
                    ['columns' => ['order_item_id']],
                ],
                'comment' => 'Line items in return requests',
            ],

            'disputes' => [
                'columns' => [
                    ['name' => 'id', 'type' => 'id'],
                    ['name' => 'order_id', 'type' => 'foreignId', 'references' => 'orders', 'onDelete' => 'cascade'],
                    ['name' => 'customer_id', 'type' => 'foreignId', 'references' => 'users', 'onDelete' => 'cascade'],
                    ['name' => 'shop_id', 'type' => 'foreignId', 'references' => 'shops', 'onDelete' => 'cascade'],
                    ['name' => 'dispute_number', 'type' => 'string', 'length' => 50, 'unique' => true],
                    ['name' => 'reason', 'type' => 'text'],
                    ['name' => 'status', 'type' => 'enum', 'values' => ['open', 'in_review', 'resolved', 'closed'], 'default' => 'open'],
                    ['name' => 'resolution', 'type' => 'text', 'nullable' => true],
                    ['name' => 'created_at', 'type' => 'timestamp', 'nullable' => true],
                    ['name' => 'updated_at', 'type' => 'timestamp', 'nullable' => true],
                ],
                'indexes' => [
                    ['columns' => ['order_id']],
                    ['columns' => ['customer_id']],
                    ['columns' => ['shop_id']],
                ],
                'comment' => 'Order disputes between customers and sellers',
            ],

            'dispute_messages' => [
                'columns' => [
                    ['name' => 'id', 'type' => 'id'],
                    ['name' => 'dispute_id', 'type' => 'foreignId', 'references' => 'disputes', 'onDelete' => 'cascade'],
                    ['name' => 'sender_id', 'type' => 'foreignId', 'references' => 'users', 'onDelete' => 'cascade'],
                    ['name' => 'message', 'type' => 'text'],
                    ['name' => 'attachment_url', 'type' => 'string', 'nullable' => true],
                    ['name' => 'created_at', 'type' => 'timestamp', 'nullable' => true],
                ],
                'indexes' => [
                    ['columns' => ['dispute_id']],
                    ['columns' => ['sender_id']],
                ],
                'comment' => 'Messages in dispute threads',
            ],

            // Level 11: Chat & Notifications
            'chat_rooms' => [
                'columns' => [
                    ['name' => 'id', 'type' => 'id'],
                    ['name' => 'room_type', 'type' => 'enum', 'values' => ['customer_seller', 'customer_support']],
                    ['name' => 'product_id', 'type' => 'foreignId', 'references' => 'products', 'nullable' => true],
                    ['name' => 'last_message_at', 'type' => 'dateTime', 'nullable' => true],
                    ['name' => 'created_at', 'type' => 'timestamp', 'nullable' => true],
                    ['name' => 'updated_at', 'type' => 'timestamp', 'nullable' => true],
                ],
                'indexes' => [
                    ['columns' => ['product_id']],
                    ['columns' => ['last_message_at']],
                ],
                'comment' => 'Chat room containers',
            ],

            'chat_participants' => [
                'columns' => [
                    ['name' => 'chat_room_id', 'type' => 'foreignId', 'references' => 'chat_rooms', 'onDelete' => 'cascade'],
                    ['name' => 'user_id', 'type' => 'foreignId', 'references' => 'users', 'onDelete' => 'cascade'],
                    ['name' => 'last_read_at', 'type' => 'dateTime', 'nullable' => true],
                    ['name' => 'created_at', 'type' => 'timestamp', 'nullable' => true],
                ],
                'primary' => ['chat_room_id', 'user_id'],
                'indexes' => [
                    ['columns' => ['user_id']],
                ],
                'comment' => 'Users participating in chat rooms',
            ],

            'chat_messages' => [
                'columns' => [
                    ['name' => 'id', 'type' => 'id'],
                    ['name' => 'chat_room_id', 'type' => 'foreignId', 'references' => 'chat_rooms', 'onDelete' => 'cascade'],
                    ['name' => 'sender_id', 'type' => 'foreignId', 'references' => 'users', 'onDelete' => 'cascade'],
                    ['name' => 'message', 'type' => 'text'],
                    ['name' => 'message_type', 'type' => 'enum', 'values' => ['text', 'image', 'product_link'], 'default' => 'text'],
                    ['name' => 'attachment_url', 'type' => 'string', 'nullable' => true],
                    ['name' => 'is_read', 'type' => 'boolean', 'default' => false],
                    ['name' => 'created_at', 'type' => 'timestamp', 'nullable' => true],
                ],
                'indexes' => [
                    ['columns' => ['chat_room_id', 'created_at']],
                    ['columns' => ['sender_id']],
                ],
                'comment' => 'Chat messages',
            ],

            'notifications' => [
                'columns' => [
                    ['name' => 'id', 'type' => 'id'],
                    ['name' => 'user_id', 'type' => 'foreignId', 'references' => 'users', 'onDelete' => 'cascade'],
                    ['name' => 'type', 'type' => 'string'],
                    ['name' => 'title', 'type' => 'string'],
                    ['name' => 'message', 'type' => 'text'],
                    ['name' => 'data', 'type' => 'json', 'nullable' => true],
                    ['name' => 'is_read', 'type' => 'boolean', 'default' => false],
                    ['name' => 'read_at', 'type' => 'dateTime', 'nullable' => true],
                    ['name' => 'created_at', 'type' => 'timestamp', 'nullable' => true],
                ],
                'indexes' => [
                    ['columns' => ['user_id', 'is_read']],
                    ['columns' => ['created_at']],
                ],
                'comment' => 'User notifications',
            ],

            // Level 12: Analytics & Tracking
            'user_events' => [
                'columns' => [
                    ['name' => 'id', 'type' => 'id'],
                    ['name' => 'user_id', 'type' => 'foreignId', 'references' => 'users', 'nullable' => true, 'onDelete' => 'set null'],
                    ['name' => 'event_type', 'type' => 'string', 'length' => 50],
                    ['name' => 'event_data', 'type' => 'json', 'nullable' => true],
                    ['name' => 'ip_address', 'type' => 'string', 'length' => 45, 'nullable' => true],
                    ['name' => 'user_agent', 'type' => 'text', 'nullable' => true],
                    ['name' => 'created_at', 'type' => 'timestamp', 'nullable' => true],
                ],
                'indexes' => [
                    ['columns' => ['user_id', 'event_type']],
                    ['columns' => ['created_at']],
                ],
                'comment' => 'User behavior tracking events',
            ],

            'search_histories' => [
                'columns' => [
                    ['name' => 'id', 'type' => 'id'],
                    ['name' => 'user_id', 'type' => 'foreignId', 'references' => 'users', 'nullable' => true, 'onDelete' => 'set null'],
                    ['name' => 'search_query', 'type' => 'string'],
                    ['name' => 'filters', 'type' => 'json', 'nullable' => true],
                    ['name' => 'results_count', 'type' => 'integer', 'default' => 0],
                    ['name' => 'created_at', 'type' => 'timestamp', 'nullable' => true],
                ],
                'indexes' => [
                    ['columns' => ['user_id']],
                    ['columns' => ['search_query']],
                    ['columns' => ['created_at']],
                ],
                'comment' => 'Search query history for analytics',
            ],

            'user_preferences' => [
                'columns' => [
                    ['name' => 'id', 'type' => 'id'],
                    ['name' => 'user_id', 'type' => 'foreignId', 'references' => 'users', 'onDelete' => 'cascade'],
                    ['name' => 'notification_email', 'type' => 'boolean', 'default' => true],
                    ['name' => 'notification_sms', 'type' => 'boolean', 'default' => false],
                    ['name' => 'notification_push', 'type' => 'boolean', 'default' => true],
                    ['name' => 'language', 'type' => 'string', 'length' => 10, 'default' => 'vi'],
                    ['name' => 'currency', 'type' => 'string', 'length' => 3, 'default' => 'VND'],
                    ['name' => 'theme', 'type' => 'enum', 'values' => ['light', 'dark', 'auto'], 'default' => 'auto'],
                    ['name' => 'created_at', 'type' => 'timestamp', 'nullable' => true],
                    ['name' => 'updated_at', 'type' => 'timestamp', 'nullable' => true],
                ],
                'indexes' => [
                    ['columns' => ['user_id']],
                ],
                'comment' => 'User preferences and settings',
            ],

            'analytics_reports' => [
                'columns' => [
                    ['name' => 'id', 'type' => 'id'],
                    ['name' => 'report_type', 'type' => 'enum', 'values' => ['daily_sales', 'monthly_sales', 'product_performance', 'user_activity']],
                    ['name' => 'entity_type', 'type' => 'string', 'length' => 50, 'nullable' => true],
                    ['name' => 'entity_id', 'type' => 'foreignId', 'nullable' => true],
                    ['name' => 'period_start', 'type' => 'date'],
                    ['name' => 'period_end', 'type' => 'date'],
                    ['name' => 'metrics', 'type' => 'json'],
                    ['name' => 'created_at', 'type' => 'timestamp', 'nullable' => true],
                ],
                'indexes' => [
                    ['columns' => ['report_type', 'period_start']],
                    ['columns' => ['entity_type', 'entity_id']],
                ],
                'comment' => 'Aggregated analytics reports',
            ],

            // Level 13: International Support
            'international_addresses' => [
                'columns' => [
                    ['name' => 'id', 'type' => 'id'],
                    ['name' => 'addressable_type', 'type' => 'string'],
                    ['name' => 'addressable_id', 'type' => 'foreignId'],
                    ['name' => 'address_line1', 'type' => 'string'],
                    ['name' => 'address_line2', 'type' => 'string', 'nullable' => true],
                    ['name' => 'city', 'type' => 'string'],
                    ['name' => 'state_province', 'type' => 'string', 'nullable' => true],
                    ['name' => 'postal_code', 'type' => 'string', 'length' => 20],
                    ['name' => 'country_id', 'type' => 'foreignId', 'references' => 'countries'],
                    ['name' => 'created_at', 'type' => 'timestamp', 'nullable' => true],
                    ['name' => 'updated_at', 'type' => 'timestamp', 'nullable' => true],
                ],
                'indexes' => [
                    ['columns' => ['addressable_type', 'addressable_id']],
                    ['columns' => ['country_id']],
                ],
                'comment' => 'Polymorphic international addresses',
            ],

            // Level 14: Two-Factor Authentication
            'two_factor_authentications' => [
                'columns' => [
                    ['name' => 'id', 'type' => 'id'],
                    ['name' => 'user_id', 'type' => 'foreignId', 'references' => 'users', 'onDelete' => 'cascade'],
                    ['name' => 'method', 'type' => 'enum', 'values' => ['authenticator', 'sms', 'email']],
                    ['name' => 'identifier', 'type' => 'string', 'nullable' => true],
                    ['name' => 'secret', 'type' => 'text', 'nullable' => true],
                    ['name' => 'backup_codes', 'type' => 'json', 'nullable' => true],
                    ['name' => 'is_active', 'type' => 'boolean', 'default' => true],
                    ['name' => 'last_used_at', 'type' => 'timestamp', 'nullable' => true],
                    ['name' => 'verified_at', 'type' => 'timestamp', 'nullable' => true],
                    ['name' => 'created_at', 'type' => 'timestamp', 'nullable' => true],
                    ['name' => 'updated_at', 'type' => 'timestamp', 'nullable' => true],
                ],
                'indexes' => [
                    ['columns' => ['user_id']],
                    ['columns' => ['method']],
                    ['columns' => ['is_active']],
                ],
                'comment' => 'Two-factor authentication methods for users',
            ],

            'two_factor_challenges' => [
                'columns' => [
                    ['name' => 'id', 'type' => 'id'],
                    ['name' => 'user_id', 'type' => 'foreignId', 'references' => 'users', 'onDelete' => 'cascade'],
                    ['name' => 'method', 'type' => 'enum', 'values' => ['authenticator', 'sms', 'email', 'backup_code']],
                    ['name' => 'code', 'type' => 'string', 'length' => 10],
                    ['name' => 'ip_address', 'type' => 'string', 'length' => 45, 'nullable' => true],
                    ['name' => 'user_agent', 'type' => 'text', 'nullable' => true],
                    ['name' => 'verified_at', 'type' => 'timestamp', 'nullable' => true],
                    ['name' => 'expires_at', 'type' => 'dateTime'],
                    ['name' => 'created_at', 'type' => 'timestamp', 'nullable' => true],
                ],
                'indexes' => [
                    ['columns' => ['user_id']],
                    ['columns' => ['expires_at']],
                    ['columns' => ['verified_at']],
                ],
                'comment' => 'Temporary 2FA verification codes and challenges',
            ],

            'two_factor_trusted_devices' => [
                'columns' => [
                    ['name' => 'id', 'type' => 'id'],
                    ['name' => 'user_id', 'type' => 'foreignId', 'references' => 'users', 'onDelete' => 'cascade'],
                    ['name' => 'device_name', 'type' => 'string', 'nullable' => true],
                    ['name' => 'device_fingerprint', 'type' => 'string', 'unique' => true],
                    ['name' => 'ip_address', 'type' => 'string', 'length' => 45, 'nullable' => true],
                    ['name' => 'user_agent', 'type' => 'text', 'nullable' => true],
                    ['name' => 'last_used_at', 'type' => 'timestamp', 'nullable' => true],
                    ['name' => 'expires_at', 'type' => 'dateTime'],
                    ['name' => 'created_at', 'type' => 'timestamp', 'nullable' => true],
                    ['name' => 'updated_at', 'type' => 'timestamp', 'nullable' => true],
                ],
                'indexes' => [
                    ['columns' => ['user_id']],
                    ['columns' => ['device_fingerprint']],
                    ['columns' => ['expires_at']],
                ],
                'comment' => 'Trusted devices that skip 2FA for a period',
            ],
        ];
    }

    /**
     * Generate migration files
     */
    protected function generateMigrations(): void
    {
        $progressBar = $this->output->createProgressBar(count($this->schema));
        $progressBar->start();

        foreach ($this->schema as $tableName => $definition) {
            $this->generateMigrationFile($tableName, $definition);
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);
    }

    /**
     * Generate single migration file
     */
    protected function generateMigrationFile(string $tableName, array $definition): void
    {
        $fileName = $this->getMigrationFileName($tableName);
        $filePath = database_path("migrations/{$fileName}");

        // Skip if file already exists
        if (File::exists($filePath)) {
            return;
        }

        $content = $this->generateMigrationContent($tableName, $definition);
        File::put($filePath, $content);

        $this->counter++;
    }

    /**
     * Generate migration file name
     */
    protected function getMigrationFileName(string $tableName): string
    {
        $number = str_pad($this->counter, 6, '0', STR_PAD_LEFT);
        return "{$this->baseDate}_{$number}_create_{$tableName}_table.php";
    }

    /**
     * Generate migration file content
     */
    protected function generateMigrationContent(string $tableName, array $definition): string
    {
        $className = Str::studly("create_{$tableName}_table");
        $comment = $definition['comment'] ?? "Table: {$tableName}";

        $upMethod = $this->generateUpMethod($tableName, $definition);
        $downMethod = $this->generateDownMethod($tableName);

        return <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * {$comment}
     */
    public function up(): void
    {
{$upMethod}
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
{$downMethod}
    }
};

PHP;
    }

    /**
     * Generate up() method content
     */
    protected function generateUpMethod(string $tableName, array $definition): string
    {
        $lines = [];
        $lines[] = "        Schema::create('{$tableName}', function (Blueprint \$table) {";

        $hasTimestamps = false;
        $hasSoftDeletes = false;

        // Check if table has timestamps/soft deletes
        foreach ($definition['columns'] as $column) {
            if ($column['name'] === 'created_at' || $column['name'] === 'updated_at') {
                $hasTimestamps = true;
            }
            if ($column['name'] === 'deleted_at') {
                $hasSoftDeletes = true;
            }
        }

        // Handle composite primary key
        if (isset($definition['primary']) && is_array($definition['primary'])) {
            foreach ($definition['columns'] as $column) {
                $line = $this->generateColumnDefinition($column, true);
                if ($line !== '') {
                    $lines[] = $line;
                }
            }
            $primaryKeys = implode("', '", $definition['primary']);
            $lines[] = "            \$table->primary(['{$primaryKeys}']);";
        } else {
            foreach ($definition['columns'] as $column) {
                $line = $this->generateColumnDefinition($column);
                if ($line !== '') {
                    $lines[] = $line;
                }
            }
        }

        // Add timestamps() if has created_at or updated_at
        if ($hasTimestamps) {
            $lines[] = "            \$table->timestamps();";
        }

        // Add softDeletes() if has deleted_at
        if ($hasSoftDeletes) {
            $lines[] = "            \$table->softDeletes();";
        }

        // Add indexes
        if (isset($definition['indexes'])) {
            $lines[] = "";
            $lines[] = "            // Indexes";
            foreach ($definition['indexes'] as $index) {
                $columns = implode("', '", $index['columns']);
                if (isset($index['unique']) && $index['unique']) {
                    $lines[] = "            \$table->unique(['{$columns}']);";
                } else {
                    $lines[] = "            \$table->index(['{$columns}']);";
                }
            }
        }

        $lines[] = "        });";

        return implode("\n", $lines);
    }

    /**
     * Generate down() method content
     */
    protected function generateDownMethod(string $tableName): string
    {
        return "        Schema::dropIfExists('{$tableName}');";
    }

    /**
     * Generate column definition
     */
    protected function generateColumnDefinition(array $column, bool $skipPrimary = false): string
    {
        $name = $column['name'];
        $type = $column['type'];

        // Handle special types
        if ($type === 'id' && !$skipPrimary) {
            return "            \$table->id();";
        }

        if ($type === 'rememberToken') {
            return "            \$table->rememberToken();";
        }

        // Skip individual created_at/updated_at/deleted_at - will be handled by timestamps()/softDeletes()
        if ($type === 'timestamp' && in_array($name, ['created_at', 'updated_at', 'deleted_at'])) {
            return ''; // Skip, will add timestamps()/softDeletes() separately
        }

        // Handle foreign keys
        if ($type === 'foreignId') {
            $line = "            \$table->foreignId('{$name}')";
            
            // Add nullable() BEFORE constrained() if needed
            if (isset($column['nullable']) && $column['nullable']) {
                $line .= "\n                ->nullable()";
            }
            
            if (isset($column['references'])) {
                $references = $column['references'];
                $line .= "\n                ->constrained('{$references}')";
                if (isset($column['onDelete'])) {
                    $onDelete = $column['onDelete'];
                    $line .= "\n                ->onDelete('{$onDelete}')";
                }
            }
            
            $line .= ";";
            return $line;
        }

        // Handle enum
        if ($type === 'enum') {
            $values = implode("', '", $column['values']);
            $line = "            \$table->enum('{$name}', ['{$values}'])";
        } else {
            // Map type
            $laravelType = $this->typeMap[$type] ?? $type;
            $line = "            \$table->{$laravelType}('{$name}'";

            // Add length or precision
            if (isset($column['length'])) {
                $line .= ", {$column['length']}";
            } elseif (isset($column['precision']) && is_array($column['precision'])) {
                $precision = implode(', ', $column['precision']);
                $line .= ", {$precision}";
            }

            $line .= ")";
        }

        // Add modifiers
        if (isset($column['nullable']) && $column['nullable']) {
            $line .= "->nullable()";
        }

        if (isset($column['unique']) && $column['unique']) {
            $line .= "->unique()";
        }

        if (isset($column['default'])) {
            $default = $column['default'];
            if (is_string($default) && !in_array($default, ['true', 'false'])) {
                $line .= "->default('{$default}')";
            } elseif (is_bool($default)) {
                $line .= "->default(" . ($default ? 'true' : 'false') . ")";
            } else {
                $line .= "->default({$default})";
            }
        }

        $line .= ";";

        return $line;
    }

    /**
     * Parse schema from file (for future enhancement)
     */
    protected function parseSchemaFromFile(string $filePath): void
    {
        $this->warn('DBML parsing not yet implemented. Using hardcoded schema.');
        $this->defineSchemaManually();
    }
}
