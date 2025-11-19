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
        Schema::create('analytics_reports', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('report_type'); // daily_sales, monthly_revenue, top_products, customer_behavior...
            $table->string('title');       // "Doanh thu ngày 18/11/2025", "Top 10 sản phẩm bán chạy"

            $table->date('date_from');
            $table->date('date_to');

            $table->json('data');          // Lưu toàn bộ dữ liệu báo cáo dưới dạng JSON (linh hoạt)
            $table->json('summary')->nullable(); // Tóm tắt: tổng doanh thu, số đơn, tỷ lệ hoàn...

            $table->unsignedBigInteger('generated_by')->nullable(); // Admin tạo báo cáo
            $table->timestamp('generated_at')->useCurrent();

            $table->timestamps();

            $table->index(['report_type', 'date_from', 'date_to']);
            $table->index('generated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytics_reports');
    }
};
