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
        Schema::create('delivery_assignments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('shipper_id')->constrained('shippers')->onDelete('restrict');

            $table->string('tracking_code', 50)->unique(); // Mã theo dõi

            $table->enum('status', [
                'assigned',      // Đã phân công
                'picked_up',     // Đã lấy hàng
                'in_transit',    // Đang giao
                'delivered',     // Đã giao thành công
                'failed',        // Giao thất bại
                'returned'       // Đã trả hàng về kho
            ])->default('assigned');

            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamp('picked_up_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('failed_at')->nullable();

            $table->text('note')->nullable(); // Ghi chú từ shipper

            $table->timestamps();

            $table->index(['shipper_id', 'status']);
            $table->index('tracking_code');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_assignments');
    }
};
