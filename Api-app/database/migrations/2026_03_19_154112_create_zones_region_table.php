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
        Schema::create('zones_region', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shipping_zone_id');
            $table->string('region');
            $table->integer('estimasi_min_day')->nullable();
            $table->integer('estimasi_max_day')->nullable();
            $table->timestamps();

            $table->foreign('shipping_zone_id')->references('id')->on('shipping_zone')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zones_region');
    }
};
