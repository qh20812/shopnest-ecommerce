<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Trusted devices that skip 2FA for a period
     */
    public function up(): void
    {
        Schema::create('two_factor_trusted_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');
            $table->string('device_name')->nullable();
            $table->string('device_fingerprint')->unique()->comment('Hashed unique device identifier');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->dateTime('expires_at')->comment('Trust expires after 30 days');
            $table->timestamps();

            // Indexes
            $table->index(['user_id']);
            $table->index(['device_fingerprint']);
            $table->index(['expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('two_factor_trusted_devices');
    }
};
