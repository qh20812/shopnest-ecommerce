<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Line items in return requests
     */
    public function up(): void
    {
        Schema::create('return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('return_id')
                ->constrained('returns')
                ->onDelete('cascade');
            $table->foreignId('order_item_id')
                ->constrained('order_items')
                ->onDelete('cascade');
            $table->integer('quantity');
            $table->text('reason');
            $table->timestamps();

            // Indexes
            $table->index(['return_id']);
            $table->index(['order_item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_items');
    }
};
