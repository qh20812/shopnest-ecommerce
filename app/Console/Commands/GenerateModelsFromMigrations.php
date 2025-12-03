<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GenerateModelsFromMigrations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:generate-models 
                            {--tables=* : Specific tables to generate models for}
                            {--force : Overwrite existing models}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Eloquent models from existing migrations';

    /**
     * Schema definition for relationships and additional metadata
     */
    protected array $schema = [];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting model generation from migrations...');
        
        $this->defineSchemaMetadata();
        
        $specificTables = $this->option('tables');
        $tablesToGenerate = !empty($specificTables) 
            ? $specificTables 
            : array_keys($this->schema);
        
        $generated = 0;
        $skipped = 0;
        
        foreach ($tablesToGenerate as $tableName) {
            // Skip pivot tables (they don't need models)
            if ($this->isPivotTable($tableName)) {
                $this->warn("⏭️  Skipped: {$tableName} (pivot table)");
                continue;
            }
            
            if (!isset($this->schema[$tableName])) {
                $this->warn("⚠️  Table '{$tableName}' not found in schema");
                continue;
            }
            
            $result = $this->generateModel($tableName, $this->schema[$tableName]);
            if ($result) {
                $generated++;
            } else {
                $skipped++;
            }
        }
        
        $this->newLine();
        $this->info("✅ Model generation completed!");
        $this->info("📊 Generated: {$generated} models");
        if ($skipped > 0) {
            $this->info("⏭️  Skipped: {$skipped} models (already exist)");
        }
    }

    /**
     * Check if table is a pivot table
     */
    protected function isPivotTable(string $tableName): bool
    {
        $pivotTables = [
            'role_user',
            'permission_role',
            'attribute_value_product_variant',
            'order_promotion',
            'chat_participants',
        ];
        
        return in_array($tableName, $pivotTables);
    }

    /**
     * Define schema metadata for relationships
     */
    protected function defineSchemaMetadata(): void
    {
        $this->schema = [
            'countries' => [
                'fillable' => ['country_name', 'iso_code_2', 'iso_code_3', 'phone_code', 'currency', 'is_active'],
                'casts' => ['is_active' => 'boolean'],
                'relationships' => [
                    'administrativeDivisions' => ['hasMany', 'AdministrativeDivision'],
                    'userAddresses' => ['hasMany', 'UserAddress'],
                ],
            ],

            'administrative_divisions' => [
                'fillable' => ['country_id', 'parent_id', 'division_name', 'division_type', 'code', 'codename', 'short_codename', 'phone_code'],
                'casts' => [],
                'relationships' => [
                    'country' => ['belongsTo', 'Country'],
                    'parent' => ['belongsTo', 'AdministrativeDivision', 'parent_id'],
                    'children' => ['hasMany', 'AdministrativeDivision', 'parent_id'],
                    'wards' => ['hasMany', 'AdministrativeDivision', 'parent_id', 'division_type=ward'],
                ],
                'scopes' => ['provinces', 'wards'],
            ],

            'roles' => [
                'fillable' => ['role_name', 'description'],
                'relationships' => [
                    'users' => ['belongsToMany', 'User', 'role_user'],
                    'permissions' => ['belongsToMany', 'Permission', 'permission_role'],
                ],
            ],

            'permissions' => [
                'fillable' => ['permission_name', 'description'],
                'relationships' => [
                    'roles' => ['belongsToMany', 'Role', 'permission_role'],
                ],
            ],

            'brands' => [
                'fillable' => ['brand_name', 'slug', 'logo_url', 'description', 'website', 'is_active'],
                'casts' => ['is_active' => 'boolean'],
                'dates' => ['deleted_at'],
                'relationships' => [
                    'products' => ['hasMany', 'Product'],
                ],
                'traits' => ['SoftDeletes'],
            ],

            'categories' => [
                'fillable' => ['parent_id', 'category_name', 'slug', 'description', 'image_url', 'display_order', 'is_active'],
                'casts' => ['is_active' => 'boolean', 'display_order' => 'integer'],
                'dates' => ['deleted_at'],
                'relationships' => [
                    'parent' => ['belongsTo', 'Category', 'parent_id'],
                    'children' => ['hasMany', 'Category', 'parent_id'],
                    'products' => ['hasMany', 'Product'],
                ],
                'traits' => ['SoftDeletes'],
            ],

            'attributes' => [
                'fillable' => ['attribute_name', 'display_name', 'input_type', 'is_required'],
                'casts' => ['is_required' => 'boolean'],
                'relationships' => [
                    'values' => ['hasMany', 'AttributeValue'],
                ],
            ],

            'attribute_values' => [
                'fillable' => ['attribute_id', 'value', 'display_value', 'color_code', 'display_order'],
                'casts' => ['display_order' => 'integer'],
                'relationships' => [
                    'attribute' => ['belongsTo', 'Attribute'],
                    'productVariants' => ['belongsToMany', 'ProductVariant', 'attribute_value_product_variant'],
                ],
            ],

            'users' => [
                'fillable' => ['email', 'phone_number', 'password', 'full_name', 'date_of_birth', 'gender', 'avatar_url', 'bio', 'default_address_id', 'is_active'],
                'hidden' => ['password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes'],
                'casts' => [
                    'email_verified_at' => 'datetime',
                    'phone_verified_at' => 'datetime',
                    'two_factor_confirmed_at' => 'datetime',
                    'last_login_at' => 'datetime',
                    'is_active' => 'boolean',
                    'date_of_birth' => 'date',
                ],
                'relationships' => [
                    'roles' => ['belongsToMany', 'Role', 'role_user'],
                    'addresses' => ['hasMany', 'UserAddress'],
                    'defaultAddress' => ['belongsTo', 'UserAddress', 'default_address_id'],
                    'shops' => ['hasMany', 'Shop', 'owner_id'],
                    'orders' => ['hasMany', 'Order', 'customer_id'],
                    'cartItems' => ['hasMany', 'CartItem'],
                    'wishlists' => ['hasMany', 'Wishlist'],
                    'reviews' => ['hasMany', 'Review'],
                    'notifications' => ['hasMany', 'Notification'],
                    'twoFactorMethods' => ['hasMany', 'TwoFactorAuthentication'],
                ],
                'traits' => ['HasFactory', 'Notifiable'],
            ],

            'user_addresses' => [
                'fillable' => ['user_id', 'address_label', 'recipient_name', 'phone_number', 'address_line1', 'address_line2', 'country_id', 'province_id', 'district_id', 'ward_id', 'postal_code', 'latitude', 'longitude', 'is_default'],
                'casts' => ['is_default' => 'boolean', 'latitude' => 'decimal:8', 'longitude' => 'decimal:8'],
                'relationships' => [
                    'user' => ['belongsTo', 'User'],
                    'country' => ['belongsTo', 'Country'],
                    'province' => ['belongsTo', 'AdministrativeDivision', 'province_id'],
                    'district' => ['belongsTo', 'AdministrativeDivision', 'district_id'],
                    'ward' => ['belongsTo', 'AdministrativeDivision', 'ward_id'],
                ],
            ],

            'shops' => [
                'fillable' => ['owner_id', 'shop_name', 'slug', 'description', 'logo_url', 'banner_url', 'rating', 'total_products', 'total_followers', 'total_orders', 'response_rate', 'response_time_hours', 'is_verified', 'is_active'],
                'casts' => [
                    'rating' => 'decimal:2',
                    'response_rate' => 'decimal:2',
                    'total_products' => 'integer',
                    'total_followers' => 'integer',
                    'total_orders' => 'integer',
                    'response_time_hours' => 'integer',
                    'is_verified' => 'boolean',
                    'is_active' => 'boolean',
                ],
                'relationships' => [
                    'owner' => ['belongsTo', 'User', 'owner_id'],
                    'products' => ['hasMany', 'Product'],
                    'orders' => ['hasMany', 'Order'],
                    'promotions' => ['hasMany', 'Promotion'],
                ],
            ],

            'hubs' => [
                'fillable' => ['hub_name', 'hub_code', 'address', 'ward_id', 'latitude', 'longitude', 'capacity', 'is_active'],
                'casts' => ['latitude' => 'decimal:8', 'longitude' => 'decimal:8', 'capacity' => 'integer', 'is_active' => 'boolean'],
                'relationships' => [
                    'ward' => ['belongsTo', 'AdministrativeDivision', 'ward_id'],
                    'shipmentJourneys' => ['hasMany', 'ShipmentJourney'],
                ],
            ],

            'products' => [
                'fillable' => ['shop_id', 'category_id', 'brand_id', 'seller_id', 'product_name', 'slug', 'description', 'specifications', 'base_price', 'currency', 'weight_grams', 'length_cm', 'width_cm', 'height_cm', 'status', 'total_quantity', 'total_sold', 'rating', 'review_count', 'view_count'],
                'casts' => [
                    'specifications' => 'json',
                    'base_price' => 'decimal:2',
                    'weight_grams' => 'integer',
                    'length_cm' => 'integer',
                    'width_cm' => 'integer',
                    'height_cm' => 'integer',
                    'total_quantity' => 'integer',
                    'total_sold' => 'integer',
                    'rating' => 'decimal:2',
                    'review_count' => 'integer',
                    'view_count' => 'integer',
                ],
                'dates' => ['deleted_at'],
                'relationships' => [
                    'shop' => ['belongsTo', 'Shop'],
                    'category' => ['belongsTo', 'Category'],
                    'brand' => ['belongsTo', 'Brand'],
                    'seller' => ['belongsTo', 'User', 'seller_id'],
                    'images' => ['hasMany', 'ProductImage'],
                    'variants' => ['hasMany', 'ProductVariant'],
                    'reviews' => ['hasMany', 'Review'],
                    'questions' => ['hasMany', 'ProductQuestion'],
                    'views' => ['hasMany', 'ProductView'],
                    'wishlistItems' => ['hasMany', 'WishlistItem'],
                ],
                'traits' => ['SoftDeletes'],
            ],

            'product_images' => [
                'fillable' => ['product_id', 'image_url', 'thumbnail_url', 'alt_text', 'display_order', 'is_primary'],
                'casts' => ['display_order' => 'integer', 'is_primary' => 'boolean'],
                'relationships' => [
                    'product' => ['belongsTo', 'Product'],
                    'variants' => ['hasMany', 'ProductVariant', 'image_id'],
                ],
            ],

            'product_variants' => [
                'fillable' => ['product_id', 'sku', 'variant_name', 'price', 'compare_at_price', 'cost_per_item', 'stock_quantity', 'reserved_quantity', 'image_id', 'weight_grams', 'is_active'],
                'casts' => [
                    'price' => 'decimal:2',
                    'compare_at_price' => 'decimal:2',
                    'cost_per_item' => 'decimal:2',
                    'stock_quantity' => 'integer',
                    'reserved_quantity' => 'integer',
                    'weight_grams' => 'integer',
                    'is_active' => 'boolean',
                ],
                'dates' => ['deleted_at'],
                'relationships' => [
                    'product' => ['belongsTo', 'Product'],
                    'image' => ['belongsTo', 'ProductImage', 'image_id'],
                    'attributeValues' => ['belongsToMany', 'AttributeValue', 'attribute_value_product_variant'],
                    'orderItems' => ['hasMany', 'OrderItem'],
                    'cartItems' => ['hasMany', 'CartItem'],
                    'flashSaleProducts' => ['hasMany', 'FlashSaleProduct'],
                ],
                'traits' => ['SoftDeletes'],
            ],

            'product_questions' => [
                'fillable' => ['product_id', 'user_id', 'question_text', 'is_answered'],
                'casts' => ['is_answered' => 'boolean'],
                'relationships' => [
                    'product' => ['belongsTo', 'Product'],
                    'user' => ['belongsTo', 'User'],
                    'answers' => ['hasMany', 'ProductAnswer', 'question_id'],
                ],
            ],

            'product_answers' => [
                'fillable' => ['question_id', 'user_id', 'answer_text', 'is_seller'],
                'casts' => ['is_seller' => 'boolean'],
                'relationships' => [
                    'question' => ['belongsTo', 'ProductQuestion'],
                    'user' => ['belongsTo', 'User'],
                ],
            ],

            'product_views' => [
                'fillable' => ['product_id', 'user_id', 'session_id', 'ip_address', 'user_agent', 'viewed_at'],
                'casts' => ['viewed_at' => 'datetime'],
                'relationships' => [
                    'product' => ['belongsTo', 'Product'],
                    'user' => ['belongsTo', 'User'],
                ],
            ],

            'flash_sale_events' => [
                'fillable' => ['event_name', 'description', 'start_time', 'end_time', 'is_active'],
                'casts' => ['start_time' => 'datetime', 'end_time' => 'datetime', 'is_active' => 'boolean'],
                'relationships' => [
                    'products' => ['hasMany', 'FlashSaleProduct'],
                ],
            ],

            'flash_sale_products' => [
                'fillable' => ['flash_sale_event_id', 'product_variant_id', 'flash_price', 'quantity_limit', 'sold_quantity', 'max_purchase_per_user'],
                'casts' => ['flash_price' => 'decimal:2', 'quantity_limit' => 'integer', 'sold_quantity' => 'integer', 'max_purchase_per_user' => 'integer'],
                'relationships' => [
                    'flashSaleEvent' => ['belongsTo', 'FlashSaleEvent'],
                    'productVariant' => ['belongsTo', 'ProductVariant'],
                ],
            ],

            'promotions' => [
                'fillable' => ['shop_id', 'promotion_name', 'description', 'promotion_type', 'discount_value', 'min_order_value', 'max_discount_amount', 'usage_limit', 'used_count', 'customer_eligibility', 'start_date', 'end_date', 'is_active'],
                'casts' => [
                    'discount_value' => 'decimal:2',
                    'min_order_value' => 'decimal:2',
                    'max_discount_amount' => 'decimal:2',
                    'usage_limit' => 'integer',
                    'used_count' => 'integer',
                    'customer_eligibility' => 'json',
                    'start_date' => 'datetime',
                    'end_date' => 'datetime',
                    'is_active' => 'boolean',
                ],
                'relationships' => [
                    'shop' => ['belongsTo', 'Shop'],
                    'codes' => ['hasMany', 'PromotionCode'],
                    'orders' => ['belongsToMany', 'Order', 'order_promotion'],
                ],
            ],

            'promotion_codes' => [
                'fillable' => ['promotion_id', 'code', 'usage_limit', 'used_count'],
                'casts' => ['usage_limit' => 'integer', 'used_count' => 'integer'],
                'relationships' => [
                    'promotion' => ['belongsTo', 'Promotion'],
                ],
            ],

            'orders' => [
                'fillable' => ['order_number', 'customer_id', 'shop_id', 'status', 'payment_status', 'subtotal', 'discount_amount', 'shipping_fee', 'tax_amount', 'total_amount', 'currency', 'shipping_address_id', 'payment_method', 'note', 'cancelled_reason', 'cancelled_at', 'confirmed_at', 'delivered_at'],
                'casts' => [
                    'subtotal' => 'decimal:2',
                    'discount_amount' => 'decimal:2',
                    'shipping_fee' => 'decimal:2',
                    'tax_amount' => 'decimal:2',
                    'total_amount' => 'decimal:2',
                    'cancelled_at' => 'datetime',
                    'confirmed_at' => 'datetime',
                    'delivered_at' => 'datetime',
                ],
                'dates' => ['deleted_at'],
                'relationships' => [
                    'customer' => ['belongsTo', 'User', 'customer_id'],
                    'shop' => ['belongsTo', 'Shop'],
                    'shippingAddress' => ['belongsTo', 'UserAddress', 'shipping_address_id'],
                    'items' => ['hasMany', 'OrderItem'],
                    'transactions' => ['hasMany', 'Transaction'],
                    'shippingDetails' => ['hasOne', 'ShippingDetail'],
                    'promotions' => ['belongsToMany', 'Promotion', 'order_promotion'],
                    'returns' => ['hasMany', 'Return'],
                    'disputes' => ['hasMany', 'Dispute'],
                ],
                'traits' => ['SoftDeletes'],
            ],

            'order_items' => [
                'fillable' => ['order_id', 'product_variant_id', 'product_name', 'variant_name', 'sku', 'quantity', 'unit_price', 'subtotal', 'discount_amount', 'total_price'],
                'casts' => [
                    'quantity' => 'integer',
                    'unit_price' => 'decimal:2',
                    'subtotal' => 'decimal:2',
                    'discount_amount' => 'decimal:2',
                    'total_price' => 'decimal:2',
                ],
                'relationships' => [
                    'order' => ['belongsTo', 'Order'],
                    'productVariant' => ['belongsTo', 'ProductVariant'],
                    'review' => ['hasOne', 'Review'],
                    'returnItems' => ['hasMany', 'ReturnItem'],
                ],
            ],

            'transactions' => [
                'fillable' => ['order_id', 'transaction_number', 'payment_method', 'amount', 'currency', 'status', 'gateway_transaction_id', 'gateway_response', 'paid_at'],
                'casts' => [
                    'amount' => 'decimal:2',
                    'gateway_response' => 'json',
                    'paid_at' => 'datetime',
                ],
                'relationships' => [
                    'order' => ['belongsTo', 'Order'],
                ],
            ],

            'shipping_details' => [
                'fillable' => ['order_id', 'shipper_id', 'tracking_number', 'carrier', 'status', 'estimated_delivery', 'actual_delivery', 'notes'],
                'casts' => [
                    'estimated_delivery' => 'datetime',
                    'actual_delivery' => 'datetime',
                ],
                'relationships' => [
                    'order' => ['belongsTo', 'Order'],
                    'shipper' => ['belongsTo', 'User', 'shipper_id'],
                    'journeys' => ['hasMany', 'ShipmentJourney'],
                ],
            ],

            'cart_items' => [
                'fillable' => ['user_id', 'product_variant_id', 'quantity'],
                'casts' => ['quantity' => 'integer'],
                'relationships' => [
                    'user' => ['belongsTo', 'User'],
                    'productVariant' => ['belongsTo', 'ProductVariant'],
                ],
            ],

            'wishlists' => [
                'fillable' => ['user_id', 'name', 'is_public'],
                'casts' => ['is_public' => 'boolean'],
                'relationships' => [
                    'user' => ['belongsTo', 'User'],
                    'items' => ['hasMany', 'WishlistItem'],
                ],
            ],

            'wishlist_items' => [
                'fillable' => ['wishlist_id', 'product_id'],
                'relationships' => [
                    'wishlist' => ['belongsTo', 'Wishlist'],
                    'product' => ['belongsTo', 'Product'],
                ],
            ],

            'reviews' => [
                'fillable' => ['product_id', 'user_id', 'order_item_id', 'rating', 'title', 'comment', 'is_verified_purchase', 'helpful_count'],
                'casts' => ['rating' => 'integer', 'is_verified_purchase' => 'boolean', 'helpful_count' => 'integer'],
                'dates' => ['deleted_at'],
                'relationships' => [
                    'product' => ['belongsTo', 'Product'],
                    'user' => ['belongsTo', 'User'],
                    'orderItem' => ['belongsTo', 'OrderItem'],
                    'media' => ['hasMany', 'ReviewMedia'],
                ],
                'traits' => ['SoftDeletes'],
            ],

            'review_media' => [
                'fillable' => ['review_id', 'media_type', 'media_url', 'thumbnail_url', 'display_order'],
                'casts' => ['display_order' => 'integer'],
                'relationships' => [
                    'review' => ['belongsTo', 'Review'],
                ],
            ],

            'shipper_profiles' => [
                'fillable' => ['user_id', 'vehicle_type', 'vehicle_number', 'license_number', 'current_hub_id', 'rating', 'total_deliveries', 'is_active'],
                'casts' => ['rating' => 'decimal:2', 'total_deliveries' => 'integer', 'is_active' => 'boolean'],
                'relationships' => [
                    'user' => ['belongsTo', 'User'],
                    'currentHub' => ['belongsTo', 'Hub', 'current_hub_id'],
                    'shippingDetails' => ['hasMany', 'ShippingDetail', 'shipper_id'],
                    'ratings' => ['hasMany', 'ShipperRating'],
                ],
            ],

            'shipment_journeys' => [
                'fillable' => ['shipping_detail_id', 'hub_id', 'status', 'notes', 'latitude', 'longitude'],
                'casts' => ['latitude' => 'decimal:8', 'longitude' => 'decimal:8'],
                'relationships' => [
                    'shippingDetail' => ['belongsTo', 'ShippingDetail'],
                    'hub' => ['belongsTo', 'Hub'],
                ],
            ],

            'shipper_ratings' => [
                'fillable' => ['shipper_id', 'order_id', 'rating', 'comment'],
                'casts' => ['rating' => 'integer'],
                'relationships' => [
                    'shipper' => ['belongsTo', 'User', 'shipper_id'],
                    'order' => ['belongsTo', 'Order'],
                ],
            ],

            'returns' => [
                'fillable' => ['order_id', 'return_number', 'reason', 'status', 'refund_amount', 'approved_at', 'completed_at'],
                'casts' => [
                    'refund_amount' => 'decimal:2',
                    'approved_at' => 'datetime',
                    'completed_at' => 'datetime',
                ],
                'dates' => ['deleted_at'],
                'relationships' => [
                    'order' => ['belongsTo', 'Order'],
                    'items' => ['hasMany', 'ReturnItem'],
                ],
                'traits' => ['SoftDeletes'],
            ],

            'return_items' => [
                'fillable' => ['return_id', 'order_item_id', 'quantity', 'reason'],
                'casts' => ['quantity' => 'integer'],
                'relationships' => [
                    'return' => ['belongsTo', 'Return'],
                    'orderItem' => ['belongsTo', 'OrderItem'],
                ],
            ],

            'disputes' => [
                'fillable' => ['order_id', 'customer_id', 'shop_id', 'subject', 'description', 'status', 'resolution', 'resolved_at'],
                'casts' => ['resolved_at' => 'datetime'],
                'relationships' => [
                    'order' => ['belongsTo', 'Order'],
                    'customer' => ['belongsTo', 'User', 'customer_id'],
                    'shop' => ['belongsTo', 'Shop'],
                    'messages' => ['hasMany', 'DisputeMessage'],
                ],
            ],

            'dispute_messages' => [
                'fillable' => ['dispute_id', 'sender_id', 'message', 'attachment_url'],
                'relationships' => [
                    'dispute' => ['belongsTo', 'Dispute'],
                    'sender' => ['belongsTo', 'User', 'sender_id'],
                ],
            ],

            'chat_rooms' => [
                'fillable' => ['room_type', 'last_message_at'],
                'casts' => ['last_message_at' => 'datetime'],
                'relationships' => [
                    'participants' => ['belongsToMany', 'User', 'chat_participants'],
                    'messages' => ['hasMany', 'ChatMessage'],
                ],
            ],

            'chat_messages' => [
                'fillable' => ['chat_room_id', 'sender_id', 'message', 'attachment_url', 'is_read'],
                'casts' => ['is_read' => 'boolean'],
                'relationships' => [
                    'chatRoom' => ['belongsTo', 'ChatRoom'],
                    'sender' => ['belongsTo', 'User', 'sender_id'],
                ],
            ],

            'notifications' => [
                'fillable' => ['user_id', 'type', 'title', 'message', 'data', 'read_at'],
                'casts' => ['data' => 'json', 'read_at' => 'datetime'],
                'relationships' => [
                    'user' => ['belongsTo', 'User'],
                ],
            ],

            'user_events' => [
                'fillable' => ['user_id', 'event_type', 'event_data', 'ip_address', 'user_agent'],
                'casts' => ['event_data' => 'json'],
                'relationships' => [
                    'user' => ['belongsTo', 'User'],
                ],
            ],

            'search_histories' => [
                'fillable' => ['user_id', 'search_query', 'filters', 'result_count'],
                'casts' => ['filters' => 'json', 'result_count' => 'integer'],
                'relationships' => [
                    'user' => ['belongsTo', 'User'],
                ],
            ],

            'user_preferences' => [
                'fillable' => ['user_id', 'preference_key', 'preference_value'],
                'casts' => ['preference_value' => 'json'],
                'relationships' => [
                    'user' => ['belongsTo', 'User'],
                ],
            ],

            'analytics_reports' => [
                'fillable' => ['report_type', 'entity_type', 'entity_id', 'metrics', 'period_start', 'period_end'],
                'casts' => [
                    'metrics' => 'json',
                    'period_start' => 'datetime',
                    'period_end' => 'datetime',
                ],
            ],

            'international_addresses' => [
                'fillable' => ['addressable_type', 'addressable_id', 'country_id', 'address_line1', 'address_line2', 'city', 'state', 'postal_code'],
                'relationships' => [
                    'addressable' => ['morphTo'],
                    'country' => ['belongsTo', 'Country'],
                ],
            ],

            'two_factor_authentications' => [
                'fillable' => ['user_id', 'method', 'identifier', 'secret', 'backup_codes', 'is_active'],
                'casts' => ['backup_codes' => 'json', 'is_active' => 'boolean'],
                'relationships' => [
                    'user' => ['belongsTo', 'User'],
                    'challenges' => ['hasMany', 'TwoFactorChallenge'],
                ],
            ],

            'two_factor_challenges' => [
                'fillable' => ['two_factor_authentication_id', 'code', 'expires_at', 'verified_at', 'ip_address', 'user_agent'],
                'casts' => ['expires_at' => 'datetime', 'verified_at' => 'datetime'],
                'relationships' => [
                    'twoFactorAuthentication' => ['belongsTo', 'TwoFactorAuthentication'],
                ],
            ],

            'two_factor_trusted_devices' => [
                'fillable' => ['user_id', 'device_identifier', 'device_name', 'ip_address', 'user_agent', 'expires_at', 'last_used_at'],
                'casts' => ['expires_at' => 'datetime', 'last_used_at' => 'datetime'],
                'relationships' => [
                    'user' => ['belongsTo', 'User'],
                ],
            ],
        ];
    }

    /**
     * Generate model for a table
     */
    protected function generateModel(string $tableName, array $definition): bool
    {
        $modelName = Str::studly(Str::singular($tableName));
        $filePath = app_path("Models/{$modelName}.php");
        
        // Check if file exists
        if (File::exists($filePath) && !$this->option('force')) {
            $this->warn("⏭️  Skipped: {$modelName} (already exists, use --force to overwrite)");
            return false;
        }
        
        $content = $this->generateModelContent($tableName, $modelName, $definition);
        File::put($filePath, $content);
        
        $this->info("✅ Created: {$modelName}");
        return true;
    }

    /**
     * Generate model file content
     */
    protected function generateModelContent(string $tableName, string $modelName, array $definition): string
    {
        $namespace = 'App\Models';
        $uses = ['Illuminate\Database\Eloquent\Factories\HasFactory', 'Illuminate\Database\Eloquent\Model'];
        $traits = ['HasFactory'];
        
        // Add SoftDeletes if needed
        if (isset($definition['traits']) && in_array('SoftDeletes', $definition['traits'])) {
            $uses[] = 'Illuminate\Database\Eloquent\SoftDeletes';
            $traits[] = 'SoftDeletes';
        }
        
        // Add Notifiable if needed
        if (isset($definition['traits']) && in_array('Notifiable', $definition['traits'])) {
            $uses[] = 'Illuminate\Notifications\Notifiable';
            $traits[] = 'Notifiable';
        }
        
        $usesStr = implode(";\n", array_map(fn($use) => "use {$use}", $uses)) . ';';
        $traitsStr = 'use ' . implode(', ', $traits) . ';';
        
        // Generate fillable
        $fillable = $definition['fillable'] ?? [];
        $fillableStr = $this->formatArrayProperty($fillable);
        
        // Generate hidden (for sensitive fields)
        $hidden = $definition['hidden'] ?? [];
        $hiddenStr = !empty($hidden) ? "\n\n    /**\n     * The attributes that should be hidden for serialization.\n     *\n     * @var array<int, string>\n     */\n    protected \$hidden = " . $this->formatArrayProperty($hidden) . ';' : '';
        
        // Generate casts
        $casts = $definition['casts'] ?? [];
        $castsStr = !empty($casts) ? "\n\n    /**\n     * The attributes that should be cast.\n     *\n     * @var array<string, string>\n     */\n    protected \$casts = " . $this->formatArrayWithKeys($casts) . ';' : '';
        
        // Generate relationships
        $relationshipsStr = $this->generateRelationships($definition['relationships'] ?? []);
        
        // Generate scopes
        $scopesStr = $this->generateScopes($definition['scopes'] ?? []);
        
        return <<<PHP
<?php

namespace {$namespace};

{$usesStr}

class {$modelName} extends Model
{
    {$traitsStr}

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected \$table = '{$tableName}';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected \$fillable = {$fillableStr};{$hiddenStr}{$castsStr}{$relationshipsStr}{$scopesStr}
}

PHP;
    }

    /**
     * Format array property
     */
    protected function formatArrayProperty(array $items): string
    {
        if (empty($items)) {
            return '[]';
        }
        
        $formatted = array_map(fn($item) => "'{$item}'", $items);
        return "[\n        " . implode(",\n        ", $formatted) . ",\n    ]";
    }

    /**
     * Format associative array
     */
    protected function formatArrayWithKeys(array $items): string
    {
        if (empty($items)) {
            return '[]';
        }
        
        $formatted = [];
        foreach ($items as $key => $value) {
            if (is_string($value)) {
                $formatted[] = "'{$key}' => '{$value}'";
            } else {
                $formatted[] = "'{$key}' => {$value}";
            }
        }
        
        return "[\n        " . implode(",\n        ", $formatted) . ",\n    ]";
    }

    /**
     * Generate relationship methods
     */
    protected function generateRelationships(array $relationships): string
    {
        if (empty($relationships)) {
            return '';
        }
        
        $methods = [];
        
        foreach ($relationships as $name => $config) {
            $type = $config[0];
            $relatedModel = $config[1] ?? null;
            $foreignKey = $config[2] ?? null;
            $condition = $config[3] ?? null;
            
            $methodName = Str::camel($name);
            $relationshipType = Str::camel($type);
            
            // Handle morphTo (no related model needed)
            if ($type === 'morphTo') {
                $methodBody = "        return \$this->morphTo();";
            } else {
                $params = "\\App\\Models\\{$relatedModel}::class";
                
                if ($foreignKey) {
                    $params .= ", '{$foreignKey}'";
                }
                
                $methodBody = "        return \$this->{$relationshipType}({$params})";
                
                // Add where clause for conditions
                if ($condition) {
                    [$field, $value] = explode('=', $condition);
                    $methodBody .= "\n            ->where('{$field}', '{$value}')";
                }
                
                $methodBody .= ';';
            }
            
            $methods[] = <<<PHP

    /**
     * Get the {$name} relationship.
     */
    public function {$methodName}()
    {
{$methodBody}
    }
PHP;
        }
        
        return "\n" . implode("\n", $methods);
    }

    /**
     * Generate scope methods
     */
    protected function generateScopes(array $scopes): string
    {
        if (empty($scopes)) {
            return '';
        }
        
        $methods = [];
        
        foreach ($scopes as $scope) {
            $scopeName = Str::studly($scope);
            $methodName = "scope{$scopeName}";
            
            // Auto-generate common scopes
            if ($scope === 'provinces') {
                $methods[] = <<<PHP

    /**
     * Scope a query to only include provinces.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  \$query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function {$methodName}(\$query)
    {
        return \$query->where('division_type', 'province');
    }
PHP;
            } elseif ($scope === 'wards') {
                $methods[] = <<<PHP

    /**
     * Scope a query to only include wards.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  \$query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function {$methodName}(\$query)
    {
        return \$query->where('division_type', 'ward');
    }
PHP;
            }
        }
        
        return "\n" . implode("\n", $methods);
    }
}
