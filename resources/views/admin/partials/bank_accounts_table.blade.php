@php $banks = $banks ?? []; @endphp
<div class="w-full overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-700 text-sm text-white">
        <thead class="bg-gray-800 hidden sm:table-header-group">
            <tr>
                <th class="px-3 py-2 text-left">ID</th>
                <th class="px-3 py-2 text-left">اسم الحساب</th>
                <th class="px-3 py-2 text-left">البنك</th>
                <th class="px-3 py-2 text-left">رقم الحساب</th>
                <th class="px-3 py-2 text-left">الإجراءات</th>
            </tr>
        </thead>
        <tbody class="bg-transparent">
            @forelse($banks as $b)
                <div class="sm:hidden bg-gray-800/40 border border-gray-700 rounded-md p-3 mb-2">
                    <div class="flex items-center justify-between mb-1">
                        <div class="text-sm text-gray-300 font-semibold">{{ $b->account_name ?? ('#' . $b->id) }}</div>
                        <div class="text-xs text-gray-400">ID: <span class="font-mono">{{ $b->id }}</span></div>
                    </div>
                    <div class="text-xs text-gray-400">البنك: <span class="text-white">{{ $b->bank_name ?? '-' }}</span></div>
                    <div class="text-xs text-gray-400">رقم الحساب: <span class="text-white">{{ $b->account_number ?? '-' }}</span></div>
                    <div class="mt-2 text-right"><a href="#" class="text-cyan-300 text-sm" data-action="edit-bank" data-id="{{ $b->id }}">تعديل</a></div>
                </div>

                <tr class="hidden sm:table-row hover:bg-gray-900/50">
                    <td class="px-3 py-2">{{ $b->id }}</td>
                    <td class="px-3 py-2">{{ $b->account_name ?? '-' }}</td>
                    <td class="px-3 py-2">{{ $b->bank_name ?? '-' }}</td>
                    <td class="px-3 py-2">{{ $b->account_number ?? '-' }}</td>
                    <td class="px-3 py-2"><a href="#" class="text-cyan-300 text-sm" data-action="edit-bank" data-id="{{ $b->id }}">تعديل</a></td>
                </tr>
            @empty
                <tr><td colspan="5" class="p-3 text-center text-gray-400">لا توجد نتائج</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
