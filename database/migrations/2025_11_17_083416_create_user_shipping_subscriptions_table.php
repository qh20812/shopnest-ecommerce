<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_shipping_subscriptions', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('package_id')->constrained('shipping_packages')->onDelete('restrict');

            $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('set null'); // Đơn mua gói
            $table->foreignId('transaction_id')->nullable()->constrained('transactions')->onDelete('set null'); // Thanh toán gói

            $table->timestamp('starts_at')->useCurrent();
            $table->timestamp('expires_at'); // Hết hạn = starts_at + duration_days

            $table->boolean('is_active')->default(true);
            $table->boolean('auto_renew')->default(false); // Tự động gia hạn (nếu có thẻ)

            $table->unsignedInteger('used_count')->default(0); // Đã dùng freeship bao nhiêu lần
            $table->decimal('saved_amount', 15, 2)->default(0); // Tổng tiền ship đã tiết kiệm

            $table->timestamps();

            $table->index(['user_id', 'is_active']);
            $table->index('expires_at');
            $table->index(['user_id', 'package_id']);
            // Lưu ý: Không dùng unique constraint vì user có thể mua lại gói sau khi hết hạn
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_shipping_subscriptions');
    }
};
