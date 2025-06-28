<?php
ob_start();
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();
//Update Profile
if (isset($_POST['ChangeProfile'])) {
  $admin_id = $_SESSION['admin_id'];
  $admin_name = $_POST['admin_name'];
  $admin_email = $_POST['admin_email'];
  $Qry = "UPDATE rpos_admin SET admin_name =?, admin_email =? WHERE admin_id =?";
  $postStmt = $mysqli->prepare($Qry);
  //bind paramaters
  $rc = $postStmt->bind_param('sss', $admin_name, $admin_email, $admin_id);
  $postStmt->execute();
  //declare a varible which will be passed to alert function
  if ($postStmt) {
    $success = "Account Updated" && header("refresh:1; url=dashboard.php");
  } else {
    $err = "Please Try Again Or Try Later";
  }
}
if (isset($_POST['changePassword'])) {

  //Change Password
  $error = 0;
  if (isset($_POST['old_password']) && !empty($_POST['old_password'])) {
    $old_password =($_POST['old_password']);
  } else {
    $error = 1;
    $err = "Old Password Cannot Be Empty";
  }
  if (isset($_POST['new_password']) && !empty($_POST['new_password'])) {
    $new_password =($_POST['new_password']);
  } else {
    $error = 1;
    $err = "New Password Cannot Be Empty";
  }
  if (isset($_POST['confirm_password']) && !empty($_POST['confirm_password'])) {
    $confirm_password = ($_POST['confirm_password']);
  } else {
    $error = 1;
    $err = "Confirmation Password Cannot Be Empty";
  }

  if (!$error) {
    $admin_id = $_SESSION['admin_id'];
    $sql = "SELECT * FROM rpos_admin   WHERE admin_id = '$admin_id'";
    $res = mysqli_query($mysqli, $sql);
    if (mysqli_num_rows($res) > 0) {
      $row = mysqli_fetch_assoc($res);
      if ($old_password != $row['admin_password']) {
        $err =  "Please Enter Correct Old Password";
      } elseif ($new_password != $confirm_password) {
        $err = "Confirmation Password Does Not Match";
      } else {

        $new_password  = ($_POST['new_password']);
        //Insert Captured information to a database table
        $query = "UPDATE rpos_admin SET  admin_password =? WHERE admin_id =?";
        $stmt = $mysqli->prepare($query);
        //bind paramaters
        $rc = $stmt->bind_param('ss', $new_password, $admin_id);
        $stmt->execute();

        //declare a varible which will be passed to alert function
        if ($stmt) {
          $success = "Password Changed" && header("refresh:1; url=dashboard.php");
        } else {
          $err = "Please Try Again Or Try Later";
        }
      }
    }
  }
}


if (isset($_POST['changePincode'])) {

  //Change Pincode
  $error = 0;
  if (isset($_POST['old_pincode']) && !empty($_POST['old_pincode'])) {
    $old_pincode =  (($_POST['old_pincode']));
  } else {
    $error = 1;
    $err = "Old Password Cannot Be Empty";
  }
  if (isset($_POST['new_pincode']) && !empty($_POST['new_pincode'])) {
    $new_pincode = ($_POST['new_pincode']);
  } else {
    $error = 1;
    $err = "New Pincode Cannot Be Empty";
  }
  if (isset($_POST['confirm_pincode']) && !empty($_POST['confirm_pincode'])) {
    $confirm_pincode = ($_POST['confirm_pincode']);
  } else {
    $error = 1;
    $err = "Confirmation Pincode Cannot Be Empty";
  }

  if (!$error) {
    $admin_id = $_SESSION['admin_id'];
    $sql = "SELECT * FROM rpos_admin   WHERE admin_id = '$admin_id'";
    $res = mysqli_query($mysqli, $sql);
    if (mysqli_num_rows($res) > 0) {
      $row = mysqli_fetch_assoc($res);
      if ($old_pincode != $row['admin_pincode']) {
        $err =  "Please Enter Correct Old Pincode";
      } elseif ($new_pincode != $confirm_pincode) {
        $err = "Confirmation Pincode Does Not Match";
      } else {

        $new_pincode  = ($_POST['new_pincode']);
        //Insert Captured information to a database table
        $query = "UPDATE rpos_admin SET  admin_pincode =? WHERE admin_id =?";
        $stmt = $mysqli->prepare($query);
        //bind paramaters
        $rc = $stmt->bind_param('ss', $new_pincode, $admin_id);
        $stmt->execute();

        //declare a varible which will be passed to alert function
        if ($stmt) {
          $success = "Pincode Changed" && header("refresh:1; url=dashboard.php");
        } else {
          $err = "Please Try Again Or Try Later";
        }
      }
    }
  }
}
if (isset($_POST['info-save'])) {
  //Prevent Posting Blank Values
  if (empty($_POST["company"]) || empty($_POST["address"]) || empty($_POST['city']) || empty($_POST['phone'])) {
    $err = "Blank Values Not Accepted";
  } else {
    $company = $_POST['company'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $phone = $_POST['phone'];
    

    //Insert Captured information to a database table
    $postQuery = "UPDATE company_info SET  company =?, address =?, city =?, phone =?";
    $postStmt = $mysqli->prepare($postQuery);
    //bind 
    $rc = $postStmt->bind_param('ssss', $company, $address, $city, $phone);
    $postStmt->execute();
    //declare a varible which will be passed to alert function
    if ($postStmt) {
      $success = "Details Updated" && header("refresh:1; url=change_profile.php");
    } else {
      $err = "Please Try Again Or Try Later";
    }
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
    $admin_id = $_SESSION['admin_id'];
    //$login_id = $_SESSION['login_id'];
    $ret = "SELECT * FROM  rpos_admin  WHERE admin_id = '$admin_id'";
    $stmt = $mysqli->prepare($ret);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($admin = $res->fetch_object()) {
    ?>
      <!-- Header -->
      <div class="header pb-8 pt-5 pt-lg-8 d-flex align-items-center" style="min-height: 600px; background-image: url(assets/img/theme/restro00.jpg); background-size: cover; background-position: center top;">
        <!-- Mask -->
        <span class="mask bg-gradient-default opacity-8"></span>
        <!-- Header container -->
        <div class="container-fluid d-flex align-items-center">
          <div class="row">
            <div class="col-lg-7 col-md-10">
              <h1 class="display-2 text-white">Hello <?php echo $admin->admin_name; ?></h1>
              <p class="text-white mt-0 mb-5">This is your profile page. You can customize your profile as you want And also change password too</p>
            </div>
          </div>
        </div>
      </div>
      <!-- Page content -->
      <div class="container-fluid mt--8">
        <div class="row">
          <div class="col-xl-4 order-xl-2 mb-5 mb-xl-0">
            <div class="card card-profile shadow">
              <div class="row justify-content-center">
                <div class="col-lg-3 order-lg-2">
                  <div class="card-profile-image">
                    <a href="#">
                      <img src="assets/img/theme/user-a-min.png" class="rounded-circle">
                    </a>
                  </div>
                </div>
              </div>
              <div class="card-header text-center border-0 pt-8 pt-md-4 pb-0 pb-md-4">
                <div class="d-flex justify-content-between">
                </div>
              </div>
              <div class="card-body pt-0 pt-md-4">
                <div class="row">
                  <div class="col">
                    <div class="card-profile-stats d-flex justify-content-center mt-md-5">
                      <div>
                      </div>
                      <div>
                      </div>
                      <div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="text-center">
                  <h3>
                    <?php echo $admin->admin_name; ?></span>
                  </h3>
                  <div class="h5 font-weight-300">
                    <i class="ni location_pin mr-2"></i><?php echo $admin->admin_email; ?>
                  </div>
                </div>
              </div>
            </div>

            <div class=" mt-5 card bg-secondary shadow">
            <div class="card-header bg-white border-0">
                <div class="row align-items-center">
                  <div class="col-8">
                   <strong><h3 class="mb-0">Caution</h3></strong> 
                  </div>
                  <div class="card-body">
                    <?php 
                    $query = "SELECT * FROM company_info ";
                    $run = mysqli_query($mysqli,$query);
                    while($row = mysqli_fetch_assoc($run)){
                      
                    
                    ?>
                    <div style="font-size:14px">  <strong>Company Name:</strong> <span><?php echo $row['company'];?></span> </div>  <br>
                    <div style="font-size:14px" ><strong>Company Address:</strong> <?php echo $row['address'];?> </div> <br>
                    <div style="font-size:14px" ><strong>City:</strong> <?php echo $row['city'];?> </div> <br>
                    <div style="font-size:14px" ><strong>Company Tel:</strong> <?php echo $row['phone'];?> </div>  <br>
                  </div>
                                    
                                    <!-- Button trigger modal -->
                  <button type="button" class="btn btn-primary px-4" data-bs-toggle="modal" data-bs-target="#exampleModal">
                  <i class="fas fa-edit" ></i> Edit 
                  </button>

                  <!-- Modal -->
                  <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Company Details</h1>
                        </div>
                        <div class="modal-body">
                          <form action="" method="post">
                                  <div>
                                      <label for="company">Company Name</label>
                                      <input type="text"  class="form-control mb-2" placeholder="eg; pro" value="<?php echo $row['company'] ?>" name="company">
                                      </div>
                                      <div>
                                      <label for="address">Company Address</label>
                                      <input type="text" class="form-control mb-2"  value="<?php echo $row['address'] ?>" placeholder="eg; Banana Street,East Legon"  name="address">
                                  </div>

                  <div>
                    <label for="city">City</label>
                    <input type="text" class="form-control mb-2"placeholder="eg; Accra"  value="<?php echo $row['city'] ?>"   name="city">
                    </div>
                    
                    <div>
                    <label for="phone">Phone Number</label>
                    <input class="form-control mb-2" placeholder="eg; +233 0490302332" value="<?php echo $row['phone'] ?>"  type="text"name="phone">
                  </div>
                        
                          <?php } ?>
                  <small>Note that User Information changes made on here automically affects details on customer's receipt.</small>
                        </div>
                        <div class="modal-footer">
                          </form>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>


          </div>
          <div class="col-xl-auto order-xl-1" style="margin-left:250px;">
            <div class="card bg-secondary shadow">
              <div class="card-header bg-white border-0">
                <div class="row align-items-center">
                  <div class="col-8">
                    <h3 class="mb-0">My account</h3>
                  </div>
                  <div class="col-4 text-right">
                  </div>
                </div>
              </div>
              <div class="card-body">
                <form method="post">
                  <h6 class="heading-small text-muted mb-4">User information</h6>
                  <div class="pl-lg-4">
                    <div class="row">
                      <div class="col-lg-6">
                        <div class="form-group">
                          <label class="form-control-label" for="input-username">User Name</label>
                          <input type="text" name="admin_name" value="<?php echo $admin->admin_name; ?>" id="input-username" class="form-control form-control-alternative">
                      </div>
                    </div>
                    <div class=" col-lg-6">
                          <div class="form-group">
                            <label class="form-control-label" for="input-email">Email address</label>
                            <input type="email" id="input-email" value="<?php echo $admin->admin_email; ?>" name="admin_email" class="form-control form-control-alternative">
                          </div>
                        </div>

                        <div class="col-lg-12">
                          <div class="form-group">
                            <input type="submit" id="input-email" name="ChangeProfile" class="btn btn-success form-control-alternative" value="Submit">
                      </div>
                    </div>
                  </div>
                </div>
              </form>
              <hr>
              <form method ="post">
                            <h6 class="heading-small text-muted mb-4">Change Password</h6>
                            <div class="pl-lg-4">
                              <div class="row">
                                <div class="col-lg-12">
                                  <div class="form-group">
                                    <label class="form-control-label" for="input-username">Old Password</label>
                                    <input type="password" name="old_password" id="input-username" class="form-control form-control-alternative">
                                  </div>
                                </div>

                                <div class="col-lg-12">
                                  <div class="form-group">
                                    <label class="form-control-label" for="input-email">New Password</label>
                                    <input type="password" name="new_password" class="form-control form-control-alternative">
                                  </div>
                                </div>

                                <div class="col-lg-12">
                                  <div class="form-group">
                                    <label class="form-control-label" for="input-email">Confirm New Password</label>
                                    <input type="password" name="confirm_password" class="form-control form-control-alternative">
                                  </div>
                                </div>

                                <div class="col-lg-12">
                                  <div class="form-group">
                                    <input type="submit" id="input-email" name="changePassword" class="btn btn-success form-control-alternative" value="Change Password">
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                </form>
              <hr>
              <div class="mx-4" >
            
              <form method ="post">
                            <h6 class="heading-small text-muted mb-4">Change Pincode</h6>
                            <div class="pl-lg-4">
                              <div class="row">
                                <div class="col-lg-12">
                                  <div class="form-group">
                                    <label class="form-control-label" for="input-username">Old Pincode</label>
                                    <input type="password" maxlength="4" name="old_pincode" id="input-username" class="form-control form-control-alternative">
                                  </div>
                                </div>

                                <div class="col-lg-12">
                                  <div class="form-group">
                                    <label class="form-control-label" for="input-email">New Pincode</label>
                                    <input type="password" maxlength="4" name="new_pincode" class="form-control form-control-alternative">
                                  </div>
                                </div>

                                <div class="col-lg-12">
                                  <div class="form-group">
                                    <label class="form-control-label" for="input-email">Confirm New Pincode</label>
                                    <input type="password" maxlength="4" name="confirm_pincode" class="form-control form-control-alternative">
                                  </div>
                                </div>

                                <div class="col-lg-12">
                                  <div class="form-group">
                                    <input type="submit" id="input-email" name="changePincode" class="btn btn-success form-control-alternative" value="Change Pincode">
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                </form>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- Footer -->
      <?php
    }
      ?>
      </div>
  </div>
  <!-- Argon Scripts -->
  <?php
  require_once('partials/_sidebar.php');
  require_once('partials/_scripts.php');
  require_once('partials/_footer.php');

  ?>
</body>

</html>