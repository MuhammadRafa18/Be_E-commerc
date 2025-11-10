<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Addres extends Model
{
    use HasFactory;
    protected $table = 'addres';

    protected $fillable = ['user_id','fullname','streetname','place','provinci','city'];
     
    public function  data_user()  {
         return $this->belongsTo(DataUser::class);
    }
}
