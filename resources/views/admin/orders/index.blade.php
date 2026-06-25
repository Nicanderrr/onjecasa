@extends('layouts.admin')
@section('content')
<div class="card shadow">
  <div class="card-header border-0 d-flex justify-content-between align-items-center flex-wrap">
    <h3 class="mb-0">Orders</h3>
    <div class="d-flex" style="gap:8px;">
      <input id="ordersFilter" class="form-control form-control-sm" style="min-width:220px;" placeholder="Filter by code/customer">
      <a href="{{ route('admin.orders.create') }}" class="btn btn-sm btn-success"><i class="fas fa-cart-plus"></i> Make A New Order</a>
    </div>
  </div>
  <div class="table-responsive">
    <table class="table align-items-center table-flush" id="ordersTable">
      <thead class="thead-light"><tr><th>Code</th><th>Customer</th><th>Total</th><th>Status</th><th>Date</th><th>Action</th></tr></thead>
      <tbody>
      @foreach($orders as $o)
        <tr>
          <td>{{ $o->code }}</td>
          <td>{{ $o->customer_name }}</td>
          <td>{{ number_format($o->grand_total,2) }}</td>
          <td><span class="status-chip paid">{{ strtoupper($o->status) }}</span></td>
          <td>{{ $o->created_at }}</td>
          <td><a class="btn btn-sm btn-outline-primary" href="{{ route('admin.orders.show',$o->id) }}">View</a></td>
        </tr>
      @endforeach
      </tbody>
    </table>
  </div>
  <div class="p-3">{{ $orders->links() }}</div>
</div>
<script>
  document.getElementById('ordersFilter')?.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#ordersTable tbody tr').forEach((tr) => {
      tr.style.display = tr.innerText.toLowerCase().includes(q) ? '' : 'none';
    });
  });
</script>
@endsection
