@extends('layouts.cashier')

@section('content')
@if($page === 'dashboard')
<div class="row">
  <div class="col-xl-4 col-lg-6">
    <a href="{{ route('cashier.sales.create') }}"><div class="card card-stats mb-4 mb-xl-0"><div class="card-body"><div class="row"><div class="col"><h5 style="font-size: large;" class="card-title text-uppercase pt-2 mb-0">Make Orders</h5></div><div class="col-auto"><div class="icon icon-shape bg-warning text-white rounded-circle shadow"><i class="fas fa-shopping-cart"></i></div></div></div></div></div></a>
  </div>
  <div class="col-xl-4 col-lg-6">
    <a href="{{ route('cashier.pages.show','payments') }}"><div class="card card-stats mb-4 mb-xl-0"><div class="card-body"><div class="row"><div class="col"><h5 style="font-size: large;" class="card-title text-uppercase pt-2 mb-0">Complete Orders</h5></div><div class="col-auto"><div class="icon icon-shape bg-default text-white rounded-circle shadow"><i class="fas fa-dollar-sign"></i></div></div></div></div></div></a>
  </div>
  <div class="col-xl-4 col-lg-6">
    <a href="{{ route('cashier.pages.show','receipts') }}"><div class="card card-stats mb-4 mb-xl-0"><div class="card-body"><div class="row"><div class="col"><h5 style="font-size: large;" class="card-title text-uppercase pt-2 mb-0">Receipts</h5></div><div class="col-auto"><div class="icon icon-shape bg-green text-white rounded-circle shadow"><i class="fa fa-print"></i></div></div></div></div></div></a>
  </div>
</div>
@endif
<div class="row mt-5">
  <div class="col-xl-12 mb-5 mb-xl-0">
    <div class="card shadow">
      <div class="card-header border-0"><h3 class="mb-0 text-capitalize">{{ str_replace('-', ' ', $page) }}</h3></div>
      <div class="table-responsive">
        <table class="table align-items-center table-flush">
          @if($page === 'products')
          <thead class="thead-light"><tr><th>Code</th><th>Name</th><th>Price</th><th>Stock</th></tr></thead>
          <tbody>@foreach($products as $p)<tr><td>{{ $p->code }}</td><td>{{ $p->name }}</td><td>{{ number_format($p->price,2) }}</td><td>{{ $p->stock }}</td></tr>@endforeach</tbody>
          @elseif(in_array($page,['payments','payments-reports','receipts']))
          <thead class="thead-light"><tr><th>#</th><th>Order</th><th>Method</th><th>Amount</th><th>Date</th></tr></thead>
          <tbody>@foreach($payments as $pay)<tr><td>{{ $pay->id }}</td><td>#{{ $pay->order_id }}</td><td>{{ $pay->method }}</td><td>{{ number_format($pay->amount,2) }}</td><td>{{ $pay->created_at }}</td></tr>@endforeach</tbody>
          @else
          <thead class="thead-light"><tr><th>Code</th><th>Customer</th><th>Total</th><th>Status</th><th>Date</th></tr></thead>
          <tbody>@foreach($orders as $order)<tr><td>{{ $order->code }}</td><td>{{ $order->customer_name }}</td><td>{{ number_format($order->grand_total,2) }}</td><td><span class="badge badge-success">{{ $order->status }}</span></td><td>{{ $order->created_at }}</td></tr>@endforeach</tbody>
          @endif
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
