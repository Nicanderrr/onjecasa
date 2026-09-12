@extends('layouts.admin')

@section('title', 'Import Products - NewPOS')
@section('page-eyebrow', 'Inventory')
@section('page-title', 'Bulk Import Products')
@section('page-description', 'Upload Excel, CSV, or SQL product data into the POS catalog.')
@section('page-actions')
  <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm">
    <i class="fas fa-arrow-left"></i> Back to Products
  </a>
@endsection

@section('content')
@php
  $errors = $errors ?? new \Illuminate\Support\ViewErrorBag;
@endphp
<style>
  .import-layout {
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(320px, .42fr);
    gap: 1rem;
    align-items: start;
  }

  .import-panel {
    padding: 1rem;
    border: 1px solid var(--admin-border);
    border-radius: 8px;
    background: var(--admin-surface);
    box-shadow: var(--admin-shadow-sm);
  }

  .import-drop {
    display: grid;
    gap: 1rem;
    padding: 1rem;
    border: 2px dashed var(--admin-border);
    border-radius: 8px;
    background: var(--admin-surface-soft);
  }

  .import-drop-icon {
    width: 48px;
    height: 48px;
    display: grid;
    place-items: center;
    border-radius: 8px;
    background: rgba(37, 99, 235, .12);
    color: var(--admin-primary);
    font-size: 1.25rem;
  }

  .import-help {
    display: grid;
    gap: .75rem;
  }

  .import-help code {
    white-space: normal;
  }

  .import-columns {
    display: grid;
    gap: .5rem;
    margin: 0;
    padding: 0;
    list-style: none;
  }

  .import-columns li {
    display: flex;
    justify-content: space-between;
    gap: 1rem;
    padding: .65rem .75rem;
    border: 1px solid var(--admin-border);
    border-radius: 8px;
    background: var(--admin-surface-soft);
  }

  @media (max-width: 991.98px) {
    .import-layout {
      grid-template-columns: 1fr;
    }
  }
</style>

<div class="import-layout">
  <section class="import-panel">
    <form method="POST" action="{{ route('admin.products.import.store') }}" enctype="multipart/form-data">
      @csrf

      <div class="import-drop">
        <span class="import-drop-icon"><i class="bi bi-file-earmark-arrow-up"></i></span>
        <div>
          <label class="form-label">Import File</label>
          <input type="file" name="import_file" class="form-control" accept=".xlsx,.xls,.csv,.sql,.txt" required>
          @error('import_file')
            <div class="text-danger small mt-1">{{ $message }}</div>
          @enderror
          <div class="form-hint mt-2">Accepted: Excel `.xlsx`, `.xls`, CSV, and SQL files. Maximum size: 10 MB.</div>
        </div>
      </div>

      <div class="form-group mt-3">
        <label class="form-label">Import Mode</label>
        <select name="mode" class="form-control" required>
          <option value="upsert" @selected(old('mode', 'upsert') === 'upsert')>Update existing products and add new products</option>
          <option value="insert_only" @selected(old('mode') === 'insert_only')>Only add new products; skip existing barcodes/SKUs</option>
        </select>
        @error('mode')
          <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
      </div>

      <div class="d-flex flex-wrap gap-2 mt-3">
        <button type="submit" class="btn btn-primary">
          <i class="bi bi-cloud-arrow-up"></i> Import Products
        </button>
        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Cancel</a>
      </div>
    </form>
  </section>

  <aside class="import-panel import-help">
    <div>
      <h3 class="h6 mb-2">Excel / CSV Columns</h3>
      <ul class="import-columns">
        <li><strong>name</strong><span>required</span></li>
        <li><strong>code</strong><span>or sku/barcode</span></li>
        <li><strong>price</strong><span>required</span></li>
        <li><strong>stock</strong><span>required</span></li>
        <li><strong>description</strong><span>optional</span></li>
        <li><strong>image</strong><span>optional filename</span></li>
      </ul>
    </div>

    <div>
      <h3 class="h6 mb-2">SQL Import Format</h3>
      <code>INSERT INTO pos_products (code, name, description, price, stock, image) VALUES ('12345', 'Apple Juice', '500ml', 12.50, 20, 'apple.jpg');</code>
      <div class="form-hint mt-2">Only `INSERT INTO pos_products (...) VALUES (...)` statements are read. Other SQL commands are ignored.</div>
    </div>
  </aside>
</div>
@endsection
