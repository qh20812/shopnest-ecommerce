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
            $table->foreignId('country_id')->constrained()->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('administrative_divisions')->onDelete('set null');
            $table->json('name');
            $table->integer('level')->comment('1=province, 2=district, 3=ward');
            $table->string('code', 20)->nullable();
            $table->timestamps();
            $table->index(['level', 'code']);
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
