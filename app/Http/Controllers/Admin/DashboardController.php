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

        return view('admin.dashboard.index', compact('stats', 'orders'));
    }
}
