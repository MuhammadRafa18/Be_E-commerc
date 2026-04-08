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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('product_title')->nullable();
            $table->string('product_size')->nullable();
            $table->unsignedBigInteger('produk_sell_price');
            $table->unsignedInteger('qty')->default(1);
            $table->unsignedBigInteger('subtotal');
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->OnDelete('cascade');
            $table->foreign('product_id')->references('id')->on('product')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
