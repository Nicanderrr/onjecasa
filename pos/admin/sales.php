<?php
ob_start();
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();

$salesSummary = [];
$totalSales = 0.0;
$totalPayments = 0;
$chartColors = ['#2563eb', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4'];

$query = "
    SELECT
        COALESCE(NULLIF(TRIM(pay_method), ''), 'Unknown') AS payment_method,
        COUNT(*) AS payment_count,
        COALESCE(SUM(CAST(REPLACE(pay_amt, ',', '') AS DECIMAL(15,2))), 0) AS total_sales
    FROM rpos_payments
    GROUP BY COALESCE(NULLIF(TRIM(pay_method), ''), 'Unknown')
    ORDER BY total_sales DESC
";

$stmt = $mysqli->prepare($query);
if ($stmt) {
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $row['total_sales'] = (float) $row['total_sales'];
        $row['payment_count'] = (int) $row['payment_count'];
        $totalSales += $row['total_sales'];
        $totalPayments += $row['payment_count'];
        $salesSummary[] = $row;
    }
    $stmt->close();
} else {
    $err = 'Unable to load sales summary.';
}

$salesChartAnchor = date('Y-m-01');
$anchorStmt = $mysqli->prepare("SELECT MAX(created_at) FROM rpos_payments");
if ($anchorStmt) {
    $anchorStmt->execute();
    $anchorStmt->bind_result($latestPaymentDate);
    $anchorStmt->fetch();
    $anchorStmt->close();

    if (!empty($latestPaymentDate)) {
        $salesChartAnchor = date('Y-m-01', strtotime($latestPaymentDate));
    }
}

$salesChartStart = date('Y-m-01 00:00:00', strtotime($salesChartAnchor . ' -5 months'));
$salesChartEnd = date('Y-m-01 00:00:00', strtotime($salesChartAnchor . ' +1 month'));
$monthlySales = [];

for ($i = 0; $i < 6; $i++) {
    $monthTime = strtotime($salesChartStart . " +{$i} months");
    $monthKey = date('Y-m', $monthTime);
    $monthlySales[$monthKey] = [
        'label' => date('M', $monthTime),
        'amount' => 0.0,
        'height' => 0,
    ];
}

$monthlyStmt = $mysqli->prepare("
    SELECT DATE_FORMAT(created_at, '%Y-%m') AS sales_month,
           COALESCE(SUM(CAST(REPLACE(pay_amt, ',', '') AS DECIMAL(15,2))), 0) AS month_total
    FROM rpos_payments
    WHERE created_at >= ? AND created_at < ?
    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
");

if ($monthlyStmt) {
    $monthlyStmt->bind_param('ss', $salesChartStart, $salesChartEnd);
    $monthlyStmt->execute();
    $monthlyResult = $monthlyStmt->get_result();
    while ($monthlyRow = $monthlyResult->fetch_assoc()) {
        if (isset($monthlySales[$monthlyRow['sales_month']])) {
            $monthlySales[$monthlyRow['sales_month']]['amount'] = (float) $monthlyRow['month_total'];
        }
    }
    $monthlyStmt->close();
}

$salesChartMax = max(array_column($monthlySales, 'amount'));
if ($salesChartMax > 0) {
    foreach ($monthlySales as $monthKey => $monthData) {
        $monthlySales[$monthKey]['height'] = max(12, (int) round(($monthData['amount'] / $salesChartMax) * 100));
    }
}

$pieStops = [];
$pieCursor = 0.0;
foreach ($salesSummary as $index => $row) {
    $color = $chartColors[$index % count($chartColors)];
    $percent = $totalSales > 0 ? ($row['total_sales'] / $totalSales) * 100 : 0;
    $nextCursor = $pieCursor + $percent;

    $salesSummary[$index]['color'] = $color;
    $salesSummary[$index]['percent'] = $percent;

    if ($percent > 0) {
        $pieStops[] = "{$color} {$pieCursor}% {$nextCursor}%";
    }

    $pieCursor = $nextCursor;
}

$salesPieGradient = !empty($pieStops) ? implode(', ', $pieStops) : '#e5e7eb 0% 100%';

require_once('partials/_head.php');
?>

<body>
  <?php require_once('partials/_sidebar.php'); ?>

  <div class="main-content">
    <?php require_once('partials/_topnav.php'); ?>

    <div style="background-image: url(assets/img/theme/restro00.jpg); background-size: cover;" class="header pb-8 pt-5 pt-md-8">
      <span class="mask bg-gradient-dark opacity-8"></span>
      <div class="container-fluid">
        <div class="header-body"></div>
      </div>
    </div>

    <div class="container-fluid mt--8">
      <div class="row">
        <div class="col-xl-6 col-lg-6 mb-4">
          <div class="card card-stats">
            <div class="card-body">
              <div class="row">
                <div class="col">
                  <h5 class="card-title text-uppercase text-muted mb-0">Total Sales</h5>
                  <span class="h2 font-weight-bold mb-0">&#8373;<?php echo number_format($totalSales, 2); ?></span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-xl-6 col-lg-6 mb-4">
          <div class="card card-stats">
            <div class="card-body">
              <div class="row">
                <div class="col">
                  <h5 class="card-title text-uppercase text-muted mb-0">Payments</h5>
                  <span class="h2 font-weight-bold mb-0"><?php echo number_format($totalPayments); ?></span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row mt-2">
        <div class="col-xl-8 mb-4">
          <div class="card shadow dashboard-panel h-100">
            <div class="card-header border-0">
              <h3 class="mb-0 section-title"><i class="fas fa-chart-line"></i><span>Sales Trend</span></h3>
              <p class="text-muted mb-0">Revenue over the latest six recorded months.</p>
            </div>
            <div class="card-body">
              <div class="chart-bars sales-chart-bars" aria-label="Monthly sales chart">
                <?php foreach ($monthlySales as $salesMonth) { ?>
                  <div class="chart-column <?php echo $salesMonth['amount'] <= 0 ? 'is-empty' : ''; ?>" style="--bar-size: <?php echo (int) $salesMonth['height']; ?>%;" title="<?php echo htmlspecialchars($salesMonth['label']); ?>: &#8373;<?php echo number_format($salesMonth['amount'], 2); ?>">
                    <span></span>
                    <small><?php echo htmlspecialchars($salesMonth['label']); ?></small>
                  </div>
                <?php } ?>
              </div>
            </div>
          </div>
        </div>

        <div class="col-xl-4 mb-4">
          <div class="card shadow dashboard-panel h-100">
            <div class="card-header border-0">
              <h3 class="mb-0 section-title"><i class="fas fa-chart-pie"></i><span>Payment Mix</span></h3>
              <p class="text-muted mb-0">Sales share by payment method.</p>
            </div>
            <div class="card-body">
              <div class="popular-items-wrap sales-pie-wrap">
                <div class="popular-pie-shell">
                  <div class="popular-pie" style="--popular-pie: <?php echo htmlspecialchars($salesPieGradient); ?>;">
                    <div class="popular-pie-center">
                      <strong><?php echo number_format($totalPayments); ?></strong>
                      <span>payments</span>
                    </div>
                  </div>
                </div>

                <div class="popular-legend">
                  <?php if (!empty($salesSummary)) { ?>
                    <?php foreach ($salesSummary as $row) { ?>
                      <div class="popular-legend-item">
                        <span class="popular-swatch" style="--popular-color: <?php echo htmlspecialchars($row['color'] ?? '#2563eb'); ?>;"></span>
                        <div class="popular-legend-copy">
                          <strong><?php echo htmlspecialchars($row['payment_method']); ?></strong>
                          <small>&#8373;<?php echo number_format($row['total_sales'], 2); ?></small>
                        </div>
                        <span class="popular-percent"><?php echo number_format($row['percent'] ?? 0, 1); ?>%</span>
                      </div>
                    <?php } ?>
                  <?php } else { ?>
                    <div class="popular-empty">
                      <strong>No sales yet</strong>
                      <span>Payment distribution will appear after sales are recorded.</span>
                    </div>
                  <?php } ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="card shadow">
        <div class="card-header border-0">
          <h3 class="mb-0">Sales Summary By Payment Method</h3>
        </div>

        <div class="table-responsive">
          <table class="table align-items-center table-flush">
            <thead class="thead-light">
              <tr>
                <th scope="col">Payment Method</th>
                <th scope="col">Payments</th>
                <th scope="col">Total Sales</th>
              </tr>
            </thead>
            <tbody>
              <?php if (count($salesSummary) > 0) { ?>
                <?php foreach ($salesSummary as $row) { ?>
                  <tr>
                    <th scope="row"><?php echo htmlspecialchars($row['payment_method']); ?></th>
                    <td><?php echo number_format($row['payment_count']); ?></td>
                    <td>&#8373;<?php echo number_format($row['total_sales'], 2); ?></td>
                  </tr>
                <?php } ?>
              <?php } else { ?>
                <tr>
                  <td colspan="3" class="text-center text-muted py-4">No sales data available.</td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>

      <?php require_once('partials/_footer.php'); ?>
    </div>
  </div>

  <?php require_once('partials/_scripts.php'); ?>
</body>
</html>
