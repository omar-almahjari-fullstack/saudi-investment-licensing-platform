@php $documents = $documents ?? []; @endphp
<div class="w-full overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-700 text-sm text-white">
        <thead class="bg-gray-800 hidden sm:table-header-group">
            <tr>
                <th class="px-3 py-2 text-left">ID</th>
                <th class="px-3 py-2 text-left">الملف</th>
                <th class="px-3 py-2 text-left">النوع</th>
                <th class="px-3 py-2 text-left">المستخدم</th>
                <th class="px-3 py-2 text-left">الإجراءات</th>
            </tr>
        </thead>
        <tbody class="bg-transparent">
            @forelse($documents as $d)
                <div class="sm:hidden bg-gray-800/40 border border-gray-700 rounded-md p-3 mb-2">
                    <div class="flex items-center justify-between mb-1">
                        <div class="text-sm text-gray-300 font-semibold">{{ $d->filename ?? ($d->name ?? '-') }}</div>
                        <div class="text-xs text-gray-400">ID: <span class="font-mono">{{ $d->id }}</span></div>
                    </div>
                    <div class="text-xs text-gray-400">النوع: <span class="text-white">{{ $d->type ?? '-' }}</span></div>
                    <div class="text-xs text-gray-400">المستخدم: <span class="text-white">{{ $d->user_id ?? '-' }}</span></div>
                    <div class="mt-2 text-right"><a href="#" class="text-cyan-300 text-sm" data-action="view-doc" data-id="{{ $d->id }}">عرض</a></div>
                </div>

                <tr class="hidden sm:table-row hover:bg-gray-900/50">
                    <td class="px-3 py-2">{{ $d->id }}</td>
                    <td class="px-3 py-2">{{ $d->filename ?? ($d->name ?? '-') }}</td>
                    <td class="px-3 py-2">{{ $d->type ?? '-' }}</td>
                    <td class="px-3 py-2">{{ $d->user_id ?? '-' }}</td>
                    <td class="px-3 py-2"><a href="#" class="text-cyan-300 text-sm" data-action="view-doc" data-id="{{ $d->id }}">عرض</a></td>
                </tr>
            @empty
                <tr><td colspan="5" class="p-3 text-center text-gray-400">لا توجد نتائج</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
