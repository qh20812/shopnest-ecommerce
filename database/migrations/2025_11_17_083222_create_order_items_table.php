<?php
// database/migrations/2025_11_18_000014_create_order_items_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('variant_id')->constrained('product_variants')->onDelete('restrict');

            $table->unsignedInteger('quantity');

            // Giá tại thời điểm đặt hàng (snapshot – chống thay đổi giá sau này)
            $table->decimal('unit_price', 15, 2);           // Giá gốc
            $table->decimal('sale_price', 15, 2)->nullable(); // Giá khuyến mãi
            $table->decimal('final_price', 15, 2);          // Giá thực tế khách trả
            $table->decimal('total_price', 15, 2);          // final_price × quantity

            // Snapshot thông tin để hiển thị khi sản phẩm bị xóa
            $table->string('product_name');
            $table->string('variant_name')->nullable();
            $table->json('variant_attributes')->nullable();
            $table->string('thumbnail')->nullable();

            $table->timestamps();

            $table->index(['order_id', 'variant_id']);
            $table->index('variant_id'); // Để query ngược: sản phẩm nào bán chạy
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
