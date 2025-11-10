<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailFaq extends Model
{
    use HasFactory;
    protected $table = 'detail_faqs';
    protected $fillable = ['faq_id','quest','answer'];
     public function faq (){
        return $this->belongsTo(Faq::class);
    }
}
