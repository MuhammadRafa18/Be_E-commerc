<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyVisitorStat extends Model
{
    use HasFactory;
    protected $table = 'daily_visitor_stats';

    protected $fillable = [
        'date',
        'total_visitors',
        'hourly_breakdown',
    ];

 
    protected $casts = [
        'date' => 'date', 
        'total_visitors' => 'integer',
        'hourly_breakdown' => 'array', 
    ];
}
