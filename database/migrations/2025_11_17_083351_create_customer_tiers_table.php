<?php
// database/migrations/2025_11_18_000019_create_customer_tiers_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_tiers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');                    // Silver, Gold, Diamond...
            $table->string('slug')->unique();          // silver, gold, diamond
            $table->unsignedInteger('min_points');     // Điểm tối thiểu để lên hạng
            $table->decimal('discount_rate', 5, 2)->default(0); // % giảm giá cố định (VD: 5.00 = 5%)
            $table->string('badge_color')->default('#95a5a6'); // Màu huy hiệu
            $table->integer('priority')->default(0);   // Thứ tự hiển thị
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed mặc định
        $this->seedDefaultTiers();
    }

    private function seedDefaultTiers(): void
    {
        DB::table('customer_tiers')->insert([
            ['name' => 'Bronze',   'slug' => 'bronze',   'min_points' => 0,     'discount_rate' => 0,    'badge_color' => '#cd7f32', 'priority' => 1],
            ['name' => 'Silver',   'slug' => 'silver',   'min_points' => 1000,  'discount_rate' => 3,    'badge_color' => '#95a5a6', 'priority' => 2],
            ['name' => 'Gold',     'slug' => 'gold',     'min_points' => 5000,  'discount_rate' => 7,    'badge_color' => '#f1c40f', 'priority' => 3],
            ['name' => 'Diamond',  'slug' => 'diamond',  'min_points' => 20000, 'discount_rate' => 12,   'badge_color' => '#3498db', 'priority' => 4],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_tiers');
    }
};
