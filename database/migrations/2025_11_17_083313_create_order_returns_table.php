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
        Schema::create('order_returns', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('order_item_id')->nullable()->constrained('order_items')->onDelete('set null');

            $table->unsignedInteger('quantity'); // Số lượng trả
            $table->decimal('refund_amount', 15, 2); // Số tiền hoàn lại

            $table->text('reason'); // Lý do trả hàng
            $table->json('images')->nullable(); // Ảnh khách gửi kèm (chụp lỗi, vỡ...)

            $table->string('status')->default('pending'); // pending, approved, rejected, processing, completed

            $table->timestamp('requested_at')->useCurrent();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->foreignId('approved_by')->nullable()->constrained('users'); // Nhân viên duyệt
            $table->text('admin_note')->nullable(); // Ghi chú nội bộ

            $table->timestamps();

            $table->index('status');
            $table->index('order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_returns');
    }
};
