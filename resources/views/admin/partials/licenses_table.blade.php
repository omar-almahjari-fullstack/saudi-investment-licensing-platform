@php $licenses = $licenses ?? []; @endphp
<div class="w-full overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-700 text-sm text-white">
        <thead class="bg-gray-800 hidden sm:table-header-group">
            <tr>
                <th class="px-3 py-2 text-left">ID</th>
                <th class="px-3 py-2 text-left">المنشأة</th>
                <th class="px-3 py-2 text-left">النشاط</th>
                <th class="px-3 py-2 text-left">الموقع</th>
                <th class="px-3 py-2 text-left">الإجراءات</th>
            </tr>
        </thead>
        <tbody class="bg-transparent">
            @forelse($licenses as $it)
                <div class="sm:hidden bg-gray-800/40 border border-gray-700 rounded-md p-3 mb-2">
                    <div class="flex items-center justify-between mb-1">
                        <div class="text-sm text-gray-300 font-semibold">{{ $it->business_name ?? ('#' . $it->id) }}</div>
                        <div class="text-xs text-gray-400">ID: <span class="font-mono">{{ $it->id }}</span></div>
                    </div>
                    <div class="text-xs text-gray-400">النشاط: <span class="text-white">{{ $it->business_type ?? '-' }}</span></div>
                    <div class="text-xs text-gray-400">الموقع: <span class="text-white">{{ $it->location ?? '-' }}</span></div>
                    <div class="mt-2 text-right"><a href="#" class="text-cyan-300 text-sm" data-action="view-license" data-id="{{ $it->id }}">عرض</a></div>
                </div>

                <tr class="hidden sm:table-row hover:bg-gray-900/50">
                    <td class="px-3 py-2">{{ $it->id }}</td>
                    <td class="px-3 py-2">{{ $it->business_name ?? '-' }}</td>
                    <td class="px-3 py-2">{{ $it->business_type ?? '-' }}</td>
                    <td class="px-3 py-2">{{ $it->location ?? '-' }}</td>
                    <td class="px-3 py-2"><a href="#" class="text-cyan-300 text-sm" data-action="view-license" data-id="{{ $it->id }}">عرض</a></td>
                </tr>
            @empty
                <tr><td colspan="5" class="p-3 text-center text-gray-400">لا توجد نتائج</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
