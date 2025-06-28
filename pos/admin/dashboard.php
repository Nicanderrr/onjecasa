<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();
require_once('partials/_head.php');
require_once('partials/_analytics.php');
?>

<body>

  <!-- Sidenav -->
  <?php
  require_once('partials/_sidebar.php');
  ?>
  <!-- Main content -->
  <div class="main-content">
    <!-- Top navbar -->
    <?php
    require_once('partials/_topnav.php');
    ?>
    <!-- Header -->
    <div style="background-image: url(assets/img/theme/restro00.jpg); background-size: cover;" class="header  pb-8 pt-5 pt-md-8">
      <span class="mask bg-gradient-dark opacity-8"></span>
      <div class="container-fluid">
        <div class="header-body">
          <!-- Card stats -->
          <div class="row">
    
            <div class="col-xl-4 col-lg-6">
              <div class="card card-stats mb-4 mb-xl-0">
                <div class="card-body">
			<a style="text-decoration: none;"  href="products.php">

                  <div class="row">
                    <div class="col">
                      <h5 class="card-title text-uppercase text-muted mb-0">Products</h5>
                      <span class="h2 font-weight-bold mb-0"><?php echo $products; ?></span>
                    </div>
                    <div class="col-auto">
                      <div class="icon icon-shape bg-primary text-white rounded-circle shadow">
                        <i class="fas fa-utensils"></i>
                      </div>
                    </div>
                  </div>
                  </a>
                </div>
              </div>
            </div>
           
            <div class="col-xl-4 col-lg-6">
              <div class="card card-stats mb-4 mb-xl-0">
                <div class="card-body">
			<a style="text-decoration: none;"  href="orders_reports.php">

                  <div class="row">
                    <div class="col">
                      <h5 class="card-title text-uppercase text-muted mb-0">Orders</h5>
                      <span class="h2 font-weight-bold mb-0"><?php echo $orders; ?></span>
                    </div>
                    <div class="col-auto">
                      <div class="icon icon-shape bg-warning text-white rounded-circle shadow">
                        <i class="fas fa-shopping-cart"></i>
                      </div>
                    </div>
                  </div>
</a>
                </div>
              </div>
            </div>
            <div class="col-xl-4 col-lg-6">
              <div class="card card-stats mb-4 mb-xl-0">
                <div class="card-body">
			<a style="text-decoration: none;"  href="sales.php">

                  <div class="row">
                    <div class="col">
                      <h5 class="card-title text-uppercase text-muted mb-0">Sales</h5>
                      <span class="h2 font-weight-bold mb-0"> ₵ <?php echo number_format($sales); ?></span>
                    </div>
                    <div class="col-auto">
                      <div class="icon icon-shape bg-green text-white rounded-circle shadow">
                        <i class="fas fa-dollar-sign"></i>
                      </div>
                    </div>
                  </div>
      </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Page content -->
    <div class="container-fluid mt--7">
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
              <!-- Projects table -->
              <table class="table align-items-center table-flush">
                <thead class="thead-light">
                  <tr>
                    <th class="text-success" scope="col"><b>Code</b></th>
               
                    <th class="text-success" scope="col"><b>Product</b></th>
                 
                    <th scope="col"><b>Total</b></th>
                    <th scop="col"><b>Status</b></th>
                    <th class="text-success" scope="col"><b>Date</b></th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $ret = "SELECT *, SUM(TOTAL) AS TOT, GROUP_CONCAT(PNAME SEPARATOR ', ') AS PNAMES FROM  invoice_products GROUP BY SID ORDER BY `invoice_products`.`created_at` DESC LIMIT 7 ";
                  $stmt = $mysqli->prepare($ret);
                  $stmt->execute();
                  $res = $stmt->get_result();
                  while ($order = $res->fetch_object()) {
                 

                  ?>
                    <tr>
                      <th class="text-success" scope="row"><?php echo $order->SID; ?></th>
                     
                      <td style="width: 300px; white-space: pre-line;" class="text-success"><?php echo $order->PNAMES; ?></td>
       
                      <td>₵ <?php echo $order->TOT ?></td>
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
              <!-- Projects table -->
              <table class="table align-items-center table-flush">
                <thead class="thead-light">
                  <tr>
                    <th class="text-success" scope="col"><b>Payment Code</b></th>
                    <th scope="col"><b>Payment Method</b></th>
                    <th class='text-success' scope="col"><b>Order Code</b></th>
                    <th scope="col"><b>Amount</b></th>
                    <th class="text-success" scope="col"><b>Date Paid</b></th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $ret = "SELECT * FROM   rpos_payments   ORDER BY `rpos_payments`.`created_at` DESC LIMIT 7 ";
                  $stmt = $mysqli->prepare($ret);
                  $stmt->execute();
                  $res = $stmt->get_result();
                  while ($payment = $res->fetch_object()) {
                  ?>
                    <tr>
                      <th class="text-success" scope="row">
                        <?php echo $payment->pay_code; ?>
                      </th>
                      <td>
                       <?php echo $payment->pay_method; ?>
                      </td>
                      <td class='text-success'>
                        <?php echo $payment->SID; ?>
                      </td>
                      <td>
                      ₵ <?php echo $payment->pay_amt; ?>
                      </td>
                      <td class="text-success">
                     <?php echo date('d/M/Y g:i', strtotime($payment->created_at)) ?>
                      </td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <!-- Footer -->
      <?php require_once('partials/_footer.php'); ?>
    </div>
  </div>
  <!-- Argon Scripts -->
  <?php
  require_once('partials/_scripts.php');
  ?>
</body>
<script type="text/javascript">
  (function(d, t) {
      var v = d.createElement(t), s = d.getElementsByTagName(t)[0];
      v.onload = function() {
        window.voiceflow.chat.load({
          verify: { projectID: '67454a4a8d43f36981214fc6' },
          url: 'https://general-runtime.voiceflow.com',
          versionID: 'production',
          voice: {
            url: "https://runtime-api.voiceflow.com"
          }
        });
      }
      v.src = "https://cdn.voiceflow.com/widget-next/bundle.mjs"; v.type = "text/javascript"; s.parentNode.insertBefore(v, s);
  })(document, 'script');
</script>
</html>