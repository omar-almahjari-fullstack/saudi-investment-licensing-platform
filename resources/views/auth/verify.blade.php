@extends('layouts.app')

@section('title','تفعيل الحساب')

@section('content')
<div style="min-height:60vh;display:flex;align-items:center;justify-content:center;padding:48px;direction:rtl">
  <div style="max-width:520px;width:100%;background:linear-gradient(180deg,#07121a 0%, #0b1620 100%);padding:32px;border-radius:14px;color:#fff;box-shadow:0 8px 24px rgba(3,7,10,0.6);">
    <h2 style="margin-top:0;color:#fff;font-weight:700">تفعيل الحساب</h2>
    <p style="color:#9fb0bf;margin-top:6px">أدخل رمز التحقق المرسل إلى بريدك الإلكتروني. سيتم الإرسال تلقائيًا عند إدخال 6 أرقام.</p>

    @if(session('status'))
      <div style="background:rgba(6,182,212,0.08);padding:12px;border-radius:8px;margin:12px 0;color:#06b6d4">{{ session('status') }}</div>
    @endif
    @if($errors->any())
      <div style="background:rgba(220,38,38,0.06);padding:12px;border-radius:8px;margin:12px 0;color:#fecaca">
        <ul style="margin:0;padding-right:10px">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form id="verifyForm" method="post" action="{{ route('verify.post') }}" style="margin-top:18px">
      @csrf
      <div style="margin-bottom:8px">
        <label for="code" style="display:block;margin-bottom:10px;color:#9fb0bf;font-size:14px">رمز التحقق</label>
        <input id="code" name="code" autocomplete="one-time-code" type="text" inputmode="numeric" pattern="[0-9]*" maxlength="6" required
          style="width:100%;padding:16px;border-radius:10px;border:2px solid #06b6d4;background:transparent;color:#fff;font-size:28px;letter-spacing:10px;text-align:center;outline:none;transition:box-shadow .15s,border-color .15s">
      </div>
      <div style="color:#9fb0bf;font-size:13px;margin-top:6px">ستُفعّل الحساب عند إدخال رمز صحيح مكوّن من 6 أرقام.</div>
    </form>

    <div style="margin-top:20px;border-top:1px solid rgba(255,255,255,0.03);padding-top:16px;">
      <p style="color:#9fb0bf;margin:0 0 8px 0">لم تستلم الرمـز؟ أعد إرساله:</p>
      <form method="post" action="{{ route('verify.resend') }}" style="display:flex;gap:8px;align-items:center">
        @csrf
        <input name="email" type="email" placeholder="بريدك الإلكتروني" required style="flex:1;padding:10px;border-radius:10px;border:1px solid rgba(255,255,255,0.04);background:transparent;color:#fff">
        <button type="submit" class="btn" style="background:#06b6d4;color:#042024;border:none;padding:10px 14px;border-radius:10px;font-weight:600">إعادة الإرسال</button>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
  const input = document.getElementById('code');
  const form = document.getElementById('verifyForm');
  if(!input || !form) return;
  input.focus();
  // focus/blur visual feedback
  input.addEventListener('focus', function(){
    this.style.boxShadow = '0 8px 20px rgba(6,182,212,0.08)';
    this.style.borderColor = '#06b6d4';
  });
  input.addEventListener('blur', function(){
    this.style.boxShadow = '';
    this.style.borderColor = '#06b6d4';
  });
  input.addEventListener('input', function(e){
    // allow only digits and limit to 6
    this.value = this.value.replace(/\D/g, '').slice(0,6);
    // auto-submit when 6 digits entered
    if (this.value.length === 6) {
      // small delay for UX (allow user to see what they typed)
      setTimeout(function(){ form.submit(); }, 180);
    }
  });
  // keyboard accessibility: paste handler to allow pasting 6-digit codes
  input.addEventListener('paste', function(e){
    const paste = (e.clipboardData || window.clipboardData).getData('text');
    const digits = paste.replace(/\D/g, '').slice(0,6);
    if (digits.length) {
      e.preventDefault();
      this.value = digits;
      if (digits.length === 6) setTimeout(function(){ form.submit(); }, 150);
    }
  });
});
</script>

@endsection
