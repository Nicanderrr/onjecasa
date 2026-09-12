<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Support\OpenAiRealtimeSession;
use App\Support\SystemAiResponder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AIController extends Controller
{
    public function chat(Request $request, SystemAiResponder $ai)
    {
        return $ai->chat(
            $this->getCashierContext((int) $request->user()->id),
            (string) $request->input('message', ''),
            is_array($request->input('history')) ? $request->input('history') : [],
            'Cashier AI'
        );
    }

    public function alerts(Request $request)
    {
        $userId = (int) $request->user()->id;
        $todayOrders = DB::table('pos_orders')
            ->where('cashier_user_id', $userId)
            ->whereDate('created_at', today())
            ->count();

        $lowStock = DB::table('pos_products')
            ->whereColumn('stock', '<=', 'low_stock_threshold')
            ->orderBy('stock')
            ->limit(5)
            ->get(['id', 'name', 'stock', 'low_stock_threshold']);

        return response()->json([
            'has_alert' => $lowStock->count() > 0,
            'low_stock_count' => $lowStock->count(),
            'out_of_stock_count' => $lowStock->where('stock', '<=', 0)->count(),
            'items' => $lowStock->values(),
            'message' => $lowStock->count() > 0
                ? 'Low stock products are visible in your cashier workspace. You have processed ' . $todayOrders . ' orders today.'
                : 'No low stock alerts. You have processed ' . $todayOrders . ' orders today.',
        ]);
    }

    public function realtimeCall(Request $request, OpenAiRealtimeSession $realtime)
    {
        return $realtime->connect(
            $request->getContent(),
            $this->getCashierContext((int) $request->user()->id) . "\n\nVOICE MODE:\nSpeak naturally, conversationally, and briefly like the NewSmartMart assistant. Start with a short greeting only once. The cashier is currently viewing: " . str($request->header('X-NewPOS-Page', '/'))->limit(120)->toString() . ".",
            'Cashier realtime AI'
        );
    }

    public function getCashierContext(int $userId): string
    {
        $user = auth()->user();
        $orderQuery = DB::table('pos_orders')->where('cashier_user_id', $userId);
        $paymentQuery = DB::table('pos_payments as p')
            ->join('pos_orders as o', 'o.id', '=', 'p.order_id')
            ->where('o.cashier_user_id', $userId);

        $totalOrders = (int) (clone $orderQuery)->count();
        $todayOrders = (int) (clone $orderQuery)->whereDate('created_at', today())->count();
        $mySalesTotal = (float) (clone $paymentQuery)->sum('p.amount');
        $myTodaySales = (float) (clone $paymentQuery)->whereDate('p.created_at', today())->sum('p.amount');
        $latestOrderAt = (clone $orderQuery)->max('created_at');

        $recentOrders = (clone $orderQuery)
            ->orderByDesc('id')
            ->limit(8)
            ->get(['id', 'code', 'customer_name', 'grand_total', 'status', 'created_at'])
            ->map(function ($order) {
                $code = $order->code ?: ('ORD-' . $order->id);
                return "- {$code}: {$order->customer_name}, total {$order->grand_total}, status {$order->status}, {$order->created_at}";
            })
            ->implode("\n");

        $myPaymentMix = DB::table('pos_payments as p')
            ->join('pos_orders as o', 'o.id', '=', 'p.order_id')
            ->where('o.cashier_user_id', $userId)
            ->selectRaw('p.method, COUNT(*) as payment_count, SUM(p.amount) as total_amount')
            ->groupBy('p.method')
            ->orderByDesc('total_amount')
            ->get()
            ->map(fn ($row) => "- {$row->method}: {$row->payment_count} payments, {$row->total_amount} collected")
            ->implode("\n");

        $myTopProducts = DB::table('pos_order_items as oi')
            ->join('pos_orders as o', 'o.id', '=', 'oi.order_id')
            ->join('pos_products as p', 'p.id', '=', 'oi.product_id')
            ->where('o.cashier_user_id', $userId)
            ->selectRaw('p.name, SUM(oi.qty) as qty_sold')
            ->groupBy('p.id', 'p.name')
            ->orderByDesc('qty_sold')
            ->limit(8)
            ->get()
            ->map(fn ($row) => "- {$row->name}: {$row->qty_sold} sold by this cashier")
            ->implode("\n");

        $productSummary = [
            'total' => (int) DB::table('pos_products')->count(),
            'low_stock' => (int) DB::table('pos_products')->whereColumn('stock', '<=', 'low_stock_threshold')->count(),
            'out_of_stock' => (int) DB::table('pos_products')->where('stock', '<=', 0)->count(),
        ];

        $lowStock = DB::table('pos_products')
            ->whereColumn('stock', '<=', 'low_stock_threshold')
            ->orderBy('stock')
            ->limit(8)
            ->get(['name', 'stock', 'low_stock_threshold'])
            ->map(fn ($product) => "- {$product->name}: {$product->stock} left, alert at {$product->low_stock_threshold}")
            ->implode("\n");

        return "You are NewPOS Cashier AI.
Current System Time: " . now()->format('Y-m-d H:i:s') . "

SECURITY SCOPE:
- You are speaking to cashier user ID {$userId}, {$user?->name}, {$user?->email}.
- Only use this cashier's own orders, payments, receipts, and sales performance.
- You may use general product catalog and stock information needed for cashier work.
- Do not reveal or infer data about other cashiers, admins, superadmins, users, staff records, global sales totals, audit logs, system settings, credentials, API keys, tokens, password hashes, or private records.
- If asked for another cashier's information or full business-wide information, refuse briefly and say this cashier view is limited to their own workspace.

CASHIER STATE:
- My Orders: {$totalOrders}
- My Orders Today: {$todayOrders}
- My Sales Collected: {$mySalesTotal}
- My Sales Today: {$myTodaySales}
- My Latest Sale At: " . ($latestOrderAt ?: 'No sales yet') . "

PRODUCT CATALOG SUMMARY:
- Products Available In Catalog: {$productSummary['total']}
- Low Stock Products: {$productSummary['low_stock']}
- Out Of Stock Products: {$productSummary['out_of_stock']}

MY PAYMENT MIX:
" . ($myPaymentMix !== '' ? $myPaymentMix : "- No payments recorded for this cashier") . "

MY TOP PRODUCTS:
" . ($myTopProducts !== '' ? $myTopProducts : "- No products sold by this cashier yet") . "

LOW STOCK ITEMS:
" . ($lowStock !== '' ? $lowStock : "- None") . "

MY RECENT ORDERS:
" . ($recentOrders !== '' ? $recentOrders : "- No orders recorded for this cashier") . "

INTERACTION RULES:
1. Talk like the NewSmartMart assistant: brief, natural, friendly, and useful.
2. Sound like a helpful human standing beside the cashier, not like a dashboard or report generator.
3. When the cashier asks for something, acknowledge it in a few words, then answer directly.
4. Keep most replies to one or two natural sentences unless the cashier asks for a detailed breakdown.
5. Do not repeat a full welcome or introduce yourself before every request.
6. Infer intent from natural wording, corrections, short phrases, synonyms, and follow-up questions. Do not force exact command phrases.
7. Use plain English only. Do not use markdown, asterisks, hashtags, numbered lists, or decorative symbols unless the user asks for a list.
8. Avoid robotic phrases like Based on the data provided, As an AI, or Here is your requested information.
9. Keep answers inside the cashier security scope.
10. If the cashier asks for another cashier's data, refuse naturally and explain that you can only see their own workspace.
11. If data is missing, say exactly what is missing.
12. Never expose secrets, API keys, tokens, password hashes, or credential values.
13. If the cashier sounds casual, respond casually while staying professional.";
    }
}
