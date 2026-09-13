<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Support\AuditTrail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SaleController extends Controller
{
    public function create(): View
    {
        $products = DB::table('pos_products')->orderBy('name')->get();

        return view('cashier.sales.create', compact('products'));
    }

    public function store(Request $request): RedirectResponse
    {
        $base = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'payment_method' => ['required', 'in:Cash,Mobile Money,Credit Card'],
            'paystack_reference' => ['nullable', 'string', 'max:255'],
            'items' => ['required', 'array'],
            'items.*.product_id' => ['nullable', 'integer'],
            'items.*.qty' => ['nullable', 'integer', 'min:1'],
        ]);

        $items = collect($base['items'])
            ->filter(fn ($item) => !empty($item['product_id']) && !empty($item['qty']))
            ->values()
            ->all();

        if (count($items) === 0) {
            throw ValidationException::withMessages(['items' => 'Add at least one product item.']);
        }

        $productIds = collect($items)->pluck('product_id')->unique()->values();

        $orderId = null;

        DB::transaction(function () use ($base, $items, $productIds, &$orderId) {
            $products = DB::table('pos_products')
                ->whereIn('id', $productIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $grandTotal = 0;
            $lineItems = [];

            foreach ($items as $item) {
                $product = $products->get((int)$item['product_id']);

                if (! $product) {
                    throw ValidationException::withMessages(['items' => 'Product not found.']);
                }

                if ((int)$product->stock < (int)$item['qty']) {
                    throw ValidationException::withMessages(['items' => 'Insufficient stock for ' . $product->name]);
                }

                $lineTotal = (float)$product->price * (int)$item['qty'];
                $grandTotal += $lineTotal;

                $lineItems[] = [
                    'product_id' => $product->id,
                    'qty' => (int)$item['qty'],
                    'price' => $product->price,
                    'total' => $lineTotal,
                ];
            }

            if ($base['payment_method'] === 'Mobile Money') {
                if (empty($base['paystack_reference'])) {
                    throw ValidationException::withMessages(['payment_method' => 'Missing Paystack payment reference.']);
                }

                $secretKey = config('services.paystack.secret_key');
                if (empty($secretKey)) {
                    throw ValidationException::withMessages(['payment_method' => 'Paystack secret key is not configured.']);
                }

                $response = Http::withToken($secretKey)
                    ->acceptJson()
                    ->get('https://api.paystack.co/transaction/verify/' . urlencode($base['paystack_reference']));

                if (! $response->ok() || ! data_get($response->json(), 'status')) {
                    throw ValidationException::withMessages(['payment_method' => 'Unable to verify Paystack transaction.']);
                }

                $paystackData = data_get($response->json(), 'data', []);
                $status = (string) data_get($paystackData, 'status', '');
                $amountKobo = (int) data_get($paystackData, 'amount', 0);
                $expectedKobo = (int) round($grandTotal * 100);

                if ($status !== 'success' || $amountKobo !== $expectedKobo) {
                    throw ValidationException::withMessages(['payment_method' => 'Paystack payment verification failed.']);
                }
            }

            $orderId = DB::table('pos_orders')->insertGetId([
                'code' => 'ORD-' . now()->format('YmdHis') . '-' . random_int(100, 999),
                'customer_name' => $base['customer_name'],
                'cashier_user_id' => auth()->id(),
                'grand_total' => $grandTotal,
                'status' => 'paid',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($lineItems as $lineItem) {
                DB::table('pos_order_items')->insert([
                    'order_id' => $orderId,
                    'product_id' => $lineItem['product_id'],
                    'qty' => $lineItem['qty'],
                    'price' => $lineItem['price'],
                    'total' => $lineItem['total'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('pos_products')
                    ->where('id', $lineItem['product_id'])
                    ->decrement('stock', $lineItem['qty']);
            }

            DB::table('pos_payments')->insert([
                'order_id' => $orderId,
                'method' => $base['payment_method'],
                'amount' => $grandTotal,
                'paystack_reference' => $base['payment_method'] === 'Mobile Money' ? ($base['paystack_reference'] ?? null) : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        AuditTrail::record('sale_created', 'Created paid cashier sale #' . $orderId, [
            'auditable_type' => 'order',
            'auditable_id' => $orderId,
            'properties' => [
                'customer_name' => $base['customer_name'],
                'payment_method' => $base['payment_method'],
                'item_count' => count($items),
            ],
        ]);

        return redirect()->route('cashier.receipts.show', ['id' => $orderId, 'autoprint' => 1])
            ->with('success', 'Payment successful. Receipt generated.');
    }
}
