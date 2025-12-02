<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Pivot table for role-permission many-to-many relationship
     */
    public function up(): void
    {
        Schema::create('permission_role', function (Blueprint $table) {
            $table->foreignId('role_id')
                ->constrained('roles')
                ->onDelete('cascade');
            $table->foreignId('permission_id')
                ->constrained('permissions')
                ->onDelete('cascade');
            $table->primary(['role_id', 'permission_id']);
            $table->timestamps();

            // Indexes
            $table->index(['permission_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permission_role');
    }
};
