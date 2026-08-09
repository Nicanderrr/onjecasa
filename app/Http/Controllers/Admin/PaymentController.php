<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(): View
    {
        $summary = [
            'payment_count' => DB::table('pos_payments')->count(),
            'total_collected' => (float) DB::table('pos_payments')->sum('amount'),
            'cash_total' => (float) DB::table('pos_payments')->whereRaw('LOWER(method) = ?', ['cash'])->sum('amount'),
            'mobile_total' => (float) DB::table('pos_payments')->whereRaw('LOWER(method) = ?', ['mobile money'])->sum('amount'),
            'latest_payment_at' => DB::table('pos_payments')->max('created_at'),
        ];

        $payments = DB::table('pos_payments as p')
            ->join('pos_orders as o', 'o.id', '=', 'p.order_id')
            ->leftJoin('users as cashier', 'cashier.id', '=', 'o.cashier_user_id')
            ->select(
                'p.id',
                'p.order_id',
                'p.method',
                'p.amount',
                'p.paystack_reference',
                'p.created_at',
                'o.code as order_code',
                'o.customer_name',
                'o.status as order_status',
                'o.grand_total as order_total',
                'cashier.name as cashier_name'
            )
            ->orderByDesc('p.id')
            ->paginate(20);

        return view('admin.payments.index', compact('payments', 'summary'));
    }
}
