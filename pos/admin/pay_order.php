<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
include('config/code-generator.php');

check_login();

if (isset($_POST['pay'])) {
    // Validate input
    if (empty($_POST["pay_code"]) || empty($_POST["pay_amt"]) || empty($_POST['pay_method'])) {
        $err = "Blank Values Not Accepted";
    } else {
        // Get variables
        $pay_code = $_POST['pay_code'];
        $SID = $_GET['SID'];
        $pay_amt  = $_POST['pay_amt'];
        $pay_method = $_POST['pay_method'];
        $pay_id = $payid; // Use generated ID, not form input

        $order_status = 'Paid'; // You want to mark it as Paid

        // Insert into rpos_payments
        $postQuery = "INSERT INTO rpos_payments (pay_id, pay_code, SID, pay_amt, pay_method) VALUES (?, ?, ?, ?, ?)";
        $postStmt = $mysqli->prepare($postQuery);
        $postStmt->bind_param('sssss', $pay_id, $pay_code, $SID, $pay_amt, $pay_method);

        // Update invoice_products status
        $upQry = "UPDATE invoice_products SET order_status = ? WHERE SID = ?";
        $upStmt = $mysqli->prepare($upQry);
        $upStmt->bind_param('ss', $order_status, $SID);

        // Execute both queries
        if ($postStmt->execute() && $upStmt->execute()) {
            $success = "Payment Successful";
            header("refresh:1; url=receipts.php");
        } else {
            $err = "Please Try Again Or Try Later";
        }
    }
}

// HTML page rendering
require_once('partials/_head.php');
?>

<body>
  <!-- Sidenav -->
  <?php require_once('partials/_sidebar.php'); ?>
  <div class="main-content">
    <?php
    require_once('partials/_topnav.php');

    $SID = $_GET['SID'];
    $total = $_GET['pay']; // Payment amount passed via GET

    // Fetch the order details (optional)
    $ret = "SELECT * FROM invoice_products WHERE SID = ? LIMIT 1";
    $stmt = $mysqli->prepare($ret);
    $stmt->bind_param('s', $SID);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($order = $res->fetch_object()):
    ?>

    <!-- Header -->
    <div style="background-image: url(assets/img/theme/restro00.jpg); background-size: cover;" class="header pb-8 pt-5 pt-md-8">
      <span class="mask bg-gradient-dark opacity-8"></span>
    </div>

    <div class="container-fluid mt--8">
      <div class="row">
        <div class="col">
          <div class="card shadow">
            <div class="card-header border-0">
              <h3>Please Fill All Fields</h3>
            </div>
            <div class="card-body">
              <form method="post">
                <div class="form-row">
                  <div class="col-md-6">
                    <label>Payment ID</label>
                    <input type="text" name="pay_id" readonly value="<?php echo $payid; ?>" class="form-control">
                  </div>
                  <div class="col-md-6">
                    <label>Payment Code</label>
                    <input type="text" name="pay_code" value="<?php echo $mpesaCode; ?>" class="form-control">
                  </div>
                </div>
                <hr>
                <div class="form-row">
                  <div class="col-md-6">
                    <label>Amount ₵ </label>
                    <input type="text" name="pay_amt" readonly value="<?php echo $total; ?>" class="form-control">
                  </div>
                  <div class="col-md-6">
                    <label>Payment Method</label>
                    <select class="form-control" name="pay_method">
                      <option selected>Cash</option>
                      <option>Mobile Money</option>
                      <option>Credit Card</option>
                      <option>Bank Transfer</option>
                    </select>
                  </div>
                </div>
                <br>
                <div class="form-row">
                  <div class="col-md-6">
                    <input type="submit" name="pay" value="Pay Order" class="btn btn-success">
                  </div>
                </div>
              </form>
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

<?php endif; ?>
