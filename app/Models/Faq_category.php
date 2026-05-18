<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Faq_category extends Model
{
    // use HasFactory;
    protected $table = 'faq_category';
    protected $fillable = ['category'];

       protected static function boot()
    {
        parent::boot();

        static::creating(function ($Faq_category) {
            if (empty($Faq_category->slug) && !empty($Faq_category->category)) {
                $Faq_category->slug = Str::slug($Faq_category->category);
            }
        });
        static::updating(function($Faq_category){
            if(!empty($Faq_category->category)){
                $Faq_category->slug = Str::slug($Faq_category->category);
            }
        });
    }

        public function  detailfaq()  {
         return $this->hasMany(DetailFaq::class);
    }
}
