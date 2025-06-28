<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();
//Delete Staff
if (isset($_GET['delete'])) {
  $id = $_GET['delete'];
  $adn = "DELETE FROM  rpos_customers  WHERE  customer_id = ?";
  $stmt = $mysqli->prepare($adn);
  $stmt->bind_param('s', $id);
  $stmt->execute();
  $stmt->close();
  if ($stmt) {
    $success = "Deleted" && header("refresh:1; url=customes.php");
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
    <div class="container-fluid mt-4">
      <!-- Table -->
      <div class="card-body">
            <style>
                  
                        .filt {
                            display: grid;
                            grid-template-columns: 80% 20%;
                            gap:10px;
                        }

                    
                </style>
                        <div class="row">
                            <div class="col-md-7">

                                <form action="" method="GET">

                                    <div class="row filt">
                                        <div class="" >
                            <div class="form-group">
                                            
                                        <input type="text" name="search" required value="<?php if(isset($_GET['search'])){echo $_GET['search']; } ?>" class="form-control" placeholder="Search Records ....">
                            </div>
                                        </div>
                                        
                                        <div class="" >
                                            <div class="form-group">
                                        <button type="submit" class="btn btn-primary">Search</button>
                                        </div>
                                        </div>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>
<?php
 if (isset($_GET['search'])) {
?>
                    <div class="row">
        <div class="col">
          <div class="card shadow">
            <div class="card-header border-0">
              <a href="add_customer.php" class="btn btn-outline-success">
                <i class="fas fa-user-plus"></i>
                Add New Customer
              </a>
            </div>
            <div class="table-responsive">
              <table class="table align-items-center table-flush">
                <thead class="thead-light">
                  <tr>
                    <th scope="col">Full Name</th>
                    <th scope="col">Contact Number</th>
                 
                    <th scope="col">Actions</th>
                  </tr>
                </thead>
                <tbody>
                    <?php
                   
                      $search = $_GET['search'];

                      $search = $_GET['search'];
                      $qy = "SELECT * FROM rpos_customers WHERE CONCAT(customer_phoneno,customer_name) LIKE '%$search%' ";
                      $run = mysqli_query($mysqli, $qy);
                      if (mysqli_num_rows($run) > 0) {
                        foreach ($run as $row) {

                          ?>
                        <tr>
                        <td><?php echo $row['customer_name'] ?></td>
                        <td><?php echo $row['customer_phoneno']  ?></td>
                        <td>
                          <a href="customes.php?delete=<?php echo $row['customer_id']?>">
                            <button class="btn btn-sm btn-danger">
                              <i class="fas fa-trash"></i>
                              
                            </button>
                          </a>
  
                          <a href="update_customer.php?update=<?php echo $row['customer_id'] ?>">
                            <button class="btn btn-sm btn-primary">
                              <i class="fas fa-user-edit"></i>
                             
                            </button>
                          </a>
                        </td>
                      </tr>
                      <?php
                      }
                    }else{
                      ?>
                      <td colspan="8" class="text-center py-6 " ><h1><?="NO RECORD FOUND 😢 "; ?></h1></td>
                      <?php
                    }
                    }else{
                    ?>
      <div class="row">
        <div class="col">
          <div class="card shadow">
            <div class="card-header border-0">
              <a href="add_customer.php" class="btn btn-outline-success">
                <i class="fas fa-user-plus"></i>
                Add New Customer
              </a>
            </div>
            <div class="table-responsive">
              <table class="table align-items-center table-flush">
                <thead class="thead-light">
                  <tr>
                    <th scope="col">Full Name</th>
                    <th scope="col">Contact Number</th>
                 
                    <th scope="col">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $ret = "SELECT * FROM  rpos_customers  ORDER BY `rpos_customers`.`created_at` DESC ";
                  $stmt = $mysqli->prepare($ret);
                  $stmt->execute();
                  $res = $stmt->get_result();
                  while ($cust = $res->fetch_object()) {
                  ?>
                    <tr>
                      <td><?php echo $cust->customer_name; ?></td>
                      <td><?php echo $cust->customer_phoneno; ?></td>
                      <td>
                        <a href="customes.php?delete=<?php echo $cust->customer_id; ?>">
                          <button class="btn btn-sm btn-danger">
                            <i class="fas fa-trash"></i>
                            
                          </button>
                        </a>

                        <a href="update_customer.php?update=<?php echo $cust->customer_id; ?>">
                          <button class="btn btn-sm btn-primary">
                            <i class="fas fa-user-edit"></i>
                           
                          </button>
                        </a>
                      </td>
                    </tr>
                  <?php } ?>
                  <?php } ?>
               
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