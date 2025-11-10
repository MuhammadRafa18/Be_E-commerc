<?php

namespace App\Models;

use App\Models\Addres;
use App\Models\Produk;
use App\Models\DataUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;
    protected $table = 'orders';
    protected $fillable = ['user_id','addres_id','produk_id','qty','diskon','ongkir','total','status','trackingNumber'];

    public function data_user(){
        return $this->belongsTo(DataUser::class);
    }
    public function addres(){
        return $this->belongsTo(Addres::class);
    }
    public function produk(){
        return $this->belongsTo(Produk::class);
    }
}
