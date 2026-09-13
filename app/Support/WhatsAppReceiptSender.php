<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppReceiptSender
{
    public function sendForOrder(int $orderId): bool
    {
        $order = DB::table('pos_orders')->where('id', $orderId)->first();

        if (! $order || empty($order->customer_whatsapp)) {
            return false;
        }

        $phone = $this->normalizePhone((string) $order->customer_whatsapp);
        $token = config('services.whatsapp.access_token');
        $phoneNumberId = config('services.whatsapp.phone_number_id');
        $version = config('services.whatsapp.api_version', 'v20.0');

        if (! $phone || ! $token || ! $phoneNumberId) {
            Log::info('WhatsApp receipt skipped because configuration or customer phone is missing.', [
                'order_id' => $orderId,
                'has_phone' => ! empty($order->customer_whatsapp),
                'has_token' => ! empty($token),
                'has_phone_number_id' => ! empty($phoneNumberId),
            ]);

            return false;
        }

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $phone,
            'type' => 'text',
            'text' => [
                'preview_url' => false,
                'body' => $this->receiptMessage($order),
            ],
        ];

        try {
            $response = Http::withToken((string) $token)
                ->acceptJson()
                ->post("https://graph.facebook.com/{$version}/{$phoneNumberId}/messages", $payload);

            if (! $response->successful()) {
                Log::warning('WhatsApp receipt could not be sent.', [
                    'order_id' => $orderId,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::warning('WhatsApp receipt send failed.', [
                'order_id' => $orderId,
                'message' => $e->getMessage(),
            ]);

            return false;
        }
    }

    public function normalizePhone(string $phone): ?string
    {
        $clean = preg_replace('/\D+/', '', $phone) ?? '';
        $countryCode = preg_replace('/\D+/', '', (string) config('services.whatsapp.default_country_code', '233')) ?? '233';

        if ($clean === '') {
            return null;
        }

        if (str_starts_with($clean, '00')) {
            $clean = substr($clean, 2);
        }

        if (str_starts_with($clean, '0') && $countryCode !== '') {
            $clean = $countryCode . ltrim($clean, '0');
        }

        return $clean !== '' ? $clean : null;
    }

    private function receiptMessage(object $order): string
    {
        $systemName = DB::table('pos_settings')->where('key', 'system_name')->value('value') ?: config('app.name', 'NewPOS');
        $payment = DB::table('pos_payments')->where('order_id', $order->id)->first();
        $items = DB::table('pos_order_items as item')
            ->join('pos_products as product', 'product.id', '=', 'item.product_id')
            ->where('item.order_id', $order->id)
            ->orderBy('item.id')
            ->get(['product.name', 'item.qty', 'item.price', 'item.total']);

        $lines = $items->map(function ($item) {
            $total = number_format((float) $item->total, 2);

            return "{$item->name} x{$item->qty} - {$total}";
        })->implode("\n");

        return trim("Thank you for shopping with {$systemName}.\n\nReceipt: {$order->code}\nCustomer: {$order->customer_name}\nPayment: " . ($payment->method ?? 'Paid') . "\n\nItems:\n{$lines}\n\nTotal: " . number_format((float) $order->grand_total, 2));
    }
}
