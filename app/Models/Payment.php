<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'payment';

            protected $fillable = [
            'order_id','transaction_id', 'gross_amount', 'midtrans_order_id',
            'payment_type','transaction_status','fraud_status','snap_token','payload'];
        protected $casts = [
            'payload' => 'array',
        ];

    
    public function order (){
        return $this->belongsTo(Order::class);
    }
}
