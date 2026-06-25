@extends('layouts.admin')
@section('content')
<div class="card shadow">
  <div class="card-header border-0 d-flex justify-content-between align-items-center flex-wrap">
    <h3 class="mb-0">Payments</h3>
    <input id="paymentsFilter" class="form-control form-control-sm" style="min-width:220px;" placeholder="Filter payment list">
  </div>
  <div class="table-responsive">
    <table class="table align-items-center table-flush" id="paymentsTable">
      <thead class="thead-light"><tr><th>Order</th><th>Customer</th><th>Method</th><th>Amount</th><th>Date</th></tr></thead>
      <tbody>
      @foreach($payments as $p)
        <tr>
          <td>{{ $p->order_code }}</td>
          <td>{{ $p->customer_name }}</td>
          <td><span class="status-chip paid">{{ $p->method }}</span></td>
          <td>{{ number_format($p->amount,2) }}</td>
          <td>{{ $p->created_at }}</td>
        </tr>
      @endforeach
      </tbody>
    </table>
  </div>
  <div class="p-3">{{ $payments->links() }}</div>
</div>
<script>
  document.getElementById('paymentsFilter')?.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#paymentsTable tbody tr').forEach((tr) => {
      tr.style.display = tr.innerText.toLowerCase().includes(q) ? '' : 'none';
    });
  });
</script>
@endsection
