<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Visitor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VisitorController extends Controller
{
    public function index(Request $request)
    {
        $range = $request->query('range', 'today');
        $query = DB::table('visitor');

        switch ($range) {
            case 'today':
                $query->whereDate('created_at', Carbon::today())
                    ->selectRaw('HOUR(created_at) as label, COUNT(*) as total')
                    ->groupBy('label')
                    ->orderBy('label');
                break;

            case 'week':
                $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                    ->selectRaw('DATE(created_at) as label, COUNT(*) as total')
                    ->groupBy('label')
                    ->orderBy('label');
                break;
            case 'month':
                $query->whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year)
                    ->selectRaw('DATE (created_at) as label, COUNT(*) as total')
                    ->groupBy('label')
                    ->orderBy('label');
                break;
            case 'years':
                $query->whereYear('created_at', Carbon::now()->year)
                    ->selectRaw('MONTH(created_at) as label, COUNT(*) as total')
                    ->groupBy('label')
                    ->orderBy('label');
                break;

            default:
                return response()->json([
                    'error' => 'Invalid range'
                ], 400);
        }

        $result = $query->get();
        return response()->json([
            'labels' => $result->pluck('label'),
            'data' => $result->pluck('total'),
        ]);
    }

    public function store(Request $request)
    {
        $visit = Visitor::create([
            'ip_address' => $request->ip(),
            'visitor' => 1,
        ]);

        return response()->json(['succes' => true], 201);
    }
}
