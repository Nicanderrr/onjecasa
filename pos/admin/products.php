<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();

function product_h($value)
{
  return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function product_money($value)
{
  return '&#8373; ' . number_format((float) $value, 2);
}

function product_image_src($image)
{
  $image = basename((string) $image);
  if ($image !== '' && file_exists(__DIR__ . '/assets/img/products/' . $image)) {
    return 'assets/img/products/' . rawurlencode($image);
  }
  return 'assets/img/products/place.png';
}

function product_stock_status($stock)
{
  $stock = (int) $stock;
  if ($stock <= 0) {
    return ['label' => 'Out of stock', 'class' => 'badge-danger'];
  }
  if ($stock <= 10) {
    return ['label' => 'Low stock', 'class' => 'badge-warning'];
  }
  return ['label' => 'In stock', 'class' => 'badge-success'];
}

if (isset($_GET['delete'])) {
  $id = trim($_GET['delete']);
  $adn = "DELETE FROM rpos_products WHERE prod_id = ?";
  $stmt = $mysqli->prepare($adn);
  $stmt->bind_param('s', $id);
  $stmt->execute();
  if ($stmt->affected_rows > 0) {
    $success = "Product Deleted";
    header("refresh:1; url=products.php");
  } else {
    $err = "Product could not be deleted";
  }
  $stmt->close();
}

$products = [];
$productResult = $mysqli->query("SELECT * FROM rpos_products ORDER BY created_at DESC");
while ($productResult && $row = $productResult->fetch_object()) {
  $products[] = $row;
}

$categories = [];
$categoryResult = $mysqli->query("SELECT catg_name FROM rpos_categories ORDER BY catg_name ASC");
while ($categoryResult && $cat = $categoryResult->fetch_object()) {
  if (trim((string) $cat->catg_name) !== '') {
    $categories[$cat->catg_name] = $cat->catg_name;
  }
}
foreach ($products as $product) {
  if (trim((string) $product->prod_catg) !== '') {
    $categories[$product->prod_catg] = $product->prod_catg;
  }
}
ksort($categories);

$query = trim($_GET['q'] ?? '');
$categoryFilter = trim($_GET['category'] ?? '');
$stockFilter = trim($_GET['stock'] ?? '');

$totalProducts = count($products);
$lowStockProducts = 0;
$outOfStockProducts = 0;
$inventoryValue = 0;

foreach ($products as $product) {
  $stock = (int) $product->prod_stock;
  $inventoryValue += ((float) $product->prod_price) * max(0, $stock);
  if ($stock <= 0) {
    $outOfStockProducts++;
  } elseif ($stock <= 10) {
    $lowStockProducts++;
  }
}

$filteredProducts = array_values(array_filter($products, function ($product) use ($query, $categoryFilter, $stockFilter) {
  if ($query !== '') {
    $haystack = strtolower($product->prod_name . ' ' . $product->prod_code . ' ' . $product->prod_barcode . ' ' . $product->prod_desc);
    if (strpos($haystack, strtolower($query)) === false) {
      return false;
    }
  }

  if ($categoryFilter !== '' && (string) $product->prod_catg !== $categoryFilter) {
    return false;
  }

  $stock = (int) $product->prod_stock;
  if ($stockFilter === 'in' && $stock <= 10) {
    return false;
  }
  if ($stockFilter === 'low' && ($stock <= 0 || $stock > 10)) {
    return false;
  }
  if ($stockFilter === 'out' && $stock > 0) {
    return false;
  }

  return true;
}));

require_once('partials/_head.php');
?>

<body>
  <?php require_once('partials/_sidebar.php'); ?>
  <div class="main-content">
    <?php require_once('partials/_topnav.php'); ?>

    <div class="container-fluid mt-4 product-workspace">
      <div class="row product-metric-row">
        <div class="col-xl-3 col-md-6">
          <div class="card card-stats metric-primary mb-4 mb-xl-0">
            <div class="card-body">
              <div class="row align-items-start">
                <div class="col">
                  <h5 class="card-title text-uppercase text-muted mb-0">Total Products</h5>
                  <span class="h2 font-weight-bold mb-0"><?php echo number_format($totalProducts); ?></span>
                </div>
                <div class="col-auto">
                  <div class="icon icon-shape bg-primary text-white rounded-circle shadow">
                    <i class="bi bi-box-seam"></i>
                  </div>
                </div>
              </div>
              <p class="metric-meta mb-0">Active catalog items</p>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-md-6">
          <div class="card card-stats metric-warning mb-4 mb-xl-0">
            <div class="card-body">
              <div class="row align-items-start">
                <div class="col">
                  <h5 class="card-title text-uppercase text-muted mb-0">Low Stock</h5>
                  <span class="h2 font-weight-bold mb-0"><?php echo number_format($lowStockProducts); ?></span>
                </div>
                <div class="col-auto">
                  <div class="icon icon-shape bg-warning text-white rounded-circle shadow">
                    <i class="bi bi-exclamation-triangle"></i>
                  </div>
                </div>
              </div>
              <p class="metric-meta mb-0">10 units or fewer</p>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-md-6">
          <div class="card card-stats metric-danger mb-4 mb-xl-0">
            <div class="card-body">
              <div class="row align-items-start">
                <div class="col">
                  <h5 class="card-title text-uppercase text-muted mb-0">Out of Stock</h5>
                  <span class="h2 font-weight-bold mb-0"><?php echo number_format($outOfStockProducts); ?></span>
                </div>
                <div class="col-auto">
                  <div class="icon icon-shape bg-danger text-white rounded-circle shadow">
                    <i class="bi bi-x-circle"></i>
                  </div>
                </div>
              </div>
              <p class="metric-meta mb-0">Needs immediate refill</p>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-md-6">
          <div class="card card-stats metric-success mb-4 mb-xl-0">
            <div class="card-body">
              <div class="row align-items-start">
                <div class="col">
                  <h5 class="card-title text-uppercase text-muted mb-0">Inventory Value</h5>
                  <span class="h2 font-weight-bold mb-0"><?php echo product_money($inventoryValue); ?></span>
                </div>
                <div class="col-auto">
                  <div class="icon icon-shape bg-green text-white rounded-circle shadow">
                    <i class="bi bi-cash-coin"></i>
                  </div>
                </div>
              </div>
              <p class="metric-meta mb-0">Retail value on hand</p>
            </div>
          </div>
        </div>
      </div>

      <div class="product-panel card shadow">
        <div class="card-header border-0 product-panel-head">
          <div>
            <h3>Product Inventory</h3>
            <p>Search, filter, and manage products from one workspace.</p>
          </div>
          <a href="add_product.php" class="btn btn-outline-success">
            <i class="fas fa-utensils"></i>
            New Product
          </a>
        </div>

        <form class="product-filter-bar" method="GET">
          <div class="product-search-field">
            <i class="fas fa-search"></i>
            <input type="search" name="q" value="<?php echo product_h($query); ?>" placeholder="Search name, SKU, barcode">
          </div>
          <select name="category" class="form-control">
            <option value="">All categories</option>
            <?php foreach ($categories as $category) { ?>
              <option value="<?php echo product_h($category); ?>" <?php echo $categoryFilter === $category ? 'selected' : ''; ?>>
                <?php echo product_h($category); ?>
              </option>
            <?php } ?>
          </select>
          <select name="stock" class="form-control">
            <option value="">All stock levels</option>
            <option value="in" <?php echo $stockFilter === 'in' ? 'selected' : ''; ?>>In stock</option>
            <option value="low" <?php echo $stockFilter === 'low' ? 'selected' : ''; ?>>Low stock</option>
            <option value="out" <?php echo $stockFilter === 'out' ? 'selected' : ''; ?>>Out of stock</option>
          </select>
          <button class="btn btn-primary" type="submit"><i class="fas fa-filter"></i></button>
          <a class="btn btn-light" href="products.php"><i class="fas fa-times"></i></a>
        </form>

        <div class="table-responsive product-table-wrap">
          <table class="table align-items-center table-flush product-inventory-table" id="productInventoryTable">
            <thead class="thead-light">
              <tr>
                <th scope="col">Image</th>
                <th scope="col">Product</th>
                <th scope="col">Category</th>
                <th scope="col">Stock</th>
                <th scope="col">Price</th>
                <th scope="col">Cost</th>
                <th scope="col">Margin</th>
                <th scope="col">Barcode</th>
                <th scope="col">Status</th>
                <th scope="col" class="text-right">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (count($filteredProducts) === 0) { ?>
                <tr>
                  <td colspan="10">
                    <div class="product-empty-state">
                      <strong>No products found</strong>
                      <span>Try a different search or clear the filters.</span>
                    </div>
                  </td>
                </tr>
              <?php } ?>
              <?php foreach ($filteredProducts as $prod) {
                $price = (float) $prod->prod_price;
                $cost = (float) $prod->prod_cost;
                $profit = $price - $cost;
                $margin = $price > 0 ? ($profit / $price) * 100 : 0;
                $status = product_stock_status($prod->prod_stock);
              ?>
                <tr>
                  <td>
                    <img src="<?php echo product_h(product_image_src($prod->prod_img)); ?>" class="table-product-image" alt="<?php echo product_h($prod->prod_name); ?>">
                  </td>
                  <td>
                    <div class="product-table-main">
                      <div>
                        <strong><?php echo product_h($prod->prod_name); ?></strong>
                        <span><?php echo product_h($prod->prod_code); ?></span>
                      </div>
                    </div>
                  </td>
                  <td><?php echo product_h($prod->prod_catg ?: 'No Category'); ?></td>
                  <td><strong><?php echo number_format((int) $prod->prod_stock); ?></strong></td>
                  <td><?php echo product_money($prod->prod_price); ?></td>
                  <td><?php echo product_money($prod->prod_cost); ?></td>
                  <td>
                    <span class="product-margin <?php echo $profit < 0 ? 'is-loss' : 'is-profit'; ?>">
                      <?php echo product_money($profit); ?>
                      <small><?php echo number_format($margin, 1); ?>%</small>
                    </span>
                  </td>
                  <td><?php echo product_h($prod->prod_barcode ?: 'N/A'); ?></td>
                  <td><span class="badge <?php echo $status['class']; ?>"><?php echo $status['label']; ?></span></td>
                  <td class="text-right">
                    <div class="product-action-group">
                      <a href="update_product.php?update=<?php echo urlencode($prod->prod_id); ?>" class="btn btn-sm btn-primary" title="Edit product">
                        <i class="fas fa-edit"></i>
                      </a>
                      <a href="products.php?delete=<?php echo urlencode($prod->prod_id); ?>" class="btn btn-sm btn-danger" title="Delete product" onclick="return confirm('Delete <?php echo product_h($prod->prod_name); ?>? This cannot be undone.');">
                        <i class="fas fa-trash"></i>
                      </a>
                    </div>
                  </td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>

      <?php require_once('partials/_footer.php'); ?>
    </div>
  </div>

  <?php require_once('partials/_scripts.php'); ?>
  <script>
    if (window.jQuery && $.fn.DataTable) {
      $('#productInventoryTable').DataTable({
        searching: false,
        lengthChange: false,
        pageLength: 10,
        order: [],
        language: {
          paginate: {
            previous: '<i class="fas fa-chevron-left"></i>',
            next: '<i class="fas fa-chevron-right"></i>'
          }
        }
      });
    }
  </script>
</body>

</html>
