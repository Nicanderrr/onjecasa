<?php
session_start();
include('config/config.php');
include('config/checklogin.php');
include('config/code-generator.php');

check_login();

function invoice_h($value)
{
  return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function invoice_product_image_src($image)
{
  $image = trim((string) $image);
  if ($image !== '' && file_exists(__DIR__ . '/assets/img/products/' . $image)) {
    return 'assets/img/products/' . rawurlencode($image);
  }
  return 'assets/img/products/place.png';
}

function invoice_row_html($products, $selectedName = '', $price = '', $qty = '1', $total = '')
{
  $selectedImage = 'assets/img/products/place.png';
  foreach ($products as $prod) {
    if ($selectedName === $prod->prod_name) {
      $selectedImage = invoice_product_image_src($prod->prod_img);
      break;
    }
  }

  ob_start();
  ?>
  <tr class="invoice-line">
    <td>
      <div class="product-search-shell">
        <i class="fas fa-search"></i>
        <input type="search" class="form-control product-search-input" placeholder="Search product..." value="<?php echo invoice_h($selectedName); ?>" autocomplete="off">
        <input type="hidden" class="product-select" name="pname[]" value="<?php echo invoice_h($selectedName); ?>">
        <div class="product-search-menu" aria-hidden="true"></div>
      </div>
    </td>
    <td>
      <img src="<?php echo invoice_h($selectedImage); ?>" class="invoice-product-image" alt="Product image">
    </td>
    <td>
      <input type="number" min="0" step="0.01" name="price[]" class="form-control price-input" value="<?php echo invoice_h($price); ?>" required>
    </td>
    <td>
      <input type="number" min="1" step="1" name="qty[]" class="form-control qty-input" value="<?php echo invoice_h($qty); ?>" required>
    </td>
    <td>
      <input type="number" min="0" step="0.01" name="total[]" class="form-control total-input" value="<?php echo invoice_h($total); ?>" readonly>
    </td>
    <td class="text-right">
      <button type="button" class="btn btn-danger btn-sm btn-row-remove">
        <i class="fas fa-times"></i>
      </button>
    </td>
  </tr>
  <?php
  return ob_get_clean();
}

$products = [];
$productPrices = [];
$productImages = [];
$productQuery = $mysqli->query("SELECT prod_name, prod_price, prod_img FROM rpos_products ORDER BY prod_name ASC");
while ($productQuery && $row = $productQuery->fetch_object()) {
  $products[] = $row;
  $productPrices[$row->prod_name] = number_format((float) $row->prod_price, 2, '.', '');
  $productImages[$row->prod_name] = invoice_product_image_src($row->prod_img);
}

$defaultOrderCode = $alpha . '-' . $beta;
$orderCodeValue = $_POST['order_code'] ?? $defaultOrderCode;
$renderRows = [];
$subTotal = 0;
$itemCount = 0;

if (isset($_POST['submit'])) {
  $order_code = trim((string) ($_POST['order_code'] ?? ''));
  $productNames = $_POST['pname'] ?? [];
  $prices = $_POST['price'] ?? [];
  $quantities = $_POST['qty'] ?? [];

  if ($order_code === '') {
    $err = "Order code is required";
  }

  if (!isset($err)) {
    for ($i = 0; $i < count($productNames); $i++) {
      $name = trim((string) ($productNames[$i] ?? ''));
      $price = trim((string) ($prices[$i] ?? ''));
      $qty = trim((string) ($quantities[$i] ?? ''));

      $linePrice = (float) $price;
      $lineQty = (int) $qty;
      $lineTotal = $linePrice * $lineQty;

      $renderRows[] = [
        'name' => $name,
        'price' => $price,
        'qty' => $qty !== '' ? $qty : '1',
        'total' => number_format($lineTotal, 2, '.', ''),
      ];

      if ($name === '') {
        continue;
      }
      if ($linePrice < 0 || $lineQty <= 0) {
        $err = "Please enter valid product prices and quantities";
        break;
      }
      $subTotal += $lineTotal;
      $itemCount += $lineQty;
    }
  }

  if (!isset($err) && $itemCount === 0) {
    $err = "Add at least one product line";
  }

  if (!isset($err)) {
    $mysqli->begin_transaction();
    try {
      $invoiceStmt = $mysqli->prepare("INSERT INTO invoice (cname, order_code, GRAND_TOTAL) VALUES ('', ?, ?)");
      $invoiceStmt->bind_param('sd', $order_code, $subTotal);
      if (!$invoiceStmt->execute()) {
        throw new RuntimeException('Invoice insert failed');
      }
      $invoiceStmt->close();

      $lineStmt = $mysqli->prepare("INSERT INTO invoice_products (SID, PNAME, PRICE, QTY, TOTAL) VALUES (?, ?, ?, ?, ?)");
      foreach ($renderRows as $row) {
        if ($row['name'] === '') {
          continue;
        }
        $linePrice = (float) $row['price'];
        $lineQty = (int) $row['qty'];
        $lineTotal = $linePrice * $lineQty;
        $priceValue = number_format($linePrice, 2, '.', '');
        $totalValue = number_format($lineTotal, 2, '.', '');
        $lineStmt->bind_param('sssss', $order_code, $row['name'], $priceValue, $row['qty'], $totalValue);
        if (!$lineStmt->execute()) {
          throw new RuntimeException('Line insert failed');
        }
      }
      $lineStmt->close();

      $mysqli->commit();
      $success = "Order Submitted";
      header("refresh:1; url=payments.php");
      exit;
    } catch (Throwable $e) {
      $mysqli->rollback();
      $err = "Invoice could not be saved";
    }
  }
}

if (empty($renderRows)) {
  $renderRows[] = [
    'name' => '',
    'price' => '',
    'qty' => '1',
    'total' => '',
  ];
}

require_once('partials/_head.php');
?>

<body>
  <?php require_once('partials/_sidebar.php'); ?>
  <div class="main-content">
    <?php require_once('partials/_topnav.php'); ?>

    <div class="container-fluid mt-4 invoice-workspace">
      <div class="invoice-builder-grid">
        <form method="post" action="invo.php" class="invoice-builder-form" id="invoiceBuilderForm">
          <div class="card shadow invoice-panel">
            <div class="card-header border-0 invoice-panel-head">
              <div>
                <h3>Invoice Builder</h3>
                <p>Select products, adjust quantities, and review totals before proceeding.</p>
              </div>
              <div class="invoice-order-chip">
                <span>Order Code</span>
                <strong><?php echo invoice_h($orderCodeValue); ?></strong>
              </div>
            </div>

            <div class="invoice-toolbar">
              <div class="invoice-toolbar-note">
                <i class="fas fa-info-circle"></i>
                Build the sale without collecting customer details.
              </div>
              <button type="button" class="btn btn-outline-primary" id="btn-add-row">
                <i class="fas fa-plus"></i>
                Add line
              </button>
            </div>

            <input type="hidden" name="order_code" value="<?php echo invoice_h($orderCodeValue); ?>">

            <div class="invoice-live-summary">
              <div class="invoice-live-summary-item">
                <span>Total Items</span>
                <strong id="builderLineCount"><?php echo number_format($itemCount); ?></strong>
              </div>
              <div class="invoice-live-summary-item">
                <span>Subtotal</span>
                <strong id="builderSubtotal">&#8373; <?php echo number_format($subTotal, 2); ?></strong>
              </div>
              <div class="invoice-live-summary-item invoice-live-summary-total">
                <span>Grand Total</span>
                <strong id="builderGrandTotal">&#8373; <?php echo number_format($subTotal, 2); ?></strong>
              </div>
              <div class="invoice-live-summary-action">
                <button type="submit" name="submit" class="btn btn-success btn-block invoice-submit-btn">
                  <i class="fas fa-arrow-right"></i>
                  Proceed
                </button>
              </div>
            </div>

            <div class="table-responsive invoice-table-wrap">
              <table class="table align-items-center table-flush invoice-table">
                <thead class="thead-light">
                  <tr>
                    <th scope="col">Product</th>
                    <th scope="col">Image</th>
                    <th scope="col">Price</th>
                    <th scope="col">Qty</th>
                    <th scope="col">Total</th>
                    <th scope="col" class="text-right">Action</th>
                  </tr>
                </thead>
                <tbody id="product_tbody">
                  <?php foreach ($renderRows as $row) {
                    echo invoice_row_html(
                      $products,
                      $row['name'],
                      $row['price'],
                      $row['qty'],
                      $row['total']
                    );
                  } ?>
                </tbody>
              </table>
            </div>
          </div>
        </form>
      </div>

      <?php require_once('partials/_footer.php'); ?>
    </div>
  </div>

  <?php require_once('partials/_scripts.php'); ?>
  <script>
    $(function() {
      const productPrices = <?php echo json_encode($productPrices); ?>;
      const productImages = <?php echo json_encode($productImages); ?>;
      const productPlaceholderImage = 'assets/img/products/place.png';
      const productNames = Object.keys(productPrices);
      const rowTemplate = () => `
          <tr class="invoice-line">
            <td>
              <div class="product-search-shell">
                <i class="fas fa-search"></i>
                <input type="search" class="form-control product-search-input" placeholder="Search product..." autocomplete="off">
                <input type="hidden" class="product-select" name="pname[]" value="">
                <div class="product-search-menu" aria-hidden="true"></div>
              </div>
            </td>
            <td>
              <img src="${productPlaceholderImage}" class="invoice-product-image" alt="Product image">
            </td>
            <td><input type="number" min="0" step="0.01" name="price[]" class="form-control price-input" value="" required></td>
            <td><input type="number" min="1" step="1" name="qty[]" class="form-control qty-input" value="1" required></td>
            <td><input type="number" min="0" step="0.01" name="total[]" class="form-control total-input" value="" readonly></td>
            <td class="text-right">
              <button type="button" class="btn btn-danger btn-sm btn-row-remove">
                <i class="fas fa-times"></i>
              </button>
            </td>
          </tr>`;

      function updateSummary() {
        let subtotal = 0;
        let items = 0;
        $('#product_tbody tr').each(function() {
          const product = $(this).find('.product-select').val();
          const price = parseFloat($(this).find('.price-input').val()) || 0;
          const qty = parseFloat($(this).find('.qty-input').val()) || 0;
          const total = price * qty;
          if (product || price > 0) {
            $(this).find('.total-input').val(total.toFixed(2));
          } else {
            $(this).find('.total-input').val('');
          }
          if (product) {
            subtotal += total;
            items += qty;
          }
        });

        $('#builderLineCount').text(items);
        $('#builderSubtotal').html('&#8373; ' + subtotal.toFixed(2));
        $('#builderGrandTotal').html('&#8373; ' + subtotal.toFixed(2));
      }

      function recalcRow($row) {
        const price = parseFloat($row.find('.price-input').val()) || 0;
        const qty = parseFloat($row.find('.qty-input').val()) || 0;
        $row.find('.total-input').val((price * qty).toFixed(2));
        updateSummary();
      }

      function selectProduct(input, productName) {
        const $currentRow = $(input).closest('tr');
        const price = parseFloat(productPrices[productName]) || 0;

        $(input).val(productName);
        $currentRow.find('.product-select').val(productName);
        $currentRow.find('.invoice-product-image').attr('src', productImages[productName] || productPlaceholderImage).attr('alt', productName);
        $currentRow.find('.price-input').val(price.toFixed(2));
        hideProductMenu($currentRow);
        recalcRow($currentRow);

        if ($currentRow.is($('#product_tbody tr').last())) {
          addEmptyRow();
        }
      }

      function clearProductRow($row) {
        $row.find('.product-select').val('');
        $row.find('.invoice-product-image').attr('src', productPlaceholderImage).attr('alt', 'Product image');
        $row.find('.price-input').val('');
        $row.find('.total-input').val('');
        updateSummary();
      }

      function hideProductMenu($row) {
        $row.find('.product-search-menu').empty().removeClass('is-open').attr('aria-hidden', 'true');
      }

      function renderProductMenu(input) {
        const $currentRow = $(input).closest('tr');
        const $menu = $currentRow.find('.product-search-menu');
        const query = $.trim($(input).val()).toLowerCase();

        if (!query) {
          clearProductRow($currentRow);
          hideProductMenu($currentRow);
          return;
        }

        const matches = productNames.filter(function(name) {
          return name.toLowerCase().indexOf(query) !== -1;
        }).slice(0, 8);

        if (!matches.length) {
          clearProductRow($currentRow);
          $menu.html('<div class="product-search-empty">No product found</div>').addClass('is-open').attr('aria-hidden', 'false');
          return;
        }

        clearProductRow($currentRow);
        $menu.html(matches.map(function(name) {
          const price = parseFloat(productPrices[name]) || 0;
          return '<button type="button" class="product-search-option" data-product="' + $('<div>').text(name).html() + '">' +
            '<span>' + $('<div>').text(name).html() + '</span>' +
            '<strong>&#8373; ' + price.toFixed(2) + '</strong>' +
          '</button>';
        }).join('')).addClass('is-open').attr('aria-hidden', 'false');
      }

      function handleProductSearch(input) {
        const $currentRow = $(input).closest('tr');
        const productName = $.trim($(input).val());
        if (!productName) {
          clearProductRow($currentRow);
          hideProductMenu($currentRow);
          return;
        }

        if (!Object.prototype.hasOwnProperty.call(productPrices, productName)) {
          renderProductMenu(input);
          return;
        }

        selectProduct(input, productName);
      }

      $('#product_tbody')
        .off('input focus', '.product-search-input')
        .on('input focus', '.product-search-input', function() {
          renderProductMenu(this);
        });

      $('#product_tbody')
        .off('change blur', '.product-search-input')
        .on('change blur', '.product-search-input', function() {
          const input = this;
          setTimeout(function() {
            handleProductSearch(input);
            hideProductMenu($(input).closest('tr'));
          }, 120);
        });

      $('#product_tbody')
        .off('mousedown', '.product-search-option')
        .on('mousedown', '.product-search-option', function(event) {
          event.preventDefault();
          const $row = $(this).closest('tr');
          selectProduct($row.find('.product-search-input')[0], $(this).data('product'));
        });

      $('#product_tbody')
        .off('input', '.price-input, .qty-input')
        .on('input', '.price-input, .qty-input', function() {
          recalcRow($(this).closest('tr'));
        });

      $('#product_tbody')
        .off('click', '.btn-row-remove')
        .on('click', '.btn-row-remove', function() {
          if ($('#product_tbody tr').length > 1) {
            $(this).closest('tr').remove();
            updateSummary();
          }
        });

      function addEmptyRow() {
        const rowHtml = rowTemplate();
        $('#product_tbody').append(rowHtml);
        updateSummary();
      }

      $('#product_tbody .product-search-input').each(function() {
        if ($(this).val() && !$(this).closest('tr').find('.price-input').val()) {
          handleProductSearch(this);
        }
      });

      $('#btn-add-row').on('click', function() {
        addEmptyRow();
      });

      updateSummary();
    });
  </script>
</body>

</html>
