<?php
// database/migrations/2025_11_18_000016_create_disputes_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disputes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('created_by')->nullable()->constrained('users'); // Nhân viên tạo (nếu admin tạo)

            $table->string('title'); // VD: "Hàng bị vỡ", "Không nhận được hàng"
            $table->text('description');
            $table->json('images')->nullable(); // Ảnh khách gửi

            // Loại khiếu nại
            $table->string('type'); // not_received, damaged_product, wrong_item, other

            // Số tiền yêu cầu hoàn
            $table->decimal('requested_refund_amount', 15, 2)->nullable();

            // Trạng thái xử lý
            $table->string('status')->default('pending'); // pending, in_review, resolved, rejected, escalated

            $table->timestamp('resolved_at')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users');
            $table->text('resolution_note')->nullable(); // Ghi chú giải quyết

            $table->timestamps();

            $table->index(['order_id', 'status']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disputes');
    }
};
