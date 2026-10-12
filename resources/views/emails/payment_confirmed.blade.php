<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>تأكيد الدفع</title>
    <style>
        body { font-family: Tahoma, Arial, sans-serif; background:#f6f7fb; color:#111; }
        .card { max-width:600px; margin:20px auto; background:#fff; border-radius:8px; padding:20px; border:1px solid #eee; }
        .muted { color:#666; }
    </style>
    </head>
<body>
    <div class="card">
        <h2 style="margin-top:0;">تم تأكيد عملية الدفع</h2>
        <p>عزيزي/عزيزتي {{ $userName }},</p>
        <p>نشكر لك ثقتك. تم تأكيد عملية الدفع الخاصة بك.</p>
        <ul>
            <li>المبلغ: <strong>{{ number_format($amount, 2) }} {{ $currency }}</strong></li>
            <li>مرجع العملية: <strong>{{ $reference }}</strong></li>
            @if(!empty($investmentId))
            <li>رقم الاستثمار: <strong>{{ $investmentId }}</strong></li>
            @endif
        </ul>
        <p class="muted">في حال لم تقم بهذه العملية، يرجى التواصل مع الدعم فورًا.</p>
        <p>مع التحية،<br>فريق الاستثمار</p>
    </div>
</body>
</html>
