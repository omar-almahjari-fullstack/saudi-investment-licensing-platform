<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerificationCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $code;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $code)
    {
        $this->user = $user;
        $this->code = $code;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('رمز تفعيل حسابك — بوابة الاستثمار')
                    ->view('emails.verification_code')
                    ->with([
                        'name' => $this->user->full_name ?? $this->user->name ?? '',
                        'code' => $this->code,
                    ]);
    }
}
