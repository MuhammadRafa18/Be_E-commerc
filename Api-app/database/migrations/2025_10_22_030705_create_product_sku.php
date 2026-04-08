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
         Schema::create('product_sku', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->integer('price');
            $table->integer('sell_price');
            $table->integer('stock')->default(0);         
            $table->integer('weight_gram')->default(0);         
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('product')->onDelete('cascade'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_sku');
    }
};
