<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Customer orders with full transaction details
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 50)->unique();
            $table->foreignId('customer_id')
                ->constrained('users')
                ->onDelete('cascade');
            $table->foreignId('shop_id')
                ->constrained('shops')
                ->onDelete('cascade');
            $table->enum('status', ['pending', 'confirmed', 'processing', 'shipping', 'delivered', 'cancelled', 'refunded'])->default('pending');
            $table->enum('payment_status', ['unpaid', 'paid', 'partially_refunded', 'refunded'])->default('unpaid');
            $table->decimal('subtotal', 15, 2);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('shipping_fee', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2);
            $table->string('currency', 3)->default('VND');
            $table->foreignId('shipping_address_id')
                ->constrained('user_addresses');
            $table->enum('payment_method', ['cod', 'credit_card', 'e_wallet', 'bank_transfer']);
            $table->text('note')->nullable();
            $table->text('cancelled_reason')->nullable();
            $table->dateTime('cancelled_at')->nullable();
            $table->dateTime('confirmed_at')->nullable();
            $table->dateTime('delivered_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['order_number']);
            $table->index(['customer_id']);
            $table->index(['shop_id']);
            $table->index(['status']);
            $table->index(['created_at']);
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
