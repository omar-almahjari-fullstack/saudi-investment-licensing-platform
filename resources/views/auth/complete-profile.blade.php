@extends('layouts.app')

@section('content')

<style>
    /* Scoped to this page to match site look-and-feel similar to login view */
    .auth-root{position:relative;min-height:80vh;display:flex;align-items:center;justify-content:center;padding:48px 18px;background:transparent;color:#fff;direction:rtl}
    .card-header{text-align:center;margin-bottom:6px}
    h2.card-title{font-size:20px;margin:0;color:#fff}
    p.card-sub{margin:6px 0 12px;color:#9fb0bf}

    /* compact card for complete-profile */
    .card{position:relative;z-index:10;min-height:320px;max-width:520px;width:100%;background:linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01));backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,0.04);box-shadow:0 16px 40px rgba(0,0,0,0.45);border-radius:12px;padding:20px 20px;display:flex;flex-direction:column}

    label.field-label{display:block;margin-bottom:6px;color:#fff;font-weight:700}
    /* Inputs: dark background, gold border only */
    input.field{width:100%;height:40px;padding:10px 12px;border-radius:8px;background:#0f1724;border:1px solid rgba(255,215,0,0.45);color:#fff;font-weight:600;box-sizing:border-box}
    .btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;width:100%;padding:12px;border-radius:10px;border:none;color:#000;background:linear-gradient(135deg,#ffd97a 0%,#ffb84d 100%);cursor:pointer;font-weight:800}
    .notice-box{padding:12px;border-radius:10px;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.04);color:#e6f7f8}
    .muted{color:#9fb0bf}
    .mb-3{margin-bottom:12px}
    .mb-2{margin-bottom:8px}
</style>

<div class="auth-root">
    <div class="card">
        <div class="card-header" style="display:flex;flex-direction:row;align-items:center;gap:12px;justify-content:center">
            <div style="width:64px;height:64px;border-radius:999px;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#00ffff 0%,#0099cc 100%);box-shadow:0 6px 18px rgba(0,0,0,0.25)">
                <svg width="34" height="34" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path d="M12 12a4 4 0 100-8 4 4 0 000 8z" fill="#000000"/>
                    <path d="M4 20a8 8 0 0116 0v1H4v-1z" fill="#000000"/>
                </svg>
            </div>
            <div>
              <h2 class="card-title">استكمال بيانات الحساب من خلال Google</h2>
              <p class="card-sub">عوائد الخليج للاستثمار السعودي — استكمال الملف الشخصي</p>
            </div>
        </div>

        @if(session('status'))
            <div class="notice-box mb-3">{{ session('status') }}</div>
        @endif

        @if($errors->any())
            <div class="notice-box mb-3" style="border-color:rgba(220,38,38,0.12);color:#fecaca">
                <ul style="margin:0;padding-right:18px">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="mb-3 notice-box">
                <strong>ملاحظة مهمة:</strong>
                <p class="muted" style="margin:6px 0 0;line-height:1.4">
                    رقم الجوال مطلوب وسيُحفظ أيضاً في ملفك الشخصي — أدخله بدقة. (مرة واحدة).
                </p>
        </div>

        <form method="post" action="{{ route('auth.complete_profile.post') }}">
            @csrf

            {{-- Allow entering full name (will be saved to users.full_name) --}}
            <div class="mb-3">
                <label class="field-label">الاسم الكامل</label>
                <input type="text" name="full_name" class="field" value="{{ old('full_name', $full_name ?? '') }}" placeholder="الاسم الكامل كما في الهوية" required />
            </div>

            <div class="mb-3">
                <label class="field-label">البريد الإلكتروني</label>
                <input type="email" name="email" class="field" value="{{ old('email', $email ?? '') }}" readonly />
            </div>

            <div class="mb-3">
                <label class="field-label">رقم الجوال</label>
                <input type="text" name="mobile_number" class="field" value="{{ old('mobile_number') }}" placeholder="مثال: 05xxxxxxxx" required />
            </div>

            <div class="mb-3">
                <label class="field-label">كلمة المرور</label>
                <input type="password" name="password" class="field" autocomplete="new-password" required />
            </div>

            <div class="mb-3">
                <label class="field-label">تأكيد كلمة المرور</label>
                <input type="password" name="password_confirmation" class="field" autocomplete="new-password" required />
            </div>

            <div class="mt-2">
                <button type="submit" class="btn">✅ إكمال وتهيئة الحساب</button>
            </div>
        </form>

    </div>
</div>

@endsection
