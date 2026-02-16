<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;
    protected $table = '';
    protected $fillable = ['user_id','produk_id','qty'];

    public function produk() {
        return $this->belongsTo(Produk::class);
    }
     public function user() {
        return $this->belongsTo(DataUser::class);
    }
}
