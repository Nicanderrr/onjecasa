@include('shared.receipts.print-sheet', [
  'systemName' => \Illuminate\Support\Facades\DB::table('pos_settings')->where('key', 'system_name')->value('value') ?? config('app.name', 'NewPOS'),
])
