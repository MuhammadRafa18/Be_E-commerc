<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DetailFaq extends Model
{
    use HasFactory;
    protected $table = 'detail_faq';
    protected $fillable = ['faq_category_id', 'quest', 'answer'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($detail_faq) {
            if (empty($detail_faq->slug) && !empty($detail_faq->quest)) {
                $detail_faq->slug = Str::slug($detail_faq->quest);
            }
        });
        static::updating(function ($detail_faq) {
            if (!empty($detail_faq->quest)) {
                $detail_faq->slug = Str::slug($detail_faq->quest);
            }
        });
    }

    public function faq()
    {
       return $this->belongsTo(Faq_category::class);
    }
}
