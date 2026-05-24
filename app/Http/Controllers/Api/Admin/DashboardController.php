<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\ProductSku;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;


class DashboardController extends Controller
{
    public function indexVisit(Request $request)
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

    public function lowStock()
    {
        $batasMin = 5;
        $lowStockSkus = ProductSku::with('product')
            ->where('stock', '<=', $batasMin)
            ->where('stock', '=', 0)->count();
        return response()->json([
            'title' => 'Prodcut Low Stock',
            'value' => $lowStockSkus
        ]);
    }

    public function getTopCategories(Request $request)
    {
        $filter = $request->query('filter', 'week');
        $now = Carbon::now();
        $startDate = match ($filter) {
            'week'  => Carbon::now()->subDays(7),
            'month' => $now->startOfMonth()->toDateTimeString(),
            'year'  => $now->startOfYear()->toDateTimeString(),
            default => Carbon::now()->subDays(7),
        };

        $categoriesReport = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('product', 'order_items.product_id', '=', 'product.id')
            ->join('category', 'product.category_id', '=', 'category.id')
            ->where('orders.status', 'Selesai')
            ->where('orders.completed_at', '>=', $startDate)
            ->select(
                'category.id as categories_id',
                'category.category as categories_name',
                DB::raw('SUM(order_items.subtotal) as total_revenue'),
                DB::raw('SUM(order_items.qty) as total_qty_sold')
            )
            ->groupBy('category.id', 'category.category')
            ->orderBy('total_revenue', 'DESC')
            ->get();
        $totalAllRevenue = $categoriesReport->sum('total_revenue');
        return response()->json([
            'status' => 'success',
            'message' => 'Data top transaksi berhasil diambil',
            'meta' => [
                'filter_used' => $filter,
                'start_date' => $startDate,
                'total_all_category_revenue' => $totalAllRevenue
            ],
            'data' => $categoriesReport
        ], 200);
    }

    public function countOrder()
    {
        $today = Order::whereBetween('created_at', [
            now()->startOfDay(),
            now()->endOfDay()
        ])->count();

        $yesterday = Order::whereBetween('created_at', [
            now()->subDay()->startOfDay(),
            now()->subDay()->endOfDay()
        ])->count();

        $result = $this->calculatePercentage($today, $yesterday);

        return response()->json([
            'title' => 'Total Order',
            'value' => $today,
            'persentase' => $result['persentase'],
            'colour' => $result['colour'],
            'view' => 'Yesterdey',
        ]);
    }

    public function countUser()
    {
        $current = User::whereBetween('created_at', [
            now()->startOfMonth(),
            now()->endOfMonth()
        ])->where('role', 'user')->count();

        $previous = User::whereBetween('created_at', [
            now()->subMonth()->startOfMonth(),
            now()->subMonth()->endOfMonth()
        ])->where('role', 'user')->count();

        $result = $this->calculatePercentage($current, $previous);

        return response()->json([
            'title' => 'Total User',
            'value' => $current,
            'persentase' => $result['persentase'],
            'colour' => $result['colour'],
            'view' => 'Last Month',
        ]);
    }

    public function countPayment()
    {
        $current = Payment::whereBetween('created_at', [
            now()->startOfMonth(),
            now()->endOfMonth()
        ])->where('transaction_status', 'settlement')->count();

        $previous = Payment::whereBetween('created_at', [
            now()->subMonth()->startOfMonth(),
            now()->subMonth()->endOfMonth()
        ])->where('transaction_status', 'settlement')->count();

        $result = $this->calculatePercentage($current, $previous);

        return response()->json([
            'title' => 'Total Transaksi',
            'value' => $current,
            'persentase' => $result['persentase'],
            'colour' => $result['colour'],
            'view' => 'Last Month',
        ]);
    }

    private function calculatePercentage(int|float $current, int|float $previous): array
    {
        if ($previous == 0 && $current == 0) {
            return [
                'persentase' => '0.00%',
                'colour' => 'gray',
            ];
        }
        if ($previous == 0 && $current > 0) {
            return [
                'persentase' => '+100.00%',
                'colour' => 'green',
            ];
        }
        $percentage = (($current - $previous) / $previous) * 100;
        $colour = match (true) {
            $percentage > 0 => 'green',
            $percentage < 0 => 'red',
            default => 'gray',
        };
        $sign = $percentage > 0 ? '+' : '';

        return [
            'persentase' => $sign . number_format($percentage, 2) . '%',
            'colour' => $colour,
        ];
    }
}
