@extends('layouts.app')

@section('content')
<div class="container">
    <div class="mobile-breadcrumb py-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">الرئيسية</a></li>
                <li class="breadcrumb-item active" aria-current="page">بوابة الاستثمار الصغيرة</li>
            </ol>
        </nav>
    </div>
</div>
<div style="margin-right:260px;padding:24px;">
  <div class="digital-background"></div>

  @php
    $title = 'بوابة الاستثمار الصغيرة';
    $subtitle = match($level){
      'beginner' => 'حزم مناسبة للمبتدئين من 1,000 إلى 10,000 ريال',
      'intermediate' => 'حزم المستوى المتوسط من 15,000 إلى 45,000 ريال',
      default => 'حزم استثمارية مضمونة من 1,000 إلى 45,000 ريال سعودي',
    };
    $monthlyReturn = 'عائد شهري مضمون';
    $investNow = 'استثمر الآن';
    $profit = 'ربح';
    $totalReturn = 'إجمالي العائد';
  @endphp

  <!-- Header -->
  <header class="text_center_title" style="position:relative;z-index:10;padding-top:16px;padding-bottom:12px;">
    <div style="padding:0 16px;">
      <h1 style="font-size:32px;color:#fff;margin-bottom:8px;" class="glow-text">{{ $title }}</h1>
      <p style="color:#cbd5e1;font-size:18px;margin-bottom:16px;">{{ $subtitle }}</p>

      <!-- Summary Stats -->
      <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:18px;">
        <div class="glass-card" style="text-align:center;padding:16px;">
          <div style="font-size:24px;color:#22c55e;font-weight:700;">{{ number_format($minAmount) }}</div>
          <div style="color:#cbd5e1;font-size:12px;">الحد الأدنى</div>
        </div>
        <div class="glass-card" style="text-align:center;padding:16px;">
          <div style="font-size:24px;color:#60a5fa;font-weight:700;">{{ number_format($maxAmount) }}</div>
          <div style="color:#cbd5e1;font-size:12px;">الحد الأقصى</div>
        </div>
        <div class="glass-card" style="text-align:center;padding:16px;">
          <div style="font-size:24px;color:#fbbf24;font-weight:700;">{{ $maxRate }}%</div>
          <div style="color:#cbd5e1;font-size:12px;">أعلى عائد</div>
        </div>
        <div class="glass-card" style="text-align:center;padding:16px;">
          <div style="font-size:24px;color:#22d3ee;font-weight:700;">30</div>
          <div style="color:#cbd5e1;font-size:12px;">يوم</div>
        </div>
      </div>
    </div>
  </header>

  <!-- Investment Packages -->
  <section style="position:relative;z-index:10;padding-bottom:24px;">
    <div style="padding:0 16px;">
      <div style="text-align:center;margin-bottom:18px;">
        <h2 style="font-size:24px;color:#fff;margin-bottom:8px;font-weight:700;">حزم الاستثمار المتاحة</h2>
        <p style="color:#cbd5e1;">{{ $monthlyReturn }}</p>
      </div>

      <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;">
        @php
          $currentUserId = optional(\Illuminate\Support\Facades\Auth::user())->id ?? session('user_id');
          $loggedIn = !empty($currentUserId);
          $hasInvestor = $loggedIn ? \App\Models\Investor::where('user_id', $currentUserId)->exists() : false;
        @endphp
        @foreach($packages as $index => $pkg)
          @php
            $amount = (float) $pkg->amount;
            $rate = (float) $pkg->profit_rate;
            $profitValue = round($amount * $rate / 100);
            $total = $amount + $profitValue;
          @endphp
          @php
            if (!$loggedIn) {
              // Not logged in: go to legacy auth login page
              $confirmUrl = route('auth.login.form');
            } elseif (!$hasInvestor) {
              // Logged in but no investor profile yet: go to register investor
              $confirmUrl = route('register.investor');
            } else {
              // Ready to confirm
              $query = [
                'amount' => $amount,
                'rate' => $rate,
              ];
              if (!empty($level)) { $query['level'] = $level; }
              $durationMonths = (int) ($pkg->duration_months ?? 1);
              $query['duration'] = $durationMonths.' شهر';
              $confirmUrl = route('investment.confirm').'?'.http_build_query($query);
            }
          @endphp
          <div class="glass-card" style="cursor:pointer;border:2px solid transparent;transition:all .2s;" onclick="window.location.href='{{ $confirmUrl }}'" onmouseover="this.style.borderColor='#22d3ee'" onmouseout="this.style.borderColor='transparent'">
            <div style="text-align:center;padding:14px 14px 0 14px;">
              @if($index >= 10)
                <div style="display:inline-block;background:#fbbf24;color:#000;padding:2px 8px;border-radius:8px;font-size:12px;font-weight:700;margin-bottom:6px;">الأكثر ربحاً</div>
              @endif
              <div style="font-size:22px;color:#22d3ee;font-weight:700;margin-bottom:8px;">{{ number_format($amount) }} ريال</div>
            </div>
            <div style="text-align:center;padding:0 14px 14px 14px;">
              <div style="background:linear-gradient(90deg,#fbbf24,#f97316);color:#000;border-radius:999px;padding:10px 14px;margin-bottom:10px;">
                <div style="font-size:24px;font-weight:800;">{{ $rate }}%</div>
                <div style="font-size:12px;font-weight:600;">عائد شهري</div>
              </div>
              
              <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:6px;">
                <span style="color:#9ca3af;">{{ $profit }}:</span>
                <span style="color:#22c55e;font-weight:700;">+{{ number_format($profitValue) }} ريال</span>
              </div>
              <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:10px;">
                <span style="color:#9ca3af;">{{ $totalReturn }}:</span>
                <span style="color:#fff;font-weight:700;">{{ number_format($total) }} ريال</span>
              </div>
              <a class="btn-primary" style="display:block;text-align:center;font-size:16px;padding:10px 0;" href="{{ $confirmUrl }}">🚀 {{ $investNow }}</a>
              <div style="font-size:11px;color:#9ca3af;margin-top:8px;">
                <div style="display:flex;justify-content:center;align-items:center;gap:6px;"><svg width="12" height="12" viewBox="0 0 20 20" fill="#22c55e"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> عائد مضمون 100%</div>
                <div style="display:flex;justify-content:center;align-items:center;gap:6px;"><svg width="12" height="12" viewBox="0 0 20 20" fill="#22c55e"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> بداية فورية</div>
              </div>
            </div>
          </div>
        @endforeach
      </div>

      <!-- CTA Section -->
      <div style="text-align:center;margin-top:24px;">
        <div class="glass-card" style="max-width:800px;margin:0 auto;padding:20px;text-align:right;">
          <h3 style="font-size:22px;color:#fff;margin-bottom:10px;font-weight:800;">لماذا تختار الاستثمار الصغير؟</h3>
          <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;">
            <div style="display:grid;gap:8px;">
              <div style="display:flex;align-items:center;color:#cbd5e1;"><svg width="16" height="16" viewBox="0 0 20 20" fill="#22c55e" style="margin-left:6px;"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> بداية مثالية للمستثمرين الجدد</div>
              <div style="display:flex;align-items:center;color:#cbd5e1;"><svg width="16" height="16" viewBox="0 0 20 20" fill="#22c55e" style="margin-left:6px;"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> مخاطر منخفضة جداً</div>
              <div style="display:flex;align-items:center;color:#cbd5e1;"><svg width="16" height="16" viewBox="0 0 20 20" fill="#22c55e" style="margin-left:6px;"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> عوائد سريعة خلال 30 يوم</div>
            </div>
            <div style="display:grid;gap:8px;">
              <div style="display:flex;align-items:center;color:#cbd5e1;"><svg width="16" height="16" viewBox="0 0 20 20" fill="#22c55e" style="margin-left:6px;"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> إجراءات بسيطة وسريعة</div>
              <div style="display:flex;align-items:center;color:#cbd5e1;"><svg width="16" height="16" viewBox="0 0 20 20" fill="#22c55e" style="margin-left:6px;"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> دعم فني متخصص</div>
              <div style="display:flex;align-items:center;color:#cbd5e1;"><svg width="16" height="16" viewBox="0 0 20 20" fill="#22c55e" style="margin-left:6px;"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> شفافية كاملة في العمليات</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
@endsection
