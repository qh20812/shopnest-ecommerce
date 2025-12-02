<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Ratings for shippers from customers
     */
    public function up(): void
    {
        Schema::create('shipper_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')
                ->constrained('orders')
                ->onDelete('cascade');
            $table->foreignId('shipper_id')
                ->constrained('users')
                ->onDelete('cascade');
            $table->foreignId('customer_id')
                ->constrained('users')
                ->onDelete('cascade');
            $table->tinyInteger('rating');
            $table->text('comment')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['shipper_id']);
            $table->index(['order_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipper_ratings');
    }
};
