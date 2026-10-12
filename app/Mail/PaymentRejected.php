<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentRejected extends Mailable
{
    use Queueable, SerializesModels;

    public string $userName;
    public float $amount;
    public string $currency;
    public string $reference;
    public ?string $reason;

    public function __construct(string $userName, float $amount, string $currency, string $reference, ?string $reason = null)
    {
        $this->userName = $userName;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->reference = $reference;
        $this->reason = $reason;
    }

    public function build()
    {
        return $this->subject('تم رفض عملية الدفع - '.$this->reference)
            ->view('emails.payment_rejected');
    }
}
