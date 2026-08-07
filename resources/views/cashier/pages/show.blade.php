@extends('layouts.cashier')

@section('content')
@if($page === 'dashboard')
<section class="row g-3 mt-1" aria-label="Cashier quick actions">
  <div class="col-12 col-sm-6 col-xl-4">
    <a href="{{ route('cashier.sales.create') }}" class="text-decoration-none">
      <article class="metric-card metric-warning">
        <div class="metric-top">
          <span class="metric-label">Make Orders</span>
          <span class="metric-icon"><i class="bi bi-cart-plus" aria-hidden="true"></i></span>
        </div>
        <div class="metric-value">Start Sale</div>
        <div class="metric-meta"><span class="text-success">Fast</span><span>order entry</span></div>
      </article>
    </a>
  </div>
  <div class="col-12 col-sm-6 col-xl-4">
    <a href="{{ route('cashier.pages.show', 'payments') }}" class="text-decoration-none">
      <article class="metric-card metric-primary">
        <div class="metric-top">
          <span class="metric-label">Complete Orders</span>
          <span class="metric-icon"><i class="bi bi-credit-card" aria-hidden="true"></i></span>
        </div>
        <div class="metric-value">Payments</div>
        <div class="metric-meta"><span class="text-success">Checkout</span><span>ready</span></div>
      </article>
    </a>
  </div>
  <div class="col-12 col-sm-6 col-xl-4">
    <a href="{{ route('cashier.pages.show', 'receipts') }}" class="text-decoration-none">
      <article class="metric-card metric-success">
        <div class="metric-top">
          <span class="metric-label">Receipts</span>
          <span class="metric-icon"><i class="bi bi-printer" aria-hidden="true"></i></span>
        </div>
        <div class="metric-value">Print</div>
        <div class="metric-meta"><span class="text-success">Recent</span><span>transactions</span></div>
      </article>
    </a>
  </div>
</section>

<section class="row g-3 mt-1">
  <div class="col-12 col-xl-8">
    <div class="panel">
      <div class="panel-header">
        <div>
          <h2 class="h5 mb-1 section-title"><i class="bi bi-graph-up-arrow" aria-hidden="true"></i><span>Workspace Summary</span></h2>
          <p class="text-muted mb-0">Daily operational data for the cashier station.</p>
        </div>
      </div>
      <div class="row g-3">
        <div class="col-md-4"><div class="mini-card"><span>Products</span><strong>{{ $stats['product_count'] }}</strong></div></div>
        <div class="col-md-4"><div class="mini-card"><span>Orders</span><strong>{{ $stats['order_count'] }}</strong></div></div>
        <div class="col-md-4"><div class="mini-card"><span>Sales</span><strong>{{ number_format($stats['sales_total'], 2) }}</strong></div></div>
      </div>
    </div>
  </div>

  <div class="col-12 col-xl-4">
    <div class="panel h-100">
      <div class="panel-header">
        <div>
          <h2 class="h5 mb-1 section-title"><i class="bi bi-lightning-charge" aria-hidden="true"></i><span>Quick Actions</span></h2>
          <p class="text-muted mb-0">Common cashier tasks.</p>
        </div>
      </div>
      <div class="d-grid gap-2">
        <a class="btn btn-primary" href="{{ route('cashier.sales.create') }}"><i class="bi bi-cart-plus"></i> Make Orders</a>
        <a class="btn btn-outline-secondary" href="{{ route('cashier.pages.show', 'payments') }}"><i class="bi bi-credit-card"></i> Open Payments</a>
        <a class="btn btn-outline-secondary" href="{{ route('cashier.pages.show', 'receipts') }}"><i class="bi bi-printer"></i> View Receipts</a>
      </div>
    </div>
  </div>
</section>
@endif

<section class="row g-3 mt-1">
  <div class="col-12">
    <div class="panel">
      <div class="panel-header">
        <div>
          <h2 class="h5 mb-1 section-title"><i class="bi bi-table" aria-hidden="true"></i><span class="text-capitalize">{{ str_replace('-', ' ', $page) }}</span></h2>
          <p class="text-muted mb-0">View the current cashier section data below.</p>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table align-middle mb-0">
          @if($page === 'products')
            <thead><tr><th>Code</th><th>Name</th><th>Price</th><th>Stock</th></tr></thead>
            <tbody>@foreach($products as $p)<tr><td>{{ $p->code }}</td><td>{{ $p->name }}</td><td>{{ number_format($p->price, 2) }}</td><td>{{ $p->stock }}</td></tr>@endforeach</tbody>
          @elseif(in_array($page, ['payments', 'payments-reports', 'receipts']))
            <thead><tr><th>#</th><th>Order</th><th>Method</th><th>Amount</th><th>Date</th></tr></thead>
            <tbody>@foreach($payments as $pay)<tr><td>{{ $pay->id }}</td><td>#{{ $pay->order_id }}</td><td>{{ $pay->method }}</td><td>{{ number_format($pay->amount, 2) }}</td><td>{{ $pay->created_at }}</td></tr>@endforeach</tbody>
          @else
            <thead><tr><th>Code</th><th>Customer</th><th>Total</th><th>Status</th><th>Date</th></tr></thead>
            <tbody>@foreach($orders as $order)<tr><td>{{ $order->code }}</td><td>{{ $order->customer_name }}</td><td>{{ number_format($order->grand_total, 2) }}</td><td><span class="badge text-bg-success">{{ $order->status }}</span></td><td>{{ $order->created_at }}</td></tr>@endforeach</tbody>
          @endif
        </table>
      </div>
    </div>
  </div>
</section>
@endsection
