<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentConfirmed extends Mailable
{
    use Queueable, SerializesModels;

    public string $userName;
    public float $amount;
    public string $currency;
    public string $reference;
    public ?string $investmentId;

    public function __construct(string $userName, float $amount, string $currency, string $reference, ?string $investmentId = null)
    {
        $this->userName = $userName;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->reference = $reference;
        $this->investmentId = $investmentId;
    }

    public function build()
    {
        return $this->subject('تم تأكيد دفعك - '.$this->reference)
            ->view('emails.payment_confirmed');
    }
}
