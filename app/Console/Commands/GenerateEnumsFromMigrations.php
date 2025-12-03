<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GenerateEnumsFromMigrations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:generate-enums 
                            {--force : Overwrite existing enum files}
                            {--tables= : Comma-separated list of specific tables to generate enums for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate PHP enum classes from database migration enum columns';

    /**
     * Mapping of table names to enum configurations
     * 
     * @var array
     */
    protected $enumDefinitions = [];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Starting enum generation from migrations...');
        $this->newLine();

        $this->defineEnumMetadata();

        $tables = $this->option('tables') 
            ? explode(',', $this->option('tables')) 
            : array_keys($this->enumDefinitions);

        $generated = 0;
        $skipped = 0;

        foreach ($tables as $table) {
            $table = trim($table);
            
            if (!isset($this->enumDefinitions[$table])) {
                $this->warn("⚠️  No enum definitions found for table: {$table}");
                continue;
            }

            foreach ($this->enumDefinitions[$table] as $enumConfig) {
                $result = $this->generateEnum($table, $enumConfig);
                
                if ($result) {
                    $generated++;
                } else {
                    $skipped++;
                }
            }
        }

        $this->newLine();
        $this->info("✅ Enum generation completed!");
        $this->info("📊 Generated: {$generated} enums");
        
        if ($skipped > 0) {
            $this->info("⏭️  Skipped: {$skipped} enums");
        }

        return Command::SUCCESS;
    }

    /**
     * Define enum metadata for all tables with enum columns
     */
    protected function defineEnumMetadata()
    {
        $this->enumDefinitions = [
            'administrative_divisions' => [
                [
                    'column' => 'division_type',
                    'enum_name' => 'DivisionType',
                    'cases' => [
                        'PROVINCE' => 'province',
                        'WARD' => 'ward',
                    ],
                    'labels' => [
                        'PROVINCE' => 'Tỉnh/Thành phố',
                        'WARD' => 'Xã/Phường',
                    ],
                ],
            ],
            'attributes' => [
                [
                    'column' => 'input_type',
                    'enum_name' => 'AttributeInputType',
                    'cases' => [
                        'SELECT' => 'select',
                        'COLOR' => 'color',
                        'TEXT' => 'text',
                    ],
                    'labels' => [
                        'SELECT' => 'Lựa chọn',
                        'COLOR' => 'Màu sắc',
                        'TEXT' => 'Văn bản',
                    ],
                ],
            ],
            'users' => [
                [
                    'column' => 'gender',
                    'enum_name' => 'Gender',
                    'cases' => [
                        'MALE' => 'male',
                        'FEMALE' => 'female',
                        'OTHER' => 'other',
                    ],
                    'labels' => [
                        'MALE' => 'Nam',
                        'FEMALE' => 'Nữ',
                        'OTHER' => 'Khác',
                    ],
                ],
            ],
            'products' => [
                [
                    'column' => 'status',
                    'enum_name' => 'ProductStatus',
                    'cases' => [
                        'DRAFT' => 'draft',
                        'ACTIVE' => 'active',
                        'INACTIVE' => 'inactive',
                        'OUT_OF_STOCK' => 'out_of_stock',
                    ],
                    'labels' => [
                        'DRAFT' => 'Nháp',
                        'ACTIVE' => 'Đang hoạt động',
                        'INACTIVE' => 'Tạm ngưng',
                        'OUT_OF_STOCK' => 'Hết hàng',
                    ],
                ],
            ],
            'promotions' => [
                [
                    'column' => 'promotion_type',
                    'enum_name' => 'PromotionType',
                    'cases' => [
                        'PERCENTAGE' => 'percentage',
                        'FIXED_AMOUNT' => 'fixed_amount',
                        'FREE_SHIPPING' => 'free_shipping',
                        'BUY_X_GET_Y' => 'buy_x_get_y',
                    ],
                    'labels' => [
                        'PERCENTAGE' => 'Giảm theo phần trăm',
                        'FIXED_AMOUNT' => 'Giảm giá cố định',
                        'FREE_SHIPPING' => 'Miễn phí vận chuyển',
                        'BUY_X_GET_Y' => 'Mua X tặng Y',
                    ],
                ],
            ],
            'orders' => [
                [
                    'column' => 'status',
                    'enum_name' => 'OrderStatus',
                    'cases' => [
                        'PENDING' => 'pending',
                        'CONFIRMED' => 'confirmed',
                        'PROCESSING' => 'processing',
                        'SHIPPING' => 'shipping',
                        'DELIVERED' => 'delivered',
                        'CANCELLED' => 'cancelled',
                        'REFUNDED' => 'refunded',
                    ],
                    'labels' => [
                        'PENDING' => 'Chờ xác nhận',
                        'CONFIRMED' => 'Đã xác nhận',
                        'PROCESSING' => 'Đang xử lý',
                        'SHIPPING' => 'Đang giao hàng',
                        'DELIVERED' => 'Đã giao',
                        'CANCELLED' => 'Đã hủy',
                        'REFUNDED' => 'Đã hoàn tiền',
                    ],
                ],
                [
                    'column' => 'payment_status',
                    'enum_name' => 'PaymentStatus',
                    'cases' => [
                        'UNPAID' => 'unpaid',
                        'PAID' => 'paid',
                        'PARTIALLY_REFUNDED' => 'partially_refunded',
                        'REFUNDED' => 'refunded',
                    ],
                    'labels' => [
                        'UNPAID' => 'Chưa thanh toán',
                        'PAID' => 'Đã thanh toán',
                        'PARTIALLY_REFUNDED' => 'Hoàn tiền một phần',
                        'REFUNDED' => 'Đã hoàn tiền',
                    ],
                ],
                [
                    'column' => 'payment_method',
                    'enum_name' => 'PaymentMethod',
                    'cases' => [
                        'COD' => 'cod',
                        'CREDIT_CARD' => 'credit_card',
                        'E_WALLET' => 'e_wallet',
                        'BANK_TRANSFER' => 'bank_transfer',
                    ],
                    'labels' => [
                        'COD' => 'Thanh toán khi nhận hàng',
                        'CREDIT_CARD' => 'Thẻ tín dụng',
                        'E_WALLET' => 'Ví điện tử',
                        'BANK_TRANSFER' => 'Chuyển khoản ngân hàng',
                    ],
                ],
            ],
            'transactions' => [
                [
                    'column' => 'payment_method',
                    'enum_name' => 'PaymentMethod',
                    'cases' => [
                        'COD' => 'cod',
                        'CREDIT_CARD' => 'credit_card',
                        'E_WALLET' => 'e_wallet',
                        'BANK_TRANSFER' => 'bank_transfer',
                    ],
                    'labels' => [
                        'COD' => 'Thanh toán khi nhận hàng',
                        'CREDIT_CARD' => 'Thẻ tín dụng',
                        'E_WALLET' => 'Ví điện tử',
                        'BANK_TRANSFER' => 'Chuyển khoản ngân hàng',
                    ],
                ],
                [
                    'column' => 'status',
                    'enum_name' => 'TransactionStatus',
                    'cases' => [
                        'PENDING' => 'pending',
                        'SUCCESS' => 'success',
                        'FAILED' => 'failed',
                    ],
                    'labels' => [
                        'PENDING' => 'Đang xử lý',
                        'SUCCESS' => 'Thành công',
                        'FAILED' => 'Thất bại',
                    ],
                ],
            ],
            'shipping_details' => [
                [
                    'column' => 'status',
                    'enum_name' => 'ShippingStatus',
                    'cases' => [
                        'PENDING' => 'pending',
                        'PICKED_UP' => 'picked_up',
                        'IN_TRANSIT' => 'in_transit',
                        'OUT_FOR_DELIVERY' => 'out_for_delivery',
                        'DELIVERED' => 'delivered',
                        'FAILED' => 'failed',
                        'RETURNED' => 'returned',
                    ],
                    'labels' => [
                        'PENDING' => 'Chờ lấy hàng',
                        'PICKED_UP' => 'Đã lấy hàng',
                        'IN_TRANSIT' => 'Đang vận chuyển',
                        'OUT_FOR_DELIVERY' => 'Đang giao hàng',
                        'DELIVERED' => 'Đã giao',
                        'FAILED' => 'Giao thất bại',
                        'RETURNED' => 'Đã trả lại',
                    ],
                ],
            ],
            'review_media' => [
                [
                    'column' => 'media_type',
                    'enum_name' => 'ReviewMediaType',
                    'cases' => [
                        'IMAGE' => 'image',
                        'VIDEO' => 'video',
                    ],
                    'labels' => [
                        'IMAGE' => 'Hình ảnh',
                        'VIDEO' => 'Video',
                    ],
                ],
            ],
            'shipper_profiles' => [
                [
                    'column' => 'vehicle_type',
                    'enum_name' => 'VehicleType',
                    'cases' => [
                        'MOTORCYCLE' => 'motorcycle',
                        'CAR' => 'car',
                        'TRUCK' => 'truck',
                    ],
                    'labels' => [
                        'MOTORCYCLE' => 'Xe máy',
                        'CAR' => 'Ô tô',
                        'TRUCK' => 'Xe tải',
                    ],
                ],
            ],
            'shipment_journeys' => [
                [
                    'column' => 'status',
                    'enum_name' => 'ShipmentJourneyStatus',
                    'cases' => [
                        'PICKED_UP' => 'picked_up',
                        'AT_HUB' => 'at_hub',
                        'IN_TRANSIT' => 'in_transit',
                        'OUT_FOR_DELIVERY' => 'out_for_delivery',
                        'DELIVERED' => 'delivered',
                        'FAILED' => 'failed',
                    ],
                    'labels' => [
                        'PICKED_UP' => 'Đã lấy hàng',
                        'AT_HUB' => 'Tại trung tâm',
                        'IN_TRANSIT' => 'Đang vận chuyển',
                        'OUT_FOR_DELIVERY' => 'Đang giao hàng',
                        'DELIVERED' => 'Đã giao',
                        'FAILED' => 'Thất bại',
                    ],
                ],
            ],
            'returns' => [
                [
                    'column' => 'status',
                    'enum_name' => 'ReturnStatus',
                    'cases' => [
                        'REQUESTED' => 'requested',
                        'APPROVED' => 'approved',
                        'REJECTED' => 'rejected',
                        'RECEIVED' => 'received',
                        'REFUNDED' => 'refunded',
                    ],
                    'labels' => [
                        'REQUESTED' => 'Yêu cầu trả hàng',
                        'APPROVED' => 'Đã chấp nhận',
                        'REJECTED' => 'Đã từ chối',
                        'RECEIVED' => 'Đã nhận hàng',
                        'REFUNDED' => 'Đã hoàn tiền',
                    ],
                ],
            ],
            'disputes' => [
                [
                    'column' => 'status',
                    'enum_name' => 'DisputeStatus',
                    'cases' => [
                        'OPEN' => 'open',
                        'IN_REVIEW' => 'in_review',
                        'RESOLVED' => 'resolved',
                        'CLOSED' => 'closed',
                    ],
                    'labels' => [
                        'OPEN' => 'Đang mở',
                        'IN_REVIEW' => 'Đang xem xét',
                        'RESOLVED' => 'Đã giải quyết',
                        'CLOSED' => 'Đã đóng',
                    ],
                ],
            ],
            'chat_rooms' => [
                [
                    'column' => 'room_type',
                    'enum_name' => 'ChatRoomType',
                    'cases' => [
                        'CUSTOMER_SELLER' => 'customer_seller',
                        'CUSTOMER_SUPPORT' => 'customer_support',
                    ],
                    'labels' => [
                        'CUSTOMER_SELLER' => 'Khách hàng - Người bán',
                        'CUSTOMER_SUPPORT' => 'Khách hàng - Hỗ trợ',
                    ],
                ],
            ],
            'chat_messages' => [
                [
                    'column' => 'message_type',
                    'enum_name' => 'MessageType',
                    'cases' => [
                        'TEXT' => 'text',
                        'IMAGE' => 'image',
                        'PRODUCT_LINK' => 'product_link',
                    ],
                    'labels' => [
                        'TEXT' => 'Văn bản',
                        'IMAGE' => 'Hình ảnh',
                        'PRODUCT_LINK' => 'Link sản phẩm',
                    ],
                ],
            ],
            'user_preferences' => [
                [
                    'column' => 'theme',
                    'enum_name' => 'Theme',
                    'cases' => [
                        'LIGHT' => 'light',
                        'DARK' => 'dark',
                        'AUTO' => 'auto',
                    ],
                    'labels' => [
                        'LIGHT' => 'Sáng',
                        'DARK' => 'Tối',
                        'AUTO' => 'Tự động',
                    ],
                ],
            ],
            'two_factor_authentications' => [
                [
                    'column' => 'method',
                    'enum_name' => 'TwoFactorMethod',
                    'cases' => [
                        'AUTHENTICATOR' => 'authenticator',
                        'SMS' => 'sms',
                        'EMAIL' => 'email',
                    ],
                    'labels' => [
                        'AUTHENTICATOR' => 'Ứng dụng xác thực',
                        'SMS' => 'Tin nhắn SMS',
                        'EMAIL' => 'Email',
                    ],
                ],
            ],
            'two_factor_challenges' => [
                [
                    'column' => 'method',
                    'enum_name' => 'TwoFactorChallengeMethod',
                    'cases' => [
                        'AUTHENTICATOR' => 'authenticator',
                        'SMS' => 'sms',
                        'EMAIL' => 'email',
                        'BACKUP_CODE' => 'backup_code',
                    ],
                    'labels' => [
                        'AUTHENTICATOR' => 'Ứng dụng xác thực',
                        'SMS' => 'Tin nhắn SMS',
                        'EMAIL' => 'Email',
                        'BACKUP_CODE' => 'Mã dự phòng',
                    ],
                ],
            ],
        ];
    }

    /**
     * Generate enum class for a specific table and column
     */
    protected function generateEnum(string $table, array $enumConfig): bool
    {
        $enumName = $enumConfig['enum_name'];
        $enumPath = app_path("Enums/{$enumName}.php");

        // Check if file exists and force option is not set
        if (File::exists($enumPath) && !$this->option('force')) {
            $this->warn("⏭️  Skipped: {$enumName} (already exists)");
            return false;
        }

        $content = $this->generateEnumContent($enumName, $enumConfig);

        // Create directory if it doesn't exist
        $directory = dirname($enumPath);
        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        File::put($enumPath, $content);

        $this->info("✅ Created: {$enumName}");
        
        // Update model with enum cast
        $this->updateModelWithEnum($table, $enumConfig);

        return true;
    }

    /**
     * Generate enum file content
     */
    protected function generateEnumContent(string $enumName, array $enumConfig): string
    {
        $cases = $enumConfig['cases'];
        $labels = $enumConfig['labels'] ?? [];

        $casesCode = '';
        foreach ($cases as $constant => $value) {
            $casesCode .= "    case {$constant} = '{$value}';\n";
        }

        $labelsArray = '';
        $labelMethod = '';
        
        if (!empty($labels)) {
            $labelsArray = "    private const LABELS = [\n";
            foreach ($labels as $constant => $label) {
                $labelsArray .= "        self::{$constant}->value => '{$label}',\n";
            }
            $labelsArray .= "    ];\n\n";

            $labelMethod = "    /**\n";
            $labelMethod .= "     * Get the label for the enum case\n";
            $labelMethod .= "     */\n";
            $labelMethod .= "    public function label(): string\n";
            $labelMethod .= "    {\n";
            $labelMethod .= "        return self::LABELS[\$this->value];\n";
            $labelMethod .= "    }\n\n";

            $labelMethod .= "    /**\n";
            $labelMethod .= "     * Get all enum values with their labels\n";
            $labelMethod .= "     */\n";
            $labelMethod .= "    public static function options(): array\n";
            $labelMethod .= "    {\n";
            $labelMethod .= "        return array_map(\n";
            $labelMethod .= "            fn(self \$enum) => ['value' => \$enum->value, 'label' => \$enum->label()],\n";
            $labelMethod .= "            self::cases()\n";
            $labelMethod .= "        );\n";
            $labelMethod .= "    }\n";
        }

        return <<<PHP
<?php

namespace App\Enums;

enum {$enumName}: string
{
{$casesCode}
{$labelsArray}{$labelMethod}}

PHP;
    }

    /**
     * Update model file to use enum cast
     */
    protected function updateModelWithEnum(string $table, array $enumConfig): void
    {
        $modelName = Str::studly(Str::singular($table));
        $modelPath = app_path("Models/{$modelName}.php");
        
        if (!File::exists($modelPath)) {
            $this->warn("   ⚠️  Model not found: {$modelName}");
            return;
        }

        $content = File::get($modelPath);
        $enumName = $enumConfig['enum_name'];
        $column = $enumConfig['column'];

        // Check if enum import already exists
        $enumImport = "use App\\Enums\\{$enumName};";
        if (!str_contains($content, $enumImport)) {
            // Add import after namespace
            $content = preg_replace(
                '/(namespace App\\\\Models;)/m',
                "$1\n\n{$enumImport}",
                $content
            );
        }

        // Update casts array to use enum
        if (preg_match('/protected \$casts = \[(.*?)\];/s', $content, $matches)) {
            $castsContent = $matches[1];
            
            // Check if column already has a cast
            if (preg_match("/'{$column}'\s*=>\s*'[^']+'/", $castsContent)) {
                // Replace existing cast
                $content = preg_replace(
                    "/'{$column}'\s*=>\s*'[^']+'/",
                    "'{$column}' => {$enumName}::class",
                    $content
                );
            } else {
                // Add new cast
                $newCast = "'{$column}' => {$enumName}::class,";
                $content = preg_replace(
                    '/(protected \$casts = \[)/s',
                    "$1\n        {$newCast}",
                    $content
                );
            }
        } else {
            // Create new casts array
            $castsArray = "\n    /**\n";
            $castsArray .= "     * The attributes that should be cast.\n";
            $castsArray .= "     *\n";
            $castsArray .= "     * @var array<string, string>\n";
            $castsArray .= "     */\n";
            $castsArray .= "    protected \$casts = [\n";
            $castsArray .= "        '{$column}' => {$enumName}::class,\n";
            $castsArray .= "    ];\n";

            // Add after fillable array
            $content = preg_replace(
                '/(protected \$fillable = \[.*?\];)/s',
                "$1\n{$castsArray}",
                $content
            );
        }

        File::put($modelPath, $content);
        $this->line("   📝 Updated model: {$modelName}");
    }
}
