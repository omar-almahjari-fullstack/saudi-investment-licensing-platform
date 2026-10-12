<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\HtmlString;
// Vonage/Nexmo SMS message class may not be available in some installs.
// We'll check class availability before using it to avoid fatal errors.

class PaymentDueNotification extends Notification
{
    use Queueable;

    protected $investment;
    protected $note;
    protected $amount;

    /**
     * @param mixed $investment
     * @param string|null $note
     * @param float|string|null $amount  explicit requested amount (overrides $investment->amount for display)
     */
    public function __construct($investment, $note = null, $amount = null)
    {
        $this->investment = $investment;
        $this->note = $note;
        $this->amount = $amount;
    }

    public function via($notifiable)
    {
        // For license reminders, use a mail-only isolated channel to avoid DB notification issues
        $channels = $this->isLicensePayload($this->investment) ? ['mail'] : ['mail', 'database'];
        // send SMS when the notifiable has a phone and Vonage is configured
        try {
            if (!empty($notifiable->phone) && config('services.vonage.key')) {
                $channels[] = 'vonage';
            }
        } catch (\Throwable $_) {}
        return $channels;
    }

    public function toMail($notifiable)
    {
        $inv = $this->investment;
        $isLicense = $this->isLicensePayload($inv);
        $url = $isLicense
            ? url('/investor/license/' . ($inv->id ?? ''))
            : url('/investor/dashboard?tab=payments&last_id=' . ($inv->id ?? ''));

        // If this is a license rejection case, return the exact message copied from the investment template with words changed to "ترخيص"
        if ($isLicense) {
            $rawNote = (string)($this->note ?? '');
            $lowerRaw = mb_strtolower($rawNote);
            if ($lowerRaw !== '' && (mb_strpos($lowerRaw, 'رفض') !== false || mb_strpos($lowerRaw, 'مرفوض') !== false)) {
                $orderNo = $inv->reference_code ?? $inv->code ?? $inv->uuid ?? $inv->request_code ?? ($inv->id ?? '');
                $mail = new MailMessage();
                $mail->subject('تحديث حالة الترخيص');
                // Ensure no default 'Hello!' and add the Arabic greeting only
                
                try { $name = $notifiable->full_name ?? $notifiable->name ?? null; } catch (\Throwable $e) { $name = null; }
                $greet = '"العميل الكريم/ة"' . (!empty($name) ? (' ' . $name) : '');
                $mail->greeting($greet);
                // other text: faint black
                $mail->line(new HtmlString('<span style="color:#111827">نود إبلاغكم بأنه تم تحديث حالة ترخيصكم.</span>'));
                // order number: uppercase + bold dark black
                if (!empty($orderNo)) {
                    $orderUpper = strtoupper((string)$orderNo);
                    $safeOrder = htmlspecialchars($orderUpper, ENT_QUOTES, 'UTF-8');
                    $mail->line(new HtmlString('رقم الطلب: <strong style="color:#000000"><span dir="ltr" style="direction:ltr; unicode-bidi:bidi-override">' . $safeOrder . '</span></strong>'));
                }
                // status and guidance in faint black
                $mail->line(new HtmlString('<span style="color:#111827">الحالة الجديدة: مرفوض</span>'));
                $mail->line(new HtmlString('<span style="color:#111827">لمعرفة التفاصيل وأي خطوات لاحقة، يُرجى زيارة صفحة الترخيص.</span>'));
                $mail->action('عرض تفاصيل الترخيص', $url)
                     ->salutation('مع خالص التحية, فريق الترخيص');
                return $mail;
            }
        }

        // Clean and simplify note: remove personal greetings/duplicate reminder and unescape literal \n
        $noteText = $this->note ?: '';
        if (is_string($noteText) && $noteText !== '') {
            // Replace literal \n with actual newlines
            // $noteText = str_replace(['\\n', "\\r\\n"], PHP_EOL, $noteText);
            // Remove a leading personalized greeting like: "مرحبًا {الاسم}،"
            // $noteText = preg_replace('/^\s*مرحب[اًا]\s+[^\n\r،]{0,100}[،,]\s*/u', '', $noteText);
            // Remove duplicate reminder if present at the start
            $noteText = preg_replace('/^\s*نذكّركم بوجود دفعة مستحقة لاستثماركم\.?\s*/um', '', $noteText);
            // Remove a leading amount line if user pasted it into the note
            $noteText = preg_replace('/^\s*يوجد\s+مبلغ\s+مستحق\s+للدفع[^\n\r]*[\n\r]*/um', '', $noteText);
            $noteText = trim($noteText);
        }

        // Prepare amount display (prefer explicitly provided amount)
        $displayAmount = $this->amount !== null && $this->amount !== ''
            ? $this->amount
            : ($inv->amount ?? null);
        $amountDisplay = null;
        if ($displayAmount !== null && $displayAmount !== '') {
            if (is_numeric($displayAmount)) {
                $amountDisplay = number_format((float)$displayAmount, 2) . ' ر.س';
            } else {
                $amountDisplay = $displayAmount . ' ر.س';
            }
        }

        $mail = (new MailMessage)
            ->subject($isLicense ? 'تذكير بدفع ترخيص' : 'تذكير بدفع استثمار');

        // Greeting: "العميل الكريم {الاسم}" when available, else "العميل الكريم"
        $name = null;
        try { $name = $notifiable->full_name ?? $notifiable->name ?? null; } catch (\Throwable $e) { $name = null; }
        $greet = '"العميل الكريم/ة"' . (!empty($name) ? (' ' . $name) : '');
        $mail->greeting($greet);

    $mail->line($isLicense ? 'نذكّركم بوجود دفعة مستحقة مرتبطة بالترخيص.' : 'نذكّركم بوجود دفعة مستحقة لاستثماركم.');

        // Insert amount line right after the reminder, if available (styled in black)
        if ($amountDisplay !== null) {
            $safeAmount = htmlspecialchars($amountDisplay, ENT_QUOTES, 'UTF-8');
            $mail->line(new HtmlString('<span style="color:#000000">هذا المبلغ المستحق: ' . $safeAmount . '</span>'));
        }

        $mail->line($noteText)
             ->action($isLicense ? 'عرض تفاصيل الترخيص' : 'عرض التفاصيل', $url)
             ->salutation("مع التحية,\nفريق المنصة");

        return $mail;
    }

    public function toArray($notifiable)
    {
        $inv = $this->investment;
        $isLicense = $this->isLicensePayload($inv);
        $amountVal = $this->amount !== null ? $this->amount : ($inv->amount ?? null);
        $amountDisplay = '—';
        if ($amountVal !== null && $amountVal !== '') {
            if (is_numeric($amountVal)) {
                $amountDisplay = number_format((float)$amountVal, 2) . ' ر.س';
            } else {
                $amountDisplay = $amountVal . ' ر.س';
            }
        }

        return [
            $isLicense ? 'license_id' : 'investment_id' => $inv->id ?? null,
            'amount' => $amountVal,
            'amount_display' => $amountDisplay,
            'note' => $this->note ?? null,
            'message' => $isLicense ? 'تم إرسال تذكير بالدفع لهذا الترخيص' : 'تم إرسال تذكير بالدفع لهذه الاستثمارات'
        ];
    }

    /**
     * Send a short SMS via Vonage/Nexmo if configured.
     */
    public function toVonage($notifiable)
    {
        $inv = $this->investment;
        $isLicense = $this->isLicensePayload($inv);
        $amountVal = $this->amount !== null ? $this->amount : ($inv->amount ?? null);
        $amountText = '';
        if ($amountVal !== null && $amountVal !== '') {
            $fmt = is_numeric($amountVal) ? number_format((float)$amountVal, 2) : $amountVal;
            // convert to Arabic-Indic numerals for SMS readability
            $arabic = strtr((string)$fmt, ['0'=>'٠','1'=>'١','2'=>'٢','3'=>'٣','4'=>'٤','5'=>'٥','6'=>'٦','7'=>'٧','8'=>'٨','9'=>'٩']);
            $amountText = 'المبلغ المستحق: ' . $arabic . ' ر.س. ';
        }

        $content = trim($amountText . ' ' . ($this->note ?? ''));
        if ($content === '') {
            $content = $isLicense
                ? 'لديك تذكير بدفع ترخيص. الرجاء فتح التطبيق للمزيد من التفاصيل.'
                : 'لديك تذكير بدفع استثمار. الرجاء فتح التطبيق للمزيد من التفاصيل.';
        }

        // If the VonageMessage class is available, use it. Otherwise, return a simple array
        // (the channel won't be used unless 'vonage' is present in via()).
        $vonageClass = 'Illuminate\\Notifications\\Messages\\VonageMessage';
        if (class_exists($vonageClass)) {
            // Instantiate dynamically to avoid static analysis errors when the class
            // isn't present in the environment.
            return (new $vonageClass())->content($content);
        }

        // Fallback: return a simple message shape to avoid fatal errors in environments
        // without the VonageMessage class. The 'vonage' channel will not be selected
        // by via() when Vonage is not configured or class not available, so this is
        // defensive only.
        return ['content' => $content];
    }

    /**
     * Determine if the payload is a License instance or license-like model.
     */
    private function isLicensePayload($model): bool
    {
        try {
            if ($model instanceof \App\Models\License) { return true; }
            if (is_object($model) && method_exists($model, 'getTable')) {
                return $model->getTable() === 'licenses';
            }
        } catch (\Throwable $e) { /* ignore */ }
        return false;
    }




}
