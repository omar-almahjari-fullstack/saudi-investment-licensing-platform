@extends('layouts.app')

@section('content')
<div style="margin-left:260px;padding:24px;">
  <div class="digital-background"></div>

  <header style="position:relative;z-index:10;padding-top:16px;padding-bottom:12px;">
    <div style="padding:0 16px;">
      <h1 style="font-size:32px;color:#fff;margin-bottom:8px;" class="glow-text">تم التسجيل بنجاح ✅</h1>
  <p style="color:#cbd5e1;font-size:18px;">شكراً لانضمامك إلى منصة عوائد الخليج للاستثمار السعودي</p>
    </div>
  </header>

  <section style="position:relative;z-index:10;padding-bottom:24px;">
    <div style="padding:0 16px;max-width:800px;margin:0 auto;">
      <div class="glass-card" style="max-width:640px;margin:40px auto;padding:24px;text-align:center;">
        <div style="width:80px;height:80px;margin:0 auto 16px;display:flex;align-items:center;justify-content:center;border-radius:50%;background:linear-gradient(90deg,#22c55e,#14b8a6);">
          <svg width="40" height="40" viewBox="0 0 20 20" fill="#fff"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        </div>
        <h2 style="font-size:22px;color:#fff;margin-bottom:8px;">تم تسجيلك كمستثمر بنجاح!</h2>
        <p style="color:#cbd5e1;margin-bottom:16px;">مرحباً <span style="color:#06b6d4;font-weight:700;">{{ $onboarding['full_name'] ?? '' }}</span>، يمكنك الآن البدء في استكشاف الفرص الاستثمارية.</p>

        <div style="background:rgba(31,41,55,0.5);padding:16px;border-radius:10px;margin-bottom:16px;text-align:right;">
          <h3 style="color:#fff;font-weight:700;margin-bottom:10px;">تفضيلاتك الاستثمارية:</h3>
          <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:10px;font-size:14px;">
            <div>
              <span style="color:#9ca3af;">المبلغ المتوقع:</span>
              <div style="color:#06b6d4;font-weight:600;">{{ $onboarding['expected_amount'] ?? '-' }}</div>
            </div>
            <div>
              <span style="color:#9ca3af;">المدة المفضلة:</span>
              <div style="color:#fbbf24;font-weight:600;">{{ $onboarding['investment_duration'] ?? '-' }}</div>
            </div>
          </div>
        </div>

        <div style="display:grid;gap:10px;max-width:360px;margin:0 auto;">
          <a href="/investment-levels" class="btn-primary" style="display:block;">استكشاف الفرص الاستثمارية</a>
          <div style="display:flex;gap:10px;justify-content:center;">
            <a href="/" class="btn-secondary" style="display:block;border-color:#64748b;color:#cbd5e1;">العودة للرئيسية</a>
            <a href="{{ route('investor.dashboard') }}" class="btn-secondary" style="display:block;border-color:#64748b;color:#cbd5e1;">تتبع الطلب</a>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection
