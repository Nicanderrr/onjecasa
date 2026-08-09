<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SalesController extends Controller
{
    public function index(): View
    {
        $summary = [
            'sale_count' => DB::table('pos_orders')->count(),
            'total_sales' => (float) DB::table('pos_payments')->sum('amount'),
            'today_sales' => (float) DB::table('pos_payments')->whereDate('created_at', now()->toDateString())->sum('amount'),
            'average_sale' => (float) DB::table('pos_orders')->avg('grand_total'),
            'latest_sale_at' => DB::table('pos_orders')->max('created_at'),
        ];

        $itemCounts = DB::table('pos_order_items')
            ->selectRaw('order_id, COUNT(*) as item_count')
            ->groupBy('order_id');

        $sales = DB::table('pos_orders as o')
            ->join('pos_payments as p', 'p.order_id', '=', 'o.id')
            ->leftJoin('users as cashier', 'cashier.id', '=', 'o.cashier_user_id')
            ->leftJoinSub($itemCounts, 'items', fn ($join) => $join->on('items.order_id', '=', 'o.id'))
            ->select(
                'o.id',
                'o.code',
                'o.customer_name',
                'o.grand_total',
                'o.status',
                'o.created_at',
                'p.method',
                'p.paystack_reference',
                'cashier.name as cashier_name',
                DB::raw('COALESCE(items.item_count, 0) as item_count')
            )
            ->orderByDesc('o.id')
            ->paginate(20);

        return view('admin.sales.index', compact('sales', 'summary'));
    }
}
