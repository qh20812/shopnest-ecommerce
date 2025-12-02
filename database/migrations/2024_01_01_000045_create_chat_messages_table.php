<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Chat messages
     */
    public function up(): void
    {
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_room_id')
                ->constrained('chat_rooms')
                ->onDelete('cascade');
            $table->foreignId('sender_id')
                ->constrained('users')
                ->onDelete('cascade');
            $table->text('message');
            $table->enum('message_type', ['text', 'image', 'product_link'])->default('text');
            $table->string('attachment_url')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            // Indexes
            $table->index(['chat_room_id', 'created_at']);
            $table->index(['sender_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};
