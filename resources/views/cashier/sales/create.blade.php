@extends('layouts.cashier')

@section('content')
<div class="row">
  <div class="col">
    <div class="card shadow">
      <div class="card-header border-0"><h3 class="mb-0">Create Sale</h3></div>
      <div class="card-body">
        <form method="POST" action="{{ route('cashier.sales.store') }}">
          @csrf
          <div class="form-row">
            <div class="col-md-6 mb-3"><label>Customer Name</label><input class="form-control" name="customer_name" required></div>
            <div class="col-md-6 mb-3"><label>Payment Method</label><select class="form-control" name="payment_method" required><option>Cash</option><option>Mobile Money</option><option>Credit Card</option></select></div>
          </div>
          <hr>
          @for($i = 0; $i < 5; $i++)
          <div class="form-row mb-2">
            <div class="col-md-8">
              <select class="form-control" name="items[{{ $i }}][product_id]">
                <option value="">Select product</option>
                @foreach($products as $product)
                  <option value="{{ $product->id }}">{{ $product->name }} - {{ number_format($product->price,2) }} (Stock: {{ $product->stock }})</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4"><input class="form-control" type="number" min="1" name="items[{{ $i }}][qty]" placeholder="Qty"></div>
          </div>
          @endfor
          <button class="btn btn-success">Complete Sale</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
