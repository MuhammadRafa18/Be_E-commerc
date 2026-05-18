<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductFashion extends Model
{
    use HasFactory;
    protected $table = "product_fashion";
    protected $fillable = [
        'product_sku_id',
        'size',
        'color',
    ];

    public function product_sku(){
        return $this->belongsTo(ProductSku::class);
    }
}
