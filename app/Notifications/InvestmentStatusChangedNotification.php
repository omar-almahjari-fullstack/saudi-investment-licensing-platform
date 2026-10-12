<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Schema;

class InvestmentStatusChangedNotification extends Notification
{
    use Queueable;

    protected $investment;
    protected $prevStatus;
    protected $newStatus;

    public function __construct($investment, $prevStatus, $newStatus)
    {
        $this->investment = $investment;
        $this->prevStatus = $prevStatus;
        $this->newStatus = $newStatus;
    }

    public function via($notifiable)
    {
        $channels = [];

        // send mail if user has email
        if (!empty($notifiable->email)) {
            $channels[] = 'mail';
        }

        // include database channel only if notifications table exists (avoid runtime errors)
        try {
            if (Schema::hasTable('notifications')) {
                $channels[] = 'database';
            }
        } catch (\Exception $e) {
            // swallow - don't let missing schema break status updates
        }

        return $channels;
    }

    public function toMail($notifiable)
    {
        $inv = $this->investment;
        $url = url('/investor/investment/' . ($inv->id ?? ''));

        // Subject
        $subject = 'تحديث حالة الاستثمار';

        // Prepare recipient name (optional)
        $name = null;
        try { $name = $notifiable->full_name ?? $notifiable->name ?? null; } catch (\Throwable $e) { $name = null; }
        $greet = '"العميل الكريم/ة"' . (!empty($name) ? (' ' . $name) : '');

        // Map status to Arabic label and color
        $status = strtolower((string)($this->newStatus ?? ''));
        $statusMap = [
            'approved'  => ['معتمد', '#16a34a'],
            'active'    => ['نشط', '#f97316'], // orange
            'completed' => ['مكتمل', '#2563eb'], // blue
            'pending'   => ['قيد المراجعة', '#f59e0b'],
            'rejected'  => ['مرفوض', '#ef4444'],
        ];
        $mapped = $statusMap[$status] ?? [($this->newStatus ?? '-'), '#6b7280'];
        [$statusAr, $statusColor] = $mapped;

        // Compose a friendly Arabic message
        $mail = (new MailMessage)
            ->subject($subject)
            ->greeting($greet);

        // Build a friendly identifier for display: prefer UUID-like or reference_code; fallback to INV-000123
        $rawId = (string)($inv->id ?? '');
        $refCode = null;
        try { $refCode = $inv->reference_code ?? null; } catch (\Throwable $e) { $refCode = null; }
        if ($refCode !== null && $refCode !== '') {
            $displayId = (string)$refCode;
        } elseif (preg_match('/^[0-9a-f\-]{16,}$/i', $rawId)) {
            // looks like a UUID
            $displayId = strtoupper($rawId);
        } elseif (is_numeric($rawId)) {
            $displayId = 'INV-' . str_pad((string) ((int)$rawId), 6, '0', STR_PAD_LEFT);
        } else {
            $displayId = $rawId !== '' ? strtoupper($rawId) : 'INV';
        }
        // Normalize: if the display ID starts with digits then a UUID, strip the leading digits
        $normalizedDisplayId = $displayId;
        if (preg_match('/^\d+([a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12})$/i', $displayId, $m)) {
            $normalizedDisplayId = $m[1];
        }
        $idText = htmlspecialchars($normalizedDisplayId, ENT_QUOTES, 'UTF-8');

       $mail->line(new HtmlString('نود إبلاغكم بأنه تم تحديث حالة استثماركم.'))
           ->line(new HtmlString('رقم الطلب: <strong style="color:#111827"><span dir="ltr" style="direction:ltr; unicode-bidi:bidi-override">' . $idText . '</span></strong>'))
           ->line(new HtmlString('الحالة الجديدة: <strong style="color: ' . $statusColor . '">' . htmlspecialchars($statusAr, ENT_QUOTES, 'UTF-8') . '</strong>'))
             ->line('لمعرفة التفاصيل وأي خطوات لاحقة، يُرجى زيارة صفحة الاستثمار.')
             ->action('عرض تفاصيل الاستثمار', $url)
             ->salutation("مع خالص التحية,\nفريق الاستثمار");

        return $mail;
    }

    public function toArray($notifiable)
    {
        $inv = $this->investment;
        return [
            'title' => 'تحديث حالة الاستثمار',
            'body' => 'تم تغيير حالة استثمارك رقم: ' . ($inv->id ?? '') . ' إلى: ' . ($this->newStatus ?? ''),
            'investment_id' => $inv->id ?? null,
            'prev_status' => $this->prevStatus,
            'new_status' => $this->newStatus,
            'link' => url('/investor/investment/' . ($inv->id ?? '')),
            'sent_at' => now()->toDateTimeString(),
        ];
    }
}
