<?php
// database/migrations/2025_11_18_000009_create_cart_items_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Người dùng đăng nhập (nullable nếu khách vãng lai)
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('cascade');

            // Khách vãng lai: dùng session_id (từ Laravel session)
            $table->string('session_id', 255)->nullable();

            // Variant của sản phẩm (bắt buộc)
            $table->foreignId('variant_id')
                ->constrained('product_variants')
                ->onDelete('cascade');

            // Số lượng
            $table->unsignedInteger('quantity')->default(1);

            // Giá tại thời điểm thêm vào giỏ (snapshot – tránh thay đổi giá sau này)
            $table->decimal('unit_price', 15, 2);        // Giá gốc
            $table->decimal('sale_price', 15, 2)->nullable(); // Giá khuyến mãi
            $table->decimal('final_price', 15, 2);       // Giá thực tế khách trả

            // Thuộc tính variant tại thời điểm thêm (snapshot)
            $table->json('variant_attributes')->nullable();

            // Tên sản phẩm + variant (để hiển thị khi sản phẩm bị xóa)
            $table->string('product_name');
            $table->string('variant_name')->nullable();

            // Ảnh thumbnail (snapshot)
            $table->string('thumbnail')->nullable();

            $table->timestamps();

            // Index tối ưu
            $table->index(['user_id', 'session_id']);
            $table->index('variant_id');
            $table->unique(['user_id', 'variant_id']);           // 1 user chỉ thêm 1 variant 1 lần
            $table->unique(['session_id', 'variant_id']);        // 1 guest chỉ thêm 1 variant 1 lần
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
