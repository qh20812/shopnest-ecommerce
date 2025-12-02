<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Answers to product questions
     */
    public function up(): void
    {
        Schema::create('product_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')
                ->constrained('product_questions')
                ->onDelete('cascade');
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');
            $table->text('answer_text');
            $table->boolean('is_seller')->default(false);
            $table->timestamps();

            // Indexes
            $table->index(['question_id']);
            $table->index(['user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_answers');
    }
};
