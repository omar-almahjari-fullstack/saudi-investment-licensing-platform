@php
    // Expect $investors as a collection or array
    $investors = $investors ?? [];
@endphp
<div class="w-full">
    <div class="w-full">
        <div class="space-y-3">
            @forelse($investors as $inv)
                @php
                    $name = $inv->full_name ?? ($inv->name ?? '-');
                    $email = $inv->email ?? '-';
                    $phone = $inv->phone ?? '-';
                    // show first two characters of the name (multibyte-safe), no spaces
                    $clean = preg_replace('/\s+/u', '', trim((string)$name));
                    $initials = $clean ? mb_strtoupper(mb_substr($clean, 0, 2, 'UTF-8'), 'UTF-8') : '';
                @endphp
                <div class="investor-card bg-gray-800/50 border border-gray-700 rounded-md p-3 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        @php
                            // deterministic hue from name
                            $h = $clean ? (crc32($clean) % 360) : 220;
                            $h2 = ($h + 40) % 360;
                            $bg = "background: linear-gradient(135deg, hsl($h,70%,45%), hsl($h2,70%,50%));";
                        @endphp
                        <div class="w-14 h-14 rounded-full flex items-center justify-center text-white font-bold text-lg" style="{{ $bg }}">{{ $initials ?: 'IN' }}</div>
                        <div>
                            <div class="text-white font-semibold">{{ $name }}</div>
                            <div class="text-sm text-gray-400">{{ $email }}</div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.investors.show', ['id' => $inv->id]) }}" data-id="{{ $inv->id }}" class="investor-view-link px-3 py-1 rounded-md bg-cyan-600 hover:bg-cyan-700 text-white text-sm">عرض</a>
                    </div>
                </div>
            @empty
                <div class="text-center text-gray-400 p-3">لا توجد نتائج</div>
            @endforelse
        </div>
    </div>
</div>
