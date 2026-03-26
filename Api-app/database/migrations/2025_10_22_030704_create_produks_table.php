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
        Schema::create('produks', function (Blueprint $table) {
            $table->id();
            $table->string('imageproduk');
            $table->string('imagebanner');
            $table->string('title');
            $table->string('slug')->unique()->nullable();
            $table->unsignedBigInteger('category_id');
            $table->integer('price');
            $table->integer('sell_price');
            $table->integer('size');
            $table->integer('stok');
            $table->text('description')->nullable;
            $table->text('useproduk')->nullable;
            $table->text('ingredient')->nullable;
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('category')->restrictOnDelete('cascade');
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produks');
    }
};
