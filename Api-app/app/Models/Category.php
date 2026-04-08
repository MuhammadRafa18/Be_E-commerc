<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;
    protected $table = "category";
    protected $fillable = ['category','type'];

     protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug) && !empty($category->category)) {
                $category->slug = Str::slug($category->category);
            }
        });
        static::updating(function($category){
            if(!empty($category->category)){
                $category->slug = Str::slug($category->category);
            }
        });
    }

       public function produk (){
        return $this->hasMany(Product::class);
    }
}
