<?php

namespace App\Models;

use App\Models\Produk;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Type extends Model
{
    use HasFactory;
    protected $table = "types";
    protected $fillable = ['type'];
    
    public function produk (){
        return $this->hasMany(Produk::class);
    }
    public function produktype (){
        return $this->belongsTo(ProdukType::class);
    }

}
