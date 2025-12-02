<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Messages in dispute threads
     */
    public function up(): void
    {
        Schema::create('dispute_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dispute_id')
                ->constrained('disputes')
                ->onDelete('cascade');
            $table->foreignId('sender_id')
                ->constrained('users')
                ->onDelete('cascade');
            $table->text('message');
            $table->string('attachment_url')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['dispute_id']);
            $table->index(['sender_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dispute_messages');
    }
};
