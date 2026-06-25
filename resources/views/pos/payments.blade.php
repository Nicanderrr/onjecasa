@extends('layouts.app')

@section('content')
<h4 class="mb-3">Payments</h4>
<table class="table table-striped table-sm">
<thead><tr><th>Payment Code</th><th>Invoice SID</th><th>Customer ID</th><th>Method</th><th>Amount</th></tr></thead>
<tbody>@foreach($payments as $p)<tr><td>{{ $p->pay_code }}</td><td>{{ $p->SID }}</td><td>{{ $p->customer_id }}</td><td>{{ $p->pay_method }}</td><td>{{ $p->pay_amt }}</td></tr>@endforeach</tbody>
</table>
@endsection
