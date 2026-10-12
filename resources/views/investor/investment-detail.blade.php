@extends('layouts.app')

@section('content')
<div class="inv-detail" style="padding:24px;max-width:900px;margin:0 auto;">
    <style>
        /* Responsive tweaks scoped to this page only */
        @media (max-width: 640px) {
            .inv-detail { padding: 16px !important; }
            .inv-detail h2 { font-size: 18px !important; }
            .inv-detail h3 { font-size: 16px !important; }
            .inv-detail .glass-card { padding: 12px !important; }
            .inv-detail .two-col { grid-template-columns: 1fr !important; gap: 10px !important; }
            .inv-detail .actions { flex-direction: column !important; align-items: stretch !important; }
            .inv-detail .actions form { width: 100% !important; }
            .inv-detail .actions input[type="number"] { width: 100% !important; }
            .inv-detail #payment-methods .pm-btn { width: 100% !important; }
            .inv-detail .bank-grid { grid-template-columns: 1fr !important; }
            .inv-detail .pay-actions { flex-direction: column !important; }
            .inv-detail .pay-actions .btn-secondary,
            .inv-detail .pay-actions .btn-primary { width: 100% !important; }
            .inv-detail .reminder-list { max-height: 240px !important; }
        }
    </style>
    <a href="{{ route('investor.dashboard') }}" class="btn-secondary" style="border-color:#22d3ee;color:#22d3ee;margin-bottom:8px;">← العودة للوحة المستثمر</a>

    <div class="glass-card" style="padding:20px;margin-top:12px;">
        <h2 style="color:#fff;margin-bottom:6px;">تفاصيل الاستثمار</h2>
        <div class="two-col" style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <div>
                <div style="color:#9ca3af;font-size:12px">رقم الاستثمار</div>
                <div style="color:#fff;font-weight:700">{{ strtoupper($inv->id) }}</div>

                <div style="margin-top:12px;color:#9ca3af;font-size:12px">المبلغ</div>
                <div style="color:#06b6d4;font-weight:700">{{ number_format($inv->amount) }} ر.س</div>

                <div style="margin-top:12px;color:#9ca3af;font-size:12px">نسبة الربح</div>
                <div style="color:#fbbf24;font-weight:700">{{ $inv->profit_rate }}%</div>

                <div style="margin-top:12px;color:#9ca3af;font-size:12px">مدة الاستثمار</div>
                <div style="color:#fff;font-weight:700">{{ $inv->duration_months }} شهر</div>
            </div>
            <div>
                <div style="color:#9ca3af;font-size:12px">الحالة</div>
                @php
                    $statusMap = [
                        'approved' => 'معتمد',
                        'active' => 'نشط',
                        'completed' => 'مكتمل',
                        'pending' => 'قيد المراجعة',
                        'rejected' => 'مرفوض',
                    ];
                    $statusKey = strtolower($inv->status ?? '');
                    $statusLabel = $statusMap[$statusKey] ?? ($inv->status ?? '—');
                @endphp
                @php
                    // Color map for statuses (show approved in green)
                    $colorMap = [
                        'approved'  => '#16a34a', // green
                        'active'    => '#f97316', // orange
                        'completed' => '#2563eb', // blue
                        'pending'   => '#f59e0b', // amber
                        'rejected'  => '#ef4444', // red
                    ];
                    $statusColor = isset($statusKey) && isset($colorMap[$statusKey]) ? $colorMap[$statusKey] : '#ffffff';
                @endphp
                <div style="color: {{ $statusColor }}; font-weight:700">{{ $statusLabel }}</div>

                <div style="margin-top:12px;color:#9ca3af;font-size:12px">تاريخ الإنشاء</div>
                <div style="color:#94a3b8">{{ \Carbon\Carbon::parse($inv->created_at)->toDayDateTimeString() }}</div>
                <div style="color:#9ca3af;font-size:12px;margin-top:2px">{{ \Carbon\Carbon::parse($inv->created_at)->locale('ar')->translatedFormat('D, M j, Y g:i A') }}</div>
                @php
                    // Compute and show the investor-facing next payout note.
                    try {
                        $approvedAt = isset($inv->approved_at) && $inv->approved_at ? \Carbon\Carbon::parse($inv->approved_at) : null;
                        if ($approvedAt && \Illuminate\Support\Facades\Schema::hasTable('investor_earnings')) {
                            $earningsCount = \Illuminate\Support\Facades\DB::table('investor_earnings')->where('investment_id', $inv->id)->count();
                            $nextMonth = $earningsCount + 1; // next month number
                            $nextPayoutAt = $approvedAt->copy()->addMonths($nextMonth);
                            // prefer ISO date YYYY-MM-DD for clarity
                            $noteDate = $nextPayoutAt->format('Y-m-d');
                            $nextPayoutNote = 'سيتم إيداع مبلغك بتاريخ ' . $noteDate;
                        } elseif ($approvedAt) {
                            // earnings table missing or not yet migrated; still show first-month note
                            $nextPayoutAt = $approvedAt->copy()->addMonth();
                            $noteDate = $nextPayoutAt->format('Y-m-d');
                            $nextPayoutNote = 'سيتم إيداع مبلغك بتاريخ ' . $noteDate;
                        } else {
                            $nextPayoutNote = 'سيتم إيداع أرباحك بعد اعتماد الاستثمار';
                        }
                    } catch (\Exception $e) {
                        $nextPayoutNote = 'سيتم إيداع أرباحك بعد اعتماد الاستثمار';
                    }
                @endphp
                <div style="margin-top:8px;display:flex;flex-direction:column;gap:4px;">
                    <div style="color:#9ca3af;font-size:12px;margin-bottom:2px;">عائد الأرباح الشهرية</div>
                    <div style="font-size:13px;color:#d8cdb6;font-weight:600;">{{ $nextPayoutNote }}</div>
                    <div style="font-size:12px;color:#bfae95;">سيظهر الإيداع في قسم "عائد الأرباح الشهرية" داخل حسابك.</div>
                </div>

                <div style="margin-top:12px;color:#9ca3af;font-size:12px">ملاحظات</div>
                <div style="color:#fff">{{ $inv->notes }}</div>
            </div>
        </div>

        <div class="actions" style="margin-top:18px;display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
            {{-- Withdrawal form removed from this page (handled from dashboard overview). --}}
        </div>
            {{-- Withdrawal form removed from this page; use the "أداء العوائد" card on the dashboard to request withdrawals. --}}
        </div>
        
        {{-- Payment step: show Pay button only when not active/completed/approved AND a reminder exists. --}}
        @php
            $canShowPayment = !in_array(strtolower($inv->status ?? ''), ['active','completed','approved']) && (isset($reminders) && $reminders->isNotEmpty());
        @endphp
        @if($canShowPayment)
        <div style="margin-top:12px;">
            <button id="show-payment-btn" class="btn-primary">ادفع الآن</button>
        </div>
        @endif
    </div>

    <div class="glass-card" style="padding:12px;margin-top:12px;">
        <h3 style="color:#fff;margin-bottom:8px;">سجل المعاملات</h3>
        @if($transactions->isEmpty())
            <div style="color:#cbd5e1">لا توجد معاملات لهذه الاستثمار.</div>
        @else
            <div style="display:grid;gap:8px;">
                @foreach($transactions as $t)
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:8px;border-radius:8px;background:rgba(255,255,255,0.02)">
                        <div>
                            <div style="color:#9ca3af;font-size:12px">{{ $t->note }}</div>
                            @php
                                $tType = strtolower($t->type ?? '');
                                $tLabel = $tType === 'credit' ? 'إيداع' : ($tType === 'debit' ? 'سحب' : ($t->type ?? '—'));
                            @endphp
                            <div style="color:#fff;font-weight:700">{{ ($t->type=='credit'?'+':'-') . number_format($t->amount) }} ر.س <span style="font-size:12px;color:#94a3b8;margin-right:8px;">{{ $tLabel }}</span></div>
                        </div>
                        <div style="color:#94a3b8;font-size:12px">{{ \Carbon\Carbon::parse($t->date)->diffForHumans() }}</div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

        {{-- Reminders (payment reminders sent by admin) --}}
        <div class="glass-card" style="padding:12px;margin-top:12px;">
            <h3 style="color:#fff;margin-bottom:8px;">التذكيرات</h3>
            <style>
                /* reminder styles for rejected notices */
                .reminder-list { display:grid; gap:8px; max-height:320px; overflow-y:auto; padding-right:6px; }
                .reminder-card { position:relative; padding:10px; border-radius:8px; background:rgba(255,255,255,0.02); display:flex; justify-content:space-between; align-items:flex-start; gap:12px; }
                .reminder--rejected { color:#ff6b6b; background: rgba(255,107,107,0.04); border-left:4px solid rgba(255,107,107,0.18); }
                .reminder--meta { color:#9ca3af; font-size:12px; }
                .reminder--note { margin-top:6px; }
            </style>
            @if(isset($reminders) && $reminders->isNotEmpty())
                <div class="reminder-list">
                    {{-- Show newest first --}}
                    @foreach($reminders->sortByDesc('created_at') as $rem)
                        @php
                            $isRejectReminder = false;
                            $isHighlight = false;
                            // prefer explicit stored amount if available, otherwise try to parse inline marker
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
                            {{-- New ribbon for the most recent reminder --}}
                            @if($loop->first)
                                <div style="position:absolute;right:-6px;top:-6px;overflow:visible;">
                                    <div style="background:#16a34a;color:#fff;padding:6px 10px;border-radius:6px;transform:rotate(12deg);box-shadow:0 6px 18px rgba(2,6,23,0.6);font-weight:700;font-size:12px;">جديد</div>
                                </div>
                            @endif

                            <div style="flex:1;">
                                <div style="color:#9ca3af;font-size:12px;">{{ $rem->admin_name ?? 'الإدارة' }} · <span style="color:#94a3b8;font-size:12px;">{{ \Carbon\Carbon::parse($rem->created_at)->diffForHumans() }}</span></div>
                                @if(isset($amountDue) && $amountDue !== null && $amountDue !== '')
                                    <div style="display:flex;gap:8px;align-items:center;margin-bottom:8px;">
                                        <div style="font-weight:900;color:#fbbf24;font-size:18px;">المبلغ المستحق: {{ number_format((float)$amountDue, 2) }} ر.س</div>
                                        <div>
                                            <button type="button" class="btn-primary reminder-pay-btn" data-amount="{{ number_format((float)$amountDue, 2, '.', '') }}" style="white-space:nowrap;">ادفع الآن</button>
                                        </div>
                                    </div>
                                @endif
                                <div class="reminder--note">{!! nl2br(e($noteText)) !!}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="color:#cbd5e1">لا توجد تذكيرات دفع لهذا الاستثمار.</div>
            @endif
        </div>

        {{-- Payment form step (hidden by default) --}}
        <div id="step-payment" style="display:none;margin-top:12px;">
            <h2 style="font-size:22px;color:#fff;margin:0 0 16px;">الدفع</h2>
            <div class="glass-card" style="padding:16px;margin-bottom:16px;">
                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <div style="color:#9ca3af;font-size:12px;">المبلغ المستحق</div>
                    <div id="pay-amount" style="color:#06b6d4;font-weight:700;">{{ number_format($inv->amount) }} ر.س</div>
                </div>
                {{-- hidden numeric amount used by JS when submitting payment (can be updated by reminder buttons) --}}
                <input type="hidden" id="pay-amount-input" value="{{ number_format($inv->amount,2,'.','') }}" />
                <div style="display:flex;justify-content:space-between;align-items:center;margin-top:6px;">
                    <div style="color:#9ca3af;font-size:12px;">العائد المتوقع</div>
                    <div id="pay-return" style="color:#22c55e;font-weight:700;">{{ number_format($inv->expected_return ?? 0) }} ر.س</div>
                </div>
            </div>

            <div id="payment-methods" style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:12px;">
                <button class="pm-btn" data-method="bank" style="flex:1;border:1px solid #4b5563;background:#111827;color:#fff;padding:10px;border-radius:8px;">تحويل بنكي</button>
            </div>

             <div id="pm-bank" class="glass-card" style="padding:12px;display:none;color:#cbd5e1;font-size:14px;margin-top:12px;">
                <div style="display:grid;gap:10px;">
                    <div style="display:grid;gap:8px;">
                        <div style="font-weight:700;color:#fff;">تفاصيل التحويل البنكي</div>
                        <div class="bank-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
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
                                    <div id="bank-ref" style="color:#fbbf24;font-weight:700;">INV-XXXXXX</div>
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

            <div class="pay-actions" style="display:flex;gap:10px;margin-top:16px;">
                <button id="payment-back" class="btn-secondary" style="border-color:#64748b;color:#cbd5e1;">رجوع</button>
                <button id="pay-later" class="btn-secondary" style="border-color:#94a3b8;color:#cbd5e1;">تحويل لاحقاً</button>
                <button id="pay-now" class="btn-primary" style="flex:1;">تاكيد التحويل </button>
            </div>
        </div>
</div>
@endsection

@push('scripts')
<script>
    (function(){
        // عرض رسائل الحالة إن وجدت
        try {
            const params = new URLSearchParams(window.location.search);
            // يمكن لاحقًا استخدام فلاش سيشن؛ هنا placeholder بسيط إذا لزم
        } catch(e) {}
    })();
</script>
@endpush

@push('scripts')
<script>
    // toast container
    $('body').append('<div id="app-toast" style="position:fixed;top:20px;left:50%;transform:translateX(-50%);z-index:10000;display:flex;flex-direction:column;gap:8px;align-items:center;pointer-events:none;"></div>');
    function showToast(type, msg, timeout){
        timeout = timeout || 7000;
        var bg = type === 'success' ? '#16a34a' : (type === 'error' ? '#dc2626' : '#0ea5e9');
        var $t = $('<div/>').css({background:bg,color:'#fff',padding:'10px 14px',borderRadius:'8px',boxShadow:'0 6px 18px rgba(2,6,23,0.6)',maxWidth:'90%',pointerEvents:'auto'}).text(msg);
        $('#app-toast').append($t);
        setTimeout(function(){ $t.fadeOut(300,function(){ $t.remove(); }); }, timeout);
    }
</script>
@endpush

@push('scripts')
<script>
    $(function(){
        // Toggle payment UI
        $('#show-payment-btn').on('click', function(e){
            e.preventDefault();
            $('#step-payment').show();
            // auto-click bank method for now
            $('.pm-btn[data-method="bank"]').trigger('click');
            // generate initial ref
            function genRef(){ return 'TX-'+Math.random().toString(36).substr(2,9).toUpperCase(); }
            // Only generate a reference if not already provided by settings
            if (($('#bank-ref').text()||'').trim()==='' || ($('#bank-ref').text()||'').trim()==='INV-XXXXXX') {
                $('#bank-ref').text(genRef());
            }
            // scroll to payment
            $('html,body').animate({ scrollTop: $('#step-payment').offset().top - 80 }, 300);
        });

        // Load bank settings and apply visibility for sender/date/receipt fields
        // helper: format IBAN visually in 4-char groups
        function formatIBAN(iban){
            if(!iban) return '—';
            try { return String(iban).replace(/[^A-Za-z0-9]/g,'').toUpperCase().match(/.{1,4}/g).join(' '); } catch(e){ return String(iban); }
        }

        $.getJSON('{{ route('api.bank.settings') }}').done(function(resp){
            try {
                window.bankSettings = resp || {};
                var s = window.bankSettings;
                // Populate bank details from DB
                if (resp) {
                    if (resp.account_name) { $('#bank-account-name').text(resp.account_name); }
                    if (resp.bank_name) { $('#bank-bank-name').text(resp.bank_name); }
                    if (resp.account_number) { $('#bank-account-number').text(resp.account_number); }
                    if (resp.iban) { $('#bank-iban').text(formatIBAN(resp.iban)); }
                    if (resp.swift) { $('#bank-swift').text(resp.swift); }
                }
                if (resp && resp.reference_code) { $('#bank-ref').text(resp.reference_code); }
                if(typeof s.ask_sender !== 'undefined'){
                    if(s.ask_sender) { $('#bank-sender-name').closest('div').show(); } else { $('#bank-sender-name').closest('div').hide().find('input').val(''); }
                }
                if(typeof s.ask_datetime !== 'undefined'){
                    if(s.ask_datetime) { $('#bank-date').closest('div').show(); } else { $('#bank-date').closest('div').hide().find('input').val(''); }
                }
                if(typeof s.require_receipt !== 'undefined'){
                    if(s.require_receipt) { $('#bank-receipt').closest('div').show(); } else { $('#bank-receipt').closest('div').hide().find('input').val(''); $('#bank-receipt-name').text(''); }
                }

                // populate instructions list according to admin configuration
                var instr = resp.instructions || '';
                var $ul = $('#bank-instructions-ul');
                if($ul.length){
                    $ul.empty();
                    // Show only when admin provided a non-empty instructions string
                    if (typeof resp.instructions !== 'undefined' && resp.instructions !== null && String(resp.instructions).trim() !== ''){
                        var lines = String(resp.instructions).split(/\r?\n/).map(function(s){ return s.trim(); }).filter(function(s){ return s.length>0; });
                        if(lines.length){ lines.forEach(function(l){ $ul.append($('<li>').html(l)); }); }
                        else { $ul.append($('<li>').html(String(resp.instructions))); }
                        $('#bank-instructions-block').show();
                    } else {
                        $('#bank-instructions-block').hide();
                    }
                }
            } catch(e) { window.bankSettings = window.bankSettings || {}; }
        }).fail(function(){ window.bankSettings = window.bankSettings || {}; });

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
            // For IBAN, copy a raw value without spaces
            var id = (tgt.attr('id')||'').toLowerCase();
            if(id.indexOf('iban') !== -1){ txt = txt.replace(/\s+/g,''); }
        navigator.clipboard && navigator.clipboard.writeText(txt).then(function(){}, function(){ showToast('error', 'نسخ فشل'); });
        });

        // receipt size check (10MB)
        $('#bank-receipt').on('change', function(){
            var f = this.files && this.files[0];
            if(!f){ $('#bank-receipt-name').text(''); return; }
            if(f.size > 10240*1024){ $('#bank-error').show().text('حجم الإيصال يجب أن لا يتجاوز 10 ميجابايت'); return; }
            $('#bank-error').hide(); $('#bank-receipt-name').text(f.name);
        });

        // cancel/back buttons
        $('#payment-back, #pay-later').on('click', function(e){ e.preventDefault(); $('#step-payment').hide(); });

        // Submit payment
        $('#pay-now').on('click', function(e){
            e.preventDefault();
            // Validate only fields that admin requires (fall back to requiring them by default)
            var s = window.bankSettings || {};
            var requireSender = (typeof s.ask_sender === 'undefined') ? true : !!s.ask_sender;
            var requireDate = (typeof s.ask_datetime === 'undefined') ? true : !!s.ask_datetime;
            var requireReceipt = (typeof s.require_receipt === 'undefined') ? true : !!s.require_receipt;

            var sender = ($('#bank-sender-name').val()||'').trim();
            var date = ($('#bank-date').val()||'').trim();
            var fileEl = $('#bank-receipt')[0];
            var file = fileEl && fileEl.files && fileEl.files[0];

            if(requireSender && !sender){ showToast('error', 'يرجى إدخال اسم المحوِّل'); return; }
            if(requireDate && !date){ showToast('error', 'يرجى إدخال تاريخ ووقت التحويل'); return; }
            if(requireReceipt && !file){ showToast('error', 'يرجى إرفاق إيصال التحويل'); return; }

            var fd = new FormData();
            fd.append('method', 'bank');
            fd.append('investment_id', '{{ $inv->id }}');
            // read amount from the hidden numeric input (may be set by a reminder button)
            fd.append('amount', ($('#pay-amount-input').val()||'{{ $inv->amount }}'));
            fd.append('sender_name', sender || '');
            fd.append('transfer_at', date || '');
            fd.append('note', $('#bank-note').val()||'');
            fd.append('reference', $('#bank-ref').text()||('TX-'+Math.random().toString(36).substr(2,9).toUpperCase()));
            if(file) fd.append('receipt', file);

            $('#pay-now').prop('disabled', true).text('جاري الارسال...');

            $.ajax({
                url: '/investment/pay',
                method: 'POST',
                data: fd,
                processData: false,
                contentType: false,
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(res){
                    $('#pay-now').prop('disabled', false).text('تاكيد التحويل');
                    if(res && res.ok){
                                showToast('success', res.message || 'تم ارسال بيانات الدفع');
                        // reload to show updated status
                        window.location.reload();
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
        // reminder pay button: prefill amount and open payment step
        $(document).on('click', '.reminder-pay-btn', function(e){
            e.preventDefault();
            var amt = $(this).data('amount');
            if(!amt) return;
            // set numeric hidden input and visible label
            $('#pay-amount-input').val(amt);
            $('#pay-amount').text(amt + ' ر.س');
            // show payment step and auto-select bank
            $('#step-payment').show();
            $('.pm-btn[data-method="bank"]').trigger('click');
            // scroll into view
            $('html,body').animate({ scrollTop: $('#step-payment').offset().top - 80 }, 300);
        });
    });
</script>
@endpush
