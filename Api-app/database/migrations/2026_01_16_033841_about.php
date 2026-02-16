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
          Schema::create('about', function (Blueprint $table) {
            $table->id();
            $table->string('headline')->nullable();
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->string('slug')->unique();
            $table->string('image')->nullable();
            $table->text('paragraf')->nullable();
            $table->string('image_visi')->nullable();
            $table->json('icon')->nullable();
            $table->json('power')->nullable();
            $table->text('visi_misi')->nullable();
            $table->timestamps();
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
