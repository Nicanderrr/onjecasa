@extends('layouts.admin')
@section('content')
<div class="row mb-3"><div class="col"><div class="card shadow"><div class="card-body"><h4 class="mb-0">Total Sales: {{ number_format($total,2) }}</h4></div></div></div></div>
<div class="card shadow"><div class="card-header border-0"><h3>Sales</h3></div><div class="table-responsive"><table class="table align-items-center table-flush"><thead class="thead-light"><tr><th>Order Code</th><th>Customer</th><th>Total</th><th>Method</th><th>Date</th></tr></thead><tbody>@foreach($sales as $s)<tr><td>{{ $s->code }}</td><td>{{ $s->customer_name }}</td><td>{{ number_format($s->grand_total,2) }}</td><td>{{ $s->method }}</td><td>{{ $s->created_at }}</td></tr>@endforeach</tbody></table></div><div class="p-3">{{ $sales->links() }}</div></div>
@endsection
