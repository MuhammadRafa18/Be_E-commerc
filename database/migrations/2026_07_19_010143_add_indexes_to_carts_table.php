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
        Schema::table('carts', function (Blueprint $table) {
            $table->index(['user_id', 'is_selected'], 'carts_user_id_is_selected_index');
            $table->unique([
                'user_id',
                'product_sku_id',
                'product_fashion_id',
                'product_skincare_id'
            ], 'user_cart_item_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropIndex('carts_user_id_is_selected_index');
            $table->dropUnique('user_cart_item_unique');
        });
    }
};
