<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Schema;

class LicenseStatusChangedNotification extends Notification
{
    use Queueable;

    protected $license;
    protected $prevStatus;
    protected $newStatus;

    public function __construct($license, $prevStatus, $newStatus)
    {
        $this->license = $license;
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
        $lic = $this->license;
        $url = url('/investor/license/' . ($lic->id ?? ''));

        // Subject
        $subject = 'تحديث حالة الترخيص';

        // Prepare recipient name (optional)
        $name = null;
        try { $name = $notifiable->full_name ?? $notifiable->name ?? null; } catch (\Throwable $e) { $name = null; }
        $greet = '"العميل الكريم/ة"' . (!empty($name) ? (' ' . $name) : '');

        // Map status to Arabic label and color
        $status = strtolower((string)($this->newStatus ?? ''));
        $statusMap = [
            'approved'  => ['معتمد', '#16a34a'],
            'active'    => ['نشط', '#16a34a'],
            'completed' => ['مكتمل', '#2563eb'],
            'pending'   => ['قيد المراجعة', '#f59e0b'],
            'rejected'  => ['مرفوض', '#ef4444'],
        ];
        $mapped = $statusMap[$status] ?? [($this->newStatus ?? '-'), '#6b7280'];
        [$statusAr, $statusColor] = $mapped;

        // Compose a friendly Arabic message
        $mail = (new MailMessage)
            ->subject($subject)
            ->greeting($greet);

        // Build a friendly identifier for display: prefer reference_code/code/uuid/request_code; fallback to LIC-000123
        $orderNo = null;
        try {
            $orderNo = $lic->reference_code ?? $lic->code ?? $lic->uuid ?? $lic->request_code ?? ($lic->id ?? '');
        } catch (\Throwable $e) {
            $orderNo = ($lic->id ?? '');
        }
        $displayId = (string)($orderNo ?? '');

        if ($displayId === '' && isset($lic->id)) {
            $displayId = (string)$lic->id;
        }

        if (preg_match('/^[0-9a-f\-]{16,}$/i', $displayId)) {
            // looks like a UUID
            $displayId = strtoupper($displayId);
        } elseif (is_numeric($displayId)) {
            $displayId = 'LIC-' . str_pad((string)((int)$displayId), 6, '0', STR_PAD_LEFT);
        } else {
            $displayId = strtoupper($displayId);
        }

        // Normalize if captured as <digits><uuid>
        $normalizedDisplayId = $displayId;
        if (preg_match('/^\d+([a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12})$/i', $displayId, $m)) {
            $normalizedDisplayId = strtoupper($m[1]);
        }
        $idText = htmlspecialchars($normalizedDisplayId, ENT_QUOTES, 'UTF-8');

        $mail->line(new HtmlString('نود إبلاغكم بأنه تم تحديث حالة ترخيصكم.'))
             ->line(new HtmlString('رقم الطلب: <strong style="color:#111827"><span dir="ltr" style="direction:ltr; unicode-bidi:bidi-override">' . $idText . '</span></strong>'))
             ->line(new HtmlString('الحالة الجديدة: <strong style="color: ' . $statusColor . '">' . htmlspecialchars($statusAr, ENT_QUOTES, 'UTF-8') . '</strong>'))
             ->line('لمعرفة التفاصيل وأي خطوات لاحقة، يُرجى زيارة صفحة الترخيص.')
             ->action('عرض تفاصيل الترخيص', $url)
             ->salutation("مع خالص التحية,\nفريق الترخيص");

        return $mail;
    }

    public function toArray($notifiable)
    {
        $lic = $this->license;
        return [
            'title' => 'تحديث حالة الترخيص',
            'body' => 'تم تغيير حالة ترخيصك رقم: ' . ($lic->id ?? '') . ' إلى: ' . ($this->newStatus ?? ''),
            'license_id' => $lic->id ?? null,
            'prev_status' => $this->prevStatus,
            'new_status' => $this->newStatus,
            'link' => url('/investor/license/' . ($lic->id ?? '')),
            'sent_at' => now()->toDateTimeString(),
        ];
    }
}
