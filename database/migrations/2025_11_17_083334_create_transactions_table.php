<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('type'); // payment, refund
            $table->decimal('amount', 15, 2); // ← Chỉ VND
            $table->string('gateway', 50)->nullable(); // stripe, momo, vnpay, paypal, cod
            $table->string('gateway_transaction_id', 255)->nullable();
            $table->string('status')->default('pending'); // pending, completed, failed
            $table->softDeletes();
            $table->timestamps();
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
