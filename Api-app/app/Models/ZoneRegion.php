<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZoneRegion extends Model
{
    use HasFactory;
    protected $table = 'zones_region';
    protected $fillable = ['shipping_zone_id', 'region','estimasi_min_day','estimasi_max_day'];

    public function shipping_zone()
    {
       return $this->belongsTo(ShippingZone::class);
    }
}
