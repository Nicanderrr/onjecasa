@extends('layouts.admin')

@section('title', 'Add Product - NewPOS')
@section('page-eyebrow', 'Inventory')
@section('page-title', 'Add Product')
@section('page-description', 'Create an in-store product with barcode, stock, price, and image.')
@section('page-actions')
  <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm">
    <i class="fas fa-arrow-left"></i> Back to Products
  </a>
@endsection

@section('content')
@php
  $defaultImage = asset('assets/admin/img/products/place.png');
@endphp

<style>
  .pos-product-form {
    display: grid;
    gap: 1rem;
  }

  .pos-product-layout {
    display: grid;
    grid-template-columns: minmax(0, 1.1fr) minmax(320px, .9fr);
    gap: 1rem;
    align-items: start;
  }

  .pos-product-fields {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 1rem;
  }

  .pos-product-panel {
    min-width: 0;
    padding: 1rem;
    border: 1px solid var(--admin-border);
    border-radius: 8px;
    background: var(--admin-surface);
    box-shadow: var(--admin-shadow-sm);
  }

  .pos-product-panel-full {
    grid-column: 1 / -1;
  }

  .pos-panel-heading {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: .75rem;
    margin-bottom: .85rem;
  }

  .pos-panel-heading strong,
  .pos-panel-heading span {
    display: block;
  }

  .pos-panel-heading strong {
    color: var(--admin-text);
    font-size: .94rem;
    line-height: 1.2;
  }

  .pos-panel-heading span {
    margin-top: .16rem;
    color: var(--admin-muted);
    font-size: .76rem;
    line-height: 1.35;
  }

  .pos-panel-icon {
    width: 36px;
    height: 36px;
    display: inline-grid;
    place-items: center;
    flex: 0 0 auto;
    border-radius: 8px;
    background: rgba(37, 99, 235, .12);
    color: var(--admin-primary);
  }

  .pos-product-hero {
    display: flex;
    align-items: stretch;
    justify-content: space-between;
    gap: 1rem;
    padding: 1rem;
    border: 1px solid var(--admin-border);
    border-radius: 8px;
    background: linear-gradient(135deg, rgba(37, 99, 235, .08), rgba(15, 118, 110, .12));
    box-shadow: var(--admin-shadow-sm);
  }

  .pos-product-hero h1 {
    margin: 0;
    color: var(--admin-text);
    font-size: 1.35rem;
    line-height: 1.2;
  }

  .pos-product-hero p {
    margin: .35rem 0 0;
    color: var(--admin-muted);
    max-width: 52rem;
    line-height: 1.45;
  }

  .pos-product-code {
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    min-width: max-content;
    padding: .75rem .9rem;
    border: 1px solid rgba(37, 99, 235, .24);
    border-radius: 8px;
    background: var(--admin-surface);
    color: var(--admin-primary);
    font-weight: 800;
  }

  .pos-image-upload {
    display: grid;
    gap: 1rem;
    padding: 1rem;
    border: 2px dashed var(--admin-border);
    border-radius: 8px;
    background: var(--admin-surface-soft);
  }

  .pos-image-preview {
    width: 100%;
    aspect-ratio: 1.25 / 1;
    border: 1px solid var(--admin-border);
    border-radius: 8px;
    object-fit: cover;
    background: #fff;
  }

  #posProductImage {
    display: none;
  }

  .pos-current-meta {
    display: grid;
    gap: .35rem;
    margin-top: .85rem;
    padding-top: .85rem;
    border-top: 1px solid var(--admin-border);
    color: var(--admin-muted);
    font-size: .78rem;
  }

  .pos-current-meta strong {
    color: var(--admin-text);
    font-size: .84rem;
  }

  .pos-form-actions {
    display: flex;
    flex-wrap: wrap;
    gap: .65rem;
  }

  @media (max-width: 991.98px) {
    .pos-product-layout,
    .pos-product-fields {
      grid-template-columns: 1fr;
    }

    .pos-product-hero {
      flex-direction: column;
    }

    .pos-product-code {
      width: fit-content;
    }
  }

  @media (max-width: 767.98px) {
    .pos-product-form,
    .pos-product-layout,
    .pos-product-fields {
      gap: .75rem;
    }

    .pos-product-hero,
    .pos-product-panel,
    .pos-image-upload {
      padding: .85rem;
    }

    .pos-product-hero h1 {
      font-size: 1.12rem;
    }

    .pos-product-hero p,
    .pos-product-code,
    .pos-panel-heading span,
    .pos-current-meta {
      font-size: .82rem;
    }

    .pos-panel-heading {
      align-items: flex-start;
    }

    .barcode-input-action {
      display: grid;
      gap: .6rem;
    }

    .barcode-input-action .btn,
    .pos-form-actions .btn {
      width: 100%;
      justify-content: center;
    }
  }

  @media (max-width: 420px) {
    .pos-panel-icon {
      width: 32px;
      height: 32px;
    }

    .pos-image-preview {
      aspect-ratio: 1 / 1;
    }
  }
</style>

<div class="entity-page">
  <section class="pos-product-hero">
    <div>
      <p class="eyebrow mb-1">New in-store item</p>
      <h1>Add Product</h1>
      <p>Scan the product barcode, add the selling details, and attach the image cashiers will see during checkout.</p>
    </div>
    <div class="pos-product-code">
      <i class="fas fa-barcode"></i>
      Barcode ready
    </div>
  </section>

  <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="pos-product-form">
    @csrf

    <div class="pos-product-layout">
      <div class="pos-product-fields">
        <section class="pos-product-panel">
          <div class="pos-panel-heading">
            <div>
              <strong>Product Name</strong>
              <span>Name shown to cashiers and on receipts.</span>
            </div>
            <span class="pos-panel-icon"><i class="fas fa-tag"></i></span>
          </div>
          <div class="form-group mb-0">
            <label>Product Name</label>
            <input type="text" name="name" value="{{ old('name') }}" class="form-control" placeholder="e.g. Pepsi" required>
          </div>
        </section>

        <section class="pos-product-panel">
          <div class="pos-panel-heading">
            <div>
              <strong>Barcode / SKU</strong>
              <span>Scan with camera or scanner for fast POS lookup.</span>
            </div>
            <span class="pos-panel-icon"><i class="fas fa-barcode"></i></span>
          </div>
          <div class="form-group mb-0">
            <label>Barcode / SKU</label>
            <div class="barcode-input-action">
              <input id="productBarcodeInput" type="text" name="code" value="{{ old('code') }}" class="form-control" inputmode="numeric" autocomplete="off" autofocus placeholder="Scan barcode or enter SKU" data-hardware-barcode-capture>
              <button type="button" class="btn btn-outline-secondary" data-open-barcode-camera data-barcode-target="#productBarcodeInput">
                <i class="bi bi-camera-video"></i> Open Camera
              </button>
            </div>
            <div class="form-hint">Hardware scanner input is detected automatically. Leave blank only for products without a barcode.</div>
          </div>
        </section>

        <section class="pos-product-panel">
          <div class="pos-panel-heading">
            <div>
              <strong>Selling Price</strong>
              <span>Price used at cashier checkout.</span>
            </div>
            <span class="pos-panel-icon"><i class="fas fa-coins"></i></span>
          </div>
          <div class="form-group mb-0">
            <label>Product Price</label>
            <input type="number" step="0.01" min="0" name="price" value="{{ old('price') }}" class="form-control" placeholder="0.00" required>
          </div>
        </section>

        <section class="pos-product-panel">
          <div class="pos-panel-heading">
            <div>
              <strong>Stock Quantity</strong>
              <span>Available units for in-store selling.</span>
            </div>
            <span class="pos-panel-icon"><i class="fas fa-boxes"></i></span>
          </div>
          <div class="form-group mb-0">
            <label>Stock</label>
            <input type="number" min="0" step="1" name="stock" value="{{ old('stock') }}" class="form-control" placeholder="0" required>
          </div>
        </section>

        <section class="pos-product-panel">
          <div class="pos-panel-heading">
            <div>
              <strong>Low Stock Alert</strong>
              <span>Warn staff when stock reaches this number.</span>
            </div>
            <span class="pos-panel-icon"><i class="fas fa-bell"></i></span>
          </div>
          <div class="form-group mb-0">
            <label>Alert Threshold</label>
            <input type="number" min="0" step="1" name="low_stock_threshold" value="{{ old('low_stock_threshold', 5) }}" class="form-control" placeholder="5">
          </div>
        </section>

        <section class="pos-product-panel pos-product-panel-full">
          <div class="pos-panel-heading">
            <div>
              <strong>Description</strong>
              <span>Short product notes for inventory review.</span>
            </div>
            <span class="pos-panel-icon"><i class="fas fa-align-left"></i></span>
          </div>
          <div class="form-group mb-0">
            <label>Product Description</label>
            <textarea rows="4" name="description" class="form-control" placeholder="Add category, size, or product notes.">{{ old('description') }}</textarea>
          </div>
        </section>
      </div>

      <aside class="pos-product-panel">
        <div class="pos-panel-heading">
          <div>
            <strong>Product Image</strong>
            <span>Preview the image before saving the product.</span>
          </div>
          <span class="pos-panel-icon"><i class="fas fa-image"></i></span>
        </div>

        <div class="pos-image-upload">
          <img id="posImagePreview" class="pos-image-preview" src="{{ $defaultImage }}" alt="Product image preview" onerror="this.onerror=null;this.src='{{ $defaultImage }}';">
          <div>
            <label for="posProductImage" class="btn btn-light btn-sm mb-2">
              <i class="fas fa-upload"></i> Choose Product Image
            </label>
            <input id="posProductImage" type="file" name="image" accept="image/*">
            <div id="posImageFileName" class="form-hint">No image selected yet. Maximum image size: 2 MB.</div>
          </div>
        </div>

        <div class="pos-current-meta">
          <strong>New POS Record</strong>
          <span>Barcode can be scanned by camera or hardware scanner.</span>
          <span>Product code must be unique when provided.</span>
          <span>Images appear on the cashier checkout screen.</span>
        </div>
      </aside>
    </div>

    <div class="pos-form-actions">
      <button type="submit" class="btn btn-success">
        <i class="fas fa-check-circle"></i> Add Product
      </button>
      <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
  </form>
</div>
@include('admin.products.partials.barcode-scanner')
@endsection

@push('scripts')
<script>
  document.getElementById('posProductImage')?.addEventListener('change', function(event) {
    var file = event.target.files && event.target.files[0] ? event.target.files[0] : null;
    var preview = document.getElementById('posImagePreview');
    var fileName = document.getElementById('posImageFileName');

    if (!file) {
      preview.src = @json($defaultImage);
      fileName.textContent = 'No image selected yet. Maximum image size: 2 MB.';
      return;
    }

    fileName.textContent = file.name;
    var reader = new FileReader();
    reader.onload = function(loadEvent) {
      preview.src = loadEvent.target.result;
    };
    reader.readAsDataURL(file);
  });
</script>
@endpush
