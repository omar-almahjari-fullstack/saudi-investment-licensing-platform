@extends('layouts.app')

@section('content')
@php
  // default: consider passed $type variable (server-side)
  $isLarge = isset($type) && $type === 'large';
  // Allow overriding via query parameters from links (e.g., large-investment sets isLargeA=true or level=advanced)
  $qIsLargeA = request()->query('isLargeA');
  $qIsLarge = request()->query('isLarge');
  $qLevel = request()->query('level');

  // helper to safely lowercase only scalar values
  $safeLower = function($v){
    if(is_array($v) || is_object($v) || is_null($v)) return '';
    return strtolower((string) $v);
  };

  // normalize level when it may be passed as an array (e.g., level[id]=...)
  $qLevelVal = null;
  if(is_array($qLevel)){
    // prefer id then title if present
    $qLevelVal = $qLevel['id'] ?? $qLevel['title'] ?? null;
  } else {
    $qLevelVal = $qLevel;
  }

  if (($qIsLargeA && $safeLower($qIsLargeA) === 'true') || ($qIsLarge && $safeLower($qIsLarge) === 'true') || ($qLevelVal && $safeLower($qLevelVal) === 'advanced')) {
    $isLarge = true;
  }
  // حزم الاستثمار الصغيرة (مطابقة للواجهة)
  $smallPackages = [
    ['amount' => 1000, 'profit_rate' => 5],
    ['amount' => 5000, 'profit_rate' => 6],
    ['amount' => 10000, 'profit_rate' => 7],
    ['amount' => 20000, 'profit_rate' => 8],
    ['amount' => 30000, 'profit_rate' => 10],
    ['amount' => 45000, 'profit_rate' => 12],
  ];

  // معطيات الحزمة القادمة من الرابط (إن وُجدت)
  $qAmount = (int) request()->query('amount', 0);
  $qRate = (int) request()->query('rate', 0);
  $qLevel = request()->query('level');
  $qDurationLabel = request()->query('duration'); // مثال: "1 شهر"
  $qExpected = ($qAmount > 0 && $qRate > 0) ? round($qAmount * $qRate / 100) : null;

  // بيانات المستخدم/المستثمر الحالية لملء الحقول تلقائياً
  $authUser = \Illuminate\Support\Facades\Auth::user();
  if (!$authUser) { $sid = session('user_id'); if ($sid) { $authUser = \App\Models\User::find($sid); } }
  $investor = $authUser ? \App\Models\Investor::where('user_id', $authUser->id)->first() : null;

  $prefill_name  = $authUser->full_name ?? ($investor->full_name ?? '');
  $prefill_email = $authUser->email ?? '';
  // Prefill phone: prioritize DB field named "phone" (investor first, then user), keep other keys as fallbacks
  $prefill_phone = '';
  if ($investor) {
    $prefill_phone = $investor->phone
      ?? ($investor->mobile_number ?? null)
      ?? ($investor->phone_number ?? null)
      ?? ($investor->mobile ?? null)
      ?? '';
  }
  if (!$prefill_phone && $authUser) {
    $prefill_phone = $authUser->phone
      ?? ($authUser->mobile ?? null)
      ?? ($authUser->mobile_number ?? null)
      ?? '';
  }
  // Prefer users.national_id as the single source of truth; fall back to investor legacy column
  $prefill_nid = '';
  if ($authUser) {
    $prefill_nid = $authUser->national_id ?? '';
  }
  if (empty($prefill_nid) && $investor) {
    $prefill_nid = $investor->national_id ?? '';
  }
  $prefill_bank  = $investor->bank_account ?? '';
  $prefill_notes = $investor->notes ?? ($investor->note ?? '');

  // $existingDocs is prepared by the controller; set simple front/back URLs for the view
  $id_front = null; $id_back = null;
  $id_front_url = $existingDocs['identity_front']['url'] ?? null;
  $id_back_url = $existingDocs['identity_back']['url'] ?? null;
@endphp

@push('styles')
<style>
/* Scoped mobile fixes for the investment/payment page to avoid overflow */
.investment-wrapper { margin-left:0; padding:16px; max-width:1200px; margin-right:auto; margin-left:auto; }
@media (max-width: 640px) {
  /* Remove the left admin sidebar offset on small screens */
  .investment-wrapper { margin-left:0 !important; padding:12px !important; }
  /* Force 2-col grids to collapse to a single column inside this page */
  .investment-wrapper [style*="grid-template-columns:repeat(2,1fr)"],
  .investment-wrapper [style*="grid-template-columns:1fr 1fr"],
  .investment-wrapper [style*="grid-template-columns:repeat(3,1fr)"],
  .investment-wrapper [style*="grid-template-columns:repeat(4,1fr)"] {
    grid-template-columns: 1fr !important;
  }
  /* Prevent fixed min-width cards from forcing horizontal scroll */
  .investment-wrapper [style*="min-width"] { min-width: 0 !important; }
  .investment-wrapper .glass-card { max-width: 100% !important; box-sizing: border-box; }
  .investment-wrapper img { max-width: 100% !important; height: auto !important; }
  /* Make inputs, selects and textareas fit their container */
  .investment-wrapper input, .investment-wrapper select, .investment-wrapper textarea { max-width: 100% !important; box-sizing: border-box; }
  /* Ensure file inputs don't expand oddly */
  .investment-wrapper input[type="file"] { width: auto !important; }
  /* Specific fixes for bank payment cards */
  #pm-bank .glass-card { min-width: 0 !important; }
  /* On small screens, make investor identity cards stack full-width */
  #investor-identity > .glass-card { flex: 1 1 100% !important; max-width: 100% !important; }
}
/* Ensure identity images in the info step are uniform */
#investor-identity .identity-equal, #investor-identity img.identity-equal { width:220px !important; height:140px !important; object-fit:cover !important; border-radius:8px !important; border:1px solid rgba(255,255,255,0.04) !important; display:block !important; margin:0 auto !important; }
@media (max-width:640px){
  #investor-identity .identity-equal { width:100% !important; height:auto !important; aspect-ratio:16/9 !important; }
}
</style>
</push>

<div class="investment-wrapper">
  <div class="digital-background"></div>

  <!-- Header -->
  <header style="position:relative;z-index:10;padding-top:8px;padding-bottom:8px;">
    <div style="padding:0 8px;max-width:1100px;margin:0 auto;">
      <a href="/" class="btn-secondary" style="border-color:#22d3ee;color:#22d3ee;">← العودة للرئيسية</a>
      <h1 style="font-size:22px;color:#fff;margin-top:8px;margin-bottom:6px;" class="glow-text">{{ $isLarge ? 'الاستثمار الكبير' : 'الاستثمار الصغير' }}</h1>
      <p style="color:#cbd5e1;margin-top:0;margin-bottom:0;font-size:14px;">{{ $isLarge ? 'الحد الأدنى 50,000 ريال سعودي' : 'عائد مضمون خلال شهر واحد' }}</p>
    </div>
  </header>

  <div style="position:relative;z-index:10;padding:0 16px 32px;max-width:1200px;">
    <!-- step 1: اختيار الحزمة / المبلغ -->
    <div id="step-1">




  <div class="glass-card" style="padding:16px;margin-bottom:18px;">
        <h3 style="color:#fff;text-align:center;margin-bottom:12px;">تفاصيل الحزمة المحددة</h3>
        <div style="display:grid;grid-template-columns:1fr;gap:12px;">

          <div id="custom-summary" class="glass-card">
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;padding:10px;">
              <div style="text-align:center;">
                <div style="font-size:12px;color:#9ca3af;">مبلغ الاستثمار</div>
                <div id="summary-amount" style="font-size:18px;color:#06b6d4;font-weight:700;">{{ $qAmount ? number_format($qAmount) : '25,000' }}</div>
              </div>
              <div style="text-align:center;">
                <div style="font-size:12px;color:#9ca3af;">نسبة الربح</div>
                <div id="summary-profit" style="font-size:18px;color:#fbbf24;font-weight:700;"> {{ $qRate ?: 10 }}%</div>
              </div>
              <div style="text-align:center;">
                <div style="font-size:12px;color:#9ca3af;">العائد المتوقع</div>
                <div id="summary-return" style="font-size:18px;color:#22c55e;font-weight:700;"> {{ $qExpected ? number_format($qExpected) : '2,500' }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
  <div id="step1_error" style="display:none;color:#ffb4b4;margin-top:8px;font-size:14px;"></div>


    </div>

    <!-- step 2: معلومات المستثمر -->
    <div id="step-2">
      

      <h2 style="font-size:22px;color:#fff;margin:0 0 16px;">معلومات المستثمر</h2>
      <div id="step_2" style="display:grid;grid-template-columns:repeat(2,1fr);gap:16px;">
        <!-- المعلومات الشخصية -->
        <div class="glass-card" style="padding:16px;">
          <h3 style="color:#fff;margin-bottom:12px;">المعلومات الشخصية</h3>
          <div style="display:grid;gap:10px;">
            <div>
              <label for="full_name" style="display:block;color:#cbd5e1;margin-bottom:6px;">الاسم الكامل</label>
              <input id="full_name" value="{{ $prefill_name }}" class="form-control" style="width:100%;background:#1f2937;border:1px solid #4b5563;color:#fff;padding:10px;border-radius:8px;opacity:0.8;" {{ !empty($prefill_name) ? 'readonly' : '' }} />
            </div>
            <div>
              <label for="email" style="display:block;color:#cbd5e1;margin-bottom:6px;">البريد الإلكتروني</label>
              <input id="email" type="email" value="{{ $prefill_email }}" class="form-control" style="width:100%;background:#1f2937;border:1px solid #4b5563;color:#fff;padding:10px;border-radius:8px;opacity:0.8;" {{ !empty($prefill_email) ? 'readonly' : '' }} />
            </div>
            <div>
              <label for="phone" style="display:block;color:#cbd5e1;margin-bottom:6px;">رقم الهاتف</label>
              <input id="phone" type="tel" value="{{ old('phone', $prefill_phone) }}" class="form-control" style="width:100%;background:#1f2937;border:1px solid #4b5563;color:#fff;padding:10px;border-radius:8px;opacity:0.8;" {{ !empty($prefill_phone) ? 'readonly' : '' }} />
            </div>
            <div>
              <label for="national_id" style="display:block;color:#cbd5e1;margin-bottom:6px;">رقم الهوية</label>
              <input id="national_id" value="{{ $prefill_nid }}" class="form-control" style="width:100%;background:#1f2937;border:1px solid #4b5563;color:#fff;padding:10px;border-radius:8px;opacity:0.8;" {{ !empty($prefill_nid) ? 'readonly' : '' }} />
            </div>
          </div>
        </div>

        <!-- المعلومات المالية -->
        <div class="glass-card" style="padding:16px;">
          <h3 style="color:#fff;margin-bottom:12px;">المعلومات المالية والاستثمارية</h3>
          <div style="display:grid;gap:10px;">
            <div>
              <label for="bank_account" style="display:block;color:#cbd5e1;margin-bottom:6px;">رقم الحساب البنكي</label>
              @if(!empty($prefill_bank))
                {{-- If investor already has a saved bank account, display it readonly (like national_id/phone) and include hidden input so the value posts. --}}
                <input id="bank_account" type="text" class="form-control" value="{{ $prefill_bank }}" readonly style="width:100%;background:#1f2937;border:1px solid #4b5563;color:#fff;padding:10px;border-radius:8px;opacity:0.85;" />
                <input type="hidden" name="bank_account" value="{{ $prefill_bank }}" />
              @else
                <!-- Allow entering a bank account per-investment (do not permanently overwrite investor profile) -->
                <input id="bank_account" name="bank_account" placeholder="مثل: SA1234567890123456789" value="{{ old('bank_account', $prefill_bank) }}" class="form-control" style="width:100%;background:#1f2937;border:1px solid #4b5563;color:#fff;padding:10px;border-radius:8px;" />
              @endif
            </div>

            <div>
              <label for="mudat_alaistithmar" style="display:block;color:#cbd5e1;margin-bottom:6px;">اختار مدة الاستثمار</label>
               <select name="mudat_alaistithmar" id="mudat_alaistithmar" class="form-control" style="width:100%;background:#1f2937;border:1px solid #4b5563;color:#fff;padding:10px;border-radius:8px;opacity:0.8;">
                <option value="ثلاثة أشهر" {{ $qDurationLabel==='ثلاثة أشهر' ? 'selected' : '' }}>ثلاثة أشهر</option>
                <option value="ستة أشهر" {{ $qDurationLabel==='ستة أشهر' ? 'selected' : '' }}>ستة أشهر</option>
                <option value="سنة واحدة" {{ $qDurationLabel==='سنة واحدة' ? 'selected' : '' }}>سنة واحدة</option>
                <option value="سنة وثلاثة أشهر" {{ $qDurationLabel==='سنة وثلاثة أشهر' ? 'selected' : '' }}>سنة وثلاثة أشهر</option>
                <option value="سنة وستة أشهر" {{ $qDurationLabel==='سنة وستة أشهر' ? 'selected' : '' }}>سنة وستة أشهر</option>
                <option value="سنتان" {{ $qDurationLabel==='سنتان' ? 'selected' : '' }}>سنتان</option>
                <option value="سنتان وثلاثة أشهر" {{ $qDurationLabel==='سنتان وثلاثة أشهر' ? 'selected' : '' }}>سنتان وثلاثة أشهر</option>
                <option value="سنتان وستة أشهر" {{ $qDurationLabel==='سنتان وستة أشهر' ? 'selected' : '' }}>سنتان وستة أشهر</option>
                <option value="ثلاث سنوات" {{ $qDurationLabel==='ثلاث سنوات' ? 'selected' : '' }}>ثلاث سنوات</option>
                              
               </select>
               <div id="duration-months-hint" style="margin-top:6px;">
                 <div id="duration-pill" style="display:inline-flex;align-items:center;gap:8px;background:linear-gradient(90deg,#fbbf24,#f97316);color:#111827;padding:6px 10px;border-radius:999px;font-weight:800;">
                   <span>⏱️</span>
                   <span>عدد الأشهر:</span>
                   <span id="duration-months-value">—</span>
                 </div>
                 <div style="margin-top:4px;color:#9ca3af;font-size:12px;">المدة المختارة: <span id="duration-label-value">—</span></div>
                 <div id="duration-note" style="margin-top:6px;color:#f59e0b;font-size:12px;">ملاحظة: يحدد اختيارك مدة الاستثمار جدول صرف الأرباح الشهرية تلقائيًا.</div>
               </div>
            </div>
            <div>
              <label for="notes" style="display:block;color:#cbd5e1;margin-bottom:6px;">ملاحظات <span style="font-size:12px;color:#9ca3af;">(يمكن تعديلها لكل استثمار)</span></label>
              <textarea id="notes" name="notes" rows="3" class="form-control" style="width:100%;background:#1f2937;border:1px solid #4b5563;color:#fff;padding:10px;border-radius:8px;resize:none;">{{ old('notes', $prefill_notes) }}</textarea>
            </div>
            <div>
              <span style="font-size:17px;color:#00ff00;">(ملاحظة)</span>
              <label style="display:block;color:#17cbeb;margin-bottom:6px;">يمكنك سحب ارباحك في اي وقت</label>
            </div>
          </div>
        </div>
      </div>

      <div id="step2_error" style="display:none;color:#ffb4b4;margin-top:8px;font-size:14px;"></div>

      <div style="display:flex;flex-direction:column;gap:10px;margin-top:16px;">
        <!-- Identity upload/preview fields will appear above the confirm button -->

        <!-- Modal -->
        <div id="quick-modal" style="display:none;position:fixed;inset:0;z-index:10050;align-items:center;justify-content:center;background:rgba(0,0,0,0.6);">
          <div role="dialog" aria-modal="true" style="max-width:720px;width:94%;margin:24px auto;background:linear-gradient(180deg,#0f172a,#111827);padding:18px;border-radius:12px;border:1px solid rgba(255,255,255,0.04);box-shadow:0 12px 40px rgba(2,6,23,0.7);color:#e6eef8;">
                <div class="">
                  <h2>هل انت متأكد من الاستثمار؟</h2>
                </div>
           
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
              <h3 style="margin:0;font-size:18px;">معلومات الحزمة</h3>
              <button id="close-quick-modal" type="button" class="btn-secondary" style="border-color:transparent;color:#9ca3af;background:transparent;font-size:16px;">✕</button>
            </div>

            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:12px;">
              <div style="text-align:center;padding:10px;border-radius:8px;background:rgba(255,255,255,0.02);">
                <div style="font-size:13px;color:#9ca3af;">مبلغ الاستثمار</div>
                <div id="modal-amount" style="font-size:16px;color:#06b6d4;font-weight:700;margin-top:6px;">—</div>
              </div>
              <div style="text-align:center;padding:10px;border-radius:8px;background:rgba(255,255,255,0.02);">
                <div style="font-size:13px;color:#9ca3af;">نسبة الربح</div>
                <div id="modal-profit" style="font-size:16px;color:#f59e0b;font-weight:700;margin-top:6px;">—</div>
              </div>
              <div style="text-align:center;padding:10px;border-radius:8px;background:rgba(255,255,255,0.02);">
                <div style="font-size:13px;color:#9ca3af;">العائد المتوقع</div>
                <div id="modal-return" style="font-size:16px;color:#22c55e;font-weight:700;margin-top:6px;">—</div>
              </div>
            </div>

            <div style="color:#cbd5e1;font-size:14px;margin-bottom:12px;">
              <div><strong>الاسم:</strong> <span id="modal-name" style="color:#fff;"></span></div>
              <div><strong>الهاتف:</strong> <span id="modal-phone" style="color:#fff;"></span></div>
              <div style="margin-top:8px;color:#9ca3af;font-size:13px;">يمكنك إغلاق هذه النافذة أو المتابعة لتأكيد الاستثمار.</div>
            </div>

            <div style="display:flex;gap:10px;justify-content:flex-end;">
              <button id="modal-cancel" type="button" class="btn-secondary" style="border-color:#64748b;color:#cbd5e1;">إغلاق</button>
              {{-- <button id="modal-proceed" type="button" class="btn-primary" style="background:#06b6d4;color:#052f33;flex:0 0 auto;">بداء الاستثمار </button> --}}
              <button id="continue-to-confirm" class="btn-primary" style="flex:1;"> بداء الاستثمار </button>

            </div>
          </div>
        </div>

        <!-- Identity upload/preview fields (styled like license page) -->
        @php
          // $existingDocs is prepared by the controller; fall back to previously-computed
          // $id_front_url / $id_back_url when present. Avoid running Schema/Storage checks in Blade.
          $display_front = $existingDocs['identity_front']['url'] ?? ($id_front_url ?? null);
          $display_back = $existingDocs['identity_back']['url'] ?? ($id_back_url ?? null);
        @endphp

        <div style="display:flex;flex-direction:column;gap:8px;width:100%;">
          <label style="color:#cbd5e1;font-size:13px;">صور الهوية (إذا كانت محفوظة في حسابك ستُعرض هنا)</label>
          <div id="investor-identity" style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-start;">
            <div class="glass-card" style="flex:1;min-width:220px;padding:12px;text-align:center;">
              <div style="color:#fff;font-weight:700;margin-bottom:8px;">صورة الهوية (أمام)</div>
                @if(!empty($existingDocs) && !empty($existingDocs['identity_front']))
                  <div style="text-align:center;">
                    <img src="{{ $existingDocs['identity_front']['url'] }}" alt="id-front" class="identity-equal" />
                    <div style="color:#9ca3af;font-size:12px;margin-top:6px;">صورة الهوية (أمام)</div>
                  </div>
                @else
                  @php $frontSrc = $display_front ?? ($id_front_url ?? null); @endphp
                  @if(!empty($frontSrc))
                    <div style="text-align:center;">
                      <img src="{{ $frontSrc }}" alt="id-front" class="identity-equal" />
                      <div style="color:#9ca3af;font-size:12px;margin-top:8px;">محفوظ في حسابك</div>
                    </div>
                  @else
                    <label for="file_identity_front" style="display:block;border:2px dashed #4b5563;padding:18px;border-radius:8px;cursor:pointer;color:#cbd5e1;">اختيار صورة أمامية
                      <input id="file_identity_front" name="identity_front" type="file" accept=".jpg,.jpeg,.png,.pdf" style="display:none;" />
                    </label>
                  @endif
                @endif
            </div>

            <div class="glass-card" style="flex:1;min-width:220px;padding:12px;text-align:center;">
              <div style="color:#fff;font-weight:700;margin-bottom:8px;">صورة الهوية (خلف)</div>
              @if(!empty($existingDocs) && !empty($existingDocs['identity_back']))
                <div style="text-align:center;">
                  <img src="{{ $existingDocs['identity_back']['url'] }}" alt="id-back" class="identity-equal" />
                  <div style="color:#9ca3af;font-size:12px;margin-top:6px;">صورة الهوية (خلف)</div>
                </div>
              @else
                @php $backSrc = $display_back ?? ($id_back_url ?? null); @endphp
                @if(!empty($backSrc))
                  <div style="text-align:center;">
                    <img src="{{ $backSrc }}" alt="id-back" class="identity-equal" />
                    <div style="color:#9ca3af;font-size:12px;margin-top:8px;">محفوظ في حسابك</div>
                  </div>
                @else
                  <label for="file_identity_back" style="display:block;border:2px dashed #4b5563;padding:18px;border-radius:8px;cursor:pointer;color:#cbd5e1;">اختيار صورة خلفية
                    <input id="file_identity_back" name="identity_back" type="file" accept=".jpg,.jpeg,.png,.pdf" style="display:none;" />
                  </label>
                @endif
              @endif
            </div>
          </div>
        </div>

        {{-- Confirm button moved below images and made full-width --}}
        <div style="display:flex;gap:10px;">
          <button id="show-modal" type="button" class="btn-primary" style="width:100%"> تأكيد الاستثمار </button>
        </div>

        <script>
          (function($){
            // فتح المودل وعرض القيم الحالية من الملخص وحقول المستخدم
            $('#show-modal').on('click', function(){
              // إذا كان الملخص مخفي أو فارغ، نمنع الفتح مع رسالة بسيطة
              var amt = $('#summary-amount').text() || '';
              var prof = $('#summary-profit').text() || '';
              var ret = $('#summary-return').text() || '';
              if(!amt.trim() || amt.trim() === '—' || $('#custom-summary').is(':hidden')){
                // عرض تنبيه صغير داخل الصفحة بدل نافذة متصفح كبيرة
                var $msg = $('<div>').text('الرجاء اختيار حزمة أو إدخال مبلغ صالح أولاً').css({
                  position:'fixed',top:20,left:'50%',transform:'translateX(-50%)',
                  background:'#2b0a0a',color:'#ffd7d7',padding:'8px 12px',borderRadius:8,zIndex:11000
                }).appendTo('body');
                setTimeout(function(){ $msg.fadeOut(200,function(){ $(this).remove(); }); },1800);
                return;
              }
              $('#modal-amount').text(amt);
              $('#modal-profit').text(prof);
              $('#modal-return').text(ret);
              $('#modal-name').text($('#full_name').val() || '—');
              $('#modal-phone').text($('#phone').val() || '—');
              $('#quick-modal').fadeIn(150).css('display','flex');
            });

            // اغلاق المودل
            $('#close-quick-modal, #modal-cancel').on('click', function(){
              $('#quick-modal').fadeOut(120);
            });

            // اضغط بالخارج لإغلاق
            $('#quick-modal').on('click', function(e){
              if(e.target === this) $(this).fadeOut(120);
            });

            // زر المتابعة: يقلد سلوك "تأكيد الاستثمار" بالانتقال لخطوة التأكيد
            $('#modal-proceed').on('click', function(){
              $('#quick-modal').fadeOut(120);
              // محاكاة زر متابعة: إظهار خطوة 2 (إذا كانت مخفية) ثم الضغط على Continue
              if($('#step-1').is(':visible')){
                $('#proceed-to-info').trigger('click');
              }
              // افتح خطوة التأكيد مباشرة لو كان المستخدم في step-2
              setTimeout(function(){
                // تمرير نفس حدث الاستمرار الموجود في الصفحة
                $('#continue-to-confirm').trigger('click');
              }, 300);
            });

            // اغلاق بالـ ESC
            $(document).on('keydown', function(e){
              if(e.key === 'Escape') $('#quick-modal').fadeOut(120);
            });
          })(jQuery);
        </script>
        {{-- <button id="continue-to-confirm" class="btn-primary" style="flex:1;"> تأكيد الاستثمار </button> --}}
      </div>
    </div>

    <!-- step 3: التأكيد -->
    <div id="step-3" style="display:none;">
      <h2 style="font-size:22px;color:#fff;margin:0 0 16px;">تأكيد الاستثمار</h2>
      <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:16px;">
        <div class="glass-card" style="padding:16px;">
          <h3 style="color:#fff;margin-bottom:10px;">تفاصيل الاستثمار</h3>
          <div style="display:grid;gap:8px;">
            <div style="display:flex;justify-content:space-between;color:#cbd5e1;">
              <span>المبلغ:</span><span id="confirm-amount" style="color:#06b6d4;font-weight:700;"></span>
            </div>
            <div style="display:flex;justify-content:space-between;color:#cbd5e1;">
              <span>نسبة الربح:</span><span id="confirm-profit" style="color:#fbbf24;font-weight:700;"></span>
            </div>
            <div style="display:flex;justify-content:space-between;color:#cbd5e1;">
              <span>العائد المتوقع:</span><span id="confirm-return" style="color:#22c55e;font-weight:700;"></span>
            </div>
            <div style="display:flex;justify-content:space-between;color:#cbd5e1;">
              <span>المدة:</span><span>شهر واحد</span>
            </div>
          </div>
        </div>
        <div class="glass-card" style="padding:16px;">
          <h3 style="color:#fff;margin-bottom:10px;">معلومات المستثمر</h3>
          <div style="display:grid;gap:8px;color:#cbd5e1;">
            <div style="display:flex;justify-content:space-between;"><span>الاسم:</span><span id="confirm-name" style="color:#fff;"></span></div>
            <div style="display:flex;justify-content:space-between;"><span>البريد الإلكتروني:</span><span id="confirm-email" style="color:#fff;"></span></div>
            <div style="display:flex;justify-content:space-between;"><span>الهاتف:</span><span id="confirm-phone" style="color:#fff;"></span></div>
            <div style="display:flex;justify-content:space-between;"><span>رقم الهوية:</span><span id="confirm-nid" style="color:#fff;"></span></div>
            <div style="display:flex;justify-content:space-between;"><span>رقم الحساب البنكي:</span><span id="confirm-bank" style="color:#fff;"></span></div>
            <div style="display:flex;justify-content:space-between;"><span>نسبة الخطورة:</span><span style="color:#22c55e;">منخفضة</span></div>
            <div id="confirm-notes-wrap" style="display:none;">
              <span>ملاحظات:</span>
              <div id="confirm-notes" style="margin-top:6px;padding:8px;background:#1f2937;border-radius:8px;color:#fff;"></div>
            </div>
          </div>
        </div>
      </div>
    
    </div>

  <!-- Preview Modal (reused from license view) -->
  <div id="doc-preview-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.6);z-index:9999;align-items:center;justify-content:center;padding:20px;">
    <div style="max-width:1100px;width:100%;background:#0b1220;border-radius:10px;padding:12px;position:relative;">
      <button id="doc-preview-close" style="position:absolute;top:8px;right:8px;background:transparent;border:none;color:#cbd5e1;font-size:18px;">✕</button>
      <div id="doc-preview-meta" style="color:#cbd5e1;margin-bottom:8px"></div>
      <div id="doc-preview-body" style="min-height:300px;display:flex;align-items:center;justify-content:center;overflow:auto;padding:8px;background:#071022;border-radius:8px;">
      </div>
    </div>
  </div>

  <script>
    (function(){
      function openPreview(src, type, meta){
        var modal = document.getElementById('doc-preview-modal');
        var body = document.getElementById('doc-preview-body');
        var m = document.getElementById('doc-preview-meta');
        body.innerHTML = '';
        m.innerHTML = '';
        if(!src){ m.textContent = 'لا يوجد ملف للمعاينة.'; }
        if(type === 'image'){
          var img = document.createElement('img'); img.src = src; img.style.maxWidth='100%'; img.style.height='auto'; img.style.borderRadius='6px'; body.appendChild(img);
        } else {
          var iframe = document.createElement('iframe'); iframe.src = src; iframe.style.width='100%'; iframe.style.minHeight='480px'; iframe.style.border='none'; body.appendChild(iframe);
        }
        if(meta){ var lines = []; if(meta.filename) lines.push('<strong>الملف:</strong> '+meta.filename); if(meta.created_at) lines.push('<strong>تاريخ الرفع:</strong> '+meta.created_at); m.innerHTML = lines.join(' &nbsp; | &nbsp; '); }
        modal.style.display = 'flex';
      }
      document.addEventListener('click', function(e){
        var t = e.target;
        if(t.classList && t.classList.contains('preview-btn')){
          var src = t.getAttribute('data-src');
          var meta = null;
          try{ meta = JSON.parse(t.getAttribute('data-meta') || 'null'); }catch(ex){}
          var type = (t.getAttribute('data-type')) || (src && src.match(/\.(png|jpe?g|gif|webp|bmp)$/i) ? 'image' : 'file');
          openPreview(src, type, meta);
        }
        if(t.classList && t.classList.contains('preview-trigger')){
          var src = t.getAttribute('data-src');
          var type = t.getAttribute('data-type') || (src && src.match(/\.(png|jpe?g|gif|webp|bmp)$/i) ? 'image' : 'file');
          openPreview(src, type, null);
        }
        if(t.id === 'doc-preview-close'){
          document.getElementById('doc-preview-modal').style.display = 'none';
        }
      });
    })();
  </script>

    <!-- step 4: الدفع -->
    <div id="step-payment" style="display:none;">
      <h2 style="font-size:22px;color:#fff;margin:0 0 16px;">الدفع</h2>
      <div class="glass-card" style="padding:16px;margin-bottom:16px;">
        <div style="display:flex;justify-content:space-between;align-items:center;">
          <div style="color:#9ca3af;font-size:12px;">المبلغ المستحق</div>
          <div id="pay-amount" style="color:#06b6d4;font-weight:700;">—</div>
        </div>
        <div style="display:flex;justify-content:space-between;align-items:center;margin-top:6px;">
          <div style="color:#9ca3af;font-size:12px;">العائد المتوقع</div>
          <div id="pay-return" style="color:#22c55e;font-weight:700;">—</div>
        </div>
      </div>

      <div id="payment-methods" style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:12px;">
        {{-- <button class="pm-btn" data-method="card" style="flex:1;border:1px solid #4b5563;background:#111827;color:#fff;padding:10px;border-radius:8px;">بطاقة (مدى/فيزا/ماستر)</button>
        <button class="pm-btn" data-method="apple" style="flex:1;border:1px solid #4b5563;background:#111827;color:#fff;padding:10px;border-radius:8px;">Apple Pay</button> --}}
        <button class="pm-btn" data-method="bank" style="flex:1;border:1px solid #4b5563;background:#111827;color:#fff;padding:10px;border-radius:8px;">تحويل بنكي</button>
      </div>

      {{-- <div id="pm-card" class="glass-card" style="padding:12px;">
        <div style="display:grid;gap:10px;">
          <div>
            <label style="display:block;color:#cbd5e1;font-size:13px;margin-bottom:6px;">رقم البطاقة</label>
            <input id="card-number" inputmode="numeric" placeholder="1111 2222 3333 4444" style="width:100%;background:#1f2937;border:1px solid #4b5563;color:#fff;padding:10px;border-radius:8px;" />
          </div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
            <div>
              <label style="display:block;color:#cbd5e1;font-size:13px;margin-bottom:6px;">تاريخ الانتهاء (MM/YY)</label>
              <input id="card-exp" placeholder="MM/YY" style="width:100%;background:#1f2937;border:1px solid #4b5563;color:#fff;padding:10px;border-radius:8px;" />
            </div>
            <div>
              <label style="display:block;color:#cbd5e1;font-size:13px;margin-bottom:6px;">رمز الأمان (CVC)</label>
              <input id="card-cvc" inputmode="numeric" placeholder="123" style="width:100%;background:#1f2937;border:1px solid #4b5563;color:#fff;padding:10px;border-radius:8px;" />
            </div>
          </div>
          <div>
            <label style="display:block;color:#cbd5e1;font-size:13px;margin-bottom:6px;">اسم حامل البطاقة</label>
            <input id="card-name" placeholder="كما هو في البطاقة" style="width:100%;background:#1f2937;border:1px solid #4b5563;color:#fff;padding:10px;border-radius:8px;" />
          </div>
          <div id="card-error" style="display:none;color:#ffb4b4;font-size:12px;"></div>
        </div>
      </div>

      <div id="pm-apple" class="glass-card" style="padding:12px;display:none;color:#cbd5e1;font-size:14px;margin-top:12px;">
        Apple Pay سيتطلب جهاز يدعم Apple Pay. هذا نموذج تجريبي.
       </div> --}}
       <div id="pm-bank" class="glass-card" style="padding:12px;display:none;color:#cbd5e1;font-size:14px;margin-top:12px;">
        <div style="display:grid;gap:10px;">
          <div style="display:grid;gap:8px;">
            <div style="font-weight:700;color:#fff;">تفاصيل التحويل البنكي</div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
              <div class="glass-card" style="padding:10px;">
                <div style="font-size:12px;color:#9ca3af;">اسم الحساب</div>
                <div id="bank-account-name" style="color:#e5e7eb;font-weight:600;">—</div>
              </div>
              <div class="glass-card" style="padding:10px;">
                <div style="font-size:12px;color:#9ca3af;">البنك</div>
                <div id="bank-bank-name" style="color:#e5e7eb;font-weight:600;">—</div>
              </div>
              <div class="glass-card" style="padding:10px;">
                <div style="font-size:12px;color:#9ca3af;">رقم الحساب</div>
                <div id="bank-account-number" style="color:#e5e7eb;font-weight:600;">—</div>
              </div>
              <div class="glass-card" style="padding:10px;display:flex;align-items:center;justify-content:space-between;gap:8px;">
                <div>
                  <div style="font-size:12px;color:#9ca3af;">الآيبان (IBAN)</div>
                  <div id="bank-iban" style="color:#06b6d4;font-weight:700;">—</div>
                </div>
                <button type="button" class="btn-secondary copy-btn" data-copy-target="#bank-iban" style="border-color:#64748b;color:#cbd5e1;white-space:nowrap;">نسخ</button>
              </div>
              <div class="glass-card" style="padding:10px;">
                <div style="font-size:12px;color:#9ca3af;">SWIFT / BIC</div>
                <div id="bank-swift" style="color:#e5e7eb;font-weight:600;">—</div>
              </div>
              <div class="glass-card" style="padding:10px;display:flex;align-items:center;justify-content:space-between;gap:8px;">
                <div>
                  <div style="font-size:12px;color:#9ca3af;">رمز مرجع العملية</div>
                  <div id="bank-ref" style="color:#fbbf24;font-weight:700;">—</div>
                </div>
                <button type="button" class="btn-secondary copy-btn" data-copy-target="#bank-ref" style="border-color:#64748b;color:#cbd5e1;white-space:nowrap;">نسخ</button>
              </div>
            </div>
          </div>

          <div id="bank-instructions-block" class="glass-card" style="padding:10px;">
            <div style="font-size:13px;color:#9ca3af;">التعليمات</div>
            <ul id="bank-instructions-ul" style="margin:8px 0 0 0;padding-right:18px;list-style:disc;color:#cbd5e1;"></ul>
          </div>

          <div class="glass-card" style="padding:10px;display:grid;gap:10px;">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
              <div>
                <label style="display:block;color:#cbd5e1;font-size:13px;margin-bottom:6px;">اسم المحوِّل</label>
                <input id="bank-sender-name" placeholder="الاسم كما في الحساب" style="width:100%;background:#1f2937;border:1px solid #4b5563;color:#fff;padding:10px;border-radius:8px;" />
              </div>
              <div>
                <label style="display:block;color:#cbd5e1;font-size:13px;margin-bottom:6px;">تاريخ ووقت التحويل</label>
                <input id="bank-date" type="datetime-local" style="width:100%;background:#1f2937;border:1px solid #4b5563;color:#fff;padding:10px;border-radius:8px;" />
              </div>
            </div>
            <div>
              <label style="display:block;color:#cbd5e1;font-size:13px;margin-bottom:6px;">ملاحظة (اختياري)</label>
              <input id="bank-note" placeholder="أي تفاصيل إضافية" style="width:100%;background:#1f2937;border:1px solid #4b5563;color:#fff;padding:10px;border-radius:8px;" />
            </div>
            <div>
              <label style="display:block;color:#cbd5e1;font-size:13px;margin-bottom:6px;">إرفاق إيصال التحويل (صورة أو PDF)</label>
              <input id="bank-receipt" type="file" accept="image/*,.pdf" style="width:100%;background:#1f2937;border:1px solid #4b5563;color:#fff;padding:10px;border-radius:8px;" />
              <div id="bank-receipt-name" style="font-size:12px;color:#9ca3af;margin-top:6px;"></div>
              <div id="bank-error" style="display:none;color:#ffb4b4;font-size:12px;margin-top:6px;"></div>
            </div>
          </div>
          <div style="font-size:12px;color:#9ca3af;">بعد التحويل سيتم تأكيد العملية خلال 24 ساعة.</div>
        </div>
      </div>

      <div style="display:flex;gap:10px;margin-top:16px;">
        <button id="payment-back" class="btn-secondary" style="border-color:#64748b;color:#cbd5e1;">رجوع</button>
        <button id="pay-later" class="btn-secondary" style="border-color:#94a3b8;color:#cbd5e1;">تحويل لاحقاً</button>
        <button id="pay-now" class="btn-primary" style="flex:1;">تاكيد التحويل </button>
      {{-- </div>
      <div id="payment-note" style="font-size:11px;color:#9ca3af;text-align:center;margin-top:8px;">لا يتم الخصم فعلياً في هذا النموذج (نسخة تجريبية)</div> --}}
    </div>

    <!-- success -->
    <div id="step-success" style="display:none;min-height:60vh;align-items:center;justify-content:center;">
      <div class="glass-card" style="max-width:480px;margin:40px auto;padding:24px;text-align:center;">
        <div style="width:80px;height:80px;margin:0 auto 16px;display:flex;align-items:center;justify-content:center;border-radius:50%;background:linear-gradient(90deg,#22c55e,#14b8a6);">
          <svg width="40" height="40" viewBox="0 0 20 20" fill="#fff"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        </div>
        <h2 style="font-size:22px;color:#fff;margin-bottom:8px;">تم تسجيل استثمارك بنجاح!</h2>
        <div style="color:#cbd5e1;margin-bottom:12px;">
          <div>المبلغ: <span id="success-amount" style="color:#06b6d4;font-weight:700;"></span></div>
          <div>نسبة الربح: <span id="success-profit" style="color:#fbbf24;font-weight:700;"></span></div>
          <div>العائد المتوقع: <span id="success-return" style="color:#22c55e;font-weight:700;"></span></div>
          <div>مرجع الدفع: <span id="success-reference" style="color:#fbbf24;font-weight:700;"></span></div>
        </div>
        <div style="display:grid;gap:8px;">
          <a href="/dashboard" class="btn-primary" style="display:block;">لوحة المعلومات</a>
          <a href="/" class="btn-secondary" style="display:block;border-color:#64748b;color:#cbd5e1;">العودة للرئيسية</a>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
  // When national_id is changed on the investment page, call owner lookup and populate fields
  (function(){
    function applyInvestmentLookup(){
      var $nid = $('#national_id');
      var $full = $('#full_name');
      var $email = $('#email');
      var $phone = $('#phone');

      $nid.on('blur change', function(){
        var nid = ($nid.val()||'').trim(); if (!nid) return;
        $.post("{{ route('owner.lookup') }}", { national_id: nid, _token: '{{ csrf_token() }}' }, function(resp){
          if (resp && resp.found) {
            $full.val(resp.owner.full_name || '');
            $email.val(resp.owner.email || '');
            $phone.val(resp.owner.phone || '');
            if (resp.locked) { $nid.prop('readonly', true); }
          }
        }, 'json').fail(function(){});
      });
    }
    $(function(){ applyInvestmentLookup(); });
  })();
</script>


<script>
  $(function(){
    var isLarge = {{ $isLarge ? 'true' : 'false' }};
    var smallPackages = @json($smallPackages);
    var createdInvestmentId = null; // will be set after successful submit
    var lastPaymentReference = null;

    // ضبط الحزمة المختارة من بارامترات الرابط إن وُجدت
    var selected = {!! ($qAmount && $qRate) ? json_encode(['amount'=>$qAmount,'profit_rate'=>$qRate]) : 'null' !!}; // {amount, profit_rate}

    function formatNumber(n){
      return (n || 0).toLocaleString('en-US');
    }

    function computeProfitRateForCustom(amount){
      if(isLarge){
        if(amount >= 100000) return 50;
        if(amount >= 75000) return 48;
        return 45;
      }
      // find closest package for small investments
      var closest = null;
      smallPackages.forEach(function(p){
        if(!closest || Math.abs(p.amount-amount) < Math.abs(closest.amount-amount)) closest = p;
      });
      return closest ? closest.profit_rate : 15;
    }

    function updateCustomSummary(){
      if(!selected){ $('#custom-summary').hide(); $('#proceed-wrapper').hide(); return; }
      var amount = selected.amount || 0;
      var pr = selected.profit_rate || 0;
      var ret = Math.round(amount * pr / 100);
      $('#summary-amount').text(formatNumber(amount) + ' ريال');
      $('#summary-profit').text(pr + '%');
      $('#summary-return').text('+' + formatNumber(ret) + ' ريال');
      $('#custom-summary').show();
      $('#proceed-wrapper').show();
    }

    // في حال وجود بارامترات، فعّل الملخص مباشرة
    if(selected){ updateCustomSummary(); }

    function fillDetails(){
      if(!selected) return;
      var amount = selected.amount || 0;
      var pr = selected.profit_rate || 0;
      var ret = Math.round(amount * pr / 100);
      $('#detail-amount').text(formatNumber(amount) + ' ريال');
      $('#detail-profit').text(pr + '%');
      $('#detail-return').text(formatNumber(ret) + ' ريال');
    }

    function fillConfirm(){
      if(!selected) return;
      var amount = selected.amount || 0;
      var pr = selected.profit_rate || 0;
      var ret = Math.round(amount * pr / 100);
      $('#confirm-amount').text(formatNumber(amount) + ' ريال');
      $('#confirm-profit').text(pr + '%');
      $('#confirm-return').text(formatNumber(ret) + ' ريال');

      $('#confirm-name').text($('#full_name').val());
      $('#confirm-email').text($('#email').val());
      $('#confirm-phone').text($('#phone').val());
      $('#confirm-nid').text($('#national_id').val());
      $('#confirm-bank').text($('#bank_account').val());

      var notes = ($('#notes').val()||'').trim();
      if(notes){ $('#confirm-notes').text(notes); $('#confirm-notes-wrap').show(); } else { $('#confirm-notes-wrap').hide(); }
    }


    // simple helpers for inline errors
    function clearErrors(){
      $('.field-error').remove();
      $('.invalid-field').removeClass('invalid-field');
    }

    function showInlineError(selector, msg){
      $(selector).text(msg).show();
    }
    function hideInlineError(selector){ $(selector).hide().text(''); }

    function showError($el, msg){
      $el.addClass('invalid-field');
      var err = $('<div>').addClass('field-error').text(msg).css({'color':'#ffb4b4','font-size':'12px','margin-top':'6px'});
      // remove existing error below this element
      $el.next('.field-error').remove();
      $el.after(err);
    }

    function validateEmail(v){ return /^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(v); }
    function validatePhone(v){ return v && v.replace(/[^0-9+]/g,'').length >= 8; }
    function validateBank(v){ return /^SA[0-9]{16,}$/.test(v) || v.length >= 10; }

    function validateStep2(){
      clearErrors();
      var firstInvalid = null;
      var name = ($('#full_name').val()||'').trim();
      var email = ($('#email').val()||'').trim();
      var phone = ($('#phone').val()||'').trim();
      var nid = ($('#national_id').val()||'').trim();
      var bank = ($('#bank_account').val()||'').trim();

      if(!name){ showError($('#full_name'), 'الرجاء إدخال الاسم الكامل'); firstInvalid = firstInvalid || $('#full_name'); }
      if(!email){ showError($('#email'), 'الرجاء إدخال البريد الإلكتروني'); firstInvalid = firstInvalid || $('#email'); }
      else if(!validateEmail(email)){ showError($('#email'), 'صيغة البريد الإلكتروني غير صحيحة'); firstInvalid = firstInvalid || $('#email'); }
      if(!phone){ showError($('#phone'), 'الرجاء إدخال رقم الهاتف'); firstInvalid = firstInvalid || $('#phone'); }
      else if(!validatePhone(phone)){ showError($('#phone'), 'رقم الهاتف غير صحيح'); firstInvalid = firstInvalid || $('#phone'); }
      if(!nid){ showError($('#national_id'), 'الرجاء إدخال رقم الهوية'); firstInvalid = firstInvalid || $('#national_id'); }
      if(!bank){ showError($('#bank_account'), 'الرجاء إدخال رقم الحساب البنكي'); firstInvalid = firstInvalid || $('#bank_account'); }
      else if(!validateBank(bank)){ showError($('#bank_account'), 'صيغة رقم الحساب غير معروفة، تأكد من الكتابة مثل: SA...'); firstInvalid = firstInvalid || $('#bank_account'); }

      if(firstInvalid){ firstInvalid.focus(); return false; }
      return true;
    }

    // expose validation to global for manual testing
    window.validateStep2 = validateStep2;

    // package click (small only)
    $('.package-card').on('click', function(){
      var amount = parseFloat($(this).data('amount'));
      var pr = parseFloat($(this).data('profit'));
      selected = { amount: amount, profit_rate: pr };
      $('.package-card').css('border','1px solid rgba(255,255,255,0.1)');
      $(this).css('border','2px solid #f59e0b');
      $('#custom-amount').val('');
      updateCustomSummary();
    });

    // custom amount change
    $('#custom-amount').on('input', function(){
      var val = $(this).val();
      if(!val){ selected = null; updateCustomSummary(); return; }
      var amt = parseFloat(val);
      if(isNaN(amt)) { selected = null; updateCustomSummary(); return; }
      if(isLarge && amt < 50000) { selected = null; updateCustomSummary(); return; }
      if(!isLarge && amt < 1000) { selected = null; updateCustomSummary(); return; }
      var pr = computeProfitRateForCustom(amt);
      selected = { amount: amt, profit_rate: pr };
      // deselect package cards highlight
      $('.package-card').css('border','1px solid rgba(255,255,255,0.1)');
      updateCustomSummary();
    });

    // proceed to step 2
    $('#proceed-to-info').on('click', function(){
      hideInlineError('#step1_error');
      if(!selected){ showInlineError('#step1_error','الرجاء اختيار حزمة أو إدخال مبلغ صالح للاستمرار'); return; }
      fillDetails();
      $('#step-1').hide();
      $('#step-2').show();
      // refresh duration hint when moving to step 2 (ensure visible value)
      try { if (typeof refreshDurationHint === 'function') refreshDurationHint(); } catch(e){}
    });

    $('#back-to-packages').on('click', function(){
      $('#step-2').hide();
      $('#step-1').show();
    });

    $('#continue-to-confirm').on('click', function(){
      hideInlineError('#step2_error');
      if(!validateStep2()){ showInlineError('#step2_error','يوجد حقول غير مكتملة أو بها أخطاء.'); return; }
      if(!selected){ showInlineError('#step2_error','الرجاء اختيار حزمة أو إدخال مبلغ صالح.'); return; }

      // Prepare payload for saving investment
      var amount = selected.amount || 0;
      var rate = selected.profit_rate || 0;
  var durationLabel = ($('#mudat_alaistithmar').val()||'').trim();
      // Precise Arabic label → months mapping to avoid falling back to 1 by mistake
      var durationMap = {
        'ثلاثة أشهر': 3,
        'ستة أشهر': 6,
        'سنة واحدة': 12,
        'سنة وثلاثة أشهر': 15,
        'سنة وستة أشهر': 18,
        'سنتان': 24,
        'سنتان وثلاثة أشهر': 27,
        'سنتان وستة أشهر': 30,
        'ثلاث سنوات': 36
      };
      var durationMonths = durationMap[durationLabel] || 1;
      var notes = ($('#notes').val()||'').trim();

      // CSRF token
      var token = $('meta[name="csrf-token"]').attr('content');

      // POST to server to create the investment, then show confirm step and toast
      $.ajax({
        method: 'POST',
        url: '{{ route('investment.submit') }}',
        data: {
          _token: token,
          amount: amount,
          rate: rate,
          bank_account: $('#bank_account').val() || '',
          duration_months: durationMonths,
          risk_level: 'منخفضة',
          notes: notes,
          investment_type: (isLarge ? 'large' : 'small')
        }
      }).done(function(res){
        // Save created investment id (for payment step)
        if(res && res.investment_id){ createdInvestmentId = res.investment_id; }

  // Fill confirmation UI and go to payment step
        fillConfirm();
        // Set payment summary from confirm values
        $('#pay-amount').text($('#confirm-amount').text());
        $('#pay-return').text($('#confirm-return').text());
  // default method: show bank transfer immediately
  setPaymentMethod('bank');
        // switch steps: hide info and confirm, show payment
        $('#step-2').hide();
        $('#step-3').hide();
        $('#step-payment').show();
        setTimeout(function(){ try{ $('#card-number').trigger('focus'); }catch(e){} }, 50);

        // Show success toast/alert
        try {
          var msg = (res && res.message) ? res.message : 'تم حفظ الاستثمار بنجاح. سنقوم بمراجعة طلبك وإتمام الإجراءات قريبًا.';
          var $toast = $('<div>').text(msg).css({
            position:'fixed',top:'20px',left:'50%',transform:'translateX(-50%)',
            background:'#064e3b',color:'#d1fae5',padding:'10px 16px',borderRadius:'8px',
            border:'1px solid #10b981',zIndex:9999,boxShadow:'0 4px 12px rgba(0,0,0,0.3)'
          });
          $('body').append($toast);
          setTimeout(function(){ $toast.fadeOut(300, function(){ $(this).remove(); }); }, 2500);
        } catch(e) { alert('تم حفظ الاستثمار بنجاح'); }
      }).fail(function(xhr){
        var errMsg = 'تعذر حفظ الاستثمار، يرجى المحاولة لاحقًا.';
        if(xhr && xhr.responseJSON && xhr.responseJSON.message){ errMsg = xhr.responseJSON.message; }
        showInlineError('#step2_error', errMsg);
      });
    });

    $('#back-to-info').on('click', function(){
      $('#step-3').hide();
      $('#step-2').show();
    });

    // Show computed months beside the select for a quick visual confirmation
    function mapDurationMonths(label){
      var durationMap = {
        'ثلاثة أشهر': 3,
        'ستة أشهر': 6,
        'سنة واحدة': 12,
        'سنة وثلاثة أشهر': 15,
        'سنة وستة أشهر': 18,
        'سنتان': 24,
        'سنتان وثلاثة أشهر': 27,
        'سنتان وستة أشهر': 30,
        'ثلاث سنوات': 36
      };
      return durationMap[(label||'').trim()] || 1;
    }
    function refreshDurationHint(){
      try{
        var label = ($('#mudat_alaistithmar').val()||'').trim();
        var months = mapDurationMonths(label);
        $('#duration-months-value').text(months);
        $('#duration-label-value').text(label || '—');
      }catch(e){}
    }
    $('#mudat_alaistithmar').on('change', refreshDurationHint);
    // initialize on page load (in case a default is selected)
    refreshDurationHint();

    // Note: navigation to payment is handled in the submit success above to avoid duplicate handlers.

    // Back from payment to confirm step
    $('#payment-back').on('click', function(){
      $('#step-payment').hide();
      $('#step-3').show();
    });

    function setPaymentMethod(m){
      $('.pm-btn').css({borderColor:'#4b5563', background:'#111827'});
      $('.pm-btn[data-method="'+m+'"]').css({borderColor:'#60a5fa', background:'#0b1830'});
      $('#pm-card, #pm-apple, #pm-bank').hide();
      if(m==='card') $('#pm-card').show();
      if(m==='apple') $('#pm-apple').show();
      if(m==='bank') $('#pm-bank').show();
      $('#pay-now').text(m==='bank' ? 'تأكيد التحويل' : 'ارفق الايصال وبيانات الدفع أولا ');
      $('#pay-now').data('method', m);
      // Generate a reference code when switching to bank
      if(m==='bank'){
        if(!$('#bank-ref').data('generated')){
          var ref = 'INV-' + Math.random().toString(36).substring(2, 8).toUpperCase();
          $('#bank-ref').text(ref).data('generated', true);
        }
      }
    }

    // helper: format IBAN in groups of 4 for display
    function formatIBAN(iban){
      if(!iban) return '—';
      try { return String(iban).replace(/[^A-Za-z0-9]/g,'').toUpperCase().match(/.{1,4}/g).join(' '); } catch(e){ return String(iban); }
    }

    // Load bank settings from server and populate pm-bank details
    function loadBankSettings(){
      $.getJSON('{{ route('api.bank.settings') }}').done(function(resp){
        if(!resp) return;
        try {
          $('#bank-account-name').text(resp.account_name || '—');
          $('#bank-bank-name').text(resp.bank_name || '—');
          $('#bank-account-number').text(resp.account_number || '—');
          $('#bank-iban').text(resp.iban ? formatIBAN(resp.iban) : '—');
          $('#bank-swift').text(resp.swift || '—');
          var ref = resp.reference_code || '';
          if(ref){
            $('#bank-ref').text(ref).data('generated', true);
          } else {
            // leave any previously-generated ref (do not overwrite) — mark as not generated so setPaymentMethod may create one
            $('#bank-ref').data('generated', false);
          }
          // populate instructions (split newlines into list items)
          var instr = resp.instructions || '';
          var $ul = $('#bank-instructions-ul');
          if($ul.length){
            $ul.empty();
            // Show instructions only when admin provided a non-empty string.
            // Treat undefined/null as "no instructions" -> hide block.
            if (typeof resp.instructions !== 'undefined' && resp.instructions !== null && String(resp.instructions).trim() !== ''){
              var lines = String(resp.instructions).split(/\r?\n/).map(function(s){ return s.trim(); }).filter(function(s){ return s.length>0; });
              if(lines.length){ lines.forEach(function(l){ $ul.append($('<li>').html(l)); }); }
              else { $ul.append($('<li>').html(String(resp.instructions))); }
              $('#bank-instructions-block').show();
            } else {
              // no instructions -> hide
              $('#bank-instructions-block').hide();
            }
          }
          // store settings globally for client-side validation/visibility
          try { window.bankSettings = resp || {}; applyBankVisibility(); } catch(e) { window.bankSettings = window.bankSettings || {}; }
        } catch(e){ console.warn('bank settings parse error', e); }
      }).fail(function(){ /* ignore silently */ });
    }

    // load settings on page ready
    loadBankSettings();

    // Apply bank visibility controls based on admin flags
    // This will be set by loadBankSettings when the API responds.
    window.bankSettings = window.bankSettings || {};
    function applyBankVisibility() {
      var s = window.bankSettings || {};
      try {
        if (typeof s.ask_sender !== 'undefined') {
          if (s.ask_sender) { $('#bank-sender-name').closest('div').show(); } else { $('#bank-sender-name').closest('div').hide().find('input').val(''); }
        }
        if (typeof s.ask_datetime !== 'undefined') {
          if (s.ask_datetime) { $('#bank-date').closest('div').show(); } else { $('#bank-date').closest('div').hide().find('input').val(''); }
        }
        if (typeof s.require_receipt !== 'undefined') {
          if (s.require_receipt) { $('#bank-receipt').closest('div').show(); } else { $('#bank-receipt').closest('div').hide().find('input').val(''); $('#bank-receipt-name').text(''); }
        }
      } catch(e){ /* defensive: if DOM shape unexpected, ignore */ }
    }

    $('#payment-methods').on('click', '.pm-btn', function(){ setPaymentMethod($(this).data('method')); });

    // Copy buttons for IBAN and Reference
    $(document).on('click', '.copy-btn', function(){
      var target = $(this).data('copy-target');
      var $t = $(target);
      if(!$t.length) return;
      var text = $t.text().trim();
      if(($t.attr('id')||'').toLowerCase().indexOf('iban') !== -1){ text = text.replace(/\s+/g,''); }
      navigator.clipboard.writeText(text).then(()=>{
        var $btn = $(this);
        var old = $btn.text();
        $btn.text('تم النسخ');
        setTimeout(()=> $btn.text(old), 1000);
      });
    });

    // Show selected receipt filename
    $(document).on('change', '#bank-receipt', function(){
      var f = this.files && this.files[0] ? this.files[0].name : '';
      $('#bank-receipt-name').text(f ? ('الملف: ' + f) : '');
      $('#bank-error').hide().text('');
    });

    function validateCard(){
      var num = ($('#card-number').val()||'').replace(/\s+/g,'');
      var exp = ($('#card-exp').val()||'').trim();
      var cvc = ($('#card-cvc').val()||'').trim();
      var name = ($('#card-name').val()||'').trim();
      var err = '';
      if(!/^\d{16}$/.test(num)) err = 'رقم البطاقة غير صالح';
      else if(!/^(0[1-9]|1[0-2])\/(\d{2})$/.test(exp)) err = 'تاريخ الانتهاء غير صالح';
      else if(!/^\d{3,4}$/.test(cvc)) err = 'رمز CVC غير صالح';
      else if(!name) err = 'أدخل اسم حامل البطاقة';
      if(err){ $('#card-error').text(err).show(); return false; }
      $('#card-error').hide();
      return true;
    }

    $('#pay-now').on('click', function(){
      var method = $('#pay-now').data('method') || 'card';
      if(method === 'card' && !validateCard()) return;
      if(method === 'bank'){
        // basic checks for bank transfer details, only enforce fields that are required per admin settings
        var s = window.bankSettings || {};
        var requireSender = (typeof s.ask_sender === 'undefined') ? true : !!s.ask_sender;
        var requireDate = (typeof s.ask_datetime === 'undefined') ? true : !!s.ask_datetime;
        var requireReceipt = (typeof s.require_receipt === 'undefined') ? true : !!s.require_receipt;

        var sender = ($('#bank-sender-name').val()||'').trim();
        var date = ($('#bank-date').val()||'').trim();
        var file = $('#bank-receipt')[0]?.files?.[0];
        var err = '';
        if(requireSender && !sender) err = 'يرجى إدخال اسم المحوِّل';
        else if(requireDate && !date) err = 'يرجى إدخال تاريخ ووقت التحويل';
        else if(requireReceipt && !file) err = 'يرجى إرفاق إيصال التحويل';
        if(err){ $('#bank-error').text(err).show(); return; }
        $('#bank-error').hide();
      }

      // Send payment to server
      var token = $('meta[name="csrf-token"]').attr('content');
      var amountText = ($('#confirm-amount').text()||'').replace(/[^0-9.]/g,'');
      var amount = parseFloat(amountText) || undefined;
      var $btn = $('#pay-now');
      var original = $btn.text();
      $btn.prop('disabled', true).text('... جاري معالجة الدفع');

      var ajaxOptions = { method: 'POST', url: '{{ route('investment.pay') }}' };
      if(method === 'bank'){
        var fd = new FormData();
        fd.append('_token', token);
        fd.append('method', method);
        if(amount) fd.append('amount', amount);
        if(createdInvestmentId) fd.append('investment_id', createdInvestmentId);
        fd.append('sender_name', ($('#bank-sender-name').val()||''));
        fd.append('transfer_at', ($('#bank-date').val()||''));
        fd.append('note', ($('#bank-note').val()||''));
        var bankRef = ($('#bank-ref').text()||'').trim();
        if(bankRef) fd.append('reference', bankRef);
        var fileObj = $('#bank-receipt')[0]?.files?.[0];
        if(fileObj) fd.append('receipt', fileObj);
        ajaxOptions.data = fd;
        ajaxOptions.processData = false;
        ajaxOptions.contentType = false;
      } else {
        ajaxOptions.data = { _token: token, method: method, amount: amount, investment_id: createdInvestmentId };
      }

      $.ajax(ajaxOptions).done(function(res){
        // Show success and move to success step
        try {
          var msg = (res && res.message) ? res.message : 'تم استلام الدفع وتفعيل الاستثمار. شكراً لك!';
          var $toast = $('<div>').text(msg).css({
            position:'fixed',top:'20px',left:'50%',transform:'translateX(-50%)',
            background:'#064e3b',color:'#d1fae5',padding:'10px 16px',borderRadius:'8px',
            border:'1px solid #10b981',zIndex:9999,boxShadow:'0 4px 12px rgba(0,0,0,0.3)'
          });
          $('body').append($toast);
          setTimeout(function(){ $toast.fadeOut(300, function(){ $(this).remove(); }); }, 2500);
        } catch(e) {}
        if(res && res.payment_reference){ lastPaymentReference = res.payment_reference; $('#success-reference').text(lastPaymentReference); }
        // Redirect to investor dashboard to let user track the saved payment
        try {
          window.location.href = @json(route('investor.dashboard'));
          return;
        } catch(e) {
          // fallback to showing success screen
          $('#success-amount').text($('#confirm-amount').text());
          $('#success-profit').text($('#confirm-profit').text());
          $('#success-return').text($('#confirm-return').text());
          $('#step-payment').hide();
          $('#step-success').show();
        }
      }).fail(function(xhr){
        var errMsg = 'تعذر إتمام الدفع، يرجى المحاولة لاحقًا.';
        if(xhr && xhr.responseJSON && xhr.responseJSON.message){ errMsg = xhr.responseJSON.message; }
        var $toast = $('<div>').text(errMsg).css({
          position:'fixed',top:'20px',left:'50%',transform:'translateX(-50%)',
          background:'#3f1212',color:'#ffd7d7',padding:'10px 16px',borderRadius:'8px',
          border:'1px solid #b91c1c',zIndex:9999,boxShadow:'0 4px 12px rgba(0,0,0,0.3)'
        });
        $('body').append($toast);
        setTimeout(function(){ $toast.fadeOut(300, function(){ $(this).remove(); }); }, 2500);
      }).always(function(){
        $btn.prop('disabled', false).text(original);
      });
    });

    // Pay later: save a pending bank transfer without receipt so user can pay at a later time
    $('#pay-later').on('click', function(){
      // basic checks: investment must exist or createdInvestmentId set
      var method = 'bank';
      var sender = ($('#bank-sender-name').val()||'').trim();
      var date = ($('#bank-date').val()||'').trim();
      // For pay-later we allow sender/date to be empty (user may fill later) but require at least investment id
      if(!createdInvestmentId){
        // prompt user to first تأكيد الاستثمار
        var $msg = $('<div>').text('الرجاء تأكيد الاستثمار أولاً ثم اختر تحويل لاحقاً').css({position:'fixed',top:20,left:'50%',transform:'translateX(-50%)',background:'#3f2b00',color:'#fff',padding:'10px 14px',borderRadius:8,zIndex:9999});
        $('body').append($msg);
        setTimeout(function(){ $msg.fadeOut(300,function(){ $(this).remove(); }); }, 2200);
        return;
      }

      var token = $('meta[name="csrf-token"]').attr('content');
      var amountText = ($('#confirm-amount').text()||'').replace(/[^0-9.]/g,'');
      var amount = parseFloat(amountText) || undefined;

      var $btn = $(this);
      var original = $btn.text();
      $btn.prop('disabled', true).text('... حفظ');

      // POST minimal payload (no file) to create a pending bank payment
      $.ajax({
        method: 'POST',
        url: '{{ route('investment.pay') }}',
        data: {
          _token: token,
          method: method,
          amount: amount,
          investment_id: createdInvestmentId,
          sender_name: sender,
          transfer_at: date,
          note: ($('#bank-note').val()||''),
          reference: ($('#bank-ref').text()||'').trim(),
          deferred: 1
        }
      }).done(function(res){
        try {
          var msg = (res && res.message) ? res.message : 'تم حفظ طلب التحويل؛ يمكنك إرفاق الإيصال لاحقًا.';
          var $toast = $('<div>').text(msg).css({position:'fixed',top:'20px',left:'50%',transform:'translateX(-50%)',background:'#064e3b',color:'#d1fae5',padding:'10px 16px',borderRadius:'8px',border:'1px solid #10b981',zIndex:9999,boxShadow:'0 4px 12px rgba(0,0,0,0.3)'});
          $('body').append($toast);
          setTimeout(function(){ $toast.fadeOut(300, function(){ $(this).remove(); }); }, 2500);
        } catch(e) {}

        if(res && res.payment_reference){ lastPaymentReference = res.payment_reference; $('#success-reference').text(lastPaymentReference); }
        // Redirect user to investor dashboard and open payments tab so they can track the saved request
        try {
          var dash = @json(route('investor.dashboard')) + '?tab=payments&last_id=' + encodeURIComponent(createdInvestmentId || lastPaymentReference || '');
          window.location.href = dash;
          return;
        } catch(e) {
          // fallback to showing success screen
          $('#success-amount').text($('#confirm-amount').text());
          $('#success-profit').text($('#confirm-profit').text());
          $('#success-return').text($('#confirm-return').text());
          $('#step-payment').hide();
          $('#step-success').show();
        }
      }).fail(function(xhr){
        var errMsg = 'تعذر حفظ طلب التحويل، يرجى المحاولة لاحقًا.';
        if(xhr && xhr.responseJSON && xhr.responseJSON.message){ errMsg = xhr.responseJSON.message; }
        var $toast = $('<div>').text(errMsg).css({position:'fixed',top:'20px',left:'50%',transform:'translateX(-50%)',background:'#3f1212',color:'#ffd7d7',padding:'10px 16px',borderRadius:'8px',border:'1px solid #b91c1c',zIndex:9999,boxShadow:'0 4px 12px rgba(0,0,0,0.3)'});
        $('body').append($toast);
        setTimeout(function(){ $toast.fadeOut(300, function(){ $(this).remove(); }); }, 2500);
      }).always(function(){
        $btn.prop('disabled', false).text(original);
      });
    });
  });
</script>
@endsection
