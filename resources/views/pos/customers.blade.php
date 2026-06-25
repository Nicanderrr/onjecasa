@extends('layouts.app')

@section('content')
<h4 class="mb-3">Customers</h4>
<table class="table table-striped table-sm">
<thead><tr><th>Name</th><th>Phone</th><th>Email</th><th>Created</th></tr></thead>
<tbody>@foreach($customers as $c)<tr><td>{{ $c->customer_name }}</td><td>{{ $c->customer_phoneno }}</td><td>{{ $c->customer_email }}</td><td>{{ $c->created_at }}</td></tr>@endforeach</tbody>
</table>
@endsection
