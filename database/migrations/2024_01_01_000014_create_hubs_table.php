<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Distribution hubs for logistics and shipping
     */
    public function up(): void
    {
        Schema::create('hubs', function (Blueprint $table) {
            $table->id();
            $table->string('hub_name', 100);
            $table->string('hub_code', 20)->unique();
            $table->string('address');
            $table->foreignId('ward_id')
                ->constrained('administrative_divisions');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->integer('capacity');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes
            $table->index(['hub_code']);
            $table->index(['ward_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hubs');
    }
};
