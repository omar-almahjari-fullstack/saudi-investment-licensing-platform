<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Schema;

class AdminSentMessageNotification extends Notification
{
    use Queueable;

    protected $fromUser;
    protected $messageText;

    public function __construct($fromUser, $messageText)
    {
        $this->fromUser = $fromUser;
        $this->messageText = $messageText;
    }

    public function via($notifiable)
    {
        $channels = [];

        // Deliver to in-app dashboard only. Do NOT send email for admin->investor chat messages.
        // Keep database notifications only if the notifications table exists to avoid migration-related errors.
        try {
            if (Schema::hasTable('notifications')) {
                $channels[] = 'database';
            }
        } catch (\Exception $e) {
            // If checking the schema fails for any reason, skip database channel to avoid breaking the flow.
        }

        return $channels;
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'رسالة من المدير',
            'message' => $this->messageText,
            'from' => [
                'id' => optional($this->fromUser)->id,
                'name' => optional($this->fromUser)->full_name ?? optional($this->fromUser)->name,
            ],
            'link' => route('chat'),
            'sent_at' => now()->toDateTimeString(),
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('رسالة جديدة من مكتب الاستثمار السعودي ')
            ->line('لديك رسالة جديدة من مكتب الإستثمار السعودي . يمكنك عرض الرسائل عبر صفحة الدردشة.')
            ->action('فتح الدردشة', route('chat'));
    }
}
