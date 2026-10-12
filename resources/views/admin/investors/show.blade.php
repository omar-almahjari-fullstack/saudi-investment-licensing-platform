@extends('layouts.admin')

@section('content')
    <div class="max-w-4xl mx-auto p-4">
        <div class="bg-gray-900/50 border border-gray-700 rounded-lg p-4">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <h2 class="text-xl font-semibold text-white">تفاصيل المستثمر — {{ $investor->full_name ?? '—' }}</h2>
                    <div class="text-sm text-gray-400">معرّف: <span class="font-mono">{{ $investor->id }}</span></div>
                </div>
                <div class="text-sm text-gray-300">
                    <div>البريد: {{ $investor->email ?? '—' }}</div>
                    <div>الهاتف: {{ $investor->phone ?? '—' }}</div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <div class="lg:col-span-1">
                    @php
                        $iname = $investor->full_name ?? ($investor->name ?? '');
                        $cleanName = preg_replace('/\s+/u', '', trim((string)$iname));
                        $initialsDisplay = $cleanName ? mb_strtoupper(mb_substr($cleanName, 0, 2, 'UTF-8'), 'UTF-8') : 'IN';
                        $h = $cleanName ? (crc32($cleanName) % 360) : 220;
                        $h2 = ($h + 40) % 360;
                        $bgStyle = "background: linear-gradient(135deg, hsl($h,70%,45%), hsl($h2,70%,50%));";
                    @endphp
                    <div class="bg-gray-800/40 p-3 rounded text-center">
                        <div class="flex justify-center mb-3">
                            <div class="w-20 h-20 rounded-full flex items-center justify-center text-white font-bold text-2xl" style="{{ $bgStyle }}">{{ $initialsDisplay }}</div>
                        </div>
                        <p class="text-sm text-gray-300">حالة الحساب: <strong class="text-white">{{ $investor->is_active ? 'نشط' : 'غير نشط' }}</strong></p>
                        <p class="text-sm text-gray-300 mt-2">تاريخ التسجيل: <strong class="text-white">{{ optional($investor->created_at)->toDayDateTimeString() ?? '—' }}</strong></p>
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <h3 class="text-sm text-gray-300 mb-2">الاستثمارات الأخيرة</h3>
                    <div class="space-y-2">
                        @if(!empty($investments) && count($investments))
                            <table class="w-full text-sm text-left">
                                <thead>
                                    <tr class="text-gray-400">
                                        <th class="p-2">المعرف</th>
                                        <th class="p-2">الحالة</th>
                                        <th class="p-2">المبلغ</th>
                                        <th class="p-2">تاريخ الإنشاء</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($investments as $inv)
                                        <tr class="border-t border-gray-800">
                                            <td class="p-2 font-mono text-gray-200">{{ $inv->id }}</td>
                                            @php
                                                $s = strtolower($inv->status ?? '');
                                                $statusLabels = [
                                                    'pending' => 'معلق', 'active' => 'نشط', 'in_progress' => 'قيد التنفيذ',
                                                    'completed' => 'مكتمل', 'paid' => 'مدفوع', 'approved' => 'موافق',
                                                    'rejected' => 'مرفوض', 'cancelled' => 'ملغى', 'failed' => 'فشل'
                                                ];
                                                $statusColors = [
                                                    'pending' => 'bg-yellow-600', 'active' => 'bg-cyan-600', 'in_progress' => 'bg-cyan-600',
                                                    'completed' => 'bg-green-600', 'paid' => 'bg-indigo-600', 'approved' => 'bg-green-500',
                                                    'rejected' => 'bg-red-600', 'cancelled' => 'bg-gray-600', 'failed' => 'bg-red-600'
                                                ];
                                                $label = $statusLabels[$s] ?? ($inv->status ?? '—');
                                                $badgeCls = $statusColors[$s] ?? 'bg-gray-700';
                                            @endphp
                                            <td class="p-2"><span class="px-2 py-1 text-xs rounded-full {{ $badgeCls }} text-white">{{ $label }}</span></td>
                                            <td class="p-2 text-gray-200">{{ number_format($inv->amount ?? 0, 2) }}</td>
                                            <td class="p-2 text-gray-400">{{ optional($inv->created_at)->diffForHumans() }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="text-gray-400">لا توجد استثمارات لعرضها.</div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="mt-4 text-right">
                <a href="{{ route('admin.investors.index') }}" class="inline-block px-3 py-2 bg-gray-700 text-white rounded">عودة إلى القائمة</a>
            </div>
        </div>
    </div>
@endsection
