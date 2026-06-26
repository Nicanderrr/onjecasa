<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
include('config/code-generator.php');

check_login();

function category_form_h($value)
{
  return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$update = (int) ($_GET['update'] ?? 0);
if ($update <= 0) {
  header('Location: categories.php');
  exit;
}

$stmt = $mysqli->prepare("SELECT * FROM rpos_categories WHERE catg_id = ? LIMIT 1");
$stmt->bind_param('i', $update);
$stmt->execute();
$res = $stmt->get_result();
$catg = $res->fetch_object();
$stmt->close();

if (!$catg) {
  $err = "Category not found";
  header("refresh:1; url=categories.php");
}

$form = [
  'catg_code' => $_POST['catg_code'] ?? ($catg->catg_code ?? ''),
  'catg_name' => $_POST['catg_name'] ?? ($catg->catg_name ?? ''),
];

if ($catg && isset($_POST['updatecatg'])) {
  if (trim((string) $form['catg_code']) === '' || trim((string) $form['catg_name']) === '') {
    $err = "Blank Values Not Accepted";
  } else {
    $dupStmt = $mysqli->prepare("SELECT catg_id FROM rpos_categories WHERE catg_id <> ? AND (catg_code = ? OR catg_name = ?) LIMIT 1");
    $dupStmt->bind_param('iss', $update, $form['catg_code'], $form['catg_name']);
    $dupStmt->execute();
    $dupStmt->store_result();
    if ($dupStmt->num_rows > 0) {
      $err = "Another category already uses this code or name";
    }
    $dupStmt->close();
  }

  if (!isset($err)) {
    $postQuery = "UPDATE rpos_categories SET catg_code = ?, catg_name = ? WHERE catg_id = ?";
    $postStmt = $mysqli->prepare($postQuery);
    $postStmt->bind_param('ssi', $form['catg_code'], $form['catg_name'], $update);
    $postStmt->execute();
    if ($postStmt->affected_rows >= 0) {
      $success = "Category Updated";
      header("refresh:1; url=categories.php");
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
      <?php if ($catg) { ?>
        <form method="POST" class="product-editor-grid">
          <div class="product-upload-panel card shadow">
            <div class="card-header border-0">
              <h3>Category Info</h3>
              <p>Keep naming consistent with the product catalog.</p>
            </div>
            <div class="card-body">
              <div class="product-image-preview category-preview">
                <span class="category-preview-icon"><i class="bi bi-bookmark-check"></i></span>
              </div>
              <small>Changes will apply anywhere this category appears.</small>
            </div>
          </div>

          <div class="product-form-panel card shadow">
            <div class="card-header border-0 product-panel-head">
              <div>
                <h3>Edit Category</h3>
                <p>Update code and name for the selected group.</p>
              </div>
              <button type="submit" name="updatecatg" class="btn btn-success">
                <i class="fas fa-save"></i>
                Save Changes
              </button>
            </div>
            <div class="card-body">
              <div class="form-row">
                <div class="col-md-6 form-group">
                  <label>Category Code</label>
                  <input type="text" name="catg_code" class="form-control" value="<?php echo category_form_h($form['catg_code']); ?>" required>
                </div>
                <div class="col-md-6 form-group">
                  <label>Category Name</label>
                  <input type="text" name="catg_name" class="form-control" value="<?php echo category_form_h($form['catg_name']); ?>" required>
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
