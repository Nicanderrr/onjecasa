<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
include('config/code-generator.php');

check_login();

function staff_form_h($value)
{
  return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$form = [
  'staff_number' => $_POST['staff_number'] ?? ($alpha . '-' . $beta),
  'staff_name' => $_POST['staff_name'] ?? '',
  'staff_email' => $_POST['staff_email'] ?? '',
  'staff_pincode' => $_POST['staff_pincode'] ?? '',
];

if (isset($_POST['addStaff'])) {
  if (trim((string) $form['staff_number']) === '' || trim((string) $form['staff_name']) === '' || trim((string) $form['staff_email']) === '' || trim((string) $form['staff_pincode']) === '') {
    $err = "Blank Values Not Accepted";
  } elseif (!filter_var($form['staff_email'], FILTER_VALIDATE_EMAIL)) {
    $err = "Please enter a valid email address";
  } elseif (!preg_match('/^\d{4}$/', (string) $form['staff_pincode'])) {
    $err = "Pincode must be exactly 4 digits";
  } else {
    $dupStmt = $mysqli->prepare("SELECT staff_id FROM rpos_staff WHERE staff_number = ? OR staff_email = ? OR staff_pincode = ? LIMIT 1");
    $dupStmt->bind_param('sss', $form['staff_number'], $form['staff_email'], $form['staff_pincode']);
    $dupStmt->execute();
    $dupStmt->store_result();
    if ($dupStmt->num_rows > 0) {
      $err = "Staff number, email, or pincode already exists";
    }
    $dupStmt->close();
  }

  if (!isset($err)) {
    $postQuery = "INSERT INTO rpos_staff (staff_number, staff_name, staff_email, staff_pincode) VALUES(?,?,?,?)";
    $postStmt = $mysqli->prepare($postQuery);
    $postStmt->bind_param('ssss', $form['staff_number'], $form['staff_name'], $form['staff_email'], $form['staff_pincode']);
    $postStmt->execute();
    if ($postStmt->affected_rows > 0) {
      $success = "Employee Added";
      header("refresh:1; url=hrm.php");
    } else {
      $err = "Please Try Again Or Try Later";
    }
    $postStmt->close();
  }
}

require_once('partials/_head.php');
?>

<body>
  <?php require_once('partials/_sidebar.php'); ?>
  <div class="main-content">
    <?php require_once('partials/_topnav.php'); ?>

    <div class="container-fluid mt-4 category-workspace">
      <form method="POST" class="product-editor-grid">
        <div class="product-upload-panel card shadow">
          <div class="card-header border-0">
            <h3>Employee Access</h3>
            <p>Keep the staff number, email, and pincode organized.</p>
          </div>
          <div class="card-body">
            <div class="product-image-preview category-preview">
              <span class="category-preview-icon"><i class="fas fa-user-shield"></i></span>
            </div>
            <small>Use a unique pincode for secure login access.</small>
          </div>
        </div>

        <div class="product-form-panel card shadow">
          <div class="card-header border-0 product-panel-head">
            <div>
              <h3>Add Employee</h3>
              <p>Create a new cashier or staff account.</p>
            </div>
            <button type="submit" name="addStaff" class="btn btn-success">
              <i class="fas fa-save"></i>
              Save Employee
            </button>
          </div>
          <div class="card-body">
            <div class="form-row">
              <div class="col-md-6 form-group">
                <label>Staff Number</label>
                <input type="text" name="staff_number" class="form-control" value="<?php echo staff_form_h($form['staff_number']); ?>" required>
              </div>
              <div class="col-md-6 form-group">
                <label>Staff Name</label>
                <input type="text" name="staff_name" class="form-control" value="<?php echo staff_form_h($form['staff_name']); ?>" required>
              </div>
            </div>
            <div class="form-row">
              <div class="col-md-6 form-group">
                <label>Staff Email</label>
                <input type="email" name="staff_email" class="form-control" value="<?php echo staff_form_h($form['staff_email']); ?>" required>
              </div>
              <div class="col-md-6 form-group">
                <label>Staff Pincode</label>
                <input type="password" maxlength="4" name="staff_pincode" class="form-control" value="<?php echo staff_form_h($form['staff_pincode']); ?>" required>
              </div>
            </div>
          </div>
        </div>
      </form>

      <?php require_once('partials/_footer.php'); ?>
    </div>
  </div>

  <?php require_once('partials/_scripts.php'); ?>
</body>

</html>
