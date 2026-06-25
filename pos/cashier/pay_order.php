<?php
ob_start();
session_start();
include('config/config.php');
include('config/checklogin.php');
include('config/code-generator.php');
require_once('../includes/paystack_core.php');

check_login();
paystack_load_env(dirname(__DIR__, 2));
$paystackPublicKey = paystack_env('PAYSTACK_PUBLIC_KEY', '');

if (isset($_POST['pay'])) {
  //Prevent Posting Blank Values

  if (empty($_POST["pay_code"]) || empty($_POST["pay_amt"]) || empty($_POST['pay_method'])) {
    $err = "Blank Values Not Accepted";
  } else {
   
    $pay_code = $_POST['pay_code'];
    $SID = $_GET['SID'];
    $pay_amt  = $_POST['pay_amt'];
    $pay_method = $_POST['pay_method'];
    $pay_id = $_POST['pay_id'];
  
    if (paystack_requires_gateway($pay_method)) {
      $err = "Use Paystack checkout to complete Mobile Money or Bank Transfer payments.";
    } else {

    $order_status = $_GET['order_status'];

    //Insert Captured information to a database table
    $postQuery = "INSERT INTO rpos_payments (pay_id, pay_code, SID, pay_amt, pay_method) VALUES(?,?,?,?,?)";
    $upQry = "UPDATE invoice_products SET order_status =? WHERE SID =?";

    $postStmt = $mysqli->prepare($postQuery);
    $upStmt = $mysqli->prepare($upQry);
    //bind paramaters

    $rc = $postStmt->bind_param('sssss', $pay_id, $pay_code, $SID, $pay_amt, $pay_method);
    $rc = $upStmt->bind_param('ss', $order_status, $SID);
    $postStmt->execute();
    $upStmt->execute();
    //declare a varible which will be passed to alert function
    if ($upStmt && $postStmt) {
$success ="Saved" && header("receipts.php");


    } else {
      $err = "Please Try Again Or Try Later";
    }
    }
  }
}
if(isset($_POST['draft'])){
  $success = "Order Drafted" && header("refresh:1; url=payments.php");
}
require_once('partials/_head.php');
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
    $total = $_GET['pay'];
    $SID = $_GET['SID'];
    $ret = "SELECT * FROM  invoice_products WHERE SID ='$SID' GROUP BY SID";
    $stmt = $mysqli->prepare($ret);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($order = $res->fetch_object()) {
       

    ?>
   
    <!-- Header -->
    <div style="background-image: url(assets/img/theme/restro00.jpg); background-size: cover;" class="header  pb-8 pt-5 pt-md-8">
    <span class="mask bg-gradient-dark opacity-8"></span>
      <div class="container-fluid">
        <div class="header-body">
        </div>
      </div>
    </div>
    <!-- Page content -->
    <div class="container-fluid mt--8">
      <!-- Table -->
      <div class="row">
        <div class="col">
          <div class="card shadow">
            <div class="card-header border-0">
              <h3>Please Fill All Fields</h3>
            </div>
            <div class="card-body">
              <form method="post" enctype="multipart/form-data" id="pos-payment-form" data-paystack-key="<?php echo htmlspecialchars($paystackPublicKey); ?>" data-verify-url="paystack_verify.php" data-sid="<?php echo htmlspecialchars($SID); ?>" data-amount="<?php echo htmlspecialchars($total); ?>">
                <div class="form-row">
                  <div class="col-md-6">
                    <label>Payment ID</label>
                    <input type="text" name="pay_id" id="pay_id" readonly value="<?php echo $payid;?>" class="form-control">
                  </div>
                  <div class="col-md-6">
                    <label>Payment Code</label>
                    <input type="text" name="pay_code" id="pay_code" value="<?php echo $mpesaCode; ?>" class="form-control" value="">
                  </div>
                </div>
                <hr>
                <div class="form-row">
                  <div class="col-md-6">
                    <label>Amount ₵ </label>
  
             <input type="text" name="pay_amt" id="pay_amt" readonly  value="<?php echo $total ?>" class="form-control">
                    
                  </div>
                  <div class="col-md-6">
                    <label>Payment Method</label>
                    <select class="form-control" name="pay_method" id="pay_method">
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
                    <input type="submit" name="pay" value="Pay Order" id="popup-button" class="btn btn-success" value="">
                    <input type="submit" name="draft" value="Draft Order" class="btn btn-info" value="">
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
         

      <!-- Footer -->
      <?php
      require_once('partials/_footer.php');
      ?>
    </div>
  </div>
  <!-- Argon Scripts -->
  <?php
  require_once('partials/_scripts.php'); }
  ?>
  <script src="https://js.paystack.co/v1/inline.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const form = document.getElementById('pos-payment-form');
      if (!form) return;

      const method = document.getElementById('pay_method');
      const payId = document.getElementById('pay_id');
      const payCode = document.getElementById('pay_code');
      const amount = document.getElementById('pay_amt');
      const publicKey = form.dataset.paystackKey;
      const verifyUrl = form.dataset.verifyUrl;
      const sid = form.dataset.sid;

      function needsPaystack(value) {
        return value === 'Mobile Money' || value === 'Bank Transfer';
      }

      form.addEventListener('submit', function (event) {
        if (!needsPaystack(method.value)) return;
        event.preventDefault();

        if (!publicKey) {
          swal('Paystack Not Configured', 'Add PAYSTACK_PUBLIC_KEY and PAYSTACK_SECRET_KEY to your .env file.', 'error');
          return;
        }

        if (typeof PaystackPop === 'undefined') {
          swal('Paystack Unavailable', 'Could not load Paystack checkout. Check your internet connection.', 'error');
          return;
        }

        const reference = 'POS-' + sid + '-' + Date.now();
        const handler = PaystackPop.setup({
          key: publicKey,
          email: sid.toLowerCase().replace(/[^a-z0-9]/g, '') + '@newpos.local',
          amount: Math.round(parseFloat(amount.value.replace(/,/g, '')) * 100),
          currency: 'GHS',
          ref: reference,
          channels: method.value === 'Mobile Money' ? ['mobile_money'] : ['bank_transfer'],
          metadata: {
            custom_fields: [
              { display_name: 'Order Code', variable_name: 'sid', value: sid },
              { display_name: 'Payment Method', variable_name: 'pay_method', value: method.value }
            ]
          },
          callback: function (response) {
            fetch(verifyUrl, {
              method: 'POST',
              credentials: 'same-origin',
              headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
              body: JSON.stringify({
                reference: response.reference,
                sid: sid,
                pay_id: payId.value,
                pay_code: payCode.value,
                pay_amt: amount.value,
                pay_method: method.value
              })
            })
            .then(function (res) { return res.json(); })
            .then(function (data) {
              if (data.success) {
                swal('Payment Verified', data.message || 'Payment successful.', 'success');
                setTimeout(function () { window.location.href = data.redirect || 'receipts.php'; }, 900);
              } else {
                swal('Verification Failed', data.message || 'Unable to verify Paystack payment.', 'error');
              }
            })
            .catch(function () {
              swal('Verification Failed', 'Could not verify the Paystack transaction.', 'error');
            });
          },
          onClose: function () {
            swal('Payment Cancelled', 'Paystack checkout was closed before completion.', 'info');
          }
        });

        handler.openIframe();
      });
    });
  </script>
</body>

</html>
