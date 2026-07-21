c;<?php

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
        Schema::table('visitor', function (Blueprint $table) {
            $table->index('created_at', 'visitor_created_at_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visitor', function (Blueprint $table) {
            $table->dropIndex('visitor_created_at_index');
        });
    }
};
