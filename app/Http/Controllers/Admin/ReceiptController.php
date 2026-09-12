<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReceiptController extends Controller
{
    public function index(Request $request): View
    {
        $filters = [
            'search' => trim((string) $request->query('search', '')),
            'method' => trim((string) $request->query('method', '')),
            'date_from' => trim((string) $request->query('date_from', '')),
            'date_to' => trim((string) $request->query('date_to', '')),
        ];

        $summary = [
            'receipt_count' => DB::table('pos_payments')->count(),
            'total_value' => (float) DB::table('pos_payments')->sum('amount'),
            'today_value' => (float) DB::table('pos_payments')->whereDate('created_at', now()->toDateString())->sum('amount'),
            'latest_receipt_at' => DB::table('pos_payments')->max('created_at'),
        ];

        $receiptQuery = DB::table('pos_orders as o')
            ->join('pos_payments as p', 'p.order_id', '=', 'o.id')
            ->leftJoin('users as cashier', 'cashier.id', '=', 'o.cashier_user_id')
            ->select(
                'o.id',
                'o.code',
                'o.customer_name',
                'o.grand_total',
                'o.status',
                'o.created_at',
                'p.method',
                'p.amount',
                'p.paystack_reference',
                'cashier.name as cashier_name'
            );

        if ($filters['search'] !== '') {
            $receiptQuery->where(function ($query) use ($filters) {
                $search = '%' . $filters['search'] . '%';
                $query->where('o.code', 'like', $search)
                    ->orWhere('o.customer_name', 'like', $search)
                    ->orWhere('p.paystack_reference', 'like', $search)
                    ->orWhere('cashier.name', 'like', $search);
            });
        }

        if ($filters['method'] !== '') {
            $receiptQuery->where('p.method', $filters['method']);
        }

        if ($filters['date_from'] !== '') {
            $receiptQuery->whereDate('o.created_at', '>=', $filters['date_from']);
        }

        if ($filters['date_to'] !== '') {
            $receiptQuery->whereDate('o.created_at', '<=', $filters['date_to']);
        }

        $paymentMethods = DB::table('pos_payments')
            ->whereNotNull('method')
            ->distinct()
            ->orderBy('method')
            ->pluck('method');

        $receipts = $receiptQuery
            ->orderByDesc('o.id')
            ->paginate(20)
            ->appends($filters);

        return view('admin.receipts.index', compact('receipts', 'summary', 'filters', 'paymentMethods'));
    }

    public function show(int $id): View
    {
        $order = DB::table('pos_orders')->where('id', $id)->first();
        abort_unless($order, 404);
        $items = DB::table('pos_order_items as i')
            ->join('pos_products as p', 'p.id', '=', 'i.product_id')
            ->select('i.*', 'p.name as product_name', 'p.image as product_image')
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
            ->select('i.*', 'p.name as product_name', 'p.image as product_image')
            ->where('i.order_id', $id)
            ->get();
        $payment = DB::table('pos_payments')->where('order_id', $id)->first();

        return view('admin.receipts.print', compact('order', 'items', 'payment'));
    }
}
