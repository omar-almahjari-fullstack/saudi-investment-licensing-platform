@php $users = $users ?? []; @endphp
<div class="w-full overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-700 text-sm text-white">
        <thead class="bg-gray-800 hidden sm:table-header-group">
            <tr>
                <th class="px-3 py-2 text-left">ID</th>
                <th class="px-3 py-2 text-left">الاسم</th>
                <th class="px-3 py-2 text-left">البريد</th>
                <th class="px-3 py-2 text-left">الدور</th>
                <th class="px-3 py-2 text-left">الإجراءات</th>
            </tr>
        </thead>
        <tbody class="bg-transparent">
            @forelse($users as $u)
                <div class="sm:hidden bg-gray-800/40 border border-gray-700 rounded-md p-3 mb-2">
                    <div class="flex items-center justify-between mb-1">
                        <div class="text-sm text-gray-300 font-semibold">{{ $u->full_name ?? '-' }}</div>
                        <div class="text-xs text-gray-400">ID: <span class="font-mono">{{ $u->id }}</span></div>
                    </div>
                    <div class="text-xs text-gray-400">البريد: <span class="text-white">{{ $u->email ?? '-' }}</span></div>
                    <div class="text-xs text-gray-400">الدور: <span class="text-white">{{ $u->role ?? '-' }}</span></div>
                    <div class="mt-2 text-right"><a href="#" class="text-cyan-300 text-sm btn-user-edit" data-user-id="{{ $u->id }}">تعديل</a></div>
                </div>

                <tr class="hidden sm:table-row hover:bg-gray-900/50">
                    <td class="px-3 py-2">{{ $u->id }}</td>
                    <td class="px-3 py-2">{{ $u->full_name ?? '-' }}</td>
                    <td class="px-3 py-2">{{ $u->email ?? '-' }}</td>
                    <td class="px-3 py-2">{{ $u->role ?? '-' }}</td>
                    <td class="px-3 py-2"><a href="#" class="text-cyan-300 text-sm btn-user-edit" data-user-id="{{ $u->id }}">تعديل</a></td>
                </tr>
            @empty
                <tr><td colspan="5" class="p-3 text-center text-gray-400">لا توجد نتائج</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
