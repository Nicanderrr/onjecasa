<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PublicReceiptController extends Controller
{
    public function show(string $token): View
    {
        [$order, $items, $payment] = $this->receiptData($token);

        return view('public.receipts.show', compact('order', 'items', 'payment'));
    }

    public function print(string $token): View
    {
        [$order, $items, $payment] = $this->receiptData($token);

        return view('public.receipts.print', compact('order', 'items', 'payment'));
    }

    private function receiptData(string $token): array
    {
        $order = DB::table('pos_orders')->where('public_receipt_token', $token)->first();
        abort_unless($order, 404);
        $items = DB::table('pos_order_items as i')
            ->join('pos_products as p', 'p.id', '=', 'i.product_id')
            ->select('i.*', 'p.name as product_name', 'p.image as product_image')
            ->where('i.order_id', $order->id)
            ->get();

        $payment = DB::table('pos_payments')->where('order_id', $order->id)->first();

        return [$order, $items, $payment];
    }
}
