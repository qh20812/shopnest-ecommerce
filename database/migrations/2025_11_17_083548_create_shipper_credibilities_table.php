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
        Schema::create('shipper_credibilities', function (Blueprint $table) {
            $table->foreignId('shipper_id')->primary()->constrained('shippers')->onDelete('cascade');

            // Tỷ lệ giao thành công (%)
            $table->decimal('success_rate', 5, 2)->default(100.00);

            // Điểm đánh giá trung bình
            $table->decimal('average_rating', 3, 2)->default(5.00);

            // Số lượng đánh giá
            $table->unsignedInteger('review_count')->default(0);

            // Số đơn bị khiếu nại (dispute liên quan đến giao hàng)
            $table->unsignedInteger('complaint_count')->default(0);

            // Số đơn giao trễ (nếu có hệ thống đo thời gian)
            $table->unsignedInteger('late_delivery_count')->default(0);

            // Điểm uy tín tổng hợp (0-100)
            $table->unsignedTinyInteger('credibility_score')->default(100);

            // Cấp độ uy tín
            $table->string('level')->default('excellent'); // excellent, good, average, warning, poor

            // Ghi chú nội bộ (admin)
            $table->text('admin_note')->nullable();

            $table->timestamp('last_calculated_at')->useCurrent();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipper_credibilities');
    }
};
