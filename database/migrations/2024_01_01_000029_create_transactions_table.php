<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Payment transactions for orders
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')
                ->constrained('orders')
                ->onDelete('cascade');
            $table->string('transaction_number', 50)->unique();
            $table->enum('payment_method', ['cod', 'credit_card', 'e_wallet', 'bank_transfer']);
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('VND');
            $table->enum('status', ['pending', 'success', 'failed'])->default('pending');
            $table->string('gateway_transaction_id')->nullable();
            $table->json('gateway_response')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['order_id']);
            $table->index(['transaction_number']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
