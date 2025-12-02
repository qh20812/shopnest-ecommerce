<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Shipment tracking journey points
     */
    public function up(): void
    {
        Schema::create('shipment_journeys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipping_detail_id')
                ->constrained('shipping_details')
                ->onDelete('cascade');
            $table->foreignId('hub_id')
                ->nullable()
                ->constrained('hubs');
            $table->enum('status', ['picked_up', 'at_hub', 'in_transit', 'out_for_delivery', 'delivered', 'failed']);
            $table->string('location')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['shipping_detail_id']);
            $table->index(['hub_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipment_journeys');
    }
};
