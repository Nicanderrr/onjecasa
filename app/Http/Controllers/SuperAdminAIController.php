<?php

namespace App\Http\Controllers;

use App\Support\SystemAiResponder;
use App\Support\OpenAiRealtimeSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuperAdminAIController extends Controller
{
    public function chat(Request $request, SystemAiResponder $ai)
    {
        return $ai->chat(
            $this->getSuperadminContext(),
            (string) $request->input('message', ''),
            is_array($request->input('history')) ? $request->input('history') : [],
            'Superadmin AI'
        );
    }

    public function alerts()
    {
        $lowStock = DB::table('pos_products')
            ->whereColumn('stock', '<=', 'low_stock_threshold')
            ->orderBy('stock')
            ->limit(10)
            ->get(['id', 'name', 'stock', 'low_stock_threshold']);

        $inactiveUsers = DB::table('users')->where('is_active', false)->count();

        return response()->json([
            'has_alert' => $lowStock->count() > 0 || $inactiveUsers > 0,
            'low_stock_count' => $lowStock->count(),
            'out_of_stock_count' => $lowStock->where('stock', '<=', 0)->count(),
            'items' => $lowStock->values(),
            'message' => $lowStock->count() > 0
                ? 'System alerts detected: ' . $lowStock->count() . ' low stock products and ' . $inactiveUsers . ' inactive users.'
                : 'No low stock alerts. Inactive users: ' . $inactiveUsers . '.',
        ]);
    }

    public function realtimeCall(Request $request, OpenAiRealtimeSession $realtime)
    {
        return $realtime->connect(
            $request->getContent(),
            $this->getSuperadminContext() . "\n\nVOICE MODE:\nSpeak naturally, conversationally, and briefly like the NewSmartMart assistant. Start with a short greeting only once. The superadmin is currently viewing: " . str($request->header('X-NewPOS-Page', '/'))->limit(120)->toString() . ".",
            'Superadmin realtime AI'
        );
    }

    public function getSuperadminContext(): string
    {
        $metrics = [
            'products' => (int) DB::table('pos_products')->count(),
            'stock_units' => (int) DB::table('pos_products')->sum('stock'),
            'low_stock' => (int) DB::table('pos_products')->whereColumn('stock', '<=', 'low_stock_threshold')->count(),
            'out_of_stock' => (int) DB::table('pos_products')->where('stock', '<=', 0)->count(),
            'orders' => (int) DB::table('pos_orders')->count(),
            'payments' => (int) DB::table('pos_payments')->count(),
            'sales_total' => (float) DB::table('pos_payments')->sum('amount'),
            'today_sales' => (float) DB::table('pos_payments')->whereDate('created_at', today())->sum('amount'),
            'staff' => (int) DB::table('pos_staff')->count(),
            'categories' => (int) DB::table('pos_categories')->count(),
            'pos_audits' => (int) DB::table('pos_audit_trails')->count(),
            'superadmin_audits' => (int) DB::table('superadmin_audit_logs')->count(),
        ];

        $roleCounts = DB::table('users')
            ->selectRaw('role, COUNT(*) as total, SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_total')
            ->groupBy('role')
            ->orderBy('role')
            ->get()
            ->map(fn ($row) => "- {$row->role}: {$row->total} users, {$row->active_total} active")
            ->implode("\n");

        $users = DB::table('users')
            ->orderBy('role')
            ->orderBy('name')
            ->limit(40)
            ->get(['id', 'name', 'email', 'role', 'is_active', 'created_at'])
            ->map(fn ($user) => "- #{$user->id} {$user->name}, {$user->email}, {$user->role}, " . ((int) $user->is_active === 1 ? 'active' : 'inactive') . ", created {$user->created_at}")
            ->implode("\n");

        $cashierPerformance = DB::table('pos_orders as o')
            ->leftJoin('users as u', 'u.id', '=', 'o.cashier_user_id')
            ->selectRaw('o.cashier_user_id, COALESCE(u.name, "Unknown") as cashier_name, COUNT(*) as order_count, SUM(o.grand_total) as sales_total, MAX(o.created_at) as latest_sale_at')
            ->groupBy('o.cashier_user_id', 'u.name')
            ->orderByDesc('sales_total')
            ->limit(12)
            ->get()
            ->map(fn ($row) => "- {$row->cashier_name} (user {$row->cashier_user_id}): {$row->order_count} orders, {$row->sales_total} sales, latest {$row->latest_sale_at}")
            ->implode("\n");

        $recentOrders = DB::table('pos_orders as o')
            ->leftJoin('users as u', 'u.id', '=', 'o.cashier_user_id')
            ->orderByDesc('o.id')
            ->limit(12)
            ->get(['o.id', 'o.code', 'o.customer_name', 'o.grand_total', 'o.status', 'o.created_at', 'u.name as cashier_name'])
            ->map(function ($order) {
                $code = $order->code ?: ('ORD-' . $order->id);
                return "- {$code}: {$order->customer_name}, {$order->grand_total}, {$order->status}, cashier {$order->cashier_name}, {$order->created_at}";
            })
            ->implode("\n");

        $paymentMix = DB::table('pos_payments')
            ->selectRaw('method, COUNT(*) as payment_count, SUM(amount) as total_amount')
            ->groupBy('method')
            ->orderByDesc('total_amount')
            ->get()
            ->map(fn ($row) => "- {$row->method}: {$row->payment_count} payments, {$row->total_amount} collected")
            ->implode("\n");

        $topProducts = DB::table('pos_order_items as oi')
            ->join('pos_products as p', 'p.id', '=', 'oi.product_id')
            ->selectRaw('p.name, SUM(oi.qty) as qty_sold, SUM(oi.total) as sales_total')
            ->groupBy('p.id', 'p.name')
            ->orderByDesc('qty_sold')
            ->limit(10)
            ->get()
            ->map(fn ($row) => "- {$row->name}: {$row->qty_sold} sold, {$row->sales_total} sales")
            ->implode("\n");

        $lowStock = DB::table('pos_products')
            ->whereColumn('stock', '<=', 'low_stock_threshold')
            ->orderBy('stock')
            ->limit(15)
            ->get(['code', 'name', 'stock', 'low_stock_threshold', 'price'])
            ->map(fn ($product) => "- {$product->code} {$product->name}: stock {$product->stock}, alert at {$product->low_stock_threshold}, price {$product->price}")
            ->implode("\n");

        $settings = DB::table('pos_settings')
            ->orderBy('key')
            ->get(['key', 'value'])
            ->map(fn ($setting) => "- {$setting->key}: " . $this->safeSettingValue((string) $setting->key, $setting->value))
            ->implode("\n");

        $recentAudit = DB::table('pos_audit_trails')
            ->orderByDesc('id')
            ->limit(8)
            ->get(['event', 'description', 'user_name', 'user_role', 'created_at'])
            ->map(fn ($row) => "- {$row->event}: {$row->description}, {$row->user_name} {$row->user_role}, {$row->created_at}")
            ->implode("\n");

        $recentSuperadminAudit = DB::table('superadmin_audit_logs as l')
            ->leftJoin('users as u', 'u.id', '=', 'l.user_id')
            ->orderByDesc('l.id')
            ->limit(8)
            ->get(['l.action', 'l.subject_type', 'l.subject_id', 'l.created_at', 'u.name as user_name'])
            ->map(fn ($row) => "- {$row->action}: {$row->subject_type} #{$row->subject_id}, by {$row->user_name}, {$row->created_at}")
            ->implode("\n");

        $tables = collect(DB::select('SHOW TABLES'))
            ->map(function ($row) {
                return array_values((array) $row)[0] ?? null;
            })
            ->filter()
            ->map(fn ($table) => "- {$table}")
            ->implode("\n");

        return "You are NewPOS Superadmin AI.
Current System Time: " . now()->format('Y-m-d H:i:s') . "

SECURITY SCOPE:
- You are speaking to a superadmin. You can summarize all POS, user, sales, payment, stock, staff, audit, settings, and maintenance information available in the database.
- Do not reveal API keys, passwords, password hashes, tokens, raw secret values, or full credential material even to superadmin.
- If the user asks for a secret, explain that secrets are intentionally redacted and suggest where to rotate or verify them.

SYSTEM METRICS:
- Products: {$metrics['products']}
- Stock Units: {$metrics['stock_units']}
- Low Stock: {$metrics['low_stock']}
- Out Of Stock: {$metrics['out_of_stock']}
- Orders: {$metrics['orders']}
- Payments: {$metrics['payments']}
- Total Sales: {$metrics['sales_total']}
- Today's Sales: {$metrics['today_sales']}
- Staff Records: {$metrics['staff']}
- Categories: {$metrics['categories']}
- POS Audit Events: {$metrics['pos_audits']}
- Superadmin Audit Events: {$metrics['superadmin_audits']}

ROLE COUNTS:
" . ($roleCounts !== '' ? $roleCounts : "- No users found") . "

USERS:
" . ($users !== '' ? $users : "- No users found") . "

CASHIER PERFORMANCE:
" . ($cashierPerformance !== '' ? $cashierPerformance : "- No cashier sales yet") . "

PAYMENT MIX:
" . ($paymentMix !== '' ? $paymentMix : "- No payments yet") . "

TOP PRODUCTS:
" . ($topProducts !== '' ? $topProducts : "- No products sold yet") . "

LOW STOCK:
" . ($lowStock !== '' ? $lowStock : "- None") . "

RECENT ORDERS:
" . ($recentOrders !== '' ? $recentOrders : "- No orders yet") . "

SETTINGS:
" . ($settings !== '' ? $settings : "- No settings found") . "

RECENT POS AUDIT:
" . ($recentAudit !== '' ? $recentAudit : "- No POS audit logs") . "

RECENT SUPERADMIN AUDIT:
" . ($recentSuperadminAudit !== '' ? $recentSuperadminAudit : "- No superadmin audit logs") . "

DATABASE TABLES:
{$tables}

INTERACTION RULES:
1. Talk like the NewSmartMart assistant: brief, natural, friendly, and useful.
2. Sound like a helpful human system operator who understands the whole POS, not like a dashboard or report generator.
3. When the superadmin asks for something, acknowledge it in a few words, then answer directly.
4. Keep most replies to one or two natural sentences unless the superadmin asks for a detailed breakdown.
5. Do not repeat a full welcome or introduce yourself before every request.
6. Infer intent from natural wording, corrections, short phrases, synonyms, and follow-up questions. Do not force exact command phrases.
7. Use plain English only. Do not use markdown, asterisks, hashtags, numbered lists, or decorative symbols unless the user asks for a list.
8. Avoid robotic phrases like Based on the data provided, As an AI, or Here is your requested information.
9. Give system-wide answers when asked, and include exact figures from the context.
10. If something needs attention, explain it simply and say what to check next.
11. Never expose secrets, API keys, tokens, password hashes, or credential values.
12. If the user sounds casual, respond casually while staying professional.";
    }

    private function safeSettingValue(string $key, mixed $value): string
    {
        if (preg_match('/(key|secret|token|password|credential|private)/i', $key)) {
            return '[redacted]';
        }

        $text = (string) $value;

        return mb_strlen($text) > 120 ? mb_substr($text, 0, 117) . '...' : $text;
    }
}
