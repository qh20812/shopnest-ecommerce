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
        Schema::create('shippers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('phone', 20)->unique();
            $table->string('email')->nullable();
            $table->foreignId('province_id')->constrained('administrative_divisions'); // Khu vực hoạt động chính
            $table->string('vehicle_type')->default('Xe máy'); // Xe máy, ô tô...
            $table->string('license_plate')->nullable();

            $table->string('status')->default('active'); // active, inactive, busy
            $table->decimal('rating', 3, 2)->default(5.00);
            $table->unsignedInteger('total_deliveries')->default(0);
            $table->unsignedInteger('completed_deliveries')->default(0);

            $table->timestamp('last_active_at')->nullable();
            $table->timestamps();

            $table->index(['province_id', 'status']);
            $table->index('rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shippers');
    }
};
