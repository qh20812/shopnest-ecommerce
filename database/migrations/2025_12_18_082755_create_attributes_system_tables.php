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
        // 1. Update existing attributes table
        if (Schema::hasTable('attributes')) {
            Schema::table('attributes', function (Blueprint $table) {
                // Rename columns if they exist
                if (Schema::hasColumn('attributes', 'attribute_name') && !Schema::hasColumn('attributes', 'name')) {
                    $table->renameColumn('attribute_name', 'name');
                }
                
                // Add slug column first (without unique constraint)
                if (!Schema::hasColumn('attributes', 'slug')) {
                    $table->string('slug')->nullable()->after('name');
                }
                
                // Add new columns
                if (!Schema::hasColumn('attributes', 'description')) {
                    $table->text('description')->nullable()->after('slug');
                }
                if (!Schema::hasColumn('attributes', 'validation_rules')) {
                    $table->json('validation_rules')->nullable()->after('description');
                }
                if (!Schema::hasColumn('attributes', 'sort_order')) {
                    $table->integer('sort_order')->default(0)->after('validation_rules');
                }
                if (!Schema::hasColumn('attributes', 'is_active')) {
                    $table->boolean('is_active')->default(true)->after('sort_order');
                }
                
                // Drop old columns if needed
                if (Schema::hasColumn('attributes', 'display_name')) {
                    $table->dropColumn('display_name');
                }
                if (Schema::hasColumn('attributes', 'is_required')) {
                    $table->dropColumn('is_required');
                }
            });
            
            // Populate slug column from name with uniqueness
            DB::statement("
                UPDATE attributes 
                SET slug = CONCAT(
                    LOWER(REPLACE(REPLACE(REPLACE(name, ' ', '-'), '_', '-'), '--', '-')),
                    '-',
                    id
                )
                WHERE slug IS NULL OR slug = ''
            ");
            
            // Now add unique constraint
            Schema::table('attributes', function (Blueprint $table) {
                $table->unique('slug');
            });
            
            // Modify input_type enum
            DB::statement("ALTER TABLE attributes MODIFY COLUMN input_type ENUM('select', 'text', 'number', 'boolean', 'date', 'textarea', 'color') NOT NULL DEFAULT 'text'");
        }

        // 2. Create attribute_options table (for select type attributes)
        if (!Schema::hasTable('attribute_options')) {
            Schema::create('attribute_options', function (Blueprint $table) {
                $table->id();
                $table->foreignId('attribute_id')->constrained('attributes')->cascadeOnDelete();
                $table->string('value'); // 8GB, 16GB, Đỏ, Xanh, M, L, XL
                $table->string('label')->nullable(); // Display name
                $table->string('color_code')->nullable(); // for color attributes
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                
                $table->index(['attribute_id', 'is_active']);
            });
        }

        // 3. Create category_attribute relationship table
        if (!Schema::hasTable('category_attribute')) {
            Schema::create('category_attribute', function (Blueprint $table) {
                $table->id();
                $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
                $table->foreignId('attribute_id')->constrained('attributes')->cascadeOnDelete();
                $table->boolean('is_variant')->default(false); // true = creates SKU, false = just spec
                $table->boolean('is_required')->default(false);
                $table->boolean('is_filterable')->default(false); // show in filters
                $table->integer('sort_order')->default(0);
                $table->timestamps();
                
                $table->unique(['category_id', 'attribute_id']);
                $table->index(['category_id', 'is_variant']);
            });
        }

        // 4. Create product_attribute_values table (for non-variant attributes/specs)
        if (!Schema::hasTable('product_attribute_values')) {
            Schema::create('product_attribute_values', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
                $table->foreignId('attribute_id')->constrained('attributes')->cascadeOnDelete();
                $table->text('value'); // Free text or option_id reference
                $table->foreignId('attribute_option_id')->nullable()->constrained('attribute_options')->nullOnDelete();
                $table->timestamps();
                
                $table->unique(['product_id', 'attribute_id']);
                $table->index('attribute_option_id');
            });
        }

        // 5. Create product_variant_attribute_values table
        if (!Schema::hasTable('product_variant_attribute_values')) {
            Schema::create('product_variant_attribute_values', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_variant_id')->constrained('product_variants')->cascadeOnDelete();
                $table->foreignId('attribute_id')->constrained('attributes')->cascadeOnDelete();
                $table->text('value'); // Free text or option_id reference
                $table->foreignId('attribute_option_id')->nullable()->constrained('attribute_options')->nullOnDelete();
                $table->timestamps();
                
                $table->unique(['product_variant_id', 'attribute_id'], 'pv_attribute_unique');
                $table->index('attribute_option_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variant_attribute_values');
        Schema::dropIfExists('product_attribute_values');
        Schema::dropIfExists('category_attribute');
        Schema::dropIfExists('attribute_options');
        
        // Revert attributes table changes
        if (Schema::hasTable('attributes')) {
            Schema::table('attributes', function (Blueprint $table) {
                if (Schema::hasColumn('attributes', 'name')) {
                    $table->renameColumn('name', 'attribute_name');
                }
                if (Schema::hasColumn('attributes', 'slug')) {
                    $table->dropColumn(['slug', 'description', 'validation_rules', 'sort_order', 'is_active']);
                }
            });
        }
    }
};
