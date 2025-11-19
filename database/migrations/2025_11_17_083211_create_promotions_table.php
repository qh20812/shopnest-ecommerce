<?php
// database/migrations/2025_11_18_000012_create_promotions_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Mã giảm giá (VD: SALE2025, FREESHIP100K)
            $table->string('code', 50)->unique();
            $table->string('name'); // Tên chương trình: "Giảm 20% toàn sàn"

            // Loại giảm giá
            $table->string('type'); // percentage, fixed_amount
            $table->decimal('value', 15, 2); // 20.00 (%) hoặc 100000 (VND)

            // Giới hạn giảm tối đa (chỉ áp dụng cho type = percentage)
            $table->decimal('max_discount_amount', 15, 2)->nullable();

            // Đơn hàng tối thiểu để áp dụng
            $table->decimal('min_order_amount', 15, 2)->nullable();

            // Giới hạn số lượt dùng
            $table->unsignedInteger('usage_limit_total')->nullable();     // Tổng lượt dùng toàn hệ thống
            $table->unsignedInteger('usage_limit_per_user')->default(1); // Mỗi user dùng được mấy lần

            // Đã dùng bao nhiêu
            $table->unsignedInteger('used_count')->default(0);

            // Thời gian hiệu lực
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();

            // Trạng thái
            $table->boolean('is_active')->default(true);

            // Áp dụng cho: toàn sàn, danh mục, sản phẩm cụ thể (tương lai mở rộng)
            $table->json('applies_to')->nullable(); // ["all", "categories", "products"]

            $table->timestamps();

            // Index tối ưu
            $table->index('code');
            $table->index(['is_active', 'starts_at', 'ends_at']);
            $table->index('used_count');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
