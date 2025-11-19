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
        Schema::create('loyalty_points', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->integer('points');                    // Số điểm +/-
            $table->string('type'); // earn, spend, expire, adjust
            $table->string('reason');                     // "Đặt hàng #12345", "Hoàn tiền", "Hết hạn"
            $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('set null');
            $table->integer('balance_after')->default(0); // Số dư điểm sau khi thay đổi

            $table->timestamp('expires_at')->nullable();  // Điểm hết hạn (nếu có)
            $table->timestamp('earned_at')->useCurrent();

            $table->timestamps();

            $table->index(['user_id', 'earned_at']);
            $table->index('expires_at');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loyalty_points');
    }
};
