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
        Schema::create('wishlist_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('wishlist_id')->constrained('wishlists')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products', 'product_id')->onDelete('cascade');
            $table->foreignId('variant_id')->nullable()->constrained('product_variants')->onDelete('set null');

            // Giá tại thời điểm thêm vào wishlist (để so sánh giảm giá)
            $table->decimal('price_at_add', 15, 2);
            $table->decimal('current_price', 15, 2); // Cập nhật realtime khi có khuyến mãi

            // Thuộc tính variant (snapshot)
            $table->json('variant_attributes')->nullable();
            $table->string('variant_name')->nullable();
            $table->string('product_name');
            $table->string('thumbnail')->nullable();

            // Tùy chọn nâng cao
            $table->unsignedTinyInteger('priority')->default(3); // 1=cao nhất, 5=thấp nhất
            $table->boolean('notify_price_drop')->default(true);  // Thông báo khi giảm giá
            $table->boolean('notify_back_in_stock')->default(true); // Thông báo khi có hàng lại

            $table->timestamp('added_at')->useCurrent();

            $table->timestamps();

            // Không cho thêm trùng sản phẩm trong cùng 1 wishlist
            $table->unique(['wishlist_id', 'product_id']);
            $table->index(['product_id', 'current_price']);
            $table->index('notify_price_drop');
            $table->index('notify_back_in_stock');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wishlist_items');
    }
};