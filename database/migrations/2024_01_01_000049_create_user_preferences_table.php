<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * User preferences and settings
     */
    public function up(): void
    {
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');
            $table->boolean('notification_email')->default(true);
            $table->boolean('notification_sms')->default(false);
            $table->boolean('notification_push')->default(true);
            $table->string('language', 10)->default('vi');
            $table->string('currency', 3)->default('VND');
            $table->enum('theme', ['light', 'dark', 'auto'])->default('auto');
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
        Schema::dropIfExists('user_preferences');
    }
};
