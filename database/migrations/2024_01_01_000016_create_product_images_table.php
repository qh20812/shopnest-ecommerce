<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Product images gallery
     */
    public function up(): void
    {
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')
                ->constrained('products')
                ->onDelete('cascade');
            $table->string('image_url');
            $table->string('thumbnail_url')->nullable();
            $table->string('alt_text')->nullable();
            $table->integer('display_order')->default(0);
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            // Indexes
            $table->index(['product_id', 'display_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_images');
    }
};
