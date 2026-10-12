@extends('layouts.app')

@section('content')
<div class="container">
    <div class="mobile-breadcrumb py-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">الرئيسية</a></li>
                <li class="breadcrumb-item active" aria-current="page">طلب ترخيص تجاري</li>
            </ol>
        </nav>
    </div>
</div>

<div style="margin-right:260px;padding:24px;">
  <div class="digital-background"></div>

  <!-- Header -->
  <header class="text_center_title" style="position:relative;z-index:10;padding-bottom:22px;">
    <div style="padding:0 16px;">
      <h1 style="font-size:32px;color:#fff;margin-top:12px;" class="glow-text">طلب ترخيص تجاري</h1>
    </div>
  </header>

  <div style="position:relative;z-index:10;padding:0 16px 32px;max-width:1200px;margin:0 auto;">
    <style>
      /* Inline error styles so injected JS errors are visible on dark background */
      .field-error { color: #f87171 !important; font-size:12px; margin-top:6px; display:block; }
      .field-error.missing-doc { color: #f87171 !important; }
      .invalid-field { box-shadow: 0 0 0 2px rgba(239,68,68,0.12); border-color:#ef4444 !important; }
    </style>
    @if ($errors->any())
      <div class="glass-card" style="max-width:1100px;margin:16px auto 20px;padding:16px;border:1px solid #ef4444;background:rgba(239,68,68,0.1);">
        <h3 style="color:#fff;margin:0 0 8px 0;">تعذر إرسال النموذج</h3>
        <p style="color:#fca5a5;margin:0 0 8px 0;">يرجى تصحيح الحقول المطلوبة أدناه.</p>
      </div>
    @endif
    <!-- Success Screen -->
    <div id="license-success" style="display:none;">
      <div class="glass-card" style="max-width:640px;margin:40px auto;padding:24px;text-align:center;">
        <div style="width:80px;height:80px;margin:0 auto 16px;display:flex;align-items:center;justify-content:center;border-radius:50%;background:linear-gradient(90deg,#22c55e,#14b8a6);">
          <svg width="40" height="40" viewBox="0 0 20 20" fill="#fff"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        </div>
        <h2 style="font-size:22px;color:#fff;margin-bottom:8px;">تم تقديم طلبك بنجاح!</h2>
        <p style="color:#cbd5e1;margin-bottom:6px;">رقم الطلب: <span id="success-license-id" style="color:#06b6d4;font-weight:700;"></span></p>
        <p style="color:#cbd5e1;margin-bottom:18px;">سيتم مراجعة طلبك خلال 24-48 ساعة</p>
        <div style="display:grid;gap:8px;max-width:360px;margin:0 auto;">
          <a id="track-link" href="{{ route('investor.dashboard') }}" class="btn-primary" style="display:block;">تتبع الطلب</a>
          <a href="/" class="btn-secondary" style="display:block;border-color:#64748b;color:#cbd5e1;">العودة للرئيسية</a>
        </div>
      </div>
    </div>

    <!-- Form -->
    <form id="license-form" method="POST" action="{{ route('license.store') }}" style="max-width:1100px;margin:0 auto;" enctype="multipart/form-data">
      @csrf
  <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:16px;">
        <!-- Business Information -->
        <div class="glass-card" style="padding:16px;">
          <h3 style="color:#fff;margin-bottom:12px;display:flex;align-items:center;gap:8px;">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="#22d3ee"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-6a1 1 0 00-1-1H9a1 1 0 00-1 1v6a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd"/></svg>
            معلومات النشاط التجاري
          </h3>
          <div style="display:grid;gap:10px;">
            <div>
              <label for="business_name" style="display:block;color:#cbd5e1;margin-bottom:6px;">اسم النشاط التجاري</label>
              <input id="business_name" name="business_name" class="form-control" required
                     value="{{ old('business_name') }}"
                     style="width:100%;background:#1f2937;border:1px solid {{ $errors->has('business_name') ? '#ef4444' : '#4b5563' }};color:#fff;padding:10px;border-radius:8px;" />
              @error('business_name')
                <p style="color:#f87171;font-size:12px;margin-top:6px;">{{ $message }}</p>
              @enderror
            </div>
            <div>
              <label for="business_type" style="display:block;color:#cbd5e1;margin-bottom:6px;">نوع النشاط</label>
              <select id="business_type" name="business_type" class="form-control" required
                      style="width:100%;background:#1f2937;border:1px solid {{ $errors->has('business_type') ? '#ef4444' : '#4b5563' }};color:#fff;padding:10px;border-radius:8px;">
                <option value="">اختر نوع النشاط</option>
              </select>
              @error('business_type')
                <p style="color:#f87171;font-size:12px;margin-top:6px;">{{ $message }}</p>
              @enderror
            </div>
            <div>
              <label for="activity_description" style="display:block;color:#cbd5e1;margin-bottom:6px;">وصف النشاط</label>
              <textarea id="activity_description" name="activity_description" rows="3" class="form-control"
                        style="width:100%;background:#1f2937;border:1px solid {{ $errors->has('activity_description') ? '#ef4444' : '#4b5563' }};color:#fff;padding:10px;border-radius:8px;resize:none;">{{ old('activity_description') }}</textarea>
              @error('activity_description')
                <p style="color:#f87171;font-size:12px;margin-top:6px;">{{ $message }}</p>
              @enderror
            </div>
            <div>
              <label for="location" style="display:block;color:#cbd5e1;margin-bottom:6px;">الموقع</label>
              <input id="location" name="location" class="form-control"
                     value="{{ old('location') }}"
                     style="width:100%;background:#1f2937;border:1px solid {{ $errors->has('location') ? '#ef4444' : '#4b5563' }};color:#fff;padding:10px;border-radius:8px;" />
              @error('location')
                <p style="color:#f87171;font-size:12px;margin-top:6px;">{{ $message }}</p>
              @enderror
            </div>
          </div>
        </div>

        <!-- Owner Information -->
        <div class="glass-card" style="padding:16px;">
          <h3 style="color:#fff;margin-bottom:12px;display:flex;align-items:center;gap:8px;">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="#fbbf24"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
            معلومات المالك
          </h3>
          <div style="display:grid;gap:10px;">
            <div>
              @php $ownerFull = old('full_name', $owner['full_name'] ?? '');
                  // Detect Google/OAuth user either via Auth facade or session('user_id') fallback
                  $isGoogle = false;
                  if (auth()->check() && !empty(optional(auth()->user())->username)) { $isGoogle = true; }
                  elseif (session('user_id')) {
                      try { $isGoogle = (bool) \App\Models\User::where('id', session('user_id'))->whereNotNull('username')->value('username'); } catch (\Throwable $e) { $isGoogle = false; }
                  }
              @endphp
              @if($isGoogle && empty($ownerFull))
                {{-- Google user with no stored full_name: allow entering full name here for the first time. --}}
                <label for="full_name" style="display:block;color:#cbd5e1;margin-bottom:6px;">الاسم الكامل</label>
                <input id="full_name" name="full_name" class="form-control" required
                  value="{{ $ownerFull }}"
                  style="width:100%;background:#1f2937;border:1px solid {{ $errors->has('full_name') ? '#ef4444' : '#4b5563' }};color:#fff;padding:10px;border-radius:8px;" />
                @error('full_name')
                  <p style="color:#f87171;font-size:12px;margin-top:6px;">{{ $message }}</p>
                @enderror
                <p style="color:#9ca3af;font-size:12px;margin-top:6px;">أدخل اسمك الكامل للمرة الأولى هنا (سيُحفظ كاسم المالك في طلب الترخيص).</p>
              @else
                <label for="full_name" style="display:block;color:#cbd5e1;margin-bottom:6px;">الاسم الكامل</label>
                <input id="full_name" name="full_name" class="form-control" required
                  value="{{ $ownerFull }}"
                  @if(!empty($owner['full_name'])) readonly @endif
                  style="width:100%;background:#1f2937;border:1px solid {{ $errors->has('full_name') ? '#ef4444' : '#4b5563' }};color:#fff;padding:10px;border-radius:8px;" />
                @error('full_name')
                  <p style="color:#f87171;font-size:12px;margin-top:6px;">{{ $message }}</p>
                @enderror
                @if(!empty($owner) && !empty($owner['full_name']))
                  <p style="color:#9ca3af;font-size:12px;margin-top:6px;">معلومات المالك معروضة للقراءة فقط.</p>
                @endif
              @endif
            </div>
            <div>
              <label for="national_id" style="display:block;color:#cbd5e1;margin-bottom:6px;">رقم الهوية</label>
    <input id="national_id" name="national_id" class="form-control" required
      value="{{ old('national_id', $owner['national_id'] ?? '') }}"
      @if(!empty($owner) && !empty($owner['national_id'])) readonly @endif
      style="width:100%;background:#1f2937;border:1px solid {{ $errors->has('national_id') ? '#ef4444' : '#4b5563' }};color:#fff;padding:10px;border-radius:8px;" />
              @error('national_id')
                <p style="color:#f87171;font-size:12px;margin-top:6px;">{{ $message }}</p>
              @enderror
              @if(!empty($owner) && !empty($owner['national_id']))
                <p style="color:#9ca3af;font-size:12px;margin-top:6px;">رقم الهوية موجود في النظام ولا يمكن تغييره.</p>
              @else
                <p style="color:#9ca3af;font-size:12px;margin-top:6px;">إذا لم يكن رقم الهوية موجودًا سابقًا، يمكنك إدخاله هنا للمرة الأولى.</p>
              @endif
            </div>
            <div>
              <label for="email" style="display:block;color:#cbd5e1;margin-bottom:6px;">البريد الإلكتروني</label>
    <input id="email" name="email" type="email" class="form-control" required
      value="{{ old('email', $owner['email'] ?? '') }}"
      @if(!empty($owner['email'])) readonly @endif
      style="width:100%;background:#1f2937;border:1px solid {{ $errors->has('email') ? '#ef4444' : '#4b5563' }};color:#fff;padding:10px;border-radius:8px;" />
              @error('email')
                <p style="color:#f87171;font-size:12px;margin-top:6px;">{{ $message }}</p>
              @enderror
              @if(!empty($owner['email']))
                <p style="color:#9ca3af;font-size:12px;margin-top:6px;">هذا الحقل للعرض فقط.</p>
              @endif
            </div>
            <div>
              <label for="phone" style="display:block;color:#cbd5e1;margin-bottom:6px;">رقم الهاتف</label>
    <input id="phone" name="phone" class="form-control" required
      value="{{ old('phone', $owner['phone'] ?? '') }}"
      @if(!empty($owner['phone'])) readonly @endif
      style="width:100%;background:#1f2937;border:1px solid {{ $errors->has('phone') ? '#ef4444' : '#4b5563' }};color:#fff;padding:10px;border-radius:8px;" />
              @error('phone')
                <p style="color:#f87171;font-size:12px;margin-top:6px;">{{ $message }}</p>
              @enderror
              @if(!empty($owner['phone']))
                <p style="color:#9ca3af;font-size:12px;margin-top:6px;">هذا الحقل للعرض فقط.</p>
              @endif
            </div>
          </div>
        </div>
      </div>

      <!-- If user already has identity images, show them here (populated from controller existingDocs or derived from DB) -->
      @php
        // Prepare a best-effort displayDocs array: prefer $existingDocs passed from controller, else derive from license/user/investor records
        $displayDocs = $existingDocs ?? [];
        if (empty($displayDocs)) {
          try {
            $userId = $owner['id'] ?? auth()->id() ?? session('user_id') ?? null;
            $userObj = $userId ? \App\Models\User::find($userId) : null;
            $investorObj = null;
            if ($userObj) {
              try { $investorObj = \App\Models\Investor::where('user_id', $userObj->id)->first(); } catch (\Throwable $e) { $investorObj = null; }
            }

            $idFrontRaw = $lic->identity_image_front ?? ($userObj && $userObj->identity_image_front ? $userObj->identity_image_front : ($investorObj->identity_image_front ?? null));
            $idBackRaw  = $lic->identity_image_back  ?? ($userObj && $userObj->identity_image_back ? $userObj->identity_image_back : ($investorObj->identity_image_back ?? null));

            if (!empty($idFrontRaw)) {
              if (preg_match('/^https?:\/\//i', $idFrontRaw)) {
                $displayDocs['identity_front'] = ['url' => $idFrontRaw];
              } else {
                try {
                  $exists = \Illuminate\Support\Facades\Storage::disk('public')->exists($idFrontRaw);
                  $publicPath = public_path('storage/' . ltrim($idFrontRaw, '/'));
                  if ($exists && file_exists($publicPath)) {
                    $displayDocs['identity_front'] = ['url' => \Illuminate\Support\Facades\Storage::url($idFrontRaw)];
                  } elseif ($exists) {
                    $displayDocs['identity_front'] = ['url' => route('investor.image', ['investor' => $investorObj ? $investorObj->id : ($userObj ? $userObj->id : ($lic->user_id ?? $lic->id)), 'side' => 'front'])];
                  } else {
                    $displayDocs['identity_front'] = ['url' => route('investor.image', ['investor' => $investorObj ? $investorObj->id : ($userObj ? $userObj->id : ($lic->user_id ?? $lic->id)), 'side' => 'front'])];
                  }
                } catch (\Exception $e) {
                  $displayDocs['identity_front'] = ['url' => route('investor.image', ['investor' => $investorObj ? $investorObj->id : ($userObj ? $userObj->id : ($lic->user_id ?? $lic->id)), 'side' => 'front'])];
                }
              }
            }

            if (!empty($idBackRaw)) {
              if (preg_match('/^https?:\/\//i', $idBackRaw)) {
                $displayDocs['identity_back'] = ['url' => $idBackRaw];
              } else {
                try {
                  $existsB = \Illuminate\Support\Facades\Storage::disk('public')->exists($idBackRaw);
                  $publicPathB = public_path('storage/' . ltrim($idBackRaw, '/'));
                  if ($existsB && file_exists($publicPathB)) {
                    $displayDocs['identity_back'] = ['url' => \Illuminate\Support\Facades\Storage::url($idBackRaw)];
                  } elseif ($existsB) {
                    $displayDocs['identity_back'] = ['url' => route('investor.image', ['investor' => $investorObj ? $investorObj->id : ($userObj ? $userObj->id : ($lic->user_id ?? $lic->id)), 'side' => 'back'])];
                  } else {
                    $displayDocs['identity_back'] = ['url' => route('investor.image', ['investor' => $investorObj ? $investorObj->id : ($userObj ? $userObj->id : ($lic->user_id ?? $lic->id)), 'side' => 'back'])];
                  }
                } catch (\Exception $e) {
                  $displayDocs['identity_back'] = ['url' => route('investor.image', ['investor' => $investorObj ? $investorObj->id : ($userObj ? $userObj->id : ($lic->user_id ?? $lic->id)), 'side' => 'back'])];
                }
              }
            }
          } catch (\Throwable $e) { /* ignore fallback errors */ }
        }
      @endphp

    

      <!-- Non-JS fallback: plain file inputs so uploads are available even if JS is disabled or dynamic UI is not loaded -->
      <noscript>
        <div class="glass-card" style="margin-top:12px;padding:12px;">
          <h3 style="color:#fff;margin-bottom:8px;">رفع المستندات (بدون جافاسكربت)</h3>
          <div style="display:grid;gap:10px;">
            @if(empty($existingDocs))
            <div>
              <label style="color:#cbd5e1;display:block;margin-bottom:6px;">صورة الهوية (أمام)</label>
              <input type="file" name="identity_front" accept=".jpg,.jpeg,.png,.pdf" />
            </div>
            <div>
              <label style="color:#cbd5e1;display:block;margin-bottom:6px;">صورة الهوية (خلف)</label>
              <input type="file" name="identity_back" accept=".jpg,.jpeg,.png,.pdf" />
            </div>
            @else
            <div style="color:#9ca3af;">صور الهوية مخفية لأن النظام يملك صوراً بالفعل.</div>
            @endif
            <div>
              <label style="color:#cbd5e1;display:block;margin-bottom:6px;">كشف حساب بنكي (اختياري)</label>
              <input type="file" name="bank_statement" accept=".pdf,.jpg,.jpeg,.png" />
            </div>
            <div>
              <label style="color:#cbd5e1;display:block;margin-bottom:6px;">شهادة راتب (اختياري)</label>
              <input type="file" name="salary_certificate" accept=".pdf,.jpg,.jpeg,.png" />
            </div>
          </div>
        </div>
      </noscript>

        <section style="position:relative;z-index:10;padding-bottom:24px;">
    <div style="padding:0 16px;">
      <!-- Required Documents -->
      <div style="margin-bottom:18px;">
        <h2 style="color:#fff;font-size:22px;margin-bottom:12px;display:flex;align-items:center;gap:8px;">
          <svg width="20" height="20" viewBox="0 0 20 20" fill="#ef4444"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
          المستندات المطلوبة
        </h2>
        <div id="required-list" style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;"></div>
      </div>

      <!-- Optional Documents -->
      <div style="margin-bottom:18px;">
        <h2 style="color:#fff;font-size:20px;margin-bottom:12px;display:flex;align-items:center;gap:8px;">
          <svg width="20" height="20" viewBox="0 0 20 20" fill="#22c55e"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v4a1 1 0 002 0V7zm-1 8a1.25 1.25 0 110-2.5A1.25 1.25 0 0110 15z" clip-rule="evenodd"/></svg>
          مستندات اختيارية
        </h2>
        <div id="optional-list" style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;"></div>
      </div>
    </div>
  </section>

  <script>
    // Pass server-side validation errors to client JS so they can be shown inline on document cards
    window.serverValidationErrors = @json($errors->messages() ?? []);
  </script>




      <!-- Submit Button -->
      <div style="text-align:center;margin-top:20px;">
        <div id="required-docs-error" style="display:none;margin-bottom:10px;color:#fca5a5;">الرجاء رفع صور الهوية الأمامية والخلفية قبل المتابعة.</div>
        <button type="submit" id="submit-license" class="btn-primary" style="font-size:16px;padding:12px 32px;">تقديم الطلب</button>
      </div>



      <!-- Process Info -->
      <div class="glass-card" style="margin-top:20px;padding:16px;">
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;text-align:center;">
          <div>
            <div style="width:48px;height:48px;margin:0 auto 8px;display:flex;align-items:center;justify-content:center;border-radius:50%;background:linear-gradient(90deg,#3b82f6,#06b6d4);">
              <svg width="24" height="24" viewBox="0 0 20 20" fill="#fff"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/></svg>
            </div>
            <h4 style="color:#fff;margin-bottom:4px;font-weight:600;">السرعة</h4>
            <p style="color:#9ca3af;font-size:12px;">مراجعة خلال 24-48 ساعة</p>
          </div>
          <div>
            <div style="width:48px;height:48px;margin:0 auto 8px;display:flex;align-items:center;justify-content:center;border-radius:50%;background:linear-gradient(90deg,#22c55e,#14b8a6);">
              <svg width="24" height="24" viewBox="0 0 20 20" fill="#fff"><path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            </div>
            <h4 style="color:#fff;margin-bottom:4px;font-weight:600;">الأمان</h4>
            <p style="color:#9ca3af;font-size:12px;">حماية كاملة للبيانات</p>
          </div>
          <div>
            <div style="width:48px;height:48px;margin:0 auto 8px;display:flex;align-items:center;justify-content:center;border-radius:50%;background:linear-gradient(90deg,#f59e0b,#f97316);">
              <svg width="24" height="24" viewBox="0 0 20 20" fill="#fff"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            </div>
            <h4 style="color:#fff;margin-bottom:4px;font-weight:600;">الجودة</h4>
            <p style="color:#9ca3af;font-size:12px;">معايير عالمية معتمدة</p>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>
<script>
  $(function(){
    var requiredDocuments = [
      { id:'identity_front', name:'صورة الهوية (الوجه الأمامي)', description:'صورة واضحة للوجه الأمامي من الهوية الوطنية', acceptedTypes: '.jpg, .jpeg, .png, .pdf', maxSize: '5 MB', icon: '🆔' },
      { id:'identity_back',  name:'صورة الهوية (الوجه الخلفي)', description:'صورة واضحة للوجه الخلفي من الهوية الوطنية', acceptedTypes: '.jpg, .jpeg, .png, .pdf', maxSize: '5 MB', icon: '🆔' }
    ];
    var optionalDocuments = [
      { id:'bank_statement',  name:'كشف حساب بنكي', description:'كشف حساب بنكي حديث (آخر 3 أشهر) (اختياري)', acceptedTypes: '.pdf, .jpg, .jpeg, .png', maxSize: '10 MB', icon: '🏦' },
      { id:'salary_certificate', name:'شهادة راتب', description:'شهادة راتب أو إثبات دخل من جهة العمل (اختياري)', acceptedTypes: '.pdf, .jpg, .jpeg, .png', maxSize: '5 MB', icon: '�' }
    ];

  // existingDocs is populated by controller when user already has identity images
  window.existingDocs = @json($existingDocs ?? []);

  var uploadedFiles = {};   // {id: {name,size,type,uploadDate,status}}
    var uploadProgress = {};  // {id: percent}

    function renderList($container, docs, isRequired){
      $container.empty();
      docs.forEach(function(doc){
        // If this is an identity doc and controller provided existingDocs, render static preview instead of input
        if ((doc.id === 'identity_front' || doc.id === 'identity_back') && window.existingDocs && window.existingDocs[doc.id]) {
          var ed = window.existingDocs[doc.id];
          var staticCard = $('<div/>').addClass('glass-card').css({padding:'14px'});
          staticCard.append($('<div/>').css({color:'#fff',fontWeight:700,marginBottom:'8px'}).text(doc.name));
          var imgWrap = $('<div/>').css({textAlign:'center'}).append($('<img/>').attr('src', ed.url).css({maxHeight:'140px',borderRadius:'8px',border:'1px solid rgba(255,255,255,0.04)'}));
          staticCard.append(imgWrap);
          staticCard.append($('<div/>').css({color:'#9ca3af',fontSize:'12px',marginTop:'8px'}).text('هذه الصورة محفوظة مسبقاً ولا يمكن تعديلها في هذا الطلب.'));
          $container.append(staticCard);
          return; // skip normal input rendering
        }
        var card = $('<div/>').addClass('glass-card').css({padding:'14px'}).attr('data-doc', doc.id);
        var header = $('<div/>').css({display:'flex',justifyContent:'space-between',alignItems:'flex-start',marginBottom:'8px'});
        var title = $('<div/>').css({color:'#fff',fontWeight:'700'}).html('<span style="font-size:20px;margin-left:8px;">'+doc.icon+'</span>'+doc.name);
        var badge = isRequired ? $('<span/>').text('مطلوب').css({background:'#ef4444',color:'#fff',borderRadius:'6px',padding:'2px 8px',fontSize:'12px'}) : null;
        header.append(title); if(badge) header.append(badge);

        var desc = $('<p/>').text(doc.description).css({color:'#cbd5e1',fontSize:'12px',margin:'6px 0'});
        var meta = $('<div/>').css({display:'flex',gap:'12px',color:'#9ca3af',fontSize:'12px'}).append(
          $('<span/>').text('الأنواع المقبولة: '+doc.acceptedTypes),
          $('<span/>').text('الحد الأقصى: '+doc.maxSize)
        );

        var body = $('<div/>').attr('data-body', doc.id);
        var inputId = 'file_'+doc.id;
  // give the input a name matching the document id so it's submitted in the multipart form
  var input = $('<input/>').attr({type:'file',id:inputId,name:doc.id,accept:doc.acceptedTypes.replace(/\s/g,'')}).css({display:'none'});
        var choose = $('<label/>').attr('for',inputId).css({display:'flex',alignItems:'center',justifyContent:'center',width:'100%',padding:'24px',border:'2px dashed #4b5563',borderRadius:'10px',cursor:'pointer'}).hover(function(){ $(this).css('borderColor','#22d3ee'); }, function(){ $(this).css('borderColor','#4b5563'); });
        choose.append($('<div/>').css({textAlign:'center'}).append(
          $('<svg/>').attr({width:48,height:48,viewBox:'0 0 24 24'}).css({display:'block',margin:'0 auto 8px',color:'#9ca3af'}).html('<path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>'),
          $('<div/>').css({color:'#cbd5e1',fontWeight:600}).text('اختيار ملف'),
          $('<p/>').css({color:'#9ca3af',fontSize:'12px',marginTop:'4px'}).text('اسحب وأفلت الملف هنا أو انقر للاختيار')
        ));

        var progressWrap = $('<div/>').attr('data-progress', doc.id).css({display:'none'});
        var progTop = $('<div/>').css({display:'flex',justifyContent:'space-between',fontSize:'12px',color:'#cbd5e1',margin:'6px 0'}).append(
          $('<span/>').text('تقدم الرفع'),
          $('<span/>').attr('data-prog-text', doc.id).css({color:'#22d3ee'}).text('0%')
        );
        var progBar = $('<div/>').css({height:'8px',background:'#1f2937',borderRadius:'6px',overflow:'hidden'}).append(
          $('<div/>').attr('data-prog-bar', doc.id).css({height:'8px',background:'#22d3ee',width:'0%'})
        );
        progressWrap.append(progTop, progBar);

        var fileInfoWrap = $('<div/>').attr('data-info', doc.id).css({display:'none',background:'#1f2937',padding:'12px',borderRadius:'10px'});
        var fileHeader = $('<div/>').css({display:'flex',justifyContent:'space-between',alignItems:'flex-start',marginBottom:'8px'});
        var fileTitle = $('<div/>');
        var fileName = $('<h4/>').css({color:'#fff',margin:'0 0 4px 0'});
        var fileMeta = $('<div/>').css({display:'flex',gap:'12px',fontSize:'12px',color:'#9ca3af'});
        var statusBadge = $('<span/>').attr('data-status', doc.id).css({background:'#22c55e',color:'#fff',borderRadius:'6px',padding:'2px 8px',fontSize:'12px'}).text('مكتمل');
        fileHeader.append(fileTitle.append(fileName, fileMeta), statusBadge);
        var actions = $('<div/>').css({display:'flex',gap:'8px'});
        var viewBtn = $('<button/>').text('عرض الملف').addClass('btn-secondary').css({borderColor:'#22d3ee',color:'#22d3ee'}).on('click', function(){
          var f = uploadedFiles[doc.id]; if(!f || !f.file) return; var url = URL.createObjectURL(f.file); window.open(url, '_blank');
        });
        var removeBtn = $('<button/>').text('إزالة الملف').addClass('btn-secondary').css({borderColor:'#f87171',color:'#f87171'}).on('click', function(){ removeFile(doc.id); });
        actions.append(viewBtn, removeBtn);
        fileInfoWrap.append(fileHeader, actions);

        body.append(input, choose, progressWrap, fileInfoWrap);
        card.append(header, desc, meta, body);
        $container.append(card);

        input.on('change', function(e){ handleFileUpload(doc.id, e.target.files[0]); });
      });
    }

    function handleFileUpload(documentId, file){
      if(!file) return;
      uploadProgress[documentId] = 0;
      var $progWrap = $('[data-progress="'+documentId+'"]');
      var $progText = $('[data-prog-text="'+documentId+'"]');
      var $progBar  = $('[data-prog-bar="'+documentId+'"]');
      var $infoWrap = $('[data-info="'+documentId+'"]');
      var $body     = $('[data-body="'+documentId+'"]');

      $progWrap.show();

      var interval = setInterval(function(){
        var curr = uploadProgress[documentId] || 0;
        var next = curr + 10;
        if(next >= 100){
          next = 100;
          clearInterval(interval);
          var sizeMB = (file.size / 1024 / 1024).toFixed(2) + ' MB';
          uploadedFiles[documentId] = {
            file: file,
            name: file.name,
            size: sizeMB,
            type: file.type,
            uploadDate: new Date().toLocaleDateString('ar-SA'),
            status: 'completed'
          };
          fillFileInfo(documentId);
          $progWrap.hide();
        }
        uploadProgress[documentId] = next;
        $progText.text(next + '%');
        $progBar.css('width', next + '%');
        updateSummary();
      }, 200);
    }

    function removeFile(documentId){
      delete uploadedFiles[documentId];
      delete uploadProgress[documentId];
      var $body = $('[data-body="'+documentId+'"]');
      $body.find('[data-info]')  .hide();
      $body.find('[data-progress]').hide();
      $body.find('label[for="file_'+documentId+'"]').show();
      $body.find('input[type=file]')[0].value = '';
      updateSummary();
    }

    function fillFileInfo(documentId){
      var info = uploadedFiles[documentId];
      if(!info) return;
      var $body = $('[data-body="'+documentId+'"]');
      $body.find('label[for="file_'+documentId+'"]').hide();
      var $infoWrap = $body.find('[data-info]');
      $infoWrap.find('h4').text(info.name);
      $infoWrap.find('div').eq(2); // placeholder no-op
      var $meta = $infoWrap.find('div').eq(0).find('div').eq(1);
      // If structure changes, fallback fill directly:
      $infoWrap.find('h4').next('div').remove();
      var meta = $('<div/>').css({display:'flex',gap:'12px',fontSize:'12px',color:'#9ca3af'}).append(
        $('<span/>').text('حجم الملف: ' + info.size),
        $('<span/>').text('تاريخ الرفع: ' + info.uploadDate)
      );
      $infoWrap.find('h4').after(meta);
      $infoWrap.show();
    }

    function updateSummary(){
      var completedUploads = Object.keys(uploadedFiles).length;
      var totalDocs = requiredDocuments.length + optionalDocuments.length;
      var percent = totalDocs ? Math.round((completedUploads / totalDocs) * 100) : 0;
      // Optional: summary UI if present (safe to no-op if not existing)
      if($('#summary-badge').length){
        $('#summary-badge').text(completedUploads + '/' + totalDocs + ' مكتمل');
        $('#summary-progress').css('width', percent + '%');
        $('#summary-text').text(completedUploads + ' من ' + totalDocs + ' مستندات تم رفعها');
      }

  var requiredOk = requiredDocuments.every(function(d){ return !!uploadedFiles[d.id] || !!(window.existingDocs && window.existingDocs[d.id]); });
  // keep submit button enabled so client-side validation runs and shows inline messages
    }

    // Owner lookup behavior: when national_id field changes, query /owner/lookup
    function applyOwnerLookupBehavior(contextSelector){
      var $nid = $(contextSelector + ' #national_id');
      var $full = $(contextSelector + ' #full_name');
      var $email = $(contextSelector + ' #email');
      var $phone = $(contextSelector + ' #phone');

      $nid.on('blur change', function(){
        var nid = $(this).val() || '';
        nid = nid.trim();
        if (!nid) return;
        // call owner lookup
        $.post("{{ route('owner.lookup') }}", { national_id: nid, _token: '{{ csrf_token() }}' }, function(resp){
          if (resp && resp.found) {
            // populate and lock only when locked=true (already used) or owner exists
            $full.val(resp.owner.full_name || '');
            $email.val(resp.owner.email || '');
            $phone.val(resp.owner.phone || '');
            if (resp.locked) { $nid.prop('readonly', true); }
          }
        }, 'json').fail(function(){ /* ignore */ });
      });
    }

    // Initial Render
    renderList($('#required-list'), requiredDocuments, true);
    renderList($('#optional-list'), optionalDocuments, false);
    updateSummary();
    // If server-side validation returned document errors, render them inline now
    if (window.serverValidationErrors) {
      ['identity_front','identity_back'].forEach(function(docId){
        var msgs = window.serverValidationErrors[docId] || window.serverValidationErrors[docId.replace('_','-')];
        if (msgs && msgs.length) {
          var $card = $('[data-doc="'+docId+'"]');
          if ($card.length) {
            if ($card.find('.field-error.missing-doc').length === 0) {
              var err = $('<div>').addClass('field-error missing-doc').text(msgs.join(', ')).css({'color':'#ffb4b4','font-size':'12px','margin-top':'6px'});
              $card.find('[data-body="'+docId+'"] > label[for], [data-body="'+docId+'"]').first().after(err);
            }
            $card.css('box-shadow','0 0 0 2px #ef4444');
            setTimeout(function(){ $card.css('box-shadow',''); }, 1200);
          }
        }
      });
    }
    // attach owner lookup behavior to the license form after render
    applyOwnerLookupBehavior('#license-form');
  });
</script>
<script>
  $(function(){
    // business types (مطابقة للواجهة)
    var businessTypes = [
      'تجارة تجزئة','تجارة جملة','خدمات مهنية','مطاعم وأغذية','تصنيع','تقنية معلومات','استشارات','عقارات','نقل ولوجستيات','تعليم وتدريب','صحة وطب','سياحة وضيافة'
    ];
    var $bt = $('#business_type');
    businessTypes.forEach(function(t){ $bt.append($('<option/>').val(t).text(t)); });
    var oldBT = @json(old('business_type'));
    if(oldBT){ $bt.val(oldBT); }

    function genId(){
      // 8 hex uppercase
      var s = (Math.random().toString(16).slice(2) + Math.random().toString(16).slice(2)).toUpperCase();
      return s.slice(0,8);
    }

    // On load, if we got a flash status like 'license_submitted:{id}', show success panel
    @if(session('status') && \Illuminate\Support\Str::startsWith(session('status'), 'license_submitted:'))
      (function(){
        var id = @json(explode(':', session('status'))[1] ?? '');
        $('#success-license-id').text(id);
        $('#license-form').hide();
        $('#license-success').show();
        var href = @json(route('investor.dashboard')) + '?tab=applications&last_id=' + encodeURIComponent(id);
        $('#track-link').attr('href', href);
      })();
    @endif

    // NOTE: submit handling merged below to ensure a single validation pass
    // Client-side inline validation for license form fields
    function clearErrors(){
      $('.field-error').remove();
      $('.invalid-field').removeClass('invalid-field');
    }
    function showError($el, msg){
      $el.addClass('invalid-field');
      var err = $('<div>').addClass('field-error').text(msg).css({'color':'#ffb4b4','font-size':'12px','margin-top':'6px'});
      $el.next('.field-error').remove();
      $el.after(err);
    }
    function validateEmail(v){ return /^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(v); }
    function validatePhone(v){ return v && v.replace(/[^0-9+]/g,'').length >= 8; }
    function validateBank(v){ return /^SA[0-9]{16,}$/.test(v) || v.length >= 10; }

    // Unified submit handler: validate required fields + required documents
    $('#license-form').on('submit', function(e){
      clearErrors();
      var firstInvalid = null;

      // Required fields list (activity_description and optional documents are NOT required)
      var requiredFields = [
        {selector:'#business_name', message:'الرجاء إدخال اسم النشاط التجاري'},
        {selector:'#business_type', message:'الرجاء اختيار نوع النشاط'},
        {selector:'#location', message:'الرجاء إدخال الموقع'},
        {selector:'#full_name', message:'الرجاء إدخال الاسم الكامل'},
        {selector:'#national_id', message:'الرجاء إدخال رقم الهوية'},
        {selector:'#email', message:'الرجاء إدخال البريد الإلكتروني'},
        {selector:'#phone', message:'الرجاء إدخال رقم الهاتف'}
      ];

      requiredFields.forEach(function(f){
        var $el = $(f.selector);
        if (!$el.length) return; // not present in DOM -> skip
        // If field is readonly/disabled but has a non-empty value, treat as valid
        var isReadonly = $el.prop('readonly') || $el.prop('disabled');
        var val = ($el.val()||'').toString().trim();
        if (isReadonly && val) return; // prefilled readonly OK
        if (!val) {
          showError($el, f.message);
          firstInvalid = firstInvalid || $el;
        } else {
          // additional format checks for some fields
          if (f.selector === '#email' && !validateEmail(val)) { showError($el, 'صيغة البريد الإلكتروني غير صحيحة'); firstInvalid = firstInvalid || $el; }
          if (f.selector === '#phone' && !validatePhone(val)) { showError($el, 'رقم الهاتف غير صحيح'); firstInvalid = firstInvalid || $el; }
        }
      });

      // Check required document uploads (identity front/back)
      var missingDocs = [];
      if (typeof requiredDocuments !== 'undefined') {
        requiredDocuments.forEach(function(d){
          var present = !!uploadedFiles[d.id] || !!(window.existingDocs && window.existingDocs[d.id]);
          if (!present) missingDocs.push(d);
        });
      }

      if (missingDocs.length) {
        e.preventDefault();
        $('#required-docs-error').show();
        // add inline errors under each missing card and highlight
        missingDocs.forEach(function(d){
          var $card = $('[data-doc="'+d.id+'"]');
          if ($card.length) {
            if ($card.find('.field-error.missing-doc').length === 0) {
              var err = $('<div>').addClass('field-error missing-doc').text('الرجاء رفع '+d.name).css({'color':'#ffb4b4','font-size':'12px','margin-top':'6px'});
              $card.find('[data-body="'+d.id+'"] > label[for], [data-body="'+d.id+'"]').first().after(err);
            }
            $card.css('box-shadow','0 0 0 2px #ef4444');
            setTimeout(function(){ $card.css('box-shadow',''); }, 1200);
          }
        });
        // scroll to first missing doc
        var $firstDoc = $('[data-doc="'+missingDocs[0].id+'"]');
        if ($firstDoc.length) { $('html,body').animate({scrollTop: $firstDoc.offset().top - 120}, 220); }
        // focus first invalid field if exists too
        if (firstInvalid) { firstInvalid.focus(); }
        return false;
      }

      if (firstInvalid) { e.preventDefault(); firstInvalid.focus(); return false; }
      // all checks passed -> allow submit
      return true;
    });
  });
</script>
@endsection
