<?php
// database/migrations/2025_11_18_000008_create_product_variants_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Liên kết với sản phẩm (product_id là PK của bảng products)
            $table->foreignId('product_id')
                ->constrained('products', 'product_id')
                ->onDelete('cascade');

            // SKU riêng cho từng variant (phải unique toàn hệ thống)
            $table->string('sku', 100)->unique();

            // Tên variant hiển thị cho khách (VD: "Đen - 128GB", "Trắng - 256GB")
            $table->string('name');

            // Giá bán thực tế của variant này (chỉ VND)
            $table->decimal('price', 15, 2);

            // Giá khuyến mãi (nếu có)
            $table->decimal('sale_price', 15, 2)->nullable();

            // Số lượng tồn kho
            $table->unsignedInteger('stock_quantity')->default(0);
            $table->unsignedInteger('reserved_quantity')->default(0); // Đang trong giỏ hàng

            // Thuộc tính variant (màu sắc, kích thước, dung lượng…)
            $table->json('attributes'); // Ví dụ: {"color": "Đen", "storage": "128GB", "ram": "8GB"}

            // Ảnh riêng của variant (nếu khác với ảnh chính)
            $table->string('image')->nullable();

            // Trạng thái bán
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false); // Variant mặc định khi vào trang sản phẩm

            $table->timestamps();

            // Index tối ưu tìm kiếm & lọc
            $table->index('price');
            $table->index('stock_quantity');
            $table->index('is_active');
            $table->index(['product_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
