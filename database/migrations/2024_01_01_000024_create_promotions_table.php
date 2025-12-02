<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Promotion campaigns and discount rules
     */
    public function up(): void
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shop_id')
                ->nullable()
                ->constrained('shops')
                ->onDelete('cascade');
            $table->string('promotion_name', 100);
            $table->text('description')->nullable();
            $table->enum('promotion_type', ['percentage', 'fixed_amount', 'free_shipping', 'buy_x_get_y']);
            $table->decimal('discount_value', 15, 2);
            $table->decimal('min_order_value', 15, 2)->nullable();
            $table->decimal('max_discount_amount', 15, 2)->nullable();
            $table->integer('usage_limit')->nullable();
            $table->integer('used_count')->default(0);
            $table->json('customer_eligibility')->nullable();
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes
            $table->index(['shop_id']);
            $table->index(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
