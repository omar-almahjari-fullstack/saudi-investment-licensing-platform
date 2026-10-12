@extends('layouts.app')

@section('content')
<style>
    .license-page { padding: 28px; max-width: 980px; margin: 0 auto; }
    .license-hero { display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:14px }
    .license-card { background: linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01)); border:1px solid rgba(255,255,255,0.04); padding:18px; border-radius:10px; }
    .license-grid { display:grid; grid-template-columns: 1fr 360px; gap:16px; align-items:start; }
    .meta-row { display:flex;flex-direction:column; gap:10px }
    .meta-label { color:#9ca3af; font-size:13px }
    .meta-value { color:#fff; font-weight:700 }
    .doc-row { display:flex; gap:12px; align-items:center }
    /* Small square thumbnail for identity images */
    .doc-thumb { width:80px; height:80px; object-fit:cover; border-radius:6px; border:1px solid rgba(255,255,255,0.06); }
    .btn-secondary.small { padding:6px 10px; font-size:13px }
    .form-input { width:100%; padding:10px 12px; border-radius:8px; background:#071124; border:1px solid rgba(255,255,255,0.04); color:#fff }
    .form-textarea { width:100%; padding:10px 12px; border-radius:8px; background:#071124; border:1px solid rgba(255,255,255,0.04); color:#fff; resize:vertical }
    @media (max-width: 780px){
        .license-grid { grid-template-columns: 1fr; }
        .license-hero { flex-direction:column; align-items:flex-start }
    }
    .form-grid { display:grid; grid-template-columns: repeat(2, 1fr); gap:12px; }
    .form-grid .full { grid-column: 1 / -1; }
</style>

<div class="license-page">
    <div class="license-hero">
        <div>
            <a href="{{ route('investor.dashboard') }}" class="btn-secondary" style="border-color:#22d3ee;color:#22d3ee;margin-bottom:6px;">← العودة للوحة التحكم</a>
            <h1 style="font-size:28px;color:#fff;margin:6px 0;">تفاصيل طلب الترخيص</h1>
            <div style="color:#9ca3af">عرض كامل لبيانات طلب الترخيص المقدم</div>
        </div>
        @php
            $rawStatus = strtolower((string)($license->status ?? ''));
            $statusMap = [
                'approved'  => ['موافق عليه', '#16a34a'],
                'active'    => ['نشط', '#16a34a'],
                'completed' => ['مكتمل', '#2563eb'],
                'pending'   => ['معلق', '#f59e0b'],
                'rejected'  => ['مرفوض', '#ef4444'],
            ];
            $mapped = $statusMap[$rawStatus] ?? [($license->status ?? '-'), '#6b7280'];
            [$statusLabel, $statusColor] = $mapped;
        @endphp
        <div style="display:flex;gap:8px;align-items:center;">
            <div style="color:#9ca3af;font-size:13px">الحالة</div>
            <div id="lic-status" style="background: {{ $statusColor }}; padding:6px 10px; border-radius:8px; color:#fff; font-weight:700">{{ $statusLabel }}</div>
        </div>
    </div>
    {{-- Payment form step (hidden by default) --}}
    <div id="step-payment" style="display:none;margin-top:12px;">
        <h2 style="font-size:22px;color:#fff;margin:0 0 16px;">الدفع</h2>
        <div class="glass-card" style="padding:16px;margin-bottom:16px;">
            <div style="display:flex;justify-content:space-between;align-items:center;">
                <div style="color:#9ca3af;font-size:12px;">المبلغ المستحق</div>
                <div id="pay-amount" style="color:#06b6d4;font-weight:700;">{{ number_format($license->amount ?? 0) }} ر.س</div>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;margin-top:6px;">
                <div style="color:#9ca3af;font-size:12px;">العائد المتوقع</div>
                <div id="pay-return" style="color:#22c55e;font-weight:700;">{{ number_format($license->expected_return ?? 0) }} ر.س</div>
            </div>
        </div>

        <div id="payment-methods" style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:12px;">
            <button class="pm-btn" data-method="bank" style="flex:1;border:1px solid #4b5563;background:#111827;color:#fff;padding:10px;border-radius:8px;">تحويل بنكي</button>
        </div>

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
                            <input id="bank-sender-name" name="sender_name" placeholder="الاسم كما في الحساب" style="width:100%;background:#1f2937;border:1px solid #4b5563;color:#fff;padding:10px;border-radius:8px;" />
                        </div>
                        <div>
                            <label style="display:block;color:#cbd5e1;font-size:13px;margin-bottom:6px;">تاريخ ووقت التحويل</label>
                            <input id="bank-date" name="transfer_at" type="datetime-local" style="width:100%;background:#1f2937;border:1px solid #4b5563;color:#fff;padding:10px;border-radius:8px;" />
                        </div>
                    </div>
                    <div>
                        <label style="display:block;color:#cbd5e1;font-size:13px;margin-bottom:6px;">ملاحظة (اختياري)</label>
                        <input id="bank-note" name="note" placeholder="أي تفاصيل إضافية" style="width:100%;background:#1f2937;border:1px solid #4b5563;color:#fff;padding:10px;border-radius:8px;" />
                    </div>
                    <div>
                        <label style="display:block;color:#cbd5e1;font-size:13px;margin-bottom:6px;">إرفاق إيصال التحويل (صورة أو PDF)</label>
                        <input id="bank-receipt" name="receipt" type="file" accept="image/*,.pdf" style="width:100%;background:#1f2937;border:1px solid #4b5563;color:#fff;padding:10px;border-radius:8px;" />
                        <div id="bank-receipt-name" style="font-size:12px;color:#9ca3af;margin-top:6px;"></div>
                        <div id="bank-error" style="display:none;color:#ffb4b4;font-size:12px;margin-top:6px;"></div>
                    </div>
                </div>
                <div style="font-size:12px;color:#9ca3af;">بعد التحويل سيتم تأكيد العملية خلال 24 ساعة.</div>
            </div>
        </div>

        <div style="display:flex;gap:10px;margin-top:16px;">
            <button id="payment-back" class="btn-secondary" style="border-color:#64748b;color:#cbd5e1;">رجوع</button>
            {{-- <button id="pay-later" class="btn-secondary" style="border-color:#94a3b8;color:#cbd5e1;">تحويل لاحقاً</button> --}}
            <button id="pay-now" class="btn-primary" style="flex:1;">تاكيد التحويل </button>
        </div>
        </div>

        @push('scripts')
        <script>
            // Load bank settings and hide/show fields accordingly
            $(function(){
                // helper: format IBAN in 4-char groups for display
                function formatIBAN(iban){
                    if(!iban) return '—';
                    try { return String(iban).replace(/[^A-Za-z0-9]/g,'').toUpperCase().match(/.{1,4}/g).join(' '); } catch(e){ return String(iban); }
                }

                $.getJSON('{{ route('api.bank.settings') }}').done(function(resp){
                    try {
                        var s = resp || {};
                        // Populate bank details from DB
                        if (resp) {
                            if (resp.account_name) { $('#bank-account-name').text(resp.account_name); }
                            if (resp.bank_name) { $('#bank-bank-name').text(resp.bank_name); }
                            if (resp.account_number) { $('#bank-account-number').text(resp.account_number); }
                            if (resp.iban) { $('#bank-iban').text(formatIBAN(resp.iban)); }
                            if (resp.swift) { $('#bank-swift').text(resp.swift); }
                            if (resp.reference_code) { $('#bank-ref').text(resp.reference_code); }
                        }
                        if(typeof s.ask_sender !== 'undefined'){
                            if(s.ask_sender) { $('#bank-sender-name').closest('div').show(); } else { $('#bank-sender-name').closest('div').hide().find('input').val(''); }
                        }
                        if(typeof s.ask_datetime !== 'undefined'){
                            if(s.ask_datetime) { $('#bank-date').closest('div').show(); } else { $('#bank-date').closest('div').hide().find('input').val(''); }
                        }
                        if(typeof s.require_receipt !== 'undefined'){
                            if(s.require_receipt) { $('#bank-receipt').closest('div').show(); } else { $('#bank-receipt').closest('div').hide().find('input').val(''); $('#bank-receipt-name').text(''); }
                        }

                        // populate instructions list (show defaults when no config, hide when explicitly empty)
                            var instr = resp.instructions || ''; // handled below; populate only when non-empty
                        var $ul = $('#bank-instructions-ul');
                        if($ul.length){
                            $ul.empty();
                            if(typeof resp.instructions === 'undefined' || resp.instructions === null){
                                // Show only when instructions is a non-empty string
                                if(typeof resp.instructions !== 'undefined' && resp.instructions !== null && String(resp.instructions).trim() !== ''){
                                    var lines = String(resp.instructions).split(/\r?\n/).map(function(s){ return s.trim(); }).filter(function(s){ return s.length>0; });
                                    if(lines.length){ lines.forEach(function(l){ $ul.append($('<li>').html(l)); }); }
                                    else { $ul.append($('<li>').html(String(resp.instructions))); }
                                    $('#bank-instructions-block').show();
                                } else {
                                    $('#bank-instructions-block').hide();
                                }
                            } else if (instr && instr.toString().trim()){
                                var lines = instr.toString().split(/\r?\n/).filter(Boolean);
                                if(lines.length){ lines.forEach(function(l){ $ul.append($('<li>').text(l)); }); }
                                else { $ul.append($('<li>').text(instr)); }
                                $('#bank-instructions-block').show();
                            } else {
                                $('#bank-instructions-block').hide();
                            }
                        }
                    } catch(e) {}
                }).fail(function(){});
            });
        </script>
        @endpush
    </div>

    <div class="license-card">
        <form class="license-form" onsubmit="return false;">
            <div class="form-grid">
                <div>
                    <label class="meta-label">اسم النشاط</label>
                    <input type="text" class="form-input" value="{{ $license->business_name ?? '' }}" disabled />
                </div>

                <div>
                    <label class="meta-label">نوع النشاط</label>
                    <input type="text" class="form-input" value="{{ $license->business_type ?? '' }}" disabled />
                </div>

                <div>
                    <label class="meta-label">الموقع</label>
                    <input type="text" class="form-input" value="{{ $license->location ?? '' }}" disabled />
                </div>

                <div>
                    <label class="meta-label">اسم مقدم الطلب</label>
                    <input type="text" class="form-input" value="{{ optional($applicant)->full_name ?? ($license->user_name ?? '') }}" disabled />
                </div>

                <div>
                    <label class="meta-label">رقم الهاتف</label>
                    <input type="text" class="form-input" value="{{ optional($investor)->phone ?? (optional($applicant)->phone ?? '') }}" disabled />
                </div>

                <div>
                    <label class="meta-label">رقم الهوية</label>
                    @php
                        $displayNid = '';
                        if (isset($applicant) && $applicant) {
                            $displayNid = $applicant->national_id ?? '';
                        }
                        if (empty($displayNid) && isset($investor)) { $displayNid = $investor->national_id ?? ''; }
                        if (empty($displayNid) && isset($license)) { $displayNid = $license->national_id ?? ''; }
                    @endphp
                    <input type="text" class="form-input" value="{{ $displayNid }}" disabled />
                </div>

                <div>
                    <label class="meta-label">تاريخ الإنشاء</label>
                    @php $createdAt = \Carbon\Carbon::parse($license->created_at ?? null); @endphp
                    <input type="text" class="form-input" value="{{ $createdAt ? $createdAt->toDayDateTimeString() : '' }}" disabled />
                    <div style="margin-top:6px;color:#9ca3af;font-size:12px;">
                        {{ $createdAt ? $createdAt->locale('ar')->translatedFormat('D, M j, Y g:i A') : '' }}
                    </div>
                </div>

                <div class="full">
                    <label class="meta-label">وصف النشاط</label>
                    <textarea class="form-textarea" rows="6" disabled>{{ $license->activity_description ?? '' }}</textarea>
                </div>

                <div class="full">
                    <label class="meta-label">المستندات</label>
                    <div style="display:flex;flex-direction:column;gap:12px;margin-top:6px">
                        @php
                            function is_image_url($u){ return is_string($u) && preg_match('/\.(png|jpe?g|gif|webp|bmp)(\?|$)/i', $u); }
                        @endphp

                        @php
                            $docsMap = isset($documents) ? (array) $documents : [];
                        @endphp
                        @php
                            // Prefer canonical user identity previews passed from controller, but be defensive
                            $userPreviews = isset($userIdentityPreviews) ? (array) $userIdentityPreviews : [];
                        @endphp

                        @php
                            $hasFront = (\Illuminate\Support\Facades\Schema::hasColumn('users','identity_image_front') && isset($userPreviews['identity_front'])) || isset($docsMap['identity_front']) || (isset($license->identity_image_front) && $license->identity_image_front);
                            $hasBack = (\Illuminate\Support\Facades\Schema::hasColumn('users','identity_image_back') && isset($userPreviews['identity_back'])) || isset($docsMap['identity_back']) || (isset($license->identity_image_back) && $license->identity_image_back);
                        @endphp

                        @if($hasFront || $hasBack)
                            <div style="display:flex;gap:12px;align-items:flex-start;flex-wrap:wrap;">
                                @if($hasFront)
                                    @php
                                        if (\Illuminate\Support\Facades\Schema::hasColumn('users','identity_image_front') && isset($userPreviews['identity_front'])) {
                                            $front = $userPreviews['identity_front'];
                                        } else {
                                            $front = $docsMap['identity_front'] ?? data_get($license, 'identity_image_front');
                                        }
                                    @endphp
                                    <div class="doc-row" style="flex:1;min-width:220px;flex-direction:column;align-items:center;">
                                        @php
                                            $frontMeta = is_array($front) ? $front : null;
                                            $frontUrl = is_array($front) ? ($front['url'] ?? null) : $front;
                                            if (is_string($frontUrl) && $frontUrl !== '') {
                                                try {
                                                    if (!preg_match('/^(https?:)?\/\//', $frontUrl) && substr($frontUrl,0,1) !== '/') {
                                                        $frontUrl = \Illuminate\Support\Facades\Storage::url($frontUrl);
                                                    }
                                                } catch (\Throwable $e) {
                                                    if (!preg_match('/^(https?:)?\/\//', $frontUrl) && substr($frontUrl,0,1) !== '/') {
                                                        $frontUrl = '/storage/' . ltrim($frontUrl, '/');
                                                    }
                                                }
                                                if (!preg_match('/^(https?:)?\/\//', $frontUrl) && substr($frontUrl,0,1) !== '/') {
                                                    $frontUrl = '/' . ltrim($frontUrl, '/');
                                                }
                                            }
                                        @endphp
                                            @if($frontUrl && is_image_url($frontUrl))
                                                <img src="{{ $frontUrl }}" class="doc-thumb preview-trigger" data-type="image" data-src="{{ $frontUrl }}" alt="id-front" />
                                            @else
                                                <div class="doc-thumb preview-trigger" data-type="file" data-src="{{ $frontUrl }}" style="display:flex;align-items:center;justify-content:center;color:#22d3ee;font-weight:700">ملف</div>
                                            @endif
                                            <div style="color:#fff;font-weight:700;text-align:center;margin-top:8px;">صورة الهوية (أمام)</div>
                                            <div style="display:flex;gap:8px;margin-top:6px;justify-content:center;">
                                                <button type="button" class="btn-secondary small preview-btn" data-src="{{ $frontUrl }}">فتح</button>
                                                <a href="{{ $frontUrl }}" download class="btn-secondary small">تنزيل</a>
                                            </div>
                                    </div>
                                @endif

                                @if($hasBack)
                                    @php
                                        if (\Illuminate\Support\Facades\Schema::hasColumn('users','identity_image_back') && isset($userPreviews['identity_back'])) {
                                            $back = $userPreviews['identity_back'];
                                        } else {
                                            $back = $docsMap['identity_back'] ?? data_get($license, 'identity_image_back');
                                        }
                                    @endphp
                                    <div class="doc-row" style="flex:1;min-width:220px;flex-direction:column;align-items:center;">
                                        @php
                                            $backMeta = is_array($back) ? $back : null;
                                            $backUrl = is_array($back) ? ($back['url'] ?? null) : $back;
                                            if (is_string($backUrl) && $backUrl !== '') {
                                                try {
                                                    if (!preg_match('/^(https?:)?\/\//', $backUrl) && substr($backUrl,0,1) !== '/') {
                                                        $backUrl = \Illuminate\Support\Facades\Storage::url($backUrl);
                                                    }
                                                } catch (\Throwable $e) {
                                                    if (!preg_match('/^(https?:)?\/\//', $backUrl) && substr($backUrl,0,1) !== '/') {
                                                        $backUrl = '/storage/' . ltrim($backUrl, '/');
                                                    }
                                                }
                                                if (!preg_match('/^(https?:)?\/\//', $backUrl) && substr($backUrl,0,1) !== '/') {
                                                    $backUrl = '/' . ltrim($backUrl, '/');
                                                }
                                            }
                                        @endphp
                                            @if($backUrl && is_image_url($backUrl))
                                                <img src="{{ $backUrl }}" class="doc-thumb preview-trigger" data-type="image" data-src="{{ $backUrl }}" alt="id-back" />
                                            @else
                                                <div class="doc-thumb preview-trigger" data-type="file" data-src="{{ $backUrl }}" style="display:flex;align-items:center;justify-content:center;color:#22d3ee;font-weight:700">ملف</div>
                                            @endif
                                            <div style="color:#fff;font-weight:700;text-align:center;margin-top:8px;">صورة الهوية (خلف)</div>
                                            <div style="display:flex;gap:8px;margin-top:6px;justify-content:center;">
                                                <button type="button" class="btn-secondary small preview-btn" data-src="{{ $backUrl }}">فتح</button>
                                                <a href="{{ $backUrl }}" download class="btn-secondary small">تنزيل</a>
                                            </div>
                                    </div>
                                @endif

                                {{-- additional docs placed under identity thumbnails --}}
                                <div style="flex-basis:100%;height:0"></div>
                                @php
                                    $bank = $docsMap['bank_statement'] ?? data_get($license, 'bank_statement');
                                    $bankUrl = is_array($bank) ? ($bank['url'] ?? null) : $bank;
                                    if (is_string($bankUrl) && $bankUrl !== '') {
                                        try {
                                            if (!preg_match('/^(https?:)?\/\//', $bankUrl) && substr($bankUrl,0,1) !== '/') {
                                                $bankUrl = \Illuminate\Support\Facades\Storage::url($bankUrl);
                                            }
                                        } catch (\Throwable $e) {
                                            if (!preg_match('/^(https?:)?\/\//', $bankUrl) && substr($bankUrl,0,1) !== '/') {
                                                $bankUrl = '/storage/' . ltrim($bankUrl, '/');
                                            }
                                        }
                                        if (!preg_match('/^(https?:)?\/\//', $bankUrl) && substr($bankUrl,0,1) !== '/') {
                                            $bankUrl = '/' . ltrim($bankUrl, '/');
                                        }
                                    }
                                @endphp
                                @if(!empty($bankUrl))
                                    <div class="doc-row" style="flex:1;min-width:220px;margin-top:6px;flex-direction:column;align-items:center;">
                                        @if($bankUrl && is_image_url($bankUrl))
                                            <img src="{{ $bankUrl }}" class="doc-thumb preview-trigger" data-type="image" data-src="{{ $bankUrl }}" alt="bank-statement" />
                                        @else
                                            <div class="doc-thumb preview-trigger" data-type="file" data-src="{{ $bankUrl }}" style="display:flex;align-items:center;justify-content:center;color:#22d3ee;font-weight:700">PDF</div>
                                        @endif
                                            <div style="color:#fff;font-weight:700;text-align:center;margin-top:8px;">كشف حساب بنكي</div>
                                            <div style="display:flex;gap:8px;margin-top:6px;justify-content:center;">
                                                <button type="button" class="btn-secondary small preview-btn" data-src="{{ $bankUrl }}">فتح</button>
                                                <a href="{{ $bankUrl }}" download class="btn-secondary small">تنزيل</a>
                                            </div>
                                    </div>
                                @endif

                                @php
                                    $salary = $docsMap['salary_certificate'] ?? data_get($license, 'salary_certificate');
                                    $salaryUrl = is_array($salary) ? ($salary['url'] ?? null) : $salary;
                                    if (is_string($salaryUrl) && $salaryUrl !== '') {
                                        try {
                                            if (!preg_match('/^(https?:)?\/\//', $salaryUrl) && substr($salaryUrl,0,1) !== '/') {
                                                $salaryUrl = \Illuminate\Support\Facades\Storage::url($salaryUrl);
                                            }
                                        } catch (\Throwable $e) {
                                            if (!preg_match('/^(https?:)?\/\//', $salaryUrl) && substr($salaryUrl,0,1) !== '/') {
                                                $salaryUrl = '/storage/' . ltrim($salaryUrl, '/');
                                            }
                                        }
                                        if (!preg_match('/^(https?:)?\/\//', $salaryUrl) && substr($salaryUrl,0,1) !== '/') {
                                            $salaryUrl = '/' . ltrim($salaryUrl, '/');
                                        }
                                    }
                                @endphp
                                @if(!empty($salaryUrl))
                                    <div class="doc-row" style="flex:1;min-width:220px;margin-top:6px;flex-direction:column;align-items:center;">
                                        @if($salaryUrl && is_image_url($salaryUrl))
                                            <img src="{{ $salaryUrl }}" class="doc-thumb preview-trigger" data-type="image" data-src="{{ $salaryUrl }}" alt="salary-certificate" />
                                        @else
                                            <div class="doc-thumb preview-trigger" data-type="file" data-src="{{ $salaryUrl }}" style="display:flex;align-items:center;justify-content:center;color:#22d3ee;font-weight:700">PDF</div>
                                        @endif
                                            <div style="color:#fff;font-weight:700;text-align:center;margin-top:8px;">شهادة راتب</div>
                                            <div style="display:flex;gap:8px;margin-top:6px;justify-content:center;">
                                                <button type="button" class="btn-secondary small preview-btn" data-src="{{ $salaryUrl }}">فتح</button>
                                                <a href="{{ $salaryUrl }}" download class="btn-secondary small">تنزيل</a>
                                            </div>
                                    </div>
                                @endif
                            </div>
                        @endif

                            @php
                                // Consider userPreviews only if users columns exist
                                $hasAnyIdentityPreview = ((\Illuminate\Support\Facades\Schema::hasColumn('users','identity_image_front') && !empty($userPreviews['identity_front'])) || (\Illuminate\Support\Facades\Schema::hasColumn('users','identity_image_back') && !empty($userPreviews['identity_back'])) ) || isset($docsMap['identity_front']) || isset($docsMap['identity_back']) || (isset($license->identity_image_front) && $license->identity_image_front) || (isset($license->identity_image_back) && $license->identity_image_back);
                                $hasAnyLicenseDoc = (isset($license->bank_statement) && $license->bank_statement) || (isset($license->salary_certificate) && $license->salary_certificate) || isset($docsMap['bank_statement']) || isset($docsMap['salary_certificate']);
                            @endphp

                        @if(! $hasAnyIdentityPreview && ! $hasAnyLicenseDoc)
                            <div style="color:#9ca3af">لا توجد مستندات مرفوعة.</div>
                        @endif
                    </div>
                </div>

                <!-- Preview Modal -->
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
                            if(meta){ var lines = [];
                                if(meta.filename) lines.push('<strong>الملف:</strong> '+meta.filename);
                                if(meta.created_at) lines.push('<strong>تاريخ الرفع:</strong> '+meta.created_at);
                                m.innerHTML = lines.join(' &nbsp; | &nbsp; ');
                            }
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
            </div>
        </form>
        {{-- Server-side Pay button (render like investments) --}}
        @php
            // Only show payment button when license is not active/completed/approved
            // AND when admin has sent at least one payment reminder for this license.
            $canShowPayment = !in_array(strtolower($license->status ?? ''), ['active','completed','approved']) && (isset($reminders) && $reminders->isNotEmpty());

            // Extract the most recent reminder amount (if any) so the client can use it
            $firstReminderAmount = null;
            if ($canShowPayment) {
                foreach($reminders->sortByDesc('created_at') as $r) {
                    // prefer stored amount column
                    if (isset($r->amount) && $r->amount !== null && $r->amount !== ''){
                        $firstReminderAmount = (float) $r->amount;
                        break;
                    }

                    $noteText = trim((string) ($r->note ?? ''));
                    if ($noteText === '') continue;
                    if (mb_substr($noteText, 0, 11) === '[HIGHLIGHT]'){
                        $noteText = ltrim(mb_substr($noteText, 11));
                    }
                    if (mb_substr($noteText, 0, 8) === '[AMOUNT:'){
                        $close = mb_strpos($noteText, ']');
                        if ($close !== false){
                            $amountDue = trim(mb_substr($noteText, 8, $close - 8));
                            if ($amountDue !== ''){
                                // Normalize and cast to float
                                $normalized = str_replace([',',' '], ['', ''], $amountDue);
                                $firstReminderAmount = (float) $normalized;
                                break;
                            }
                        }
                    }
                }
            }
        @endphp
        @if($canShowPayment)
            <div style="margin-top:12px;">
                <button id="show-payment-btn" class="btn-primary">ادفع الآن</button>
            </div>
        @endif
    </div>
    
    {{-- Reminders (payment reminders sent by admin) --}}
    <div class="glass-card" style="padding:12px;margin-top:12px;">
        <h3 style="color:#fff;margin-bottom:8px;">التذكيرات</h3>
        <style>
            .reminder-list { display:grid; gap:8px; max-height:320px; overflow-y:auto; padding-right:6px; }
            .reminder-card { position:relative; padding:10px; border-radius:8px; background:rgba(255,255,255,0.02); display:flex; justify-content:space-between; align-items:flex-start; gap:12px; }
            .reminder--rejected { color:#ff6b6b; background: rgba(255,107,107,0.04); border-left:4px solid rgba(255,107,107,0.18); }
            .reminder--meta { color:#9ca3af; font-size:12px; }
            .reminder--note { margin-top:6px; }
        </style>
        @if(isset($reminders) && $reminders->isNotEmpty())
            <div class="reminder-list">
                @foreach($reminders->sortByDesc('created_at') as $rem)
                    @php
                        $isRejectReminder = false;
                        $isHighlight = false;
                        // prefer stored amount column first
                        $amountDue = isset($rem->amount) && $rem->amount !== null && $rem->amount !== '' ? $rem->amount : null;
                        $noteText = trim((string) ($rem->note ?? ''));
                        if ($noteText !== ''){
                            if (mb_substr($noteText, 0, 11) === '[HIGHLIGHT]'){
                                $isHighlight = true;
                                $noteText = ltrim(mb_substr($noteText, 11));
                            }
                            if ($amountDue === null && mb_substr($noteText, 0, 8) === '[AMOUNT:'){
                                $close = mb_strpos($noteText, ']');
                                if ($close !== false){
                                    $amountDue = trim(mb_substr($noteText, 8, $close - 8));
                                    $noteText = ltrim(mb_substr($noteText, $close + 1));
                                }
                            }
                            $lower = mb_strtolower($noteText);
                            if (mb_strpos($lower, 'رفض') !== false || mb_strpos($lower, 'مرفوض') !== false) {
                                $isRejectReminder = true;
                            }
                        }
                    @endphp
                    <div class="reminder-card {{ $isRejectReminder ? 'reminder--rejected' : '' }} {{ $isHighlight ? 'reminder--highlight' : '' }}">
                        @if($loop->first)
                            <div style="position:absolute;right:-6px;top:-6px;overflow:visible;">
                                <div style="background:#16a34a;color:#fff;padding:6px 10px;border-radius:6px;transform:rotate(12deg);box-shadow:0 6px 18px rgba(2,6,23,0.6);font-weight:700;font-size:12px;">جديد</div>
                            </div>
                        @endif
                        <div style="flex:1;">
                            <div style="color:#9ca3af;font-size:12px;">{{ $rem->admin_name ?? 'الإدارة' }} · <span style="color:#94a3b8;font-size:12px;">{{ \Carbon\Carbon::parse($rem->created_at)->diffForHumans() }}</span></div>
                                @if(isset($amountDue) && $amountDue !== null && $amountDue !== '')
                                    <div style="font-weight:900;color:#fbbf24;margin-bottom:8px;font-size:18px;">المبلغ المستحق: {{ number_format((float)$amountDue, 2) }} ر.س</div>
                                @endif
                            <div class="reminder--note">{!! nl2br(e($noteText)) !!}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div style="color:#cbd5e1">لا توجد تذكيرات دفع لهذا الطلب.</div>
        @endif
    </div>
</div>
@endsection
 
@push('scripts')
        <script>
    $(function(){
        // expose reminder/license amounts to this inline script
        var reminderAmount = @json($firstReminderAmount);
        var licenseAmount = @json((float)($license->amount ?? 0));
        var licenseExpectedReturn = @json((float)($license->expected_return ?? 0));
        var currentPayAmount = null;

        function formatCurrency(n){
            n = Number(n) || 0;
            return n.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' ر.س';
        }
        // toast helper (small, local to this view)
        if (!document.getElementById('app-toast')) {
            $('body').append('<div id="app-toast" style="position:fixed;top:20px;left:50%;transform:translateX(-50%);z-index:10000;display:flex;flex-direction:column;gap:8px;align-items:center;pointer-events:none;"></div>');
        }
        function showToast(type, msg, timeout){
            timeout = timeout || 5000;
            var bg = type === 'success' ? '#16a34a' : (type === 'error' ? '#dc2626' : '#0ea5e9');
            var $t = $('<div/>').css({background:bg,color:'#fff',padding:'10px 14px',borderRadius:'8px',boxShadow:'0 6px 18px rgba(2,6,23,0.6)',maxWidth:'90%',pointerEvents:'auto'}).text(msg);
            $('#app-toast').append($t);
            setTimeout(function(){ $t.fadeOut(300,function(){ $t.remove(); }); }, timeout);
        }
        // Show payment step similar to investment-detail — button is rendered server-side; just bind behavior
        var licStatus = '{{ strtolower($license->status ?? '') }}';

        // Toggle payment UI (inline, same as investment detail)
        $(document).on('click','#show-payment-btn', function(e){
            e.preventDefault();
            $('#step-payment').show();

            // Determine the amount to pay: prefer reminderAmount if available
            currentPayAmount = (typeof reminderAmount === 'number' && reminderAmount > 0) ? reminderAmount : (Number(licenseAmount) || 0);

            // Update displayed amount and expected return based on the chosen payment amount
            $('#pay-amount').text(formatCurrency(currentPayAmount));
            var expected = 0;
            if (Number(licenseAmount) > 0) {
                var rate = Number(licenseExpectedReturn) / Number(licenseAmount);
                expected = rate * currentPayAmount;
            } else {
                expected = Number(licenseExpectedReturn) || 0;
            }
            $('#pay-return').text(formatCurrency(expected));

            // auto-click bank method for now
            $('.pm-btn[data-method="bank"]').trigger('click');
            // generate initial ref only if not set by settings
            function genRef(){ return 'TX-'+Math.random().toString(36).substr(2,9).toUpperCase(); }
            if (($('#bank-ref').text()||'').trim()==='' || ($('#bank-ref').text()||'').trim()==='INV-XXXXXX') {
                $('#bank-ref').text(genRef());
            }
            // scroll to payment
            $('html,body').animate({ scrollTop: $('#step-payment').offset().top - 80 }, 300);
        });

        // Show bank block
        $(document).on('click', '.pm-btn', function(){
            var m = $(this).data('method');
            if(m === 'bank') $('#pm-bank').show();
        });

        // copy buttons
        $(document).on('click', '.copy-btn', function(){
            var tgt = $($(this).data('copy-target'));
            if(!tgt || !tgt.length) return;
            var txt = tgt.text();
            // copy raw IBAN (no spaces)
            var id = (tgt.attr('id')||'').toLowerCase();
            if(id.indexOf('iban') !== -1){ txt = txt.replace(/\s+/g,''); }
            navigator.clipboard && navigator.clipboard.writeText(txt).then(function(){}, function(){ showToast('error', 'نسخ فشل'); });
        });

        // receipt size check (10MB)
        $(document).on('change', '#bank-receipt', function(){
            var f = this.files && this.files[0];
            if(!f){ $('#bank-receipt-name').text(''); return; }
            if(f.size > 10240*1024){ $('#bank-error').show().text('حجم الإيصال يجب أن لا يتجاوز 10 ميجابايت'); return; }
            $('#bank-error').hide(); $('#bank-receipt-name').text(f.name);
        });

        // cancel/back buttons
        $(document).on('click', '#payment-back, #pay-later', function(e){ e.preventDefault(); $('#step-payment').hide(); });

        // Submit payment (adapted to license)
        $(document).on('click', '#pay-now', function(e){
            e.preventDefault();
            var fd = new FormData();
            fd.append('method', 'bank');
            fd.append('license_id', '{{ $license->id }}');
            // use the chosen currentPayAmount (reminder-preferred) if set, otherwise fallback to license amount
            try{
                var sendAmount = (typeof currentPayAmount === 'number' && currentPayAmount > 0) ? currentPayAmount : (Number({{ (float)($license->amount ?? 0) }})||0);
            }catch(e){ var sendAmount = Number({{ (float)($license->amount ?? 0) }})||0; }
            fd.append('amount', sendAmount);
            fd.append('sender_name', $('#bank-sender-name').val()||'');
            fd.append('transfer_at', $('#bank-date').val()||'');
            fd.append('note', $('#bank-note').val()||'');
            fd.append('reference', $('#bank-ref').text()||('TX-'+Math.random().toString(36).substr(2,9).toUpperCase()));
            var fileEl = $('#bank-receipt')[0];
            if(fileEl && fileEl.files && fileEl.files[0]) fd.append('receipt', fileEl.files[0]);

            $('#pay-now').prop('disabled', true).text('جاري الارسال...');

            $.ajax({
                url: '/license/pay',
                method: 'POST',
                data: fd,
                processData: false,
                contentType: false,
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(res){
                    $('#pay-now').prop('disabled', false).text('تاكيد التحويل');
                    if(res && res.ok){
                        showToast('success', res.message || 'تم ارسال بيانات الدفع');
                        // clear inputs and UI immediately so user sees form reset
                        try {
                            $('#bank-sender-name').val('');
                            $('#bank-date').val('');
                            $('#bank-note').val('');
                            $('#bank-receipt').val('');
                            $('#bank-receipt-name').text('');
                            $('#bank-error').hide();
                            $('#pm-bank').hide();
                            $('#step-payment').hide();
                            $('#bank-ref').text('INV-XXXXXX');
                        } catch(e){}
                        // reload after a short delay to refresh server-side state
                        setTimeout(function(){ window.location.reload(); }, 900);
                    } else {
                        showToast('error', res && res.message ? res.message : 'حدث خطأ');
                    }
                },
                error: function(xhr){
                    $('#pay-now').prop('disabled', false).text('تاكيد التحويل');
                    var msg = 'فشل الاتصال بالخادم';
                    try { var j = xhr.responseJSON; if(j && j.message) msg = j.message; } catch(e){}
                    showToast('error', msg);
                }
            });
        });
    });
</script>
@endpush
