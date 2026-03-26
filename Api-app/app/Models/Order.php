<?php

namespace App\Models;



use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;
    protected $table = 'orders';
    protected $fillable = [
        'user_id',
        'invoice_number',
        'address_id',
        'zones_region_id',
        'shipping_name',
        'shipping_phone',
        'shipping_street',
        'shipping_city',
        'shipping_province',
        'subtotal',
        'diskon',
        'ongkir',
        'total',
        'status',
        'trackingNumber',
        'estimated_delivery_min',
        'estimated_delivery_max',
    ];


    protected static function booted()
    {
        static::creating(function ($order) {
            $order->invoice_number = 'INV-' . now()->format('Ymd') . '-' . Str::upper(Str::random(8));
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function addres()
    {
        return $this->belongsTo(Addres::class);
    }
    public function order_item()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function payments(){
         return $this->hasMany(Payment::class);
    }
    public function zones_region(){
        return $this->belongsTo(ZoneRegion::class);
    }
}
