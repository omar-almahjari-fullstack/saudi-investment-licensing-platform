@extends('layouts.app')

@section('content')
<div style="margin-left:260px;padding:24px;">
  <div class="digital-background"></div>
  <header style="position:relative;z-index:10;padding-top:16px;padding-bottom:12px;">
    <div style="padding:0 16px;">
      <a href="/" class="btn-secondary" style="border-color:#22d3ee;color:#22d3ee;">← العودة للرئيسية</a>
      <h1 style="font-size:32px;color:#fff;margin-bottom:8px;" class="glow-text">الدفع - إتمام التسجيل</h1>
      <p style="color:#cbd5e1;font-size:18px;">يرجى إتمام عملية الدفع لإكمال التسجيل كمستثمر</p>
    </div>
  </header>

  <section style="position:relative;z-index:10;padding-bottom:24px;">
    <div style="padding:0 16px;max-width:800px;margin:0 auto;">
      <div class="glass-card" style="padding:16px;margin-bottom:16px;">
        <h3 style="color:#fff;margin-bottom:12px;">ملخص التسجيل</h3>
        <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:10px;color:#cbd5e1;">
          <div>
            <div>الاسم: <span style="color:#06b6d4;font-weight:700;">{{ $onboarding['full_name'] ?? '' }}</span></div>
            <div>المبلغ المتوقع: <span style="color:#06b6d4;font-weight:700;">{{ $onboarding['expected_amount'] ?? '-' }}</span></div>
          </div>
          <div>
            <div>مدة الاستثمار: <span style="color:#fbbf24;font-weight:700;">{{ $onboarding['investment_duration'] ?? '-' }}</span></div>
          </div>
        </div>
      </div>

      <form method="POST" action="{{ route('checkout.pay') }}" class="glass-card" style="padding:16px;">
        @csrf
        <h3 style="color:#fff;margin-bottom:12px;">تفاصيل الدفع</h3>
        <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;">
          <div>
            <label for="amount" style="display:block;color:#cbd5e1;margin-bottom:6px;">المبلغ (ر.س)</label>
            <input id="amount" name="amount" type="number" step="0.01" value="{{ number_format($suggestedAmount, 2, '.', '') }}" style="width:100%;background:#1f2937;border:1px solid #4b5563;color:#fff;padding:10px;border-radius:8px;" />
          </div>
          <div>
            <label style="display:block;color:#cbd5e1;margin-bottom:6px;">طريقة الدفع</label>
            <select style="width:100%;background:#1f2937;border:1px solid #4b5563;color:#fff;padding:10px;border-radius:8px;">
              <option>بطاقة ائتمانية</option>
              <option>مدى</option>
              <option>تحويل بنكي</option>
            </select>
          </div>
        </div>
        <div style="margin-top:16px;">
          <button type="submit" class="btn-primary" style="padding:12px 24px;">ادفع الآن</button>
          <a href="/" class="btn-secondary" style="margin-inline-start:10px;border-color:#64748b;color:#cbd5e1;">إلغاء</a>
        </div>
      </form>
    </div>
  </section>
</div>
@endsection
