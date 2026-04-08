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
          Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('product_sku_id');
            $table->unsignedBigInteger('product_fashion_id')->nullable();
            $table->unsignedBigInteger('product_skincare_id')->nullable();
            $table->integer('qty');
            $table->boolean('is_selected')->default(true);
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->OnDelete('cascade');
            $table->foreign('product_id')->references('id')->on('product')->OnDelete('cascade');
            $table->foreign('product_sku_id')->references('id')->on('product_sku')->OnDelete('cascade');
            $table->foreign('product_fashion_id')->references('id')->on('product_fashion')->OnDelete('cascade');
            $table->foreign('product_skincare_id')->references('id')->on('product_skincare')->OnDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
