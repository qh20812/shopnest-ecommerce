<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Hierarchical administrative divisions (provinces/wards - Vietnam structure)
     */
    public function up(): void
    {
        Schema::create('administrative_divisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')
                ->constrained('countries');
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('administrative_divisions');
            $table->string('division_name', 100);
            $table->enum('division_type', ['province', 'ward']);
            $table->string('code', 20)->nullable();
            $table->string('codename', 100)->nullable();
            $table->string('short_codename', 100)->nullable();
            $table->string('phone_code', 10)->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['country_id']);
            $table->index(['parent_id']);
            $table->index(['division_type']);
            $table->index(['code']);
            $table->index(['codename']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('administrative_divisions');
    }
};
