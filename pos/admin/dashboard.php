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

    <div class="header pb-8 pt-5 pt-md-8 dashboard-workspace-header">
      <div class="container-fluid">
        <div class="header-body">
          <div class="row dashboard-workspace-row">
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
                    <p class="metric-meta mb-0"><span class="metric-meta-scroll"><span class="metric-meta-track"><span><span class="<?php echo $productsTrend['class']; ?>"><?php echo $productsTrend['percent']; ?></span> <?php echo $productsTrend['label']; ?></span><span aria-hidden="true"><span class="<?php echo $productsTrend['class']; ?>"><?php echo $productsTrend['percent']; ?></span> <?php echo $productsTrend['label']; ?></span></span></span></p>
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
                    <p class="metric-meta mb-0"><span class="metric-meta-scroll"><span class="metric-meta-track"><span><span class="<?php echo $ordersTrend['class']; ?>"><?php echo $ordersTrend['percent']; ?></span> <?php echo $ordersTrend['label']; ?></span><span aria-hidden="true"><span class="<?php echo $ordersTrend['class']; ?>"><?php echo $ordersTrend['percent']; ?></span> <?php echo $ordersTrend['label']; ?></span></span></span></p>
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
                    <p class="metric-meta mb-0"><span class="metric-meta-scroll"><span class="metric-meta-track"><span><span class="<?php echo $salesTrend['class']; ?>"><?php echo $salesTrend['percent']; ?></span> <?php echo $salesTrend['label']; ?></span><span aria-hidden="true"><span class="<?php echo $salesTrend['class']; ?>"><?php echo $salesTrend['percent']; ?></span> <?php echo $salesTrend['label']; ?></span></span></span></p>
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
                    <p class="metric-meta mb-0"><span class="metric-meta-scroll"><span class="metric-meta-track"><span><span class="<?php echo $customersTrend['class']; ?>"><?php echo $customersTrend['percent']; ?></span> <?php echo $customersTrend['label']; ?></span><span aria-hidden="true"><span class="<?php echo $customersTrend['class']; ?>"><?php echo $customersTrend['percent']; ?></span> <?php echo $customersTrend['label']; ?></span></span></span></p>
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
                <?php foreach ($salesPerformance as $salesMonth) { ?>
                  <div class="chart-column <?php echo $salesMonth['amount'] <= 0 ? 'is-empty' : ''; ?>" style="--bar-size: <?php echo (int) $salesMonth['height']; ?>%;" title="<?php echo htmlspecialchars($salesMonth['label']); ?>: &#8373;<?php echo number_format($salesMonth['amount'], 2); ?>">
                    <span></span>
                    <small><?php echo htmlspecialchars($salesMonth['label']); ?></small>
                  </div>
                <?php } ?>
              </div>
            </div>
          </div>
        </div>

        <div class="col-xl-4">
          <div class="card shadow dashboard-panel h-100">
            <div class="card-header border-0">
              <div class="row align-items-center">
                <div class="col">
                  <h3 class="mb-0 section-title"><i class="fas fa-tasks"></i><span>Team Activity</span></h3>
                  <p class="text-muted mb-0">Live operational updates from the system.</p>
                </div>
                <?php if (!empty($teamActivities)) { ?>
                  <div class="col-auto">
                    <button type="button" class="activity-clear-all" id="teamActivityClearAll">Clear All</button>
                  </div>
                <?php } ?>
              </div>
            </div>
            <div class="card-body">
              <div class="activity-list" id="teamActivityList" data-page-size="2" data-dismiss-url="activity_dismiss.php">
                <?php if (!empty($teamActivities)) { ?>
                  <?php foreach ($teamActivities as $activityIndex => $activity) { ?>
                    <div class="activity-item" data-activity-index="<?php echo (int) $activityIndex; ?>" data-activity-id="<?php echo htmlspecialchars($activity['id']); ?>">
                      <span class="activity-icon <?php echo htmlspecialchars($activity['class']); ?>"><i class="<?php echo htmlspecialchars($activity['icon']); ?>"></i></span>
                      <div>
                        <div class="activity-line-head">
                          <p class="mb-1 font-weight-bold"><?php echo htmlspecialchars($activity['title']); ?></p>
                          <button type="button" class="activity-dismiss" aria-label="Clear activity"><i class="fas fa-times"></i></button>
                        </div>
                        <p class="text-muted small mb-0"><?php echo htmlspecialchars($activity['description']); ?></p>
                        <small class="activity-time"><?php echo htmlspecialchars($activity['time']); ?></small>
                      </div>
                    </div>
                  <?php } ?>
                <?php } else { ?>
                  <div class="activity-item">
                    <span class="activity-icon activity-primary"><i class="fas fa-info"></i></span>
                    <div>
                      <p class="mb-1 font-weight-bold">No recent activity</p>
                      <p class="text-muted small mb-0">Payments, orders, and stock alerts will appear here automatically.</p>
                    </div>
                  </div>
                <?php } ?>
              </div>
              <?php if (!empty($teamActivities)) { ?>
                <div class="activity-pagination" id="teamActivityPagination" data-total="<?php echo (int) count($teamActivities); ?>">
                  <button type="button" class="activity-page-btn" data-activity-page="prev" aria-label="Previous activity page"><i class="fas fa-chevron-left"></i></button>
                  <span id="teamActivityPageLabel">1 / <?php echo (int) ceil(count($teamActivities) / 2); ?></span>
                  <button type="button" class="activity-page-btn" data-activity-page="next" aria-label="Next activity page"><i class="fas fa-chevron-right"></i></button>
                </div>
              <?php } ?>
            </div>
          </div>
        </div>
      </div>

      <div class="row mt-5">
        <div class="col-xl-12">
          <div class="card shadow dashboard-panel popular-items-panel">
            <div class="card-header border-0">
              <div class="row align-items-center">
                <div class="col">
                  <h3 class="mb-0 section-title"><i class="fas fa-chart-pie"></i><span>Most Sold Items</span></h3>
                  <p class="text-muted mb-0">Top products ranked by paid order quantity.</p>
                </div>
                <div class="col text-right">
                  <a href="orders_reports.php" class="btn btn-sm btn-light">View Orders</a>
                </div>
              </div>
            </div>
            <div class="card-body">
              <div class="popular-items-wrap">
                <div class="popular-pie-shell">
                  <div class="popular-pie" style="--popular-pie: <?php echo htmlspecialchars($popularPieGradient); ?>;">
                    <div class="popular-pie-center">
                      <strong><?php echo number_format($popularItemsTotal); ?></strong>
                      <span>sold</span>
                    </div>
                  </div>
                </div>

                <div class="popular-legend">
                  <?php if (!empty($popularItems)) { ?>
                    <?php foreach ($popularItems as $popularItem) { ?>
                      <div class="popular-legend-item">
                        <span class="popular-swatch" style="--popular-color: <?php echo htmlspecialchars($popularItem['color']); ?>;"></span>
                        <div class="popular-legend-copy">
                          <strong><?php echo htmlspecialchars($popularItem['name']); ?></strong>
                          <small><?php echo number_format($popularItem['quantity']); ?> sold</small>
                        </div>
                        <span class="popular-percent"><?php echo number_format($popularItem['percent'], 1); ?>%</span>
                      </div>
                    <?php } ?>
                  <?php } else { ?>
                    <div class="popular-empty">
                      <strong>No paid sales yet</strong>
                      <span>Popular items will appear once paid orders are recorded.</span>
                    </div>
                  <?php } ?>
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
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const list = document.getElementById('teamActivityList');
      if (!list) return;

      const pageSize = parseInt(list.dataset.pageSize || '2', 10);
      const dismissUrl = list.dataset.dismissUrl;
      const pagination = document.getElementById('teamActivityPagination');
      const pageLabel = document.getElementById('teamActivityPageLabel');
      const clearAll = document.getElementById('teamActivityClearAll');
      let currentPage = 0;

      function items() {
        return Array.from(list.querySelectorAll('.activity-item:not(.activity-removed)'));
      }

      function persistDismiss(ids) {
        if (!dismissUrl || !ids.length) return;
        fetch(dismissUrl, {
          method: 'POST',
          credentials: 'same-origin',
          headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
          body: JSON.stringify({ ids: ids })
        }).catch(function () {});
      }

      function renderEmptyState() {
        if (list.querySelector('.activity-empty-state')) return;
        const empty = document.createElement('div');
        empty.className = 'activity-item activity-empty-state';
        empty.innerHTML = '<span class="activity-icon activity-primary"><i class="fas fa-info"></i></span><div><p class="mb-1 font-weight-bold">No recent activity</p><p class="text-muted small mb-0">Payments, orders, and stock alerts will appear here automatically.</p></div>';
        list.appendChild(empty);
      }

      function renderPage() {
        const activeItems = items();
        const totalPages = Math.max(1, Math.ceil(activeItems.length / pageSize));
        currentPage = Math.min(currentPage, totalPages - 1);

        activeItems.forEach(function (item, index) {
          const show = index >= currentPage * pageSize && index < (currentPage + 1) * pageSize;
          item.hidden = !show;
        });

        if (!activeItems.length) {
          renderEmptyState();
          if (clearAll) clearAll.hidden = true;
        }

        if (pagination) {
          pagination.hidden = activeItems.length <= pageSize;
          const prev = pagination.querySelector('[data-activity-page="prev"]');
          const next = pagination.querySelector('[data-activity-page="next"]');
          if (prev) prev.disabled = currentPage === 0;
          if (next) next.disabled = currentPage >= totalPages - 1;
        }

        if (pageLabel) {
          pageLabel.textContent = (currentPage + 1) + ' / ' + totalPages;
        }
      }

      list.addEventListener('click', function (event) {
        const button = event.target.closest('.activity-dismiss');
        if (!button) return;

        const item = button.closest('.activity-item');
        if (!item) return;

        item.classList.add('activity-removed');
        item.hidden = true;
        persistDismiss([item.dataset.activityId].filter(Boolean));
        renderPage();
      });

      if (pagination) {
        pagination.addEventListener('click', function (event) {
          const button = event.target.closest('[data-activity-page]');
          if (!button || button.disabled) return;

          currentPage += button.dataset.activityPage === 'next' ? 1 : -1;
          renderPage();
        });
      }

      if (clearAll) {
        clearAll.addEventListener('click', function () {
          const activeItems = items();
          const ids = activeItems.map(function (item) { return item.dataset.activityId; }).filter(Boolean);
          activeItems.forEach(function (item) {
            item.classList.add('activity-removed');
            item.hidden = true;
          });
          persistDismiss(ids);
          currentPage = 0;
          renderPage();
        });
      }

      renderPage();
    });
  </script>
</body>

</html>
