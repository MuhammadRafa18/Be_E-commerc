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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('addres_id');
            $table->unsignedBigInteger('produk_id');
            $table->integer('qty')->default(0);
            $table->integer('diskon');
            $table->integer('ongkir');
            $table->integer('total');
            $table->string('status')->default('Pending');
            $table->string('trackingNumber')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('data_users')->onDelete('cascade');
            $table->foreign('addres_id')->references('id')->on('addres')->onDelete('cascade');
            $table->foreign('produk_id')->references('id')->on('produks')->onDelete('cascade');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
