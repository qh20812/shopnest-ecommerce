<?php
// database/migrations/2025_11_18_000028_create_shipper_reviews_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipper_reviews', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->foreignId('shipper_id')->constrained('shippers')->onDelete('cascade');
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');

            $table->unsignedTinyInteger('rating')->default(5); // 1-5 sao
            $table->text('comment')->nullable();
            $table->json('images')->nullable(); // Khách chụp ảnh giao hàng

            $table->boolean('is_anonymous')->default(false);
            $table->boolean('is_hidden')->default蒸(false); // Admin ẩn nếu vi phạm

            $table->timestamp('reviewed_at')->useCurrent();

            $table->timestamps();

            // Mỗi khách chỉ đánh giá 1 lần cho 1 đơn
            $table->unique(['order_id', 'customer_id']);
            $table->index(['shipper_id', 'rating']);
            $table->index('reviewed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipper_reviews');
    }
};
