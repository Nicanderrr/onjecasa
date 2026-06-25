<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SalesController extends Controller
{
    public function index(): View
    {
        $sales = DB::table('pos_orders as o')
            ->join('pos_payments as p', 'p.order_id', '=', 'o.id')
            ->select('o.code', 'o.customer_name', 'o.grand_total', 'p.method', 'o.created_at')
            ->orderByDesc('o.id')
            ->paginate(20);

        $total = (float) DB::table('pos_payments')->sum('amount');

        return view('admin.sales.index', compact('sales', 'total'));
    }
}
