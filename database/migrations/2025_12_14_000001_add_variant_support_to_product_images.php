<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add variant_id to product_images to support variant-specific images
     * This allows each variant to have its own set of images (like Shopee, Lazada)
     */
    public function up(): void
    {
        Schema::table('product_images', function (Blueprint $table) {
            // Check if variant_id column doesn't exist before adding
            if (!Schema::hasColumn('product_images', 'variant_id')) {
                $table->foreignId('variant_id')
                    ->nullable()
                    ->after('product_id')
                    ->constrained('product_variants')
                    ->onDelete('cascade');
                
                // Add index for better query performance
                $table->index(['variant_id', 'display_order']);
            }
        });

        // Remove image_id from product_variants if it exists
        if (Schema::hasColumn('product_variants', 'image_id')) {
            Schema::table('product_variants', function (Blueprint $table) {
                $table->dropForeign(['image_id']);
                $table->dropColumn('image_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore image_id to product_variants (only if it doesn't exist)
        if (!Schema::hasColumn('product_variants', 'image_id')) {
            Schema::table('product_variants', function (Blueprint $table) {
                $table->foreignId('image_id')
                    ->nullable()
                    ->after('reserved_quantity')
                    ->constrained('product_images')
                    ->onDelete('set null');
            });
        }

        // Remove variant_id from product_images (only if it exists)
        if (Schema::hasColumn('product_images', 'variant_id')) {
            Schema::table('product_images', function (Blueprint $table) {
                $table->dropForeign(['variant_id']);
                $table->dropColumn('variant_id');
            });
        }
    }
};
