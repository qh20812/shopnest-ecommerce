<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Flash sale event campaigns
     */
    public function up(): void
    {
        Schema::create('flash_sale_events', function (Blueprint $table) {
            $table->id();
            $table->string('event_name', 100);
            $table->text('description')->nullable();
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes
            $table->index(['start_time', 'end_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flash_sale_events');
    }
};
