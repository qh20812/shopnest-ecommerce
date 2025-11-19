<?php
// database/migrations/2025_11_18_000022_create_product_recommendations_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_recommendations', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('cascade');

            $table->foreignId('product_id')
                ->constrained('products', 'product_id')
                ->onDelete('cascade');

            $table->foreignId('variant_id')
                ->nullable()
                ->constrained('product_variants')
                ->onDelete('set null');

            // Điểm gợi ý (càng cao càng nên hiển thị)
            $table->decimal('score', 8, 4)->default(0);

            // Nguồn gợi ý
            $table->enum('source', [
                'bestseller',       // Bán chạy nhất
                'viewed_together',  // Khách xem cùng
                'bought_together',  // Mua cùng
                'similar_category', // Cùng danh mục
                'personalized',     // Dựa trên lịch sử cá nhân
                'new_arrival',      // Hàng mới
                'price_drop',       // Giảm giá mạnh
                'back_in_stock'     // Có hàng lại
            ]);

            $table->json('metadata')->nullable(); // Lưu thêm dữ liệu: số lượt xem, % chuyển đổi...

            $table->timestamp('calculated_at')->useCurrent();
            $table->timestamp('expires_at')->nullable(); // Gợi ý có thể hết hạn

            $table->timestamps();

            $table->index(['user_id', 'score']);
            $table->index('source');
            $table->index('expires_at');
            $table->unique(['user_id', 'product_id', 'source']); // Tránh trùng
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_recommendations');
    }
};
