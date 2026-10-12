@extends('layouts.app')

@section('content')
<div class="earnings-page" style="padding:20px;max-width:1080px;margin:0 auto;">
    <style>
        /* Page-scoped styles for clarity and responsiveness */
        .earnings-intro{color:#cbd5e1;background:rgba(255,255,255,0.04);border:1px solid #334155;padding:12px;border-radius:10px;margin-bottom:12px;}
        .inv-card{position:relative;background:rgba(17,24,39,0.6);border:1px solid rgba(148,163,184,0.25);border-radius:12px;padding:14px;margin-bottom:14px;box-shadow:0 6px 24px rgba(2,6,23,0.35)}
        .inv-card__stripe{position:absolute;inset-inline-start:-1px;top:-1px;bottom:-1px;width:6px;border-radius:12px 0 0 12px}
        .inv-meta{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;margin-top:6px}
        .inv-meta__item{background:rgba(255,255,255,0.03);border:1px solid rgba(148,163,184,0.18);border-radius:10px;padding:10px}
        .inv-meta__label{color:#9ca3af;font-size:12px}
        .inv-meta__value{color:#e5e7eb;font-weight:700}
        .table-wrap{margin-top:10px;overflow:auto;border:1px solid rgba(148,163,184,0.18);border-radius:10px}
        table.inv{width:100%;border-collapse:separate;border-spacing:0;min-width:720px;background:rgba(255,255,255,0.02)}
        table.inv thead th{position:sticky;top:0;background:rgba(15,23,42,0.8);backdrop-filter:blur(6px);color:#cbd5e1;font-weight:700;text-align:right;padding:10px;border-bottom:1px solid rgba(148,163,184,0.18)}
        table.inv tbody td{color:#e5e7eb;padding:10px;border-bottom:1px solid rgba(148,163,184,0.08)}
        .inv-total{display:flex;justify-content:flex-end;margin-top:8px;color:#e5e7eb}
        .hint{font-size:12px;color:#94a3b8}
        .chart-row{display:grid;grid-template-columns:180px 1fr;gap:10px;align-items:center;margin-top:10px}
        .chart-legend{display:flex;flex-wrap:wrap;gap:6px}
        .legend-pill{display:inline-flex;align-items:center;gap:6px;background:rgba(255,255,255,0.03);border:1px solid rgba(148,163,184,0.18);padding:6px 8px;color:#cbd5e1;border-radius:999px}
        .legend-dot{width:10px;height:10px;border-radius:999px}
        @media (max-width: 640px){
            .earnings-page{padding:16px}
            .inv-meta{grid-template-columns:1fr}
            .chart-row{grid-template-columns:1fr}
        }
    </style>

    <h2 style="color:#fff;font-weight:800;font-size:22px;margin-bottom:8px;">عائد الأرباح الشهرية</h2>
    <div class="earnings-intro">
        هذه الصفحة تعرض أرباحك الشهرية لكل استثمار على حدة. ستجد في الأعلى معلومات موجزة عن الاستثمار (المبلغ، الحالة، مدة الاستثمار، وتاريخي البدء والانتهاء المتوقع)، وأسفلها مخططًا بيانيًا بسيطًا يوضح الربح لكل شهر، ثم جدولًا مفصلًا لكل عملية إضافة ربح.
    </div>

    @php
        // simple accent palette
        $palette = ['#06b6d4','#22c55e','#f59e0b','#ef4444','#8b5cf6','#10b981','#3b82f6'];
        $statusMap = [
            'approved' => 'معتمد',
            'active' => 'نشط',
            'completed' => 'مكتمل',
            'pending' => 'قيد المراجعة',
            'rejected' => 'مرفوض',
        ];
    @endphp

    @if(empty($summary))
        <div class="inv-card"><div style="color:#cbd5e1">لا توجد عوائد حتى الآن.</div></div>
    @else
        @foreach($summary as $invId => $data)
            @php
                $meta = $data['meta'] ?? [];
                $accent = $palette[(is_string($invId)? array_sum(array_map('ord', str_split($invId))) : intval($invId)) % count($palette)];
                $statusKey = strtolower((string)($meta['status'] ?? ''));
                $statusLabel = $statusMap[$statusKey] ?? ($meta['status'] ?? '—');
                $createdAt = isset($meta['created_at']) ? \Carbon\Carbon::parse($meta['created_at']) : null;
                $approvedAt = isset($meta['approved_at']) ? \Carbon\Carbon::parse($meta['approved_at']) : null;
                $expectedEnd = isset($meta['expected_end_at']) && $meta['expected_end_at'] ? \Carbon\Carbon::parse($meta['expected_end_at']) : null;
            @endphp
            <div class="inv-card">
                <div class="inv-card__stripe" style="background: {{ $accent }};"></div>
                <div style="display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap;">
                    <div style="color:#e5e7eb;font-weight:800;font-size:18px;">الاستثمار: <span style="color:{{ $accent }}">{{ strtoupper($invId) }}</span></div>
                    <div class="legend-pill" style="border-color:{{ $accent }};">
                        <span class="legend-dot" style="background:{{ $accent }};"></span>
                        <span>{{ $statusLabel }}</span>
                    </div>
                </div>

                <div class="inv-meta">
                    <div class="inv-meta__item">
                        <div class="inv-meta__label">المبلغ</div>
                        <div class="inv-meta__value" style="color:{{ $accent }}">{{ isset($meta['amount']) ? number_format((float)$meta['amount']) : '—' }} ر.س</div>
                    </div>
                    <div class="inv-meta__item">
                        <div class="inv-meta__label">نسبة الربح</div>
                        <div class="inv-meta__value" style="color:#fbbf24">{{ isset($meta['profit_rate']) ? $meta['profit_rate'] : '—' }}%</div>
                    </div>
                    <div class="inv-meta__item">
                        <div class="inv-meta__label">مدة الاستثمار</div>
                        <div class="inv-meta__value">{{ $meta['duration_months'] ?? '—' }} شهر</div>
                    </div>
                    <div class="inv-meta__item">
                        <div class="inv-meta__label">العائد المتوقع</div>
                        <div class="inv-meta__value" style="color:#22c55e">{{ isset($meta['expected_return']) ? number_format((float)$meta['expected_return']) : '—' }} ر.س</div>
                    </div>
                    <div class="inv-meta__item">
                        <div class="inv-meta__label">تاريخ الإنشاء</div>
                        <div class="inv-meta__value">{{ $createdAt ? $createdAt->locale('ar')->translatedFormat('D، j M Y g:i A') : '—' }}</div>
                    </div>
                    <div class="inv-meta__item">
                        <div class="inv-meta__label">تاريخ البدء (الاعتماد)</div>
                        <div class="inv-meta__value">{{ $approvedAt ? $approvedAt->locale('ar')->translatedFormat('D، j M Y g:i A') : '—' }}</div>
                    </div>
                    <div class="inv-meta__item">
                        <div class="inv-meta__label">تاريخ الاكتمال المتوقع</div>
                        <div class="inv-meta__value">{{ $expectedEnd ? $expectedEnd->locale('ar')->translatedFormat('D، j M Y g:i A') : '—' }}</div>
                    </div>
                    <div class="inv-meta__item">
                        <div class="inv-meta__label">إجمالي الأرباح حتى الآن</div>
                        <div class="inv-meta__value" style="color:#22c55e">{{ number_format((float)$data['total'],2) }} ر.س</div>
                    </div>
                </div>

                <div class="chart-row">
                    <div class="chart-legend">
                        <span class="legend-pill"><span class="legend-dot" style="background:{{ $accent }}"></span>الربح الشهري</span>
                        <span class="hint">يوضح الربح المضاف لكل شهر.</span>
                    </div>
                    <canvas class="earnings-chart" width="600" height="160"
                        data-color="{{ $accent }}"
                        data-labels='@json(collect($data['rows'])->sortBy('month_number')->pluck('month_number')->values())'
                        data-values='@json(collect($data['rows'])->sortBy('month_number')->pluck('profit_amount')->map(fn($v)=>round($v,2))->values())'>
                    </canvas>
                </div>

                <div class="table-wrap">
                    <table class="inv">
                        <thead>
                            <tr>
                                <th>رقم الاستثمار</th>
                                <th>تاريخ الإنشاء</th>
                                <th>تاريخ الاكتمال</th>
                                <th>رقم الشهر</th>
                                <th>مبلغ الربح (ر.س)</th>
                                <th>تاريخ الإضافة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(collect($data['rows'])->sortBy('month_number') as $row)
                            <tr>
                                <td>{{ strtoupper($invId) }}</td>
                                <td>{{ $createdAt ? $createdAt->locale('ar')->translatedFormat('D، j M Y') : '—' }}</td>
                                <td>{{ $expectedEnd ? $expectedEnd->locale('ar')->translatedFormat('D، j M Y') : '—' }}</td>
                                <td>{{ $row->month_number }}</td>
                                <td>{{ number_format($row->profit_amount,2) }}</td>
                                <td>{{ \Carbon\Carbon::parse($row->created_at)->locale('ar')->translatedFormat('D، j M Y g:i A') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="inv-total"><span class="hint">إجمالي الأرباح لهذا الاستثمار:</span>&nbsp;<strong style="color:#22c55e">{{ number_format($data['total'],2) }} ر.س</strong></div>
            </div>
        @endforeach
    @endif
</div>
@endsection

@push('scripts')
<script>
    (function(){
        function ensureChartJs(cb){
            if(window.Chart){ cb(); return; }
            var s=document.createElement('script');
            s.src='https://cdn.jsdelivr.net/npm/chart.js';
            s.onload=cb; document.head.appendChild(s);
        }
        ensureChartJs(function(){
            try{
                document.querySelectorAll('.earnings-chart').forEach(function(cv){
                    var ctx=cv.getContext('2d');
                    var labels=JSON.parse(cv.getAttribute('data-labels')||'[]');
                    var values=JSON.parse(cv.getAttribute('data-values')||'[]');
                    var color=cv.getAttribute('data-color')||'#06b6d4';
                    new Chart(ctx,{ type:'bar', data:{ labels:labels, datasets:[{ label:'الربح (ر.س)', data:values, backgroundColor:color+'88', borderColor:color, borderWidth:1, borderRadius:6 }] }, options:{ plugins:{ legend:{ display:false } }, scales:{ x:{ ticks:{ color:'#cbd5e1' }, grid:{ display:false } }, y:{ ticks:{ color:'#94a3b8' }, grid:{ color:'rgba(148,163,184,0.12)' } } } } });
                });
            }catch(e){}
        });
    })();
    </script>
@endpush
