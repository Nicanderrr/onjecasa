@extends('layouts.admin')

@section('title', 'Add Product - NewPOS')
@section('page-eyebrow', 'Inventory')
@section('page-title', 'Add Product')
@section('page-description', 'Create a clean product entry with price, stock, and image.')
@section('page-actions')
  <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm">
    <i class="fas fa-arrow-left"></i> Back to Products
  </a>
@endsection

@section('content')
<div class="entity-page">
  <section class="entity-hero">
    <div class="entity-hero-copy">
      <p class="eyebrow">New inventory item</p>
      <h1>Add Product</h1>
      <p>Keep the SKU short, the stock exact, and the description brief.</p>
    </div>
    <div class="entity-chip">
      <i class="fas fa-bolt"></i>
      Product codes auto-fill when left blank
    </div>
  </section>

  <div class="card shadow entity-card">
    <div class="card-header border-0">
      <h3 class="mb-0">Product details</h3>
    </div>
    <div class="card-body">
      <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="compact-form">
        @csrf
        <div class="form-row">
          <div class="col-md-6 form-group">
            <label>Product Name</label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <div class="col-md-6 form-group">
            <label>SKU</label>
            <input type="text" name="code" class="form-control">
            <div class="form-hint">Leave empty to auto-generate a code.</div>
          </div>
        </div>
        <div class="form-row">
          <div class="col-md-6 form-group">
            <label>Product Price</label>
            <input type="number" step="0.01" name="price" class="form-control" required>
          </div>
          <div class="col-md-6 form-group">
            <label>Stock</label>
            <input type="number" name="stock" class="form-control" required>
          </div>
        </div>
        <div class="form-row">
          <div class="col-md-6 form-group">
            <label>Product Image</label>
            <input type="file" name="image" class="form-control-file">
          </div>
          <div class="col-md-6 form-group">
            <label>Product Description</label>
            <textarea rows="3" name="description" class="form-control"></textarea>
          </div>
        </div>
        <div class="d-flex flex-wrap gap-2">
          <button type="submit" class="btn btn-success">Add Product</button>
          <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
