<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(): View
    {
        $payments = DB::table('pos_payments as p')
            ->join('pos_orders as o', 'o.id', '=', 'p.order_id')
            ->select('p.*', 'o.code as order_code', 'o.customer_name')
            ->orderByDesc('p.id')
            ->paginate(20);

        return view('admin.payments.index', compact('payments'));
    }
}
