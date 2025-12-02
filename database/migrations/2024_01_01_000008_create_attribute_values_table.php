<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Attribute values (Red, Blue, Large, Small, etc.)
     */
    public function up(): void
    {
        Schema::create('attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attribute_id')
                ->constrained('attributes')
                ->onDelete('cascade');
            $table->string('value', 100);
            $table->string('display_value', 100);
            $table->string('color_code', 7)->nullable();
            $table->integer('display_order')->default(0);
            $table->timestamps();

            // Indexes
            $table->index(['attribute_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_values');
    }
};
