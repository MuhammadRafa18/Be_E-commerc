<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;
    protected $table = 'carts';
    protected $fillable = ['user_id', 'product_id','product_sku_id','product_fashion_id','product_skincare_id' ,'qty' ,'is_selected'];
    protected $casts = [
        'is_selected' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function product_sku()
    {
        return $this->belongsTo(ProductSku::class);
    }
    public function product_fashion()
    {
        return $this->belongsTo(ProductFashion::class);
    }
    public function product_skincare()
    {
        return $this->belongsTo(ProductSkincare::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
