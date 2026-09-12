@extends('layouts.admin')
@section('content')
@include('shared.receipts.show-card', [
  'printUrl' => route('admin.receipts.print', $order->id),
  'printAutoloadUrl' => route('admin.receipts.print', $order->id) . '?autoprint=1',
])
@endsection
