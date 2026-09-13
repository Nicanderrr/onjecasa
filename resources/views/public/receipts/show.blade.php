<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Receipt {{ $order->code }}</title>
  <link rel="stylesheet" href="{{ asset('assets/adminhmd/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/adminhmd/vendors/bootstrap-icons/bootstrap-icons.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/adminhmd/css/style.css') }}">
</head>
<body>
  <main class="container py-4">
    @include('shared.receipts.show-card', [
      'printUrl' => route('receipts.public.print', $order->public_receipt_token),
      'printAutoloadUrl' => route('receipts.public.print', $order->public_receipt_token) . '?autoprint=1',
      'systemName' => \Illuminate\Support\Facades\DB::table('pos_settings')->where('key', 'system_name')->value('value') ?? config('app.name', 'NewPOS'),
    ])
  </main>
</body>
</html>
