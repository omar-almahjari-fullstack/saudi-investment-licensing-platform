<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\HtmlString;

class InvestorInvestmentCompletedNotification extends Notification
{
    use Queueable;

    protected $investment;
    protected $totalReturn;

    public function __construct($investment, float $totalReturn)
    {
        $this->investment = $investment;
        $this->totalReturn = $totalReturn;
    }

    public function via($notifiable)
    {
        $channels = [];
        if (!empty($notifiable->email)) {
            $channels[] = 'mail';
        }
        try {
            if (Schema::hasTable('notifications')) {
                $channels[] = 'database';
            }
        } catch (\Throwable $e) {}
        return $channels;
    }

    public function toMail($notifiable)
    {
        $inv = $this->investment;
        $url = url('/investor/investment/' . ($inv->id ?? ''));
        $subject = 'اكتمل استثمارك بنجاح';
        $displayId = (string)($inv->reference_code ?? $inv->id ?? 'INV');
        $idHtml = '<span dir="ltr" style="direction:ltr; unicode-bidi:bidi-override">' . e($displayId) . '</span>';
        $amountStr = number_format($this->totalReturn, 2);

        $mail = (new MailMessage)
            ->subject($subject)
            ->greeting('عميلنا الكريم')
            ->line(new HtmlString('نبارك لكم اكتمال استثماركم رقم ' . $idHtml . '.'))
            ->line(new HtmlString('إجمالي العائد المحقق: <strong>' . e($amountStr) . ' ر.س</strong>'))
            ->action('عرض تفاصيل الاستثمار', $url)
            ->salutation("مع خالص التحية,\nفريق الاستثمار");

        return $mail;
    }

    public function toArray($notifiable)
    {
        $inv = $this->investment;
        return [
            'title' => 'اكتمل استثمارك بنجاح',
            'body' => 'تم اكتمال استثمارك رقم ' . ($inv->id ?? '') . ' وإجمالي العائد ' . number_format($this->totalReturn, 2) . ' ر.س',
            'investment_id' => $inv->id ?? null,
            'status' => 'completed',
            'total_return' => $this->totalReturn,
            'link' => url('/investor/investment/' . ($inv->id ?? '')),
            'sent_at' => now()->toDateTimeString(),
        ];
    }
}
