<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>رمز تفعيل الحساب</title>
</head>
<body style="font-family:Tahoma,Arial,Helvetica,sans-serif;direction:rtl;background:#f7fafc;padding:24px;color:#0f1724;">
  <div style="max-width:620px;margin:0 auto;background:#fff;padding:20px;border-radius:8px;border:1px solid #e2e8f0">
    <h2 style="margin-top:0;color:#0f1724">رمز تفعيل حسابك</h2>
    <p>مرحباً {{ $name }},</p>
  <p>شكرًا لتسجيلك في عوائد الخليج للاستثمار السعودي. رمز التحقق لتفعيل حسابك هو:</p>
    <p style="font-size:28px;font-weight:700;color:#06b6d4;letter-spacing:6px">{{ $code }}</p>
    <p>أدخل هذا الرمز في صفحة التحقق لإكمال تفعيل حسابك. تنتهي صلاحية الرمز بعد مدة قصيرة لأسباب أمنية.</p>
    <p>إن لم تطلب هذا الرمز، يمكنك تجاهل هذه الرسالة.</p>
    <hr>
  <p style="font-size:12px;color:#64748b">© {{ date('Y') }} عوائد الخليج للاستثمار السعودي</p>
  </div>
</body>
</html>
