<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class LowStockAlertMail extends Mailable
{
    public function __construct(public object $product)
    {
    }

    public function build(): self
    {
        return $this
            ->subject('Low stock alert: ' . $this->product->name)
            ->text('emails.low-stock-alert');
    }
}
