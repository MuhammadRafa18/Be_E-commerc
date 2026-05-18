<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class About extends Model
{
    use HasFactory;

    protected $table = 'about';

    protected $fillable = ['headline','title','subtitle','image','paragraf','image_visi','visi_misi'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($about) {
            if (empty($about->slug) && !empty($about->title)) {
                $about->slug = Str::slug($about->title);
            }
        });
        static::updating(function($about){
            if(!empty($about->title)){
                $about->slug = Str::slug($about->title);
            }
        });
    }
    public function powers()
{
    return $this->hasMany(Power::class)->orderBy('order');
}



}
