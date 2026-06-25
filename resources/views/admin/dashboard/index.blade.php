@extends('layouts.admin')
@section('content')
<div class="row">
  <div class="col-xl-4 col-lg-6">
    <div class="card card-stats mb-4 mb-xl-0">
      <div class="card-body">
        <div class="row">
          <div class="col">
            <h5 class="card-title text-uppercase text-muted mb-0">Products</h5>
            <span class="h2 font-weight-bold mb-0">{{ $stats['product_count'] }}</span>
          </div>
          <div class="col-auto">
            <div class="icon icon-shape bg-primary text-white rounded-circle shadow"><i class="fas fa-utensils"></i></div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-4 col-lg-6">
    <div class="card card-stats mb-4 mb-xl-0">
      <div class="card-body">
        <div class="row">
          <div class="col">
            <h5 class="card-title text-uppercase text-muted mb-0">Orders</h5>
            <span class="h2 font-weight-bold mb-0">{{ $stats['order_count'] }}</span>
          </div>
          <div class="col-auto">
            <div class="icon icon-shape bg-warning text-white rounded-circle shadow"><i class="fas fa-shopping-cart"></i></div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-xl-4 col-lg-6">
    <div class="card card-stats mb-4 mb-xl-0">
      <div class="card-body">
        <div class="row">
          <div class="col">
            <h5 class="card-title text-uppercase text-muted mb-0">Sales</h5>
            <span class="h2 font-weight-bold mb-0">{{ number_format($stats['sales_total'],2) }}</span>
          </div>
          <div class="col-auto">
            <div class="icon icon-shape bg-green text-white rounded-circle shadow"><i class="fas fa-dollar-sign"></i></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row mt-5">
  <div class="col-xl-12">
    <div class="card shadow">
      <div class="card-header border-0">
        <h3 class="mb-0">Recent Orders</h3>
      </div>
      <div class="table-responsive">
        <table class="table align-items-center table-flush">
          <thead class="thead-light">
            <tr><th>Code</th><th>Customer</th><th>Total</th><th>Status</th><th>Date</th></tr>
          </thead>
          <tbody>
            @foreach($orders as $o)
              <tr>
                <td>{{ $o->code }}</td>
                <td>{{ $o->customer_name }}</td>
                <td>{{ number_format($o->grand_total,2) }}</td>
                <td><span class="badge badge-success">{{ $o->status }}</span></td>
                <td>{{ $o->created_at }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
