<?php


namespace App\Services\Dashboard;

use App\Models\Order;
use App\Models\Payment;
use App\Models\ProductSku;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function indexVisit(string $range = 'week'): array
    {
        return Cache::remember(
            "dashboard.visit.{$range}",
            now()->addMinutes(5),
            function () use ($range) {
                $today = Carbon::today()->toDateString();

                switch ($range) {
                    case 'week':
                        $historical = DB::table('daily_visitor_stats')
                            ->whereBetween('date', [
                                Carbon::now()->subDays(6)->toDateString(),
                                Carbon::yesterday()->toDateString()
                            ])
                            ->selectRaw('DATE_FORMAT(date, "%Y-%m-%d") as label, total_visitors as total')
                            ->orderBy('date')
                            ->get();
                        break;

                    case 'month':
                        $historical = DB::table('daily_visitor_stats')
                            ->whereMonth('date', Carbon::now()->month)
                            ->whereYear('date', Carbon::now()->year)
                            ->where('date', '<', $today)
                            ->selectRaw('DATE_FORMAT(date, "%Y-%m-%d") as label, total_visitors as total')
                            ->orderBy('date')
                            ->get();
                        break;

                    case 'years':
                        $historical = DB::table('daily_visitor_stats')
                            ->whereYear('date', Carbon::now()->year)
                            ->whereMonth('date', '<', Carbon::now()->month)
                            ->selectRaw('DATE_FORMAT(date, "%M") as label, SUM(total_visitors) as total, MONTH(date) as month_num')
                            ->groupBy('label', 'month_num')
                            ->orderBy('month_num')
                            ->get();
                        break;

                    default:
                        throw new \InvalidArgumentException('Invalid range');
                }

                $todayCount = DB::table('visitor')
                    ->whereDate('created_at', $today)
                    ->count();

                if ($range === 'years') {
                    $thisMonthStats = DB::table('daily_visitor_stats')
                        ->whereYear('date', Carbon::now()->year)
                        ->whereMonth('date', Carbon::now()->month)
                        ->sum('total_visitors');

                    $historical->push([
                        'label' => Carbon::now()->translatedFormat('F'),
                        'total' => (int) ($thisMonthStats + $todayCount),
                    ]);
                } else {
                    $historical->push([
                        'label' => $today,
                        'total' => $todayCount,
                    ]);
                }

                return [
                    'labels' => $historical->pluck('label'),
                    'data'   => $historical->pluck('total'),
                ];
            }
        );
    }

    public function lowStock(): array
    {
        $value = Cache::remember('dashboard.low_stock', now()->addMinutes(5), function () {
            return ProductSku::where('stock', '<=', 5)->count();
        });

        return [
            'title' => 'Product Low Stock',
            'value' => $value,
        ];
    }

    public function getTopCategories(string $filter = 'week'): array
    {
        $now = Carbon::now();

        $startDate = match ($filter) {
            'week'  => $now->copy()->subDays(7),
            'month' => $now->copy()->startOfMonth(),
            'years' => $now->copy()->startOfYear(),
            default => $now->copy()->subDays(7),
        };

        return Cache::remember(
            "dashboard.top_categories.{$filter}",
            now()->addMinutes(10),
            function () use ($startDate, $filter) {
                $categories = DB::table('orders')
                    ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                    ->join('product', 'order_items.product_id', '=', 'product.id')
                    ->join('category', 'product.category_id', '=', 'category.id')
                    ->where('orders.status', 'Selesai')
                    ->where('orders.completed_at', '>=', $startDate)
                    ->select(
                        'category.id as category_id',
                        'category.category as category_name',
                        DB::raw('SUM(order_items.subtotal) as total_revenue'),
                        DB::raw('SUM(order_items.qty) as total_qty_sold')
                    )
                    ->groupBy('category.id', 'category.category')
                    ->orderByDesc('total_revenue')
                    ->limit(5)
                    ->get();

                return [
                    'status' => 'success',
                    'message' => 'Data top kategori berhasil diambil',
                    'meta' => [
                        'filter_used' => $filter,
                        'start_date' => $startDate->toDateTimeString(),
                        'total_all_category_revenue' => $categories->sum('total_revenue'),
                    ],
                    'data' => $categories,
                ];
            }
        );
    }

    public function countOrder(): array
    {
        return Cache::remember('dashboard.count.order', now()->addMinutes(5), function () {
            $today = Order::whereBetween('created_at', [
                now()->startOfDay(),
                now()->endOfDay(),
            ])->count();

            $yesterday = Order::whereBetween('created_at', [
                now()->subDay()->startOfDay(),
                now()->subDay()->endOfDay(),
            ])->count();

            return [
                'title' => 'Total Order',
                'value' => $today,
                ...$this->calculatePercentage($today, $yesterday),
                'view' => 'Yesterday',
            ];
        });
    }

    public function countUser(): array
    {
        return Cache::remember('dashboard.count.user', now()->addMinutes(5), function () {
            $current = User::whereBetween('created_at', [
                now()->startOfMonth(),
                now()->endOfMonth(),
            ])->where('role', 'user')->count();

            $previous = User::whereBetween('created_at', [
                now()->subMonth()->startOfMonth(),
                now()->subMonth()->endOfMonth(),
            ])->where('role', 'user')->count();

            return [
                'title' => 'Total User',
                'value' => $current,
                ...$this->calculatePercentage($current, $previous),
                'view' => 'Last Month',
            ];
        });
    }

    public function countPayment(): array
    {
        return Cache::remember('dashboard.count.payment', now()->addMinutes(5), function () {
            $current = Payment::whereBetween('created_at', [
                now()->startOfMonth(),
                now()->endOfMonth(),
            ])->where('transaction_status', 'settlement')->count();

            $previous = Payment::whereBetween('created_at', [
                now()->subMonth()->startOfMonth(),
                now()->subMonth()->endOfMonth(),
            ])->where('transaction_status', 'settlement')->count();

            return [
                'title' => 'Total Transaksi',
                'value' => $current,
                ...$this->calculatePercentage($current, $previous),
                'view' => 'Last Month',
            ];
        });
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

        return [
            'persentase' => ($percentage > 0 ? '+' : '') . number_format($percentage, 2) . '%',
            'colour' => $percentage > 0 ? 'green' : ($percentage < 0 ? 'red' : 'gray'),
        ];
    }
}
