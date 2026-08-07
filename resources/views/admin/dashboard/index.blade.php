@extends('layouts.admin')
@section('content')
<section class="row g-3 mt-1 dashboard-metrics" aria-label="Dashboard metrics">
  <div class="col-12 col-sm-6 col-xl-3">
    <article class="metric-card metric-primary">
      <div class="metric-top">
        <span class="metric-label">Products</span>
        <span class="metric-icon"><i class="bi bi-box-seam" aria-hidden="true"></i></span>
      </div>
      <div class="metric-value">{{ $stats['product_count'] }}</div>
      <div class="metric-meta"><span class="text-success">Inventory</span><span>items listed</span></div>
    </article>
  </div>

  <div class="col-12 col-sm-6 col-xl-3">
    <article class="metric-card metric-success">
      <div class="metric-top">
        <span class="metric-label">Orders</span>
        <span class="metric-icon"><i class="bi bi-cart-check" aria-hidden="true"></i></span>
      </div>
      <div class="metric-value">{{ $stats['order_count'] }}</div>
      <div class="metric-meta"><span class="text-success">Live</span><span>order count</span></div>
    </article>
  </div>

  <div class="col-12 col-sm-6 col-xl-3">
    <article class="metric-card metric-warning">
      <div class="metric-top">
        <span class="metric-label">Sales</span>
        <span class="metric-icon"><i class="bi bi-currency-dollar" aria-hidden="true"></i></span>
      </div>
      <div class="metric-value">{{ number_format($stats['sales_total'], 2) }}</div>
      <div class="metric-meta"><span class="text-success">Total</span><span>revenue</span></div>
    </article>
  </div>

  <div class="col-12 col-sm-6 col-xl-3">
    <article class="metric-card metric-danger">
      <div class="metric-top">
        <span class="metric-label">AI Assistant</span>
        <span class="metric-icon"><i class="bi bi-robot" aria-hidden="true"></i></span>
      </div>
      <div class="metric-value">Online</div>
      <div class="metric-meta"><span class="text-danger">Ask</span><span>for insights</span></div>
    </article>
  </div>
</section>

<section class="row g-3 mt-1 dashboard-charts" aria-label="Sales charts">
  <div class="col-12 col-xl-8">
    <div class="panel chart-panel h-100">
      <div class="panel-header">
        <div>
          <h2 class="h5 mb-1 section-title"><i class="bi bi-activity" aria-hidden="true"></i><span>Sales Trend</span></h2>
          <p class="text-muted mb-0">Payment totals over the last seven days.</p>
        </div>
      </div>
      <div class="dashboard-chart dashboard-line-chart">
        <canvas id="salesTrendChart" aria-label="Seven-day sales trend graph" role="img"></canvas>
      </div>
    </div>
  </div>

  <div class="col-12 col-xl-4">
    <div class="panel chart-panel h-100">
      <div class="panel-header">
        <div>
          <h2 class="h5 mb-1 section-title"><i class="bi bi-pie-chart" aria-hidden="true"></i><span>Payment Mix</span></h2>
          <p class="text-muted mb-0">Sales value by payment method.</p>
        </div>
      </div>
      <div class="dashboard-chart dashboard-pie-chart">
        @if($chartData['payment_values']->sum() > 0)
          <canvas id="paymentMixChart" aria-label="Payment method pie chart" role="img"></canvas>
        @else
          <p class="chart-empty mb-0">Payment data will appear here after the first sale.</p>
        @endif
      </div>
    </div>
  </div>
</section>

<section class="row g-3 mt-1">
  <div class="col-12 col-xl-8">
    <div class="panel recent-orders-panel">
      <div class="panel-header">
        <div>
          <h2 class="h5 mb-1 section-title"><i class="bi bi-graph-up-arrow" aria-hidden="true"></i><span>Recent Orders</span></h2>
          <p class="text-muted mb-0">Most recent sales activity from the admin workspace.</p>
        </div>
        <a class="btn btn-light btn-sm" href="{{ route('admin.orders.index') }}">View Orders</a>
      </div>
      <div class="table-responsive">
        <table class="table align-middle mb-0">
          <thead>
            <tr><th scope="col">Code</th><th scope="col">Customer</th><th scope="col">Total</th><th scope="col">Status</th><th scope="col">Date</th></tr>
          </thead>
          <tbody>
            @foreach($orders as $o)
              <tr>
                <td>{{ $o->code }}</td>
                <td>{{ $o->customer_name }}</td>
                <td>{{ number_format($o->grand_total, 2) }}</td>
                <td><span class="badge text-bg-success">{{ $o->status }}</span></td>
                <td>{{ $o->created_at }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="col-12 col-xl-4">
    <div class="panel h-100 quick-actions-panel">
      <div class="panel-header">
        <div>
          <h2 class="h5 mb-1 section-title"><i class="bi bi-lightning-charge" aria-hidden="true"></i><span>Quick Actions</span></h2>
          <p class="text-muted mb-0">Fast access to key admin tasks.</p>
        </div>
      </div>
      <div class="d-grid gap-2">
        <a class="btn btn-primary" href="{{ route('admin.orders.create') }}"><i class="bi bi-plus-circle"></i> New Order</a>
        <a class="btn btn-outline-secondary" href="{{ route('admin.products.create') }}"><i class="bi bi-box-seam"></i> Add Product</a>
        <a class="btn btn-outline-secondary" href="{{ route('admin.ai.index') }}"><i class="bi bi-robot"></i> Open AI Assistant</a>
      </div>
    </div>
  </div>
</section>
@endsection

@push('scripts')
<script src="{{ asset('assets/admin/vendor/chart.js/dist/Chart.bundle.min.js') }}"></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    if (typeof Chart === 'undefined') return;

    var chartData = @json($chartData);
    var salesCanvas = document.getElementById('salesTrendChart');
    var paymentCanvas = document.getElementById('paymentMixChart');

    if (salesCanvas) {
      new Chart(salesCanvas.getContext('2d'), {
        type: 'line',
        data: {
          labels: chartData.sales_labels,
          datasets: [{
            label: 'Sales',
            data: chartData.sales_values,
            borderColor: '#2563eb',
            backgroundColor: 'rgba(37, 99, 235, 0.10)',
            borderWidth: 3,
            pointBackgroundColor: '#ffffff',
            pointBorderColor: '#2563eb',
            pointBorderWidth: 2,
            pointRadius: 4,
            pointHoverRadius: 6,
            lineTension: 0.35,
            fill: true
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          legend: { display: false },
          tooltips: {
            callbacks: {
              label: function (item) { return 'Sales: ' + Number(item.yLabel).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }
            }
          },
          scales: {
            xAxes: [{ gridLines: { display: false }, ticks: { fontSize: 11 } }],
            yAxes: [{
              gridLines: { color: 'rgba(148, 163, 184, 0.18)', drawBorder: false },
              ticks: { beginAtZero: true, fontSize: 11, callback: function (value) { return Number(value).toLocaleString(); } }
            }]
          }
        }
      });
    }

    if (paymentCanvas) {
      new Chart(paymentCanvas.getContext('2d'), {
        type: 'doughnut',
        data: {
          labels: chartData.payment_labels,
          datasets: [{
            data: chartData.payment_values,
            backgroundColor: ['#2563eb', '#10b981', '#f59e0b', '#ef4444', '#0ea5e9'],
            borderColor: '#ffffff',
            borderWidth: 3
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          cutoutPercentage: 62,
          legend: {
            display: true,
            position: 'bottom',
            labels: { usePointStyle: true, boxWidth: 8, padding: 14, fontSize: 11 }
          },
          tooltips: {
            callbacks: {
              label: function (item, data) {
                var label = data.labels[item.index] || '';
                var value = data.datasets[0].data[item.index] || 0;
                return label + ': ' + Number(value).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
              }
            }
          }
        }
      });
    }
  });
</script>
@endpush
