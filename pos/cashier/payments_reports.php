<?php
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
        <div style="background-image: url(assets/img/theme/restro00.jpg); background-size: cover;"
            class="header  pb-8 pt-5 pt-md-8">
            <span class="mask bg-gradient-dark opacity-8"></span>
            <div class="container-fluid">
                <div class="header-body">
                </div>
            </div>
        </div>
        <!-- Page content -->

        <div class="container-fluid">
            <div class="card-body">
                <style>
                    @media (max-width: 780px) {
                        .filt {
                            display: grid;
                            grid-template-columns: 50% 50%;
                        }

                    }
                </style>
                <form action="" method="GET">
                    <div class="row filt">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>From Date</label>
                                <input type="date" name="from_date" value="<?php if (isset($_GET['from_date'])) {
                                        echo $_GET['from_date'];
                                    } ?>" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>To Date</label>
                                <input type="date" name="to_date" value="<?php if (isset($_GET['to_date'])) {
                                        echo $_GET['to_date'];
                                    } ?>" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Click to Filter</label> <br>
                                <button type="submit" class="btn btn-primary ">Filter</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <!-- Table -->
            <?php
            if (isset($_GET['from_date']) && isset($_GET['to_date'])) {
            ?>
            <div class="row ">
                <div class="col ">
                    <div class="card shadow">
                        <div class="card-header border-0">
                            Payment Reports
                        </div>
                        <div class="table-responsive ">
                            <table class="table align-items-center table-flush md-7">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-success" scope="col">Payment Code</th>
                                        <th scope="col">Payment Method</th>
                                        <th class="text-success" scope="col">Order Code</th>
                                        <th scope="col">Amount Paid</th>
                                        <th class="text-success" scope="col">Date Paid</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                $from_date = $_GET['from_date'];
                $to_date = $_GET['to_date'];
                $qy = "SELECT * FROM rpos_payments WHERE created_at BETWEEN '$from_date' AND '$to_date'";
                $run = mysqli_query($mysqli, $qy);
                if (mysqli_num_rows($run) > 0) {
                    foreach ($run as $row) {

                                    ?>
                                    <tr>
                                        <td><?= $row['pay_code']; ?></td>
                                        <td><?= $row['pay_method']; ?></td>
                                        <td><?= $row['SID']; ?></td>
                                        <td><?= $row['pay_amt']; ?></td>
                                        <td> <?= date('d/M/Y g:i', strtotime($row['created_at'])) ?></td>
                                    </tr>
                                    <?php
                    }
                } else {

                                    ?>
                                  <td colspan="5" class="text-center py-6 " ><h1><?="NO RECORD FOUND 😢 "; ?></h1></td>
                                    <?php
                }



            } else {
                                    ?>
                                    <div class="row">
                                        <div class="col">
                                            <div class="card shadow">
                                                <div class="card-header border-0">
                                                    Payment Reports
                                                </div>
                                                <div class="table-responsive">
                                                    <table class="table align-items-center table-flush">
                                                        <thead class="thead-light">
                                                            <tr>
                                                                <th class="text-success" scope="col">Payment Code</th>
                                                                <th scope="col">Payment Method</th>
                                                                <th class="text-success" scope="col">Order Code</th>
                                                                <th scope="col">Amount Paid</th>
                                                                <th class="text-success" scope="col">Date Paid</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                $ret = "SELECT * FROM  rpos_payments ORDER BY `created_at` DESC ";
                $stmt = $mysqli->prepare($ret);
                $stmt->execute();
                $res = $stmt->get_result();
                while ($payment = $res->fetch_object()) {
                                                            ?>
                                                          

                                                            <tr>
                                                                <th class="text-success" scope="row">
                                                                    <?php echo $payment->pay_code; ?>
                                                                </th>
                                                                <th scope="row">
                                                                    <?php echo $payment->pay_method; ?>
                                                                </th>
                                                                <td class="text-success">
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


                        </div>
                    </div>


                    <?php
            }
                    ?>


                    </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php
    require_once('partials/_footer.php');
    ?>


    <?php
    require_once('partials/_scripts.php');
    ?>
</body>

</html>