<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AggregateVisitorStats extends Command
{
    protected $signature = 'visitor:aggregate';
    protected $description = 'Rekap data visitor mentah ke summary table harian';
    public function handle()
    {
   
        $yesterday = Carbon::yesterday()->toDateString();

        $hourlyData = DB::table('visitor')
            ->whereDate('created_at', $yesterday)
            ->selectRaw('HOUR(created_at) as hour, COUNT(*) as total')
            ->groupBy('hour')
            ->pluck('total', 'hour')
            ->toArray();

        $total = array_sum($hourlyData);


        DB::table('daily_visitor_stats')->updateOrCreate(
            ['date' => $yesterday],
            [
                'total_visitors' => $total,
                'hourly_breakdown' => json_encode($hourlyData),
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        $this->info("Rekap visitor untuk tanggal {$yesterday} berhasil disimpan.");
    }
}
