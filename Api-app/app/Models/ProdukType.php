<?php

namespace App\Models;

use App\Models\Type;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProdukType extends Model
{
    use HasFactory;
    protected $table = 'produk_types';
    protected $fillable = ['image','type_id'];
    public function type (){
        return $this->belongsTo(Type::class);
    }
}
