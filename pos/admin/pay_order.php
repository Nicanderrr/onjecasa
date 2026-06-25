<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
include('config/code-generator.php');
require_once('../includes/paystack_core.php');

check_login();
paystack_load_env(dirname(__DIR__, 2));
$paystackPublicKey = paystack_env('PAYSTACK_PUBLIC_KEY', '');

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

        if (paystack_requires_gateway($pay_method)) {
            $err = "Use Paystack checkout to complete Mobile Money or Bank Transfer payments.";
        } else {
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
              <form method="post" id="pos-payment-form" data-paystack-key="<?php echo htmlspecialchars($paystackPublicKey); ?>" data-verify-url="paystack_verify.php" data-sid="<?php echo htmlspecialchars($SID); ?>" data-amount="<?php echo htmlspecialchars($total); ?>">
                <div class="form-row">
                  <div class="col-md-6">
                    <label>Payment ID</label>
                    <input type="text" name="pay_id" id="pay_id" readonly value="<?php echo $payid; ?>" class="form-control">
                  </div>
                  <div class="col-md-6">
                    <label>Payment Code</label>
                    <input type="text" name="pay_code" id="pay_code" value="<?php echo $mpesaCode; ?>" class="form-control">
                  </div>
                </div>
                <hr>
                <div class="form-row">
                  <div class="col-md-6">
                    <label>Amount ₵ </label>
                    <input type="text" name="pay_amt" id="pay_amt" readonly value="<?php echo $total; ?>" class="form-control">
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

<?php endif; ?>
