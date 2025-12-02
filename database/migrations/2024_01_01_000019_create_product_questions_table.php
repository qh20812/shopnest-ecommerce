<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Customer questions about products
     */
    public function up(): void
    {
        Schema::create('product_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')
                ->constrained('products')
                ->onDelete('cascade');
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');
            $table->text('question_text');
            $table->boolean('is_answered')->default(false);
            $table->timestamps();

            // Indexes
            $table->index(['product_id']);
            $table->index(['user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_questions');
    }
};
