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
        Schema::create('paragraf_abouts', function (Blueprint $table) {
                $table->id();
            $table->string('imageabout');
            $table->text('paragrafabout1');
            $table->text('paragrafabout2');
            $table->text('paragrafabout3');
            $table->text('paragrafabout4');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paragraf_abouts');
    }
};
