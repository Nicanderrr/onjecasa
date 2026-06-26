<?php
ob_start();
session_start();
include('config/config.php');
include('config/checklogin.php');
check_login();

function category_h($value)
{
  return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

if (isset($_GET['delete'])) {
  $id = (int) $_GET['delete'];
  $adn = "DELETE FROM rpos_categories WHERE catg_id = ?";
  $stmt = $mysqli->prepare($adn);
  $stmt->bind_param('i', $id);
  $stmt->execute();
  if ($stmt->affected_rows > 0) {
    $success = "Category Deleted";
    header("refresh:1; url=categories.php");
  } else {
    $err = "Category could not be deleted";
  }
  $stmt->close();
}

$categories = [];
$categoryCounts = [];
$categoryResult = $mysqli->query("SELECT * FROM rpos_categories ORDER BY catg_name ASC");
while ($categoryResult && $row = $categoryResult->fetch_object()) {
  $categories[] = $row;
}

$countResult = $mysqli->query("SELECT prod_catg, COUNT(*) AS product_count FROM rpos_products GROUP BY prod_catg");
while ($countResult && $row = $countResult->fetch_object()) {
  $key = trim((string) $row->prod_catg);
  if ($key !== '') {
    $categoryCounts[$key] = (int) $row->product_count;
  }
}

$query = trim($_GET['q'] ?? '');
$statusFilter = trim($_GET['status'] ?? '');

$totalCategories = count($categories);
$usedCategories = 0;
$emptyCategories = 0;
$totalTaggedProducts = array_sum($categoryCounts);

$filteredCategories = array_values(array_filter($categories, function ($category) use ($query, $statusFilter, $categoryCounts, &$usedCategories, &$emptyCategories) {
  $name = (string) $category->catg_name;
  $code = (string) $category->catg_code;
  $productCount = $categoryCounts[$name] ?? 0;
  $isUsed = $productCount > 0;

  if ($isUsed) {
    $usedCategories++;
  } else {
    $emptyCategories++;
  }

  if ($query !== '') {
    $haystack = strtolower($name . ' ' . $code);
    if (strpos($haystack, strtolower($query)) === false) {
      return false;
    }
  }

  if ($statusFilter === 'used' && !$isUsed) {
    return false;
  }
  if ($statusFilter === 'empty' && $isUsed) {
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

    <div class="container-fluid mt-4 category-workspace">
      <div class="row category-metric-row">
        <div class="col-xl-3 col-md-6">
          <div class="card card-stats metric-primary mb-4 mb-xl-0">
            <div class="card-body">
              <div class="row align-items-start">
                <div class="col">
                  <h5 class="card-title text-uppercase text-muted mb-0">Total Categories</h5>
                  <span class="h2 font-weight-bold mb-0"><?php echo number_format($totalCategories); ?></span>
                </div>
                <div class="col-auto">
                  <div class="icon icon-shape bg-primary text-white rounded-circle shadow">
                    <i class="bi bi-tags"></i>
                  </div>
                </div>
              </div>
              <p class="metric-meta mb-0">Catalog groups</p>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-md-6">
          <div class="card card-stats metric-success mb-4 mb-xl-0">
            <div class="card-body">
              <div class="row align-items-start">
                <div class="col">
                  <h5 class="card-title text-uppercase text-muted mb-0">Used Categories</h5>
                  <span class="h2 font-weight-bold mb-0"><?php echo number_format($usedCategories); ?></span>
                </div>
                <div class="col-auto">
                  <div class="icon icon-shape bg-green text-white rounded-circle shadow">
                    <i class="bi bi-grid-1x2"></i>
                  </div>
                </div>
              </div>
              <p class="metric-meta mb-0">Have products assigned</p>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-md-6">
          <div class="card card-stats metric-warning mb-4 mb-xl-0">
            <div class="card-body">
              <div class="row align-items-start">
                <div class="col">
                  <h5 class="card-title text-uppercase text-muted mb-0">Empty Categories</h5>
                  <span class="h2 font-weight-bold mb-0"><?php echo number_format($emptyCategories); ?></span>
                </div>
                <div class="col-auto">
                  <div class="icon icon-shape bg-warning text-white rounded-circle shadow">
                    <i class="bi bi-folder-minus"></i>
                  </div>
                </div>
              </div>
              <p class="metric-meta mb-0">No products yet</p>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-md-6">
          <div class="card card-stats metric-danger mb-4 mb-xl-0">
            <div class="card-body">
              <div class="row align-items-start">
                <div class="col">
                  <h5 class="card-title text-uppercase text-muted mb-0">Tagged Products</h5>
                  <span class="h2 font-weight-bold mb-0"><?php echo number_format($totalTaggedProducts); ?></span>
                </div>
                <div class="col-auto">
                  <div class="icon icon-shape bg-danger text-white rounded-circle shadow">
                    <i class="bi bi-box-seam"></i>
                  </div>
                </div>
              </div>
              <p class="metric-meta mb-0">Products assigned to categories</p>
            </div>
          </div>
        </div>
      </div>

      <div class="product-panel card shadow">
        <div class="card-header border-0 product-panel-head">
          <div>
            <h3>Category Inventory</h3>
            <p>Group products, filter empty categories, and manage names from one place.</p>
          </div>
          <a href="add_catg.php" class="btn btn-outline-success">
            <i class="bi bi-bookmark-plus-fill"></i>
            New Category
          </a>
        </div>

        <form class="product-filter-bar" method="GET">
          <div class="product-search-field">
            <i class="fas fa-search"></i>
            <input type="search" name="q" value="<?php echo category_h($query); ?>" placeholder="Search category name or code">
          </div>
          <select name="status" class="form-control">
            <option value="">All status</option>
            <option value="used" <?php echo $statusFilter === 'used' ? 'selected' : ''; ?>>Used</option>
            <option value="empty" <?php echo $statusFilter === 'empty' ? 'selected' : ''; ?>>Empty</option>
          </select>
          <button class="btn btn-primary" type="submit"><i class="fas fa-filter"></i></button>
          <a class="btn btn-light" href="categories.php"><i class="fas fa-times"></i></a>
        </form>

        <div class="table-responsive product-table-wrap">
          <table class="table align-items-center table-flush product-inventory-table" id="categoryInventoryTable">
            <thead class="thead-light">
              <tr>
                <th scope="col">Icon</th>
                <th scope="col">Category</th>
                <th scope="col">Code</th>
                <th scope="col">Products</th>
                <th scope="col">Status</th>
                <th scope="col" class="text-right">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (count($filteredCategories) === 0) { ?>
                <tr>
                  <td colspan="6">
                    <div class="product-empty-state">
                      <strong>No categories found</strong>
                      <span>Try a different search or clear the filters.</span>
                    </div>
                  </td>
                </tr>
              <?php } ?>
              <?php foreach ($filteredCategories as $catg) {
                $productCount = $categoryCounts[$catg->catg_name] ?? 0;
                $statusLabel = $productCount > 0 ? 'In use' : 'Empty';
                $statusClass = $productCount > 0 ? 'badge-success' : 'badge-warning';
              ?>
                <tr>
                  <td>
                    <span class="category-swatch" aria-hidden="true">
                      <i class="bi bi-folder2-open"></i>
                    </span>
                  </td>
                  <td>
                    <div class="category-table-main">
                      <div>
                        <strong><?php echo category_h($catg->catg_name); ?></strong>
                        <span>Category group</span>
                      </div>
                    </div>
                  </td>
                  <td><?php echo category_h($catg->catg_code); ?></td>
                  <td><strong><?php echo number_format($productCount); ?></strong></td>
                  <td><span class="badge <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span></td>
                  <td class="text-right">
                    <div class="product-action-group">
                      <a href="update_catg.php?update=<?php echo (int) $catg->catg_id; ?>" class="btn btn-sm btn-primary" title="Edit category">
                        <i class="fas fa-edit"></i>
                      </a>
                      <a href="categories.php?delete=<?php echo (int) $catg->catg_id; ?>" class="btn btn-sm btn-danger" title="Delete category" onclick="return confirm('Delete <?php echo category_h($catg->catg_name); ?>? This cannot be undone.');">
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
      $('#categoryInventoryTable').DataTable({
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
