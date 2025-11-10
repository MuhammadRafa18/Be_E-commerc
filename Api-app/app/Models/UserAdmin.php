<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


class UserAdmin extends  Authenticatable 
{
    use HasFactory,HasApiTokens,Notifiable;
    
    protected $table = 'user_admins';
    protected $fillable = ['email','password','role'];
    

    
    
    
}
