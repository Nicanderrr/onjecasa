@extends('layouts.app')

@section('content')
<h4 class="mb-3">Products</h4>
<table class="table table-striped table-sm">
<thead><tr><th>Code</th><th>Name</th><th>Category</th><th>Price</th><th>Stock</th></tr></thead>
<tbody>@foreach($products as $p)<tr><td>{{ $p->prod_code }}</td><td>{{ $p->prod_name }}</td><td>{{ $p->prod_catg }}</td><td>{{ $p->prod_price }}</td><td>{{ $p->prod_stock }}</td></tr>@endforeach</tbody>
</table>
@endsection
