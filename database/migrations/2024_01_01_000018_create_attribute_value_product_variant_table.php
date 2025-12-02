<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Pivot table linking variants to attribute values
     */
    public function up(): void
    {
        Schema::create('attribute_value_product_variant', function (Blueprint $table) {
            $table->foreignId('product_variant_id')
                ->constrained('product_variants')
                ->onDelete('cascade');
            $table->foreignId('attribute_value_id')
                ->constrained('attribute_values')
                ->onDelete('cascade');
            $table->primary(['product_variant_id', 'attribute_value_id']);

            // Indexes
            $table->index(['attribute_value_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_value_product_variant');
    }
};
