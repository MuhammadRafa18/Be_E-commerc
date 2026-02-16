<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class DataUser extends Model
{
    use HasFactory, HasApiTokens, Notifiable;
    protected $table = 'data_users';
    protected $fillable = ['email', 'fullname', 'password', 'phone'];

    public function addres()
    {
        return $this->hasMany(Addres::class);
    }
    public function  favorite()
    {
        return $this->hasMany(Favorite::class);
    }
    public function cart()
    {
        return $this->hasMany(Cart::class);
    }
}
