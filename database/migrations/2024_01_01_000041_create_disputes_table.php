<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Order disputes between customers and sellers
     */
    public function up(): void
    {
        Schema::create('disputes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')
                ->constrained('orders')
                ->onDelete('cascade');
            $table->foreignId('customer_id')
                ->constrained('users')
                ->onDelete('cascade');
            $table->foreignId('shop_id')
                ->constrained('shops')
                ->onDelete('cascade');
            $table->string('dispute_number', 50)->unique();
            $table->text('reason');
            $table->enum('status', ['open', 'in_review', 'resolved', 'closed'])->default('open');
            $table->text('resolution')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['order_id']);
            $table->index(['customer_id']);
            $table->index(['shop_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disputes');
    }
};
