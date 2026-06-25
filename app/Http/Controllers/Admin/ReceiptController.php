<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReceiptController extends Controller
{
    public function index(): View
    {
        $receipts = DB::table('pos_orders as o')
            ->join('pos_payments as p', 'p.order_id', '=', 'o.id')
            ->select('o.id', 'o.code', 'o.customer_name', 'o.grand_total', 'o.created_at', 'p.method')
            ->orderByDesc('o.id')
            ->paginate(20);

        return view('admin.receipts.index', compact('receipts'));
    }

    public function show(int $id): View
    {
        $order = DB::table('pos_orders')->where('id', $id)->first();
        abort_unless($order, 404);
        $items = DB::table('pos_order_items as i')
            ->join('pos_products as p', 'p.id', '=', 'i.product_id')
            ->select('i.*', 'p.name as product_name')
            ->where('i.order_id', $id)->get();
        $payment = DB::table('pos_payments')->where('order_id', $id)->first();

        return view('admin.receipts.show', compact('order', 'items', 'payment'));
    }

    public function print(int $id): View
    {
        $order = DB::table('pos_orders')->where('id', $id)->first();
        abort_unless($order, 404);
        $items = DB::table('pos_order_items as i')
            ->join('pos_products as p', 'p.id', '=', 'i.product_id')
            ->select('i.*', 'p.name as product_name')
            ->where('i.order_id', $id)
            ->get();
        $payment = DB::table('pos_payments')->where('order_id', $id)->first();

        return view('admin.receipts.print', compact('order', 'items', 'payment'));
    }
}
