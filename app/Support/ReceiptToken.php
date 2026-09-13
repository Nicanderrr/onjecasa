<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReceiptToken
{
    public static function generate(): string
    {
        do {
            $token = Str::random(40);
        } while (DB::table('pos_orders')->where('public_receipt_token', $token)->exists());

        return $token;
    }
}
