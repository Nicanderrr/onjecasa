@extends('layouts.admin')
@section('content')
<div class="card shadow">
  <div class="card-header border-0 d-flex justify-content-between align-items-center flex-wrap">
    <h3 class="mb-0">Receipts</h3>
    <input id="receiptsFilter" class="form-control form-control-sm" style="min-width:220px;" placeholder="Filter receipts">
  </div>
  <div class="table-responsive">
    <table class="table align-items-center table-flush" id="receiptsTable">
      <thead class="thead-light"><tr><th>Code</th><th>Customer</th><th>Amount</th><th>Method</th><th>Date</th><th>Action</th></tr></thead>
      <tbody>
      @foreach($receipts as $r)
        <tr>
          <td>{{ $r->code }}</td>
          <td>{{ $r->customer_name }}</td>
          <td>{{ number_format($r->grand_total,2) }}</td>
          <td><span class="status-chip paid">{{ $r->method }}</span></td>
          <td>{{ $r->created_at }}</td>
          <td class="d-flex" style="gap:6px;">
            <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.receipts.show',$r->id) }}">Open</a>
            <a class="btn btn-sm btn-outline-success" href="{{ route('admin.receipts.print',$r->id) }}" target="_blank"><i class="fas fa-print"></i></a>
          </td>
        </tr>
      @endforeach
      </tbody>
    </table>
  </div>
  <div class="p-3">{{ $receipts->links() }}</div>
</div>
<script>
  document.getElementById('receiptsFilter')?.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#receiptsTable tbody tr').forEach((tr) => {
      tr.style.display = tr.innerText.toLowerCase().includes(q) ? '' : 'none';
    });
  });
</script>
@endsection
