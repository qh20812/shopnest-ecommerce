<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Promotions applied to orders
     */
    public function up(): void
    {
        Schema::create('order_promotion', function (Blueprint $table) {
            $table->foreignId('order_id')
                ->constrained('orders')
                ->onDelete('cascade');
            $table->foreignId('promotion_id')
                ->constrained('promotions')
                ->onDelete('cascade');
            $table->decimal('discount_amount', 15, 2);
            $table->primary(['order_id', 'promotion_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_promotion');
    }
};
