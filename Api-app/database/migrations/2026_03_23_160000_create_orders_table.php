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
            $table->unsignedBigInteger('address_id')->nullable();
            $table->string('invoice_number')->unique();
            $table->string('shipping_name');
            $table->string('shipping_phone');
            $table->string('shipping_street');
            $table->string('shipping_city');
            $table->string('shipping_province');
            $table->unsignedBigInteger('subtotal');
            $table->unsignedInteger('diskon')->default(0);
            $table->unsignedInteger('ongkir')->default(0);
            $table->unsignedInteger('total');
            $table->enum('status', [
                'Pending',
                'Paid',
                'Diproses',
                'Dikirim',
                'Selesai',
                'Canceled',
                'Expired'
            ])->default('Pending');

            $table->string('trackingNumber')->nullable();
            $table->dateTime('estimated_delivery_min')->nullable();
            $table->dateTime('estimated_delivery_max')->nullable();
            $table->timestamps();
            $table->index(['status', 'created_at']);

            $table->foreign('user_id')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('address_id')->references('id')->on('addres')->nullOnDelete();
        
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
