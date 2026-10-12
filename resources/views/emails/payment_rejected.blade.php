<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>رفض الدفع</title>
    <style>
        body { font-family: Tahoma, Arial, sans-serif; background:#f6f7fb; color:#111; }
        .card { max-width:600px; margin:20px auto; background:#fff; border-radius:8px; padding:20px; border:1px solid #eee; }
        .muted { color:#666; }
    </style>
    </head>
<body>
    <div class="card">
        <h2 style="margin-top:0;">نأسف، تم رفض عملية الدفع</h2>
        <p>عزيزي/عزيزتي {{ $userName }},</p>
        <p>نود إبلاغك بأنه تم رفض عملية الدفع التالية:</p>
        <ul>
            <li>المبلغ: <strong>{{ number_format($amount, 2) }} {{ $currency }}</strong></li>
            <li>مرجع العملية: <strong>{{ $reference }}</strong></li>
        </ul>
        @if(!empty($reason))
        <p>السبب: <strong>{{ $reason }}</strong></p>
        @endif
        <p class="muted">يمكنك إعادة المحاولة أو التواصل مع فريق الدعم لمزيد من التفاصيل.</p>
        <p>مع التحية،<br>فريق الاستثمار</p>
    </div>
</body>
</html>
