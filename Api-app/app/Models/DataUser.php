<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DataUser extends Model
{
    use HasFactory,HasApiTokens,Notifiable;
    protected $table = 'data_users';
    protected $fillable = ['email','fullname','password','phone'];
  
}
