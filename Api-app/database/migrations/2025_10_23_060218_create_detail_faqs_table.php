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
        Schema::create('detail_faqs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('faq_id');
            $table->string('quest');
            $table->text('answer');
            $table->timestamps();

            $table->foreign('faq_id')->references('id')->on('faqs')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_faqs');
    }
};
