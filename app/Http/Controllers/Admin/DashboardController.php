<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'product_count' => DB::table('pos_products')->count(),
            'order_count' => DB::table('pos_orders')->count(),
            'sales_total' => (float) DB::table('pos_payments')->sum('amount'),
        ];

        $orders = DB::table('pos_orders')->orderByDesc('id')->limit(10)->get();

        $chartStart = now()->startOfDay()->subDays(6);
        $dailySales = DB::table('pos_payments')
            ->selectRaw('DATE(created_at) as sale_date, SUM(amount) as total')
            ->where('created_at', '>=', $chartStart)
            ->groupByRaw('DATE(created_at)')
            ->pluck('total', 'sale_date');

        $trendDates = collect(range(0, 6))->map(fn (int $day) => $chartStart->copy()->addDays($day));
        $paymentMix = DB::table('pos_payments')
            ->selectRaw('method, SUM(amount) as total')
            ->groupBy('method')
            ->orderByDesc('total')
            ->get();

        $chartData = [
            'sales_labels' => $trendDates->map(fn ($date) => $date->format('D'))->values(),
            'sales_values' => $trendDates
                ->map(fn ($date) => (float) ($dailySales[$date->toDateString()] ?? 0))
                ->values(),
            'payment_labels' => $paymentMix->pluck('method')->values(),
            'payment_values' => $paymentMix->pluck('total')->map(fn ($total) => (float) $total)->values(),
        ];

        return view('admin.dashboard.index', compact('stats', 'orders', 'chartData'));
    }
}
