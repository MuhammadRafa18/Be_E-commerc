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
        Schema::create('produk_skin_type', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('skin_type_id');
            $table->unsignedBigInteger('produk_id');
            $table->timestamps();

            $table->foreign('skin_type_id')->references('id')->on('skin_type')->onDelete('cascade');    
            $table->foreign('produk_id')->references('id')->on('produks')->onDelete('cascade');    
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produk_types');
    }
};
