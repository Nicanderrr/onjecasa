<?php

namespace App\Support;

use App\Mail\LowStockAlertMail;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class LowStockNotifier
{
    public static function handleProduct(int $productId): void
    {
        $product = DB::table('pos_products')->where('id', $productId)->first();

        if (! $product) {
            return;
        }

        $stock = (int) $product->stock;
        $threshold = (int) ($product->low_stock_threshold ?? 5);
        $isLowStock = $stock <= $threshold;

        if (! $isLowStock) {
            if ($product->low_stock_notified_at !== null) {
                DB::table('pos_products')->where('id', $productId)->update([
                    'low_stock_notified_at' => null,
                    'updated_at' => now(),
                ]);
            }

            return;
        }

        if ($product->low_stock_notified_at !== null) {
            return;
        }

        $recipients = self::adminRecipients();

        if ($recipients->isEmpty()) {
            Log::info('Low stock email skipped because no active admin recipients exist.', [
                'product_id' => $product->id,
                'product_name' => $product->name,
            ]);

            return;
        }

        try {
            foreach ($recipients as $recipient) {
                Mail::to($recipient->email, $recipient->name)->send(new LowStockAlertMail($product));
            }

            DB::table('pos_products')->where('id', $productId)->update([
                'low_stock_notified_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Low stock email could not be sent', [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'mailer' => config('mail.default'),
                'message' => $e->getMessage(),
            ]);
        }
    }

    public static function handleProducts(iterable $productIds): void
    {
        foreach (collect($productIds)->unique()->values() as $productId) {
            self::handleProduct((int) $productId);
        }
    }

    private static function adminRecipients(): Collection
    {
        return DB::table('users')
            ->whereIn('role', ['admin', 'superadmin'])
            ->where('is_active', true)
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->get(['name', 'email']);
    }
}
