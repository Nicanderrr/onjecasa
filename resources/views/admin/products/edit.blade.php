@extends('layouts.admin')

@section('content')
<div class="row"><div class="col"><div class="card shadow"><div class="card-header border-0"><h3>Please Fill All Fields</h3></div><div class="card-body"><form method="POST" action="{{ route('admin.products.update',$product->id) }}" enctype="multipart/form-data">@csrf @method('PUT')
<div class="form-row"><div class="col-md-6"><label>Product Name</label><input type="text" name="name" value="{{ $product->name }}" class="form-control" required></div><div class="col-md-6"><label>SKU</label><input type="text" name="code" value="{{ $product->code }}" class="form-control" required></div></div><hr>
<div class="form-row"><div class="col-md-6"><label>Product Price</label><input type="number" step="0.01" name="price" value="{{ $product->price }}" class="form-control" required></div><div class="col-md-6"><label>Stock</label><input type="number" name="stock" value="{{ $product->stock }}" class="form-control" required></div></div><hr>
<div class="form-row"><div class="col-md-6"><label>Product Image</label><input type="file" name="image" class="btn btn-outline-success form-control"></div><div class="col-md-6"><label>Product Description</label><textarea rows="3" name="description" class="form-control">{{ $product->description }}</textarea></div></div><br>
<div class="form-row"><div class="col-md-6"><button type="submit" class="btn btn-success">Update Product</button></div></div>
</form></div></div></div></div>
@endsection
