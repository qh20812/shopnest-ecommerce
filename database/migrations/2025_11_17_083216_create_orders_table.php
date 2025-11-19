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
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('order_number', 30)->unique();
            $table->foreignId('customer_id')->constrained('users')->onDelete('restrict');
            $table->decimal('sub_total', 15, 2);
            $table->decimal('shipping_fee', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2); // ← Chỉ VND
            $table->string('payment_method'); // cod, stripe, momo, vnpay, paypal
            $table->string('payment_status')->default('unpaid'); // unpaid, paid, failed, refunded
            $table->string('status')->default('pending'); // pending, confirmed, processing, shipped, delivered, cancelled, returned
            $table->foreignId('shipping_address_id')->constrained('user_addresses');
            $table->string('customer_phone', 20)->nullable();
            $table->foreignId('promotion_id')->nullable()->constrained();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes quan trọng cho tìm kiếm và lọc
            $table->index('customer_id');
            $table->index('status');
            $table->index('payment_status');
            $table->index(['customer_id', 'status']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
