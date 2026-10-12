@extends('layouts.app')

@section('title','تسجيل دخول المدير')

@section('content')

<style>
    /* Minimal scoped CSS to reproduce AdminLogin.jsx look using Tailwind-like classes where possible. */
    .admin-root{position:relative;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:48px 18px;background:#051226;color:#fff;direction:rtl}
    .digital-background{position:absolute;inset:0;z-index:0;background:linear-gradient(180deg, #02121a, #031425 40%)}
    /* taller card: use min-height and generous vertical padding to emphasize height */
    .card{position:relative;z-index:10;min-height:420px;max-width:560px;width:100%;background:linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01));backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,0.04);box-shadow:0 20px 50px rgba(0,0,0,0.65);border-radius:14px;padding:34px 30px;display:flex;flex-direction:column;justify-content:space-between}
    .card-header{text-align:center;margin-bottom:6px}
    .avatar-wrap{width:96px;height:96px;margin:0 auto 14px;border-radius:9999px;display:flex;align-items:center;justify-content:center;background:linear-gradient(90deg,#ef4444,#f97316);box-shadow:0 6px 22px rgba(249,115,22,0.12)}
    .avatar-svg{width:44px;height:44px;color:#fff}
    h2.card-title{font-size:24px;margin:0;color:#fff}
    p.card-sub{margin:6px 0 0;color:#9fb0bf}

    label.field-label{display:block;margin-bottom:8px;color:#9fb0bf;font-weight:700}
    input.field{width:100%;height:48px;padding:12px 14px;border-radius:10px;background:#0f1724;border:1px solid rgba(255,255,255,0.04);color:#fff;box-sizing:border-box}
    .btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;width:100%;padding:14px;border-radius:12px;border:none;color:#fff;background:linear-gradient(90deg,#ef4444,#f97316);cursor:pointer;font-weight:800;letter-spacing:0.2px}
    .btn[disabled]{opacity:0.6;cursor:not-allowed}
    .alert{margin-top:8px;padding:10px;border-radius:8px;border:1px solid rgba(220,38,38,0.15);background:rgba(127,29,29,0.12);color:#fecaca}
    .muted-box{margin-top:18px;padding:14px;background:rgba(15,23,32,0.5);border-radius:10px;color:#cbd5e1}
    .link-outline{display:inline-block;margin-top:14px;padding:8px 12px;border-radius:8px;border:1px solid rgba(255,255,255,0.06);color:#cbd5e1;text-decoration:none}

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
        .admin-root{padding:28px 12px}
    }
</style>

<div class="admin-root">
    <div class="digital-background"></div>

    <div class="card">
        <div class="card-header">
            <div class="avatar-wrap" role="img" aria-label="admin icon">
                <svg class="avatar-svg" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <h2 class="card-title">لوحة تحكم المدير</h2>
            <p class="card-sub">عوائد الخليج للاستثمار السعودي</p>
        </div>

        @if($errors->any())
            <div class="alert" role="alert">
                @foreach($errors->all() as $err)
                    <div>{{ $err }}</div>
                @endforeach
            </div>
        @endif

        <form id="admin-login-form" method="post" action="{{ route('admin.login.submit') }}">
            @csrf
            <div style="margin-bottom:12px;">
                <label class="field-label" for="email">البريد الإلكتروني أو اسم المستخدم</label>
                <input id="email" name="email" class="field" type="text" placeholder="admin@example.com أو admin" value="{{ old('email') ?? old('username') ?? '' }}" autocomplete="username" required />
            </div>

            <div style="margin-bottom:12px;">
                <label class="field-label" for="password">كلمة المرور</label>
                <input id="password" name="password" class="field" type="password" placeholder="••••••••" autocomplete="current-password" required />
                @if($errors->has('password'))
                    <div style="margin-top:8px;color:#fecaca;font-weight:700">{{ $errors->first('password') }}</div>
                @endif
            </div>

            <div style="text-align:left;margin-bottom:12px">
                <a href="{{ route('admin.forgot-password') }}" class="link-outline" style="padding:6px 10px;">نسيت كلمة المرور؟</a>
            </div>

            <div>
                <button id="login-btn" type="submit" class="btn">🔐 دخول لوحة التحكم</button>
            </div>
        </form>

    <div class="muted-box">
            <h4 style="margin:0 0 8px;color:#fff;font-weight:700">بيانات تجريبية:</h4>
            <div style="font-size:14px;color:#9fb0bf">
                <p style="margin:0">البريد: <span style="color:#7dd3fc">admin@example.com</span> أو اسم المستخدم: <span style="color:#7dd3fc">admin</span></p>
                <p style="margin:0">كلمة المرور: <span style="color:#7dd3fc">admin123</span></p>
            </div>
        </div>

        <div style="text-align:center;margin-top:14px">
            <a href="{{ route('home') }}" class="link-outline">العودة للموقع الرئيسي</a>
        </div>
    </div>
</div>

@push('scripts')
<script>
    (function(){
        const form = document.getElementById('admin-login-form');
        const btn = document.getElementById('login-btn');
        form.addEventListener('submit', function(){
            btn.disabled = true;
            const originalText = btn.textContent;
            btn.textContent = 'جاري تسجيل الدخول...';
        });
    })();
</script>
@endpush

@endsection
