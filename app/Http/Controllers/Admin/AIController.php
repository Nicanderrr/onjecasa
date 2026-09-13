<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use App\Support\OpenAiCredentials;
use App\Support\OpenAiRealtimeSession;

class AIController extends Controller
{
    public function index(): View
    {
        return view('admin.ai.index');
    }

    public function chat(Request $request)
    {
        $message = (string) $request->input('message', '');
        $history = $request->input('history', []);

        if (trim($message) === '') {
            return response()->json(['reply' => 'Please type a message.'], 422);
        }

        $context = $this->getSystemContext();
        $apiKey = OpenAiCredentials::apiKey();
        $model = OpenAiCredentials::model();

        if (! $apiKey) {
            return response()->json(['reply' => 'OpenAI API key is not configured. Add OPENAI_API_KEY in .env.']);
        }

        try {
            $messages = array_merge([
                ['role' => 'system', 'content' => $context],
            ], is_array($history) ? $history : []);

            $messages[] = ['role' => 'user', 'content' => $message];

            $response = Http::withToken($apiKey)
                ->timeout(30)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => $model,
                    'messages' => $messages,
                ]);

            if ($response->successful()) {
                $rawReply = (string) data_get($response->json(), 'choices.0.message.content', 'No response generated.');
                return response()->json([
                    'reply' => $this->normalizePlainReply($rawReply),
                ]);
            }

            Log::error('OpenAI API error', ['status' => $response->status(), 'body' => $response->body()]);
            $providerMessage = (string) data_get($response->json(), 'error.message', '');
            $providerCode = (string) data_get($response->json(), 'error.code', '');
            $details = trim($providerMessage . ($providerCode !== '' ? ' (' . $providerCode . ')' : ''));

            return response()->json([
                'error' => $details !== '' ? $details : ('OpenAI API error: ' . $response->status()),
            ], 500);
        } catch (\Throwable $e) {
            Log::error('Admin AI exception', ['message' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function alerts()
    {
        $lowStock = DB::table('pos_products')
            ->whereColumn('stock', '<=', 'low_stock_threshold')
            ->orderBy('stock')
            ->limit(10)
            ->get(['id', 'name', 'stock', 'low_stock_threshold']);

        $outOfStock = $lowStock->where('stock', '<=', 0)->count();

        return response()->json([
            'has_alert' => $lowStock->count() > 0,
            'low_stock_count' => $lowStock->count(),
            'out_of_stock_count' => $outOfStock,
            'items' => $lowStock->values(),
            'message' => $lowStock->count() > 0
                ? 'Low stock detected on ' . $lowStock->count() . ' products.'
                : 'No low stock alerts.',
        ]);
    }

    public function analyze(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240',
            'prompt' => 'nullable|string',
        ]);

        $file = $request->file('file');
        $prompt = (string) $request->input('prompt', 'Analyze this file and summarize only operationally useful details for the POS.');
        $apiKey = OpenAiCredentials::apiKey();

        if (! $apiKey) {
            return response()->json(['reply' => 'OpenAI API key is not configured. Add OPENAI_API_KEY in .env.']);
        }

        $extension = strtolower((string) $file->getClientOriginalExtension());
        $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true);

        if (! $isImage) {
            return response()->json([
                'reply' => 'File received: ' . $file->getClientOriginalName() . '. Document parsing is not enabled yet; upload an image for full AI analysis.',
            ]);
        }

        $model = OpenAiCredentials::model('OPENAI_VISION_MODEL');
        $base64Image = base64_encode(file_get_contents($file->path()));
        $mimeType = (string) $file->getMimeType();

        try {
            $response = Http::withToken($apiKey)
                ->timeout(60)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => $model,
                    'messages' => [[
                        'role' => 'user',
                        'content' => [
                            ['type' => 'text', 'text' => $prompt],
                            ['type' => 'image_url', 'image_url' => ['url' => "data:{$mimeType};base64,{$base64Image}"]],
                        ],
                    ]],
                ]);

            if ($response->successful()) {
                return response()->json([
                    'reply' => data_get($response->json(), 'choices.0.message.content', 'No response generated.'),
                ]);
            }

            Log::error('OpenAI vision API error', ['status' => $response->status(), 'body' => $response->body()]);
            return response()->json(['error' => 'OpenAI vision API error: ' . $response->status()], 500);
        } catch (\Throwable $e) {
            Log::error('Admin AI vision exception', ['message' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function realtimeCall(Request $request, OpenAiRealtimeSession $realtime)
    {
        return $realtime->connect(
            $request->getContent(),
            $this->getSystemContext() . "\n\nVOICE MODE:\nSpeak naturally, conversationally, and briefly like the NewSmartMart assistant. Start with a short greeting only once. The admin is currently viewing: " . str($request->header('X-NewPOS-Page', '/'))->limit(120)->toString() . ".",
            'Admin realtime AI'
        );
    }

    public function getSystemContext(): string
    {
        $totalProducts = (int) DB::table('pos_products')->count();
        $lowStockProducts = (int) DB::table('pos_products')->whereColumn('stock', '<=', 'low_stock_threshold')->count();
        $outOfStockProducts = (int) DB::table('pos_products')->where('stock', '<=', 0)->count();
        $totalOrders = (int) DB::table('pos_orders')->count();
        $pendingOrders = (int) DB::table('pos_orders')->where('status', 'pending')->count();
        $completedOrders = (int) DB::table('pos_orders')->where('status', 'completed')->count();
        $totalPayments = (int) DB::table('pos_payments')->count();
        $salesTotal = (float) DB::table('pos_payments')->sum('amount');
        $todaySales = (float) DB::table('pos_payments')->whereDate('created_at', now()->toDateString())->sum('amount');
        $totalStaff = (int) DB::table('pos_staff')->count();
        $totalCategories = (int) DB::table('pos_categories')->count();
        $adminUsers = (int) DB::table('users')->where('role', 'admin')->count();
        $cashierUsers = (int) DB::table('users')->where('role', 'cashier')->count();

        $recentOrders = DB::table('pos_orders')
            ->orderByDesc('id')
            ->limit(8)
            ->get()
            ->map(function ($o) {
                $code = $o->code ?: ('ORD-' . $o->id);
                $total = $o->grand_total ?? $o->total_amount ?? 0;
                $status = $o->status ?: 'unknown';
                return "- {$code}: total {$total}, status {$status}";
            })->implode("\n");

        $topProducts = DB::table('pos_order_items as oi')
            ->join('pos_products as p', 'p.id', '=', 'oi.product_id')
            ->selectRaw('p.name as name, SUM(oi.qty) as qty_sold')
            ->groupBy('p.id', 'p.name')
            ->orderByDesc('qty_sold')
            ->limit(5)
            ->get()
            ->map(fn ($row) => "- {$row->name}: {$row->qty_sold} sold")
            ->implode("\n");

        $lowStockNames = DB::table('pos_products')
            ->whereColumn('stock', '<=', 'low_stock_threshold')
            ->orderBy('stock')
            ->limit(10)
            ->get(['name', 'stock', 'low_stock_threshold'])
            ->map(fn ($p) => "- {$p->name}: {$p->stock} left, alert at {$p->low_stock_threshold}")
            ->implode("\n");

        $schemaOverview = collect(DB::select('SHOW TABLES'))
            ->map(function ($row) {
                $table = array_values((array) $row)[0] ?? null;
                return $table ? "- {$table}" : null;
            })
            ->reject(fn ($table) => str_contains((string) $table, 'superadmin_audit_logs'))
            ->filter()
            ->values()
            ->implode("\n");

        return "You are NewPOS Command AI Assistant.
Current System Time: " . now()->format('Y-m-d H:i:s') . "

SYSTEM STATE:
- Products: {$totalProducts}
- Low Stock (<=10): {$lowStockProducts}
- Out of Stock: {$outOfStockProducts}
- Orders: {$totalOrders}
- Pending Orders: {$pendingOrders}
- Completed Orders: {$completedOrders}
- Payments: {$totalPayments}
- Total Sales: {$salesTotal}
- Today's Sales: {$todaySales}
- Staff Records: {$totalStaff}
- Categories: {$totalCategories}
- Admin Users: {$adminUsers}
- Cashier Users: {$cashierUsers}

TOP PRODUCTS:
" . ($topProducts !== '' ? $topProducts : "- No sales yet") . "

LOW STOCK ITEMS:
" . ($lowStockNames !== '' ? $lowStockNames : "- None") . "

RECENT ORDERS:
" . ($recentOrders !== '' ? $recentOrders : "- No orders yet") . "

DATABASE TABLES:
{$schemaOverview}

INTERACTION RULES:
1. Talk like the NewSmartMart assistant: brief, natural, friendly, and useful.
2. Sound like a helpful human POS guide who is paying attention, not like a dashboard or report generator.
3. When the user asks for something, acknowledge it in a few words, then answer directly.
4. Keep most replies to one or two natural sentences unless the user asks for a detailed breakdown.
5. Do not repeat a full welcome or introduce yourself before every request.
6. Infer intent from natural wording, corrections, short phrases, synonyms, and follow-up questions. Do not force exact command phrases.
7. Use plain English only. Do not use markdown, asterisks, hashtags, numbered lists, or decorative symbols unless the user asks for a list.
8. Avoid robotic phrases like Based on the data provided, As an AI, or Here is your requested information.
9. If something looks wrong, explain it simply and say what to check next.
10. If data is missing, say exactly what is missing.
11. Never expose secrets, API keys, or raw credential values.
12. Never reveal superadmin audit logs or describe superadmin-only activity. Those records are only visible to superadmins.
13. If the user sounds casual, respond casually while staying professional.";
    }

    private function normalizePlainReply(string $reply): string
    {
        $text = str_replace(["\r\n", "\r"], "\n", $reply);
        $text = preg_replace('/[`*_#>~]+/u', '', $text);
        $text = preg_replace('/^\s*[-•]\s*/mu', '', $text);
        $text = preg_replace('/\n{3,}/', "\n\n", $text);
        $text = preg_replace('/[ \t]{2,}/', ' ', $text);
        return trim((string) $text);
    }
}
