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

<style>
  .receipt-studio {
    display: grid;
    gap: 1rem;
  }

  .receipt-actions {
    display: flex;
    flex-wrap: wrap;
    justify-content: flex-end;
    gap: .65rem;
  }

  .receipt-book-stage {
    display: grid;
    place-items: start center;
    padding: 1.5rem;
    border: 1px solid var(--admin-border);
    border-radius: 8px;
    background:
      linear-gradient(135deg, rgba(15, 23, 42, .04), rgba(22, 163, 74, .06)),
      var(--admin-surface-soft);
    overflow-x: auto;
  }

  .receipt-book {
    position: relative;
    width: min(860px, 100%);
    padding-top: 1.35rem;
    filter: drop-shadow(0 24px 38px rgba(15, 23, 42, .15));
  }

  .receipt-book::before {
    content: "";
    position: absolute;
    left: 2.25rem;
    right: 2.25rem;
    top: .3rem;
    height: 1.35rem;
    border-radius: 8px 8px 0 0;
    background: linear-gradient(180deg, #f7e8c7, #efdbad);
    box-shadow: inset 0 -1px 0 rgba(15, 23, 42, .12);
  }

  .receipt-rings {
    position: absolute;
    z-index: 2;
    top: 0;
    left: 3.2rem;
    right: 3.2rem;
    display: flex;
    justify-content: space-between;
    pointer-events: none;
  }

  .receipt-rings span {
    width: 16px;
    height: 34px;
    border: 3px solid #94a3b8;
    border-bottom: 0;
    border-radius: 999px 999px 0 0;
    background: linear-gradient(90deg, #e5e7eb, #f8fafc 45%, #9ca3af);
  }

  .receipt-paper {
    position: relative;
    z-index: 1;
    padding: 1.25rem;
    border: 1px solid #1f2937;
    border-radius: 4px;
    background:
      linear-gradient(#ffffff, #ffffff),
      repeating-linear-gradient(0deg, transparent 0 31px, rgba(15, 23, 42, .08) 32px);
    color: #111827;
  }

  .receipt-paper::after {
    content: "";
    position: absolute;
    left: 1.25rem;
    right: 1.25rem;
    bottom: -.55rem;
    height: .55rem;
    background:
      linear-gradient(135deg, transparent 8px, #fff 0) 0 0 / 16px 100%;
    filter: drop-shadow(0 1px 0 #1f2937);
  }

  .receipt-topline {
    display: grid;
    grid-template-columns: 130px minmax(0, 1fr) 150px;
    gap: 1rem;
    align-items: start;
    margin-bottom: .85rem;
  }

  .receipt-logo-box {
    min-height: 92px;
    display: grid;
    place-items: center;
    border: 2px solid #111827;
    color: #111827;
    font-weight: 900;
    line-height: 1.05;
    text-align: center;
    text-transform: uppercase;
  }

  .receipt-logo-box img {
    width: min(96px, 90%);
    height: 72px;
    object-fit: contain;
  }

  .receipt-company {
    text-align: center;
  }

  .receipt-company h2 {
    margin: 0;
    color: #111827;
    font-size: 1.55rem;
    font-weight: 900;
    letter-spacing: 0;
    text-transform: uppercase;
  }

  .receipt-company p {
    margin: .2rem 0 0;
    color: #4b5563;
    font-size: .86rem;
    line-height: 1.35;
  }

  .receipt-number {
    text-align: right;
  }

  .receipt-number span {
    display: block;
    color: #111827;
    font-size: .72rem;
    font-weight: 900;
    letter-spacing: .08em;
    text-transform: uppercase;
  }

  .receipt-number strong {
    display: block;
    margin-top: .2rem;
    color: #dc2626;
    font-size: 1.6rem;
    font-weight: 900;
    letter-spacing: .05em;
  }

  .receipt-form-grid {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 180px;
    border: 1px solid #111827;
    border-bottom: 0;
  }

  .receipt-field {
    display: grid;
    grid-template-columns: 95px minmax(0, 1fr);
    min-height: 42px;
    border-bottom: 1px solid #111827;
  }

  .receipt-field:nth-child(odd) {
    border-right: 1px solid #111827;
  }

  .receipt-field-label {
    padding: .55rem .65rem;
    color: #111827;
    font-size: .78rem;
    font-weight: 900;
    text-transform: uppercase;
  }

  .receipt-field-value {
    padding: .55rem .65rem;
    border-left: 1px solid #111827;
    font-weight: 700;
    overflow-wrap: anywhere;
  }

  .receipt-payment-strip {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr)) minmax(0, 1.5fr);
    border: 1px solid #111827;
    border-top: 0;
  }

  .receipt-check {
    display: flex;
    align-items: center;
    gap: .45rem;
    min-height: 42px;
    padding: .55rem .65rem;
    border-right: 1px solid #111827;
    color: #111827;
    font-size: .78rem;
    font-weight: 900;
    text-transform: uppercase;
  }

  .receipt-check:last-child {
    border-right: 0;
  }

  .receipt-box {
    width: 14px;
    height: 14px;
    display: inline-grid;
    place-items: center;
    border: 1px solid #111827;
    font-size: .75rem;
    line-height: 1;
  }

  .receipt-reference {
    min-width: 0;
    overflow-wrap: anywhere;
    text-transform: none;
  }

  .receipt-lines {
    width: 100%;
    margin: .85rem 0 0;
    border-collapse: collapse;
    border: 1px solid #111827;
  }

  .receipt-lines th {
    padding: .55rem .5rem;
    border-right: 1px solid #111827;
    border-bottom: 2px solid #111827;
    color: #111827;
    font-size: .78rem;
    font-weight: 900;
    text-align: left;
    text-transform: uppercase;
  }

  .receipt-lines th:last-child,
  .receipt-lines td:last-child {
    border-right: 0;
  }

  .receipt-lines td {
    height: 58px;
    padding: .45rem .5rem;
    border-right: 1px solid #111827;
    border-bottom: 1px solid #111827;
    vertical-align: middle;
  }

  .receipt-lines tbody tr:nth-child(even) td {
    background: #fafafa;
  }

  .receipt-product {
    display: flex;
    align-items: center;
    gap: .65rem;
    min-width: 0;
  }

  .receipt-product-img {
    width: 42px;
    height: 42px;
    flex: 0 0 42px;
    border: 1px solid #111827;
    border-radius: 4px;
    object-fit: cover;
    background: #fff;
  }

  .receipt-product-name {
    display: block;
    color: #111827;
    font-weight: 800;
  }

  .receipt-product-code {
    display: block;
    color: #6b7280;
    font-size: .72rem;
  }

  .receipt-num {
    text-align: right;
    white-space: nowrap;
  }

  .receipt-bottom {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 240px;
    border: 1px solid #111827;
    border-top: 0;
  }

  .receipt-signature {
    display: grid;
    align-content: end;
    gap: .7rem;
    padding: 1rem;
    min-height: 122px;
    border-right: 1px solid #111827;
  }

  .receipt-signature p {
    margin: 0;
    color: #4b5563;
    font-size: .86rem;
  }

  .receipt-sign-line {
    border-top: 1px solid #111827;
    padding-top: .35rem;
    color: #111827;
    font-size: .78rem;
    font-weight: 900;
    text-transform: uppercase;
  }

  .receipt-total-box {
    display: grid;
  }

  .receipt-total-row {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 120px;
    min-height: 40px;
    border-bottom: 1px solid #111827;
  }

  .receipt-total-row:last-child {
    border-bottom: 0;
  }

  .receipt-total-row span,
  .receipt-total-row strong {
    padding: .55rem .65rem;
  }

  .receipt-total-row span {
    border-right: 1px solid #111827;
    color: #111827;
    font-size: .78rem;
    font-weight: 900;
    text-transform: uppercase;
  }

  .receipt-total-row strong {
    text-align: right;
  }

  .receipt-total-row.is-grand {
    color: #dc2626;
    font-size: 1.05rem;
  }

  @media (max-width: 991.98px) {
    .receipt-book-stage {
      padding: .75rem;
    }

    .receipt-book {
      min-width: 760px;
    }
  }

  @media (max-width: 575.98px) {
    .receipt-actions {
      justify-content: stretch;
    }

    .receipt-actions .btn {
      flex: 1 1 auto;
    }
  }
</style>

<div class="receipt-studio">
  <div class="receipt-actions">
    <a href="{{ $printUrl }}" target="_blank" class="btn btn-success">
      <i class="bi bi-printer"></i> Print Receipt
    </a>
  </div>

  <div class="receipt-book-stage">
    <section class="receipt-book" aria-label="Receipt {{ $order->code }}">
      <div class="receipt-rings" aria-hidden="true">
        @for($i = 0; $i < 12; $i++)
          <span></span>
        @endfor
      </div>

      <div class="receipt-paper">
        <header class="receipt-topline">
          <div class="receipt-logo-box">
            <img src="{{ $receiptLogoUrl }}" alt="{{ $systemName }} logo">
          </div>
          <div class="receipt-company">
            <h2>{{ $systemName }}</h2>
            <p>Payment receipt and sales transaction record</p>
            <p>Issued {{ $receiptDate->format('d M Y, h:i A') }}</p>
          </div>
          <div class="receipt-number">
            <span>Receipt No.</span>
            <strong>{{ str_pad((string) $order->id, 5, '0', STR_PAD_LEFT) }}</strong>
          </div>
        </header>

        <section class="receipt-form-grid">
          <div class="receipt-field">
            <span class="receipt-field-label">Name</span>
            <span class="receipt-field-value">{{ $order->customer_name }}</span>
          </div>
          <div class="receipt-field">
            <span class="receipt-field-label">Date</span>
            <span class="receipt-field-value">{{ $receiptDate->format('d/m/Y') }}</span>
          </div>
          @if(!empty($order->customer_whatsapp))
            <div class="receipt-field">
              <span class="receipt-field-label">Phone</span>
              <span class="receipt-field-value">{{ $order->customer_whatsapp }}</span>
            </div>
          @endif
          @if(!empty($order->customer_email))
            <div class="receipt-field">
              <span class="receipt-field-label">Email</span>
              <span class="receipt-field-value">{{ $order->customer_email }}</span>
            </div>
          @endif
          <div class="receipt-field">
            <span class="receipt-field-label">Order No.</span>
            <span class="receipt-field-value">{{ $order->code }}</span>
          </div>
          <div class="receipt-field">
            <span class="receipt-field-label">Items</span>
            <span class="receipt-field-value">{{ number_format((int) $itemCount) }}</span>
          </div>
        </section>

        <section class="receipt-payment-strip">
          <div class="receipt-check"><span class="receipt-box">{{ $isCash ? 'X' : '' }}</span> Cash</div>
          <div class="receipt-check"><span class="receipt-box">{{ $isMobileMoney ? 'X' : '' }}</span> Mobile Money</div>
          <div class="receipt-check"><span class="receipt-box">{{ $isCard ? 'X' : '' }}</span> Credit Card</div>
          <div class="receipt-check receipt-reference">
            Ref: {{ $isMobileMoney && !empty($payment->paystack_reference) ? $payment->paystack_reference : ($paymentMethod ?: 'N/A') }}
          </div>
        </section>

        <table class="receipt-lines">
          <thead>
            <tr>
              <th style="width: 84px;">Qty</th>
              <th>Description</th>
              <th style="width: 130px;" class="receipt-num">Price</th>
              <th style="width: 150px;" class="receipt-num">Amount</th>
            </tr>
          </thead>
          <tbody>
            @foreach($items as $i)
              <tr>
                <td>{{ $i->qty }}</td>
                <td>
                  <div class="receipt-product">
                    <img class="receipt-product-img" src="{{ $receiptImage($i) }}" alt="{{ $i->product_name }}" onerror="this.onerror=null;this.src='{{ $receiptDefaultImage }}';">
                    <div>
                      <span class="receipt-product-name">{{ $i->product_name }}</span>
                      <span class="receipt-product-code">Line item #{{ $loop->iteration }}</span>
                    </div>
                  </div>
                </td>
                <td class="receipt-num">{{ number_format($i->price, 2) }}</td>
                <td class="receipt-num"><strong>{{ number_format($i->total, 2) }}</strong></td>
              </tr>
            @endforeach
            @for($i = $items->count(); $i < 8; $i++)
              <tr>
                <td>&nbsp;</td>
                <td></td>
                <td></td>
                <td></td>
              </tr>
            @endfor
          </tbody>
        </table>

        <footer class="receipt-bottom">
          <div class="receipt-signature">
            <p>Thank you for your purchase. Keep this receipt for payment reference and order verification.</p>
            <div class="receipt-sign-line">Received by / cashier signature</div>
          </div>
          <div class="receipt-total-box">
            <div class="receipt-total-row"><span>Subtotal</span><strong>{{ number_format($order->grand_total, 2) }}</strong></div>
            <div class="receipt-total-row"><span>Tax</span><strong>0.00</strong></div>
            <div class="receipt-total-row is-grand"><span>Total</span><strong>{{ number_format($order->grand_total, 2) }}</strong></div>
          </div>
        </footer>
      </div>
    </section>
  </div>
</div>

@if(request()->boolean('autoprint'))
<script>
  window.addEventListener('load', function () {
    window.open(@json($printAutoloadUrl), '_blank');
  });
</script>
@endif
