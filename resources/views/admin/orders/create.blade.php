@extends('layouts.admin')
@section('content')
<style>
  .order-grid { display:grid; grid-template-columns: 1.3fr .7fr; gap:16px; }
  .panel { border:1px solid var(--admin-border); border-radius:14px; background:var(--admin-surface); box-shadow:0 8px 20px rgba(15,23,42,.05); }
  .panel-h { padding:12px 14px; border-bottom:1px solid var(--admin-border); font-weight:800; color:var(--admin-text); }
  .panel-b { padding:14px; }
  .sticky-panel { position:sticky; top:18px; }
  .order-table th { font-size:11px; text-transform:uppercase; letter-spacing:.06em; color:var(--admin-muted); }
  .mini { font-size:12px; color:var(--admin-muted); }
  .sum-box { border:1px dashed var(--admin-border); border-radius:10px; padding:10px; background:var(--admin-surface-soft); }
  @media (max-width: 992px) { .order-grid { grid-template-columns: 1fr; } .sticky-panel { position:static; } }
</style>

<form method="POST" action="{{ route('admin.orders.store') }}" id="order-form">
@csrf
<div class="order-grid">
  <div class="panel">
    <div class="panel-h d-flex justify-content-between align-items-center">
      <span>Order Builder</span>
      <span class="mini">Code: ORD-{{ now()->format('YmdHis') }}</span>
    </div>
    <div class="panel-b">
      <div class="table-responsive">
        <table class="table order-table">
          <thead><tr><th>Product</th><th>Price</th><th>Qty</th><th>Total</th><th></th></tr></thead>
          <tbody id="product_tbody">
            <tr>
              <td>
                <select class="form-control item-product" name="items[0][product_id]">
                  <option value="">Select product</option>
                  @foreach($products as $product)
                    <option value="{{ $product->id }}" data-price="{{ $product->price }}">{{ $product->name }}</option>
                  @endforeach
                </select>
              </td>
              <td><input type="text" class="form-control item-price" readonly></td>
              <td><input type="number" min="1" name="items[0][qty]" class="form-control item-qty"></td>
              <td><input type="text" class="form-control item-total" readonly></td>
              <td><button type="button" class="btn btn-sm btn-outline-danger btn-row-remove"><i class="fas fa-times"></i></button></td>
            </tr>
          </tbody>
        </table>
      </div>
      <button type="button" class="btn btn-outline-success btn-sm" id="btn-add-row"><i class="fas fa-plus"></i> Add Item</button>
    </div>
  </div>

  <div class="panel sticky-panel">
    <div class="panel-h">Checkout Panel</div>
    <div class="panel-b">
      <div class="form-group">
        <label class="mini">Customer Name</label>
        <input class="form-control" name="customer_name" value="{{ old('customer_name', 'Walk-in') }}" required>
      </div>
      <div class="form-group">
        <label class="mini">Phone Number</label>
        <input class="form-control" name="customer_whatsapp" value="{{ old('customer_whatsapp') }}" inputmode="tel" autocomplete="tel" placeholder="0240000000">
        <div class="mini">Optional. Sends the receipt link by SMS when Zeckta is configured.</div>
      </div>
      <div class="form-group">
        <label class="mini">Email Address</label>
        <input class="form-control" type="email" name="customer_email" value="{{ old('customer_email') }}" autocomplete="email" placeholder="buyer@example.com">
        <div class="mini">Optional. Emails the receipt after payment.</div>
      </div>
      <div class="form-group">
        <label class="mini">Payment Method</label>
        <select class="form-control" name="payment_method" id="payment_method" required>
          <option>Cash</option>
          <option>Mobile Money</option>
        </select>
        <input type="hidden" name="paystack_reference" id="paystack_reference">
      </div>

      <div class="sum-box mb-3">
        <div class="d-flex justify-content-between"><span>Subtotal</span><span id="subtotal_text">0.00</span></div>
        <div class="d-flex justify-content-between"><span>Tax</span><span>0.00</span></div>
        <hr>
        <div class="d-flex justify-content-between font-weight-bold" style="font-size:18px;"><span>Total</span><span id="total_text">0.00</span></div>
      </div>
      <input type="hidden" id="grand_total" value="0">
      <button type="submit" class="btn btn-success btn-block" id="submit_order_btn"><i class="fas fa-check-circle"></i> Complete Payment</button>
    </div>
  </div>
</div>
</form>

<script>
(() => {
  const paystackPublicKey = @json(config('services.paystack.public_key'));
  const tbody = document.getElementById('product_tbody');
  const addBtn = document.getElementById('btn-add-row');
  const paymentMethod = document.getElementById('payment_method');
  const orderForm = document.getElementById('order-form');
  const paystackRefInput = document.getElementById('paystack_reference');
  const grandTotalInput = document.getElementById('grand_total');
  const subtotalText = document.getElementById('subtotal_text');
  const totalText = document.getElementById('total_text');
  let receiptWindow = null;
  let rowIndex = 1;

  function recalcRow(row) {
    const select = row.querySelector('.item-product');
    const priceInput = row.querySelector('.item-price');
    const qtyInput = row.querySelector('.item-qty');
    const totalInput = row.querySelector('.item-total');
    const selected = select.options[select.selectedIndex];
    const price = selected ? Number(selected.dataset.price || 0) : 0;
    const qty = Number(qtyInput.value || 0);
    const total = price * qty;
    priceInput.value = price ? price.toFixed(2) : '';
    totalInput.value = total ? total.toFixed(2) : '';
    recalcGrandTotal();
  }

  function recalcGrandTotal() {
    let total = 0;
    tbody.querySelectorAll('.item-total').forEach((input) => total += Number(input.value || 0));
    grandTotalInput.value = total.toFixed(2);
    subtotalText.textContent = total.toFixed(2);
    totalText.textContent = total.toFixed(2);
  }

  function bindRow(row) {
    row.querySelector('.item-product').addEventListener('change', () => recalcRow(row));
    row.querySelector('.item-qty').addEventListener('input', () => recalcRow(row));
    row.querySelector('.btn-row-remove').addEventListener('click', () => { row.remove(); recalcGrandTotal(); });
  }

  addBtn.addEventListener('click', () => {
    const template = tbody.querySelector('tr');
    const clone = template.cloneNode(true);
    clone.querySelector('.item-product').name = `items[${rowIndex}][product_id]`;
    clone.querySelector('.item-product').selectedIndex = 0;
    clone.querySelector('.item-price').value = '';
    clone.querySelector('.item-qty').name = `items[${rowIndex}][qty]`;
    clone.querySelector('.item-qty').value = '';
    clone.querySelector('.item-total').value = '';
    tbody.appendChild(clone);
    bindRow(clone);
    rowIndex++;
  });

  bindRow(tbody.querySelector('tr'));

  orderForm.addEventListener('submit', function (e) {
    if (paymentMethod.value !== 'Mobile Money') {
      orderForm.target = '_self';
      return;
    }
    e.preventDefault();
    const amount = Math.round(Number(grandTotalInput.value || 0) * 100);
    if (!amount || amount < 100) { orderForm.target = '_self'; alert('Please add at least one valid order item.'); return; }
    if (!paystackPublicKey) { orderForm.target = '_self'; alert('Paystack public key is not configured.'); return; }

    receiptWindow = window.open('', 'newposReceiptWindow');
    if (!receiptWindow) {
      orderForm.target = '_self';
      alert('Allow popups so the receipt can open after payment.');
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
              border-radius: 14px;
              background: var(--admin-surface);
              border: 1px solid #d8e4db;
              box-shadow: 0 10px 26px rgba(15, 23, 42, 0.08);
              font-weight: 700;
            }
          </style>
        </head>
        <body><div class="box">Preparing receipt...</div></body>
      </html>`);
    receiptWindow.document.close();
    orderForm.target = 'newposReceiptWindow';

    const handler = PaystackPop.setup({
      key: paystackPublicKey,
      email: @json(auth()->user()->email ?? 'admin@example.com'),
      amount, currency: 'GHS',
      channels: ['mobile_money'],
      callback: function(response) {
        paystackRefInput.value = response.reference;
        orderForm.submit();
      },
      onClose: function() {
        if (receiptWindow && !receiptWindow.closed) {
          receiptWindow.close();
        }
        orderForm.target = '_self';
        alert('Payment window closed.');
      }
    });
    handler.openIframe();
  });
})();
</script>
<script src="https://js.paystack.co/v1/inline.js"></script>
@endsection
