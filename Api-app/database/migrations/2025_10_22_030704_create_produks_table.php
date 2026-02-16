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
            $table->string('size');
            $table->decimal('rating', 2, 1)->default(0);
            $table->integer('stok');
            $table->text('description')->nullable;
            $table->text('useproduk')->nullable;
            $table->text('ingredient')->nullable;
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('category')->onDelete('cascade');
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
