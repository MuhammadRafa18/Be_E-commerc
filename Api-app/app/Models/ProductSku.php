<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSku extends Model
{
    use HasFactory;
    protected $table = "product_sku";
    protected $fillable = [
        'product_id',
        'price',
        'sell_price',
        'stock',
        'weight_gram',
    ];

     public function product(){
        return $this->belongsTo(Product::class);
    }
    public function skincare(){
        return $this->hasOne(ProductSkincare::class);
    }
     public function attribute(){
        return $this->hasMany(ProductFashion::class);
    }

}
