<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Line items in orders
     */
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')
                ->constrained('orders')
                ->onDelete('cascade');
            $table->foreignId('product_variant_id')
                ->constrained('product_variants');
            $table->string('product_name');
            $table->string('variant_name');
            $table->string('sku', 100);
            $table->integer('quantity');
            $table->decimal('unit_price', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('total_price', 15, 2);
            $table->timestamps();

            // Indexes
            $table->index(['order_id']);
            $table->index(['product_variant_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
