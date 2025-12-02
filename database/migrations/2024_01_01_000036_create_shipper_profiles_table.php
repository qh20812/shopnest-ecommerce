<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Shipper/driver profiles
     */
    public function up(): void
    {
        Schema::create('shipper_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');
            $table->enum('vehicle_type', ['motorcycle', 'car', 'truck']);
            $table->string('vehicle_number', 50);
            $table->string('license_number', 50);
            $table->foreignId('current_hub_id')
                ->nullable()
                ->constrained('hubs');
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('total_deliveries')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes
            $table->index(['user_id']);
            $table->index(['current_hub_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipper_profiles');
    }
};
