@extends('layouts.app')

@section('content')
<div style="margin-right:260px;padding:24px;">
  <div class="digital-background"></div>

  <header style="position:relative;z-index:10;padding-top:16px;padding-bottom:12px;">
    <div style="padding:0 16px;">
      <a href="/investment-levels" class="btn-secondary" style="border-color:#22d3ee;color:#22d3ee;">← الرجوع للمستويات</a>
      <h1 style="font-size:28px;color:#fff;margin-bottom:8px;" class="glow-text">تأكيد طلب الاستثمار</h1>
      <p style="color:#cbd5e1;">راجع تفاصيل الحزمة وبياناتك قبل إرسال الطلب</p>
    </div>
  </header>

  <section style="position:relative;z-index:10;padding-bottom:24px;">
    <div style="padding:0 16px;max-width:1000px;margin:0 auto;display:grid;grid-template-columns:repeat(2,1fr);gap:16px;">
      <!-- Package Summary -->
      <div class="glass-card" style="padding:16px;">
        <h3 style="color:#fff;margin-bottom:10px;display:flex;align-items:center;gap:8px;"><svg width="20" height="20" viewBox="0 0 20 20" fill="#22d3ee"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg> بيانات الحزمة</h3>
        <div style="display:grid;gap:8px;">
          <div style="display:flex;justify-content:space-between;background:rgba(31,41,55,.5);padding:10px;border-radius:10px;">
            <span style="color:#9ca3af;">المستوى</span>
            <span style="color:#fff;font-weight:700;">{{ $data['level'] ?? 'غير محدد' }}</span>
          </div>
          <div style="display:flex;justify-content:space-between;background:rgba(31,41,55,.5);padding:10px;border-radius:10px;">
            <span style="color:#9ca3af;">المبلغ</span>
            <span style="color:#22d3ee;font-weight:700;">{{ number_format($data['amount'] ?? 0) }} ريال</span>
          </div>
          <div style="display:flex;justify-content:space-between;background:rgba(31,41,55,.5);padding:10px;border-radius:10px;">
            <span style="color:#9ca3af;">نسبة الربح</span>
            <span style="color:#fbbf24;font-weight:700;">{{ $data['rate'] ?? 0 }}%</span>
          </div>
          <div style="display:flex;justify-content:space-between;background:rgba(31,41,55,.5);padding:10px;border-radius:10px;">
            <span style="color:#9ca3af;">المدة</span>
            <span style="color:#fff;font-weight:700;">{{ $data['duration'] ?? '1 شهر' }}</span>
          </div>
        </div>
      </div>

      <!-- Investor Summary -->
      <div class="glass-card" style="padding:16px;">
        <h3 style="color:#fff;margin-bottom:10px;display:flex;align-items:center;gap:8px;"><svg width="20" height="20" viewBox="0 0 20 20" fill="#fbbf24"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg> بيانات المستثمر</h3>
        <div style="display:grid;gap:8px;">
          <div style="display:flex;justify-content:space-between;background:rgba(31,41,55,.5);padding:10px;border-radius:10px;">
            <span style="color:#9ca3af;">الاسم الكامل</span>
            <span style="color:#fff;font-weight:700;">{{ $investor->full_name ?? $user->full_name ?? '-' }}</span>
          </div>
          <div style="display:flex;justify-content:space-between;background:rgba(31,41,55,.5);padding:10px;border-radius:10px;">
            <span style="color:#9ca3af;">رقم الهوية</span>
            <span style="color:#fff;font-weight:700;">{{ $user->national_id ?? ($investor->national_id ?? '-') }}</span>
          </div>
          <div style="display:flex;justify-content:space-between;background:rgba(31,41,55,.5);padding:10px;border-radius:10px;">
            <span style="color:#9ca3af;">الجوال</span>
            <span style="color:#fff;font-weight:700;">{{ $investor->phone ?? $user->phone ?? '-' }}</span>
          </div>
          <div style="display:flex;justify-content:space-between;background:rgba(31,41,55,.5);padding:10px;border-radius:10px;">
            <span style="color:#9ca3af;">البريد</span>
            <span style="color:#fff;font-weight:700;">{{ $investor->email ?? $user->email ?? '-' }}</span>
          </div>
          <div style="display:flex;justify-content:space-between;background:rgba(31,41,55,.5);padding:10px;border-radius:10px;">
            <span style="color:#9ca3af;">IBAN</span>
            <span style="color:#fff;font-weight:700;">{{ $investor->bank_account ?? '-' }}</span>
          </div>
        </div>
        <div style="text-align:right;margin-top:10px;">
          <a href="{{ route('investor.profile') }}" class="btn-secondary" style="border-color:#64748b;color:#cbd5e1;">تعديل البيانات</a>
        </div>
      </div>

      <!-- Submit form -->
      <div class="glass-card" style="padding:16px;grid-column:1/-1;">
        <form method="POST" action="{{ route('investment.submit') }}">
          @csrf
          <input type="hidden" name="amount" value="{{ $data['amount'] ?? 0 }}" />
          <input type="hidden" name="rate" value="{{ $data['rate'] ?? 0 }}" />
          <input type="hidden" name="duration_months" value="1" />
          <div style="display:grid;gap:10px;">
            <label style="display:block;color:#cbd5e1;">ملاحظة (اختياري)</label>
            <textarea name="notes" rows="3" class="form-control" style="background:#1f2937;border:1px solid #4b5563;color:#fff;padding:10px;border-radius:8px;resize:none;" placeholder="اكتب أي ملاحظات إضافية"></textarea>
            <button type="submit" class="btn-primary" style="font-size:18px;padding:12px 36px;">🚀 إرسال طلب الاستثمار</button>
          </div>
        </form>
      </div>
    </div>
  </section>
</div>
@endsection
