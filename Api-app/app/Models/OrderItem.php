<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;
    protected $fillable = ['order_id','produk_id','produk_title','produk_price','qty','subtotal'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
     public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
}
