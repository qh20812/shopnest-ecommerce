<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Product reviews and ratings
     */
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')
                ->constrained('products')
                ->onDelete('cascade');
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');
            $table->foreignId('order_item_id')
                ->nullable()
                ->constrained('order_items');
            $table->tinyInteger('rating');
            $table->string('title')->nullable();
            $table->text('comment')->nullable();
            $table->boolean('is_verified_purchase')->default(false);
            $table->integer('helpful_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['product_id']);
            $table->index(['user_id']);
            $table->index(['rating']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
