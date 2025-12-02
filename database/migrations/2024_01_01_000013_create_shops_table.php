<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Seller shops with metrics and verification status
     */
    public function up(): void
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')
                ->constrained('users')
                ->onDelete('cascade');
            $table->string('shop_name', 100);
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('logo_url')->nullable();
            $table->string('banner_url')->nullable();
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('total_products')->default(0);
            $table->integer('total_followers')->default(0);
            $table->integer('total_orders')->default(0);
            $table->decimal('response_rate', 5, 2)->nullable();
            $table->integer('response_time_hours')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes
            $table->index(['owner_id']);
            $table->index(['slug']);
            $table->index(['is_verified', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shops');
    }
};
