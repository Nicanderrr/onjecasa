@extends('layouts.admin')

@section('title', 'Cashier Shifts - NewPOS')
@section('page-eyebrow', 'Operations')
@section('page-title', 'Cashier Shifts')
@section('page-description', 'Monitor active cashier sessions, drawer amounts, and sales during each shift.')

@section('content')
<div class="entity-page">
  <section class="row g-2 dashboard-metrics entity-metrics" aria-label="Shift metrics">
    <div class="col-12 col-md-4">
      <article class="metric-card metric-primary">
        <div class="metric-top"><span class="metric-label">Total Shifts</span><span class="metric-icon"><i class="bi bi-clock-history"></i></span></div>
        <div class="metric-value">{{ $summary['total'] }}</div>
        <div class="metric-meta"><span>Recorded</span><span>sessions</span></div>
      </article>
    </div>
    <div class="col-12 col-md-4">
      <article class="metric-card metric-success">
        <div class="metric-top"><span class="metric-label">Active</span><span class="metric-icon"><i class="bi bi-play-circle"></i></span></div>
        <div class="metric-value">{{ $summary['active'] }}</div>
        <div class="metric-meta"><span>Open</span><span>right now</span></div>
      </article>
    </div>
    <div class="col-12 col-md-4">
      <article class="metric-card metric-warning">
        <div class="metric-top"><span class="metric-label">Closed</span><span class="metric-icon"><i class="bi bi-check2-circle"></i></span></div>
        <div class="metric-value">{{ $summary['closed'] }}</div>
        <div class="metric-meta"><span>Ended</span><span>sessions</span></div>
      </article>
    </div>
  </section>

  <div class="card shadow entity-card">
    <div class="card-header border-0 entity-toolbar">
      <div class="cashier-table-heading"><span><i class="bi bi-stopwatch"></i></span><div><strong>Shift history</strong><small>Cashier sessions and sales activity</small></div></div>
      <form method="GET" action="{{ route('admin.shifts.index') }}" class="d-flex gap-2 flex-wrap">
        <select name="status" class="form-control form-control-sm" style="max-width: 140px;">
          <option value="all" @selected($filters['status'] === 'all')>All shifts</option>
          <option value="active" @selected($filters['status'] === 'active')>Active</option>
          <option value="closed" @selected($filters['status'] === 'closed')>Closed</option>
        </select>
        <select name="cashier_id" class="form-control form-control-sm" style="max-width: 220px;">
          <option value="">All cashiers</option>
          @foreach($cashiers as $cashier)
            <option value="{{ $cashier->id }}" @selected((string) $filters['cashier_id'] === (string) $cashier->id)>{{ $cashier->name }}</option>
          @endforeach
        </select>
        <button class="btn btn-primary btn-sm" type="submit"><i class="bi bi-funnel"></i> Filter</button>
      </form>
    </div>

    <div class="table-responsive">
      <table class="table align-items-center table-flush">
        <thead class="thead-light">
          <tr><th>Cashier</th><th>Status</th><th>Started</th><th>Ended</th><th>Orders</th><th>Sales</th><th>Opening</th><th>Closing</th></tr>
        </thead>
        <tbody>
          @forelse($shifts as $shift)
            <tr>
              <td><strong>{{ $shift->cashier_name }}</strong><small class="d-block text-muted">{{ $shift->cashier_email }}</small></td>
              <td><span class="badge {{ $shift->ended_at ? 'text-bg-secondary' : 'text-bg-success' }}">{{ $shift->ended_at ? 'Closed' : 'Active' }}</span></td>
              <td>{{ \Illuminate\Support\Carbon::parse($shift->started_at)->format('d M Y, h:i A') }}</td>
              <td>{{ $shift->ended_at ? \Illuminate\Support\Carbon::parse($shift->ended_at)->format('d M Y, h:i A') : '-' }}</td>
              <td>{{ $shift->order_count }}</td>
              <td>{{ number_format($shift->sales_total, 2) }}</td>
              <td>{{ number_format((float) $shift->opening_cash, 2) }}</td>
              <td>{{ $shift->closing_cash !== null ? number_format((float) $shift->closing_cash, 2) : '-' }}</td>
            </tr>
          @empty
            <tr><td colspan="8" class="text-center py-5 text-muted">No shifts match the current filters.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($shifts->hasPages())
      <div class="card-footer border-0">{{ $shifts->links() }}</div>
    @endif
  </div>
</div>
@endsection
