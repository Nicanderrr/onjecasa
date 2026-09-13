<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\AuditTrail;
use App\Support\LowStockNotifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductCrudController extends Controller
{
    public function index(): View
    {
        $products = DB::table('pos_products')->orderByDesc('id')->get();
        return view('admin.products.index', compact('products'));
    }

    public function create(): View
    {
        return view('admin.products.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:100', 'unique:pos_products,code'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'low_stock_threshold' => ['nullable', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        $imageName = null;
        if ($request->hasFile('image')) {
            $imageName = Str::uuid() . '.' . $request->file('image')->getClientOriginalExtension();
            $request->file('image')->move(public_path('assets/admin/img/products'), $imageName);
        }

        $productId = DB::table('pos_products')->insertGetId([
            'code' => $data['code'] ?: $this->generateProductCode(),
            'name' => $data['name'],
            'description' => $data['description'] ?? '',
            'price' => $data['price'],
            'stock' => $data['stock'],
            'low_stock_threshold' => $data['low_stock_threshold'] ?? 5,
            'image' => $imageName,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        AuditTrail::record('product_created', 'Created product ' . $data['name'], [
            'auditable_type' => 'product',
            'auditable_id' => $productId,
            'properties' => ['product' => $data],
        ]);

        LowStockNotifier::handleProduct($productId);

        return redirect()->route('admin.products.index')->with('success', 'Product Added');
    }

    public function edit(int $id): View
    {
        $product = DB::table('pos_products')->where('id', $id)->first();
        abort_unless($product, 404);
        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $product = DB::table('pos_products')->where('id', $id)->first();
        abort_unless($product, 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:100', 'unique:pos_products,code,' . $id],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'low_stock_threshold' => ['nullable', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        $imageName = $product->image ?? null;
        if ($request->hasFile('image')) {
            $imageName = Str::uuid() . '.' . $request->file('image')->getClientOriginalExtension();
            $request->file('image')->move(public_path('assets/admin/img/products'), $imageName);
        }

        DB::table('pos_products')->where('id', $id)->update([
            'code' => $data['code'],
            'name' => $data['name'],
            'description' => $data['description'] ?? '',
            'price' => $data['price'],
            'stock' => $data['stock'],
            'low_stock_threshold' => $data['low_stock_threshold'] ?? 5,
            'image' => $imageName,
            'updated_at' => now(),
        ]);

        AuditTrail::record('product_updated', 'Updated product ' . $data['name'], [
            'auditable_type' => 'product',
            'auditable_id' => $id,
            'properties' => [
                'before' => (array) $product,
                'after' => [
                    'code' => $data['code'],
                    'name' => $data['name'],
                    'description' => $data['description'] ?? '',
                    'price' => $data['price'],
                    'stock' => $data['stock'],
                    'low_stock_threshold' => $data['low_stock_threshold'] ?? 5,
                    'image' => $imageName,
                ],
            ],
        ]);

        LowStockNotifier::handleProduct($id);

        return redirect()->route('admin.products.index')->with('success', 'Product Updated');
    }

    public function destroy(int $id): RedirectResponse
    {
        $product = DB::table('pos_products')->where('id', $id)->first();
        DB::table('pos_products')->where('id', $id)->delete();
        AuditTrail::record('product_deleted', 'Deleted product ' . ($product->name ?? '#' . $id), [
            'auditable_type' => 'product',
            'auditable_id' => $id,
            'properties' => ['product' => $product ? (array) $product : null],
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Deleted');
    }

    private function generateProductCode(): string
    {
        do {
            $code = 'PRD-' . random_int(1000, 9999);
        } while (DB::table('pos_products')->where('code', $code)->exists());

        return $code;
    }
}
