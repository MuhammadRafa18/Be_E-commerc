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
            $table->unsignedBigInteger('produk_id')->nullable();
            $table->string('produk_title')->nullable();
            $table->unsignedBigInteger('produk_price');
            $table->unsignedInteger('qty')->default(1);
            $table->unsignedBigInteger('subtotal');
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->OnDelete('cascade');
            $table->foreign('produk_id')->references('id')->on('produks')->nullOnDelete();
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
