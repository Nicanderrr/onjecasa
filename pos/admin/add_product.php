<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
include('config/code-generator.php');

check_login();

function product_form_h($value)
{
  return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function product_upload_image($field, &$err)
{
  if (!isset($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
    return '';
  }

  if ($_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
    $err = "Product image could not be uploaded";
    return false;
  }

  if ($_FILES[$field]['size'] > 3145728) {
    $err = "Product image must be 3MB or smaller";
    return false;
  }

  $allowed = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/webp' => 'webp',
    'image/avif' => 'avif',
  ];
  $mime = mime_content_type($_FILES[$field]['tmp_name']);
  if (!isset($allowed[$mime])) {
    $err = "Only JPG, PNG, WEBP, or AVIF product images are allowed";
    return false;
  }

  $name = 'product-' . bin2hex(random_bytes(8)) . '.' . $allowed[$mime];
  $target = __DIR__ . '/assets/img/products/' . $name;
  if (!move_uploaded_file($_FILES[$field]['tmp_name'], $target)) {
    $err = "Product image could not be saved";
    return false;
  }

  return $name;
}

$categories = [];
$categoryResult = $mysqli->query("SELECT catg_name FROM rpos_categories ORDER BY catg_name ASC");
while ($categoryResult && $cat = $categoryResult->fetch_object()) {
  if (trim((string) $cat->catg_name) !== '') {
    $categories[] = $cat->catg_name;
  }
}

$form = [
  'prod_name' => $_POST['prod_name'] ?? '',
  'prod_code' => $_POST['prod_code'] ?? ($alpha . '-' . $beta),
  'prod_price' => $_POST['prod_price'] ?? '',
  'prod_cost' => $_POST['prod_cost'] ?? '',
  'prod_catg' => $_POST['prod_catg'] ?? '',
  'prod_barcode' => $_POST['prod_barcode'] ?? '',
  'prod_stock' => $_POST['prod_stock'] ?? '',
  'prod_desc' => $_POST['prod_desc'] ?? '',
];

if (isset($_POST['addProduct'])) {
  $required = ['prod_code', 'prod_name', 'prod_desc', 'prod_price', 'prod_cost', 'prod_stock'];
  foreach ($required as $field) {
    if (trim((string) $form[$field]) === '') {
      $err = "Please fill all required product fields";
      break;
    }
  }

  if (!isset($err) && (!is_numeric($form['prod_price']) || !is_numeric($form['prod_cost']) || !is_numeric($form['prod_stock']))) {
    $err = "Price, cost, and stock must be valid numbers";
  }

  if (!isset($err) && ((float) $form['prod_price'] < 0 || (float) $form['prod_cost'] < 0 || (int) $form['prod_stock'] < 0)) {
    $err = "Price, cost, and stock cannot be negative";
  }

  if (!isset($err)) {
    $duplicateQuery = "SELECT prod_id FROM rpos_products WHERE prod_code = ? OR (prod_barcode <> '' AND prod_barcode = ?) LIMIT 1";
    $duplicateStmt = $mysqli->prepare($duplicateQuery);
    $duplicateStmt->bind_param('ss', $form['prod_code'], $form['prod_barcode']);
    $duplicateStmt->execute();
    $duplicateStmt->store_result();
    if ($duplicateStmt->num_rows > 0) {
      $err = "A product with this SKU or barcode already exists";
    }
    $duplicateStmt->close();
  }

  if (!isset($err)) {
    $prod_img = product_upload_image('prod_img', $err);
    if ($prod_img !== false) {
      $prod_id = $_POST['prod_id'];
      $postQuery = "INSERT INTO rpos_products (prod_id, prod_code, prod_name, prod_img, prod_desc, prod_price, prod_cost, prod_catg, prod_stock, prod_barcode) VALUES(?,?,?,?,?,?,?,?,?,?)";
      $postStmt = $mysqli->prepare($postQuery);
      $postStmt->bind_param(
        'ssssssssss',
        $prod_id,
        $form['prod_code'],
        $form['prod_name'],
        $prod_img,
        $form['prod_desc'],
        $form['prod_price'],
        $form['prod_cost'],
        $form['prod_catg'],
        $form['prod_stock'],
        $form['prod_barcode']
      );
      $postStmt->execute();
      if ($postStmt->affected_rows > 0) {
        $success = "Product Added";
        header("refresh:1; url=products.php");
      } else {
        $err = "Please Try Again Or Try Later";
      }
      $postStmt->close();
    }
  }
}

require_once('partials/_head.php');
?>

<body>
  <?php require_once('partials/_sidebar.php'); ?>
  <div class="main-content">
    <?php require_once('partials/_topnav.php'); ?>

    <div class="header product-page-header pb-4 pt-3 pt-md-4">
      <div class="container-fluid">
        <div class="header-body product-header-body">
          <div>
            <p class="product-eyebrow">Catalog Setup</p>
            <h1>Add Product</h1>
            <span>Create a new sellable item with pricing, stock, and barcode details.</span>
          </div>
          <a href="products.php" class="btn btn-light product-header-action">
            <i class="fas fa-arrow-left"></i>
            Products
          </a>
        </div>
      </div>
    </div>

    <div class="container-fluid mt-4 product-workspace">
      <form method="POST" enctype="multipart/form-data" class="product-editor-grid">
        <input type="hidden" name="prod_id" value="<?php echo product_form_h($prod_id); ?>">

        <div class="product-upload-panel card shadow">
          <div class="card-header border-0">
            <h3>Product Image</h3>
            <p>Upload a clean product photo for tables and orders.</p>
          </div>
          <div class="card-body">
            <div class="product-image-preview">
              <img id="productImagePreview" src="assets/img/products/place.png" alt="Product preview">
            </div>
            <label class="product-upload-control">
              <i class="fas fa-cloud-upload-alt"></i>
              <span>Choose image</span>
              <input type="file" name="prod_img" id="prodImageInput" accept="image/jpeg,image/png,image/webp,image/avif">
            </label>
            <small>JPG, PNG, WEBP, or AVIF. Max 3MB.</small>
          </div>
        </div>

        <div class="product-form-panel card shadow">
          <div class="card-header border-0 product-panel-head">
            <div>
              <h3>Product Details</h3>
              <p>Required fields are marked with an asterisk.</p>
            </div>
            <button type="submit" name="addProduct" class="btn btn-success">
              <i class="fas fa-save"></i>
              Save Product
            </button>
          </div>
          <div class="card-body">
            <div class="form-row">
              <div class="col-md-6 form-group">
                <label>Product Name *</label>
                <input type="text" name="prod_name" class="form-control" value="<?php echo product_form_h($form['prod_name']); ?>" required>
              </div>
              <div class="col-md-6 form-group">
                <label>SKU *</label>
                <input type="text" name="prod_code" class="form-control" value="<?php echo product_form_h($form['prod_code']); ?>" required>
              </div>
            </div>
            <div class="form-row">
              <div class="col-md-4 form-group">
                <label>Price *</label>
                <input type="number" name="prod_price" class="form-control" value="<?php echo product_form_h($form['prod_price']); ?>" min="0" step="0.01" required>
              </div>
              <div class="col-md-4 form-group">
                <label>Cost *</label>
                <input type="number" name="prod_cost" class="form-control" value="<?php echo product_form_h($form['prod_cost']); ?>" min="0" step="0.01" required>
              </div>
              <div class="col-md-4 form-group">
                <label>Stock *</label>
                <input type="number" name="prod_stock" class="form-control" value="<?php echo product_form_h($form['prod_stock']); ?>" min="0" step="1" required>
              </div>
            </div>
            <div class="form-row">
              <div class="col-md-6 form-group">
                <label>Category</label>
                <select class="form-control" name="prod_catg">
                  <option value="">No Category</option>
                  <?php foreach ($categories as $category) { ?>
                    <option value="<?php echo product_form_h($category); ?>" <?php echo $form['prod_catg'] === $category ? 'selected' : ''; ?>><?php echo product_form_h($category); ?></option>
                  <?php } ?>
                </select>
              </div>
              <div class="col-md-6 form-group">
                <label>Barcode</label>
                <input type="text" name="prod_barcode" class="form-control" value="<?php echo product_form_h($form['prod_barcode']); ?>">
              </div>
            </div>
            <div class="form-group">
              <label>Description *</label>
              <textarea rows="5" name="prod_desc" class="form-control" required><?php echo product_form_h($form['prod_desc']); ?></textarea>
            </div>
          </div>
        </div>
      </form>

      <?php require_once('partials/_footer.php'); ?>
    </div>
  </div>

  <?php require_once('partials/_scripts.php'); ?>
  <script>
    const productInput = document.getElementById('prodImageInput');
    const productPreview = document.getElementById('productImagePreview');
    if (productInput && productPreview) {
      productInput.addEventListener('change', function() {
        const file = this.files && this.files[0];
        if (!file) return;
        productPreview.src = URL.createObjectURL(file);
      });
    }
  </script>
</body>

</html>
