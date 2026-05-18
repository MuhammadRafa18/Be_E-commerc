<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;
    protected $fillable = ['order_id', 'product_id', 'product_sku_id', 'product_title', 'product_size', 'product_image', 'product_price', 'qty', 'subtotal'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function product_sku()
    {
        return $this->belongsTo(ProductSku::class, 'product_sku_id');
    }
}
