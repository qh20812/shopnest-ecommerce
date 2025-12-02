<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * User shipping addresses with full geographic information
     */
    public function up(): void
    {
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');
            $table->string('address_label', 50);
            $table->string('recipient_name', 100);
            $table->string('phone_number', 20);
            $table->string('address_line1');
            $table->string('address_line2')->nullable();
            $table->foreignId('country_id')
                ->constrained('countries');
            $table->foreignId('province_id')
                ->constrained('administrative_divisions');
            $table->foreignId('district_id')
                ->constrained('administrative_divisions');
            $table->foreignId('ward_id')
                ->constrained('administrative_divisions');
            $table->string('postal_code', 20)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            // Indexes
            $table->index(['user_id']);
            $table->index(['country_id', 'province_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_addresses');
    }
};
