@extends('layouts.app')

@section('title','تسجيل مستثمر جديد')

@section('content')
<style>
  .auth-root{position:relative;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:48px 18px;background:#051226;color:#fff;direction:rtl}
  .digital-background{position:absolute;inset:0;z-index:0;background:linear-gradient(180deg, #02121a, #031425 40%)}
  .card-header{text-align:center;margin-bottom:6px}
  .avatar-wrap{width:96px;height:96px;margin:0 auto 14px;border-radius:9999px;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg, #00ffff 0%, #0099cc 100%);box-shadow:0 6px 22px rgba(0,153,204,0.22)}
  .avatar-svg{width:44px;height:44px;color:#fff}
  h2.card-title{font-size:24px;margin:0;color:#fff}
  p.card-sub{margin:6px 0 0;color:#9fb0bf}

  /* make the card more compact */
  .card{position:relative;z-index:10;min-height:360px;max-width:540px;width:100%;background:linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01));backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,0.04);box-shadow:0 18px 40px rgba(0,0,0,0.6);border-radius:12px;padding:24px 22px;display:flex;flex-direction:column}

  label.field-label{display:block;margin-bottom:6px;color:#fff;font-weight:700}
  /* Inputs: dark background, gold border only */
  input.field{width:100%;height:40px;padding:10px 12px;border-radius:8px;background:#0f1724;border:1px solid rgba(255,215,0,0.45);color:#fff;font-weight:600;box-sizing:border-box}
  .btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;width:100%;padding:12px;border-radius:10px;border:none;color:#000;background:linear-gradient(135deg,#ffd97a 0%,#ffb84d 100%);cursor:pointer;font-weight:800;letter-spacing:0.2px}
  .alert{margin-top:8px;padding:10px;border-radius:8px;border:1px solid rgba(220,38,38,0.15);background:rgba(127,29,29,0.12);color:#fecaca}
  .link-outline{display:inline-block;margin-top:14px;padding:8px 12px;border-radius:8px;border:1px solid rgba(255,255,255,0.06);color:#cbd5e1;text-decoration:none}
  .hint{font-size:12px;color:#9fb0bf;margin-top:6px}
  .strength-wrap{display:flex;align-items:center;gap:10px;margin-top:8px}
  .strength-bar{flex:1;height:8px;background:rgba(255,255,255,0.08);border-radius:999px;overflow:hidden}
  .strength-bar-inner{height:100%;width:0;border-radius:999px;transition:width .25s ease, background .25s ease}
  .strength-label{min-width:68px;font-size:12px;color:#cbd5e1}
  /* Modal styles */
  .modal-backdrop{position:fixed;inset:0;background:rgba(0,0,0,0.55);z-index:60;display:none;align-items:center;justify-content:center;padding:18px}
  .modal-backdrop.show{display:flex}
  .modal-card{max-width:720px;width:100%;background:linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01));backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,0.06);border-radius:14px;box-shadow:0 20px 50px rgba(0,0,0,0.65);color:#fff}
  .modal-header{display:flex;align-items:center;justify-content:space-between;padding:14px 16px;border-bottom:1px solid rgba(255,255,255,0.06)}
  .modal-title{margin:0;font-size:18px}
  .modal-close{background:transparent;border:0;color:#cbd5e1;font-size:22px;cursor:pointer}
  .modal-body{padding:16px;color:#cbd5e1}
  .modal-actions{display:flex;justify-content:flex-end;gap:8px;padding:0 16px 16px}
  .btn-outline{background:transparent;border:1px solid rgba(255,255,255,0.18);color:#cbd5e1;padding:10px 14px;border-radius:10px;cursor:pointer}
  .btn-brand{background:linear-gradient(135deg, #00ffff 0%, #0099cc 100%);color:#000;border:0;padding:10px 16px;border-radius:10px;cursor:pointer;font-weight:700}
  /* Blue primary button for create account (match investor login gradient) */
  .btn-blue{background:linear-gradient(135deg,#06b6d4 0%,#0284c7 100%);color:#030000;border:0;padding:12px;border-radius:10px;font-weight:800}

  @media (max-width:900px){
    .card{min-height:380px;padding:28px;border-radius:12px}
    .avatar-wrap{width:84px;height:84px}
    .avatar-svg{width:40px;height:40px}
    .card-title{font-size:22px}
  }
  @media (max-width:480px){
    .card{min-height:0;padding:18px}
    input.field{height:44px}
    .btn{padding:12px}
    .auth-root{padding:28px 12px}
  }
  .mb-3{ margin-bottom:12px }
  .text-left{ text-align:left }
  .text-center{ text-align:center }
  /* Terms row: checkbox + links + quick-summary button */
  .terms-row{display:flex;align-items:flex-start;gap:10px;flex-wrap:wrap}
  .terms-control{display:flex;align-items:flex-start;gap:10px;flex:1;min-width:0}
  .terms-checkbox{margin-top:6px}
  .terms-label{color:#cbd5e1;line-height:1.3}
  .terms-links{white-space:nowrap;display:inline}
  .terms-links a{margin:0 4px}
  .terms-sep{color:rgba(203,213,225,0.9);margin:0 4px}
  .terms-summary-btn{margin-right:auto}
</style>

<div class="auth-root">
  <div class="digital-background"></div>

  <div class="card">
    <div class="card-header">
      <div class="avatar-wrap" role="img" aria-label="register icon">
        <svg class="avatar-svg" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path d="M12 12a5 5 0 100-10 5 5 0 000 10z"/>
          <path d="M4 20a8 8 0 1116 0v1H4v-1z"/>
        </svg>
      </div>
      <h2 class="card-title">تسجيل مستثمر جديد</h2>
  <p class="card-sub">أهلاً بك في عوائد الخليج للاستثمار السعودي</p>
    </div>

    @if($errors->any())
      <div class="alert" role="alert">
        <ul style="margin:0;padding-right:18px">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="post" action="{{ route('auth.register') }}">
      @csrf
      <div class="mb-3">
        <label class="field-label" for="name">الاسم الكامل</label>
        <input id="name" name="name" class="field" type="text" placeholder="الاسم الكامل" value="{{ old('name') }}" required />
      </div>

      <div class="mb-3">
        <label class="field-label" for="email">البريد الإلكتروني</label>
        <input id="email" name="email" class="field" type="email" placeholder="you@example.com" value="{{ old('email') }}" autocomplete="email" required />
      </div>

      <div class="mb-3">
        <label class="field-label" for="phone">رقم الهاتف</label>
        <input id="phone" name="phone" class="field" type="tel" placeholder="05XXXXXXXX" value="{{ old('phone') }}" required />
      </div>

      <div class="mb-3">
        <label class="field-label" for="password">كلمة المرور</label>
        <input id="password" name="password" class="field" type="password" placeholder="••••••••" autocomplete="new-password" required />
        <div class="hint">الحد الأدنى 6 أحرف، ويُفضّل استخدام حروف كبيرة وصغيرة وأرقام ورموز.</div>
        <div class="strength-wrap">
          <div class="strength-bar"><div id="pwd-bar" class="strength-bar-inner"></div></div>
          <div id="pwd-label" class="strength-label">ضعيفة</div>
        </div>
      </div>

      <div class="mb-3">
        <label class="field-label" for="password_confirmation">تأكيد كلمة المرور</label>
        <input id="password_confirmation" name="password_confirmation" class="field" type="password" placeholder="••••••••" autocomplete="new-password" required />
      </div>

      <div class="mb-3 terms-row" dir="rtl">
        <div class="terms-control">
          <input id="accept_terms" name="accept_terms" type="checkbox" value="1" class="terms-checkbox" {{ old('accept_terms') ? 'checked' : '' }} required />
          <label for="accept_terms" class="terms-label">
            أوافق على
            <span class="terms-links">
              <a href="{{ route('pages.terms') }}" class="link-outline">الشروط والأحكام</a>
              <span class="terms-sep">&nbsp;·&nbsp;</span>
              <a href="{{ route('pages.privacy') }}" class="link-outline">سياسة الخصوصية</a>
            </span>
          </label>
        </div>
        <button id="terms-summary-open" type="button" class="btn-outline terms-summary-btn">عرض ملخص سريع</button>
      </div>

      <div class="mb-3" style="margin-top:10px">
        <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site', '6LdfqPMrAAAAAB22N7xYC5dHUBiM-s_0oc5n-19f') }}"></div>
      </div>

      <!-- Social / Google signup -->
      <div style="margin:14px 0;display:flex;align-items:center;gap:12px;flex-direction:column">
        <a href="{{ route('auth.google') }}" class="btn" style="display:flex;align-items:center;justify-content:center;gap:10px;background:#fff;color:#222;padding:10px 12px;border-radius:12px;border:1px solid rgba(0,0,0,0.06);width:100%;font-weight:700">
          <svg width="20" height="20" viewBox="0 0 533.5 544.3" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path fill="#4285F4" d="M533.5 278.4c0-18.5-1.6-37.6-4.9-55.5H272v105.2h146.9c-6.3 34.8-25 64.3-53.4 84v69.6h86.3c50.6-46.6 81.7-115.3 81.7-203.3z"/>
            <path fill="#34A853" d="M272 544.3c72.6 0 133.6-24 178.2-65.3l-86.3-69.6c-24.1 16.2-55 25.8-91.9 25.8-70.6 0-130.4-47.6-151.8-111.6H32.6v70.1C76.8 481.3 169.6 544.3 272 544.3z"/>
            <path fill="#FBBC05" d="M120.2 321.6c-10.9-32.8-10.9-68 0-100.8V150.7H32.6c-39.8 79.5-39.8 174.9 0 254.4l87.6-83.5z"/>
            <path fill="#EA4335" d="M272 107.7c38.5 0 73 13.3 100.2 39.5l75.1-75.1C405.3 24.6 344.3 0 272 0 169.6 0 76.8 63 32.6 150.7l87.6 70.9C141.6 155.3 201.4 107.7 272 107.7z"/>
          </svg>
          التسجيل عبر Google
        </a>

        <div style="width:100%;display:flex;align-items:center;gap:12px;margin-top:6px">
          <hr style="flex:1;border:none;height:1px;background:rgba(255,255,255,0.04)">
          <div style="color:#9fb0bf;font-size:13px">أو</div>
          <hr style="flex:1;border:none;height:1px;background:rgba(255,255,255,0.04)">
        </div>

        <div style="width:100%">
          <button type="submit" class="btn btn-blue">إنشاء الحساب</button>
        </div>
      </div>
    </form>

    <div class="text-center" style="margin-top:14px">
      <a href="{{ route('auth.login.form') }}" class="link-outline">لديك حساب؟ تسجيل الدخول</a>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script>
  (function(){
    const pwd = document.getElementById('password');
    if(!pwd) return;
    const bar = document.getElementById('pwd-bar');
    const label = document.getElementById('pwd-label');

    function scorePassword(p){
      if(!p) return 0;
      let s = 0;
      if(p.length >= 6) s++;
      if(/[a-z]/.test(p) && /[A-Z]/.test(p)) s++;
      if(/\d/.test(p)) s++;
      if(/[^A-Za-z0-9]/.test(p)) s++;
      if(p.length >= 12) s++;
      return Math.min(s, 4);
    }

    function colorForScore(sc){
      switch(sc){
        case 0: return '#ef4444'; // red
        case 1: return '#f59e0b'; // amber
        case 2: return '#eab308'; // yellow
        case 3: return '#06b6d4'; // cyan
        case 4: return 'linear-gradient(135deg, #00ffff 0%, #0099cc 100%)'; // brand gradient
      }
    }

    function textForScore(sc){
      return ['ضعيفة','ضعيفة','متوسطة','جيدة','قوية'][sc];
    }

    function widthForScore(sc){
      return ['10%','25%','50%','75%','100%'][sc];
    }

    function update(){
      const sc = scorePassword(pwd.value);
      const color = colorForScore(sc);
      bar.style.width = widthForScore(sc);
      // support gradient string
      if(color.startsWith('linear-gradient')){
        bar.style.background = color;
      } else {
        bar.style.background = color;
      }
      label.textContent = textForScore(sc);
    }

    pwd.addEventListener('input', update);
    update();
  })();
</script>
@endpush

@push('scripts')
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endpush

@push('scripts')
<script>
  (function(){
    const openBtn = document.getElementById('terms-summary-open');
    const backdrop = document.getElementById('terms-summary-modal');
    const closeBtn = document.getElementById('terms-summary-close');
    if(!openBtn || !backdrop || !closeBtn) return;
    function open(){ backdrop.classList.add('show'); }
    function close(){ backdrop.classList.remove('show'); }
    openBtn.addEventListener('click', open);
    closeBtn.addEventListener('click', close);
    backdrop.addEventListener('click', function(e){ if(e.target === backdrop) close(); });
    document.addEventListener('keydown', function(e){ if(e.key === 'Escape') close(); });
  })();
</script>
@endpush

{{-- Modal: Terms & Privacy Summary --}}
<div id="terms-summary-modal" class="modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="termsSummaryTitle">
  <div class="modal-card">
    <div class="modal-header">
      <h3 id="termsSummaryTitle" class="modal-title">ملخص الشروط والخصوصية</h3>
      <button id="terms-summary-close" class="modal-close" aria-label="إغلاق">×</button>
    </div>
    <div class="modal-body">
      <ul style="margin:0;padding-right:18px;line-height:1.9">
        <li>استخدام المنصة يجب أن يلتزم بالأنظمة المحلية والشروط المذكورة.</li>
        <li>حماية بياناتك أولوية لدينا، ونستخدمها فقط لتحسين الخدمة وفق سياسة الخصوصية.</li>
        <li>يمكنك طلب تعديل أو حذف بياناتك عبر قنوات الدعم.</li>
        <li>بالضغط على إنشاء الحساب فأنت توافق على الشروط والأحكام وسياسة الخصوصية.</li>
      </ul>
    </div>
    <div class="modal-actions">
      <a href="{{ route('pages.terms') }}" class="btn-outline">قراءة الشروط كاملة</a>
      <a href="{{ route('pages.privacy') }}" class="btn-outline">قراءة سياسة الخصوصية</a>
      <button type="button" class="btn-brand" id="terms-summary-close-2">موافق</button>
    </div>
  </div>
</div>

@push('scripts')
<script>
  (function(){
    // Wire second close button
    const backdrop = document.getElementById('terms-summary-modal');
    const btn2 = document.getElementById('terms-summary-close-2');
    if(btn2 && backdrop){ btn2.addEventListener('click', function(){ backdrop.classList.remove('show'); }); }
  })();
</script>
@endpush
