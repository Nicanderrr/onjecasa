<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsReceiptSender
{
    public function sendForOrder(int $orderId): bool
    {
        $order = DB::table('pos_orders')->where('id', $orderId)->first();

        if (! $order || empty($order->customer_whatsapp) || empty($order->public_receipt_token)) {
            return false;
        }

        $phone = $this->normalizePhone((string) $order->customer_whatsapp);
        $endpoint = config('services.zeckta.endpoint');
        $apiKey = config('services.zeckta.api_key');
        $senderId = config('services.zeckta.sender_id');

        if (! $phone || ! $endpoint || ! $apiKey) {
            Log::info('SMS receipt skipped because Zeckta configuration or customer phone is missing.', [
                'order_id' => $orderId,
                'has_phone' => ! empty($order->customer_whatsapp),
                'has_endpoint' => ! empty($endpoint),
                'has_api_key' => ! empty($apiKey),
            ]);

            return false;
        }

        $payload = [
            'to' => $phone,
            'message' => $this->receiptMessage($order),
        ];

        if (! empty($senderId)) {
            $payload['sender_id'] = $senderId;
        }

        try {
            $response = Http::withToken((string) $apiKey)
                ->acceptJson()
                ->asJson()
                ->post((string) $endpoint, $payload);

            if (! $response->successful()) {
                Log::warning('SMS receipt could not be sent through Zeckta.', [
                    'order_id' => $orderId,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::warning('SMS receipt send failed.', [
                'order_id' => $orderId,
                'message' => $e->getMessage(),
            ]);

            return false;
        }
    }

    public function normalizePhone(string $phone): ?string
    {
        $clean = preg_replace('/\D+/', '', $phone) ?? '';
        $countryCode = preg_replace('/\D+/', '', (string) config('services.zeckta.default_country_code', '233')) ?? '233';

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
        $receiptUrl = route('receipts.public', $order->public_receipt_token);

        return "Thanks for shopping with {$systemName}. Your receipt {$order->code}: {$receiptUrl}";
    }
}
