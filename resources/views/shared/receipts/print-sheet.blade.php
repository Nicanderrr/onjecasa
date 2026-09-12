@php
  $receiptDefaultImage = asset('assets/admin/img/products/place.png');
  $receiptImage = fn ($item) => !empty($item->product_image)
      ? asset('assets/admin/img/products/' . $item->product_image)
      : $receiptDefaultImage;
  $systemName = \Illuminate\Support\Facades\DB::table('pos_settings')->where('key', 'system_name')->value('value') ?? 'NewPOS';
  $receiptLogoSetting = \Illuminate\Support\Facades\DB::table('pos_settings')->where('key', 'sidebar_logo')->value('value');
  $receiptLogoUrl = !empty($receiptLogoSetting)
      ? asset('assets/admin/img/settings/' . $receiptLogoSetting)
      : asset('assets/adminhmd/images/brand/logo/logo-icon.svg');
  $paymentMethod = $payment->method ?? 'N/A';
  $itemCount = $items->sum('qty');
  $receiptDate = \Illuminate\Support\Carbon::parse($order->created_at);
  $isCash = strcasecmp($paymentMethod, 'Cash') === 0;
  $isMobileMoney = strcasecmp($paymentMethod, 'Mobile Money') === 0;
  $isCard = strcasecmp($paymentMethod, 'Credit Card') === 0;
@endphp
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Receipt {{ $order->code }}</title>
  <style>
    * { box-sizing: border-box; }
    body { margin: 0; background: #dfe7ea; font-family: "Segoe UI", Arial, sans-serif; color: #111827; }
    .actions { width: 210mm; margin: 14px auto; text-align: right; }
    .btn { border: 0; border-radius: 8px; padding: 10px 14px; background: #166534; color: #fff; cursor: pointer; font-weight: 800; }
    .sheet { width: 210mm; min-height: 297mm; margin: 0 auto 20px; padding: 14mm; background: #f5f0e6; }
    .receipt-book { position: relative; width: 100%; padding-top: 13px; filter: drop-shadow(0 18px 28px rgba(15, 23, 42, .18)); }
    .receipt-book::before { content: ""; position: absolute; left: 22px; right: 22px; top: 0; height: 16px; border-radius: 8px 8px 0 0; background: linear-gradient(180deg, #f7e8c7, #efdbad); box-shadow: inset 0 -1px 0 rgba(15, 23, 42, .18); }
    .rings { position: absolute; z-index: 2; top: -7px; left: 35px; right: 35px; display: flex; justify-content: space-between; pointer-events: none; }
    .rings span { width: 13px; height: 29px; border: 3px solid #94a3b8; border-bottom: 0; border-radius: 999px 999px 0 0; background: linear-gradient(90deg, #e5e7eb, #fff 45%, #9ca3af); }
    .paper { position: relative; z-index: 1; padding: 18px; border: 1px solid #111827; border-radius: 4px; background: #fff; }
    .paper::after { content: ""; position: absolute; left: 18px; right: 18px; bottom: -7px; height: 7px; background: linear-gradient(135deg, transparent 8px, #fff 0) 0 0 / 16px 100%; filter: drop-shadow(0 1px 0 #111827); }
    .topline { display: grid; grid-template-columns: 120px minmax(0, 1fr) 145px; gap: 14px; align-items: start; margin-bottom: 12px; }
    .logo-box { min-height: 86px; display: grid; place-items: center; border: 2px solid #111827; background: #fff; }
    .logo-box img { width: min(94px, 90%); height: 66px; object-fit: contain; }
    .company { text-align: center; }
    .company h1 { margin: 0; color: #111827; font-size: 25px; line-height: 1.1; font-weight: 900; text-transform: uppercase; }
    .company p { margin: 4px 0 0; color: #4b5563; font-size: 12px; }
    .number { text-align: right; }
    .number span { display: block; font-size: 10px; font-weight: 900; letter-spacing: .08em; text-transform: uppercase; }
    .number strong { display: block; margin-top: 3px; color: #dc2626; font-size: 25px; font-weight: 900; letter-spacing: .05em; }
    .form-grid { display: grid; grid-template-columns: minmax(0, 1fr) 170px; border: 1px solid #111827; border-bottom: 0; }
    .field { display: grid; grid-template-columns: 92px minmax(0, 1fr); min-height: 39px; border-bottom: 1px solid #111827; }
    .field:nth-child(odd) { border-right: 1px solid #111827; }
    .label { padding: 8px 10px; font-size: 11px; font-weight: 900; text-transform: uppercase; }
    .value { padding: 8px 10px; border-left: 1px solid #111827; font-weight: 700; overflow-wrap: anywhere; }
    .payment-strip { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)) minmax(0, 1.4fr); border: 1px solid #111827; border-top: 0; }
    .check { display: flex; align-items: center; gap: 7px; min-height: 39px; padding: 8px 10px; border-right: 1px solid #111827; font-size: 11px; font-weight: 900; text-transform: uppercase; }
    .check:last-child { border-right: 0; }
    .box { width: 14px; height: 14px; display: inline-grid; place-items: center; border: 1px solid #111827; font-size: 11px; line-height: 1; }
    .ref { min-width: 0; overflow-wrap: anywhere; text-transform: none; }
    table { width: 100%; margin-top: 12px; border-collapse: collapse; border: 1px solid #111827; }
    th { padding: 8px; border-right: 1px solid #111827; border-bottom: 2px solid #111827; font-size: 11px; font-weight: 900; text-align: left; text-transform: uppercase; }
    th:last-child, td:last-child { border-right: 0; }
    td { height: 51px; padding: 7px 8px; border-right: 1px solid #111827; border-bottom: 1px solid #111827; vertical-align: middle; }
    tbody tr:nth-child(even) td { background: #fafafa; }
    .product { display: flex; align-items: center; gap: 9px; min-width: 0; }
    .product img { width: 38px; height: 38px; flex: 0 0 38px; border: 1px solid #111827; border-radius: 4px; object-fit: cover; background: #fff; }
    .product-name { display: block; color: #111827; font-weight: 800; }
    .product-code { display: block; color: #6b7280; font-size: 10px; }
    .num { text-align: right; white-space: nowrap; }
    .bottom { display: grid; grid-template-columns: minmax(0, 1fr) 230px; border: 1px solid #111827; border-top: 0; }
    .signature { display: grid; align-content: end; gap: 12px; min-height: 108px; padding: 14px; border-right: 1px solid #111827; }
    .signature p { margin: 0; color: #4b5563; font-size: 12px; line-height: 1.45; }
    .sign-line { border-top: 1px solid #111827; padding-top: 5px; font-size: 11px; font-weight: 900; text-transform: uppercase; }
    .totals { display: grid; }
    .total-row { display: grid; grid-template-columns: minmax(0, 1fr) 112px; min-height: 38px; border-bottom: 1px solid #111827; }
    .total-row:last-child { border-bottom: 0; }
    .total-row span, .total-row strong { padding: 8px 10px; }
    .total-row span { border-right: 1px solid #111827; font-size: 11px; font-weight: 900; text-transform: uppercase; }
    .total-row strong { text-align: right; }
    .total-row.grand { color: #dc2626; font-size: 16px; }
    @media print {
      body { background: #fff; }
      .actions { display: none; }
      .sheet { width: auto; min-height: auto; margin: 0; padding: 0; background: #fff; }
      .receipt-book { filter: none; }
      .paper, .logo-box, .form-grid, .field, .payment-strip, .check, table, th, td, .bottom, .signature, .total-row, .total-row span { border-color: #111827 !important; }
      @page { size: A4; margin: 8mm; }
    }
  </style>
</head>
<body>
  <div class="actions">
    <button class="btn" onclick="window.print()">Print Receipt</button>
  </div>

  <main class="sheet">
    <section class="receipt-book" aria-label="Receipt {{ $order->code }}">
      <div class="rings" aria-hidden="true">
        @for($i = 0; $i < 12; $i++)
          <span></span>
        @endfor
      </div>

      <div class="paper">
        <header class="topline">
          <div class="logo-box"><img src="{{ $receiptLogoUrl }}" alt="{{ $systemName }} logo"></div>
          <div class="company">
            <h1>{{ $systemName }}</h1>
            <p>Payment receipt and sales transaction record</p>
            <p>Issued {{ $receiptDate->format('d M Y, h:i A') }}</p>
          </div>
          <div class="number">
            <span>Receipt No.</span>
            <strong>{{ str_pad((string) $order->id, 5, '0', STR_PAD_LEFT) }}</strong>
          </div>
        </header>

        <section class="form-grid">
          <div class="field"><span class="label">Name</span><span class="value">{{ $order->customer_name }}</span></div>
          <div class="field"><span class="label">Date</span><span class="value">{{ $receiptDate->format('d/m/Y') }}</span></div>
          <div class="field"><span class="label">Order No.</span><span class="value">{{ $order->code }}</span></div>
          <div class="field"><span class="label">Items</span><span class="value">{{ number_format((int) $itemCount) }}</span></div>
        </section>

        <section class="payment-strip">
          <div class="check"><span class="box">{{ $isCash ? 'X' : '' }}</span> Cash</div>
          <div class="check"><span class="box">{{ $isMobileMoney ? 'X' : '' }}</span> Mobile Money</div>
          <div class="check"><span class="box">{{ $isCard ? 'X' : '' }}</span> Credit Card</div>
          <div class="check ref">Ref: {{ $isMobileMoney && !empty($payment->paystack_reference) ? $payment->paystack_reference : ($paymentMethod ?: 'N/A') }}</div>
        </section>

        <table>
          <thead>
            <tr>
              <th style="width: 72px;">Qty</th>
              <th>Description</th>
              <th style="width: 115px;" class="num">Price</th>
              <th style="width: 130px;" class="num">Amount</th>
            </tr>
          </thead>
          <tbody>
            @foreach($items as $i)
              <tr>
                <td>{{ $i->qty }}</td>
                <td>
                  <div class="product">
                    <img src="{{ $receiptImage($i) }}" alt="{{ $i->product_name }}" onerror="this.onerror=null;this.src='{{ $receiptDefaultImage }}';">
                    <div>
                      <span class="product-name">{{ $i->product_name }}</span>
                      <span class="product-code">Line item #{{ $loop->iteration }}</span>
                    </div>
                  </div>
                </td>
                <td class="num">{{ number_format($i->price, 2) }}</td>
                <td class="num"><strong>{{ number_format($i->total, 2) }}</strong></td>
              </tr>
            @endforeach
            @for($i = $items->count(); $i < 8; $i++)
              <tr><td>&nbsp;</td><td></td><td></td><td></td></tr>
            @endfor
          </tbody>
        </table>

        <footer class="bottom">
          <div class="signature">
            <p>Thank you for your purchase. Keep this receipt for payment reference and order verification.</p>
            <div class="sign-line">Received by / cashier signature</div>
          </div>
          <div class="totals">
            <div class="total-row"><span>Subtotal</span><strong>{{ number_format($order->grand_total, 2) }}</strong></div>
            <div class="total-row"><span>Tax</span><strong>0.00</strong></div>
            <div class="total-row grand"><span>Total</span><strong>{{ number_format($order->grand_total, 2) }}</strong></div>
          </div>
        </footer>
      </div>
    </section>
  </main>

  @if(request()->boolean('autoprint'))
    <script>window.addEventListener('load', function(){ window.print(); });</script>
  @endif
</body>
</html>
