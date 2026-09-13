@extends('layouts.cashier')

@section('title', 'New Sale - Cashier')

@section('content')
@php
  $defaultImage = asset('upload/no_image.jpg');
@endphp

<style>
  .pos-order-shell {
    display: grid;
    gap: 1rem;
  }

  .pos-order-hero {
    display: flex;
    align-items: stretch;
    justify-content: space-between;
    gap: 1rem;
    padding: 1rem;
    border: 1px solid var(--admin-border);
    border-radius: 8px;
    background: linear-gradient(135deg, rgba(22, 101, 52, .08), rgba(245, 158, 11, .16));
    box-shadow: var(--admin-shadow-sm);
  }

  .pos-order-hero h2 {
    margin: 0;
    color: var(--admin-text);
    font-size: 1.25rem;
    line-height: 1.2;
  }

  .pos-order-hero p {
    margin: .35rem 0 0;
    color: var(--admin-muted);
    max-width: 52rem;
    line-height: 1.45;
  }

  .pos-order-code {
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    min-width: max-content;
    padding: .75rem .9rem;
    border: 1px solid rgba(22, 163, 74, .24);
    border-radius: 8px;
    background: var(--admin-surface);
    color: #166534;
    font-weight: 800;
  }

  .pos-scan-panel {
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto auto;
    gap: .75rem;
    align-items: end;
    padding: 1rem;
    border: 1px solid var(--admin-border);
    border-radius: 8px;
    background: var(--admin-surface);
    box-shadow: var(--admin-shadow-sm);
  }

  .pos-scan-field label {
    display: block;
    margin-bottom: .35rem;
    color: var(--admin-muted);
    font-size: .72rem;
    font-weight: 800;
    letter-spacing: .06em;
    text-transform: uppercase;
  }

  .pos-scan-input-wrap {
    position: relative;
  }

  .pos-scan-input-wrap i {
    position: absolute;
    left: .9rem;
    top: 50%;
    color: var(--admin-muted);
    transform: translateY(-50%);
    pointer-events: none;
  }

  .pos-scan-input-wrap .form-control {
    min-height: 3rem;
    padding-left: 2.65rem;
    font-weight: 800;
  }

  .pos-scan-status {
    min-height: 1.25rem;
    margin-top: .4rem;
    color: var(--admin-muted);
    font-size: .82rem;
    font-weight: 700;
  }

  .pos-scan-status.is-success {
    color: #166534;
  }

  .pos-scan-status.is-error {
    color: #b42318;
  }

  .pos-order-grid {
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(300px, .42fr);
    gap: 1rem;
    align-items: start;
  }

  .pos-order-panel {
    min-width: 0;
    border: 1px solid var(--admin-border);
    border-radius: 8px;
    background: var(--admin-surface);
    box-shadow: var(--admin-shadow-sm);
    overflow: hidden;
  }

  .pos-order-panel-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .75rem;
    padding: 1rem;
    border-bottom: 1px solid var(--admin-border);
    background: var(--admin-surface-soft);
  }

  .pos-order-panel-title {
    display: flex;
    align-items: center;
    gap: .75rem;
    min-width: 0;
  }

  .pos-order-panel-title span {
    width: 38px;
    height: 38px;
    display: inline-grid;
    place-items: center;
    flex: 0 0 auto;
    border-radius: 8px;
    background: rgba(22, 163, 74, .12);
    color: #166534;
  }

  .pos-order-panel-title strong,
  .pos-order-panel-title small {
    display: block;
  }

  .pos-order-panel-title strong {
    color: var(--admin-text);
    line-height: 1.2;
  }

  .pos-order-panel-title small {
    margin-top: .15rem;
    color: var(--admin-muted);
    line-height: 1.35;
  }

  .pos-order-panel-body {
    min-width: 0;
    padding: 1rem;
  }

  .pos-order-items {
    display: grid;
    gap: .85rem;
    min-width: 0;
  }

  .pos-order-item {
    display: grid;
    grid-template-columns: 72px minmax(0, 1fr) minmax(88px, .28fr) minmax(76px, .24fr) 38px;
    gap: .75rem;
    align-items: end;
    min-width: 0;
    padding: .85rem;
    border: 1px solid var(--admin-border);
    border-radius: 8px;
    background: var(--admin-surface);
  }

  .pos-order-thumb {
    width: 72px;
    aspect-ratio: 1;
    align-self: stretch;
    border: 1px solid var(--admin-border);
    border-radius: 8px;
    overflow: hidden;
    background: #fff;
  }

  .pos-order-thumb img {
    width: 100%;
    height: 100%;
    display: block;
    object-fit: cover;
  }

  .pos-order-item .form-control,
  .pos-order-item .pos-field {
    min-width: 0;
    max-width: 100%;
  }

  .pos-order-item .pos-line-total {
    grid-column: 2 / 4;
  }

  .pos-order-item .pos-product-meta {
    grid-column: 4 / 5;
    align-self: center;
    margin-top: 0;
  }

  .pos-field label {
    margin-bottom: .35rem;
    color: var(--admin-muted);
    font-size: .72rem;
    font-weight: 800;
    letter-spacing: .06em;
    text-transform: uppercase;
  }

  .pos-product-meta {
    margin-top: .35rem;
    color: var(--admin-muted);
    font-size: .78rem;
    line-height: 1.3;
  }

  .pos-remove-cell {
    align-self: center;
    justify-self: end;
  }

  .pos-checkout-panel {
    position: sticky;
    top: 5.75rem;
  }

  .pos-payment-options {
    display: grid;
    gap: .65rem;
  }

  .pos-summary {
    display: grid;
    gap: .65rem;
    margin-top: 1rem;
    padding: 1rem;
    border: 1px dashed var(--admin-border);
    border-radius: 8px;
    background: var(--admin-surface-soft);
  }

  .pos-summary-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    color: var(--admin-muted);
  }

  .pos-summary-row strong {
    color: var(--admin-text);
  }

  .pos-summary-total {
    padding-top: .75rem;
    border-top: 1px solid var(--admin-border);
    color: var(--admin-text);
    font-size: 1.2rem;
    font-weight: 900;
  }

  .pos-order-empty {
    padding: .9rem 1rem;
    border: 1px solid rgba(245, 158, 11, .34);
    border-radius: 8px;
    background: rgba(245, 158, 11, .1);
    color: var(--admin-muted);
    font-size: .9rem;
  }

  @media (max-width: 1399.98px) {
    .pos-order-item {
      grid-template-columns: 72px minmax(0, 1fr) minmax(86px, .32fr) minmax(76px, .28fr) 38px;
    }
  }

  @media (max-width: 991.98px) {
    .pos-order-grid {
      grid-template-columns: 1fr;
    }

    .pos-checkout-panel {
      position: static;
    }

    .pos-order-hero {
      flex-direction: column;
    }

    .pos-order-code {
      width: fit-content;
    }

    .pos-scan-panel {
      grid-template-columns: 1fr;
    }
  }

  @media (max-width: 767.98px) {
    .pos-order-shell {
      gap: .75rem;
    }

    .pos-order-hero,
    .pos-scan-panel,
    .pos-order-panel-body,
    .pos-order-panel-header {
      padding: .85rem;
    }

    .pos-order-hero h2 {
      font-size: 1.08rem;
    }

    .pos-order-hero p,
    .pos-order-code,
    .pos-scan-status {
      font-size: .82rem;
    }

    .pos-scan-panel .btn,
    .pos-order-panel-header .btn,
    .pos-checkout-panel .btn {
      width: 100%;
      justify-content: center;
    }

    .pos-order-panel-header {
      flex-direction: column;
      align-items: stretch;
    }

    .pos-order-item {
      grid-template-columns: 64px minmax(0, 1fr) 38px;
      align-items: start;
      gap: .65rem;
      padding: .75rem;
    }

    .pos-order-thumb {
      width: 64px;
    }

    .pos-order-item .pos-field {
      grid-column: 2 / 3;
    }

    .pos-order-item .pos-price-field,
    .pos-order-item .pos-qty-field,
    .pos-order-item .pos-line-total,
    .pos-order-item .pos-product-meta {
      grid-column: 1 / -1;
    }

    .pos-remove-cell {
      grid-column: 3 / 4;
      grid-row: 1;
      align-self: start;
    }

    .pos-summary-row,
    .pos-summary-total {
      font-size: .92rem;
    }
  }

  @media (max-width: 420px) {
    .pos-order-item {
      grid-template-columns: 56px minmax(0, 1fr) 36px;
    }

    .pos-order-thumb {
      width: 56px;
    }

    .pos-order-panel-title span {
      width: 34px;
      height: 34px;
    }

    .pos-order-panel-title small {
      font-size: .78rem;
    }
  }
</style>

<form method="POST" action="{{ route('cashier.sales.store') }}" id="order-form" class="pos-order-shell">
  @csrf

  <section class="pos-order-hero">
    <div>
      <h2>Walk-in Checkout</h2>
      <p>Scan products continuously or select them manually, confirm quantities, and complete the order from one screen.</p>
    </div>
    <div class="pos-order-code">
      <i class="bi bi-receipt-cutoff"></i>
      ORD-{{ now()->format('YmdHis') }}
    </div>
  </section>

  @if($products->isNotEmpty())
    <section class="pos-scan-panel">
      <div class="pos-scan-field">
        <label for="barcode_scan">Barcode Scan</label>
        <div class="pos-scan-input-wrap">
          <i class="bi bi-upc-scan" aria-hidden="true"></i>
          <input id="barcode_scan" type="text" class="form-control" inputmode="numeric" autocomplete="off" placeholder="Scan barcode or type SKU and press Enter" data-hardware-barcode-capture data-barcode-camera-continuous>
        </div>
        <div class="pos-scan-status" id="barcode_scan_status">Ready for continuous barcode scans.</div>
      </div>
      <button type="button" class="btn btn-outline-secondary" data-open-barcode-camera data-barcode-target="#barcode_scan">
        <i class="bi bi-camera-video"></i> Open Camera Scanner
      </button>
      <button type="button" class="btn btn-outline-secondary" id="focus_barcode_scan">
        <i class="bi bi-crosshair"></i> Focus Scanner
      </button>
    </section>
  @endif

  <div class="pos-order-grid">
    <section class="pos-order-panel">
      <div class="pos-order-panel-header">
        <div class="pos-order-panel-title">
          <span><i class="bi bi-basket2"></i></span>
          <div>
            <strong>Order Items</strong>
            <small>Scanned products are added automatically; repeat scans increase quantity.</small>
          </div>
        </div>
        <button type="button" class="btn btn-outline-success btn-sm" id="btn-add-row">
          <i class="bi bi-plus-circle"></i> Add Item
        </button>
      </div>

      <div class="pos-order-panel-body">
        @if($products->isEmpty())
          <div class="alert alert-warning mb-0">No products are available.</div>
        @else
          <div class="pos-order-items" id="product_tbody">
            <div class="pos-order-item" data-order-row>
              <div class="pos-order-thumb">
                <img class="item-image" src="{{ $defaultImage }}" alt="Selected product image">
              </div>

              <div class="pos-field">
                <label>Product</label>
                <select class="form-control item-product" name="items[0][product_id]" required>
                  <option value="" data-price="" data-stock="" data-image="{{ $defaultImage }}">Select product</option>
                  @foreach($products as $product)
                    @php
                      $imageUrl = $product->image
                          ? asset('assets/admin/img/products/' . $product->image)
                          : $defaultImage;
                    @endphp
                    <option
                      value="{{ $product->id }}"
                      data-price="{{ $product->price }}"
                      data-stock="{{ $product->stock }}"
                      data-code="{{ $product->code }}"
                      data-image="{{ $imageUrl }}"
                      @disabled((int) $product->stock < 1)
                    >
                      {{ $product->name }}
                    </option>
                  @endforeach
                </select>
              </div>

              <div class="pos-field pos-price-field">
                <label>Price</label>
                <input type="text" class="form-control item-price" readonly>
              </div>

              <div class="pos-field pos-qty-field">
                <label>Qty</label>
                <input type="number" min="1" name="items[0][qty]" class="form-control item-qty" placeholder="1" required>
              </div>

              <div class="pos-field pos-line-total">
                <label>Total</label>
                <input type="text" class="form-control item-total" readonly>
              </div>

              <div class="pos-product-meta item-meta">Choose a product to show its photo and available stock.</div>

              <div class="pos-remove-cell">
                <button type="button" class="btn btn-sm btn-outline-danger btn-row-remove" aria-label="Remove order item">
                  <i class="bi bi-x-lg"></i>
                </button>
              </div>
            </div>
          </div>

          <div class="pos-order-empty mt-3" id="order-helper">
            Start by scanning a product or choosing one manually. New scanned products add rows automatically.
          </div>
        @endif
      </div>
    </section>

    <aside class="pos-order-panel pos-checkout-panel">
      <div class="pos-order-panel-header">
        <div class="pos-order-panel-title">
          <span><i class="bi bi-credit-card"></i></span>
          <div>
            <strong>Checkout</strong>
            <small>Walk-in customer and payment summary.</small>
          </div>
        </div>
      </div>

      <div class="pos-order-panel-body">
        <div class="form-group mb-3">
          <label class="mb-2">Customer Name</label>
          <input class="form-control" name="customer_name" value="{{ old('customer_name', 'Walk-in') }}" required>
        </div>

        <div class="form-group mb-3">
          <label class="mb-2">Payment Method</label>
        <select class="form-control" name="payment_method" id="payment_method" required>
          <option @selected(old('payment_method') === 'Cash')>Cash</option>
          <option @selected(old('payment_method') === 'Mobile Money')>Mobile Money</option>
          <option @selected(old('payment_method') === 'Credit Card')>Credit Card</option>
        </select>
        <input type="hidden" name="paystack_reference" id="paystack_reference">
      </div>

        <div class="pos-summary">
          <div class="pos-summary-row">
            <span>Items</span>
            <strong id="items_count_text">0</strong>
          </div>
          <div class="pos-summary-row">
            <span>Subtotal</span>
            <strong id="subtotal_text">0.00</strong>
          </div>
          <div class="pos-summary-row">
            <span>Tax</span>
            <strong>0.00</strong>
          </div>
          <div class="pos-summary-row pos-summary-total">
            <span>Total</span>
            <strong id="total_text">0.00</strong>
          </div>
        </div>

        <input type="hidden" id="grand_total" value="0">

        <button type="submit" class="btn btn-success btn-block mt-3" id="submit_order_btn" @disabled($products->isEmpty())>
          <i class="bi bi-check2-circle"></i> Complete Payment
        </button>
      </div>
    </aside>
  </div>
</form>

@if($products->isNotEmpty())
@include('admin.products.partials.barcode-scanner')
<script src="{{ asset('assets/cashier/js/swal.js') }}"></script>
<script>
(() => {
  const defaultImage = @json($defaultImage);
  const tbody = document.getElementById('product_tbody');
  const addBtn = document.getElementById('btn-add-row');
  const paymentMethod = document.getElementById('payment_method');
  const orderForm = document.getElementById('order-form');
  const paystackPublicKey = @json(config('services.paystack.public_key'));
  const paystackRefInput = document.getElementById('paystack_reference');
  const grandTotalInput = document.getElementById('grand_total');
  const subtotalText = document.getElementById('subtotal_text');
  const totalText = document.getElementById('total_text');
  const itemsCountText = document.getElementById('items_count_text');
  const helper = document.getElementById('order-helper');
  const submitButton = document.getElementById('submit_order_btn');
  const barcodeInput = document.getElementById('barcode_scan');
  const barcodeStatus = document.getElementById('barcode_scan_status');
  const focusBarcodeButton = document.getElementById('focus_barcode_scan');
  let receiptWindow = null;
  let paymentConfirmed = false;
  let rowIndex = 1;

  function selectedOption(row) {
    const select = row.querySelector('.item-product');
    return select.options[select.selectedIndex];
  }

  function refreshRowProduct(row) {
    const selected = selectedOption(row);
    const image = row.querySelector('.item-image');
    const meta = row.querySelector('.item-meta');
    const price = Number(selected ? selected.dataset.price || 0 : 0);
    const stock = selected ? Number(selected.dataset.stock || 0) : 0;
    const productName = selected ? selected.textContent.trim() : '';

    image.src = selected && selected.dataset.image ? selected.dataset.image : defaultImage;
    image.alt = productName && selected.value ? productName : 'Selected product image';
    row.querySelector('.item-price').value = price ? price.toFixed(2) : '';

    if (selected && selected.value) {
      meta.textContent = `Stock available: ${stock} units`;
    } else {
      meta.textContent = 'Choose a product to show its photo and available stock.';
    }
  }

  function recalcRow(row) {
    const selected = selectedOption(row);
    const qtyInput = row.querySelector('.item-qty');
    const totalInput = row.querySelector('.item-total');
    const price = selected ? Number(selected.dataset.price || 0) : 0;
    const stock = selected ? Number(selected.dataset.stock || 0) : 0;
    let qty = Number(qtyInput.value || 0);

    if (selected && selected.value && qty > stock) {
      qty = stock;
      qtyInput.value = stock;
    }

    refreshRowProduct(row);
    totalInput.value = price && qty ? (price * qty).toFixed(2) : '';
    recalcGrandTotal();
  }

  function recalcGrandTotal() {
    let total = 0;
    let items = 0;

    tbody.querySelectorAll('[data-order-row]').forEach((row) => {
      total += Number(row.querySelector('.item-total').value || 0);
      items += Number(row.querySelector('.item-qty').value || 0);
    });

    grandTotalInput.value = total.toFixed(2);
    subtotalText.textContent = total.toFixed(2);
    totalText.textContent = total.toFixed(2);
    itemsCountText.textContent = items;
    helper.style.display = total > 0 ? 'none' : '';
    submitButton.disabled = total <= 0;
  }

  function refreshNames() {
    tbody.querySelectorAll('[data-order-row]').forEach((row, index) => {
      row.querySelector('.item-product').name = `items[${index}][product_id]`;
      row.querySelector('.item-qty').name = `items[${index}][qty]`;
    });
    rowIndex = tbody.querySelectorAll('[data-order-row]').length;
  }

  function setScanStatus(message, state = '') {
    barcodeStatus.textContent = message;
    barcodeStatus.classList.toggle('is-success', state === 'success');
    barcodeStatus.classList.toggle('is-error', state === 'error');
  }

  function resetRow(row) {
    row.querySelector('.item-product').selectedIndex = 0;
    row.querySelector('.item-price').value = '';
    row.querySelector('.item-qty').value = '';
    row.querySelector('.item-total').value = '';
    row.querySelector('.item-image').src = defaultImage;
    row.querySelector('.item-meta').textContent = 'Choose a product to show its photo and available stock.';
    recalcGrandTotal();
  }

  function bindRow(row) {
    row.querySelector('.item-product').addEventListener('change', () => recalcRow(row));
    row.querySelector('.item-qty').addEventListener('input', () => recalcRow(row));
    row.querySelector('.btn-row-remove').addEventListener('click', () => {
      if (tbody.querySelectorAll('[data-order-row]').length === 1) {
        resetRow(row);
        return;
      }

      row.remove();
      refreshNames();
      recalcGrandTotal();
    });
    refreshRowProduct(row);
  }

  function addEmptyRow() {
    const template = tbody.querySelector('[data-order-row]');
    const clone = template.cloneNode(true);

    clone.querySelector('.item-product').name = `items[${rowIndex}][product_id]`;
    clone.querySelector('.item-product').selectedIndex = 0;
    clone.querySelector('.item-price').value = '';
    clone.querySelector('.item-qty').name = `items[${rowIndex}][qty]`;
    clone.querySelector('.item-qty').value = '';
    clone.querySelector('.item-total').value = '';
    clone.querySelector('.item-image').src = defaultImage;
    clone.querySelector('.item-meta').textContent = 'Choose a product to show its photo and available stock.';
    tbody.insertBefore(clone, tbody.firstElementChild);
    bindRow(clone);
    refreshNames();
    rowIndex++;
    return clone;
  }

  function productOptionByBarcode(code) {
    const normalized = String(code || '').trim().toLowerCase();
    if (!normalized) {
      return null;
    }

    return [...tbody.querySelector('[data-order-row]').querySelectorAll('.item-product option')]
      .find((option) => String(option.dataset.code || '').trim().toLowerCase() === normalized) || null;
  }

  function rowForProduct(productId) {
    return [...tbody.querySelectorAll('[data-order-row]')]
      .find((row) => row.querySelector('.item-product').value === String(productId)) || null;
  }

  function firstEmptyRow() {
    return [...tbody.querySelectorAll('[data-order-row]')]
      .find((row) => !row.querySelector('.item-product').value) || null;
  }

  function addBarcodeProduct(code) {
    const option = productOptionByBarcode(code);

    if (!option) {
      setScanStatus(`No product found for barcode ${code}.`, 'error');
      return;
    }

    if (option.disabled) {
      setScanStatus(`${option.textContent.trim()} is out of stock.`, 'error');
      return;
    }

    let row = rowForProduct(option.value);

    if (row) {
      const qtyInput = row.querySelector('.item-qty');
      const stock = Number(selectedOption(row).dataset.stock || 0);
      const nextQty = Math.min(stock, Number(qtyInput.value || 0) + 1);
      qtyInput.value = nextQty;
      recalcRow(row);
      setScanStatus(`${option.textContent.trim()} quantity is now ${nextQty}.`, 'success');
      return;
    }

    row = firstEmptyRow() || addEmptyRow();
    row.querySelector('.item-product').value = option.value;
    row.querySelector('.item-qty').value = 1;
    recalcRow(row);
    setScanStatus(`${option.textContent.trim()} added to the order.`, 'success');
  }

  addBtn.addEventListener('click', () => {
    addEmptyRow();
  });

  barcodeInput?.addEventListener('keydown', (event) => {
    if (event.key !== 'Enter') {
      return;
    }

    event.preventDefault();
    const code = barcodeInput.value.trim();
    if (!code) {
      setScanStatus('Scan a barcode or type a SKU first.', 'error');
      return;
    }

    addBarcodeProduct(code);
    barcodeInput.value = '';
    barcodeInput.focus();
  });

  barcodeInput?.addEventListener('barcode-scanned', (event) => {
    const code = String(event.detail?.code || barcodeInput.value || '').trim();
    if (!code) {
      return;
    }

    addBarcodeProduct(code);
    barcodeInput.value = '';
    barcodeInput.focus();
  });

  focusBarcodeButton?.addEventListener('click', () => {
    barcodeInput?.focus();
  });

  function closeReceiptWindow() {
    if (receiptWindow && !receiptWindow.closed) {
      receiptWindow.close();
    }
    receiptWindow = null;
  }

  function submitPaystackOrder(reference) {
    paystackRefInput.value = reference;
    orderForm.submit();
  }

  function handlePaystackClosed(message) {
    closeReceiptWindow();
    orderForm.target = '_self';
    paymentConfirmed = false;

    if (typeof swal === 'function') {
      swal('Payment not completed', message, 'warning');
      return;
    }

    alert(message);
  }

  function showPaymentConfirmation() {
    const method = paymentMethod.value;
    const total = Number(grandTotalInput.value || 0).toFixed(2);
    const customerName = document.querySelector('[name="customer_name"]')?.value || 'Walk-in';

    if (typeof swal !== 'function') {
      return Promise.resolve(window.confirm(`Confirm ${method} payment of ${total} for ${customerName}?`));
    }

    return swal({
      title: 'Confirm payment',
      text: `Payment method: ${method}\nCustomer: ${customerName}\nTotal: ${total}`,
      icon: 'warning',
      buttons: {
        cancel: {
          text: 'Cancel',
          visible: true,
          value: false,
        },
        confirm: {
          text: method === 'Mobile Money' ? 'Proceed to MoMo' : 'Confirm Payment',
          value: true,
          closeModal: true,
        },
      },
      dangerMode: false,
    });
  }

  function openPaystackCheckout(amount) {
    const options = {
      key: paystackPublicKey,
      email: @json(auth()->user()->email ?? 'cashier@example.com'),
      amount,
      currency: 'GHS',
      channels: ['mobile_money'],
      metadata: {
        customer_name: document.querySelector('[name="customer_name"]')?.value || 'Walk-in',
        payment_method: 'Mobile Money',
      },
    };

    if (typeof PaystackPop === 'undefined') {
      handlePaystackClosed('Paystack could not load. Check internet connection and try again.');
      return;
    }

    if (typeof PaystackPop.setup === 'function') {
      const handler = PaystackPop.setup({
        ...options,
        callback: function(response) {
          submitPaystackOrder(response.reference);
        },
        onClose: function() {
          handlePaystackClosed('Payment window closed.');
        },
      });
      handler.openIframe();
      return;
    }

    const popup = new PaystackPop();
    popup.newTransaction({
      ...options,
      onSuccess: function(transaction) {
        submitPaystackOrder(transaction.reference);
      },
      onCancel: function() {
        handlePaystackClosed('Payment window closed.');
      },
      onError: function(error) {
        handlePaystackClosed(error?.message || 'Unable to start Paystack payment.');
      },
    });
  }

  orderForm.addEventListener('submit', async function(event) {
    if (paystackRefInput.value) {
      orderForm.target = '_self';
      return;
    }

    event.preventDefault();
    const amount = Math.round(Number(grandTotalInput.value || 0) * 100);

    if (!amount || amount < 100) {
      orderForm.target = '_self';
      alert('Please add at least one valid order item.');
      return;
    }

    if (!paymentConfirmed) {
      const confirmed = await showPaymentConfirmation();
      if (!confirmed) {
        orderForm.target = '_self';
        return;
      }
      paymentConfirmed = true;
    }

    if (paymentMethod.value !== 'Mobile Money') {
      orderForm.target = '_self';
      orderForm.submit();
      return;
    }

    if (!paystackPublicKey) {
      orderForm.target = '_self';
      paymentConfirmed = false;
      if (typeof swal === 'function') {
        swal('Paystack not configured', 'Paystack public key is not configured.', 'error');
      } else {
        alert('Paystack public key is not configured.');
      }
      return;
    }

    receiptWindow = window.open('', 'newposCashierReceiptWindow');
    if (!receiptWindow) {
      orderForm.target = '_self';
      paymentConfirmed = false;

      if (typeof swal === 'function') {
        swal('Receipt popup blocked', 'Allow popups so the receipt can open after payment.', 'warning');
      } else {
        alert('Allow popups so the receipt can open after payment.');
      }
      return;
    }

    receiptWindow.document.write(`<!doctype html>
      <html lang="en">
        <head>
          <meta charset="utf-8">
          <title>Preparing receipt...</title>
          <style>
            body {
              margin: 0;
              min-height: 100vh;
              display: grid;
              place-items: center;
              font-family: "Segoe UI", Arial, sans-serif;
              background: #eef3ef;
              color: #14532d;
            }
            .box {
              padding: 18px 22px;
              border-radius: 8px;
              background: #ffffff;
              border: 1px solid #d8e4db;
              box-shadow: 0 10px 26px rgba(15, 23, 42, 0.08);
              font-weight: 700;
            }
          </style>
        </head>
        <body><div class="box">Preparing receipt...</div></body>
      </html>`);
    receiptWindow.document.close();
    orderForm.target = 'newposCashierReceiptWindow';
    openPaystackCheckout(amount);
  });

  bindRow(tbody.querySelector('[data-order-row]'));
  recalcGrandTotal();
  barcodeInput?.focus();
})();
</script>
<script src="https://js.paystack.co/v2/inline.js"></script>
@endif
@endsection
