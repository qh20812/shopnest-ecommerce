<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Shipping and delivery information
     */
    public function up(): void
    {
        Schema::create('shipping_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')
                ->constrained('orders')
                ->onDelete('cascade');
            $table->foreignId('shipper_id')
                ->nullable()
                ->constrained('users');
            $table->string('tracking_number', 100)->nullable();
            $table->string('carrier', 100)->nullable();
            $table->enum('status', ['pending', 'picked_up', 'in_transit', 'out_for_delivery', 'delivered', 'failed', 'returned'])->default('pending');
            $table->dateTime('estimated_delivery')->nullable();
            $table->dateTime('actual_delivery')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['order_id']);
            $table->index(['shipper_id']);
            $table->index(['tracking_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_details');
    }
};
