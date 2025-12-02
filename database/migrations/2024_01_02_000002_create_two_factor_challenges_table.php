<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Temporary 2FA verification codes and challenges
     */
    public function up(): void
    {
        Schema::create('two_factor_challenges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');
            $table->enum('method', ['authenticator', 'sms', 'email', 'backup_code']);
            $table->string('code', 10)->comment('Hashed verification code');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->dateTime('expires_at');
            $table->timestamp('created_at')->nullable();

            // Indexes
            $table->index(['user_id']);
            $table->index(['expires_at']);
            $table->index(['verified_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('two_factor_challenges');
    }
};
