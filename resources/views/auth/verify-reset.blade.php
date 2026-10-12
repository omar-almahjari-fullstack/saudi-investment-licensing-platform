@extends('layouts.app')

@section('title','أدخل رمز التحقق')

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
    .otp-timer{display:block;margin-top:10px;color:#9fb0bf;font-weight:600}
    /* subtle links (distinct from primary buttons) */
    .link-outline{display:inline-block;margin-top:12px;padding:4px 6px;border-radius:6px;border:none;color:#cbd5e1;background:transparent;text-decoration:none;font-weight:600;font-size:13px}
    @media (max-width:640px){ .fp-card{padding:18px;border-radius:10px;width:94%} .fp-card h2{font-size:22px} input.fp-field{width:160px} .fp-form{display:flex;flex-direction:column;gap:10px} .fp-btn{margin-left:0;width:100%} }
</style>

<div class="fp-root">
    <div class="fp-card">
        <h2>التحقق من الحساب</h2>
        <p>أدخل رمز التحقق المكون من 6 أرقام الذي أرسلناه إلى بريدك الإلكتروني.</p>

        @if($errors->any())
            <div class="status" style="background:rgba(239,68,68,0.06);color:#fecaca">{{ $errors->first() }}</div>
        @endif

        @if(session('status'))
            <div class="status">{{ session('status') }}</div>
        @endif

        <form method="post" action="{{ route('auth.verify-reset.submit') }}">
            @csrf
            <input type="hidden" name="email" value="{{ old('email', $email ?? '') }}" />

            <label for="code" style="display:block;margin:8px 0;color:#fff">رمز التحقق</label>
            <div class="fp-form">
                <input id="code" name="code" class="fp-field" type="text" placeholder="أدخل رمز التحقق (٦ أرقام)" required />
            </div>

            <div style="margin-top:14px">
                <button type="submit" class="fp-btn">تحقق و انتقل لتعيين كلمة المرور</button>
            </div>
            <!-- OTP countdown + resend controls -->
            <div id="otp-controls" style="margin-top:12px;text-align:center">
                {{-- data-ttl: seconds (prefer server-provided $ttl, otherwise default to 120) --}}
                <span id="otp-timer" class="otp-timer" data-ttl="{{ $ttl ?? 120 }}">يمكنك إعادة الطلب بعد: 02:00</span>
            </div>
        </form>

        <div style="text-align:center;margin-top:18px">
            <a href="{{ route('auth.forgot-password') }}" class="link-outline" id="resend-code" aria-disabled="true">طلب رمز جديد</a>
            <a href="{{ route('auth.login.form') }}" class="link-outline" style="margin-right:8px">العودة لتسجيل الدخول</a>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    (function(){
        // Read TTL (seconds) from data attribute, query param `ttl` overrides it
        const timerEl = document.getElementById('otp-timer');
        if(!timerEl) return;

        const url = new URL(window.location.href);
        const qttl = url.searchParams.get('ttl');
        const dataTtl = parseInt(timerEl.getAttribute('data-ttl') || '120', 10) || 120;
        let remaining = (qttl && !isNaN(parseInt(qttl,10))) ? parseInt(qttl,10) : dataTtl;

        const resendLink = document.getElementById('resend-code');
        let intervalId = null;

        function formatTime(s){
            const mm = Math.floor(s/60).toString().padStart(2,'0');
            const ss = (s%60).toString().padStart(2,'0');
            return `${mm}:${ss}`;
        }

        function updateUI(){
            if(remaining > 0){
                timerEl.textContent = `يمكنك إعادة الطلب بعد: ${formatTime(remaining)}`;
                if(resendLink){
                    resendLink.setAttribute('aria-disabled','true');
                    resendLink.classList.add('disabled');
                }
            } else {
                timerEl.textContent = 'يمكنك الآن إعادة طلب الرمز';
                if(resendLink){
                    resendLink.removeAttribute('aria-disabled');
                    resendLink.classList.remove('disabled');
                }
            }
        }

        function start(){
            updateUI();
            if(remaining <= 0) return;
            intervalId = setInterval(function(){
                remaining = Math.max(0, remaining - 1);
                updateUI();
                if(remaining <= 0 && intervalId){ clearInterval(intervalId); intervalId = null; }
            }, 1000);
        }

        // Prevent clicking resend while disabled
        if(resendLink){
            resendLink.addEventListener('click', function(e){
                if(resendLink.getAttribute('aria-disabled') === 'true'){
                    e.preventDefault();
                    // tiny visual feedback
                    timerEl.style.opacity = '0.9';
                    setTimeout(()=> timerEl.style.opacity = '', 180);
                }
            });
        }

        // Start the countdown on page load
        document.addEventListener('DOMContentLoaded', start);
    })();
</script>
@endpush
