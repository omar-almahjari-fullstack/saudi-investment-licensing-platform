<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\HtmlString;

class InvestorProfitAddedNotification extends Notification
{
    use Queueable;

    protected $investment;
    protected $monthNumber;
    protected $amount;

    public function __construct($investment, int $monthNumber, float $amount)
    {
        $this->investment = $investment;
        $this->monthNumber = $monthNumber;
        $this->amount = $amount;
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

        $subject = 'تم إضافة أرباحك إلى حسابك';
        // تحضير اسم العميل لدمجه في التحية
        $name = null;
        try { $name = $notifiable->full_name ?? $notifiable->name ?? null; } catch (\Throwable $e) { $name = null; }

        $displayId = (string)($inv->reference_code ?? $inv->id ?? 'INV');
        $displayIdUpper = strtoupper($displayId);
        // إبقاء رقم الاستثمار باتجاه LTR وكتابته بالـ Uppercase مع لون أسود غامق
        $idHtml = '<strong style="color:#111827; text-transform: uppercase;"><span dir="ltr" style="direction:ltr; unicode-bidi:bidi-override">' . e($displayIdUpper) . '</span></strong>';
        $amountStr = number_format($this->amount, 2);

        $mail = (new MailMessage)
            ->subject($subject)
            ->greeting('"عميلنا الكريم"' . ($name ? (' ' . $name) : ''))
            ->line(new HtmlString('تم إضافة ربح <strong>الشهر ' . e($this->monthNumber) . '</strong> من استثمارك رقم ' . $idHtml . ' إلى حسابك.'))
            ->line(new HtmlString('المبلغ المضاف: <strong>' . e($amountStr) . ' ر.س</strong>'))
            ->action('عرض تفاصيل الاستثمار', $url)
            ->salutation("مع خالص التحية,\nفريق الاستثمار");

        return $mail;
    }

    public function toArray($notifiable)
    {
        $inv = $this->investment;
        return [
            'title' => 'تم إضافة أرباحك إلى حسابك',
            'body' => 'تم إضافة ربح الشهر ' . $this->monthNumber . ' للاستثمار رقم: ' . ($inv->id ?? ''),
            'investment_id' => $inv->id ?? null,
            'month_number' => $this->monthNumber,
            'profit_amount' => $this->amount,
            'link' => url('/investor/investment/' . ($inv->id ?? '')),
            'sent_at' => now()->toDateTimeString(),
        ];
    }
}
