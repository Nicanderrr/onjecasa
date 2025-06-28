<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();
//Cancel Order
if (isset($_GET['cancel'])) {
$id = $_GET['cancel'];
$adn = "DELETE FROM  invoice_products  WHERE  SID = ?";
$stmt = $mysqli->prepare($adn);
$stmt->bind_param('s', $id);
$stmt->execute();
$stmt->close();
if ($stmt) {
$success = "Deleted" && header("refresh:1; url=payments.php");
} else {
$err = "Try Again Later";
}
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
<a href="invo.php" class="btn btn-outline-success">
<i class="fas fa-plus"></i> <i class="fas fa-utensils"></i>
Make A New Order
</a>
</div>
<div class="table-responsive">
<table class="table align-items-center table-fluid">
<thead class="thead-light">
<tr class="text-center">
<th scope="col">Code</th>

<th scope="col">Product</th>
<th scope="col">Total Price</th>
<th scope="col">Date</th>
<th scope="col">Action</th>
</tr>
</thead>
<tbody>

    <?php

    $qq = "SELECT *,GROUP_CONCAT(PNAME SEPARATOR ', ') AS PNAMES, SUM(TOTAL) AS TOT FROM invoice_products WHERE order_status = '' GROUP BY SID ORDER BY created_at DESC";


    $ress = mysqli_query($mysqli,$qq);
    while ($row = mysqli_fetch_assoc($ress)) {
        
      
        ?>
<tr class="text-center">
    <th class="text-success" scope="row"><?php echo $row['SID']; ?></th>
    
    <td  style="max-width:100px; white-space: pre-line;">
    <?php echo $row['PNAMES'] ?>
</td>

    <td>₵ <?php echo $row['TOT'] ?></td>
<td><?php echo date('d/M/Y g:i', strtotime($row['created_at'])); ?></td>
    <td>
    <a href="pay_order.php?SID=<?php echo $row['SID']?>&pay=<?php echo $row['TOT']?>&order_status=Paid">

            <button class="btn btn-sm btn-success">
                <i class="fas fa-handshake"></i>
                Pay Order
            </button>
        </a>

<a href="payments.php?cancel=<?php echo $row['SID']; ?>">
            <button class="btn btn-sm btn-danger">
                <i class="fas fa-window-close"></i>
                Cancel Order
            </button>
        </a>
    </td>
</tr>
<?php }?>

</tbody>
</table>
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
    require_once('partials/_scripts.php');
    ?>
</body>

</html>