<?php
// database/migrations/2025_11_18_000024_create_shipping_packages_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_packages', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name');                    // "Gói Freeship 1 Tháng", "Gói Freeship Trọn Đời"
            $table->string('slug')->unique();          // all-in-one-month, all-in-one-lifetime
            $table->text('description')->nullable();   // Mô tả gói

            $table->decimal('price', 15, 2);           // Giá bán gói (199000, 999000...)
            $table->integer('duration_days');          // Số ngày hiệu lực (30, 365, 9999 = trọn đời)

            $table->boolean('is_active')->default(true);
            $table->boolean('is_popular')->default(false); // Gói nổi bật
            $table->integer('sort_order')->default(0);

            $table->unsignedInteger('sold_count')->default(0);     // Đã bán bao nhiêu gói
            $table->unsignedInteger('active_subscriptions')->default(0); // Đang có bao người dùng

            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });

        // Seed 3 gói mặc định (theo đúng báo cáo)
        $this->seedDefaultPackages();
    }

    private function seedDefaultPackages(): void
    {
        DB::table('shipping_packages')->insert([
            [
                'name' => 'Gói Freeship 1 Tháng',
                'slug' => 'freeship-1-month',
                'description' => 'Miễn phí vận chuyển toàn quốc trong 30 ngày',
                'price' => 199000,
                'duration_days' => 30,
                'is_popular' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Gói Freeship 1 Năm',
                'slug' => 'freeship-1-year',
                'description' => 'Tiết kiệm hơn với gói 1 năm – chỉ 999k',
                'price' => 999000,
                'duration_days' => 365,
                'is_popular' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Gói Freeship Trọn Đời',
                'slug' => 'freeship-lifetime',
                'description' => 'Chỉ thanh toán 1 lần – miễn phí ship mãi mãi!',
                'price' => 2999000,
                'duration_days' => 99999,
                'is_popular' => true,
                'sort_order' => 3,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_packages');
    }
};
