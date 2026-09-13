Thank you for shopping with {{ $systemName }}.

Receipt: {{ $order->code }}
Customer: {{ $order->customer_name }}
Payment: {{ $payment->method ?? 'Paid' }}
Date: {{ \Illuminate\Support\Carbon::parse($order->created_at)->format('d M Y, h:i A') }}

Items:
@foreach($items as $item)
{{ $item->product_name }} x{{ $item->qty }} - {{ number_format((float) $item->total, 2) }}
@endforeach

Total: {{ number_format((float) $order->grand_total, 2) }}
