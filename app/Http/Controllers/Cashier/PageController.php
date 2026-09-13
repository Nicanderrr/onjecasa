<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PageController extends Controller
{
    public function show(string $page): View
    {
        $allowed = ['dashboard','products','orders','payments','receipts','orders-reports','payments-reports','sales','settings','new-sale'];
        abort_unless(in_array($page, $allowed, true), 404);

        $receiptFilters = [
            'search' => trim((string) request('search', '')),
            'method' => trim((string) request('method', '')),
            'date_from' => trim((string) request('date_from', '')),
            'date_to' => trim((string) request('date_to', '')),
        ];

        $receiptQuery = DB::table('pos_orders as o')
            ->join('pos_payments as p', 'p.order_id', '=', 'o.id')
            ->select(
                'o.id',
                'o.code',
                'o.customer_name',
                'o.grand_total',
                'o.created_at',
                'p.method',
                'p.amount'
            );

        if ($receiptFilters['search'] !== '') {
            $receiptQuery->where(function ($query) use ($receiptFilters) {
                $search = '%' . $receiptFilters['search'] . '%';
                $query->where('o.code', 'like', $search)
                    ->orWhere('o.customer_name', 'like', $search);
            });
        }

        if ($receiptFilters['method'] !== '') {
            $receiptQuery->where('p.method', $receiptFilters['method']);
        }

        if ($receiptFilters['date_from'] !== '') {
            $receiptQuery->whereDate('o.created_at', '>=', $receiptFilters['date_from']);
        }

        if ($receiptFilters['date_to'] !== '') {
            $receiptQuery->whereDate('o.created_at', '<=', $receiptFilters['date_to']);
        }

        $data = [
            'page' => $page,
            'products' => DB::table('pos_products')->orderBy('name')->get(),
            'orders' => DB::table('pos_orders')->orderByDesc('id')->limit(20)->get(),
            'payments' => DB::table('pos_payments')->orderByDesc('id')->limit(20)->get(),
            'receipts' => $receiptQuery
                ->orderByDesc('o.id')
                ->limit(20)
                ->get(),
            'receiptFilters' => $receiptFilters,
            'paymentMethods' => DB::table('pos_payments')
                ->whereNotNull('method')
                ->distinct()
                ->orderBy('method')
                ->pluck('method'),
            'items' => DB::table('pos_order_items as i')->join('pos_products as p','p.id','=','i.product_id')->select('i.*','p.name as product_name')->orderByDesc('i.id')->limit(30)->get(),
            'stats' => [
                'product_count' => DB::table('pos_products')->count(),
                'order_count' => DB::table('pos_orders')->count(),
                'sales_total' => (float) DB::table('pos_payments')->sum('amount'),
            ],
            'lowStockProducts' => $this->lowStockProducts(),
        ];

        return view('cashier.pages.show', $data);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $request->validate([
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user = $request->user();
        $user->avatar_path = $this->storeProfileAvatar($request, $user->avatar_path);
        $user->save();

        return back()->with('success', 'Profile photo updated.');
    }

    private function storeProfileAvatar(Request $request, ?string $currentAvatar): ?string
    {
        if (! $request->hasFile('avatar')) {
            return $currentAvatar;
        }

        $dir = public_path('assets/admin/img/profiles');
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $file = $request->file('avatar');
        $fileName = 'avatar-' . now()->format('YmdHis') . '-' . random_int(100, 999) . '.' . $file->getClientOriginalExtension();
        $file->move($dir, $fileName);

        if (!empty($currentAvatar)) {
            $oldPath = $dir . DIRECTORY_SEPARATOR . $currentAvatar;
            if (is_file($oldPath)) {
                @unlink($oldPath);
            }
        }

        return $fileName;
    }

    private function lowStockProducts()
    {
        return DB::table('pos_products')
            ->whereColumn('stock', '<=', 'low_stock_threshold')
            ->orderBy('stock')
            ->orderBy('name')
            ->limit(8)
            ->get();
    }
}
