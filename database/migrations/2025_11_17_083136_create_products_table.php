<?php
// database/migrations/2025_11_18_000006_create_products_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('product_id'); // PK riêng cho sản phẩm
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('sku', 100)->unique();
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->json('images')->nullable(); // Mảng ảnh phụ
            $table->decimal('base_price', 15, 2)->nullable(); // Giá gốc (nếu không dùng variant)
            $table->boolean('has_variants')->default(false);
            $table->integer('stock_quantity')->default(0);
            $table->integer('sold_count')->default(0);
            $table->decimal('rating_average', 3, 2)->default(0);
            $table->integer('review_count')->default(0);
            $table->string('status')->default('draft'); // draft, published, archived
            $table->boolean('is_featured')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'published_at']);
            $table->index('is_featured');
            $table->index('sold_count');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
