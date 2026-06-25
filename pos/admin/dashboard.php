<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();
require_once('partials/_head.php');
require_once('partials/_analytics.php');
?>

<body>
  <?php require_once('partials/_sidebar.php'); ?>

  <div class="main-content">
    <?php require_once('partials/_topnav.php'); ?>

    <div class="header pb-8 pt-5 pt-md-8">
      <div class="container-fluid">
        <div class="header-body">
          <div class="row">
            <div class="col-xl-3 col-md-6">
              <a class="dashboard-stat-link" href="products.php">
                <div class="card card-stats metric-primary mb-4 mb-xl-0">
                  <div class="card-body">
                    <div class="row align-items-start">
                      <div class="col">
                        <h5 class="card-title text-uppercase text-muted mb-0">Products</h5>
                        <span class="h2 font-weight-bold mb-0"><span class="metric-value-scroll"><?php echo $products; ?></span></span>
                      </div>
                      <div class="col-auto">
                        <div class="icon icon-shape bg-primary text-white rounded-circle shadow">
                          <i class="fas fa-utensils"></i>
                        </div>
                      </div>
                    </div>
                    <p class="metric-meta mb-0"><span class="metric-meta-scroll"><span class="<?php echo $productsTrend['class']; ?>"><?php echo $productsTrend['percent']; ?></span><span><?php echo $productsTrend['label']; ?></span></span></p>
                  </div>
                </div>
              </a>
            </div>

            <div class="col-xl-3 col-md-6">
              <a class="dashboard-stat-link" href="orders_reports.php">
                <div class="card card-stats metric-warning mb-4 mb-xl-0">
                  <div class="card-body">
                    <div class="row align-items-start">
                      <div class="col">
                        <h5 class="card-title text-uppercase text-muted mb-0">Orders</h5>
                        <span class="h2 font-weight-bold mb-0"><span class="metric-value-scroll"><?php echo $orders; ?></span></span>
                      </div>
                      <div class="col-auto">
                        <div class="icon icon-shape bg-warning text-white rounded-circle shadow">
                          <i class="fas fa-shopping-cart"></i>
                        </div>
                      </div>
                    </div>
                    <p class="metric-meta mb-0"><span class="metric-meta-scroll"><span class="<?php echo $ordersTrend['class']; ?>"><?php echo $ordersTrend['percent']; ?></span><span><?php echo $ordersTrend['label']; ?></span></span></p>
                  </div>
                </div>
              </a>
            </div>

            <div class="col-xl-3 col-md-6">
              <a class="dashboard-stat-link" href="sales.php">
                <div class="card card-stats metric-success mb-4 mb-xl-0">
                  <div class="card-body">
                    <div class="row align-items-start">
                      <div class="col">
                        <h5 class="card-title text-uppercase text-muted mb-0">Sales</h5>
                        <span class="h2 font-weight-bold mb-0">
                          <span class="metric-value-scroll metric-value-marquee">
                            <span class="metric-value-track">
                              <span>₵<?php echo number_format($sales ?? 0); ?></span>
                              <span aria-hidden="true">₵<?php echo number_format($sales ?? 0); ?></span>
                            </span>
                          </span>
                        </span>
                      </div>
                      <div class="col-auto">
                        <div class="icon icon-shape bg-green text-white rounded-circle shadow">
                          <i class="fas fa-dollar-sign"></i>
                        </div>
                      </div>
                    </div>
                    <p class="metric-meta mb-0"><span class="metric-meta-scroll"><span class="<?php echo $salesTrend['class']; ?>"><?php echo $salesTrend['percent']; ?></span><span><?php echo $salesTrend['label']; ?></span></span></p>
                  </div>
                </div>
              </a>
            </div>

            <div class="col-xl-3 col-md-6">
              <a class="dashboard-stat-link" href="customes.php">
                <div class="card card-stats metric-danger mb-4 mb-xl-0">
                  <div class="card-body">
                    <div class="row align-items-start">
                      <div class="col">
                        <h5 class="card-title text-uppercase text-muted mb-0">Customers</h5>
                        <span class="h2 font-weight-bold mb-0"><span class="metric-value-scroll"><?php echo $customers; ?></span></span>
                      </div>
                      <div class="col-auto">
                        <div class="icon icon-shape bg-danger text-white rounded-circle shadow">
                          <i class="fas fa-users"></i>
                        </div>
                      </div>
                    </div>
                    <p class="metric-meta mb-0"><span class="metric-meta-scroll"><span class="<?php echo $customersTrend['class']; ?>"><?php echo $customersTrend['percent']; ?></span><span><?php echo $customersTrend['label']; ?></span></span></p>
                  </div>
                </div>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="container-fluid mt--7">
      <div class="row mt-5">
        <div class="col-xl-8 mb-4 mb-xl-0">
          <div class="card shadow dashboard-panel h-100">
            <div class="card-header border-0">
              <div class="row align-items-center">
                <div class="col">
                  <h3 class="mb-0 section-title"><i class="fas fa-chart-line"></i><span>Sales Performance</span></h3>
                  <p class="text-muted mb-0">Monthly revenue compared with operational targets.</p>
                </div>
                <div class="col text-right">
                  <a href="sales.php" class="btn btn-sm btn-light">View Details</a>
                </div>
              </div>
            </div>
            <div class="card-body">
              <div class="chart-bars" aria-label="Sales performance chart">
                <div class="chart-column bar-42"><span></span><small>Jan</small></div>
                <div class="chart-column bar-58"><span></span><small>Feb</small></div>
                <div class="chart-column bar-51"><span></span><small>Mar</small></div>
                <div class="chart-column bar-72"><span></span><small>Apr</small></div>
                <div class="chart-column bar-66"><span></span><small>May</small></div>
                <div class="chart-column bar-83"><span></span><small>Jun</small></div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-xl-4">
          <div class="card shadow dashboard-panel h-100">
            <div class="card-header border-0">
              <h3 class="mb-0 section-title"><i class="fas fa-wave-square"></i><span>Team Activity</span></h3>
              <p class="text-muted mb-0">Recent operational updates.</p>
            </div>
            <div class="card-body">
              <div class="activity-list">
                <div class="activity-item">
                  <span class="activity-dot bg-primary"></span>
                  <div>
                    <p class="mb-1 font-weight-bold">Inventory reviewed</p>
                    <p class="text-muted small mb-0">Product records were refreshed for the current shift.</p>
                  </div>
                </div>
                <div class="activity-item">
                  <span class="activity-dot bg-success"></span>
                  <div>
                    <p class="mb-1 font-weight-bold">Payment batch cleared</p>
                    <p class="text-muted small mb-0">Recent transactions are ready for reports.</p>
                  </div>
                </div>
                <div class="activity-item">
                  <span class="activity-dot bg-warning"></span>
                  <div>
                    <p class="mb-1 font-weight-bold">Order queue active</p>
                    <p class="text-muted small mb-0">Pending receipts need a quick review.</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row mt-5">
        <div class="col-xl-12 mb-5 mb-xl-0">
          <div class="card shadow">
            <div class="card-header border-0">
              <div class="row align-items-center">
                <div class="col">
                  <h3 class="mb-0">Recent Orders</h3>
                </div>
                <div class="col text-right">
                  <a href="orders_reports.php" class="btn btn-sm btn-primary">See all</a>
                </div>
              </div>
            </div>
            <div class="table-responsive">
              <table class="table align-items-center table-flush">
                <thead class="thead-light">
                  <tr>
                    <th class="text-success" scope="col"><b>Code</b></th>
                    <th class="text-success" scope="col"><b>Product</b></th>
                    <th scope="col"><b>Total</b></th>
                    <th scope="col"><b>Status</b></th>
                    <th class="text-success" scope="col"><b>Date</b></th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $ret = "SELECT *, SUM(TOTAL) AS TOT, GROUP_CONCAT(PNAME SEPARATOR ', ') AS PNAMES FROM invoice_products GROUP BY SID ORDER BY invoice_products.created_at DESC LIMIT 7";
                  $stmt = $mysqli->prepare($ret);
                  $stmt->execute();
                  $res = $stmt->get_result();
                  while ($order = $res->fetch_object()) {
                  ?>
                    <tr>
                      <th class="text-success" scope="row"><?php echo $order->SID; ?></th>
                      <td style="width: 300px; white-space: pre-line;" class="text-success"><?php echo $order->PNAMES; ?></td>
                      <td>GHS <?php echo $order->TOT; ?></td>
                      <td><?php if ($order->order_status == '') {
                            echo "<span class='badge badge-danger'>Not Paid</span>";
                          } else {
                            echo "<span class='badge badge-success'>$order->order_status</span>";
                          } ?></td>
                      <td class="text-success"><?php echo date('d/M/Y g:i', strtotime($order->created_at)); ?></td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <div class="row mt-5">
        <div class="col-xl-12">
          <div class="card shadow">
            <div class="card-header border-0">
              <div class="row align-items-center">
                <div class="col">
                  <h3 class="mb-0">Recent Payments</h3>
                </div>
                <div class="col text-right">
                  <a href="payments_reports.php" class="btn btn-sm btn-primary">See all</a>
                </div>
              </div>
            </div>
            <div class="table-responsive">
              <table class="table align-items-center table-flush">
                <thead class="thead-light">
                  <tr>
                    <th class="text-success" scope="col"><b>Payment Code</b></th>
                    <th scope="col"><b>Payment Method</b></th>
                    <th class="text-success" scope="col"><b>Order Code</b></th>
                    <th scope="col"><b>Amount</b></th>
                    <th class="text-success" scope="col"><b>Date Paid</b></th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $ret = "SELECT * FROM rpos_payments ORDER BY rpos_payments.created_at DESC LIMIT 7";
                  $stmt = $mysqli->prepare($ret);
                  $stmt->execute();
                  $res = $stmt->get_result();
                  while ($payment = $res->fetch_object()) {
                  ?>
                    <tr>
                      <th class="text-success" scope="row"><?php echo $payment->pay_code; ?></th>
                      <td><?php echo $payment->pay_method; ?></td>
                      <td class="text-success"><?php echo $payment->SID; ?></td>
                      <td>GHS <?php echo $payment->pay_amt; ?></td>
                      <td class="text-success"><?php echo date('d/M/Y g:i', strtotime($payment->created_at)); ?></td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <?php require_once('partials/_footer.php'); ?>
    </div>
  </div>

  <?php require_once('partials/_scripts.php'); ?>
</body>

</html>
