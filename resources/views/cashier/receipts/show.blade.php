@extends('layouts.cashier')
@section('content')
@include('shared.receipts.show-card', [
  'printUrl' => route('cashier.receipts.print', $order->id),
  'printAutoloadUrl' => route('cashier.receipts.print', $order->id) . '?autoprint=1',
])
@endsection
