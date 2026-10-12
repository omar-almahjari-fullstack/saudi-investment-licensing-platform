@extends('layouts.app')

@section('content')
<style>
    /* Responsive tweaks for investor dashboard */
    @media (max-width: 1024px) {
        #dashboard-grid { grid-template-columns: 1fr !important; }
    }
    @media (max-width: 900px) {
        #summary-grid { grid-template-columns: repeat(2, 1fr) !important; }
    }
    @media (max-width: 768px) {
        #summary-grid { grid-template-columns: 1fr !important; }
        .sparkline { width: 100% !important; height: 60px !important; }
        .tab-btn { flex: 1 1 auto !important; min-width: 120px; }
        .investments-list .inv-card { grid-template-columns: 1fr !important; }
        h1.glow-text { font-size: 24px !important; }
    }
    @media (max-width: 600px) {
        .btn-primary, .btn-secondary { width: 100% !important; text-align: center !important; }
    }
    /* Earnings tab responsiveness */
    @media (max-width: 768px) {
        #earnings-summary { flex-direction: column !important; align-items: flex-start !important; gap: 6px; }
        #earnings-chart-wrapper .chart-area { height: 220px !important; }
        /* make meta grid two columns on tablets */
        .earnings-meta { grid-template-columns: repeat(2, 1fr) !important; }
    }
    @media (max-width: 640px) {
        /* stack earnings highlights */
        #earnings-highlights { flex-direction: column !important; align-items: stretch !important; }
        #earnings-highlights > div { width: 100% !important; }
        /* turn earnings tables into mobile-friendly cards */
        table.earnings-table thead { display: none !important; }
        table.earnings-table, table.earnings-table tbody, table.earnings-table tr, table.earnings-table td { display: block !important; width: 100% !important; }
        table.earnings-table tr { border: 1px solid rgba(255,255,255,0.06); border-radius: 8px; margin-bottom: 8px; background: rgba(255,255,255,0.01); }
        table.earnings-table td { padding: 10px !important; display: flex !important; justify-content: space-between; align-items: center; gap: 8px; }
        table.earnings-table td::before { content: attr(data-label); color: #94a3b8; font-size: 12px; }
        /* override inline min-width on tables to avoid horizontal scroll */
        table.earnings-table { min-width: auto !important; }
        /* hide the desktop-only total label cell in mobile */
        table.earnings-table td.total-label { display: none !important; }
        /* meta grid to single column */
        .earnings-meta { grid-template-columns: 1fr !important; }
    }
    /* Ensure earnings + recent-transactions split stacks on narrow screens */
    @media (max-width: 768px) {
        .overview-sparkline-grid { grid-template-columns: 1fr !important; }
        .overview-sparkline-grid .glass-card { width: 100% !important; }
        .overview-sparkline-grid .glass-card + .glass-card { margin-top: 8px; }
    }
    /* Earnings table visual enhancements */
    table.earnings-table tbody tr { transition: background 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease; }
    table.earnings-table tbody tr:hover { background: rgba(255,255,255,0.03) !important; box-shadow: 0 2px 10px rgba(0,0,0,0.15); }
    .earnings-pill { display: inline-block; padding: 4px 10px; border-radius: 999px; font-weight: 700; font-size: 13px; }
    .earnings-pill.pill-time { background: rgba(59,130,246,0.12); color: #93c5fd; border: 1px solid rgba(59,130,246,0.28); }
    .earnings-pill.pill-amount { background: rgba(16,185,129,0.12); color: #86efac; border: 1px solid rgba(16,185,129,0.28); }
    /* Earnings tab scroll area to avoid long page */
    #tab-earnings > .glass-card { max-height: 75vh; overflow: auto; overscroll-behavior: contain; }
    @media (max-width: 768px) { #tab-earnings > .glass-card { max-height: 70vh; } }
    @media (max-height: 740px) { #tab-earnings > .glass-card { max-height: 65vh; } }
    /* optional: subtle scrollbar styling (webkit) */
    #tab-earnings > .glass-card::-webkit-scrollbar { width: 8px; height: 8px; }
    #tab-earnings > .glass-card::-webkit-scrollbar-thumb { background: rgba(148,163,184,0.35); border-radius: 8px; }

    /* Strong mobile fixes to avoid horizontal overflow and stack complex layouts */
    .page-wrapper { box-sizing: border-box; }
    .glass-card { box-sizing: border-box; width: 100%; }

    @media (max-width: 480px) {
        .page-wrapper { padding: 12px !important; }
        /* make tab buttons wrap and shrink */
        .tab-btn { min-width: 0 !important; padding: 6px 8px !important; font-size: 13px !important; white-space: normal !important; }
        /* ensure dashboard grid stacks */
        #dashboard-grid { grid-template-columns: 1fr !important; }
        /* investments cards stack into single column */
        .investments-list { max-height: 60vh !important; padding-right: 4px !important; }
        .investments-list .inv-card { grid-template-columns: 1fr !important; gap:8px !important; }
        /* earnings card: stack metadata and table vertically */
        .earnings-card { display: block !important; padding: 10px !important; }
        .earnings-meta { grid-template-columns: 1fr !important; }
        /* smaller chart height for phones */
        #earnings-chart-wrapper .chart-area { height: 200px !important; padding: 8px !important; }
        canvas#earningsChart { max-width: 100% !important; height: 100% !important; display: block !important; }
        /* table -> stacked card rows already handled for <=640px, tweak fonts/padding */
        table.earnings-table td { padding: 8px !important; font-size: 13px !important; }
        table.earnings-table tr { margin-bottom: 8px !important; }
        /* prevent long text or badges from overflowing */
        .earnings-pill { max-width: 100%; overflow: hidden; text-overflow: ellipsis; }
        /* reduce header sizes to avoid pushing layout */
        h1.glow-text { font-size: 20px !important; }
    }

    /* defensive: if any element still causes page overflow, hide overflow-x at wrapper level */
    @media (max-width: 640px) {
        .page-wrapper { overflow-x: hidden !important; }
    }
</style>
<div class="container">
    <div class="mobile-breadcrumb py-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">الرئيسية</a></li>
                <li class="breadcrumb-item active" aria-current="page">لوحة تحكم المستثمر</li>
            </ol>
        </nav>
    </div>
</div>

<div class="page-wrapper" style="padding:24px;max-width:1200px;margin:0 auto;">
    <header class="text_center_title" style="margin-bottom:18px;">
        <h1 style="font-size:32px;color:#fff;margin:6px 0;" class="glow-text">لوحة تحكم المستثمر</h1>
        <p style="color:#cbd5e1;">مرحباً، {{ $user->full_name ?? 'مستثمر' }}</p>
    </header>

    <!-- Summary -->
    <div id="summary-grid" style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:16px;">
        <div class="glass-card" style="text-align:center;padding:16px;">
            <div style="font-size:28px;color:#22d3ee;font-weight:800;">{{ number_format($stats['total_investments']) }}</div>
            <div style="color:#cbd5e1;">إجمالي الاستثمارات</div>
        </div>
        <div class="glass-card" style="text-align:center;padding:16px;">
            <div style="font-size:28px;color:#fbbf24;font-weight:800;">{{ number_format($stats['total_invested_amount'] ?: 0) }}</div>
            <div style="color:#cbd5e1;">إجمالي المبالغ المستثمرة</div>
        </div>
        <div class="glass-card" style="text-align:center;padding:16px;">
            <div style="font-size:28px;color:#22c55e;font-weight:800;">{{ number_format($stats['total_applications']) }}</div>
            <div style="color:#cbd5e1;">طلبات التراخيص</div>
        </div>
    </div>

    <div id="dashboard-grid" style="display:grid;grid-template-columns:2fr 1fr;gap:12px;">
        <div>
            @php
                $__statusMap = [
                    'approved' => 'معتمد',
                    'active' => 'نشط',
                    'completed' => 'مكتمل',
                    'pending' => 'قيد المراجعة',
                    'rejected' => 'مرفوض',
                ];
                // transaction type labels
                $__txMap = [ 'credit' => 'إيداع', 'debit' => 'سحب' ];
                // status -> color class mapping for inline styles
                $__statusColor = [
                    'approved' => '#16a34a', // green
                    'active' => '#16a34a',
                    'completed' => '#0ea5a3',
                    'pending' => '#f59e0b', // yellow
                    'rejected' => '#ef4444', // red
                ];
            @endphp
            <div class="glass-card" style="padding:12px;margin-bottom:12px;">
                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                    <button class="tab-btn active" data-tab="overview" style="padding:8px 12px;border-radius:8px;background:#22d3ee;color:#000;border:0;font-weight:700;">نظرة عامة</button>
                    <button class="tab-btn" data-tab="investments" style="padding:8px 12px;border-radius:8px;border:1px solid #d4af37;background:transparent;color:#cbd5e1;">استثماراتي</button>
                    <button class="tab-btn" data-tab="applications" style="padding:8px 12px;border-radius:8px;border:1px solid #d4af37;background:transparent;color:#cbd5e1;">طلبات التراخيص</button>
                    <button class="tab-btn" data-tab="documents" style="padding:8px 12px;border-radius:8px;border:1px solid #d4af37;background:transparent;color:#cbd5e1;">مستنداتي</button>
                    <button class="tab-btn" data-tab="earnings" style="padding:8px 12px;border-radius:8px;border:1px solid #d4af37;background:transparent;color:#cbd5e1;">عائد الأرباح الشهرية</button>
                    {{-- <button class="tab-btn" data-tab="settings" style="padding:8px 12px;border-radius:8px;border:1px solid #d4af37;background:transparent;color:#cbd5e1;">الإعدادات</button> --}}
                </div>
            </div>

            <div id="tab-overview" class="tab-panel">
                <div class="glass-card" style="padding:16px;margin-bottom:12px;">
                    <h3 style="color:#fff;margin-bottom:8px;">نظرة عامة سريعة</h3>
                    <p style="color:#cbd5e1;">مرحباً {{ $user->full_name ?? 'مستثمر' }} — هذه معاينة لبياناتك.</p>
                        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-top:12px;">
                        <div class="glass-card" style="padding:12px;text-align:center;">
                            <div style="font-size:20px;color:#06b6d4;font-weight:800">{{ number_format($stats['total_investments']) }}</div>
                            <div style="color:#cbd5e1">عدد الاستثمارات</div>
                        </div>
                        <div class="glass-card" style="padding:12px;text-align:center;">
                            <div style="font-size:20px;color:#fbbf24;font-weight:800">{{ number_format($stats['total_invested_amount'] ?: 0) }}</div>
                            <div style="color:#cbd5e1">إجمالي المبلغ المستثمر</div>
                        </div>
                        <div class="glass-card" style="padding:12px;text-align:center;">
                            <div style="font-size:20px;color:#22c55e;font-weight:800">{{ number_format($stats['total_applications']) }}</div>
                            <div style="color:#cbd5e1">طلبات التراخيص</div>
                        </div>
                    </div>

                        <!-- Sparkline + recent transactions -->
                        <div class="overview-sparkline-grid" style="display:grid;grid-template-columns:2fr 1fr;gap:12px;margin-top:12px;">
                            <div class="glass-card" style="padding:12px;">
                                <div style="display:flex;justify-content:space-between;align-items:center;">
                                    <div>
                                        <h4 style="color:#fff;margin:0 0 6px;">أداء العوائد</h4>
                                        @php
                                            // defensive: ensure $monthlyReturns exists and is an array of numbers
                                            if(!isset($monthlyReturns)){
                                                $monthlyReturns = collect([0,0,0,0,0,0]);
                                            }
                                            $vals = is_object($monthlyReturns) && method_exists($monthlyReturns, 'toArray')
                                                ? $monthlyReturns->toArray()
                                                : (is_array($monthlyReturns) ? $monthlyReturns : []);
                                            if(empty($vals)){
                                                // default to six zero months so UI keeps its shape
                                                $vals = [0,0,0,0,0,0];
                                            }
                                        @endphp
                                        <div style="color:#9ca3af;font-size:13px">مجموع العوائد: <strong style="color:#fff">{{ number_format(array_sum($vals)) }} ر.س</strong></div>
                                    </div>
                                    <div class="sparkline" style="width:240px;height:60px;">
                                        @php
                                            // prepare sparkline points using $vals prepared above
                                            $max = !empty($vals) ? max($vals) : 0;
                                            $max = $max ?: 1; // avoid division by zero
                                            $w = 240; $h = 60; $len = count($vals);
                                            $points = [];
                                            if($len <= 1){
                                                // not enough points: draw a flat line at bottom
                                                $points[] = "0,".($h);
                                                $points[] = ($w).",".($h);
                                            } else {
                                                $denom = ($len - 1) ?: 1;
                                                foreach($vals as $i => $v){
                                                    $x = ($i / $denom) * $w;
                                                    $y = $h - (($v / $max) * $h);
                                                    $points[] = round($x,2).",".round($y,2);
                                                }
                                            }
                                            $poly = implode(' ', $points);
                                        @endphp
                                        <svg width="240" height="60" viewBox="0 0 240 60" preserveAspectRatio="none">
                                            <polyline fill="none" stroke="#06b6d4" stroke-width="2" points="{{ $poly }}" />
                                        </svg>
                                    </div>
                                </div>

                                {{-- Quick withdrawal request (from pooled returns across all investments) --}}
                                <div style="margin-top:12px;display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                                    <form action="{{ url('/investor/withdrawal') }}" method="POST" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                                        @csrf
                                        <input type="hidden" name="type" value="withdrawal" />
                                        <div>
                                            <label for="withdraw-amount-dashboard" style="display:block;color:#9ca3af;font-size:12px;margin-bottom:4px;">المبلغ المطلوب سحبه</label>
                                            <input id="withdraw-amount-dashboard" name="amount" type="number" min="0" step="0.01" placeholder="0.00" required style="width:160px;background:#1f2937;border:1px solid #4b5563;color:#fff;padding:8px;border-radius:8px;" />
                                        </div>
                                        <button type="submit" class="btn-primary" style="white-space:nowrap;">طلب سحب</button>
                                    </form>
                                </div>
                            </div>

                            <div class="glass-card" style="padding:12px;">
                                <h4 style="color:#fff;margin-bottom:8px;">المعاملات الأخيرة</h4>
                                @if($transactions->isEmpty())
                                    <div style="color:#cbd5e1">لا توجد معاملات.</div>
                                @else
                                    <div style="display:grid;gap:8px;">
                                        @foreach($transactions as $t)
                                            <div style="display:flex;justify-content:space-between;align-items:center;padding:8px;border-radius:8px;background:rgba(255,255,255,0.02)">
                                                <div>
                                                    <div style="color:#9ca3af;font-size:12px">{{ $t->note }}</div>
                                                    @php $tType = strtolower($t->type ?? ''); $tLabel = $__txMap[$tType] ?? ($t->type ?? '—'); @endphp
                                                    <div style="color:#fff;font-weight:700">{{ ($t->type=='credit'?'+':'-') . number_format($t->amount) }} ر.س <span style="font-size:12px;color:#94a3b8;margin-right:8px;">{{ $tLabel }}</span></div>
                                                </div>
                                                <div style="color:#94a3b8;font-size:12px">{{
                                                    \Carbon\Carbon::parse($t->date)->diffForHumans()
                                                }}</div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                </div>
            </div>

            <div id="tab-investments" class="tab-panel" style="display:none;">
                <div class="glass-card" style="padding:12px;margin-bottom:12px;">
                    <h3 style="color:#fff;margin-bottom:8px;">قائمة الاستثمارات</h3>
                    @if($investments->isEmpty())
                        <div style="padding:18px;text-align:center;color:#cbd5e1;">لا توجد استثمارات.</div>
                    @else
                        <div class="investments-list" style="display:grid;gap:12px;max-height:420px;overflow:auto;padding-right:8px;">
                            @foreach($investments as $inv)
                                @php $isLastInv = isset($lastId) && $lastId && $inv->id === $lastId; @endphp
                                <div id="investment-{{ $inv->id }}" data-inv-id="{{ $inv->id }}" class="glass-card inv-card" style="padding:12px;display:grid;grid-template-columns:1fr 1fr 1fr 1fr 1fr;gap:8px;align-items:center; border: {{ $isLastInv ? '1px solid #22d3ee' : '1px solid rgba(255,255,255,0.08)' }}; box-shadow: {{ $isLastInv ? '0 0 0 3px rgba(34,211,238,0.2)' : 'none' }};">
                                    <div>
                                        <div style="color:#9ca3af;font-size:12px">المبلغ</div>
                                        <div style="color:#06b6d4;font-weight:700">{{ number_format($inv->amount) }} ر.س</div>
                                    </div>
                                    <div>
                                        <div style="color:#9ca3af;font-size:12px">نسبة الربح</div>
                                        <div style="color:#fbbf24;font-weight:700">{{ $inv->profit_rate ?? '-' }}%</div>
                                    </div>
                                    <div>
                                        <div style="color:#9ca3af;font-size:12px">نوع الاستثمار</div>
                                        <div style="color:#a78bfa;font-weight:700">{{ isset($inv->investment_type) ? ($inv->investment_type === 'large' ? 'استثمار كبير' : ($inv->investment_type === 'small' ? 'استثمار صغير' : $inv->investment_type)) : 'غير محدد' }}</div>
                                    </div>
                                        <div>
                                        <div style="color:#9ca3af;font-size:12px">الحالة</div>
                                        @php $skey = strtolower($inv->status ?? ''); $scolor = $__statusColor[$skey] ?? '#cbd5e1'; @endphp
                                        <div style="color:{{ $scolor }};font-weight:700">{{ $__statusMap[$skey] ?? ($inv->status ?? '—') }}</div>
                                    </div>
                                    <div style="text-align:left;">
                                        <a href="{{ route('investor.investment.detail', ['id' => $inv->id]) }}" class="btn-secondary" style="border-color:#64748b;color:#cbd5e1;">عرض</a>
                                    </div>
                                    @php $diag = $invDiagnostics[$inv->id] ?? null; @endphp
                                    <div style="grid-column:1/-1;margin-top:10px;background:rgba(255,255,255,0.01);border:1px solid rgba(255,255,255,0.03);padding:8px;border-radius:8px;">
                                        <div style="color:#9ca3af;font-size:12px;margin-bottom:4px;">عائد الأرباح الشهرية</div>
                                        <div style="display:flex;flex-direction:column;gap:4px;">
                                            <div style="font-size:13px;color:#d8cdb6;font-weight:600;">{{ $diag['note'] ?? '—' }} @if(isset($diag['overdue']) && $diag['overdue']) <span style="color:#ef4444;margin-left:8px;font-weight:800;">(تم تجاوز موعد الإيداع المتوقع)</span> @endif</div>
                                            <div style="font-size:12px;color:#bfae95;">سيظهر الإيداع في قسم \"عائد الأرباح الشهرية\" داخل حسابك.</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div id="tab-applications" class="tab-panel" style="display:none;">
                <div class="glass-card" style="padding:12px;">
                    <h3 style="color:#fff;margin-bottom:8px;">طلبات التراخيص</h3>
                    @if($applications->isEmpty())
                        <div style="padding:18px;text-align:center;color:#cbd5e1;">لا توجد طلبات.</div>
                    @else
                        <div style="display:grid;gap:12px;">
                            @foreach($applications as $app)
                                @php $isLast = isset($lastId) && $lastId && $app->id === $lastId; @endphp
                                <div class="glass-card" style="padding:12px;display:flex;justify-content:space-between;align-items:center; border: {{ $isLast ? '1px solid #22d3ee' : '1px solid rgba(255,255,255,0.08)' }}; box-shadow: {{ $isLast ? '0 0 0 3px rgba(34,211,238,0.2)' : 'none' }};">
                                    <div>
                                        <div style="color:#9ca3af;font-size:12px">اسم النشاط</div>
                                        <div style="color:#fff;font-weight:700">{{ $app->business_name }}</div>
                                    </div>
                                    <div style="text-align:left;">
                                        <div style="color:#9ca3af;font-size:12px">الحالة</div>
                                        @php $ask = strtolower($app->status ?? ''); $acolor = $__statusColor[$ask] ?? '#cbd5e1'; @endphp
                                        <div style="color:{{ $acolor }};font-weight:700">{{ $__statusMap[$ask] ?? ($app->status ?? '—') }}</div>
                                    </div>
                                        <div style="display:flex;gap:8px;align-items:center;">
                                            <a class="btn-secondary" href="{{ route('investor.license.show', ['id' => $app->id]) }}" style="border-color:#64748b;color:#cbd5e1;">عرض</a>
                                        </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div id="tab-documents" class="tab-panel" style="display:none;">
                <div class="glass-card" style="padding:12px;">
                    <h3 style="color:#fff;margin-bottom:8px;">مستنداتي</h3>
                    <p style="color:#cbd5e1;">قائمة المستندات ستكون هنا (معاينة تصميم).</p>
                </div>
            </div>

            <style>
            @media (max-width: 480px) {
                #tab-earnings > div {
                    min-width: 0 !important;
                    width: 315px !important;
                    max-width: 100vw !important;
                    box-sizing: border-box;
                }
            }
            </style>
            <div id="tab-earnings" class="tab-panel" style="display:none;">
                <div class="glass-card" style="padding:12px;">
                    <h3 style="color:#fff;margin-bottom:8px;">عائد الأرباح الشهرية</h3>
                    <div style="color:#cbd5e1;margin-bottom:8px;">فيما يلي ملخص العوائد الشهرية لكل استثمار؛ يتم تحديث الدفعات آليًا حسب جدول السداد.</div>

                    @if(empty($earningsSummary) || count($earningsSummary) === 0)
                        <div style="color:#cbd5e1;">لا توجد عوائد مُسجَّلة بعد.</div>
                    @else
                        <div id="earnings-chart-wrapper" style="margin-bottom:12px;">
                            <div id="earnings-summary" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                                    <div style="color:#94a3b8;font-size:13px">إجمالي العوائد: <strong id="earnings-total" style="color:#fff">-- ر.س</strong></div>
                                    <div style="color:#94a3b8;font-size:13px">متوسط شهري: <strong id="earnings-avg" style="color:#fff">-- ر.س</strong></div>
                                    <div>
                                        <button id="export-earnings-pdf" class="btn-secondary" style="border-color:#64748b;color:#cbd5e1;padding:8px 12px;border-radius:8px;">تصدير PDF</button>
                                    </div>
                            </div>
                            <div class="chart-area" style="height:320px;max-width:100%;padding:12px;box-sizing:border-box;">
                                <canvas id="earningsChart" style="background:#071124;border-radius:8px;display:block;width:100%;height:100%;box-sizing:border-box;"></canvas>
                            </div>
                        </div>
                        @php
                            // compute overall total across investments
                            $overallTotal = 0;
                            foreach($earningsSummary as $es){ $overallTotal += (float) ($es['total'] ?? 0); }
                        @endphp

                        <div id="earnings-highlights" style="display:flex;gap:12px;margin-bottom:12px;align-items:center;">
                            <div style="background:linear-gradient(90deg,#0f1724,#071124);padding:12px;border-radius:8px;border:1px solid rgba(255,255,255,0.04);">
                                <div style="color:#94a3b8;font-size:12px">إجمالي العوائد جميع الاستثمارات</div>
                                <div style="color:#fff;font-weight:800;font-size:18px;margin-top:6px;">{{ number_format($overallTotal,2) }} ر.س</div>
                            </div>
                            <div style="background:linear-gradient(90deg,#071124,#02101a);padding:12px;border-radius:8px;border:1px solid rgba(255,255,255,0.04);">
                                <div style="color:#94a3b8;font-size:12px">عدد الاستثمارات</div>
                                <div style="color:#fff;font-weight:700;font-size:16px;margin-top:6px;">{{ count($earningsSummary) }}</div>
                            </div>
                        </div>

                        @php $__palette = ['#06b6d4','#22c55e','#f59e0b','#ef4444','#8b5cf6','#10b981','#3b82f6']; @endphp
                        @foreach($earningsSummary as $invId => $data)
                            @php
                                $inv = $investments->firstWhere('id', $invId);
                                $invAmount = $inv->amount ?? null;
                                $invType = $inv->investment_type ?? null;
                                $typeLabel = $invType ? ($invType === 'large' ? 'استثمار كبير' : ($invType === 'small' ? 'استثمار صغير' : $invType)) : 'غير محدد';
                                $accent = $__palette[(is_string($invId)? array_sum(array_map('ord', str_split($invId))) : intval($invId)) % count($__palette)];
                                // dates for creation and expected completion
                                $createdAt = isset($inv->created_at) ? \Carbon\Carbon::parse($inv->created_at) : null;
                                $approvedAt = isset($inv->approved_at) ? \Carbon\Carbon::parse($inv->approved_at) : null;
                                $durationMonths = isset($inv->duration_months) ? (int) $inv->duration_months : null;
                                $expectedEnd = ($approvedAt && $durationMonths) ? $approvedAt->copy()->addMonths($durationMonths) : null;
                            @endphp
                            <div class="earnings-card" style="position:relative;background:linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01)); padding:14px;border-radius:10px;margin-bottom:14px;border:1px solid rgba(255,255,255,0.06);">
                                <div style="position:absolute;inset-inline-start:-1px;top:-1px;bottom:-1px;width:5px;background:{{ $accent }};border-radius:10px 0 0 10px;"></div>
                                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;gap:8px;flex-wrap:wrap;">
                                    <div style="color:#cbd5e1;font-size:14px;font-weight:700">الاستثمار: <span style="color:{{ $accent }};">{{ strtoupper($invId) }}</span> <small style="color:#94a3b8;margin-left:8px">({{ $typeLabel }})</small></div>
                                    <div style="color:#94a3b8;font-size:13px">إجمالي العوائد: <strong style="color:#10b981">{{ number_format($data['total'],2) }} ر.س</strong></div>
                                </div>
                                <div class="earnings-meta" style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:8px;margin-bottom:8px;">
                                    <div style="background:rgba(255,255,255,0.02);border:1px solid rgba(148,163,184,0.12);border-radius:8px;padding:8px;">
                                        <div style="color:#9ca3af;font-size:12px;">رقم الاستثمار</div>
                                        <div style="color:#e5e7eb;font-weight:700;">{{ strtoupper($invId) }}</div>
                                    </div>
                                    <div style="background:rgba(255,255,255,0.02);border:1px solid rgba(148,163,184,0.12);border-radius:8px;padding:8px;">
                                        <div style="color:#9ca3af;font-size:12px;">تاريخ الإنشاء</div>
                                        <div style="color:#e5e7eb;font-weight:700;">{{ $createdAt ? $createdAt->locale('ar')->translatedFormat('D، j M Y g:i A') : '—' }}</div>
                                    </div>
                                    <div style="background:rgba(255,255,255,0.02);border:1px solid rgba(148,163,184,0.12);border-radius:8px;padding:8px;">
                                        <div style="color:#9ca3af;font-size:12px;">تاريخ الاكتمال المتوقع</div>
                                        <div style="color:#e5e7eb;font-weight:700;">{{ $expectedEnd ? $expectedEnd->locale('ar')->translatedFormat('D، j M Y g:i A') : '—' }}</div>
                                    </div>
                                </div>
                                <div style="overflow:auto;">
                                <table class="w-full earnings-table" style="border-collapse:collapse;width:100%;">
                                    <thead>
                                        <tr>
                                            <th style="text-align:center;padding:10px;color:#94a3b8;background:rgba(255,255,255,0.02);">📅 تاريخ الاكتمال</th>
                                            <th style="text-align:center;padding:10px;color:#94a3b8;background:rgba(255,255,255,0.02);">🔢 رقم الشهر</th>
                                            <th style="text-align:center;padding:10px;color:#94a3b8;background:rgba(255,255,255,0.02);">⏳ المدة (أشهر)</th>
                                            <th style="text-align:center;padding:10px;color:#94a3b8;background:rgba(255,255,255,0.02);">💼 المبلغ المستثمر (ر.س)</th>
                                            <th style="text-align:center;padding:10px;color:#94a3b8;background:rgba(255,255,255,0.02);">💰 المبلغ المودع (ر.س)</th>
                                            <th style="text-align:center;padding:10px;color:#94a3b8;background:rgba(255,255,255,0.02);">➕ تاريخ الإضافة</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($data['rows'] as $row)
                                            <tr style="border-top:1px solid rgba(255,255,255,0.03);">
                                                <td data-label="📅 تاريخ الاكتمال" style="padding:10px;color:#e6edf3;text-align:center;">{{ $expectedEnd ? $expectedEnd->format('Y-m-d') : '—' }}</td>
                                                <td data-label="🔢 رقم الشهر" style="padding:10px;color:#e6edf3;text-align:center;">{{ $row->month_number }}</td>
                                                <td data-label="⏳ المدة (أشهر)" style="padding:10px;color:#e6edf3;text-align:center;">
                                                    @if($durationMonths)
                                                        <span class="earnings-pill pill-time">{{ $durationMonths }} شهر</span>
                                                    @else
                                                        —
                                                    @endif
                                                </td>
                                                <td data-label="💼 المبلغ المستثمر (ر.س)" style="padding:10px;color:#e6edf3;text-align:center;">
                                                    @if(isset($invAmount))
                                                        <span class="earnings-pill pill-amount">{{ number_format($invAmount,2) }} ر.س</span>
                                                    @else
                                                        —
                                                    @endif
                                                </td>
                                                <td data-label="💰 المبلغ المودع (ر.س)" style="padding:10px;color:#e6edf3;text-align:center;font-weight:700">{{ number_format($row->profit_amount,2) }}</td>
                                                <td data-label="➕ تاريخ الإضافة" style="padding:10px;color:#94a3b8;text-align:center;">{{ \Carbon\Carbon::parse($row->created_at)->format('Y-m-d') }}</td>
                                            </tr>
                                        @endforeach
                                        <tr style="border-top:2px solid rgba(255,255,255,0.04);background:rgba(255,255,255,0.01);">
                                            <td class="total-label" colspan="5" style="padding:10px;color:#94a3b8;font-weight:700;text-align:center;">الإجمالي</td>
                                            <td data-label="الإجمالي" style="padding:10px;color:#fff;text-align:center;font-weight:800">{{ number_format($data['total'],2) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                                </div>
                                <div style="color:#94a3b8;font-size:12px;margin-top:8px;">يوضح الجدول أعلاه قيمة الربح المضافة لكل شهر لهذا الاستثمار، مع توضيح رقم الاستثمار وتواريخ البداية والنهاية المتوقعة لسهولة المتابعة.</div>
                            </div>
                        @endforeach
                    @endif


                </div>
            </div>

            <div id="tab-settings" class="tab-panel" style="display:none;">
                <div class="glass-card" style="padding:12px;">
                    <h3 style="color:#fff;margin-bottom:8px;">إعدادات الحساب</h3>
                    <form method="POST" action="#">
                        <div style="display:grid;gap:8px;">
                            <label style="color:#cbd5e1;">الاسم الكامل</label>
                            <input type="text" value="{{ $user->full_name ?? '' }}" style="padding:8px;border-radius:8px;background:#0f1724;border:1px solid rgba(255,255,255,0.04);color:#fff;" />
                            <label style="color:#cbd5e1;">البريد الإلكتروني</label>
                            <input type="email" value="{{ $user->email ?? '' }}" style="padding:8px;border-radius:8px;background:#0f1724;border:1px solid rgba(255,255,255,0.04);color:#fff;" />
                            <button class="btn-primary" style="margin-top:8px;">حفظ التغييرات</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right: quick actions & account -->
        {{-- <div>
            <div class="glass-card" style="padding:12px;margin-bottom:12px;">
                <h4 style="color:#fff;margin-bottom:8px;">إجراءات سريعة</h4>
                <div style="display:flex;flex-direction:column;gap:8px">
                    <a href="/investment/check" class="btn-primary">إنشاء استثمار</a>
                    <a href="/license" class="btn-secondary" style="border-color:#64748b;color:#cbd5e1;">تقديم طلب ترخيص</a>
                    <a href="/documents" class="btn-secondary" style="border-color:#64748b;color:#cbd5e1;">مستنداتي</a>
                    <a href="/withdraw" class="btn-secondary" style="border-color:#ef4444;color:#fda4af;">سحب أرباح</a>
                </div>
            </div> --}}

            <div class="glass-card" style="padding:12px;">
                <h4 style="color:#fff;margin-bottom:8px;">معلومات الحساب</h4>
                <div style="color:#cbd5e1">البريد الإلكتروني: <strong style="color:#fff">{{ $user->email ?? '-' }}</strong></div>
                <div style="color:#cbd5e1;">الهاتف: <strong style="color:#fff">{{ optional($investor)->phone ?? '-' }}</strong></div>
            </div>
        </div>
    </div>
</div>
@endsection



<!-- License Application Modal -->
<div id="license-modal" class="hidden fixed inset-0 z-50 items-center justify-center bg-black/60">
    <div class="glass-card" style="max-width:860px;width:95%;padding:18px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
            <h3 style="color:#fff;margin:0;">عرض طلب الترخيص</h3>
            <button id="license-modal-close" class="btn-secondary" style="border-color:#64748b;color:#cbd5e1;">إغلاق</button>
        </div>
        <div id="license-modal-body">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div>
                    <div style="color:#9ca3af;font-size:12px">اسم النشاط</div>
                    <div id="lm-business-name" style="color:#fff;font-weight:700">—</div>
                    <div style="margin-top:8px;color:#9ca3af;font-size:12px">نوع النشاط</div>
                    <div id="lm-business-type" style="color:#fff">—</div>
                    <div style="margin-top:8px;color:#9ca3af;font-size:12px">الموقع</div>
                    <div id="lm-location" style="color:#fff">—</div>
                </div>
                <div>
                    <div style="color:#9ca3af;font-size:12px">حالة الطلب</div>
                    <div id="lm-status" style="color:#fff;font-weight:700">—</div>
                    <div style="margin-top:8px;color:#9ca3af;font-size:12px">رقم الطلب</div>
                    <div id="lm-id" style="color:#fff">—</div>
                    <div style="margin-top:8px;color:#9ca3af;font-size:12px">تاريخ الإنشاء</div>
                    <div id="lm-created" style="color:#fff">—</div>
                </div>
            </div>
            <div style="margin-top:12px;">
                <div style="color:#9ca3af;font-size:12px">وصف النشاط</div>
                <div id="lm-desc" style="color:#fff;white-space:pre-wrap;margin-top:6px;">—</div>
            </div>
            <div id="lm-docs" style="margin-top:12px;display:grid;gap:8px;"></div>
            <div id="lm-preview" style="margin-top:12px;display:block;">
                <!-- preview (image or iframe for pdf) will be injected here -->
                <div id="lm-preview-empty" style="color:#9ca3af;font-size:13px">اختر ملفاً للمعاينة</div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
@parent
<script>
    (function(){
        // Tab handling (activate panels, initial tab, URL anchors)
        var tabs = document.querySelectorAll('.tab-btn');
        function activate(name){
            document.querySelectorAll('.tab-panel').forEach(function(p){ p.style.display = 'none'; });
            var panel = document.getElementById('tab-'+name);
            if(panel) panel.style.display = 'block';
            tabs.forEach(function(b){ b.classList.remove('active'); b.style.background='transparent'; b.style.color='#cbd5e1'; b.style.border='1px solid #d4af37'; });
            var active = document.querySelector('.tab-btn[data-tab="'+name+'"]');
            if(active){ active.classList.add('active'); active.style.background='#22d3ee'; active.style.color='#000'; active.style.border='0'; }
        }
        tabs.forEach(function(b){ b.addEventListener('click', function(){ activate(this.getAttribute('data-tab')); }); });
        var initialTab = @json($activeTab ?? 'overview');
        activate(initialTab);
        try {
            var params = new URLSearchParams(window.location.search);
            var tab = params.get('tab');
            var lastId = params.get('last_id');
            if(tab === 'payments' || lastId){
                activate('investments');
                if(lastId){
                    var el = document.getElementById('investment-' + lastId) || document.querySelector('[data-inv-id="'+ lastId +'"]');
                    if(el){
                        el.style.transition = 'box-shadow 0.3s ease, border-color 0.3s ease';
                        el.style.boxShadow = '0 8px 30px rgba(34,211,238,0.12)';
                        el.style.borderColor = '#22d3ee';
                        el.scrollIntoView({behavior:'smooth', block:'center'});
                        setTimeout(function(){ el.style.boxShadow = ''; if(!el.getAttribute('data-permanent-highlight')){ el.style.borderColor = 'rgba(255,255,255,0.08)'; } }, 3000);
                    }
                }
            }
        } catch(e){}

        const apps = @json($applications->keyBy('id')) || {};
        function openLicense(id){
            const app = apps[id];
            if(!app) return alert('تعذر العثور على الطلب');
            document.getElementById('lm-business-name').textContent = app.business_name || '-';
            document.getElementById('lm-business-type').textContent = app.business_type || '-';
            document.getElementById('lm-location').textContent = app.location || '-';
            document.getElementById('lm-status').textContent = app.status || '-';
            document.getElementById('lm-id').textContent = app.id || '-';
            document.getElementById('lm-created').textContent = app.created_at || '-';
            document.getElementById('lm-desc').textContent = app.activity_description || '-';
            const docs = document.getElementById('lm-docs'); docs.innerHTML = '';
            // Render document thumbnails + download/preview
            function makeDocItem(url, label){
                const wrap = document.createElement('div');
                wrap.style.display = 'flex';
                wrap.style.alignItems = 'center';
                wrap.style.gap = '8px';

                const thumb = document.createElement('div');
                thumb.style.width = '84px';
                thumb.style.height = '64px';
                thumb.style.background = '#071124';
                thumb.style.display = 'flex';
                thumb.style.alignItems = 'center';
                thumb.style.justifyContent = 'center';
                thumb.style.border = '1px solid rgba(255,255,255,0.04)';
                thumb.style.borderRadius = '6px';
                thumb.style.cursor = 'pointer';
                thumb.title = 'انقر للمعاينة';

                // try to detect image vs pdf
                const lower = (url || '').toLowerCase();
                const isImage = lower.match(/\.(png|jpe?g|gif|webp|bmp)(\?|$)/);
                const isPdf = lower.match(/\.pdf(\?|$)/);

                if(isImage){
                    const img = document.createElement('img');
                    img.src = url;
                    img.style.maxWidth = '100%';
                    img.style.maxHeight = '100%';
                    img.style.objectFit = 'cover';
                    thumb.appendChild(img);
                } else if(isPdf){
                    const icon = document.createElement('div');
                    icon.textContent = 'PDF';
                    icon.style.color = '#22d3ee';
                    icon.style.fontWeight = '700';
                    thumb.appendChild(icon);
                } else {
                    const icon = document.createElement('div');
                    icon.textContent = 'ملف';
                    icon.style.color = '#94a3b8';
                    thumb.appendChild(icon);
                }

                const meta = document.createElement('div');
                meta.style.display = 'flex';
                meta.style.flexDirection = 'column';
                meta.style.gap = '6px';

                const title = document.createElement('div');
                title.textContent = label || 'مستند';
                title.style.color = '#fff';
                title.style.fontWeight = '700';

                const actions = document.createElement('div');
                actions.style.display = 'flex';
                actions.style.gap = '8px';

                const btnPreview = document.createElement('button');
                btnPreview.type = 'button';
                btnPreview.className = 'btn-secondary';
                btnPreview.textContent = 'معاينة';
                btnPreview.style.borderColor = '#64748b';
                btnPreview.style.color = '#cbd5e1';
                btnPreview.addEventListener('click', function(){
                    showPreview(url);
                });

                const btnDownload = document.createElement('a');
                btnDownload.href = url;
                btnDownload.download = '';
                btnDownload.className = 'btn-secondary';
                btnDownload.textContent = 'تنزيل';
                btnDownload.style.borderColor = '#64748b';
                btnDownload.style.color = '#cbd5e1';
                btnDownload.target = '_blank';

                actions.appendChild(btnPreview);
                actions.appendChild(btnDownload);

                meta.appendChild(title);
                meta.appendChild(actions);

                wrap.appendChild(thumb);
                wrap.appendChild(meta);

                // clicking thumbnail also previews
                thumb.addEventListener('click', function(){ showPreview(url); });

                return wrap;
            }

            function showPreview(url){
                const preview = document.getElementById('lm-preview');
                preview.innerHTML = '';
                const lower = (url || '').toLowerCase();
                const isImage = lower.match(/\.(png|jpe?g|gif|webp|bmp)(\?|$)/);
                const isPdf = lower.match(/\.pdf(\?|$)/);
                if(isImage){
                    const img = document.createElement('img');
                    img.src = url;
                    img.style.maxWidth = '100%';
                    img.style.borderRadius = '6px';
                    img.style.border = '1px solid rgba(255,255,255,0.04)';
                    preview.appendChild(img);
                } else if(isPdf){
                    const iframe = document.createElement('iframe');
                    iframe.src = url;
                    iframe.style.width = '100%';
                    iframe.style.height = '520px';
                    iframe.style.border = '1px solid rgba(255,255,255,0.04)';
                    iframe.style.borderRadius = '6px';
                    preview.appendChild(iframe);
                } else {
                    const a = document.createElement('a');
                    a.href = url; a.target = '_blank'; a.textContent = 'فتح الملف في علامة تبويب جديدة'; a.style.color='#22d3ee';
                    preview.appendChild(a);
                }
            }

            if(app.identity_image_front){
                docs.appendChild(makeDocItem(app.identity_image_front, 'صورة الهوية (أمام)'));
            }
            if(app.identity_image_back){
                docs.appendChild(makeDocItem(app.identity_image_back, 'صورة الهوية (خلف)'));
            }
            document.getElementById('license-modal').classList.remove('hidden');
            document.getElementById('license-modal').classList.add('flex');
        }
        document.addEventListener('click', function(e){
            const btn = e.target.closest('.btn-app-view');
            if(btn){ openLicense(btn.getAttribute('data-id')); }
            if(e.target.id === 'license-modal-close'){
                document.getElementById('license-modal').classList.add('hidden');
                document.getElementById('license-modal').classList.remove('flex');
            }
        });
    })();
</script>

@if(isset($chartDatasets) && isset($monthlyLabels))
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script>
        (function(){
            var labels = @json($monthlyLabels ?? []);
            var chartDatasets = @json($chartDatasets ?? []);

            function formatCurrency(v){ try { return v.toLocaleString(); } catch (e) { return v; } }

            function hexToRgba(hex, alpha){
                try{
                    hex = (hex||'#06b6d4').replace('#','');
                    if(hex.length === 3) { hex = hex.split('').map(function(h){ return h+h; }).join(''); }
                    var bigint = parseInt(hex, 16);
                    var r = (bigint >> 16) & 255;
                    var g = (bigint >> 8) & 255;
                    var b = bigint & 255;
                    return 'rgba(' + r + ',' + g + ',' + b + ',' + (alpha || 1) + ')';
                }catch(e){ return 'rgba(6,182,212,' + (alpha||0.08) + ')'; }
            }

            function initEarningsChart(){
                try {
                    var canvas = document.getElementById('earningsChart'); if(!canvas) return;
                    var ctx = canvas.getContext('2d');
                    // prepare datasets for Chart.js
                    var datasets = chartDatasets.map(function(d,i){
                        var color = d.borderColor || '#06b6d4';
                        // create pale gradient fill
                        var grad = ctx.createLinearGradient(0,0,0,320);
                        grad.addColorStop(0, hexToRgba(color, 0.14));
                        grad.addColorStop(1, hexToRgba(color, 0.02));
                        return {
                            label: d.label,
                            data: d.data,
                            borderColor: color,
                            borderWidth: 2,
                            backgroundColor: grad,
                            fill: true,
                            tension: 0.25,
                            pointRadius: 3,
                        };
                    });

                    if(window.__earningsChartInstance){
                        window.__earningsChartInstance.data.labels = labels;
                        window.__earningsChartInstance.data.datasets = datasets;
                        window.__earningsChartInstance.update();
                        return;
                    }

                    window.__earningsChartInstance = new Chart(ctx, {
                        type: 'line',
                        data: { labels: labels, datasets: datasets },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: true, position: 'bottom', labels: { color: '#cbd5e1' } }, tooltip: { callbacks: { label: function(ctx){ return formatCurrency(ctx.parsed.y || 0) + ' ر.س'; } } } },
                            scales: { y: { beginAtZero: true, ticks: { color: '#cbd5e1', callback: function(v){ return formatCurrency(v); } }, grid: { color: 'rgba(148,163,184,0.06)' } }, x: { ticks: { color: '#cbd5e1' }, grid: { display: false } } }
                        }
                    });

                    // compute totals across datasets for summary
                    try {
                        var totals = labels.map(function(){ return 0; });
                        chartDatasets.forEach(function(d){ d.data.forEach(function(v, idx){ totals[idx] += Number(v) || 0; }); });
                        var totalSum = totals.reduce(function(a,b){ return a + (Number(b)||0); }, 0);
                        var avg = totals.length ? (totalSum / totals.length) : 0;
                        document.getElementById('earnings-total').textContent = formatCurrency(totalSum) + ' ر.س';
                        document.getElementById('earnings-avg').textContent = formatCurrency(Math.round(avg*100)/100) + ' ر.س';
                    } catch(e){}

                } catch (e){ console.warn('initEarningsChart error', e); }
            }

            // export PDF: capture wrapper and produce PDF
            async function exportEarningsPdf(){
                try{
                    var wrapper = document.getElementById('earnings-chart-wrapper');
                    if(!wrapper) { alert('لا يوجد شيء لتصديره'); return; }
                    // temporarily expand width for better quality
                    var originalWidth = wrapper.style.width;
                    wrapper.style.width = '1100px';
                    const canvas = await html2canvas(wrapper, { scale: 2, useCORS: true });
                    wrapper.style.width = originalWidth;
                    const imgData = canvas.toDataURL('image/jpeg', 0.95);
                    const { jsPDF } = window.jspdf;
                    const pdf = new jsPDF({ orientation: 'landscape', unit: 'pt', format: 'a4' });
                    const pageWidth = pdf.internal.pageSize.getWidth();
                    const pageHeight = pdf.internal.pageSize.getHeight();
                    // fit image into page while preserving aspect
                    const imgProps = { width: canvas.width, height: canvas.height };
                    const ratio = Math.min(pageWidth / imgProps.width, pageHeight / imgProps.height);
                    const w = imgProps.width * ratio; const h = imgProps.height * ratio;
                    pdf.addImage(imgData, 'JPEG', (pageWidth - w) / 2, 20, w, h);
                    pdf.save('earnings.pdf');
                } catch (e){ console.warn('export pdf failed', e); alert('فشل تصدير PDF: ' + e.message); }
            }

            document.addEventListener('DOMContentLoaded', function(){
                // init chart when tab active
                var active = document.querySelector('.tab-btn.active');
                if(active && active.getAttribute('data-tab') === 'earnings'){ initEarningsChart(); }
                else { document.querySelectorAll('.tab-btn').forEach(function(btn){ btn.addEventListener('click', function(){ if(this.getAttribute('data-tab') === 'earnings'){ setTimeout(initEarningsChart, 50); } }); }); }
                setTimeout(function(){ if(!window.__earningsChartInstance) initEarningsChart(); }, 600);

                var exp = document.getElementById('export-earnings-pdf'); if(exp){ exp.addEventListener('click', exportEarningsPdf); }
            });
        })();
    </script>
@endif
@endsection
