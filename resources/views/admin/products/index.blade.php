@extends('layouts.admin')

@section('title', 'Products - NewPOS')
@section('page-eyebrow', 'Inventory')
@section('page-title', 'Products')
@section('page-description', 'Track stock levels, pricing, and catalog updates from one compact view.')
@section('page-actions')
  <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm">
    <i class="fas fa-utensils"></i> Add Product
  </a>
@endsection

@section('content')
@php
  $lowStock = $products->filter(fn ($prod) => (int) ($prod->stock ?? 0) <= 10)->count();
  $inventoryValue = $products->sum(fn ($prod) => (float) ($prod->price ?? 0) * (int) ($prod->stock ?? 0));
  $featured = $products->first();
@endphp

<div class="entity-page">
  <section class="entity-hero entity-hero-slim">
    <div class="entity-stats">
      <div class="entity-stat">
        <span>Total Products</span>
        <strong>{{ $products->count() }}</strong>
      </div>
      <div class="entity-stat">
        <span>Low Stock</span>
        <strong>{{ $lowStock }}</strong>
      </div>
      <div class="entity-stat">
        <span>Inventory Value</span>
        <strong>{{ number_format($inventoryValue, 2) }}</strong>
      </div>
    </div>
  </section>

  <div class="card shadow entity-card">
    <div class="card-header border-0 entity-toolbar">
      <div class="entity-chip">
        <i class="fas fa-box-open"></i>
        {{ $featured ? $featured->name : 'No products yet' }}
      </div>
      <input
        type="search"
        class="form-control form-control-sm entity-filter"
        placeholder="Filter products"
        aria-label="Filter products"
        data-table-filter="#products-table"
      >
    </div>

    <div class="table-responsive">
      <table class="table align-items-center table-flush" id="products-table">
        <thead class="thead-light">
          <tr>
            <th>Image</th>
            <th>Stock</th>
            <th>Name</th>
            <th>Price</th>
            <th class="text-right">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($products as $prod)
            <tr data-filter-row>
              <td>
                @if($prod->image)
                  <img class="thumb-preview" src="{{ asset('assets/admin/img/products/'.$prod->image) }}" alt="{{ $prod->name }}">
                @else
                  <img class="thumb-preview" src="{{ asset('assets/admin/img/products/place.png') }}" alt="{{ $prod->name }}">
                @endif
              </td>
              <td>
                <span class="stock-pill {{ (int) $prod->stock <= 10 ? 'low' : 'ok' }}">
                  <i class="fas fa-cubes"></i>
                  {{ (int) $prod->stock }} units
                </span>
              </td>
              <td>
                <div class="font-weight-bold">{{ $prod->name }}</div>
                <div class="text-muted small">{{ $prod->code }}</div>
              </td>
              <td>{{ number_format($prod->price, 2) }}</td>
              <td class="text-right">
                <div class="action-group justify-content-end">
                  <form method="POST" action="{{ route('admin.products.destroy', $prod->id) }}">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger" title="Delete product">
                      <i class="fas fa-trash"></i>
                    </button>
                  </form>
                  <a href="{{ route('admin.products.edit', $prod->id) }}" class="btn btn-sm btn-outline-primary" title="Edit product">
                    <i class="fas fa-edit"></i>
                  </a>
                </div>
              </td>
            </tr>
          @empty
            <tr data-filter-empty>
              <td colspan="5" class="text-center py-5 text-muted">No products yet. Add the first item to start the catalog.</td>
            </tr>
          @endforelse
          @if($products->count())
            <tr data-filter-empty style="display:none;">
              <td colspan="5" class="text-center py-5 text-muted">No matching products found.</td>
            </tr>
          @endif
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
