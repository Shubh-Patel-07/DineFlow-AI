<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Modifier Groups (e.g., "Crust Type", "Extra Cheese", "Spice Level")
        if (!Schema::hasTable('modifier_groups')) {
            Schema::create('modifier_groups', function (Blueprint $table) {
                $table->id();
                $table->string('restaurant_id')->index();
                $table->string('name');
                $table->string('selection_type')->default('single'); // 'single' (radio) or 'multiple' (checkbox)
                $table->integer('min_selection')->default(0);
                $table->integer('max_selection')->default(1);
                $table->boolean('is_required')->default(false);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        // 2. Modifier Options (e.g., "Thin Crust" ₹0, "Cheese Burst" +₹60)
        if (!Schema::hasTable('modifier_options')) {
            Schema::create('modifier_options', function (Blueprint $table) {
                $table->id();
                $table->foreignId('modifier_group_id')->constrained('modifier_groups')->onDelete('cascade');
                $table->string('name');
                $table->decimal('price', 8, 2)->default(0.00);
                $table->boolean('is_veg')->default(true);
                $table->boolean('is_default')->default(false);
                $table->boolean('is_available')->default(true);
                $table->timestamps();
            });
        }

        // 3. Pivot: Menu Items to Modifier Groups
        if (!Schema::hasTable('menu_item_modifiers')) {
            Schema::create('menu_item_modifiers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('menu_item_id')->constrained('menu_items')->onDelete('cascade');
                $table->foreignId('modifier_group_id')->constrained('modifier_groups')->onDelete('cascade');
                $table->timestamps();
            });
        }

        // 4. Combo Groups (e.g. "Combo Step 1: Choice of Burger", "Step 2: Choice of Drink")
        if (!Schema::hasTable('combo_groups')) {
            Schema::create('combo_groups', function (Blueprint $table) {
                $table->id();
                $table->foreignId('menu_item_id')->constrained('menu_items')->onDelete('cascade');
                $table->string('name');
                $table->integer('min_choice')->default(1);
                $table->integer('max_choice')->default(1);
                $table->timestamps();
            });
        }

        // 5. Combo Group Items
        if (!Schema::hasTable('combo_group_items')) {
            Schema::create('combo_group_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('combo_group_id')->constrained('combo_groups')->onDelete('cascade');
                $table->foreignId('menu_item_id')->constrained('menu_items')->onDelete('cascade');
                $table->decimal('extra_price', 8, 2)->default(0.00);
                $table->timestamps();
            });
        }

        // Add modifier/combo tracking flags to menu_items
        Schema::table('menu_items', function (Blueprint $table) {
            if (!Schema::hasColumn('menu_items', 'has_modifiers')) {
                $table->boolean('has_modifiers')->default(false)->after('is_veg');
            }
            if (!Schema::hasColumn('menu_items', 'is_combo')) {
                $table->boolean('is_combo')->default(false)->after('has_modifiers');
            }
        });

        // Add selected modifiers and combo details to order_items
        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'selected_modifiers')) {
                $table->json('selected_modifiers')->nullable()->after('price');
            }
            if (!Schema::hasColumn('order_items', 'combo_selections')) {
                $table->json('combo_selections')->nullable()->after('selected_modifiers');
            }
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['selected_modifiers', 'combo_selections']);
        });

        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropColumn(['has_modifiers', 'is_combo']);
        });

        Schema::dropIfExists('combo_group_items');
        Schema::dropIfExists('combo_groups');
        Schema::dropIfExists('menu_item_modifiers');
        Schema::dropIfExists('modifier_options');
        Schema::dropIfExists('modifier_groups');
    }
};
