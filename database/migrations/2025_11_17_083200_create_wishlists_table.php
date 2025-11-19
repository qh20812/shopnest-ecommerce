<?php
// database/migrations/2025_11_18_000010_create_wishlists_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wishlists', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->string('name')->default('Danh sách yêu thích');
            $table->text('description')->nullable();

            // Quyền riêng tư
            $table->string('privacy')->default('private'); // private, shared, public

            // Danh sách mặc định của người dùng
            $table->boolean('is_default')->default(false);

            // Đếm số sản phẩm (tối ưu hiển thị)
            $table->unsignedInteger('items_count')->default(0);

            $table->timestamps();

            $table->unique(['user_id', 'is_default']); // Mỗi user chỉ có 1 wishlist mặc định
            $table->index(['user_id', 'privacy']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wishlists');
    }
};
