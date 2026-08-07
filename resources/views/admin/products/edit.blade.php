@extends('layouts.admin')

@section('title', 'Edit Product - NewPOS')
@section('page-eyebrow', 'Inventory')
@section('page-title', 'Edit Product')
@section('page-description', 'Adjust the product details without reopening the whole record.')
@section('page-actions')
  <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm">
    <i class="fas fa-arrow-left"></i> Back to Products
  </a>
@endsection

@section('content')
<div class="entity-page">
  <section class="entity-hero">
    <div class="entity-hero-copy">
      <p class="eyebrow">Update inventory item</p>
      <h1>Edit Product</h1>
      <p>Update pricing, stock, or description in one pass.</p>
    </div>
    <div class="entity-chip">
      <i class="fas fa-box-open"></i>
      {{ $product->code }}
    </div>
  </section>

  <div class="card shadow entity-card">
    <div class="card-header border-0">
      <h3 class="mb-0">Product details</h3>
    </div>
    <div class="card-body">
      <form method="POST" action="{{ route('admin.products.update', $product->id) }}" enctype="multipart/form-data" class="compact-form">
        @csrf
        @method('PUT')
        <div class="form-row">
          <div class="col-md-6 form-group">
            <label>Product Name</label>
            <input type="text" name="name" value="{{ $product->name }}" class="form-control" required>
          </div>
          <div class="col-md-6 form-group">
            <label>SKU</label>
            <input type="text" name="code" value="{{ $product->code }}" class="form-control" required>
          </div>
        </div>
        <div class="form-row">
          <div class="col-md-6 form-group">
            <label>Product Price</label>
            <input type="number" step="0.01" name="price" value="{{ $product->price }}" class="form-control" required>
          </div>
          <div class="col-md-6 form-group">
            <label>Stock</label>
            <input type="number" name="stock" value="{{ $product->stock }}" class="form-control" required>
          </div>
        </div>
        <div class="form-row">
          <div class="col-md-6 form-group">
            <label>Product Image</label>
            <input type="file" name="image" class="form-control-file">
          </div>
          <div class="col-md-6 form-group">
            <label>Product Description</label>
            <textarea rows="3" name="description" class="form-control">{{ $product->description }}</textarea>
          </div>
        </div>
        <div class="d-flex flex-wrap gap-2">
          <button type="submit" class="btn btn-success">Update Product</button>
          <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
