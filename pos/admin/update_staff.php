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

$update = (int) ($_GET['update'] ?? 0);
if ($update <= 0) {
  header('Location: hrm.php');
  exit;
}

$stmt = $mysqli->prepare("SELECT * FROM rpos_staff WHERE staff_id = ? LIMIT 1");
$stmt->bind_param('i', $update);
$stmt->execute();
$res = $stmt->get_result();
$staff = $res->fetch_object();
$stmt->close();

if (!$staff) {
  $err = "Employee not found";
  header("refresh:1; url=hrm.php");
}

$form = [
  'staff_number' => $_POST['staff_number'] ?? ($staff->staff_number ?? ''),
  'staff_name' => $_POST['staff_name'] ?? ($staff->staff_name ?? ''),
  'staff_email' => $_POST['staff_email'] ?? ($staff->staff_email ?? ''),
  'staff_pincode' => $_POST['staff_pincode'] ?? '',
];

if ($staff && isset($_POST['UpdateStaff'])) {
  if (trim((string) $form['staff_number']) === '' || trim((string) $form['staff_name']) === '' || trim((string) $form['staff_email']) === '') {
    $err = "Blank Values Not Accepted";
  } elseif (!filter_var($form['staff_email'], FILTER_VALIDATE_EMAIL)) {
    $err = "Please enter a valid email address";
  } elseif ($form['staff_pincode'] !== '' && !preg_match('/^\d{4}$/', (string) $form['staff_pincode'])) {
    $err = "Pincode must be exactly 4 digits";
  } else {
    $dupStmt = $mysqli->prepare("SELECT staff_id FROM rpos_staff WHERE staff_id <> ? AND (staff_number = ? OR staff_email = ? OR (staff_pincode <> '' AND staff_pincode = ?)) LIMIT 1");
    $dupStmt->bind_param('isss', $update, $form['staff_number'], $form['staff_email'], $form['staff_pincode']);
    $dupStmt->execute();
    $dupStmt->store_result();
    if ($dupStmt->num_rows > 0) {
      $err = "Staff number, email, or pincode already exists";
    }
    $dupStmt->close();
  }

  if (!isset($err)) {
    if ($form['staff_pincode'] === '') {
      $postQuery = "UPDATE rpos_staff SET staff_number = ?, staff_name = ?, staff_email = ? WHERE staff_id = ?";
      $postStmt = $mysqli->prepare($postQuery);
      $postStmt->bind_param('sssi', $form['staff_number'], $form['staff_name'], $form['staff_email'], $update);
    } else {
      $postQuery = "UPDATE rpos_staff SET staff_number = ?, staff_name = ?, staff_email = ?, staff_pincode = ? WHERE staff_id = ?";
      $postStmt = $mysqli->prepare($postQuery);
      $postStmt->bind_param('ssssi', $form['staff_number'], $form['staff_name'], $form['staff_email'], $form['staff_pincode'], $update);
    }
    $postStmt->execute();
    if ($postStmt->affected_rows >= 0) {
      $success = "Employee Updated";
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
      <?php if ($staff) { ?>
        <form method="POST" class="product-editor-grid">
          <div class="product-upload-panel card shadow">
            <div class="card-header border-0">
              <h3>Employee Profile</h3>
              <p>Update the account details for this staff record.</p>
            </div>
            <div class="card-body">
              <div class="product-image-preview category-preview">
                <span class="category-preview-icon"><i class="fas fa-user-edit"></i></span>
              </div>
              <small>Keep the staff number and email unique.</small>
            </div>
          </div>

          <div class="product-form-panel card shadow">
            <div class="card-header border-0 product-panel-head">
              <div>
                <h3>Edit Employee</h3>
                <p>Adjust login details and contact information.</p>
              </div>
              <button type="submit" name="UpdateStaff" class="btn btn-success">
                <i class="fas fa-save"></i>
                Save Changes
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
                  <input type="password" maxlength="4" name="staff_pincode" class="form-control" value="" placeholder="Leave blank to keep current pin">
                </div>
              </div>
            </div>
          </div>
        </form>
      <?php } ?>

      <?php require_once('partials/_footer.php'); ?>
    </div>
  </div>

  <?php require_once('partials/_scripts.php'); ?>
</body>

</html>
