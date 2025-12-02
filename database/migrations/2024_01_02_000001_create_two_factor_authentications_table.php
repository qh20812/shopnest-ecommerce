<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Two-factor authentication methods for users
     */
    public function up(): void
    {
        Schema::create('two_factor_authentications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');
            $table->enum('method', ['authenticator', 'sms', 'email']);
            $table->string('identifier')->nullable()->comment('Phone number for SMS or email address');
            $table->text('secret')->nullable()->comment('Encrypted secret for authenticator apps');
            $table->json('backup_codes')->nullable()->comment('Hashed backup codes');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['user_id']);
            $table->index(['method']);
            $table->index(['is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('two_factor_authentications');
    }
};
