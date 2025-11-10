<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParagrafAbout extends Model
{
    use HasFactory;
     protected $table = 'paragraf_abouts';
    protected $fillable = ['imageabout','paragrafabout1','paragrafabout2','paragrafabout3','paragrafabout4','paragrafabout5'];
}
