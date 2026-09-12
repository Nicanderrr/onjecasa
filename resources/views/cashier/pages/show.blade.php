@extends('layouts.cashier')

@section('content')
@if($page === 'settings')
<section class="row g-3 mt-1">
  <div class="col-12 col-xl-5">
    <div class="panel h-100">
      <div class="panel-header">
        <div>
          <h2 class="h5 mb-1 section-title"><i class="bi bi-person-badge" aria-hidden="true"></i><span>My Profile</span></h2>
          <p class="text-muted mb-0">Update the photo shown across the cashier workspace.</p>
        </div>
      </div>
      <div class="d-grid gap-3">
        <div class="d-flex align-items-center gap-3">
          <img src="{{ auth()->user()->avatarUrl() }}" alt="{{ auth()->user()->name }}" class="rounded-circle border" style="width:72px;height:72px;object-fit:cover;background:#fff;">
          <div>
            <strong class="d-block">{{ auth()->user()->name }}</strong>
            <small class="text-muted">{{ auth()->user()->email }}</small>
          </div>
        </div>
        <form method="POST" action="{{ route('cashier.profile.update') }}" enctype="multipart/form-data" class="d-grid gap-3">
          @csrf
          @method('PUT')
          <div>
            <label class="form-label">Profile Photo</label>
            <input type="file" name="avatar" class="form-control" accept=".jpg,.jpeg,.png,.webp">
            <div class="form-text">Square images look best. Max 2MB.</div>
          </div>
          <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
            <div class="small text-muted">Used in the cashier sidebar and account menu.</div>
            <button class="btn btn-primary btn-sm" type="submit"><i class="bi bi-check2-circle"></i> Save Photo</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div class="col-12 col-xl-7">
    <div class="panel h-100">
      <div class="panel-header">
        <div>
          <h2 class="h5 mb-1 section-title"><i class="bi bi-clock-history" aria-hidden="true"></i><span>Recent Shifts</span></h2>
          <p class="text-muted mb-0">Last clock-in sessions for this cashier account.</p>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table align-middle mb-0">
          <thead><tr><th>Started</th><th>Ended</th><th>Opening</th><th>Closing</th></tr></thead>
          <tbody>
            @forelse($recentShifts as $shift)
              <tr>
                <td>{{ \Illuminate\Support\Carbon::parse($shift->started_at)->format('d M, h:i A') }}</td>
                <td>{{ $shift->ended_at ? \Illuminate\Support\Carbon::parse($shift->ended_at)->format('d M, h:i A') : 'Active' }}</td>
                <td>{{ number_format((float) $shift->opening_cash, 2) }}</td>
                <td>{{ $shift->closing_cash !== null ? number_format((float) $shift->closing_cash, 2) : '-' }}</td>
              </tr>
            @empty
              <tr><td colspan="4" class="text-center text-muted py-4">No shifts recorded yet.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>
@endif

@if($page !== 'settings')
@if($page === 'dashboard')
<section class="row g-3 mt-1" aria-label="Shift and stock alerts">
  <div class="col-12 col-xl-5">
    <div class="panel h-100">
      <div class="panel-header">
        <div>
          <h2 class="h5 mb-1 section-title"><i class="bi bi-stopwatch" aria-hidden="true"></i><span>Shift</span></h2>
          <p class="text-muted mb-0">{{ $activeShift ? 'You are currently clocked in.' : 'Start a shift before the first sale.' }}</p>
        </div>
      </div>
      @if($activeShift)
        <div class="row g-3 mb-3">
          <div class="col-6"><div class="mini-card"><span>Orders</span><strong>{{ $shiftSummary['orders'] }}</strong></div></div>
          <div class="col-6"><div class="mini-card"><span>Sales</span><strong>{{ number_format($shiftSummary['sales'], 2) }}</strong></div></div>
        </div>
        <div class="small text-muted mb-3">Started {{ \Illuminate\Support\Carbon::parse($activeShift->started_at)->format('d M Y, h:i A') }}</div>
        <form method="POST" action="{{ route('cashier.shifts.end') }}" class="row g-2 align-items-end">
          @csrf
          <div class="col-12 col-sm-5">
            <label class="form-label mb-1">Closing Cash</label>
            <input type="number" min="0" step="0.01" name="closing_cash" class="form-control form-control-sm" placeholder="0.00">
          </div>
          <div class="col-12 col-sm-5">
            <label class="form-label mb-1">Notes</label>
            <input type="text" name="notes" class="form-control form-control-sm" placeholder="Optional note">
          </div>
          <div class="col-12 col-sm-2">
            <button class="btn btn-outline-danger btn-sm w-100" type="submit">End</button>
          </div>
        </form>
      @else
        <form method="POST" action="{{ route('cashier.shifts.start') }}" class="row g-2 align-items-end">
          @csrf
          <div class="col-12 col-sm-7">
            <label class="form-label mb-1">Opening Cash</label>
            <input type="number" min="0" step="0.01" name="opening_cash" class="form-control" placeholder="0.00">
          </div>
          <div class="col-12 col-sm-5">
            <button class="btn btn-primary w-100" type="submit"><i class="bi bi-play-circle"></i> Start Shift</button>
          </div>
        </form>
      @endif
    </div>
  </div>

  <div class="col-12 col-xl-7">
    <div class="panel h-100">
      <div class="panel-header">
        <div>
          <h2 class="h5 mb-1 section-title"><i class="bi bi-exclamation-triangle" aria-hidden="true"></i><span>Low Stock Alerts</span></h2>
          <p class="text-muted mb-0">Products at or below their alert threshold.</p>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table align-middle mb-0">
          <thead><tr><th>Product</th><th>Stock</th><th>Alert At</th></tr></thead>
          <tbody>
            @forelse($lowStockProducts as $product)
              <tr>
                <td><strong>{{ $product->name }}</strong><small class="d-block text-muted">{{ $product->code }}</small></td>
                <td><span class="badge text-bg-danger">{{ $product->stock }}</span></td>
                <td>{{ $product->low_stock_threshold }}</td>
              </tr>
            @empty
              <tr><td colspan="3" class="text-center text-muted py-4">No low-stock products right now.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>

<section class="row g-3 mt-1" aria-label="Cashier quick actions">
  <div class="col-12 col-sm-6 col-xl-4">
    <a href="{{ route('cashier.sales.create') }}" class="text-decoration-none">
      <article class="metric-card metric-warning">
        <div class="metric-top">
          <span class="metric-label">Make Orders</span>
          <span class="metric-icon"><i class="bi bi-cart-plus" aria-hidden="true"></i></span>
        </div>
        <div class="metric-value">Start Sale</div>
        <div class="metric-meta"><span class="text-success">Fast</span><span>order entry</span></div>
      </article>
    </a>
  </div>
  <div class="col-12 col-sm-6 col-xl-4">
    <a href="{{ route('cashier.pages.show', 'payments') }}" class="text-decoration-none">
      <article class="metric-card metric-primary">
        <div class="metric-top">
          <span class="metric-label">Complete Orders</span>
          <span class="metric-icon"><i class="bi bi-credit-card" aria-hidden="true"></i></span>
        </div>
        <div class="metric-value">Payments</div>
        <div class="metric-meta"><span class="text-success">Checkout</span><span>ready</span></div>
      </article>
    </a>
  </div>
  <div class="col-12 col-sm-6 col-xl-4">
    <a href="{{ route('cashier.pages.show', 'receipts') }}" class="text-decoration-none">
      <article class="metric-card metric-success">
        <div class="metric-top">
          <span class="metric-label">Receipts</span>
          <span class="metric-icon"><i class="bi bi-printer" aria-hidden="true"></i></span>
        </div>
        <div class="metric-value">Print</div>
        <div class="metric-meta"><span class="text-success">Recent</span><span>transactions</span></div>
      </article>
    </a>
  </div>
</section>

<section class="row g-3 mt-1">
  <div class="col-12 col-xl-8">
    <div class="panel">
      <div class="panel-header">
        <div>
          <h2 class="h5 mb-1 section-title"><i class="bi bi-graph-up-arrow" aria-hidden="true"></i><span>Workspace Summary</span></h2>
          <p class="text-muted mb-0">Daily operational data for the cashier station.</p>
        </div>
      </div>
      <div class="row g-3">
        <div class="col-md-4"><div class="mini-card"><span>Products</span><strong>{{ $stats['product_count'] }}</strong></div></div>
        <div class="col-md-4"><div class="mini-card"><span>Orders</span><strong>{{ $stats['order_count'] }}</strong></div></div>
        <div class="col-md-4"><div class="mini-card"><span>Sales</span><strong>{{ number_format($stats['sales_total'], 2) }}</strong></div></div>
      </div>
    </div>
  </div>

  <div class="col-12 col-xl-4">
    <div class="panel h-100">
      <div class="panel-header">
        <div>
          <h2 class="h5 mb-1 section-title"><i class="bi bi-lightning-charge" aria-hidden="true"></i><span>Quick Actions</span></h2>
          <p class="text-muted mb-0">Common cashier tasks.</p>
        </div>
      </div>
      <div class="d-grid gap-2">
        <a class="btn btn-primary" href="{{ route('cashier.sales.create') }}"><i class="bi bi-cart-plus"></i> Make Orders</a>
        <a class="btn btn-outline-secondary" href="{{ route('cashier.pages.show', 'payments') }}"><i class="bi bi-credit-card"></i> Open Payments</a>
        <a class="btn btn-outline-secondary" href="{{ route('cashier.pages.show', 'receipts') }}"><i class="bi bi-printer"></i> View Receipts</a>
      </div>
    </div>
  </div>
</section>
@endif

<section class="row g-3 mt-1">
  <div class="col-12">
    <div class="panel">
      <div class="panel-header">
        <div>
          <h2 class="h5 mb-1 section-title"><i class="bi bi-table" aria-hidden="true"></i><span class="text-capitalize">{{ str_replace('-', ' ', $page) }}</span></h2>
          <p class="text-muted mb-0">View the current cashier section data below.</p>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table align-middle mb-0">
          @if($page === 'products')
            <thead><tr><th>Code</th><th>Name</th><th>Price</th><th>Stock</th></tr></thead>
            <tbody>@foreach($products as $p)<tr><td>{{ $p->code }}</td><td>{{ $p->name }}</td><td>{{ number_format($p->price, 2) }}</td><td>{{ $p->stock }}</td></tr>@endforeach</tbody>
          @elseif($page === 'receipts')
            <caption class="caption-top p-0">
              <form method="GET" action="{{ route('cashier.pages.show', 'receipts') }}" class="mb-3">
                <div class="row g-2 align-items-end">
                  <div class="col-12 col-lg-4">
                    <label class="form-label mb-1">Search</label>
                    <input type="search" name="search" value="{{ $receiptFilters['search'] ?? '' }}" class="form-control form-control-sm" placeholder="Receipt or customer">
                  </div>
                  <div class="col-12 col-sm-6 col-lg-2">
                    <label class="form-label mb-1">Method</label>
                    <select name="method" class="form-control form-control-sm">
                      <option value="">All methods</option>
                      @foreach($paymentMethods as $method)
                        <option value="{{ $method }}" @selected(($receiptFilters['method'] ?? '') === $method)>{{ $method }}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-12 col-sm-6 col-lg-2">
                    <label class="form-label mb-1">From</label>
                    <input type="date" name="date_from" value="{{ $receiptFilters['date_from'] ?? '' }}" class="form-control form-control-sm">
                  </div>
                  <div class="col-12 col-sm-6 col-lg-2">
                    <label class="form-label mb-1">To</label>
                    <input type="date" name="date_to" value="{{ $receiptFilters['date_to'] ?? '' }}" class="form-control form-control-sm">
                  </div>
                  <div class="col-12 col-sm-6 col-lg-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-fill"><i class="bi bi-funnel"></i> Filter</button>
                    <a href="{{ route('cashier.pages.show', 'receipts') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                  </div>
                </div>
              </form>
            </caption>
            <thead><tr><th>Receipt</th><th>Customer</th><th>Method</th><th>Amount</th><th>Date</th><th class="text-end">Actions</th></tr></thead>
            <tbody>
              @forelse($receipts as $receipt)
                <tr>
                  <td>
                    <a href="{{ route('cashier.receipts.show', $receipt->id) }}">{{ $receipt->code }}</a>
                  </td>
                  <td>{{ $receipt->customer_name }}</td>
                  <td>{{ $receipt->method }}</td>
                  <td>{{ number_format($receipt->amount, 2) }}</td>
                  <td>{{ $receipt->created_at }}</td>
                  <td class="text-end">
                    <a class="btn btn-sm btn-outline-primary" href="{{ route('cashier.receipts.show', $receipt->id) }}">
                      <i class="bi bi-eye"></i> View
                    </a>
                    <a class="btn btn-sm btn-success" href="{{ route('cashier.receipts.print', $receipt->id) }}" target="_blank">
                      <i class="bi bi-printer"></i> Print
                    </a>
                  </td>
                </tr>
              @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No receipts available yet.</td></tr>
              @endforelse
            </tbody>
          @elseif(in_array($page, ['payments', 'payments-reports']))
            <thead><tr><th>#</th><th>Order</th><th>Method</th><th>Amount</th><th>Date</th></tr></thead>
            <tbody>@foreach($payments as $pay)<tr><td>{{ $pay->id }}</td><td>#{{ $pay->order_id }}</td><td>{{ $pay->method }}</td><td>{{ number_format($pay->amount, 2) }}</td><td>{{ $pay->created_at }}</td></tr>@endforeach</tbody>
          @else
            <thead><tr><th>Code</th><th>Customer</th><th>Total</th><th>Status</th><th>Date</th></tr></thead>
            <tbody>@foreach($orders as $order)<tr><td>{{ $order->code }}</td><td>{{ $order->customer_name }}</td><td>{{ number_format($order->grand_total, 2) }}</td><td><span class="badge text-bg-success">{{ $order->status }}</span></td><td>{{ $order->created_at }}</td></tr>@endforeach</tbody>
          @endif
        </table>
      </div>
    </div>
  </div>
</section>
@endif
@endsection
