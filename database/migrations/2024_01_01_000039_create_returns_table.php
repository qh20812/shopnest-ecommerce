<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Product return requests
     */
    public function up(): void
    {
        Schema::create('returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')
                ->constrained('orders')
                ->onDelete('cascade');
            $table->foreignId('customer_id')
                ->constrained('users')
                ->onDelete('cascade');
            $table->string('return_number', 50)->unique();
            $table->text('reason');
            $table->enum('status', ['requested', 'approved', 'rejected', 'received', 'refunded'])->default('requested');
            $table->decimal('refund_amount', 15, 2)->nullable();
            $table->text('admin_note')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['order_id']);
            $table->index(['customer_id']);
            $table->index(['return_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('returns');
    }
};
