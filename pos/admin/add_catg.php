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

$form = [
  'catg_code' => $_POST['catg_code'] ?? ($alpha . '-' . $beta),
  'catg_name' => $_POST['catg_name'] ?? '',
];

if (isset($_POST['addcatg'])) {
  if (trim((string) $form['catg_name']) === '') {
    $err = "Blank Values Not Accepted";
  } else {
    $dupStmt = $mysqli->prepare("SELECT catg_id FROM rpos_categories WHERE catg_code = ? OR catg_name = ? LIMIT 1");
    $dupStmt->bind_param('ss', $form['catg_code'], $form['catg_name']);
    $dupStmt->execute();
    $dupStmt->store_result();
    if ($dupStmt->num_rows > 0) {
      $err = "A category with this code or name already exists";
    }
    $dupStmt->close();
  }

  if (!isset($err)) {
    $postQuery = "INSERT INTO rpos_categories (catg_name, catg_code) VALUES(?,?)";
    $postStmt = $mysqli->prepare($postQuery);
    $postStmt->bind_param('ss', $form['catg_name'], $form['catg_code']);
    $postStmt->execute();
    if ($postStmt->affected_rows > 0) {
      $success = "Category Added";
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
      <form method="POST" class="product-editor-grid">
        <div class="product-upload-panel card shadow">
          <div class="card-header border-0">
            <h3>Category Code</h3>
            <p>Use a stable code for filters and internal references.</p>
          </div>
          <div class="card-body">
            <div class="product-image-preview category-preview">
              <span class="category-preview-icon"><i class="bi bi-bookmark"></i></span>
            </div>
            <small>Codes help keep lists sorted and searchable.</small>
          </div>
        </div>

        <div class="product-form-panel card shadow">
          <div class="card-header border-0 product-panel-head">
            <div>
              <h3>Add Category</h3>
              <p>Create a new category for inventory grouping.</p>
            </div>
            <button type="submit" name="addcatg" class="btn btn-success">
              <i class="fas fa-save"></i>
              Save Category
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

      <?php require_once('partials/_footer.php'); ?>
    </div>
  </div>

  <?php require_once('partials/_scripts.php'); ?>
</body>

</html>
