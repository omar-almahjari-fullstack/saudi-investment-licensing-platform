@extends('layouts.app')

@section('content')



  <div style="padding:16px 24px 32px;">

    <!-- الخلفية الرقمية والنجوم أزيلت بناءً على الطلب -->

  <div style="padding-top:8px;text-align:center;">
  <h1 style="font-size:41px;color:#ffffff;margin-bottom:8px;" class="glow-text" data-step="1" data-intro="مرحباً! هذا عنوان المنصة — هنا مكان عرض نبذة سريعة عن المنصة وأهدافها.">{{ isset($platform_setting['name']) ? $platform_setting['name'] : (config('app.name') ?? 'عوائد الخليج للاستثمار السعودي') }}</h1>
      <h2 style="color:#7dd3fc;margin-bottom:12px;">{{ isset($platform_setting['description']) ? $platform_setting['description'] : (config('app.description') ?? 'منصة التراخيص الموحدة') }}</h2>
      <p style="color:#cbd5e1;max-width:900px;margin:0 auto;">احصل على تراخيصك وابدأ استثمارك في المملكة العربية
        السعودية من خلال منصة واحدة</p>
        </div>

        <!-- الوصف التاريخي -->
        <section style="margin-top:28px;max-width:900px;margin-right:auto;margin-left:auto;">
            <div class="glass-card" style="padding:20px;">
                <h3 style="font-size:22px;color:#f59e0b;margin-bottom:8px;font-weight:700;">تاريخنا العريق</h3>
                <p style="color:#cbd5e1;line-height:1.9;font-size:16px;">
                    تأسست عوائد الخليج للاستثمار السعودي في عام 2018 كأول منصة رقمية متخصصة في الاستثمارات قصيرة المدى في المملكة.
                    منذ انطلاقتها، نجحت المنصة في خدمة أكثر من <span style="color:#06b6d4;font-weight:700;">378
                        مستثمر</span> وإصدار
                    <span style="color:#fbbf24;font-weight:700;"> 743 ترخيص تجاري</span>، محققة بذلك نسبة نجاح استثنائية
                    تبلغ
                    <span style="color:#2e9453;font-weight:700;"> 98%</span>.
                </p>
                <br />
                <p style="color:#cbd5e1;line-height:1.9;font-size:16px;">
                    على مدى 7 سنوات من العمل المتواصل، ترسخت مكانة المنصة كرائدة في مجال الاستثمار الآمن والمضمون،
                    حيث ساهمت في تحقيق أحلام المئات من المستثمرين السعوديين في بناء ثرواتهم وتنمية أصولهم المالية من خلال
                    استثمارات موثوقة وعوائد مجزية تتماشى مع رؤية السعودية 2030.
                </p>
            </div>
        </section>

        <!-- المؤشرات -->
        <section style="margin-top:24px;">
            <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:18px;max-width:1100px;margin:0 auto;">
                <div class="glass-card" style="padding:18px;text-align:center;" data-step="2" data-intro="هذه المؤشرات تعرض ملخص سريع عن المستخدمين، التراخيص ونسبة النجاح — راقبها للحصول على لمحة عن أداء المنصة.">
                    <div style="font-size:28px;color:#06b6d4;font-weight:700;">
                       635{{ number_format($stats['total_users']) }}</div>
                    <div style="color:#cbd5e1;margin-top:6px;">مستثمر مسجل</div>
                </div>
                <div class="glass-card" style="padding:18px;text-align:center;">
                    <div style="font-size:28px;color:#06b6d4;font-weight:700;">
                        235{{ number_format($stats['total_licenses']) }}</div>
                    <div style="color:#cbd5e1;margin-top:6px;">ترخيص تجاري</div>
                </div>
                <div class="glass-card" style="padding:18px;text-align:center;">
                    <div style="font-size:28px;color:#fbbf24;font-weight:700;">{{ $stats['success_rate'] }}%</div>
                    <div style="color:#cbd5e1;margin-top:6px;">نسبة نجاح</div>
                </div>
                <div class="glass-card" style="padding:18px;text-align:center;">
                    <div style="font-size:28px;color:#22c55e;font-weight:700;">24/7</div>
                    <div style="color:#cbd5e1;margin-top:6px;">الدعم</div>
                </div>
            </div>
        </section>

        <!-- البطاقات الرئيسية للإجراءات -->
        <section style="margin-top:32px;max-width:1100px;margin-right:auto;margin-left:auto;">
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:18px;">
                <!-- ترخيص تجاري -->
                <div class="glass-card" style="padding:20px;cursor:pointer;" onclick="window.location.href='/license'" data-step="3" data-intro="اضغط هنا لبدء طلب ترخيص تجاري — سيأخذك النموذج لتعبئة بيانات الترخيص المطلوبة.">
                    <div style="text-align:center;">
                        <div
                            style="width:64px;height:64px;margin:0 auto 12px;display:flex;align-items:center;justify-content:center;border-radius:50%;background:linear-gradient(90deg,#3b82f6,#06b6d4);">
                            <svg width="28" height="28" viewBox="0 0 20 20" fill="#fff">
                                <path fill-rule="evenodd"
                                    d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V4a2 2 0 00-2-2H6zm1 2a1 1 0 000 2h6a1 1 0 100-2H7zm6 7a1 1 0 011 1v3a1 1 0 11-2 0v-3a1 1 0 011-1zm-3 3a1 1 0 100 2h.01a1 1 0 100-2H10zm-4 1a1 1 0 011-1h.01a1 1 0 110 2H7a1 1 0 01-1-1zm1-4a1 1 0 100 2h.01a1 1 0 100-2H7zm2 0a1 1 0 100 2h.01a1 1 0 100-2H9zm4 0a1 1 0 100 2h.01a1 1 0 100-2h-.01z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <h3 style="color:#fff;margin-bottom:8px;">طلب ترخيص تجاري</h3>
                        <p style="color:#cbd5e1;margin-bottom:12px;">للمستثمرين الراغبين في تأسيس أو تجديد النشاط التجاري
                        </p>
                    </div>
                </div>



                <!-- تسجيل مستثمر جديد -->
             <div class="glass-card" style="padding:20px;text-align:center;cursor:pointer;"
              onclick="(function(){ var isAuth = {{ (auth()->check() || session()->has('user_id')) ? 'true' : 'false' }}; if(isAuth){ window.location.href = '/register-investor'; } else { window.location.href = '{{ route('auth.login.form') }}?redirect=' + encodeURIComponent('/register-investor'); } })();" data-step="4" data-intro="للانضمام كمستثمر جديد، استخدم هذا الزر لتسجيل حسابك والبدء في الاستثمار.">
                    <div
                        style="width:64px;height:64px;margin:0 auto 12px;display:flex;align-items:center;justify-content:center;border-radius:50%;background:linear-gradient(90deg,#22c55e,#14b8a6);">
                        <svg width="28" height="28" viewBox="0 0 20 20" fill="#fff">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <h3 style="color:#fff;margin-bottom:8px;">تسجيل مستثمر جديد</h3>
                    <p style="color:#cbd5e1;margin-bottom:12px;">للأفراد والكيانات للانضمام إلى منظومة الاستثمار</p>
                    <a href="{{ (auth()->check() || session()->has('user_id')) ? '/register-investor' : (route('auth.login.form') . '?redirect=' . urlencode('/register-investor')) }}" class="btn-primary" style="display:inline-block;">👤 سجل الآن</a>
                </div>



                 <!-- الاستثمار السريع -->
                <div class="glass-card" style="padding:20px;border:2px solid #f59e0b;position:relative;overflow:hidden;" data-step="5" data-intro="هنا تستطيع البدء بالاستثمار السريع — اختر نوع الاستثمار الصغير أو الكبير بحسب ميزانيتك.">
                    <div
                        style="position:absolute;top:10px;right:10px;background:#f59e0b;color:#000;padding:4px 8px;border-radius:8px;font-weight:700;">
                        الأكثر بروزاً</div>
                    <div style="text-align:center;">
                        <div
                            style="width:64px;height:64px;margin:0 auto 12px;display:flex;align-items:center;justify-content:center;border-radius:50%;background:linear-gradient(90deg,#f59e0b,#f97316);">
                            <svg width="28" height="28" viewBox="0 0 20 20" fill="#fff">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 style="color:#fff;margin-bottom:8px;">ابدأ الاستثمار السريع</h3>
                        <p style="color:#cbd5e1;margin-bottom:12px;">لتحديد نوع الاستثمار المناسب</p>
                        <div style="display:flex;gap:8px;">
                            <a href="/small-investment" class="btn-primary" style="flex:1;text-align:center;">الاستثمار
                                الصغير</a>
                            <a href="/large-investment" class="btn-secondary" style="flex:1;text-align:center;">الاستثمار
                                الكبير</a>
                        </div>
                    </div>
                </div>



            </div>
        </section>
    <br>
    <br>
    <br>

    <!-- مركز مستويات الاستثمار (نسق مطابق لصفحة المستويات) -->
    @include('investment-levels-section')



        <!-- بوابة الاستثمار الصغيرة -->
        <div style="margin-right:260px;padding:24px;">
            <div class=""></div>


              @php
    // Ensure $level is defined (may come from query string or outer scope).
    $level = $level ?? request()->query('level') ?? null;

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
            <header style="position:relative;z-index:10;padding-top:16px;padding-bottom:12px;">
                <div >
          <h1 style="font-size:32px;color:#ffffff;margin-bottom:8px;text-align:center;">بوابة
                        الاستثمار الصغيرة</h1>
                    <p style="color:#cbd5e1;font-size:18px;text-align:center;margin-bottom: 20px;">حزم استثمارية مضمونة من
                        1,000 إلى 45,000 ريال سعودي</p>
                    <!-- Summary Stats -->
                    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:18px;">
                        <div class="glass-card" style="text-align:center;padding:16px;">
                            <div style="font-size:24px;color:#22c55e;font-weight:700;">1,000</div>
                            <div style="color:#cbd5e1;font-size:12px;">الحد الأدنى</div>
                        </div>
                        <div class="glass-card" style="text-align:center;padding:16px;">
                            <div style="font-size:24px;color:#60a5fa;font-weight:700;">45,000</div>
                            <div style="color:#cbd5e1;font-size:12px;">الحد الأقصى</div>
                        </div>
                        <div class="glass-card" style="text-align:center;padding:16px;">
                            <div style="font-size:24px;color:#fbbf24;font-weight:700;">47%</div>
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
    <div >
      <div style="text-align:center;margin-bottom:18px;">
  <h2 style="font-size:24px;color:#fff;margin-bottom:8px;font-weight:700;" data-step="6" data-intro="قائمة حزم الاستثمار المتاحة — اختر الباقة التي تناسب ميزانيتك للعرض الكامل وتقديم الطلب.">حزم الاستثمار المتاحة</h2>
        <p style="color:#cbd5e1;">{{ $monthlyReturn }}</p>
      </div>

      <div class="hed" style="margin-bottom: 30px;">
         <a href="{{ route('investment.small')}}" class="btn btn-primary btn-filter" style="padding: 4px 13px; cursor:pointer"> عرض المزيد</a>

      </div>


      <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;">
        @php
          $currentUserId = optional(\Illuminate\Support\Facades\Auth::user())->id ?? session('user_id');
          $loggedIn = !empty($currentUserId);
          $hasInvestor = $loggedIn ? \App\Models\Investor::where('user_id', $currentUserId)->exists() : false;
        @endphp
     @php
// فلترة الحزم بحيث نعرض فقط التي المبلغ فيها 30000 أو أكثر
$topPackages = $packages->filter(function ($pkg) {
    return $pkg->amount >= 30000;
});
@endphp

@foreach($topPackages as $pkg)
  @php
    $amount = (float) $pkg->amount;
    $rate = (float) $pkg->profit_rate;
    $profitValue = round($amount * $rate / 100);
    $total = $amount + $profitValue;

    if (!$loggedIn) {
      $confirmUrl = route('auth.login.form');
    } elseif (!$hasInvestor) {
      $confirmUrl = route('register.investor');
    } else {
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

  <div class="glass-card"
       style="cursor:pointer;border:2px solid transparent;transition:all .2s;"
       onclick="window.location.href='{{ $confirmUrl }}'"
       onmouseover="this.style.borderColor='#22d3ee'"
       onmouseout="this.style.borderColor='transparent'">

    <div style="text-align:center;padding:14px 14px 0 14px;">
      {{-- ✅ الوسم الأصفر للباقات ذات السعر المرتفع --}}
      <div style="display:inline-block;background:#fbbf24;color:#000;padding:4px 10px;border-radius:8px;font-size:13px;font-weight:700;margin-bottom:8px;">
        🌟 الأكثر ربحاً
      </div>

      <div style="font-size:22px;color:#22d3ee;font-weight:700;margin-bottom:8px;">
        {{ number_format($amount) }} ريال
      </div>
    </div>

    <div style="text-align:center;padding:0 14px 14px 14px;">
      <div style="background:linear-gradient(90deg,#fbbf24,#f97316);color:#000;border-radius:999px;padding:10px 14px;margin-bottom:10px;">
        <div style="font-size:24px;font-weight:800;">{{ $rate }}%</div>
        <div style="font-size:12px;font-weight:600;">عائد شهري</div>
      </div>

      <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:6px;">
        <span style="color:#9ca3af;">الربح:</span>
        <span style="color:#22c55e;font-weight:700;">+{{ number_format($profitValue) }} ريال</span>
      </div>
      <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:10px;">
        <span style="color:#9ca3af;">الإجمالي:</span>
        <span style="color:#fff;font-weight:700;">{{ number_format($total) }} ريال</span>
      </div>
      <a class="btn-primary" style="display:block;text-align:center;font-size:16px;padding:10px 0;" href="{{ $confirmUrl }}">🚀 {{ $investNow }}</a>
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




        <!-- مركز الاستثمار الكبير -->
        {{-- <div style="margin-right:260px;padding:24px;">
            <div class=""></div>
  @php
    $title = 'مركز الاستثمار الكبير';
    $subtitle = 'استثمارات احترافية تبدأ من 50,000 ريال سعودي';
    $tiers = [
      ['range' => '50,000 - 74,999 ريال',  'rate' => '45%', 'example' => '50,000 ريال → 22,500 ريال ربح'],
      ['range' => '75,000 - 99,999 ريال',  'rate' => '46%', 'example' => '75,000 ريال → 34,500 ريال ربح'],
      ['range' => '100,000 - 199,999 ريال','rate' => '47%', 'example' => '100,000 ريال → 47,000 ريال ربح'],
      ['range' => '200,000 - 499,999 ريال','rate' => '48%', 'example' => '200,000 ريال → 96,000 ريال ربح'],
      ['range' => '500,000+ ريال',         'rate' => '50%', 'example' => '500,000 ريال → 250,000 ريال ربح'],
    ];
  @endphp

  @php
    // Resolve server-side flags for JS
    $srvUser = auth()->user();
    if (!$srvUser && session()->has('user_id')) { $srvUser = \App\Models\User::find(session('user_id')); }
    $srvHasInvestor = false;
    $srvHasInvestments = false;
    if ($srvUser) {
      $inv = \App\Models\Investor::where('user_id', $srvUser->id)->first();
      if ($inv) {
        $srvHasInvestor = true;
        $srvHasInvestments = \App\Models\Investment::where('investor_id', $inv->id)->exists();
      }
    }
  @endphp

            <!-- Header -->
            <header style="position:relative;z-index:10;padding-top:16px;padding-bottom:12px;">
                <div style="padding:0 16px;">
          <h1 style="font-size:32px;color:#ffffff;margin-bottom:8px;text-align:center;">مركز
                        الاستثمار الكبير</h1>
                    <p style="color:#cbd5e1;font-size:18px;text-align:center;margin-bottom:20px;">استثمارات احترافية تبدأ
                        من 50,000 ريال سعودي</p>

                    <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:10px;margin-bottom:16px;">
                        @foreach ($tiers as $t)
                            <div class="glass-card" style="text-align:center;padding:12px;">
                                <div style="color:#22d3ee;font-weight:700;">{{ $t['rate'] }}</div>
                                <div style="color:#9ca3af;font-size:12px;">{{ $t['range'] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </header>

            <!-- Content -->
  <!-- Content -->
  <section style="position:relative;z-index:10;padding-bottom:24px;">
    <div style="padding:0 16px;max-width:1100px;margin:0 auto;">
      <!-- Calculator -->
      <div class="glass-card" style="padding:16px;margin-bottom:16px;max-width:760px;margin-left:auto;margin-right:auto;">
        <h3 style="color:#fff;text-align:center;font-size:20px;margin-bottom:12px;">حاسبة الاستثمار</h3>
        <div style="max-width:460px;margin:0 auto;">
          <label for="large-amount" style="display:block;color:#cbd5e1;margin-bottom:6px;font-size:16px;">أدخل مبلغ الاستثمار</label>
          <div style="position:relative;">
       <input id="large-amount" type="number" min="50000" placeholder="50000"
         class="form-control"
         data-step="7" data-intro="في هذه الحقول يمكنك تجربة حاسبة الاستثمار لمعرفة العائد التقريبي بسرعة." style="width:100%;background:#1f2937;border:1px solid #4b5563;color:#fff;padding:14px 48px 14px 16px;border-radius:10px;font-size:18px;">
            <span style="position:absolute;right:12px;top:50%;transform:translateY(-50%);color:#9ca3af;">ريال</span>
          </div>
          <div id="calc-error" style="color:#f87171;font-size:13px;margin-top:6px;display:none;"></div>
          <button id="btn-calc" class="btn-primary" style="width:100%;margin-top:12px;font-size:16px;padding:12px 0;">حساب العوائد</button>
        </div>
      </div>

      <!-- Calculation Results -->
      <div id="calc-result" class="glass-card" style="display:none;border:2px solid #fbbf24;margin-bottom:16px;">
        <div style="padding:16px;display:flex;justify-content:space-between;align-items:center;">
          <h4 style="color:#fff;font-size:18px;margin:0;">نتائج الحساب</h4>
          <span style="background:#fbbf24;color:#000;border-radius:8px;padding:4px 8px;font-weight:700;font-size:12px;">محسوبة</span>
        </div>
        <div style="padding:0 16px 16px 16px;display:grid;grid-template-columns:repeat(2,1fr);gap:12px;">
          <div style="display:grid;gap:8px;">
            <div style="display:flex;justify-content:space-between;padding:10px;background:rgba(31,41,55,.5);border-radius:10px;">
              <span style="color:#9ca3af;">مبلغ الاستثمار:</span>
              <span id="res-amount" style="color:#22d3ee;font-weight:700;"></span>
            </div>
            <div style="display:flex;justify-content:space-between;padding:10px;background:rgba(31,41,55,.5);border-radius:10px;">
              <span style="color:#9ca3af;">نسبة الربح:</span>
              <span id="res-rate" style="color:#fbbf24;font-weight:700;"></span>
            </div>
            <div style="display:flex;justify-content:space-between;padding:10px;background:rgba(31,41,55,.5);border-radius:10px;">
              <span style="color:#9ca3af;">المدة:</span>
              <span style="color:#fff;font-weight:700;">شهر واحد</span>
            </div>
          </div>
          <div style="display:grid;gap:8px;">
            <div style="display:flex;justify-content:space-between;padding:10px;background:rgba(22,163,74,.2);border-radius:10px;">
              <span style="color:#9ca3af;">الربح المتوقع:</span>
              <span id="res-profit" style="color:#22c55e;font-weight:700;"></span>
            </div>
            <div style="display:flex;justify-content:space-between;padding:10px;background:rgba(30,64,175,.2);border-radius:10px;">
              <span style="color:#9ca3af;">إجمالي العائد:</span>
              <span id="res-total" style="color:#60a5fa;font-weight:700;"></span>
            </div>
            <div style="display:flex;justify-content:space-between;padding:10px;background:rgba(31,41,55,.5);border-radius:10px;">
              <span style="color:#9ca3af;">مستوى المخاطر:</span>
              <span style="color:#22c55e;font-weight:700;">مضمون</span>
            </div>
          </div>
        </div>
        <div style="text-align:center;padding:0 16px 16px 16px;">

                    <a id="btn-submit-invest" href="/investment/confirm}" class="btn-primary" style="font-size:18px;padding:12px 36px;display:inline-block;">🚀 رفع طلب الاستثمار</a>

          <p style="color:#9ca3af;font-size:12px;margin-top:6px;">سيتم توجيهك لملء البيانات المطلوبة</p>
        </div>
      </div>

      <!-- Tiers details -->
      <div class="glass-card" style="padding:16px;">
        <h3 style="color:#fff;text-align:center;margin-bottom:12px;">شرائح الاستثمار والعوائد</h3>
        <div style="display:grid;gap:10px;">
          @foreach($tiers as $t)
            <div style="display:flex;justify-content:space-between;align-items:center;padding:12px;background:rgba(31,41,55,.5);border-radius:10px;">
              <div>
                <div style="color:#fff;font-weight:700;">{{ $t['range'] }}</div>
                <div style="color:#9ca3af;font-size:12px;">{{ $t['example'] }}</div>
              </div>
              <div style="color:#fbbf24;font-weight:800;font-size:22px;">{{ $t['rate'] }}</div>
            </div>
          @endforeach
        </div>
      </div>
    </div>
  </section>
        </div>


<script>
  $(function(){
    function formatNumber(n){ return (n||0).toLocaleString('en-US'); }
    function rateFor(amount){
      if(amount >= 500000) return 50;
      if(amount >= 200000) return 48;
      if(amount >= 100000) return 47;
      if(amount >= 75000)  return 46;
      return 45; // 50k - 75k
    }

      $('#btn-calc').on('click', function(){
      var val = $('#large-amount').val();
      var amt = parseFloat(val);
      if(!val || isNaN(amt)){
        $('#calc-error').text('يرجى إدخال مبلغ صحيح').show();
        $('#calc-result').hide();
        return;
      }
      if(amt < 50000){
        $('#calc-error').text('⚠️الحد الأدنى للاستثمار الكبير هو 50,000 ريال سعودي. للمبالغ الأصغر يرجى استخدام بوابة الاستثمار الصغيرة').show();
        $('#calc-result').hide();
        return;
      }
      $('#calc-error').hide();
      var pr = rateFor(amt);
      var profit = Math.round(amt * pr / 100);
      var total  = amt + profit;
      $('#res-amount').text(formatNumber(amt)+' ريال');
      $('#res-rate').text(pr+'%');
      $('#res-profit').text('+'+formatNumber(profit)+' ريال');
      $('#res-total').text(formatNumber(total)+' ريال');
      $('#calc-result').show();

  var baseConfirm = '/investment/confirm?amount=' + encodeURIComponent(Math.round(amt)) + '&rate=' + encodeURIComponent(pr) + '&isLargeA=' + 'true' + '&level=advanced';
  var isAuth = {{ $srvUser ? 'true' : 'false' }};
      var hasInvestor = {{ $srvHasInvestor ? 'true' : 'false' }};
      var hasInvestments = {{ $srvHasInvestments ? 'true' : 'false' }};
      var confirmUrl = baseConfirm;
      if(!isAuth){
        confirmUrl = '{{ route('auth.login.form') }}' + '?redirect=' + encodeURIComponent(baseConfirm);
      } else if(isAuth && (!hasInvestor || !hasInvestments)) {
        // Logged in but missing investor profile/investments: send directly to the confirmation page.
        // The confirmation route will enforce investor requirements server-side (redirecting to onboarding if needed).
        confirmUrl = baseConfirm;
      }
      $('#btn-submit-invest').attr('href', confirmUrl);
    });

    $('#large-amount').on('input', function(){
      var v = parseFloat($(this).val());
      if(!isNaN(v) && v >= 50000){ $('#calc-error').hide(); }
      else { $('#calc-error').text('الحد الأدنى: 50,000 ريال').show(); }
      $('#calc-result').hide();
      var isAuth = {{ $srvUser ? 'true' : 'false' }};
      var hasInvestor = {{ $srvHasInvestor ? 'true' : 'false' }};
      var hasInvestments = {{ $srvHasInvestments ? 'true' : 'false' }};
      // Default to pointing at the large-investment confirm URL; authentication or investor middleware will handle redirects if needed.
      var defaultConfirm = '/investment/confirm';
      if(!isAuth){
        $('#btn-submit-invest').attr('href', '{{ route('auth.login.form') }}?redirect=/investment/large');
      } else if(!hasInvestor || !hasInvestments) {
        // For consistency, point to the confirm URL — server will redirect to onboarding if investor missing
        $('#btn-submit-invest').attr('href', defaultConfirm);
      } else {
        $('#btn-submit-invest').attr('href', '#');
      }
    });
  });
</script> --}}



<style>
  .a_card{
    display:grid;
    }
    
  
  
  @media (max-width: 468px) {
  .a_card{
    display: flex;
        display: flex;
    flex-wrap: wrap; /* ✅ تسمح للعناصر بالنزول للسطر الثاني */
    justify-content: center; /* اختياري - لترتيبهم بشكل جميل */
    }
    
    .a_card_it{
        min-width: 120px;
    }
    
  
  }
</style>



                    <section style="position:relative;z-index:10;padding-bottom:24px;">
        <div style="padding:0 16px;">
            <!-- Top Featured Stories -->
            <h2 style="font-size:32px;color:#fff;margin:8px 0 12px;text-align:center;font-weight:800;">🌟 أبرز قصص النجاح المميزة</h2>

             <div class="hed" style="margin-bottom: 30px;">
            <a href="{{ route('success.stories')}}" class="btn btn-primary btn-filter" style="padding: 4px 13px; cursor:pointer;"> عرض المزيد</a>
             </div>
            <!-- Server-side render of top featured stories -->
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:16px;margin-bottom:24px;" id="top-featured">
                @foreach($topStories as $s)
                    <div class="investment-card" style="position:relative;border:4px solid; border-image: linear-gradient(90deg,#fbbf24,#f97316) 1;padding:0;"
                             data-name="{{ e($s->name) }}"
                             data-avatar="{{ e($s->avatar ?? '') }}"
                             data-location="{{ e($s->location) }}"
                             data-amount="{{ e($s->investment_amount ?? '') }}"
                             data-returns="{{ e($s->returns ?? '') }}"
                             data-profit="{{ e($s->profit_rate ?? '') }}"
                             data-story="{{ e($s->story ?? '') }}"
                             data-testimonial="{{ e($s->testimonial ?? '') }}">
                        <span style="position:absolute;top:10px;right:10px;background:linear-gradient(90deg,#fbbf24,#f97316);color:#000;padding:4px 8px;border-radius:8px;font-weight:800;">⭐ قصة مميزة</span>
                        <div style="padding:16px;text-align:center;">
                            <div style="width:80px;height:80px;margin:0 auto 8px;background:linear-gradient(90deg,#fbbf24,#f97316);border-radius:50%;display:flex;align-items:center;justify-content:center;">
                                <span style="font-size:22px;font-weight:800;color:#000;">{{ mb_substr($s->name,0,1) }}</span>
                            </div>
                            <div style="color:#fff;font-size:20px;font-weight:800">{{ $s->name }}</div>
                            <div style="color:#94a3b8">{{ $s->location }}</div>
                        </div>
                        <div style="padding:0 16px 16px 16px;">
                            <div class="a_card" style="grid-template-columns:repeat(3,1fr);gap:8px;text-align:center;margin-bottom:10px;">
                                <div class="a_card_it" style="background:rgba(31,41,55,.5);padding:10px;border-radius:10px;"><div style="color:#9ca3af;font-size:12px">الاستثمار</div><div style="color:#06b6d4;font-weight:800">{{ $s->investment_amount }}</div></div>
                                <div class="a_card_it" style="background:rgba(31,41,55,.5);padding:10px;border-radius:10px;"><div style="color:#9ca3af;font-size:12px">الأرباح</div><div style="color:#22c55e;font-weight:800">{{ $s->returns }}</div></div>
                                <div class="a_card_it" style="background:rgba(31,41,55,.5);padding:10px;border-radius:10px;"><div style="color:#9ca3af;font-size:12px">العائد</div><div style="color:#fbbf24;font-weight:800">{{ $s->profit_rate }}</div></div>
                            </div>
                            <div style="background:linear-gradient(90deg,rgba(31,41,55,.5),rgba(55,65,81,.5));padding:12px;border-radius:10px;">
                                <p style="color:#e5e7eb;font-style:italic;font-size:16px">"{{ $s->testimonial }}"</p>
                            </div>
                        </div>
                        <div style="padding:0 16px 16px 16px;">
                            <button class="btn-secondary btn-read-full" style="width:100%;border-color:#22d3ee;color:#22d3ee">اقرأ القصة كاملة</button>
                        </div>
                    </div>
                @endforeach
            </div>

            <div id="stories-stats" style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-top:24px;"></div>
        </div>
  </section>
    <!-- Modal: Read Full Story -->
  <div id="ss-full-modal" style="display:none;position:fixed;inset:0;z-index:1000;align-items:center;justify-content:center;background:rgba(0,0,0,0.6);">
    <div style="background:#0f172a;border:1px solid #334155;border-radius:8px;max-width:820px;margin:40px auto;padding:18px;color:#e6eef8;">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
        <div>
          <div id="ss-full-name" style="font-size:20px;font-weight:800;color:#fff"></div>
          <div id="ss-full-meta" style="color:#94a3b8;font-size:13px;margin-top:4px"></div>
        </div>
        <div>
          <button id="ss-full-close" style="background:transparent;border:0;color:#94a3b8;font-size:20px;">✕</button>
        </div>
      </div>
      <div style="display:flex;gap:12px;align-items:flex-start;margin-bottom:12px;">
        <div style="width:72px;height:72px;border-radius:50%;background:linear-gradient(90deg,#06b6d4,#3b82f6);display:flex;align-items:center;justify-content:center;font-weight:800;color:#fff;font-size:24px" id="ss-full-avatar">A</div>
        <div style="flex:1;">
          <div style="display:flex;gap:12px;margin-bottom:8px;">
            <div style="background:rgba(31,41,55,.5);padding:10px;border-radius:8px;text-align:center;min-width:120px;">
              <div style="color:#9ca3af;font-size:12px">الاستثمار</div>
              <div id="ss-full-amount" style="color:#06b6d4;font-weight:800"></div>
            </div>
            <div style="background:rgba(31,41,55,.5);padding:10px;border-radius:8px;text-align:center;min-width:120px;">
              <div style="color:#9ca3af;font-size:12px">العوائد</div>
              <div id="ss-full-returns" style="color:#22c55e;font-weight:800"></div>
            </div>
            <div style="background:rgba(31,41,55,.5);padding:10px;border-radius:8px;text-align:center;min-width:120px;">
              <div style="color:#9ca3af;font-size:12px">العائد</div>
              <div id="ss-full-profit" style="color:#fbbf24;font-weight:800"></div>
            </div>
          </div>
          <div id="ss-full-story" style="color:#e5e7eb;line-height:1.6;margin-bottom:10px"></div>
          <div id="ss-full-testimonial" style="background:linear-gradient(90deg,rgba(31,41,55,.5),rgba(55,65,81,.5));padding:12px;border-radius:8px;color:#cfe6ff;font-style:italic"></div>
        </div>
      </div>
      <div style="text-align:right;"><button id="ss-full-close-2" style="background:#2563eb;color:#fff;border:0;padding:8px 12px;border-radius:6px">إغلاق</button></div>
    </div>
  </div>

  <script>
  // simple client-side filter for categories
  (function(){
    function setActive(btn){ document.querySelectorAll('.filter-btn').forEach(b=>b.classList.remove('btn-primary') || b.classList.add('btn-secondary'));
    btn.classList.remove('btn-secondary'); btn.classList.add('btn-primary'); }
    document.querySelectorAll('.filter-btn').forEach(function(b){
      b.addEventListener('click', function(){
        var key = this.dataset.filter;
        setActive(this);
        document.querySelectorAll('.story-item').forEach(function(it){
          if(key==='all' || it.dataset.category===key) it.style.display = '';
          else it.style.display = 'none';
        });
      });
    });
    // Read full story modal logic
    function escapeHtml(unsafe) {
      return (unsafe || '').toString()
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
    }

    function toArabicIndicDigits(str) {
      if (!str) return str || '';
      const map = { '0':'٠','1':'١','2':'٢','3':'٣','4':'٤','5':'٥','6':'٦','7':'٧','8':'٨','9':'٩' };
      return str.toString().replace(/[0-9]/g, function(d){ return map[d]; });
    }

    function formatAmountForDisplay(amount) {
      if (!amount) return '-';
      // keep full string but convert latin digits to Arabic-Indic for consistency
      return toArabicIndicDigits(amount);
    }

    function enhanceStoryDisplay(data) {
      // Name
      document.getElementById('ss-full-name').textContent = data.name || '-';
      // Avatar initials
      var avatarEl = document.getElementById('ss-full-avatar');
      avatarEl.textContent = (data.avatar || '').toString().substring(0,2) || (data.name || '-').toString().substring(0,1);
      // Meta (location · category)
      var meta = '';
      if (data.location) meta += escapeHtml(data.location);
      if (data.category) meta += (meta ? ' · ' : '') + escapeHtml(data.category);
      document.getElementById('ss-full-meta').textContent = meta;

      // Amounts (formatted)
      document.getElementById('ss-full-amount').innerHTML = '<span>' + formatAmountForDisplay(data.amount || '-') + '</span>';
      document.getElementById('ss-full-returns').innerHTML = '<span>' + formatAmountForDisplay(data.returns || '-') + '</span>';
      document.getElementById('ss-full-profit').innerHTML = '<span>' + escapeHtml(data.profit || '-') + '</span>';

      // Story: preserve paragraphs, escape HTML
      var storyHtml = '';
      if (data.story) {
        var parts = data.story.split(/\r?\n\r?\n/); // split by blank line for paragraphs
        storyHtml = parts.map(function(p){ return '<p>' + escapeHtml(p.trim()).replace(/\n/g, '<br>') + '</p>'; }).join('');
      }
      document.getElementById('ss-full-story').innerHTML = storyHtml;

      // Testimonial
      document.getElementById('ss-full-testimonial').innerHTML = data.testimonial ? ('"' + escapeHtml(data.testimonial) + '"') : '';
    }

    function openFullStoryModal(data) {
      enhanceStoryDisplay(data);
      document.getElementById('ss-full-modal').style.display = 'flex';
    }

    function closeFullStoryModal() {
      document.getElementById('ss-full-modal').style.display = 'none';
    }

    document.addEventListener('click', function(e){
      var btn = e.target.closest && e.target.closest('.btn-read-full');
      if (btn) {
        var card = btn.closest('.investment-card');
        if (!card) return;
        var data = {
          name: card.dataset.name || '',
          avatar: card.dataset.avatar || '',
          location: card.dataset.location || '',
          amount: card.dataset.amount || '',
          returns: card.dataset.returns || '',
          profit: card.dataset.profit || '',
          story: card.dataset.story || '',
          testimonial: card.dataset.testimonial || '',
          category: card.dataset.category || ''
        };
        openFullStoryModal(data);
      }
    });

    document.getElementById('ss-full-close').addEventListener('click', closeFullStoryModal);
    document.getElementById('ss-full-close-2').addEventListener('click', closeFullStoryModal);
    // close modal on overlay click
    document.getElementById('ss-full-modal').addEventListener('click', function(e){ if (e.target === this) closeFullStoryModal(); });
  })();
</script>

  <style>
    /* Clamp long news summaries to 3 lines and ensure long words break */
    .news-summary {
      white-space: normal;
      /* allow breaking very long words/URLs so they don't overflow the card */
      overflow-wrap: anywhere;
      word-break: break-word; /* preferred */
      -ms-word-break: break-word;
      word-break: break-all; /* fallback to force-break extremely long words */
      max-width: 100%;
    }
    .news-summary.collapsed {
      display: -webkit-box;
      -webkit-line-clamp: 3;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }
    /* Ensure news cards do not expand horizontally and stay within their column */
    #tab-news .glass-card {
      width: 100%;
      box-sizing: border-box;
      max-width: 100%;
      overflow-wrap: anywhere;
      word-break: break-word;
      -ms-word-break: break-word;
      overflow-x: hidden;
    }
    /* ensure summary wraps and expands vertically instead of horizontally */
    #tab-news .news-summary {
      white-space: normal;
      overflow-wrap: anywhere;
      word-break: break-word;
    }
    /* Responsive grids and card rules for investor dashboard (match admin behavior) */
    .mi-grid { display: grid; gap: 12px; }
    .mi-grid.indicators { grid-template-columns: repeat(4, 1fr); }
    .mi-grid.sectors { grid-template-columns: repeat(2, 1fr); }
    .mi-grid.news { grid-template-columns: 1fr; }
    .mi-grid.trends { grid-template-columns: repeat(3, 1fr); }

    .mi-card { width:100%; box-sizing:border-box; overflow-wrap:anywhere; word-break:break-word; padding:16px; }
    .mi-card .mi-summary { color:#cbd5e1; margin-bottom:10px; white-space:normal; overflow-wrap:anywhere; word-break:break-word; }

    /* Small screens: single column layout */
    @media (max-width: 640px) {
      .mi-grid.indicators { grid-template-columns: 1fr !important; }
      .mi-grid.sectors { grid-template-columns: 1fr !important; }
      .mi-grid.trends { grid-template-columns: 1fr !important; }
    }
  </style>


        <!-- مركز رؤى السوق -->
        <div style="margin-right:260px;padding:24px;margin-top:30px;">
            <div class=""></div>

            <!-- Header -->
            <header style="position:relative;z-index:10;padding-top:16px;padding-bottom:12px;">
                <div style="padding:0 16px;">
          <h1 style="font-size:32px;color:#ffffff;margin-bottom:8px;text-align:center;">مركز
                        رؤى السوق</h1>
                    <p style="color:#cbd5e1;font-size:18px;text-align:center;">تحليلات وأخبار السوق الاستثماري السعودي</p>

                </div>
            </header>

  @php
    // Data is provided by MarketInsightsController@index as:
    // $indicators, $sectors, $news, $trends
    // Fallback to empty collections to avoid undefined variable errors when rendering in isolation.
    $indicators = $indicators ?? collect();
    $sectors = $sectors ?? collect();
    $news = $news ?? collect();
    $trends = $trends ?? collect();
  @endphp

  <section style="position:relative;z-index:10;padding-bottom:24px;">
    <div >

      <!-- Market Overview -->
      <div style="margin-bottom:18px;">
        <h2 style="font-size:22px;color:#fff;margin-bottom:12px;font-weight:800;">نظرة عامة على السوق</h2>
        <div class="mi-grid indicators">
          @foreach($indicators as $idx => $ind)
            <div class="glass-card mi-card" style="text-align:center;" data-testid="market-indicator-{{ $idx }}">
              <div style="color:#9ca3af;font-size:12px;margin-bottom:6px;">{{ $ind->name }}</div>
              <div style="color:#fff;font-size:22px;font-weight:800;margin-bottom:4px;">{{ $ind->value }}</div>
              <div style="display:flex;align-items:center;justify-content:center;font-weight:700;{{ $ind->trend === 'up' ? 'color:#22c55e' : 'color:#ef4444' }};">
                @if($ind->trend === 'up')
                  <svg width="14" height="14" viewBox="0 0 20 20" fill="currentColor" style="margin-left:4px;"><path fill-rule="evenodd" d="M5.293 7.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L10 4.414 6.707 7.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
                @else
                  <svg width="14" height="14" viewBox="0 0 20 20" fill="currentColor" style="margin-left:4px;"><path fill-rule="evenodd" d="M14.707 12.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L10 15.586l3.293-3.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                @endif
                {{ $ind->change }}
              </div>
            </div>
          @endforeach
        </div>
      </div>

      <!-- Tabs -->
      <div class="glass-card" style="padding:12px;">
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-bottom:12px;background:#1f2937;border-radius:10px;padding:6px;">
          <button class="tab-trigger active" data-tab="sectors" style="padding:8px;border-radius:8px;border:1px solid #b8860b;background:#22d3ee;color:#000;font-weight:700;">أفضل القطاعات أداءً</button>
          <button class="tab-trigger" data-tab="news" style="padding:8px;border-radius:8px;border:1px solid #b8860b;background:transparent;color:#cbd5e1;">الأخبار الحديثة</button>
          <button class="tab-trigger" data-tab="trends" style="padding:8px;border-radius:8px;border:1px solid #b8860b;background:transparent;color:#cbd5e1;">اتجاهات الاستثمار</button>
        </div>

        <!-- Sectors -->
        <div class="tab-content" id="tab-sectors">
          <div class="mi-grid sectors">
            @foreach($sectors as $i => $sector)
              <div class="glass-card mi-card" data-testid="sector-card-{{ $i }}">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:10px;">
                  <h3 style="color:#fff;font-size:20px;font-weight:800;">{{ $sector->name }}</h3>
                  <span style="background:{{ $sector->color }};color:#fff;border-radius:6px;padding:4px 8px;font-size:12px;">{{ $sector->trend }}</span>
                </div>
                <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:10px;">
                  <div>
                    <div style="color:#9ca3af;font-size:12px;">الأداء</div>
                    <div style="color:#22c55e;font-size:22px;font-weight:800;">{{ $sector->performance }}</div>
                  </div>
                  <div>
                    <div style="color:#9ca3af;font-size:12px;">حجم التداول</div>
                    <div style="color:#22d3ee;font-size:18px;font-weight:700;">{{ $sector->volume }}</div>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        </div>

    <!-- News -->
    <div class="tab-content" id="tab-news" style="display:none;">
      <div class="mi-grid news">
            @foreach($news as $i => $news)
              <div class="glass-card mi-card" data-testid="news-card-{{ $i }}">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:10px;">
                  <span style="color:#22d3ee;border:1px solid #22d3ee;border-radius:8px;padding:4px 8px;font-size:12px;">{{ $news->category }}</span>
                  <div style="color:#94a3b8;font-size:12px;">{{ $news->date }} • {{ $news->read_time ?? $news->readTime ?? '' }}</div>
                </div>
                <h3 style="color:#fff;font-size:20px;font-weight:800;margin-bottom:6px;">{{ $news->title }}</h3>
                  @php
                    $full = (string) ($news->summary ?? '');
                    // Insert zero-width space every 40 non-space characters to allow browsers to break very long unbroken strings
                    $display = preg_replace('/(\S{40})(?=\S)/u', '$1&#8203;', $full);
                  @endphp
                  <p class="news-summary collapsed" data-full="{{ e($full) }}" style="color:#cbd5e1;margin-bottom:10px;">{!! $display !!}</p>
                  <button class="btn-secondary news-read-more" data-expanded="0" style="border-color:#22d3ee;color:#22d3ee;display:inline-block;">اقرأ المزيد</button>
              </div>
            @endforeach
          </div>
        </div>

        <!-- Trends -->
        <div class="tab-content" id="tab-trends" style="display:none;">
          <div class="mi-grid trends">
            @foreach($trends as $i => $tr)
              <div class="glass-card mi-card" data-testid="trend-card-{{ $i }}">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
                  <h3 style="color:#fff;font-size:20px;font-weight:800;">{{ $tr->trend }}</h3>
                  <div style="color:#22c55e;font-size:22px;font-weight:800;">{{ $tr->growth }}</div>
                </div>
                <p class="mi-summary">{{ $tr->description }}</p>
              </div>
            @endforeach
          </div>
        </div>
      </div>

    </div>
  </section>
        </div>

<script>
  $(function(){
    $('.tab-trigger').on('click', function(){
      var tab = $(this).data('tab');
  $('.tab-trigger').removeClass('active').css({background:'transparent', color:'#cbd5e1', border:'1px solid #b8860b'});
  $(this).addClass('active').css({background:'#22d3ee', color:'#000', border:'1px solid #b8860b'});
      $('.tab-content').hide();
      $('#tab-'+tab).show();
    });
    // Read more toggle for news summaries using CSS clamp
    $('.news-read-more').each(function(){
      var $btn = $(this);
      var $p = $btn.prev('.news-summary');
      if (!$p.length) return;
      var full = $p.data('full') || '';
      if (!full || full.length <= 160) {
        $btn.hide();
      } else {
        $p.addClass('collapsed');
        $btn.attr('data-expanded','0');
        $btn.show();
      }
    });

    $(document).on('click', '.news-read-more', function(e){
      e.preventDefault();
      var $btn = $(this);
      var $p = $btn.prev('.news-summary');
      if (!$p.length) return;
      var expanded = $btn.attr('data-expanded') === '1';
      if (!expanded) {
        $p.removeClass('collapsed');
        $btn.text('إخفاء');
        $btn.attr('data-expanded','1');
      } else {
        $p.addClass('collapsed');
        $btn.text('اقرأ المزيد');
        $btn.attr('data-expanded','0');
        // scroll card into view if needed
        try { $p[0].scrollIntoView({behavior:'smooth', block:'nearest'}); } catch(e) {}
      }
    });
  });
</script>


        <!--   مركز الثقة والامتثال -->
        <div style="margin-left:260px;">
            <div class=""></div>

            <!-- Header -->
            <header style="position:relative;z-index:10;padding-top:16px;padding-bottom:12px;">
                <div style="padding:0 16px;">

          <h1 style="font-size:32px;color:#ffffff;margin-bottom:8px;text-align:center;">مركز
                        الثقة والامتثال</h1>
                    <p style="color:#cbd5e1;font-size:18px;text-align:center;">الشفافية والأمان في جميع عملياتنا
                        الاستثمارية</p>
                </div>
            </header>


  <section style="position:relative;z-index:10;padding-bottom:24px;">
    <div style="padding:0 16px;">
      <!-- Trust Indicators (from DB) -->
      <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:18px;">
        @php
          $sec = data_get($indicators_tr_com, 'security_rate', ['icon'=>'🛡️','value'=>'96%','label'=>'معدل الأمان']);
          $lic = data_get($indicators_tr_com, 'licenses_count', ['icon'=>'📜','value'=>'5202+','label'=>'تراخيص رسمية']);
          $cert = data_get($indicators_tr_com, 'certificates_count', ['icon'=>'🏆','value'=>'3524+','label'=>'شهادات اعتماد']);
          $comp = data_get($indicators_tr_com, 'compliance_rate', ['icon'=>'✅','value'=>'98%','label'=>'معدل الامتثال']);
        @endphp

        <div class="glass-card" style="text-align:center;padding:16px;">
          <div style="font-size:34px;margin-bottom:8px;">{{ data_get($sec,'icon') }}</div>
          <div style="font-size:24px;color:#22c55e;font-weight:800;">{{ data_get($sec,'value') }}</div>
          <div style="color:#cbd5e1;">{{ data_get($sec,'label') }}</div>
        </div>

        <div class="glass-card" style="text-align:center;padding:16px;">
          <div style="font-size:34px;margin-bottom:8px;">{{ data_get($lic,'icon') }}</div>
          <div style="font-size:24px;color:#22d3ee;font-weight:800;">{{ data_get($lic,'value') }}</div>
          <div style="color:#cbd5e1;">{{ data_get($lic,'label') }}</div>
        </div>

        <div class="glass-card" style="text-align:center;padding:16px;">
          <div style="font-size:34px;margin-bottom:8px;">{{ data_get($cert,'icon') }}</div>
          <div style="font-size:24px;color:#fbbf24;font-weight:800;">{{ data_get($cert,'value') }}</div>
          <div style="color:#cbd5e1;">{{ data_get($cert,'label') }}</div>
        </div>

        <div class="glass-card" style="text-align:center;padding:16px;">
          <div style="font-size:34px;margin-bottom:8px;">{{ data_get($comp,'icon') }}</div>
          <div style="font-size:24px;color:#22c55e;font-weight:800;">{{ data_get($comp,'value') }}</div>
          <div style="color:#cbd5e1;">{{ data_get($comp,'label') }}</div>
        </div>
      </div>

      <!-- Tabs -->
      <div class="glass-card" style="padding:12px;">
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:8px;margin-bottom:12px;background:#1f2937;border-radius:10px;padding:6px;">
          <button class="tc-trigger active" data-tab="reg" style="padding:8px;border-radius:8px;border:0;background:#22d3ee;color:#000;font-weight:700;">اللوائح والتراخيص</button>
          <button class="tc-trigger" data-tab="sec" style="padding:8px;border-radius:8px;border:1px solid #334155;background:transparent;color:#cbd5e1;">الأمان والحماية</button>
          <button class="tc-trigger" data-tab="trans" style="padding:8px;border-radius:8px;border:1px solid #334155;background:transparent;color:#cbd5e1;">الشفافية</button>
          <button class="tc-trigger" data-tab="cert" style="padding:8px;border-radius:8px;border:1px solid #334155;background:transparent;color:#cbd5e1;">الشهادات والاعتمادات</button>
        </div>

        <!-- Regulations -->
        <div class="tc-content" id="tc-reg">
          <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;">
            @foreach($regulations as $i => $r)
              <div class="investment-card" style="padding:12px;" data-testid="regulation-card-{{ $i }}">
                <div style="display:flex;align-items:center;margin-bottom:8px;gap:8px;">
                  <div style="font-size:24px;">{{ data_get($r, 'icon', '') }}</div>
                  <div style="color:#fff;font-weight:700;">{{ data_get($r, 'title', '') }}</div>
                </div>
                <div style="color:#94a3b8;font-size:12px;margin-bottom:8px;">{{ data_get($r, 'name_en', '') }}</div>
                <div style="color:#cbd5e1;font-size:12px;margin-bottom:8px;">{{ data_get($r, 'license', '') }}</div>
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                  <span style="background:#22c55e;color:#fff;border-radius:6px;padding:2px 8px;font-size:12px;">{{ data_get($r, 'status', '') }}</span>
                  <span style="color:#94a3b8;font-size:12px;">حتى: {{ data_get($r, 'valid_until', '') }}</span>
                </div>
                @php
                  // Prefer stored file_path (from DB). If it's an absolute URL use it directly, otherwise generate storage URL.
                  $fileUrl = null;
                  if (!empty($r->file_path)) {
                      if (preg_match('/^https?:\/\//i', $r->file_path)) { $fileUrl = $r->file_path; }
                      else { $fileUrl = \Illuminate\Support\Facades\Storage::url($r->file_path); }
                  }
                @endphp
                @if(!empty($fileUrl))
                  <button type="button" class="btn-secondary tc-open-cert" data-url="{{ $fileUrl }}" style="width:100%;border-color:#22d3ee;color:#22d3ee;">عرض الشهادة</button>
                @else
                  
                @endif
              </div>
            @endforeach
          </div>
        </div>

        <!-- Security -->
        <div class="tc-content" id="tc-sec" style="display:none;">
          <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;margin-bottom:12px;">
            @foreach($security as $i => $f)
              <div class="glass-card" style="padding:16px;" data-testid="security-feature-{{ $i }}">
                <div style="display:flex;gap:12px;align-items:flex-start;">
                  <div style="font-size:28px;">{{ data_get($f, 'icon', '') }}</div>
                  <div style="flex:1;">
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:6px;">
                      <h3 style="color:#fff;font-weight:800;">{{ data_get($f, 'title', '') }}</h3>
                      <span style="background:#22c55e;color:#fff;border-radius:6px;padding:2px 8px;font-size:12px;">{{ data_get($f, 'level', '') }}</span>
                    </div>
                    <p style="color:#cbd5e1;">{{ data_get($f, 'description', '') }}</p>
                  </div>
                </div>
              </div>
            @endforeach
          </div>

          <!-- Risk Management -->
          <div class="glass-card" style="padding:16px;">
            <h3 style="color:#fff;margin-bottom:10px;">إدارة المخاطر</h3>
            <div style="display:grid;gap:10px;">
              @foreach($risks as $rl)
                <div style="display:flex;justify-content:space-between;align-items:center;padding:12px;background:rgba(31,41,55,.5);border-radius:10px;">
                  <div style="display:flex;gap:10px;align-items:center;">
                    <div style="width:16px;height:16px;border-radius:50%;background:{{ data_get($rl, 'color', '#000') }};"></div>
                    <div>
                      <div style="color:#fff;font-weight:700;">مخاطر {{ data_get($rl, 'level', '') }}</div>
                      <div style="color:#94a3b8;font-size:12px;">{{ data_get($rl, 'description', '') }}</div>
                    </div>
                  </div>
                  <div style="color:#22d3ee;font-weight:800;">{{ data_get($rl, 'percentage_text') ?: data_get($rl, 'percentage', '') }}</div>
                </div>
              @endforeach
            </div>
          </div>
        </div>

        <!-- Transparency -->
        <div class="tc-content" id="tc-trans" style="display:none;">
          <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;">
            <div class="glass-card" style="padding:16px;">
              <h3 style="color:#fff;margin-bottom:8px;">الشفافية المالية</h3>
              <div style="display:grid;gap:8px;">
                <div style="display:flex;justify-content:space-between;">
                  <span style="color:#94a3b8;">إجمالي الأصول المدارة</span>
                  <span style="color:#22d3ee;font-weight:800;">2.5 مليار ريال</span>
                </div>
                <div style="display:flex;justify-content:space-between;">
                  <span style="color:#94a3b8;">عدد المستثمرين النشطين</span>
                  <span style="color:#22d3ee;font-weight:800;">15,000+ مستثمر</span>
                </div>
                <div style="display:flex;justify-content:space-between;">
                  <span style="color:#94a3b8;">متوسط العوائد السنوية</span>
                  <span style="color:#22c55e;font-weight:800;">38.5%</span>
                </div>
                <div style="display:flex;justify-content:space-between;">
                  <span style="color:#94a3b8;">معدل نجاح الاستثمارات</span>
                  <span style="color:#22c55e;font-weight:800;">98.2%</span>
                </div>
              </div>
            </div>

            <div class="glass-card" style="padding:16px;">
              <h3 style="color:#fff;margin-bottom:8px;">تقارير المراجعة</h3>
              <div style="display:grid;gap:8px;">
                @foreach($audits as $r)
                  <div style="padding:10px;background:rgba(31,41,55,.5);border-radius:10px;display:flex;justify-content:space-between;align-items:center;">
                    <div>
                      <div style="color:#fff;font-weight:600;font-size:14px;">{{ data_get($r, 'title', '') }}</div>
                      <div style="color:#94a3b8;font-size:12px;">{{ data_get($r, 'date', '') }} • {{ data_get($r, 'file_size', '') }}</div>
                    </div>
  
                    @if($r->file_path)
                        <a href="storage/{{ $r->file_path }}" target="_blank" class="btn-primary" style="display:inline-block;margin-top:8px;">عرض التقرير</a>
                    @else
                      
                    @endif
                  </div>
                @endforeach
              </div>
            </div>
          </div>
        </div>

        <!-- Certifications -->
        <div class="tc-content" id="tc-cert" style="display:none;">
          <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;">
            @foreach($certs as $i => $c)
              <div class="investment-card" style="padding:12px;" data-testid="certification-card-{{ $i }}">
                <div style="text-align:center;margin-bottom:8px;">
                  <h3 style="color:#fff;font-size:20px;font-weight:800;">{{ data_get($c, 'name', '') }}</h3>
                  <div style="color:#94a3b8;font-size:12px;">{{ data_get($c, 'description', '') }}</div>
                </div>
                <div style="text-align:center;margin-bottom:8px;">
                  <div style="font-size:40px;">🏆</div>
                  <span style="background:#22c55e;color:#fff;border-radius:8px;padding:2px 8px;font-size:12px;">{{ data_get($c, 'status', '') }}</span>
                </div>
                <div style="display:grid;gap:6px;font-size:12px;color:#cbd5e1;">
                  <div style="display:flex;justify-content:space-between;"><span>تاريخ الإصدار:</span><span>{{ data_get($c, 'issued_at', '') }}</span></div>
                  <div style="display:flex;justify-content:space-between;"><span>ساري حتى:</span><span>{{ data_get($c, 'expires_at', '') }}</span></div>
                </div>
                 @php
                  $fileUrl = null;
                  if (!empty($c->file_path)) {
                      if (preg_match('/^https?:\/\//i', $c->file_path)) { $fileUrl = $c->file_path; }
                      else { $fileUrl = \Illuminate\Support\Facades\Storage::url($c->file_path); }
                  }
                @endphp
                @if($fileUrl)
                  <button type="button" class="btn-secondary tc-open-cert" data-url="{{ $fileUrl }}" style="width:100%;margin-top:8px;border-color:#22d3ee;color:#22d3ee;">عرض الشهادة</button>
                @else
                  
                @endif
            </div>
            @endforeach
          </div>
        </div>
      </div>


    </div>
  </section>
        </div>
    
                   <script>
                      // Open certificate links in a new tab while preserving original button styling
                      document.addEventListener('click', function(e){
                        var t = e.target;
                        if(!t) return;
                        if(t.classList && t.classList.contains('tc-open-cert')){
                          var url = t.getAttribute('data-url');
                          if(url){ window.open(url, '_blank'); }
                        }
                      }, false);
                    </script>

<script>
  $(function(){
    $('.tc-trigger').on('click', function(){
      var tab = $(this).data('tab');
      $('.tc-trigger').removeClass('active').css({background:'transparent', color:'#cbd5e1', border:'1px solid #334155'});
      $(this).addClass('active').css({background:'#22d3ee', color:'#000', border:'0'});
      $('.tc-content').hide();
      $('#tc-'+tab).show();
    });
  });
</script>


        <!-- تواصل معنا -->
        <section style="margin-top:40px;max-width:800px;margin-right:auto;margin-left:auto;">
            <div class="glass-card" style="padding:20px;text-align:center;">
                <h3 style="color:#fff;margin-bottom:8px;">تواصل معنا</h3>

    @php
      // Use shared contact (site_contact) for WhatsApp link
      $waRaw = isset($site_contact) ? ($site_contact['whatsapp'] ?? $site_contact['phone'] ?? '') : '';
      $waNumber = preg_replace('/[^0-9]/', '', $waRaw ?: '');
      $message = urlencode('مرحباً، أود التواصل معكم.');
    @endphp

    <a href="https://wa.me/{{ $waNumber }}?text={{ $message }}"
   id="waLink"
   class="btn-primary"
   style="display:inline-block;margin-top:8px;"
   target="_blank">
   تواصل عبر واتساب
</a>

<p style="color:#9ca3af;margin-top:10px;">
    ساعات العمل: السبت - الخميس (8:00 ص - 12:00 ص)
</p>
 </div>
        </section>


    </section>
</div>

    <style>


        /* خلفية الجسيمات - تبقى كما هي */
        #particles-js {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 0;
            background: transparent;
            /* background: rgb(5, 21, 34); */

        }

        /* Canvas للنجوم - يبقى كما هو */
        #space {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
        }

        /* إعادة ضبط المحتوى */
        .hero-section {
            position: relative;
            z-index: 2;
        }

        .container {
            position: relative;
            z-index: 3;
        }

        /* الخلفية الرقمية - تبقى كما هي */
        .digital-background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 1;
        }

        /* الخلفية الإلكترونية - تبقى كما هي */
        .electronic-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background:
                linear-gradient(45deg, rgba(10, 25, 47, 0.85) 0%, rgba(10, 25, 47, 0.7) 100%),
                url('#') center/cover;
            background-blend-mode: overlay;
            animation: bgShift 20s infinite alternate;
        }

        .electronic-img {
            border: 2px solid var(--gold-primary);
            box-shadow:
                0 0 30px rgba(212, 175, 55, 0.3),
                inset 0 0 30px rgba(212, 175, 55, 0.1);
            transition: all 0.5s ease;
        }

        .electronic-img:hover {
            transform: scale(1.02);
            box-shadow:
                0 0 50px rgba(212, 175, 55, 0.5),
                inset 0 0 40px rgba(212, 175, 55, 0.2);
        }

        .particles {
            width: 100%;
            height: 100%;
        }

        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: var(#0099cc);
            border-radius: 50%;
            animation: float 6s infinite ease-in-out;
            box-shadow: 0 0 10px var(--gold-primary);
        }

        .network-lines {
            position: absolute;
            width: 100%;
            height: 100%;
        }

        .network-line {
            position: absolute;
            background: linear-gradient(90deg, transparent, rgba(#0099cc), transparent);
            height: 1px;
            animation: networkFlow 8s infinite linear;
            box-shadow: 0 0 10px rgba(212, 175, 55, 0.5);
        }

        .network-line:nth-child(1) { top: 20%; animation-delay: 0s; width: 100%; }
        .network-line:nth-child(2) { top: 50%; animation-delay: 2s; width: 80%; left: 10%; }
        .network-line:nth-child(3) { top: 80%; animation-delay: 4s; width: 90%; left: 5%; }

        .growth-arrows {
            position: absolute;
            width: 100%;
            height: 100%;
        }

        .arrow {
            position: absolute;
            width: 30px;
            height: 30px;
            border-right: 3px solid var(--gold-primary);
            border-bottom: 3px solid var(--gold-primary);
            animation: growArrow 4s infinite;
            filter: drop-shadow(0 0 8px var(--gold-primary));
        }

        .arrow:nth-child(1) { top: 30%; left: 20%; animation-delay: 0s; }
        .arrow:nth-child(2) { top: 60%; left: 70%; animation-delay: 1s; }
        .arrow:nth-child(3) { top: 80%; left: 40%; animation-delay: 2s; }

        /* البطاقات الزجاجية - تبقى كما هي */
        .glass-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: var(--border-radius);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
        }

        /* بطاقات الاستثمار - تبقى كما هي */
        .investment-card {
            transition: var(--transition);
            background: rgba(17, 34, 64, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
        }

        .investment-card:hover {
            transform: translateY(-8px);
            box-shadow:
                var(--shadow-gold),
                0 15px 40px rgba(212, 175, 55, 0.3);
            border-color: var(--gold-primary);
            background: rgba(17, 34, 64, 0.9);
        }

        .investment-icon {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }

        .investment-icon.blue {
            background: linear-gradient(135deg, #3b82f6, #06b6d4);
            color: white;
        }

        .investment-icon.gold {
            background: linear-gradient(135deg, var(--gold-primary), var(--gold-secondary));
            color: #000;
        }

        .investment-icon.green {
            background: linear-gradient(135deg, #10b981, #14b8a6);
            color: white;
        }

        .investment-card:hover .investment-icon {
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4);
        }

        .border-gold {
            border: 2px solid var(--gold-primary) !important;
            box-shadow: 0 0 20px rgba(212, 175, 55, 0.4);
        }

        .bg-yellow {
            background: linear-gradient(135deg, var(--gold-primary), var(--gold-secondary)) !important;
        }

        /* تأثيرات النص - تبقى كما هي */
        .text-glow {
            text-shadow:
                0 0 20px rgba(212, 175, 55, 0.6),
                0 0 40px rgba(212, 175, 55, 0.4),
                0 0 60px rgba(212, 175, 55, 0.2);
        }

        .text-cyan {
            color: #06b6d4 !important;
            text-shadow: 0 0 15px rgba(6, 182, 212, 0.6);
        }

        .text-yellow {
            color: var(--gold-primary) !important;
            text-shadow: 0 0 15px rgba(212, 175, 55, 0.6);
        }

        /* تأثيرات الحركة - تبقى كما هي */
        @keyframes float {
            0%, 100% {
                transform: translateY(0) rotate(0deg) scale(1);
                opacity: 0.7;
            }
            50% {
                transform: translateY(-30px) rotate(180deg) scale(1.2);
                opacity: 1;
            }
        }

        @keyframes networkFlow {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        @keyframes growArrow {
            0%, 100% {
                transform: rotate(45deg) scale(1);
                opacity: 0.3;
            }
            50% {
                transform: rotate(45deg) scale(1.3);
                opacity: 1;
            }
        }

        @keyframes bgShift {
            0% { background-position: 0% 50%; }
            100% { background-position: 100% 50%; }
        }

        .hover-scale:hover {
            transform: scale(1.05);
        }

        /* شبكة الإحصائيات - تبقى كما هي */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .stat-item {
            padding: 2.5rem 1rem;
            transition: all 0.3s ease;
        }

        .stat-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.5);
        }

        /* التجاوب */
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
            }

            .hero-buttons .btn {
                margin-bottom: 0.5rem;
                width: 100%;
            }

            .electronic-bg {
                background-position: 60% center;
            }

            .stat-item {
                padding: 1.5rem 0.5rem;
            }

            /* تقليل عدد الجسيمات على الجوال */
            #particles-js {
                transform: scale(0.8);
            }
        }

        @media (max-width: 576px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .hero-title {
                font-size: 2.5rem;
            }
        }
    </style>

    <!-- مكتبة particles.js -->
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // تهيئة particles.js - تبقى كما هي
            particlesJS('particles-js', {
                "particles": {
                    "number": {
                        "value": 60,
                        "density": {
                            "enable": true,
                            "value_area": 800
                        }
                    },
                    "color": {
                        "value": "#d4af37"
                    },
                    "shape": {
                        "type": "circle",
                        "stroke": {
                            "width": 0,
                            "color": "#000000"
                        }
                    },
                    "opacity": {
                        "value": 0.5,
                        "random": true,
                        "anim": {
                            "enable": true,
                            "speed": 1,
                            "opacity_min": 0.1,
                            "sync": false
                        }
                    },
                    "size": {
                        "value": 3,
                        "random": true,
                        "anim": {
                            "enable": true,
                            "speed": 2,
                            "size_min": 0.1,
                            "sync": false
                        }
                    },
                    "line_linked": {
                        "enable": true,
                        "distance": 150,
                        "color": "#d4af37",
                        "opacity": 0.4,
                        "width": 1
                    },
                    "move": {
                        "enable": true,
                        "speed": 2,
                        "direction": "none",
                        "random": true,
                        "straight": false,
                        "out_mode": "out",
                        "bounce": false,
                        "attract": {
                            "enable": false,
                            "rotateX": 600,
                            "rotateY": 1200
                        }
                    }
                },
                "interactivity": {
                    "detect_on": "canvas",
                    "events": {
                        "onhover": {
                            "enable": true,
                            "mode": "repulse"
                        },
                        "onclick": {
                            "enable": true,
                            "mode": "push"
                        },
                        "resize": true
                    },
                    "modes": {
                        "grab": {
                            "distance": 400,
                            "line_linked": {
                                "opacity": 1
                            }
                        },
                        "bubble": {
                            "distance": 400,
                            "size": 40,
                            "duration": 2,
                            "opacity": 8,
                            "speed": 3
                        },
                        "repulse": {
                            "distance": 100
                        },
                        "push": {
                            "particles_nb": 4
                        },
                        "remove": {
                            "particles_nb": 2
                        }
                    }
                },
                "retina_detect": true
            });

            // نظام النجوم Canvas - يبقى كما هو
            var canvas = document.getElementById('space');
            var context = canvas.getContext('2d');
            var screenH = window.innerHeight;
            var screenW = window.innerWidth;
            var stars = [];
            var numStars = 200;

            // ضبط حجم canvas
            canvas.width = screenW;
            canvas.height = screenH;

            // إنشاء النجوم
            for(var i = 0; i < numStars; i++) {
                var x = Math.random() * screenW;
                var y = Math.random() * screenH;
                var length = 0.5 + Math.random() * 1;
                var opacity = Math.random() * 0.5 + 0.2;

                var star = new Star(x, y, length, opacity);
                stars.push(star);
            }

            function Star(x, y, length, opacity) {
                this.x = x;
                this.y = y;
                this.length = length;
                this.opacity = opacity;
                this.factor = 1;
                this.increment = Math.random() * 0.02;
            }

            Star.prototype.draw = function() {
                context.save();
                context.translate(this.x, this.y);

                if(this.opacity > 1) {
                    this.factor = -1;
                }
                else if(this.opacity <= 0) {
                    this.factor = 1;
                    this.x = Math.random() * screenW;
                    this.y = Math.random() * screenH;
                }

                this.opacity += this.increment * this.factor;

                context.beginPath();
                for (var i = 5; i--;) {
                    context.lineTo(0, this.length);
                    context.translate(0, this.length);
                    context.rotate((Math.PI * 2 / 10));
                    context.lineTo(0, - this.length);
                    context.translate(0, - this.length);
                    context.rotate(-(Math.PI * 6 / 10));
                }
                context.closePath();
                context.fillStyle = "rgba(212, 175, 55, " + this.opacity + ")";
                context.shadowBlur = 10;
                context.shadowColor = '#d4af37';
                context.fill();
                context.restore();
            }

            function animateStars() {
                context.clearRect(0, 0, screenW, screenH);
                context.fillStyle = 'transparent';
                context.fillRect(0, 0, screenW, screenH);

                stars.forEach(function(star) {
                    star.draw();
                });

                requestAnimationFrame(animateStars);
            }

            animateStars();


            // Animation on scroll للمحتوى
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const delay = entry.target.getAttribute('data-delay') || 0;
                        setTimeout(() => {
                            entry.target.style.animationPlayState = 'running';
                        }, delay);
                    }
                });
            }, observerOptions);

            // Observe all animated elements
            document.querySelectorAll('.animate-fade-in, .animate-slide-up').forEach(el => {
                el.style.animationPlayState = 'paused';
                observer.observe(el);
            });

            // Counter animation
            const counters = document.querySelectorAll('.counter');
            const speed = 200;

            counters.forEach(counter => {
                const updateCount = () => {
                    const target = parseInt(counter.getAttribute('data-count'));
                    const count = parseInt(counter.innerText.replace(/,/g, ''));
                    const increment = Math.ceil(target / speed);

                    if (count < target) {
                        counter.innerText = (count + increment).toLocaleString();
                        setTimeout(updateCount, 1);
                    } else {
                        counter.innerText = target.toLocaleString();
                    }
                };

                const counterObserver = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            updateCount();
                            counterObserver.unobserve(entry.target);
                        }
                    });
                });

                counterObserver.observe(counter);
            });

            // إضافة تأثير النقر على البطاقات
            document.querySelectorAll('.investment-card').forEach(card => {
                card.style.cursor = 'pointer';
                card.addEventListener('click', function() {
                    if (this.getAttribute('onclick')) {
                        const onclickContent = this.getAttribute('onclick');
                        const urlMatch = onclickContent.match(/window\.location='([^']+)'/);
                        if (urlMatch && urlMatch[1]) {
                            window.location.href = urlMatch[1];
                        }
                    }
                });
            });


            // إعادة ضبط النجوم عند تغيير حجم النافذة
            window.addEventListener('resize', function() {
                screenW = window.innerWidth;
                screenH = window.innerHeight;
                canvas.width = screenW;
                canvas.height = screenH;
            });
        });
    </script>
    <script>
        $(function() {
            $('#whatsapp-contact').on('click', function(e) {
                e.preventDefault();
                window.open('https://wa.me/message/MKKY7YC3Y34KC1', '_blank');
            });
        });
    </script>
  <script>
    // Auto-start onboarding tour on first visit to home
    document.addEventListener('DOMContentLoaded', function(){
      try {
        var shown = false;
        try { shown = !!localStorage.getItem('site_onboarding_shown'); } catch(e) { shown = false; }
        console.info('Onboarding shown flag:', shown);
        if (!shown && typeof window.startOnboardingTour === 'function'){
          // small timeout to allow layout and assets to settle
          setTimeout(function(){
            try {
              window.startOnboardingTour();
            } catch(e) { console.error('Error starting tour', e); }
          }, 700);
        }
      } catch(e) { console.error('Onboarding autostart error', e); }
    });
  </script>
@endsection
