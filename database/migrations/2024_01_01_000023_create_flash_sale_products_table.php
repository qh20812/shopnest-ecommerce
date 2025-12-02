<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Products participating in flash sales
     */
    public function up(): void
    {
        Schema::create('flash_sale_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flash_sale_event_id')
                ->constrained('flash_sale_events')
                ->onDelete('cascade');
            $table->foreignId('product_variant_id')
                ->constrained('product_variants')
                ->onDelete('cascade');
            $table->decimal('flash_price', 15, 2);
            $table->integer('quantity_limit');
            $table->integer('sold_quantity')->default(0);
            $table->integer('max_purchase_per_user')->default(1);
            $table->timestamps();

            // Indexes
            $table->index(['flash_sale_event_id']);
            $table->index(['product_variant_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flash_sale_products');
    }
};
