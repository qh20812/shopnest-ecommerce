<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Product master table with variants support
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')
                ->constrained('shops')
                ->onDelete('cascade');
            $table->foreignId('category_id')
                ->constrained('categories');
            $table->foreignId('brand_id')
                ->nullable()
                ->constrained('brands');
            $table->foreignId('seller_id')
                ->constrained('users');
            $table->string('product_name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->json('specifications')->nullable();
            $table->decimal('base_price', 15, 2);
            $table->string('currency', 3)->default('VND');
            $table->integer('weight_grams')->nullable();
            $table->integer('length_cm')->nullable();
            $table->integer('width_cm')->nullable();
            $table->integer('height_cm')->nullable();
            $table->enum('status', ['draft', 'active', 'inactive', 'out_of_stock'])->default('draft');
            $table->integer('total_quantity')->default(0);
            $table->integer('total_sold')->default(0);
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('review_count')->default(0);
            $table->integer('view_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['shop_id']);
            $table->index(['category_id']);
            $table->index(['brand_id']);
            $table->index(['seller_id']);
            $table->index(['slug']);
            $table->index(['status']);
            $table->index(['rating', 'review_count']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
