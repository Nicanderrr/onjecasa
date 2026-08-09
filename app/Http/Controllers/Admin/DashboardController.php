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
            'cashier_count' => DB::table('users')->where('role', 'cashier')->where('is_active', true)->count(),
        ];

        $orders = DB::table('pos_orders as orders')
            ->leftJoin('users as cashier', 'cashier.id', '=', 'orders.cashier_user_id')
            ->select('orders.*', 'cashier.name as cashier_name')
            ->orderByDesc('orders.id')
            ->limit(10)
            ->get();

        $cashierPerformance = DB::table('users as cashier')
            ->leftJoin('pos_orders as orders', 'orders.cashier_user_id', '=', 'cashier.id')
            ->where('cashier.role', 'cashier')
            ->groupBy('cashier.id', 'cashier.name', 'cashier.email', 'cashier.is_active')
            ->selectRaw('cashier.id, cashier.name, cashier.email, cashier.is_active, COUNT(orders.id) as order_count, COALESCE(SUM(orders.grand_total), 0) as sales_total, COALESCE(AVG(orders.grand_total), 0) as average_order, MAX(orders.created_at) as last_sale_at')
            ->orderByDesc('sales_total')
            ->limit(8)
            ->get();

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

        return view('admin.dashboard.index', compact('stats', 'orders', 'cashierPerformance', 'chartData'));
    }
}
