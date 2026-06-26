<?php
$adminPageFile = basename($_SERVER['PHP_SELF'], '.php');
$adminPageTitles = [
    'dashboard' => 'Dashboard',
    'products' => 'Products',
    'categories' => 'Categories',
    'add_product' => 'Add Product',
    'update_product' => 'Edit Product',
    'add_catg' => 'Add Category',
    'update_catg' => 'Edit Category',
    'hrm' => 'Employees',
    'add_staff' => 'Add Employee',
    'update_staff' => 'Edit Employee',
    'orders' => 'Orders',
    'orders_reports' => 'Orders Reports',
    'payments' => 'Payments',
    'payments_reports' => 'Payments Reports',
    'receipts' => 'Receipts',
    'change_profile' => 'Profile',
    'settings' => 'Settings',
    'invo' => 'Invoice',
    'newi' => 'Invoice',
    'print_receipt' => 'Receipt',
];
$adminPageTitle = $adminPageTitles[$adminPageFile] ?? ucwords(str_replace('_', ' ', $adminPageFile));
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Start your development with a Dashboard for Bootstrap 4.">
    <meta name="author" content="MartDevelopers Inc">
    <title>POS Admin | <?php echo htmlspecialchars($adminPageTitle); ?></title>
    <!-- Favicon -->
     <link rel="apple-touch-icon" sizes="180x180" href="assets/img/icons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="" href="assets/img/icons/ff.png">
    <link rel="icon" type="image/png" sizes="" href="assets/img/icons/ff.png">
    <link rel="manifest" href="assets/img/icons/site.webmanifest">
    <link rel="mask-icon" href="assets/img/icons/safari-pinned-tab.svg" color="#5bbad5">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap4.min.css">

    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff"> 
    <!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css" />
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
    <!-- Icons -->
    <link href="assets/vendor/nucleo/css/nucleo.css" rel="stylesheet">
    <link href="assets/vendor/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
    <!-- Argon CSS -->
    <link type="text/css" href="assets/css/argon.css?v=1.0.0" rel="stylesheet">
    <link type="text/css" href="assets/css/rtonline-admin.css?v=1.0.33" rel="stylesheet">
    <script src="assets/js/swal.js"></script>
    <!--Load Swal-->
    <?php if (isset($success)) { ?>
        <!--This code for injecting success alert-->
        <script>
            setTimeout(function() {
                    swal("Success", "<?php echo $success; ?>", "success");
                },
                100);
        </script>

    <?php } ?>
    <?php if (isset($err)) { ?>
        <!--This code for injecting error alert-->
        <script>
            setTimeout(function() {
                    swal("Failed", "<?php echo $err; ?>", "error");
                },
                100);
        </script>

    <?php } ?>
    <?php if (isset($info)) { ?>
        <!--This code for injecting info alert-->
        <script>
            setTimeout(function() {
                    swal("Success", "<?php echo $info; ?>", "info");
                },
                100);
        </script>

    <?php } ?>
    <script>
        function getPrice(val) {
            $.ajax({

                type: "POST",
                url: "customer_ajax.php",
                data: 'prodName=' + val,
                success: function(data) {
                    //alert(data);
                    $('#priceI').val(data);
                }
            });

        }
        function getPrices(val) {
            $.ajax({

                type: "POST",
                url: "customer_ajax.php",
                data: 'prodNames=' + val,
                success: function(data) {
                    //alert(data);
                    $('#priceII').val(data);
                }
            });

        }
    </script>
    
</head>

<div id="myModal" class="modal fade admin-support-modal" role="dialog" aria-labelledby="adminSupportModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="admin-support-modal-head">
        <div class="admin-support-icon">
          <i class="fas fa-headset"></i>
        </div>
        <div>
          <p class="admin-form-kicker">Technical Support</p>
          <h4 id="adminSupportModalTitle" class="modal-title">Need help with the POS?</h4>
          <p>Reach support through WhatsApp or email and include the page you were working on.</p>
        </div>
        <button type="button" class="admin-support-close" data-dismiss="modal" aria-label="Close">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div class="modal-body admin-support-modal-body">
        <a class="admin-support-card" href="https://wa.me/+233555835895" target="_blank" rel="noopener">
          <span class="admin-support-card-icon admin-support-card-icon-whatsapp"><i class="fab fa-whatsapp"></i></span>
          <span>
            <strong>WhatsApp Message</strong>
            <small>Fastest option for urgent checkout or payment issues.</small>
          </span>
          <i class="fas fa-arrow-right"></i>
        </a>
        <a class="admin-support-card" href="mailto:nicanderarkomensah@gmail.com">
          <span class="admin-support-card-icon admin-support-card-icon-email"><i class="fas fa-envelope"></i></span>
          <span>
            <strong>Email Support</strong>
            <small>Best for reports, screenshots, and detailed requests.</small>
          </span>
          <i class="fas fa-arrow-right"></i>
        </a>
      </div>
      <div class="modal-footer admin-support-modal-footer">
        <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
