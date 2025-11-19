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
        Schema::create('order_shipping_calculations', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('rule_id')->nullable()->constrained('shipping_fee_rules')->onDelete('set null');

            $table->decimal('subtotal_before_rule', 15, 2);     // Tổng tiền hàng
            $table->decimal('applied_shipping_fee', 15, 2);     // Phí ship thực tế áp dụng
            $table->decimal('original_shipping_fee', 15, 2);   // Phí gốc (trước khi áp freeship)
            $table->decimal('saved_amount', 15, 2)->default(0); // Tiền ship được miễn

            $table->string('applied_rule_name')->nullable();    // Tên quy tắc đã áp dụng
            $table->text('note')->nullable();                   // Ghi chú: "Freeship 500k", "Nội thành HN"

            $table->timestamps();

            $table->index('order_id');
            $table->index('rule_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_shipping_calculations');
    }
};
