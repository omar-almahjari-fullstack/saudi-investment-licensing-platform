@extends('layouts.app')

@section('title','نسيت كلمة المرور - المستثمر')

@section('content')
<style>
    .fp-root{min-height:80vh;display:flex;align-items:center;justify-content:center;padding:40px;background:#051226;color:#fff;direction:rtl;position:relative;overflow:hidden}
    .fp-card{background:linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01));backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,0.04);padding:36px;border-radius:14px;max-width:780px;width:90%;box-shadow:0 18px 50px rgba(0,0,0,0.6);text-align:center}
    .fp-card h2{margin:0 0 8px;font-size:30px}
    .fp-card p{color:#9fb0bf;margin:0 0 18px;max-width:820px;margin-left:auto;margin-right:auto}
    input.fp-field{width:260px;padding:10px;border-radius:8px;border:1px solid rgba(255,255,255,0.06);background:#0f1724;color:#fff}
    /* Login-related buttons: blue background, black text */
    .fp-btn{margin-left:8px;padding:8px 12px;border-radius:8px;background:linear-gradient(135deg,#06b6d4 0%,#0284c7 100%);color:#000;border:none;cursor:pointer;font-weight:700}
    .status{margin-top:12px;padding:10px;background:rgba(34,197,94,0.12);border-radius:8px;color:#bbf7d0}

    .fp-form{display:inline-flex;align-items:center;justify-content:center;gap:8px;margin-top:10px}
    /* subtle links (distinct from primary buttons) */
    .link-outline{display:inline-block;margin-top:12px;padding:4px 6px;border-radius:6px;border:none;color:#cbd5e1;background:transparent;text-decoration:none;font-weight:600;font-size:13px}

    .plus{position:absolute;color:rgba(255,255,255,0.06);font-size:18px;transform:rotate(0deg);user-select:none}
    .plus.p1{top:8%;right:22%}
    .plus.p2{top:18%;left:12%}
    .plus.p3{top:30%;right:10%}
    .plus.p4{bottom:24%;left:28%}
    .plus.p5{bottom:18%;right:18%}
    .plus.p6{top:44%;left:50%}

    @media (max-width:640px){
        .fp-card{padding:22px;border-radius:10px;width:94%}
        .fp-card h2{font-size:22px}
        input.fp-field{width:180px}
        .fp-form{display:flex;flex-direction:column;gap:10px}
        .fp-btn{margin-left:0;width:100%}
    }
</style>

<div class="fp-root">
    <span class="plus p1">+</span>
    <span class="plus p2">+</span>
    <span class="plus p3">+</span>
    <span class="plus p4">+</span>
    <span class="plus p5">+</span>
    <span class="plus p6">+</span>

    <div class="fp-card">
        <h2>نسيت كلمة المرور</h2>
        <p>أدخل بريدك الإلكتروني للحصول على تعليمات إعادة تعيين كلمة المرور. (هذه عملية محاكاة في النسخة المحلية)</p>

        @if(session('status'))
            <div class="status">{{ session('status') }}</div>
        @endif

        <form method="post" action="{{ route('auth.forgot-password.send') }}">
            @csrf
            <label for="email" style="display:block;margin:8px 0;color:#9fb0bf">البريد الإلكتروني</label>
            <div class="fp-form">
                <input id="email" name="email" class="fp-field" type="email" placeholder="you@example.com" required />
                <button type="submit" class="fp-btn">إرسال رابط إعادة التعيين</button>
            </div>
        </form>

        <div style="text-align:center;margin-top:18px">
            <a href="{{ route('auth.login.form') }}" class="link-outline">العودة لتسجيل الدخول</a>
        </div>
    </div>
</div>

@endsection
