<?php

namespace App\Support;

use App\Mail\CustomerReceiptMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailReceiptSender
{
    public function sendForOrder(int $orderId): bool
    {
        $order = DB::table('pos_orders')->where('id', $orderId)->first();

        if (! $order || empty($order->customer_email)) {
            return false;
        }

        $payment = DB::table('pos_payments')->where('order_id', $orderId)->first();

        if (! $payment) {
            return false;
        }

        $items = DB::table('pos_order_items as item')
            ->join('pos_products as product', 'product.id', '=', 'item.product_id')
            ->where('item.order_id', $orderId)
            ->orderBy('item.id')
            ->get(['product.name as product_name', 'item.qty', 'item.price', 'item.total']);

        $systemName = DB::table('pos_settings')->where('key', 'system_name')->value('value') ?: config('app.name', 'NewPOS');

        try {
            Mail::to($order->customer_email, $order->customer_name)
                ->send(new CustomerReceiptMail($order, $payment, $items, (string) $systemName));

            return true;
        } catch (\Throwable $e) {
            Log::warning('Customer receipt email could not be sent.', [
                'order_id' => $orderId,
                'customer_email' => $order->customer_email,
                'mailer' => config('mail.default'),
                'message' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
