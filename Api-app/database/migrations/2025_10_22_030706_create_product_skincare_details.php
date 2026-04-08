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
        Schema::create('product_skincare', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_sku_id');
            $table->string('size');         
            $table->text('use_produk')->nullable();
            $table->text('ingredient')->nullable();
            $table->timestamps();

            $table->foreign('product_sku_id')->references('id')->on('product_sku')->onDelete('cascade'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_skincare');
    }
};
