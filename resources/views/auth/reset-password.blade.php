@extends('layouts.app')

@section('title','إعادة تعيين كلمة المرور')

@section('content')
<style>
    .fp-root{min-height:80vh;display:flex;align-items:center;justify-content:center;padding:40px;background:#051226;color:#fff;direction:rtl;position:relative;overflow:hidden}
    .fp-card{background:linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01));backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,0.04);padding:28px;border-radius:12px;max-width:520px;width:92%;box-shadow:0 12px 30px rgba(0,0,0,0.5);text-align:center}
    .fp-card h2{margin:0 0 8px;font-size:24px}
    .fp-card p{color:#9fb0bf;margin:0 0 14px;max-width:680px;margin-left:auto;margin-right:auto}
    input.fp-field{width:220px;padding:10px;border-radius:8px;border:1px solid #D4AF37;background:#0f1724;color:#fff}
    /* Login-related buttons: blue background, black text */
    .fp-btn{margin-left:8px;padding:8px 12px;border-radius:8px;background:linear-gradient(135deg,#06b6d4 0%,#0284c7 100%);color:#000;border:none;cursor:pointer;font-weight:700}
    .status{margin-top:12px;padding:10px;background:rgba(34,197,94,0.12);border-radius:8px;color:#bbf7d0}
    .fp-form{display:inline-flex;align-items:center;justify-content:center;gap:8px;margin-top:10px}
    /* subtle links (distinct from primary buttons) */
    .link-outline{display:inline-block;margin-top:12px;padding:4px 6px;border-radius:6px;border:none;color:#cbd5e1;background:transparent;text-decoration:none;font-weight:600;letter-spacing:0.2px;font-size:13px}
    @media (max-width:640px){ .fp-card{padding:18px;border-radius:10px;width:94%} .fp-card h2{font-size:22px} input.fp-field{width:160px} .fp-form{display:flex;flex-direction:column;gap:10px} .fp-btn{margin-left:0;width:100%} }
</style>

<div class="fp-root">
    <div class="fp-card">
        <h2>إعادة تعيين كلمة المرور</h2>
        <p>أدخل رمز التحقق المرسل إلى بريدك والكلمة الجديدة التي تريد استخدامها.</p>

        @if($errors->any())
            <div class="status" style="background:rgba(239,68,68,0.06);color:#fecaca">{{ $errors->first() }}</div>
        @endif

        @if(session('status'))
            <div class="status">{{ session('status') }}</div>
        @endif

        <form method="post" action="{{ route('auth.reset-password.submit') }}">
            @csrf
            <input type="hidden" name="email" value="{{ old('email', $email ?? '') }}" />

            <label for="password" style="display:block;margin:8px 0;color:#fff">كلمة المرور الجديدة</label>
            <div class="fp-form">
                <input id="password" name="password" class="fp-field" type="password" placeholder="كلمة المرور الجديدة" required />
            </div>

            <label for="password_confirmation" style="display:block;margin:8px 0;color:#fff">تأكيد كلمة المرور</label>
            <div class="fp-form">
                <input id="password_confirmation" name="password_confirmation" class="fp-field" type="password" placeholder="تأكيد كلمة المرور" required />
            </div>

            <div style="margin-top:14px">
                <button type="submit" class="fp-btn">إعادة التعيين</button>
            </div>
        </form>

        <div style="text-align:center;margin-top:18px">
            <a href="{{ route('auth.login.form') }}" class="link-outline">العودة لتسجيل الدخول</a>
            <a href="{{ route('auth.forgot-password') }}" class="link-outline" style="margin-right:8px">طلب رمز جديد</a>
        </div>
    </div>
</div>

@endsection
