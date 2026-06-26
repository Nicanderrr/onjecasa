<?php
$receiptId = $_GET['id'] ?? '';
$receiptId = trim($receiptId);

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

$company = [
    'company' => 'Command Center POS',
    'address' => '172 Banana Street',
    'city' => 'Accra',
    'phone' => '(+233) 337-337-3069',
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

$stmt = $mysqli->prepare('SELECT company, address, city, phone FROM company_info LIMIT 1');
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
$receiptInitials = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $company['company']), 0, 2)) ?: 'POS';
$verificationSeed = $invoice['invoice_no'] . '|' . $grandTotal . '|' . $paymentReference;
$verificationCode = strtoupper(substr(hash('sha256', $verificationSeed), 0, 12));
$qrHash = hash('sha256', $verificationSeed . '|qr');
$qrCells = [];
for ($i = 0; $i < 81; $i++) {
    $qrCells[] = hexdec($qrHash[$i % strlen($qrHash)]) % 2 === 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Receipt #<?php echo receipt_text($invoice['invoice_no']); ?></title>
  <style>
    :root {
      --ink: #182230;
      --muted: #667085;
      --line: #e7edf5;
      --paper: #ffffff;
      --soft: #f6f8fb;
      --brand: #2563eb;
      --brand-dark: #163b8f;
      --green: #10b981;
      --gold: #f59e0b;
      --danger: #ef4444;
    }

    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      color: var(--ink);
      background:
        radial-gradient(circle at top left, rgba(37, 99, 235, 0.18), transparent 34rem),
        linear-gradient(135deg, #eef4ff 0%, #f8fafc 52%, #edfdf6 100%);
      font-family: Inter, "Segoe UI", Arial, sans-serif;
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
    }

    .print-actions {
      position: sticky;
      top: 0;
      z-index: 10;
      display: flex;
      justify-content: center;
      gap: 0.75rem;
      padding: 1rem;
      backdrop-filter: blur(14px);
      background: rgba(246, 248, 251, 0.78);
      border-bottom: 1px solid rgba(148, 163, 184, 0.25);
    }

    .print-actions button,
    .print-actions a {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-height: 42px;
      padding: 0 1.2rem;
      color: #fff;
      background: linear-gradient(135deg, var(--brand), var(--brand-dark));
      border: 0;
      border-radius: 8px;
      box-shadow: 0 14px 28px rgba(37, 99, 235, 0.25);
      font-size: 0.92rem;
      font-weight: 800;
      text-decoration: none;
      cursor: pointer;
    }

    .print-actions a {
      color: var(--ink);
      background: #fff;
      box-shadow: 0 12px 24px rgba(15, 23, 42, 0.08);
    }

    .receipt-shell {
      width: min(960px, calc(100% - 32px));
      margin: 2rem auto;
      padding: 1rem;
    }

    .receipt {
      position: relative;
      overflow: hidden;
      background: var(--paper);
      border: 1px solid rgba(148, 163, 184, 0.2);
      border-radius: 18px;
      box-shadow: 0 30px 80px rgba(15, 23, 42, 0.16);
    }

    .receipt::before {
      content: "PAID";
      position: absolute;
      right: -1.6rem;
      top: 7.6rem;
      z-index: 1;
      color: rgba(16, 185, 129, 0.08);
      font-size: 8rem;
      font-weight: 900;
      letter-spacing: 0.04em;
      transform: rotate(-14deg);
      pointer-events: none;
    }

    .receipt-hero {
      position: relative;
      display: grid;
      grid-template-columns: 1fr auto;
      gap: 2rem;
      padding: 2rem;
      color: #fff;
      background:
        linear-gradient(135deg, rgba(22, 59, 143, 0.95), rgba(37, 99, 235, 0.92)),
        repeating-linear-gradient(45deg, rgba(255,255,255,0.08) 0 1px, transparent 1px 12px);
    }

    .brand-row {
      display: flex;
      align-items: center;
      gap: 0.9rem;
    }

    .brand-mark {
      display: grid;
      width: 58px;
      height: 58px;
      place-items: center;
      border: 1px solid rgba(255, 255, 255, 0.45);
      border-radius: 16px;
      background: rgba(255, 255, 255, 0.16);
      box-shadow: inset 0 1px 0 rgba(255,255,255,0.24);
      font-weight: 900;
      letter-spacing: 0.04em;
    }

    .brand-name {
      margin: 0;
      font-size: clamp(1.6rem, 3vw, 2.35rem);
      line-height: 1.05;
      letter-spacing: 0;
    }

    .brand-meta {
      margin: 0.55rem 0 0;
      max-width: 35rem;
      color: rgba(255, 255, 255, 0.82);
      font-size: 0.95rem;
      line-height: 1.7;
    }

    .receipt-badge {
      align-self: start;
      min-width: 190px;
      padding: 1rem;
      border: 1px solid rgba(255, 255, 255, 0.35);
      border-radius: 14px;
      background: rgba(255, 255, 255, 0.15);
      text-align: right;
    }

    .receipt-badge span {
      display: inline-flex;
      margin-bottom: 0.65rem;
      padding: 0.35rem 0.65rem;
      border-radius: 999px;
      background: rgba(16, 185, 129, 0.18);
      color: #d1fae5;
      font-size: 0.72rem;
      font-weight: 900;
      letter-spacing: 0.08em;
      text-transform: uppercase;
    }

    .receipt-badge strong {
      display: block;
      font-size: 1.05rem;
      word-break: break-word;
    }

    .receipt-body {
      position: relative;
      z-index: 2;
      padding: 2rem;
    }

    .detail-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 0.8rem;
      margin-bottom: 1.5rem;
    }

    .detail-card {
      min-height: 92px;
      padding: 1rem;
      border: 1px solid var(--line);
      border-radius: 12px;
      background: var(--soft);
    }

    .detail-card span {
      display: block;
      margin-bottom: 0.35rem;
      color: var(--muted);
      font-size: 0.72rem;
      font-weight: 900;
      letter-spacing: 0.08em;
      text-transform: uppercase;
    }

    .detail-card strong {
      display: block;
      font-size: 0.98rem;
      line-height: 1.35;
      word-break: break-word;
    }

    .items-table {
      width: 100%;
      overflow: hidden;
      border-collapse: separate;
      border-spacing: 0;
      border: 1px solid var(--line);
      border-radius: 14px;
    }

    .items-table th {
      padding: 0.85rem 0.95rem;
      color: #475467;
      background: #f1f5f9;
      border-bottom: 1px solid var(--line);
      font-size: 0.72rem;
      font-weight: 900;
      letter-spacing: 0.08em;
      text-align: left;
      text-transform: uppercase;
    }

    .items-table td {
      padding: 1rem 0.95rem;
      border-bottom: 1px solid var(--line);
      font-size: 0.93rem;
      vertical-align: top;
    }

    .items-table tr:last-child td {
      border-bottom: 0;
    }

    .items-table .num {
      text-align: right;
      white-space: nowrap;
    }

    .product-name {
      font-weight: 800;
    }

    .receipt-bottom {
      display: grid;
      grid-template-columns: minmax(0, 1fr) 330px;
      gap: 1.25rem;
      margin-top: 1.5rem;
      align-items: start;
    }

    .note-card,
    .summary-card,
    .verify-card {
      border: 1px solid var(--line);
      border-radius: 14px;
      background: #fff;
    }

    .note-card {
      padding: 1.2rem;
    }

    .note-card h4 {
      margin: 0 0 0.5rem;
      font-size: 0.9rem;
    }

    .note-card p {
      margin: 0;
      color: var(--muted);
      font-size: 0.9rem;
      line-height: 1.65;
    }

    .barcode {
      display: flex;
      align-items: end;
      gap: 3px;
      height: 46px;
      margin-top: 1rem;
    }

    .barcode i {
      display: block;
      width: 5px;
      background: #111827;
      border-radius: 2px 2px 0 0;
    }

    .barcode i:nth-child(2n) { height: 68%; }
    .barcode i:nth-child(3n) { height: 84%; }
    .barcode i:nth-child(4n) { height: 52%; }
    .barcode i:nth-child(5n) { height: 100%; }

    .verify-grid {
      display: grid;
      grid-template-columns: 1fr auto;
      gap: 1rem;
      align-items: center;
      margin-top: 1rem;
      padding: 1rem;
      border: 1px dashed #cbd5e1;
      border-radius: 12px;
      background: #f8fafc;
    }

    .verify-grid span {
      display: block;
      color: var(--muted);
      font-size: 0.72rem;
      font-weight: 900;
      letter-spacing: 0.08em;
      text-transform: uppercase;
    }

    .verify-grid strong {
      display: block;
      margin-top: 0.25rem;
      color: var(--ink);
      font-size: 0.95rem;
      word-break: break-word;
    }

    .qr-mark {
      display: grid;
      grid-template-columns: repeat(9, 6px);
      grid-template-rows: repeat(9, 6px);
      gap: 2px;
      padding: 0.45rem;
      border: 1px solid #d7deea;
      border-radius: 10px;
      background: #fff;
    }

    .qr-mark i {
      display: block;
      width: 6px;
      height: 6px;
      border-radius: 1px;
      background: #e2e8f0;
    }

    .qr-mark i.is-on {
      background: #111827;
    }

    .summary-card {
      overflow: hidden;
    }

    .summary-line {
      display: flex;
      justify-content: space-between;
      gap: 1rem;
      padding: 0.9rem 1rem;
      border-bottom: 1px solid var(--line);
      color: var(--muted);
      font-weight: 700;
    }

    .summary-line strong {
      color: var(--ink);
    }

    .summary-total {
      display: flex;
      justify-content: space-between;
      gap: 1rem;
      padding: 1.1rem 1rem;
      color: #fff;
      background: linear-gradient(135deg, var(--brand-dark), var(--brand));
      font-weight: 900;
    }

    .summary-total strong {
      font-size: 1.35rem;
    }

    .payment-pill {
      display: inline-flex;
      align-items: center;
      min-height: 28px;
      padding: 0 0.65rem;
      border-radius: 999px;
      color: #065f46;
      background: #d1fae5;
      font-size: 0.78rem;
      font-weight: 900;
    }

    .receipt-footer {
      display: grid;
      grid-template-columns: 1fr auto;
      gap: 1rem;
      align-items: center;
      padding: 1.35rem 2rem;
      border-top: 1px dashed #cbd5e1;
      background: #fbfdff;
      color: var(--muted);
      font-size: 0.88rem;
    }

    .signature {
      min-width: 220px;
      padding-top: 0.6rem;
      border-top: 1px solid #98a2b3;
      color: var(--ink);
      font-weight: 800;
      text-align: center;
    }

    @media (max-width: 760px) {
      .receipt-hero,
      .receipt-bottom,
      .receipt-footer {
        grid-template-columns: 1fr;
      }

      .receipt-badge {
        text-align: left;
      }

      .detail-grid {
        grid-template-columns: repeat(2, 1fr);
      }

      .items-table {
        font-size: 0.85rem;
      }
    }

    @media print {
      @page {
        size: 80mm auto;
        margin: 4mm;
      }

      body {
        background: #fff;
        font-size: 11px;
      }

      .print-actions {
        display: none;
      }

      .receipt-shell {
        width: 100%;
        margin: 0;
        padding: 0;
      }

      .receipt {
        border: 0;
        border-radius: 0;
        box-shadow: none;
      }

      .receipt-hero {
        grid-template-columns: 1fr;
        gap: 1rem;
        padding: 1rem;
        border-radius: 0;
      }

      .brand-mark {
        width: 42px;
        height: 42px;
        border-radius: 10px;
      }

      .brand-name {
        font-size: 1.15rem;
      }

      .brand-meta,
      .receipt-badge,
      .detail-card strong,
      .items-table td,
      .note-card p,
      .receipt-footer {
        font-size: 0.72rem;
      }

      .receipt-body {
        padding: 1rem 0;
      }

      .detail-grid,
      .receipt-bottom,
      .receipt-footer {
        grid-template-columns: 1fr;
      }

      .detail-card,
      .note-card,
      .summary-card {
        border-radius: 0;
      }

      .items-table {
        border-right: 0;
        border-left: 0;
        border-radius: 0;
      }

      .items-table tr,
      .detail-card,
      .summary-card,
      .note-card {
        break-inside: avoid;
      }
    }
  </style>
</head>
<body>
  <div class="print-actions">
    <button type="button" onclick="window.print()">Print Receipt</button>
    <a href="receipts.php">Back to Receipts</a>
  </div>

  <main class="receipt-shell">
    <section class="receipt">
      <header class="receipt-hero">
        <div>
          <div class="brand-row">
            <div class="brand-mark"><?php echo receipt_text($receiptInitials); ?></div>
            <div>
              <h1 class="brand-name"><?php echo receipt_text($company['company']); ?></h1>
              <p class="brand-meta">
                <?php echo receipt_text($company['address']); ?>, <?php echo receipt_text($company['city']); ?><br>
                <?php echo receipt_text($company['phone']); ?>
              </p>
            </div>
          </div>
        </div>
        <aside class="receipt-badge">
          <span>Paid receipt</span>
          <strong>#<?php echo receipt_text($invoice['invoice_no']); ?></strong>
        </aside>
      </header>

      <div class="receipt-body">
        <section class="detail-grid">
          <div class="detail-card">
            <span>Customer</span>
            <strong><?php echo receipt_text($invoice['customer']); ?></strong>
          </div>
          <div class="detail-card">
            <span>Date issued</span>
            <strong><?php echo receipt_text($invoice['invoice_date']); ?></strong>
          </div>
          <div class="detail-card">
            <span>Sales person</span>
            <strong><?php echo receipt_text($salesPerson ?: 'Not recorded'); ?></strong>
          </div>
          <div class="detail-card">
            <span>Payment</span>
            <strong><span class="payment-pill"><?php echo receipt_text($paymentMethod); ?></span></strong>
          </div>
          <div class="detail-card">
            <span>Payment Ref</span>
            <strong><?php echo receipt_text($paymentReference); ?></strong>
          </div>
          <div class="detail-card">
            <span>Paid At</span>
            <strong><?php echo receipt_text($paidAt ?: $invoice['invoice_date']); ?></strong>
          </div>
        </section>

        <table class="items-table">
          <thead>
            <tr>
              <th>Item</th>
              <th class="num">Unit Price</th>
              <th class="num">Qty</th>
              <th class="num">Line Total</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($products as $product) { ?>
              <tr>
                <td><span class="product-name"><?php echo receipt_text($product['name']); ?></span></td>
                <td class="num">&#8373;<?php echo receipt_money($product['price']); ?></td>
                <td class="num"><?php echo receipt_text(rtrim(rtrim(number_format($product['qty'], 2), '0'), '.')); ?></td>
                <td class="num">&#8373;<?php echo receipt_money($product['total']); ?></td>
              </tr>
            <?php } ?>
          </tbody>
        </table>

        <section class="receipt-bottom">
          <div class="note-card">
            <h4>Thank you for your purchase.</h4>
            <p>This receipt confirms that payment was received for the listed items. Keep it for returns, reconciliation, and customer service reference.</p>
            <div class="barcode" aria-hidden="true">
              <?php for ($i = 0; $i < 34; $i++) { ?><i></i><?php } ?>
            </div>
            <div class="verify-grid">
              <div>
                <span>Verification Code</span>
                <strong><?php echo receipt_text($verificationCode); ?></strong>
              </div>
              <div class="qr-mark" aria-label="Receipt verification code">
                <?php foreach ($qrCells as $isOn) { ?><i class="<?php echo $isOn ? 'is-on' : ''; ?>"></i><?php } ?>
              </div>
            </div>
          </div>

          <div class="summary-card">
            <div class="summary-line">
              <span>Items</span>
              <strong><?php echo receipt_text(rtrim(rtrim(number_format($itemCount, 2), '0'), '.')); ?></strong>
            </div>
            <div class="summary-line">
              <span>Subtotal</span>
              <strong>&#8373;<?php echo receipt_money($subTotal); ?></strong>
            </div>
            <div class="summary-line">
              <span>Discount</span>
              <strong>&#8373;0.00</strong>
            </div>
            <div class="summary-line">
              <span>Tax / VAT</span>
              <strong>&#8373;0.00</strong>
            </div>
            <div class="summary-total">
              <span>Total Paid</span>
              <strong>&#8373;<?php echo receipt_money($grandTotal); ?></strong>
            </div>
            <div class="summary-line">
              <span>Balance</span>
              <strong>&#8373;0.00</strong>
            </div>
          </div>
        </section>
      </div>

      <footer class="receipt-footer">
        <div>
          Generated by Command Center POS. Receipt ID <?php echo receipt_text($invoice['invoice_no']); ?>. Developed by Nicander and Benjamin.
        </div>
        <div class="signature">Authorized Signature</div>
      </footer>
    </section>
  </main>
</body>
</html>
