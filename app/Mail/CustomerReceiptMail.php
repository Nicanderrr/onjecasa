<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class CustomerReceiptMail extends Mailable
{
    public function __construct(
        public object $order,
        public object $payment,
        public iterable $items,
        public string $systemName
    ) {
    }

    public function build(): self
    {
        return $this
            ->subject('Receipt ' . $this->order->code . ' from ' . $this->systemName)
            ->text('emails.customer-receipt');
    }
}
