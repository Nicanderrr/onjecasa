<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
include('config/code-generator.php');

check_login();
//Add Customer
if (isset($_POST['addCustomer'])) {
  //Prevent Posting Blank Values
  if (empty($_POST["customer_phoneno"]) || empty($_POST["customer_name"]) ) {
    $err = "Blank Values Not Accepted";
  } else {
    $customer_name = $_POST['customer_name'];
    $customer_phoneno = $_POST['customer_phoneno']; 
    $customer_id = $_POST['customer_id'];

    //Insert Captured information to a database table
    $postQuery = "INSERT INTO rpos_customers (customer_id, customer_name, customer_phoneno) VALUES(?,?,?)";
    $postStmt = $mysqli->prepare($postQuery);
    //bind paramaters
    $rc = $postStmt->bind_param('sss', $customer_id, $customer_name, $customer_phoneno);
    $postStmt->execute();
    //declare a varible which will be passed to alert function
    if ($postStmt) {
      $success = "Customer Added" && header("refresh:1; url=customes.php");
    } else {
      $err = "Please Try Again Or Try Later";
    }
  }
}

require_once('partials/_head.php');
?>
<head>
  <style>
    .switch {
  position: relative;
  display: inline-block;
  width: 48px;
  height: 28px;
}

/* Hide default HTML checkbox */
.switch input {
  opacity: 0;
  width: 0;

  height: 0;
}

/* The slider */
.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 20px;
  width: 20px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}
input:checked + .slider {
  background-color: #2196F3;
}

input:focus + .slider {
  box-shadow: 0 0 1px #2196F3;
}

input:checked + .slider:before {
  -webkit-transform: translateX(18px);
  -ms-transform: translateX(18px);
  transform: translateX(18px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}

.form-checks{
  display: flex;
  justify-content: space-between;

}
.cc{
  display: flex;
  align-items: center;
gap: 2%;
}
.qr{
  width: 80px;
  height: 80px;
}
.modal-body > div{
  display: block;
  padding-bottom:20px ;

}
  </style>
</head>
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
      <div class="row">
        <div class="col">
          <div class="card shadow">
            <div class="card-header border-0">
              <h2>General Settings</h2>
              <div class="cc">
              <h4 class="text-success" >Change Theme Color</h4>
              
              <button class="btn btn-primary" onclick="doit()" id="toggleBtn">Change</button>          
       
                        </label>
                        
</div>
<h4 style="cursor: pointer;" class="text-success" data-toggle="modal" data-target="#myModal">Support</h4>

<!-- Modal -->

<div>
  <select class="form-control col-2" name="lang" id="lang">
    <option value="">English</option>
    <option value="">French</option>
    <option value="">Chinese</option>
    <option value="">Spanish</option>
    <option value="">German</option>
  </select>
</div>
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