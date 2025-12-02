<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Users participating in chat rooms
     */
    public function up(): void
    {
        Schema::create('chat_participants', function (Blueprint $table) {
            $table->foreignId('chat_room_id')
                ->constrained('chat_rooms')
                ->onDelete('cascade');
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');
            $table->dateTime('last_read_at')->nullable();
            $table->primary(['chat_room_id', 'user_id']);
            $table->timestamps();

            // Indexes
            $table->index(['user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_participants');
    }
};
