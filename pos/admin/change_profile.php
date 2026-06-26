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
if (isset($_POST['changeCommandCenterImage'])) {
  if (empty($_FILES['command_center_image']['name'])) {
    $err = "Please Choose An Image";
  } elseif ($_FILES['command_center_image']['error'] !== UPLOAD_ERR_OK) {
    $err = "Image Upload Failed";
  } else {
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
    $extension = strtolower(pathinfo($_FILES['command_center_image']['name'], PATHINFO_EXTENSION));

    if (!in_array($extension, $allowedExtensions, true)) {
      $err = "Only JPG, PNG, And WEBP Images Are Accepted";
    } else {
      $uploadDir = __DIR__ . '/assets/img/settings';
      if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0775, true);
      }

      $fileName = 'command-center-' . date('YmdHis') . '-' . random_int(100, 999) . '.' . $extension;
      $relativePath = 'assets/img/settings/' . $fileName;
      $targetPath = $uploadDir . '/' . $fileName;

      if (move_uploaded_file($_FILES['command_center_image']['tmp_name'], $targetPath)) {
        $postQuery = "UPDATE company_info SET command_center_image = ?";
        $postStmt = $mysqli->prepare($postQuery);
        $postStmt->bind_param('s', $relativePath);
        $postStmt->execute();
        $success = "Command Center Image Updated" && header("refresh:1; url=change_profile.php");
      } else {
        $err = "Unable To Save Uploaded Image";
      }
    }
  }
}

require_once('partials/_head.php');
?>

<body>
  <?php
  require_once('partials/_sidebar.php');
  ?>
  <div class="main-content">
    <?php
    require_once('partials/_topnav.php');
    $admin_id = $_SESSION['admin_id'];
    $ret = "SELECT * FROM  rpos_admin  WHERE admin_id = '$admin_id'";
    $stmt = $mysqli->prepare($ret);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($admin = $res->fetch_object()) {
      $companyQuery = "SELECT * FROM company_info LIMIT 1";
      $companyRun = mysqli_query($mysqli, $companyQuery);
      $companyInfo = mysqli_fetch_assoc($companyRun) ?: [
        'company' => 'POS System',
        'address' => '',
        'city' => '',
        'phone' => '',
        'command_center_image' => '',
      ];
      $commandCenterImage = $companyInfo['command_center_image'] ?? '';
      $adminInitials = 'AD';
      $adminNameParts = preg_split('/\s+/', trim($admin->admin_name));
      if (!empty($adminNameParts[0])) {
        $adminInitials = strtoupper(substr($adminNameParts[0], 0, 1));
        if (!empty($adminNameParts[1])) {
          $adminInitials .= strtoupper(substr($adminNameParts[1], 0, 1));
        }
      }
    ?>
      <main class="container-fluid admin-settings-page">
        <section class="admin-settings-hero admin-profile-command-hero">
          <div class="admin-profile-hero-copy">
            <p class="admin-profile-kicker">Account Settings</p>
            <h1 class="admin-profile-title">Profile command center</h1>
            <p class="admin-profile-copy">Update administrator access, receipt identity, and the sidebar image from one clean workspace.</p>
            <div class="admin-profile-hero-actions">
              <a class="btn btn-light" href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
              <a class="btn btn-outline-light" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
          </div>
          <div class="admin-settings-identity admin-profile-identity-card">
            <div class="admin-profile-avatar admin-profile-avatar-modern">
              <img src="assets/img/theme/user-a-min.png" alt="<?php echo htmlspecialchars($admin->admin_name); ?>">
              <span class="admin-profile-avatar-initials"><?php echo htmlspecialchars($adminInitials); ?></span>
            </div>
            <div class="admin-profile-identity-copy">
              <span class="admin-profile-role-pill">Administrator</span>
              <h2><?php echo htmlspecialchars($admin->admin_name); ?></h2>
              <p><?php echo htmlspecialchars($admin->admin_email); ?></p>
              <span class="admin-profile-status-pill"><span></span> Signed in</span>
            </div>
          </div>
        </section>

        <section class="admin-settings-strip">
          <div class="admin-settings-stat">
            <span>Company</span>
            <strong><?php echo htmlspecialchars($companyInfo['company']); ?></strong>
          </div>
          <div class="admin-settings-stat">
            <span>City</span>
            <strong><?php echo htmlspecialchars($companyInfo['city']); ?></strong>
          </div>
          <div class="admin-settings-stat">
            <span>Phone</span>
            <strong><?php echo htmlspecialchars($companyInfo['phone']); ?></strong>
          </div>
        </section>

        <div class="admin-settings-grid">
          <section class="admin-panel admin-panel-pad admin-settings-card">
            <div class="admin-settings-card-head">
              <span class="admin-settings-card-icon"><i class="fas fa-user-shield"></i></span>
              <div class="admin-settings-card-title">
                <p class="admin-form-kicker">Administrator</p>
                <h3>My Account</h3>
              </div>
            </div>
            <form method="post">
              <div class="admin-form-grid">
                <div class="admin-form-field">
                  <label for="input-username">User Name</label>
                  <input type="text" name="admin_name" value="<?php echo htmlspecialchars($admin->admin_name); ?>" id="input-username" class="form-control">
                </div>
                <div class="admin-form-field">
                  <label for="input-email">Email Address</label>
                  <input type="email" id="input-email" value="<?php echo htmlspecialchars($admin->admin_email); ?>" name="admin_email" class="form-control">
                </div>
              </div>
              <div class="admin-form-actions">
                <button type="submit" name="ChangeProfile" class="btn btn-primary">Save Account</button>
              </div>
            </form>
          </section>

          <section class="admin-panel admin-panel-pad admin-settings-card">
            <div class="admin-settings-card-head">
              <div class="admin-settings-card-title-row">
                <span class="admin-settings-card-icon"><i class="fas fa-image"></i></span>
                <div class="admin-settings-card-title">
                  <p class="admin-form-kicker">Command Center</p>
                  <h3>Sidebar Image</h3>
                </div>
              </div>
              <div class="admin-command-preview">
                <?php if (!empty($commandCenterImage)) { ?>
                  <img src="<?php echo htmlspecialchars($commandCenterImage); ?>" alt="Command center image">
                <?php } else { ?>
                  <span>POS</span>
                <?php } ?>
              </div>
            </div>
            <form method="post" enctype="multipart/form-data">
              <div class="admin-form-field">
                <label for="command-center-image">Upload Image</label>
                <input type="file" class="form-control" id="command-center-image" name="command_center_image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
              </div>
              <p class="admin-form-note">This image appears in the sidebar command-center card.</p>
              <div class="admin-form-actions">
                <button type="submit" name="changeCommandCenterImage" class="btn btn-primary">Update Image</button>
              </div>
            </form>
          </section>

          <section class="admin-panel admin-panel-pad admin-settings-card">
            <div class="admin-settings-card-head">
              <span class="admin-settings-card-icon"><i class="fas fa-key"></i></span>
              <div class="admin-settings-card-title">
                <p class="admin-form-kicker">Security</p>
                <h3>Change Password</h3>
              </div>
            </div>
            <form method="post">
              <div class="admin-form-grid admin-form-grid-one">
                <div class="admin-form-field">
                  <label for="old-password">Old Password</label>
                  <input type="password" name="old_password" id="old-password" class="form-control">
                </div>
                <div class="admin-form-field">
                  <label for="new-password">New Password</label>
                  <input type="password" name="new_password" id="new-password" class="form-control">
                </div>
                <div class="admin-form-field">
                  <label for="confirm-password">Confirm New Password</label>
                  <input type="password" name="confirm_password" id="confirm-password" class="form-control">
                </div>
              </div>
              <div class="admin-form-actions">
                <button type="submit" name="changePassword" class="btn btn-primary">Change Password</button>
              </div>
            </form>
          </section>

          <section class="admin-panel admin-panel-pad admin-settings-card">
            <div class="admin-settings-card-head">
              <span class="admin-settings-card-icon"><i class="fas fa-lock"></i></span>
              <div class="admin-settings-card-title">
                <p class="admin-form-kicker">PIN Access</p>
                <h3>Change Pincode</h3>
              </div>
            </div>
            <form method="post">
              <div class="admin-form-grid">
                <div class="admin-form-field">
                  <label for="old-pincode">Old Pincode</label>
                  <input type="password" maxlength="4" name="old_pincode" id="old-pincode" class="form-control">
                </div>
                <div class="admin-form-field">
                  <label for="new-pincode">New Pincode</label>
                  <input type="password" maxlength="4" name="new_pincode" id="new-pincode" class="form-control">
                </div>
                <div class="admin-form-field">
                  <label for="confirm-pincode">Confirm New Pincode</label>
                  <input type="password" maxlength="4" name="confirm_pincode" id="confirm-pincode" class="form-control">
                </div>
              </div>
              <div class="admin-form-actions">
                <button type="submit" name="changePincode" class="btn btn-primary">Change Pincode</button>
              </div>
            </form>
          </section>

          <section class="admin-panel admin-panel-pad admin-settings-card admin-settings-card-wide">
            <div class="admin-settings-card-head">
              <span class="admin-settings-card-icon"><i class="fas fa-building"></i></span>
              <div class="admin-settings-card-title">
                <p class="admin-form-kicker">Receipt Identity</p>
                <h3>Edit Company Details</h3>
              </div>
            </div>
            <form method="post">
              <div class="admin-form-grid admin-form-grid-four">
                <div class="admin-form-field">
                  <label for="company">Company Name</label>
                  <input type="text" class="form-control" id="company" value="<?php echo htmlspecialchars($companyInfo['company']); ?>" name="company">
                </div>
                <div class="admin-form-field">
                  <label for="address">Company Address</label>
                  <input type="text" class="form-control" id="address" value="<?php echo htmlspecialchars($companyInfo['address']); ?>" name="address">
                </div>
                <div class="admin-form-field">
                  <label for="city">City</label>
                  <input type="text" class="form-control" id="city" value="<?php echo htmlspecialchars($companyInfo['city']); ?>" name="city">
                </div>
                <div class="admin-form-field">
                  <label for="phone">Phone Number</label>
                  <input class="form-control" id="phone" value="<?php echo htmlspecialchars($companyInfo['phone']); ?>" type="text" name="phone">
                </div>
              </div>
              <p class="admin-form-note">Company information here appears on customer receipts.</p>
              <div class="admin-form-actions">
                <button type="submit" name="info-save" class="btn btn-primary">Save Company Details</button>
              </div>
            </form>
          </section>
        </div>
      </main>
    <?php } ?>
  </div>

  <?php
  require_once('partials/_scripts.php');
  require_once('partials/_footer.php');
  ?>
</body>

</html>
