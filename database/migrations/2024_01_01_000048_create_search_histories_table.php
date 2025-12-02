<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Search query history for analytics
     */
    public function up(): void
    {
        Schema::create('search_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
            $table->string('search_query');
            $table->json('filters')->nullable();
            $table->integer('results_count')->default(0);
            $table->timestamps();

            // Indexes
            $table->index(['user_id']);
            $table->index(['search_query']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('search_histories');
    }
};
