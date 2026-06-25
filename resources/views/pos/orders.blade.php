@extends('layouts.app')

@section('content')
<h4 class="mb-3">Orders</h4>
<table class="table table-striped table-sm">
<thead><tr><th>Order Code</th><th>Customer</th><th>Product</th><th>Qty</th><th>Status</th></tr></thead>
<tbody>@foreach($orders as $o)<tr><td>{{ $o->order_code }}</td><td>{{ $o->customer_name }}</td><td>{{ $o->prod_name }}</td><td>{{ $o->prod_qty }}</td><td>{{ $o->order_status }}</td></tr>@endforeach</tbody>
</table>
@endsection
