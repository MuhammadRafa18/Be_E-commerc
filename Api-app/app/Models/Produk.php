<?php

namespace App\Models;


use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Produk extends Model
{
    use HasFactory;
    protected $table = "produks";
    protected $fillable = [
        'imageproduk',
        'imagebanner',
        'title',
        'type_id',
        'category_id',
        'price',
        'sell_price',
        'size',
        'stok',
        'description',
        'useproduk',
        'ingredient',
        'is_active'
    ];
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($produk) {
            if (empty($produk->slug) && !empty($produk->title)) {
                $produk->slug = Str::slug($produk->title);
            }
        });
        static::updating(function ($produk) {
            if (!empty($produk->title)) {
                $produk->slug = Str::slug($produk->title);
            }
        });
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function skin_type()
    {
        return $this->belongsToMany(SkinType::class);
    }
    public function  favorite()
    {
        return $this->hasMany(Favorite::class);
    }
     public function cart() {
        return $this->hasMany(Cart::class);
    }
}
