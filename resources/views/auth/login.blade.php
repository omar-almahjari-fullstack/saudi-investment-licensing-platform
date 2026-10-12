@extends('layouts.app')

@section('title','تسجيل دخول المستثمر')

@section('content')

<style>
  /* Scoped styles to mirror the admin login look */
  .auth-root{position:relative;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:48px 18px;background:#051226;color:#fff;direction:rtl}
  .digital-background{position:absolute;inset:0;z-index:0;background:linear-gradient(180deg, #02121a, #031425 40%)}
  .card{position:relative;z-index:10;min-height:420px;max-width:560px;width:100%;background:linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01));backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,0.04);box-shadow:0 20px 50px rgba(0,0,0,0.65);border-radius:14px;padding:34px 30px;display:flex;flex-direction:column;justify-content:space-between}
  .card-header{text-align:center;margin-bottom:6px}
  .avatar-wrap{width:96px;height:96px;margin:0 auto 14px;border-radius:9999px;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg, #00ffff 0%, #0099cc 100%);box-shadow:0 6px 22px rgba(0,153,204,0.22)}
  .avatar-svg{width:44px;height:44px;color:#fff}
  h2.card-title{font-size:24px;margin:0;color:#fff}
  p.card-sub{margin:6px 0 0;color:#9fb0bf}

  label.field-label{display:block;margin-bottom:6px;color:#fff;font-weight:700}
  /* Inputs: dark fill, gold border only */
  input.field{width:100%;height:42px;padding:10px 12px;border-radius:8px;background:#0f1724;border:1px solid rgba(255,215,0,0.45);color:#fff;font-weight:600;box-sizing:border-box}
  /* Login main button styling -> blue background, black text */
  .btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;width:100%;padding:12px;border-radius:10px;border:none;color:#000;background:linear-gradient(135deg,#06b6d4 0%,#0284c7 100%);cursor:pointer;font-weight:800;letter-spacing:0.2px}
  .btn[disabled]{opacity:0.6;cursor:not-allowed}
  .alert{margin-top:8px;padding:10px;border-radius:8px;border:1px solid rgba(220,38,38,0.15);background:rgba(127,29,29,0.12);color:#fecaca}
  .muted-box{margin-top:18px;padding:14px;background:rgba(15,23,32,0.5);border-radius:10px;color:#cbd5e1}
  /* subtle links (distinct from primary buttons) */
  .link-outline{display:inline-block;margin-top:12px;padding:4px 6px;border-radius:6px;border:none;color:#cbd5e1;background:transparent;text-decoration:none;font-weight:600;font-size:13px}

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
  .hidden{display:none}
  .text-left{ text-align:left }
  .text-center{ text-align:center }
  .mb-3{ margin-bottom:12px }
  .mb-2{ margin-bottom:8px }
  .mt-2{ margin-top:8px }
  .mt-3{ margin-top:12px }
  .mt-4{ margin-top:16px }
  .w-full{ width:100% }
</style>

<div class="auth-root">
  <div class="digital-background"></div>

  <div class="card">
    <div class="card-header">
      <div class="avatar-wrap" role="img" aria-label="investor icon">
        <svg class="avatar-svg" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path d="M12 12a5 5 0 100-10 5 5 0 000 10z"/>
          <path d="M4 20a8 8 0 1116 0v1H4v-1z"/>
        </svg>
      </div>
      <h2 class="card-title">دخول المستثمر</h2>
  <p class="card-sub">عوائد الخليج للاستثمار السعودي</p>
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

    <form method="post" action="{{ route('auth.login') }}">
      @csrf
      <div class="mb-3">
        <label class="field-label" for="email">البريد الإلكتروني</label>
        <input id="email" name="email" class="field" type="email" placeholder="you@example.com" value="{{ old('email') }}" autocomplete="email" required />
      </div>

      <div class="mb-3">
        <label class="field-label" for="password">كلمة المرور</label>
        <input id="password" name="password" class="field" type="password" placeholder="••••••••" autocomplete="current-password" required />
      </div>

      <div class="text-left mb-3">
        <a href="{{ route('auth.forgot-password') }}" class="link-outline" style="padding:6px 10px;">نسيت كلمة المرور؟</a>
        <a href="{{ route('auth.register.form') }}" class="link-outline" style="padding:6px 10px;margin-right:8px">ليس لديك حساب؟ تسجيل جديد</a>
      </div>

      <div>
        <button type="submit" class="btn">🚀 دخول حساب المستثمر</button>
      </div>
    </form>

    <div class="text-center mt-3">
      <a href="{{ route('home') }}" class="link-outline">العودة للموقع الرئيسي</a>
    </div>
  </div>

</div>

@endsection
