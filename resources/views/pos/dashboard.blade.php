@extends('layouts.app')

@section('content')
<div class="row g-3 mb-3">
    <div class="col-md-3"><div class="card"><div class="card-body"><div class="text-muted">Products</div><h3>{{ $products }}</h3></div></div></div>
    <div class="col-md-3"><div class="card"><div class="card-body"><div class="text-muted">Customers</div><h3>{{ $customers }}</h3></div></div></div>
    <div class="col-md-3"><div class="card"><div class="card-body"><div class="text-muted">Orders</div><h3>{{ $orders }}</h3></div></div></div>
    <div class="col-md-3"><div class="card"><div class="card-body"><div class="text-muted">Payments</div><h3>{{ number_format($payments, 2) }}</h3></div></div></div>
</div>
<div class="d-flex gap-2">
    <a class="btn btn-outline-primary" href="{{ route('products') }}">Products</a>
    <a class="btn btn-outline-primary" href="{{ route('customers') }}">Customers</a>
    <a class="btn btn-outline-primary" href="{{ route('orders') }}">Orders</a>
    <a class="btn btn-outline-primary" href="{{ route('payments') }}">Payments</a>
</div>
@endsection
