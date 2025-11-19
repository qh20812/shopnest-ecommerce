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
        Schema::create('administrative_divisions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('country_id')->default(1)->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('type')->index(); // 'province' hoặc 'ward'
            $table->string('code', 10)->nullable()->unique(); // Mã tỉnh/xã theo quy định mới 2025
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('administrative_divisions');
    }
};
