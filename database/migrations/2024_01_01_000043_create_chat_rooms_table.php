<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Chat room containers
     */
    public function up(): void
    {
        Schema::create('chat_rooms', function (Blueprint $table) {
            $table->id();
            $table->enum('room_type', ['customer_seller', 'customer_support']);
            $table->foreignId('product_id')
                ->nullable()
                ->constrained('products');
            $table->dateTime('last_message_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['product_id']);
            $table->index(['last_message_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_rooms');
    }
};
