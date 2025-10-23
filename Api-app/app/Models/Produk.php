<?php

namespace App\Models;

use App\Models\Type;
use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Produk extends Model
{
    use HasFactory;
     public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function type()
    {
        return $this->belongsTo(Type::class);
    }
    protected $table = "produks";
    protected $fillable = ['imageproduk','imagebanner','title','type_id','category_id','price','size','rating','stok','description','useproduk','ingredient'];
}
