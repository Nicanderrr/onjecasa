<?php
$receiptId = trim((string) ($_GET['id'] ?? ''));

if ($receiptId === '') {
    http_response_code(400);
    echo 'Missing receipt id.';
    exit;
}

function receipt_money($value): string
{
    return number_format((float) str_replace(',', '', (string) $value), 2);
}

function receipt_text($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function receipt_qty($value): string
{
    return rtrim(rtrim(number_format((float) $value, 2), '0'), '.');
}

$company = [
    'company' => 'Command Center POS',
    'address' => '172 Banana Street',
    'city' => 'Accra',
    'phone' => '(+233) 337-337-3069',
    'command_center_image' => '',
];

$invoice = [
    'customer' => 'Walk-in Customer',
    'invoice_no' => $receiptId,
    'invoice_date' => date('d M Y, g:i A'),
    'total_amt' => 0,
];

$products = [];
$paymentMethod = 'Not recorded';
$paymentReference = 'Not recorded';
$paidAt = '';
$salesPerson = $receiptSalesPerson ?? '';

$stmt = $mysqli->prepare('SELECT company, address, city, phone, command_center_image FROM company_info LIMIT 1');
if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $company = array_merge($company, array_filter($row, static fn($value) => $value !== null && $value !== ''));
    }
    $stmt->close();
}

$stmt = $mysqli->prepare('SELECT cname, order_code, GRAND_TOTAL, created_at FROM invoice WHERE order_code = ? LIMIT 1');
$stmt->bind_param('s', $receiptId);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $invoice = [
        'customer' => $row['cname'] ?: 'Walk-in Customer',
        'invoice_no' => $row['order_code'],
        'invoice_date' => date('d M Y, g:i A', strtotime($row['created_at'])),
        'total_amt' => (float) str_replace(',', '', (string) $row['GRAND_TOTAL']),
    ];
} else {
    http_response_code(404);
    echo 'Receipt not found.';
    exit;
}
$stmt->close();

$stmt = $mysqli->prepare('SELECT PNAME, PRICE, QTY, TOTAL FROM invoice_products WHERE SID = ?');
$stmt->bind_param('s', $receiptId);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $products[] = [
        'name' => $row['PNAME'],
        'price' => (float) str_replace(',', '', (string) $row['PRICE']),
        'qty' => (float) $row['QTY'],
        'total' => (float) str_replace(',', '', (string) $row['TOTAL']),
    ];
}
$stmt->close();

$stmt = $mysqli->prepare('SELECT pay_method, pay_code, created_at FROM rpos_payments WHERE SID = ? ORDER BY created_at DESC LIMIT 1');
$stmt->bind_param('s', $receiptId);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $paymentMethod = $row['pay_method'] ?: $paymentMethod;
    $paymentReference = $row['pay_code'] ?: $paymentReference;
    $paidAt = !empty($row['created_at']) ? date('d M Y, g:i A', strtotime($row['created_at'])) : '';
}
$stmt->close();

if ($salesPerson === '') {
    $stmt = $mysqli->prepare('SELECT admin_name FROM rpos_admin LIMIT 1');
    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $salesPerson = $row['admin_name'] ?? '';
        }
        $stmt->close();
    }
}

$subTotal = array_sum(array_column($products, 'total'));
if ($subTotal <= 0) {
    $subTotal = (float) $invoice['total_amt'];
}

$grandTotal = (float) $invoice['total_amt'];
$itemCount = array_sum(array_column($products, 'qty'));
$lineCount = count($products);
$receiptInitials = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $company['company']), 0, 2)) ?: 'POS';
$receiptLogo = trim((string) ($company['command_center_image'] ?? ''));
if ($receiptLogo !== '' && !preg_match('/^https?:\/\//i', $receiptLogo) && strpos($receiptLogo, '/') === 0) {
    $receiptLogo = ltrim($receiptLogo, '/');
}
if ($receiptLogo !== '' && !preg_match('/^https?:\/\//i', $receiptLogo) && strpos($receiptLogo, 'assets/') === 0) {
    $receiptLogo = strpos($_SERVER['PHP_SELF'] ?? '', '/cashier/') !== false ? '../admin/' . $receiptLogo : $receiptLogo;
}
$verificationSeed = $invoice['invoice_no'] . '|' . $grandTotal . '|' . $paymentReference;
$verificationCode = strtoupper(substr(hash('sha256', $verificationSeed), 0, 12));
$shortReference = $paymentReference !== 'Not recorded' ? substr($paymentReference, 0, 18) : 'Manual entry';
$generatedAt = date('d M Y, g:i A');
$verificationPayload = implode("\n", [
    $company['company'] . ' Receipt',
    'Receipt: ' . $invoice['invoice_no'],
    'Total: GHS ' . receipt_money($grandTotal),
    'Paid: ' . ($paidAt ?: $invoice['invoice_date']),
    'Verification: ' . $verificationCode,
]);
$qrImageUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=180x180&margin=10&data=' . rawurlencode($verificationPayload);
$barcodeImageUrl = 'https://bwipjs-api.metafloor.com/?bcid=code128&scale=2&height=12&includetext&text=' . rawurlencode($invoice['invoice_no']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Receipt #<?php echo receipt_text($invoice['invoice_no']); ?></title>
  <style>
    :root {
      --ink: #111827;
      --muted: #64748b;
      --line: #dbe4ef;
      --paper: #fffefa;
      --panel: #ffffff;
      --soft: #f6f9fc;
      --navy: #0f172a;
      --blue: #2563eb;
      --cyan: #0891b2;
      --green: #10b981;
      --amber: #d97706;
      --red: #e11d48;
    }

    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      color: var(--ink);
      background:
        linear-gradient(135deg, rgba(37, 99, 235, 0.14), transparent 34rem),
        linear-gradient(315deg, rgba(16, 185, 129, 0.13), transparent 30rem),
        #eef3f8;
      font-family: Constantia, "Times New Roman", serif;
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
    }

    .print-actions {
      position: sticky;
      top: 0;
      z-index: 20;
      display: flex;
      justify-content: center;
      gap: 0.75rem;
      padding: 1rem;
      border-bottom: 1px solid rgba(148, 163, 184, 0.28);
      background: rgba(248, 250, 252, 0.82);
      backdrop-filter: blur(16px);
    }

    .print-actions button,
    .print-actions a {
      display: inline-flex;
      min-height: 42px;
      align-items: center;
      justify-content: center;
      padding: 0 1.2rem;
      border: 1px solid transparent;
      border-radius: 8px;
      font-family: inherit;
      font-size: 0.96rem;
      font-weight: 600;
      text-decoration: none;
      cursor: pointer;
    }

    .print-actions button {
      color: #fff;
      background: var(--navy);
      box-shadow: 0 16px 32px rgba(15, 23, 42, 0.22);
    }

    .print-actions a {
      color: var(--navy);
      border-color: var(--line);
      background: #fff;
    }

    .receipt-stage {
      width: min(1040px, calc(100% - 32px));
      margin: 2rem auto;
    }

    .receipt {
      position: relative;
      overflow: hidden;
      border: 1px solid rgba(148, 163, 184, 0.34);
      border-radius: 8px;
      background: var(--paper);
      box-shadow: 0 34px 90px rgba(15, 23, 42, 0.18);
    }

    .receipt::before,
    .receipt::after {
      content: "";
      position: absolute;
      right: 0;
      left: 0;
      height: 10px;
      background: repeating-linear-gradient(90deg, var(--navy) 0 22px, var(--blue) 22px 38px, var(--green) 38px 54px, var(--amber) 54px 70px);
    }

    .receipt::before {
      top: 0;
    }

    .receipt::after {
      bottom: 0;
    }

    .receipt-header {
      display: grid;
      grid-template-columns: minmax(0, 1fr) minmax(280px, 0.48fr);
      gap: 1.4rem;
      padding: 2.2rem 2.2rem 1.4rem;
      background:
        linear-gradient(135deg, #ffffff 0%, #f8fbff 62%, #eef6ff 100%);
    }

    .brand-block {
      display: grid;
      grid-template-columns: auto minmax(0, 1fr);
      gap: 1rem;
      align-items: center;
      min-width: 0;
    }

    .brand-mark {
      display: grid;
      width: 72px;
      height: 72px;
      place-items: center;
      overflow: hidden;
      border: 1px solid #bfdbfe;
      border-radius: 8px;
      background: #eff6ff;
      color: var(--blue);
      font-size: 1.28rem;
      font-weight: 600;
      box-shadow: inset 0 0 0 6px #fff, 0 16px 30px rgba(37, 99, 235, 0.14);
    }

    .brand-mark img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .brand-name {
      margin: 0;
      color: var(--navy);
      font-size: clamp(1.9rem, 4vw, 3.2rem);
      font-weight: 600;
      line-height: 0.98;
    }

    .brand-meta {
      margin: 0.75rem 0 0;
      max-width: 35rem;
      color: var(--muted);
      font-size: 1rem;
      line-height: 1.55;
    }

    .receipt-status {
      display: grid;
      align-content: space-between;
      gap: 1rem;
      min-height: 190px;
      padding: 1.15rem;
      border: 1px solid #cfe0f4;
      border-radius: 8px;
      background: #fff;
      box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
    }

    .paid-chip {
      display: inline-flex;
      width: fit-content;
      align-items: center;
      gap: 0.45rem;
      padding: 0.42rem 0.72rem;
      border: 1px solid #bbf7d0;
      border-radius: 999px;
      color: #047857;
      background: #ecfdf5;
      font-size: 0.82rem;
      font-weight: 600;
    }

    .paid-chip i {
      width: 0.55rem;
      height: 0.55rem;
      border-radius: 999px;
      background: var(--green);
      box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.16);
    }

    .receipt-number span,
    .receipt-number strong {
      display: block;
    }

    .receipt-number span {
      color: var(--muted);
      font-size: 0.78rem;
      letter-spacing: 0.08em;
      text-transform: uppercase;
    }

    .receipt-number strong {
      margin-top: 0.3rem;
      color: var(--navy);
      font-size: 1.35rem;
      font-weight: 600;
      word-break: break-word;
    }

    .grand-total-panel {
      padding: 1rem;
      border-radius: 8px;
      color: #fff;
      background: linear-gradient(135deg, var(--navy), #1e3a8a 56%, var(--blue));
    }

    .grand-total-panel span {
      display: block;
      opacity: 0.78;
      font-size: 0.82rem;
    }

    .grand-total-panel strong {
      display: block;
      margin-top: 0.2rem;
      font-size: 2rem;
      font-weight: 600;
      line-height: 1.05;
    }

    .receipt-body {
      padding: 0 2.2rem 2.2rem;
    }

    .meta-strip {
      display: grid;
      grid-template-columns: repeat(4, minmax(0, 1fr));
      gap: 0.8rem;
      margin-bottom: 1.15rem;
    }

    .meta-card {
      min-width: 0;
      padding: 0.95rem;
      border: 1px solid var(--line);
      border-radius: 8px;
      background: #fff;
    }

    .meta-card span {
      display: block;
      color: var(--muted);
      font-size: 0.74rem;
      letter-spacing: 0.08em;
      text-transform: uppercase;
    }

    .meta-card strong {
      display: block;
      overflow: hidden;
      margin-top: 0.35rem;
      color: var(--navy);
      font-size: 1rem;
      font-weight: 600;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .payment-ribbon {
      display: grid;
      grid-template-columns: auto minmax(0, 1fr) auto;
      gap: 1rem;
      align-items: center;
      margin-bottom: 1.15rem;
      padding: 1rem;
      border: 1px solid #c7d2fe;
      border-radius: 8px;
      background: linear-gradient(135deg, #eef2ff, #f8fafc);
    }

    .payment-icon {
      display: grid;
      width: 46px;
      height: 46px;
      place-items: center;
      border-radius: 8px;
      color: #fff;
      background: var(--blue);
      font-weight: 600;
    }

    .payment-ribbon span,
    .verification-card span,
    .totals-card span {
      color: var(--muted);
      font-size: 0.78rem;
    }

    .payment-ribbon strong {
      display: block;
      margin-top: 0.18rem;
      color: var(--navy);
      font-size: 1.02rem;
      font-weight: 600;
      word-break: break-word;
    }

    .items-card {
      overflow: hidden;
      border: 1px solid var(--line);
      border-radius: 8px;
      background: #fff;
    }

    .items-head {
      display: flex;
      justify-content: space-between;
      gap: 1rem;
      padding: 1rem 1.1rem;
      border-bottom: 1px solid var(--line);
      background: #f8fafc;
    }

    .items-head h2 {
      margin: 0;
      color: var(--navy);
      font-size: 1.05rem;
      font-weight: 600;
    }

    .items-head p {
      margin: 0.2rem 0 0;
      color: var(--muted);
      font-size: 0.9rem;
    }

    .item-count-pill {
      align-self: center;
      padding: 0.4rem 0.65rem;
      border: 1px solid #bae6fd;
      border-radius: 999px;
      color: #075985;
      background: #f0f9ff;
      font-size: 0.82rem;
      font-weight: 600;
      white-space: nowrap;
    }

    .items-table {
      width: 100%;
      border-collapse: collapse;
    }

    .items-table th,
    .items-table td {
      padding: 0.95rem 1.1rem;
      border-bottom: 1px solid #edf2f7;
      vertical-align: top;
    }

    .items-table th {
      color: var(--muted);
      background: #fff;
      font-size: 0.72rem;
      font-weight: 600;
      letter-spacing: 0.08em;
      text-align: left;
      text-transform: uppercase;
    }

    .items-table tr:last-child td {
      border-bottom: 0;
    }

    .item-main {
      display: grid;
      gap: 0.18rem;
    }

    .item-main strong {
      color: var(--navy);
      font-size: 1rem;
      font-weight: 600;
      line-height: 1.3;
    }

    .item-main span {
      color: var(--muted);
      font-size: 0.82rem;
    }

    .num {
      text-align: right;
      white-space: nowrap;
    }

    .item-total {
      color: var(--navy);
      font-weight: 600;
    }

    .receipt-lower {
      display: grid;
      grid-template-columns: minmax(0, 1fr) 350px;
      gap: 1rem;
      margin-top: 1rem;
      align-items: start;
    }

    .verification-card,
    .totals-card,
    .thank-you-card {
      border: 1px solid var(--line);
      border-radius: 8px;
      background: #fff;
    }

    .verification-card {
      display: grid;
      grid-template-columns: minmax(0, 1fr) auto;
      gap: 1rem;
      padding: 1rem;
    }

    .verification-card h3,
    .thank-you-card h3 {
      margin: 0;
      color: var(--navy);
      font-size: 1rem;
      font-weight: 600;
    }

    .verification-code {
      display: inline-flex;
      margin-top: 0.7rem;
      padding: 0.45rem 0.65rem;
      border: 1px dashed #94a3b8;
      border-radius: 8px;
      color: var(--navy);
      background: #f8fafc;
      font-weight: 600;
      letter-spacing: 0.08em;
    }

    .qr-mark {
      display: grid;
      place-items: center;
      padding: 0.55rem;
      border: 1px solid #cbd5e1;
      border-radius: 8px;
      background: #fff;
    }

    .qr-mark img {
      display: block;
      width: 104px;
      height: 104px;
      object-fit: contain;
    }

    .thank-you-card {
      margin-top: 1rem;
      padding: 1rem;
      background: linear-gradient(135deg, #ffffff, #f8fafc);
    }

    .thank-you-card p {
      margin: 0.45rem 0 0;
      color: var(--muted);
      line-height: 1.55;
    }

    .barcode {
      display: block;
      margin-top: 1rem;
      padding: 0.75rem;
      border: 1px solid #e2e8f0;
      border-radius: 8px;
      background: #fff;
    }

    .barcode img {
      display: block;
      width: 100%;
      max-width: 360px;
      height: 62px;
      object-fit: contain;
    }

    .totals-card {
      overflow: hidden;
    }

    .total-line {
      display: flex;
      justify-content: space-between;
      gap: 1rem;
      padding: 0.88rem 1rem;
      border-bottom: 1px solid var(--line);
      color: var(--muted);
    }

    .total-line strong {
      color: var(--navy);
      font-weight: 600;
    }

    .total-paid {
      padding: 1rem;
      color: #fff;
      background: linear-gradient(135deg, var(--green), #047857);
    }

    .total-paid span,
    .total-paid strong {
      display: block;
    }

    .total-paid span {
      opacity: 0.86;
      font-size: 0.9rem;
    }

    .total-paid strong {
      margin-top: 0.2rem;
      font-size: 2rem;
      font-weight: 600;
      line-height: 1.05;
    }

    .receipt-footer {
      display: block;
      padding: 1.2rem 2.2rem 2.2rem;
      color: var(--muted);
      font-size: 0.9rem;
      text-align: right;
    }

    @media (max-width: 820px) {
      .receipt-header,
      .receipt-lower,
      .payment-ribbon {
        grid-template-columns: 1fr;
      }

      .meta-strip {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }
    }

    @media (max-width: 560px) {
      .receipt-stage {
        width: min(100% - 16px, 1040px);
        margin: 0.75rem auto;
      }

      .receipt-header,
      .receipt-body,
      .receipt-footer {
        padding-right: 1rem;
        padding-left: 1rem;
      }

      .brand-block,
      .meta-strip,
      .verification-card {
        grid-template-columns: 1fr;
      }

      .items-table th:nth-child(3),
      .items-table td:nth-child(3) {
        display: none;
      }
    }

    @media print {
      @page {
        size: 80mm auto;
        margin: 4mm;
      }

      body {
        background: #fff;
        font-size: 10.5px;
      }

      .print-actions {
        display: none;
      }

      .receipt-stage {
        width: 100%;
        margin: 0;
      }

      .receipt {
        border: 0;
        border-radius: 0;
        box-shadow: none;
      }

      .receipt::before,
      .receipt::after {
        height: 4px;
      }

      .receipt-header,
      .receipt-body,
      .receipt-footer {
        padding-right: 0;
        padding-left: 0;
      }

      .receipt-header,
      .meta-strip,
      .payment-ribbon,
      .receipt-lower,
      .receipt-footer,
      .verification-card {
        grid-template-columns: 1fr;
      }

      .receipt-header {
        padding-top: 0.8rem;
      }

      .brand-block {
        grid-template-columns: auto 1fr;
      }

      .brand-mark {
        width: 46px;
        height: 46px;
      }

      .brand-name {
        font-size: 1.28rem;
      }

      .brand-meta,
      .meta-card strong,
      .payment-ribbon strong,
      .items-table td,
      .thank-you-card p,
      .receipt-footer {
        font-size: 0.72rem;
      }

      .receipt-status,
      .meta-card,
      .payment-ribbon,
      .items-card,
      .verification-card,
      .thank-you-card,
      .totals-card {
        break-inside: avoid;
        border-radius: 0;
        box-shadow: none;
      }

      .items-table th,
      .items-table td {
        padding: 0.55rem 0.35rem;
      }

      .items-head,
      .total-line,
      .total-paid {
        padding-right: 0.55rem;
        padding-left: 0.55rem;
      }

    }
  </style>
</head>
<body>
  <div class="print-actions">
    <button type="button" onclick="window.print()">Print Receipt</button>
    <a href="receipts.php">Back to Receipts</a>
  </div>

  <main class="receipt-stage">
    <section class="receipt">
      <header class="receipt-header">
        <div class="brand-block">
          <div class="brand-mark">
            <?php if ($receiptLogo !== '') { ?>
              <img src="<?php echo receipt_text($receiptLogo); ?>" alt="<?php echo receipt_text($company['company']); ?> logo">
            <?php } else { ?>
              <?php echo receipt_text($receiptInitials); ?>
            <?php } ?>
          </div>
          <div>
            <h1 class="brand-name"><?php echo receipt_text($company['company']); ?></h1>
            <p class="brand-meta">
              <?php echo receipt_text($company['address']); ?>, <?php echo receipt_text($company['city']); ?><br>
              <?php echo receipt_text($company['phone']); ?>
            </p>
          </div>
        </div>

        <aside class="receipt-status">
          <span class="paid-chip"><i></i> Payment confirmed</span>
          <div class="receipt-number">
            <span>Receipt number</span>
            <strong>#<?php echo receipt_text($invoice['invoice_no']); ?></strong>
          </div>
          <div class="grand-total-panel">
            <span>Total received</span>
            <strong>&#8373;<?php echo receipt_money($grandTotal); ?></strong>
          </div>
        </aside>
      </header>

      <div class="receipt-body">
        <section class="meta-strip">
          <div class="meta-card">
            <span>Customer</span>
            <strong><?php echo receipt_text($invoice['customer']); ?></strong>
          </div>
          <div class="meta-card">
            <span>Date issued</span>
            <strong><?php echo receipt_text($invoice['invoice_date']); ?></strong>
          </div>
          <div class="meta-card">
            <span>Sold by</span>
            <strong><?php echo receipt_text($salesPerson ?: 'Not recorded'); ?></strong>
          </div>
          <div class="meta-card">
            <span>Generated</span>
            <strong><?php echo receipt_text($generatedAt); ?></strong>
          </div>
        </section>

        <section class="payment-ribbon">
          <div class="payment-icon">&#8373;</div>
          <div>
            <span>Payment method</span>
            <strong><?php echo receipt_text($paymentMethod); ?></strong>
          </div>
          <div>
            <span>Reference</span>
            <strong><?php echo receipt_text($shortReference); ?></strong>
          </div>
        </section>

        <section class="items-card">
          <div class="items-head">
            <div>
              <h2>Purchased Items</h2>
              <p><?php echo receipt_text($lineCount); ?> line item<?php echo $lineCount === 1 ? '' : 's'; ?> on this receipt.</p>
            </div>
            <span class="item-count-pill"><?php echo receipt_text(receipt_qty($itemCount)); ?> total qty</span>
          </div>
          <table class="items-table">
            <thead>
              <tr>
                <th>Item</th>
                <th class="num">Unit</th>
                <th class="num">Qty</th>
                <th class="num">Total</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($products as $index => $product) { ?>
                <tr>
                  <td>
                    <div class="item-main">
                      <strong><?php echo receipt_text($product['name']); ?></strong>
                      <span>Line <?php echo receipt_text($index + 1); ?> of <?php echo receipt_text($lineCount); ?></span>
                    </div>
                  </td>
                  <td class="num">&#8373;<?php echo receipt_money($product['price']); ?></td>
                  <td class="num"><?php echo receipt_text(receipt_qty($product['qty'])); ?></td>
                  <td class="num item-total">&#8373;<?php echo receipt_money($product['total']); ?></td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </section>

        <section class="receipt-lower">
          <div>
            <div class="verification-card">
              <div>
                <span>Verification</span>
                <h3>Authentic paid receipt</h3>
                <div class="verification-code"><?php echo receipt_text($verificationCode); ?></div>
              </div>
              <div class="qr-mark" aria-label="Receipt verification pattern">
                <img src="<?php echo receipt_text($qrImageUrl); ?>" alt="QR code for receipt <?php echo receipt_text($invoice['invoice_no']); ?>">
              </div>
            </div>

            <div class="thank-you-card">
              <h3>Thank you for shopping with us.</h3>
              <p>This receipt confirms payment for the listed items. Keep it for returns, reconciliation, warranty checks, and customer support.</p>
              <div class="barcode">
                <img src="<?php echo receipt_text($barcodeImageUrl); ?>" alt="Barcode for receipt <?php echo receipt_text($invoice['invoice_no']); ?>">
              </div>
            </div>
          </div>

          <aside class="totals-card">
            <div class="total-line">
              <span>Subtotal</span>
              <strong>&#8373;<?php echo receipt_money($subTotal); ?></strong>
            </div>
            <div class="total-line">
              <span>Discount</span>
              <strong>&#8373;0.00</strong>
            </div>
            <div class="total-line">
              <span>Tax / VAT</span>
              <strong>&#8373;0.00</strong>
            </div>
            <div class="total-paid">
              <span>Total Paid</span>
              <strong>&#8373;<?php echo receipt_money($grandTotal); ?></strong>
            </div>
            <div class="total-line">
              <span>Balance</span>
              <strong>&#8373;0.00</strong>
            </div>
            <div class="total-line">
              <span>Paid at</span>
              <strong><?php echo receipt_text($paidAt ?: $invoice['invoice_date']); ?></strong>
            </div>
          </aside>
        </section>
      </div>

      <footer class="receipt-footer">
        <div>
          Generated by <?php echo receipt_text($company['company']); ?>. Receipt ID <?php echo receipt_text($invoice['invoice_no']); ?>.
        </div>
      </footer>
    </section>
  </main>
</body>
</html>
