<?php
// database/migrations/2025_11_18_000026_create_shipping_fee_rules_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_fee_rules', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->string('name'); // VD: "Freeship toàn quốc đơn từ 300k"
            $table->text('description')->nullable();

            // Điều kiện áp dụng
            $table->decimal('min_order_amount', 15, 2)->nullable(); // Từ bao nhiêu tiền
            $table->decimal('max_order_amount', 15, 2)->nullable(); // Đến bao nhiêu tiền (nullable = không giới hạn)

            // Phí vận chuyển áp dụng
            $table->decimal('shipping_fee', 15, 2)->default(0); // 0 = freeship

            // Áp dụng cho khu vực nào?
            $table->json('provinces')->nullable(); // ["Hà Nội", "TP.HCM"] hoặc null = toàn quốc
            $table->boolean('apply_to_all_provinces')->default(true);

            // Ưu tiên (càng cao càng được áp dụng trước)
            $table->integer('priority')->default(0);

            $table->boolean('is_active')->default(true);
            $table->boolean('is_freeship_rule')->default(false); // Dùng để hiển thị badge

            $table->timestamps();

            $table->index(['is_active', 'priority']);
            $table->index('min_order_amount');
        });

        // Seed 4 quy tắc mặc định (theo đúng yêu cầu thực tế)
        $this->seedDefaultRules();
    }

    private function seedDefaultRules(): void
    {
        DB::table('shipping_fee_rules')->insert([
            [
                'name' => 'Freeship toàn quốc đơn từ 500.000đ',
                'description' => 'Miễn phí vận chuyển cho mọi đơn hàng từ 500k trở lên',
                'min_order_amount' => 500000,
                'max_order_amount' => null,
                'shipping_fee' => 0,
                'apply_to_all_provinces' => true,
                'priority' => 100,
                'is_active' => true,
                'is_freeship_rule' => true,
            ],
            [
                'name' => 'Freeship nội thành Hà Nội & TP.HCM đơn từ 300k',
                'description' => 'Chỉ áp dụng cho 2 thành phố lớn',
                'min_order_amount' => 300000,
                'max_order_amount' => 499999,
                'shipping_fee' => 0,
                'provinces' => json_encode(['Hà Nội', 'TP. Hồ Chí Minh']),
                'apply_to_all_provinces' => false,
                'priority' => 90,
                'is_active' => true,
                'is_freeship_rule' => true,
            ],
            [
                'name' => 'Phí ship mặc định toàn quốc',
                'description' => 'Áp dụng khi không đủ điều kiện freeship',
                'min_order_amount' => 0,
                'max_order_amount' => 299999,
                'shipping_fee' => 35000,
                'apply_to_all_provinces' => true,
                'priority' => 10,
                'is_active' => true,
                'is_freeship_rule' => false,
            ],
            [
                'name' => 'Phí ship vùng sâu vùng xa',
                'description' => 'Áp dụng cho một số huyện xa',
                'min_order_amount' => 0,
                'max_order_amount' => null,
                'shipping_fee' => 50000,
                'provinces' => json_encode(['Lai Châu', 'Điện Biên', 'Cà Mau', 'Bạc Liêu']),
                'apply_to_all_provinces' => false,
                'priority' => 20,
                'is_active' => true,
                'is_freeship_rule' => false,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_fee_rules');
    }
};