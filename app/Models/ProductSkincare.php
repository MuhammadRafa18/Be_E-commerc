<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSkincare extends Model
{
    use HasFactory;
    protected $table = "product_skincare";
    protected $fillable = [
        'product_sku_id',
        'size',
        'use_produk',
        'ingredient',
    ];

    public function product_sku()
    {
        return $this->belongsTo(ProductSku::class);
    }
}
