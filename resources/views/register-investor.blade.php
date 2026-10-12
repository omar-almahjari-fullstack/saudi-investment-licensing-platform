@extends('layouts.app')

@section('content')
<div class="container">
    <div class="mobile-breadcrumb py-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">الرئيسية</a></li>
                <li class="breadcrumb-item active" aria-current="page">تسجيل مستثمر جديد</li>
            </ol>
        </nav>
    </div>
</div>
<div style="margin-left:260px;padding:24px;">
  <div class="digital-background"></div>

  <!-- Header -->
  <header class="text_center_title" style="position:relative;z-index:10;padding-top:16px;padding-bottom:12px;">
    <div style="padding:0 16px;">
      <h1 style="font-size:32px;color:#fff;margin-bottom:8px;" class="glow-text">تسجيل مستثمر جديد</h1>
  <p style="color:#cbd5e1;font-size:18px;">انضم إلى منصة عوائد الخليج للاستثمار السعودي</p>
    </div>
  </header>

  <section style="position:relative;z-index:10;padding-bottom:24px;">
    <div style="padding:0 16px;max-width:1100px;margin:0 auto;">
      <!-- Success Card -->
      <div id="register-success" style="display:none;">
        <div class="glass-card" style="max-width:640px;margin:40px auto;padding:24px;text-align:center;">
          <div style="width:80px;height:80px;margin:0 auto 16px;display:flex;align-items:center;justify-content:center;border-radius:50%;background:linear-gradient(90deg,#22c55e,#14b8a6);">
            <svg width="40" height="40" viewBox="0 0 20 20" fill="#fff"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
          </div>
          <h2 style="font-size:22px;color:#fff;margin-bottom:8px;">تم تسجيلك بنجاح كمستثمر!</h2>
          <p style="color:#cbd5e1;margin-bottom:16px;">مرحباً <span id="success-full-name" style="color:#06b6d4;font-weight:700;"></span>، تم إنشاء حسابك بنجاح. يمكنك الآن البدء في استكشاف الفرص الاستثمارية المتاحة.</p>

          <div style="background:rgba(31,41,55,0.5);padding:16px;border-radius:10px;margin-bottom:16px;text-align:right;">
            <h3 style="color:#fff;font-weight:700;margin-bottom:10px;">تفضيلاتك الاستثمارية:</h3>
            <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:10px;font-size:14px;">
              <div>
                <span style="color:#9ca3af;">المبلغ المتوقع:</span>
                <div id="success-expected-amount" style="color:#06b6d4;font-weight:600;"></div>
              </div>
              <div>
                <span style="color:#9ca3af;">المدة المفضلة:</span>
                <div id="success-investment-duration" style="color:#fbbf24;font-weight:600;"></div>
              </div>
            </div>
          </div>

          <div style="display:grid;gap:10px;max-width:360px;margin:0 auto;">
            <a href="/investment-levels" class="btn-primary" style="display:block;">استكشاف الفرص الاستثمارية</a>
            <a href="/" class="btn-secondary" style="display:block;border-color:#64748b;color:#cbd5e1;">العودة للرئيسية</a>
          </div>
        </div>
      </div>

      <!-- Registration Form -->
      <form id="register-form" method="POST" action="{{ route('investor.register.submit') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="redirect_after" id="redirect_after" value="{{ request()->query('redirect') ?? '' }}" />
        <input type="hidden" name="pre_amount" id="pre_amount" value="{{ request()->query('amount') ?? '' }}" />
        <input type="hidden" name="pre_rate" id="pre_rate" value="{{ request()->query('rate') ?? '' }}" />
        @if($errors->any())
          <div style="background:rgba(127,29,29,0.25);border:1px solid rgba(127,29,29,0.5);color:#fecaca;padding:10px;border-radius:10px;margin-bottom:12px;">
            {{ $errors->first() }}
          </div>
        @endif
        <!-- 1. نوع المستثمر -->
        <div class="glass-card" style="padding:16px;margin-bottom:16px;">
          <h3 style="color:#fff;margin-bottom:12px;display:flex;align-items:center;gap:8px;">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="#22d3ee"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
            1. نوع المستثمر
          </h3>
          <div style="display:grid;gap:10px;">
            <label style="display:flex;align-items:center;gap:10px;border:1px solid #4b5563;border-radius:10px;padding:12px;cursor:pointer;">
              <input type="radio" name="investorType" value="individual" {{ old('investorType','individual') === 'individual' ? 'checked' : '' }} />
              <div style="color:#fff;">
                <div style="font-weight:700;">مستثمر فردي</div>
                <div style="color:#9ca3af;font-size:12px;">للأفراد الذين يستثمرون لحسابهم الشخصي</div>
              </div>
            </label>
            <label style="display:flex;align-items:center;gap:10px;border:1px solid #4b5563;border-radius:10px;padding:12px;cursor:pointer;">
              <input type="radio" name="investorType" value="institutional" {{ old('investorType') === 'institutional' ? 'checked' : '' }} />
              <div style="color:#fff;">
                <div style="font-weight:700;">مستثمر مؤسسي</div>
                <div style="color:#9ca3af;font-size:12px;">للشركات والمؤسسات والصناديق الاستثمارية</div>
              </div>
            </label>
          </div>
        </div>

        <!-- 2. البيانات الشخصية -->
        <div class="glass-card" style="padding:16px;margin-bottom:16px;">
          <h3 style="color:#fff;margin-bottom:12px;display:flex;align-items:center;gap:8px;">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="#fbbf24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            2. البيانات الشخصية
          </h3>
          <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;">
            <div>
              @unless(auth()->check() && auth()->user()->username)
                <label for="full_name" style="display:block;color:#cbd5e1;margin-bottom:6px;">الاسم الكامل *</label>
                <input name="full_name" id="full_name" class="form-control" placeholder="الاسم الكامل كما هو مدون في الهوية" value="{{ old('full_name', $userData['full_name'] ?? '') }}" style="width:100%;background:#1f2937;border:1px solid #4b5563;color:#fff;padding:10px;border-radius:8px;" {{ isset($userData) ? 'readonly' : '' }} />
                <div id="err-full_name" style="color:#f87171;font-size:12px;margin-top:4px;display:none;"></div>
                @error('full_name')<div style="color:#fca5a5;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
              @else
                {{-- User signed in via Google / has username stored: keep full_name empty for later completion. --}}
                <input type="hidden" name="full_name" id="full_name" value="" />
                <p style="color:#9ca3af;font-size:12px;margin-top:6px;">سيتم إدخال الاسم الكامل لاحقاً من ملفك الشخصي بعد إتمام الخطوات التالية.</p>
              @endunless
            </div>
            <div>
              <label for="national_id" style="display:block;color:#cbd5e1;margin-bottom:6px;">رقم الهوية أو الإقامة *</label>
              @php $prefilledNid = old('national_id', $userData['national_id'] ?? ''); @endphp
              <input name="national_id" id="national_id" class="form-control" placeholder="رقم الهوية أو الإقامة" value="{{ $prefilledNid }}" style="width:100%;background:#1f2937;border:1px solid #4b5563;color:#fff;padding:10px;border-radius:8px;" {{ (!empty($userData) && !empty($userData['national_id'])) ? 'readonly' : '' }} />
              <div id="err-national_id" style="color:#f87171;font-size:12px;margin-top:4px;display:none;"></div>
              @error('national_id')<div style="color:#fca5a5;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
            </div>
            <div>
              <label for="nationality" style="display:block;color:#cbd5e1;margin-bottom:6px;">الجنسية *</label>
              <select name="nationality" id="nationality" class="form-control" style="width:100%;background:#1f2937;border:1px solid #4b5563;color:#fff;padding:10px;border-radius:8px;"></select>
              <div id="err-nationality" style="color:#f87171;font-size:12px;margin-top:4px;display:none;"></div>
              @error('nationality')<div style="color:#fca5a5;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
            </div>
            <div>
              <label for="city" style="display:block;color:#cbd5e1;margin-bottom:6px;">المدينة *</label>
              <select name="city" id="city" class="form-control" style="width:100%;background:#1f2937;border:1px solid #4b5563;color:#fff;padding:10px;border-radius:8px;"></select>
              <div id="err-city" style="color:#f87171;font-size:12px;margin-top:4px;display:none;"></div>
              @error('city')<div style="color:#fca5a5;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
            </div>
          </div>
        </div>

  <!-- 3. بيانات التواصل -->
        <div class="glass-card" style="padding:16px;margin-bottom:16px;">
          <h3 style="color:#fff;margin-bottom:12px;display:flex;align-items:center;gap:8px;">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="#22c55e"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/></svg>
            3. بيانات التواصل
          </h3>
          <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;">
            <div>
              <label for="mobile_number" style="display:block;color:#cbd5e1;margin-bottom:6px;">رقم الجوال *</label>
              <input name="mobile_number" id="mobile_number" class="form-control" placeholder="+966501234567" value="{{ old('mobile_number', $userData['phone'] ?? '') }}" style="width:100%;background:#1f2937;border:1px solid #4b5563;color:#fff;padding:10px;border-radius:8px;" {{ isset($userData) ? 'readonly' : '' }} />
              <div id="err-mobile_number" style="color:#f87171;font-size:12px;margin-top:4px;display:none;"></div>
              @error('mobile_number')<div style="color:#fca5a5;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
            </div>
            <div>
              <label for="email" style="display:block;color:#cbd5e1;margin-bottom:6px;">البريد الإلكتروني *</label>
              <input name="email" id="email" type="email" class="form-control" placeholder="example@email.com" value="{{ old('email', $userData['email'] ?? '') }}" style="width:100%;background:#1f2937;border:1px solid #4b5563;color:#fff;padding:10px;border-radius:8px;" {{ isset($userData) ? 'readonly' : '' }} />
              <div id="err-email" style="color:#f87171;font-size:12px;margin-top:4px;display:none;"></div>
              @error('email')<div style="color:#fca5a5;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
            </div>
          </div>
        </div>

  <!-- 4. بيانات الاستثمار -->
        <div class="glass-card" style="padding:16px;margin-bottom:16px;">
          <h3 style="color:#fff;margin-bottom:12px;display:flex;align-items:center;gap:8px;">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="#a78bfa"><path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"/></svg>
            4. بيانات الاستثمار
          </h3>
          <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;">
            <div>
              <label for="expected_amount" style="display:block;color:#cbd5e1;margin-bottom:6px;">المبلغ المتوقع للاستثمار *</label>
              <select name="expected_amount" id="expected_amount" class="form-control" style="width:100%;background:#1f2937;border:1px solid #4b5563;color:#fff;padding:10px;border-radius:8px;"></select>
              <div id="err-expected_amount" style="color:#f87171;font-size:12px;margin-top:4px;display:none;"></div>
              @error('expected_amount')<div style="color:#fca5a5;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
            </div>
            <input type="hidden" name="investment_duration" id="investment_duration" value="{{ request()->query('duration', old('investment_duration','')) }}" />
            <div>
              <label for="bank_account" style="display:block;color:#cbd5e1;margin-bottom:6px;">رقم الحساب البنكي (IBAN)</label>
              <input name="bank_account" id="bank_account" class="form-control" placeholder="SAxx xxxx xxxx xxxx xxxx xx" value="{{ old('bank_account') }}" style="width:100%;background:#1f2937;border:1px solid #4b5563;color:#fff;padding:10px;border-radius:8px;" />
              @error('bank_account')<div style="color:#fca5a5;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
            </div>
            <div style="grid-column:1/-1;">
              <label for="notes" style="display:block;color:#cbd5e1;margin-bottom:6px;">ملاحظة</label>
              <textarea name="notes" id="notes" rows="3" class="form-control" style="width:100%;background:#1f2937;border:1px solid #4b5563;color:#fff;padding:10px;border-radius:8px;">{{ old('notes') }}</textarea>
              @error('notes')<div style="color:#fca5a5;font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
            </div>
          </div>
        </div>

        <!-- 4.1 تحميل صور الهوية -->
        <div class="glass-card" style="padding:16px;margin-bottom:16px;">
          <h3 style="color:#fff;margin-bottom:12px;display:flex;align-items:center;gap:8px;">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="#38bdf8"><path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h8l6-6V5a2 2 0 00-2-2H4z"/></svg>
            4.1. تحميل صور الهوية
          </h3>
          <div id="identity-list" style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;"></div>
          <p style="color:#94a3b8;font-size:12px;margin-top:8px;">يسمح برفع صور PNG / JPG أو ملفات PDF حتى 5MB لكل ملف.</p>
        </div>

        <!-- 5. إتمام التسجيل -->
        <div class="glass-card" style="padding:16px;margin-bottom:16px;">
          <h3 style="color:#fff;margin-bottom:12px;display:flex;align-items:center;gap:8px;">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="#fb923c"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            5. إتمام التسجيل
          </h3>
          <div style="display:grid;gap:10px;">
            <label style="display:flex;gap:10px;align-items:flex-start;">
              <input name="agree_terms" id="agree_terms" type="checkbox" value="1" {{ old('agree_terms') ? 'checked' : '' }} />
              <span style="color:#cbd5e1;">
                الموافقة على
                <button type="button" id="open-terms" style="margin-inline-start:8px;background:none;border:0;color:#22d3ee;text-decoration:underline;cursor:pointer;padding:0;">الشروط والأحكام وسياسة الخصوصية</button>
              </span>
            </label>
            <div id="err-agree_terms" style="color:#f87171;font-size:12px;margin-top:4px;display:none;"></div>
            <label style="display:flex;gap:10px;align-items:flex-start;">
              <input name="agree_offers" id="agree_offers" type="checkbox" value="1" {{ old('agree_offers') ? 'checked' : '' }} />
              <span style="color:#cbd5e1;">الموافقة على تلقي العروض والفرص الاستثمارية عبر البريد الإلكتروني</span>
            </label>
          </div>
        </div>

        <!-- Submit -->
        <div style="text-align:center;">
          <div id="err-submit" style="display:none;color:#f87171;margin-bottom:12px;padding:10px;background:rgba(127,29,29,0.3);border-radius:10px;"></div>
          <button id="register-submit" type="submit" class="btn-primary" style="font-size:18px;padding:12px 36px;">🚀 تسجيل كمستثمر</button>
          <p style="color:#9ca3af;font-size:12px;margin-top:8px;">* الحقول المطلوبة</p>
        </div>
      </form>
      
      <!-- Terms & Privacy Modal -->
      <div id="terms-modal" style="display:none;position:fixed;inset:0;background:rgba(2,6,23,0.7);backdrop-filter:blur(2px);z-index:1000;align-items:center;justify-content:center;">
        <div class="glass-card" style="width:min(840px,92vw);max-height:84vh;overflow:auto;padding:16px;position:relative;">
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;">
            <h3 style="color:#fff;margin:0;">الشروط والأحكام وسياسة الخصوصية</h3>
            <button type="button" id="close-terms" aria-label="إغلاق" style="background:#0f172a;border:1px solid #334155;color:#cbd5e1;border-radius:8px;padding:6px 10px;cursor:pointer;">إغلاق</button>
          </div>

          <div style="display:flex;gap:8px;margin-bottom:12px;">
            <button type="button" class="terms-tab-btn" data-tab="terms" style="background:#22d3ee;border:0;color:#000;border-radius:8px;padding:8px 12px;cursor:pointer;">الشروط والأحكام</button>
            <button type="button" class="terms-tab-btn" data-tab="privacy" style="background:transparent;border:1px solid #334155;color:#cbd5e1;border-radius:8px;padding:8px 12px;cursor:pointer;">سياسة الخصوصية</button>
          </div>

          <div id="tab-terms" class="terms-tab-panel" style="display:block;color:#cbd5e1;">
            <ol style="padding-inline-start:20px;line-height:1.9;">
              <li>باستخدامك لمنصة عوائد الخليج للاستثمار السعودي، فإنك تقر بموافقتك على هذه الشروط.</li>
              <li>جميع المعلومات التي تقدمها يجب أن تكون صحيحة وكاملة ومحدثة.</li>
              <li>تحتفظ المنصة بحق رفض أو إلغاء أي عملية استثمار وفقاً لسياسات الامتثال.</li>
              <li>العوائد المتوقعة تقديرية وقد تختلف بحسب أداء الاستثمار والمخاطر.</li>
              <li>يجب الالتزام بجميع الأنظمة والقوانين المعمول بها في المملكة العربية السعودية.</li>
            </ol>
          </div>
          <div id="tab-privacy" class="terms-tab-panel" style="display:none;color:#cbd5e1;">
            <p style="line-height:1.9;">نحترم خصوصيتك ونلتزم بحماية بياناتك الشخصية. تُجمع البيانات وتُعالج لأغراض إنشاء الحساب، وإدارة الاستثمارات، والتواصل بشأن الفرص والخدمات. لن نشارك بياناتك مع أطراف ثالثة إلا وفق المتطلبات القانونية أو بموافقتك. يمكنك طلب تحديث أو حذف بياناتك بما لا يتعارض مع الالتزامات النظامية.</p>
            <p style="line-height:1.9;">باستمرارك في استخدام المنصة فإنك توافق على سياسة الخصوصية وتحديثاتها.</p>
          </div>

          <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:12px;">
            <button type="button" id="accept-terms" class="btn-primary" style="padding:10px 16px;">أوافق</button>
            <button type="button" id="dismiss-terms" class="btn-secondary" style="padding:10px 16px;border-color:#334155;color:#cbd5e1;">إلغاء</button>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<script>
  $(function(){
    // If redirected from investment flow, pre-fill expected amount and store redirect
    var urlParams = new URLSearchParams(window.location.search);
    var preAmount = urlParams.get('amount') || $('#pre_amount').val();
    var preRate = urlParams.get('rate') || $('#pre_rate').val();
    var redirectAfter = urlParams.get('redirect') || $('#redirect_after').val();
    if(preAmount){
      // choose nearest range option if possible
      if(preAmount >= 50000) { $('#expected_amount').val('50000-100000'); }
      else if(preAmount >= 10000) { $('#expected_amount').val('10000-50000'); }
      else { $('#expected_amount').val('1000-10000'); }
    }
    var investmentRanges = [
      { value: '1000-10000', label: '1,000 - 10,000 ر.س' },
      { value: '10000-50000', label: '10,000 - 50,000 ر.س' },
      { value: '50000-100000', label: '50,000 - 100,000 ر.س' },
      { value: '100000-500000', label: '100,000 - 500,000 ر.س' },
      { value: '500000+', label: 'أكثر من 500,000 ر.س' }
    ];
    // var investmentDurations = [
    //   { value: '3months', label: 'ثلاثة أشهر' },
    //   { value: '6months', label: 'ستة أشهر' },
    //   { value: '1year', label: 'سنة واحدة' },
    //   { value: '1year3months', label: 'سنة وثلاثة أشهر' },
    //   { value: '1year6months', label: 'سنة وستة أشهر' },
    //   { value: '2years', label: 'سنتان' },
    //   { value: '2years3months', label: 'سنتان وثلاثة أشهر' },
    //   { value: '2years6months', label: 'سنتان وستة أشهر' },
    //   { value: '3years', label: 'ثلاث سنوات' }
    // ];
    var nationalities = ['السعودية','اليمن','الإمارات العربية المتحدة','الكويت','قطر','البحرين','عُمان','الأردن','لبنان','مصر','المغرب','تونس','الجزائر','السودان','العراق','سوريا','فلسطين','ليبيا','أخرى'];
    var citiesByCountry = {
      'السعودية': ['الرياض','جدة','مكة المكرمة','المدينة المنورة','الدمام','الخبر','الظهران','الطائف','أبها','تبوك','بريدة','خميس مشيط','حفر الباطن','الجبيل','ينبع','الأحساء','القطيف','عرعر','سكاكا','جازان','نجران','الباحة','القريات','رفحاء'],
      'اليمن': ['صنعاء','عدن','تعز','الحديدة','إب','المكلا','ذمار','صعدة','أبين','لحج','البيضاء','مأرب','حضرموت','المهرة','ريمة','المحويت'],
      'الإمارات العربية المتحدة': ['أبو ظبي','دبي','الشارقة','عجمان','أم القيوين','رأس الخيمة','الفجيرة','العين','خورفكان','دبا الفجيرة'],
      'الكويت': ['مدينة الكويت','حولي','الأحمدي','الفروانية','الجهراء','مبارك الكبير','السالمية','صباح السالم'],
      'قطر': ['الدوحة','الريان','الوكرة','الخور','الظعاين','الشحانية','أم صلال','الشمال'],
      'البحرين': ['المنامة','المحرق','الرفاع','مدينة حمد','سترة','مدينة عيسى','جد حفص'],
      'عُمان': ['مسقط','صلالة','صحار','نزوى','صور','البريمي','عبري','الرستاق','خصب','إبراء'],
      'الأردن': ['عمّان','الزرقاء','إربد','العقبة','السلط','المفرق','جرش','مأدبا','الكرك','الطفيلة','عجلون'],
      'لبنان': ['بيروت','طرابلس','صيدا','صور','زحلة','جبيل','جونيه','بعبدا','بعلبك'],
      'مصر': ['القاهرة','الجيزة','الإسكندرية','بورسعيد','الإسماعيلية','السويس','دمياط','المنصورة','طنطا','أسيوط','أسوان'],
      'المغرب': ['الرباط','الدار البيضاء','مراكش','فاس','طنجة','مكناس','أكادير','وجدة','تطوان'],
      'تونس': ['تونس','صفاقس','سوسة','قفصة','قابس','بنزرت','نابل','المنستير','المهدية','جندوبة'],
      'الجزائر': ['الجزائر','وهران','قسنطينة','عنابة','سطيف','البليدة','باتنة','تيزي وزو','بسكرة'],
      'السودان': ['الخرطوم','أم درمان','بحري','بورتسودان','مدني','كسلا','الأبيض'],
      'العراق': ['بغداد','البصرة','الموصل','أربيل','النجف','كربلاء','السليمانية','الديوانية','الأنبار','بابل'],
      'سوريا': ['دمشق','حلب','حمص','حماة','اللاذقية','طرطوس','دير الزور','السويداء','إدلب','الرقة'],
      'فلسطين': ['القدس','غزة','رام الله','نابلس','الخليل','بيت لحم','طولكرم','جنين','قلقيلية','أريحا'],
      'ليبيا': ['طرابلس','بنغازي','مصراتة','البيضاء','درنة','سبها','سرت','طبرق','الخمس'],
      'أخرى': ['أخرى']
    };

    // Fill nationality select with old selection if any
    var selectedNationality = @json(old('nationality'));
    nationalities.forEach(function(n){
      var $opt = $('<option/>').val(n).text(n);
      if(selectedNationality && selectedNationality === n){ $opt.attr('selected', 'selected'); }
      $('#nationality').append($opt);
    });

    // Helper to populate cities based on nationality
    function populateCities(country){
      var $city = $('#city');
      $city.empty();
      var list = citiesByCountry[country] || ['أخرى'];
      list.forEach(function(c){ $city.append($('<option/>').val(c).text(c)); });
    }

    // Initial population
  var initialNat = selectedNationality || $('#nationality').val() || nationalities[0];
    populateCities(initialNat);
  var selectedCity = @json(old('city'));
  if(selectedCity){ $('#city').val(selectedCity); }

    // Update cities on nationality change
    $('#nationality').on('change', function(){ populateCities(this.value); });
  var selectedExpected = @json(old('expected_amount'));
  // duration is set hidden; user does not choose it from UI
  investmentRanges.forEach(function(r){
    var $opt = $('<option/>').val(r.value).text(r.label);
    if(selectedExpected && selectedExpected === r.value){ $opt.attr('selected','selected'); }
    $('#expected_amount').append($opt);
  });
  // no duration select to populate

    // Terms modal logic
    var $modal = $('#terms-modal');
    function openTerms(){ $modal.css('display','flex'); }
    function closeTerms(){ $modal.hide(); }
    $(document).on('click', '#open-terms', function(e){ e.preventDefault(); e.stopPropagation(); openTerms(); });
    $(document).on('click', '#close-terms, #dismiss-terms', function(){ closeTerms(); });
    // accept checks the checkbox then closes
    $(document).on('click', '#accept-terms', function(){ $('#agree_terms').prop('checked', true); closeTerms(); });
    // click outside card closes
    $modal.on('click', function(e){ if(e.target === this){ closeTerms(); } });
    // ESC to close
    $(document).on('keydown', function(e){ if(e.key === 'Escape' && $modal.is(':visible')) closeTerms(); });
    // Tabs switcher
    $(document).on('click', '.terms-tab-btn', function(){
      var tab = $(this).data('tab');
      $('.terms-tab-btn').css({background:'transparent',color:'#cbd5e1',border:'1px solid #334155'});
      $(this).css({background:'#22d3ee',color:'#000',border:'0'});
      $('.terms-tab-panel').hide();
      $('#tab-'+tab).show();
    });

    function showError(id, msg){ var $e = $('#err-'+id); if(msg){ $e.text(msg).show(); } else { $e.hide(); } }

    function validate(){
      var errors = {};
  if($('#full_name').is(':visible') && !$('#full_name').val().trim()) errors.full_name = 'الاسم الكامل مطلوب';
      if(!$('#national_id').val().trim()) errors.national_id = 'رقم الهوية مطلوب';
      if(!$('#nationality').val()) errors.nationality = 'الجنسية مطلوبة';
      if(!$('#city').val()) errors.city = 'المدينة مطلوبة';
      if(!$('#mobile_number').val().trim()) errors.mobile_number = 'رقم الجوال مطلوب';
      if(!$('#email').val().trim()) errors.email = 'البريد الإلكتروني مطلوب';
      if(!$('#expected_amount').val()) errors.expected_amount = 'المبلغ المتوقع مطلوب';
  // duration is not required from user input any more
  if(!$('#agree_terms').is(':checked')) errors.agree_terms = 'يجب الموافقة على الشروط والأحكام';
  // optional client-side max size check (~5MB)
  var front = document.getElementById('identity_image_front');
  var back = document.getElementById('identity_image_back');
  var max = 5 * 1024 * 1024;
  if(front && front.files && front.files[0] && front.files[0].size > max){ errors.identity_image_front = 'حجم الصورة الأمامية أكبر من 5MB'; }
  if(back && back.files && back.files[0] && back.files[0].size > max){ errors.identity_image_back = 'حجم الصورة الخلفية أكبر من 5MB'; }

      showError('full_name', errors.full_name);
      showError('national_id', errors.national_id);
      showError('nationality', errors.nationality);
      showError('city', errors.city);
      showError('mobile_number', errors.mobile_number);
      showError('email', errors.email);
      showError('expected_amount', errors.expected_amount);
  // no duration error display
      showError('agree_terms', errors.agree_terms);
  showError('identity_image_front', errors.identity_image_front);
  showError('identity_image_back', errors.identity_image_back);

      if(Object.keys(errors).length){
        $('#err-submit').text('يرجى تصحيح الأخطاء أعلاه').show();
        return false;
      }
      $('#err-submit').hide();
      return true;
    }

    // allow normal form submission; keep client-side validation helper to show inline messages
    $('#register-form').on('submit', function(e){
      if(!validate()) { e.preventDefault(); return false; }
    });

  // Compute existing canonical user identity preview URLs (if available) so we can hide upload UI when present
  @php
    $existingUserFront = null;
    $existingUserBack = null;
    // Only attempt to read users identity columns if the column exists
    // helper to normalize path and resolve to a public URL if the file exists
    $resolvePublicUrl = function($path) {
        if (empty($path)) return null;
        // strip leading slashes
        $p = ltrim($path, '/');
        $storagePath = storage_path('app/public/' . $p);
        $publicPath = public_path('storage/' . $p);
    if (file_exists($storagePath) || file_exists($publicPath)) {
      return asset('storage/' . $p);
    }
    if (\Illuminate\Support\Facades\Storage::disk('public')->exists($p)) {
      return \Illuminate\Support\Facades\Storage::disk('public')->url($p);
    }
        return null;
    };

  // Prefer explicit front/back; do not use legacy single identity_image
  $frontCandidate = null;
  if (!empty($userData['identity_image_front'])) { $frontCandidate = $userData['identity_image_front']; }
    if (!empty($frontCandidate)) {
        $existingUserFront = $resolvePublicUrl($frontCandidate);
    }

    if (!empty($userData['identity_image_back'])) {
        $existingUserBack = $resolvePublicUrl($userData['identity_image_back']);
    }
  @endphp

  // Identity uploads UI (dynamic cards with cloud icon and progress)
    var identityDocs = [
      { id: 'identity_image_front', name: 'صورة الهوية (الوجه الأمامي)', description:'صورة واضحة للوجه الأمامي من الهوية الوطنية', acceptedTypes: '.jpg, .jpeg, .png, .pdf', maxSize: '5 MB', icon: '🆔' },
      { id: 'identity_image_back',  name: 'صورة الهوية (الوجه الخلفي)',  description:'صورة واضحة للوجه الخلفي من الهوية الوطنية', acceptedTypes: '.jpg, .jpeg, .png, .pdf', maxSize: '5 MB', icon: '🆔' }
    ];
    var identityFiles = {};   // {id: {file,name,size,uploadDate}}
    var identityProgress = {}; // {id: percent}

    function renderIdentityCards(){
      var $wrap = $('#identity-list');
      $wrap.empty();
      identityDocs.forEach(function(doc){
        var $card = $('<div/>').addClass('glass-card').css({padding:'14px'}).attr('data-doc', doc.id);
        var $header = $('<div/>').css({display:'flex',justifyContent:'space-between',alignItems:'flex-start',marginBottom:'8px'});
        var $title = $('<div/>').css({color:'#fff',fontWeight:'700'}).html('<span style="font-size:20px;margin-left:8px;">'+doc.icon+'</span>'+doc.name);
        var $badge = $('<span/>').text('مطلوب').css({background:'#ef4444',color:'#fff',borderRadius:'6px',padding:'2px 8px',fontSize:'12px'});
        $header.append($title, $badge);

        var $desc = $('<p/>').text(doc.description).css({color:'#cbd5e1',fontSize:'12px',margin:'6px 0'});
        var $metaRow = $('<div/>').css({display:'flex',gap:'12px',color:'#9ca3af',fontSize:'12px'}).append(
          $('<span/>').text('الأنواع المقبولة: '+doc.acceptedTypes),
          $('<span/>').text('الحد الأقصى: '+doc.maxSize)
        );

        var $body = $('<div/>').attr('data-body', doc.id);
        // hidden file input with the exact name/id expected by backend
        var $input = $('<input/>').attr({ type:'file', id: doc.id, name: doc.id, accept: doc.acceptedTypes.replace(/\s/g,'') }).css({display:'none'});
        var $choose = $('<label/>').attr('for', doc.id)
          .css({display:'flex',alignItems:'center',justifyContent:'center',width:'100%',padding:'24px',border:'2px dashed #4b5563',borderRadius:'10px',cursor:'pointer'})
          .hover(function(){ $(this).css('borderColor','#22d3ee'); }, function(){ $(this).css('borderColor','#4b5563'); })
          .append(
            $('<div/>').css({textAlign:'center'}).append(
              $('<svg/>').attr({width:48,height:48,viewBox:'0 0 24 24'}).css({display:'block',margin:'0 auto 8px',color:'#9ca3af'})
                .html('<path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>'),
              $('<div/>').css({color:'#cbd5e1',fontWeight:600}).text('اختيار ملف'),
              $('<p/>').css({color:'#9ca3af',fontSize:'12px',marginTop:'4px'}).text('اسحب وأفلت الملف هنا أو انقر للاختيار')
            )
          );

        var $progress = $('<div/>').attr('data-progress', doc.id).css({display:'none'});
        var $progTop = $('<div/>').css({display:'flex',justifyContent:'space-between',fontSize:'12px',color:'#cbd5e1',margin:'6px 0'}).append(
          $('<span/>').text('تقدم الرفع'),
          $('<span/>').attr('data-prog-text', doc.id).css({color:'#22d3ee'}).text('0%')
        );
        var $progBar = $('<div/>').css({height:'8px',background:'#1f2937',borderRadius:'6px',overflow:'hidden'}).append(
          $('<div/>').attr('data-prog-bar', doc.id).css({height:'8px',background:'#22d3ee',width:'0%'})
        );
        $progress.append($progTop, $progBar);

        var $info = $('<div/>').attr('data-info', doc.id).css({display:'none',background:'#1f2937',padding:'12px',borderRadius:'10px'});
        var $infoHeader = $('<div/>').css({display:'flex',justifyContent:'space-between',alignItems:'flex-start',marginBottom:'8px'});
        var $titleWrap = $('<div/>' );
        var $fileName = $('<h4/>').css({color:'#fff',margin:'0 0 4px 0'});
        var $meta = $('<div/>').css({display:'flex',gap:'12px',fontSize:'12px',color:'#9ca3af'});
        $titleWrap.append($fileName, $meta);
        var $status = $('<span/>').css({background:'#22c55e',color:'#fff',borderRadius:'6px',padding:'2px 8px',fontSize:'12px'}).text('مكتمل');
        $infoHeader.append($titleWrap, $status);
        var $actions = $('<div/>').css({display:'flex',gap:'8px'});
        var $viewBtn = $('<button/>').attr('type','button').text('عرض الملف').addClass('btn-secondary').css({borderColor:'#22d3ee',color:'#22d3ee'});
        var $removeBtn = $('<button/>').attr('type','button').text('إزالة الملف').addClass('btn-secondary').css({borderColor:'#f87171',color:'#f87171'});
        $actions.append($viewBtn, $removeBtn);
        $info.append($infoHeader, $actions);

        // inline error area for client-side size checks and server errors
        var $inlineErr = $('<div/>').attr('id','err-'+doc.id).css({color:'#f87171',fontSize:'12px',marginTop:'6px',display:'none'});
        var serverErr = @json($errors->toArray() ?? []);
        if(serverErr && serverErr[doc.id] && serverErr[doc.id][0]){
          $inlineErr.text(serverErr[doc.id][0]).show();
        }

        $body.append($input, $choose, $progress, $info, $inlineErr);
        $card.append($header, $desc, $metaRow, $body);
        $wrap.append($card);

        // wire up behaviors
        $input.on('change', function(e){ handleIdentityUpload(doc.id, e.target.files[0]); });
        $viewBtn.on('click', function(){ var f = identityFiles[doc.id]; if(!f || !f.file) return; var url = URL.createObjectURL(f.file); window.open(url, '_blank'); });
        $removeBtn.on('click', function(){ removeIdentityFile(doc.id); });
      });
    }

    function handleIdentityUpload(id, file){
      if(!file) return;
      identityProgress[id] = 0;
      var $progWrap = $('[data-progress="'+id+'"]');
      var $progText = $('[data-prog-text="'+id+'"]');
      var $progBar  = $('[data-prog-bar="'+id+'"]');
      var $body     = $('[data-body="'+id+'"]');
      var $choose   = $body.find('label[for="'+id+'"]');

      $progWrap.show();
      $choose.hide();

      var interval = setInterval(function(){
        var curr = identityProgress[id] || 0;
        var next = curr + 12;
        if(next >= 100){
          next = 100;
          clearInterval(interval);
          var sizeMB = (file.size / 1024 / 1024).toFixed(2) + ' MB';
          identityFiles[id] = { file:file, name:file.name, size:sizeMB, uploadDate:new Date().toLocaleDateString('ar-SA') };
          fillIdentityInfo(id);
          $progWrap.hide();
        }
        identityProgress[id] = next;
        $progText.text(next + '%');
        $progBar.css('width', next + '%');
      }, 180);
    }

    function fillIdentityInfo(id){
      var info = identityFiles[id]; if(!info) return;
      var $body = $('[data-body="'+id+'"]');
      var $infoWrap = $body.find('[data-info]');
      $infoWrap.find('h4').text(info.name);
      $infoWrap.find('h4').next('div').remove();
      var $meta = $('<div/>').css({display:'flex',gap:'12px',fontSize:'12px',color:'#9ca3af'}).append(
        $('<span/>').text('حجم الملف: '+info.size),
        $('<span/>').text('تاريخ الرفع: '+info.uploadDate)
      );
      $infoWrap.find('h4').after($meta);
      $infoWrap.show();
    }

    function removeIdentityFile(id){
      delete identityFiles[id];
      delete identityProgress[id];
      var $body = $('[data-body="'+id+'"]');
      $body.find('[data-info]').hide();
      $body.find('[data-progress]').hide();
      $body.find('label[for="'+id+'"]').show();
      var input = document.getElementById(id); if(input){ input.value = ''; }
    }

    renderIdentityCards();
    // If the server passed canonical user identity URLs, show preview and disable picker
    var initialExisting = {
      identity_image_front: @json($existingUserFront ?? null),
      identity_image_back:  @json($existingUserBack ?? null)
    };
    Object.keys(initialExisting).forEach(function(key){
      var url = initialExisting[key];
      if(!url) return;
      var $card = $('[data-doc="'+key+'"]');
      if(!$card.length) return;
      // disable the file input and replace choose area with preview
      $card.find('label[for="'+key+'"]').hide();
      var $body = $card.find('[data-body="'+key+'"]');
      var $preview = $('<div/>').css({marginTop:'8px'}).append(
        $('<a/>').attr('href', url).attr('target','_blank').append(
          $('<img/>').attr('src', url).css({maxWidth:'100%',borderRadius:'8px',border:'1px solid #334155'})
        )
      );
      $body.prepend($preview);
    });
  });
</script>
@endsection
