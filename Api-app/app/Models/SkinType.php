<?php

namespace App\Models;

use App\Models\Type;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class SkinType extends Model
{
    use HasFactory;
    protected $table = 'skin_type';
    protected $fillable = ['image','type'];
   
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($Skin_type) {
            if (empty($Skin_type->slug) && !empty($Skin_type->type)) {
                $Skin_type->slug = Str::slug($Skin_type->type);
            }
        });
        static::updating(function($Skin_type){
            if(!empty($Skin_type->type)){
                $Skin_type->slug = Str::slug($Skin_type->type);
            }
        });
    }
    public function produk(){
        return $this->belongsToMany(Produk::class);
    }
}
