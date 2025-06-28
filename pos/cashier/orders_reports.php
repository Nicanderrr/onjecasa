<?php
ob_start();
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();
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
            <div class="card-body">
            <style>
                  
                        .filt {
                            display: grid;
                            grid-template-columns: 80% 20%;
                            gap:10px;
                        }

                    
                </style>
         


                <div class="col">
                    <div class="card shadow">
                        <div class="card-header border-0">
                            Orders Records
                        </div>

                        <div class="table-responsive p-3">
                            <table id="datatable" class="table align-items-center table-flush">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-success" scope="col">Code</th>
                                      
                                        <th class="text-success" scope="col">Product</th>
                                     
                                       
                                        <th scope="col">Total Price</th>
                                        <th scop="col">Status</th>
                                        <th scope="col">Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $ret = "SELECT *, SUM(TOTAL) AS TOT,GROUP_CONCAT(PNAME SEPARATOR ', ') AS PNAMES FROM  invoice_products GROUP BY SID ORDER BY `created_at` DESC  ";
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
                                            <td><?php echo date('d/M/Y g:i', strtotime($order->created_at)); ?></td>
                                        </tr>

                                    <?php } ?>
                                   
                                </tbody>
                            </table>
                   </div>
                    </div>
                    <?php
                   require_once('partials/_footer.php');
                       ?>
           
                </div>
            </div>
        </div>
    </div>
   
  
           
            </div>
    <?php
    require_once('partials/_scripts.php');
    ?>
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap4.min.js"></script>
<script>

$(document).ready(function () {
    $('#datatable').DataTable();
});
</script>
</body>

    
</html>