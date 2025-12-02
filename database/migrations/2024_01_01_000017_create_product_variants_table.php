<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Product variants (SKUs) with pricing and inventory
     */
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')
                ->constrained('products')
                ->onDelete('cascade');
            $table->string('sku', 100)->unique();
            $table->string('variant_name');
            $table->decimal('price', 15, 2);
            $table->decimal('compare_at_price', 15, 2)->nullable();
            $table->decimal('cost_per_item', 15, 2)->nullable();
            $table->integer('stock_quantity')->default(0);
            $table->integer('reserved_quantity')->default(0);
            $table->foreignId('image_id')
                ->nullable()
                ->constrained('product_images')
                ->onDelete('set null');
            $table->integer('weight_grams')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['product_id']);
            $table->index(['sku']);
            $table->index(['image_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
