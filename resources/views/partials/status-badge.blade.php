@php
    $statusConfig = [
        'pending' => ['color' => 'bg-yellow-500', 'text' => 'معلق', 'icon' => 'clock'],
    'active' => ['color' => 'bg-orange-500', 'text' => 'نشط', 'icon' => 'check-circle'],
        'approved' => ['color' => 'bg-green-500', 'text' => 'موافق عليه', 'icon' => 'check-circle'],
        'rejected' => ['color' => 'bg-red-500', 'text' => 'مرفوض', 'icon' => 'alert-circle'],
    'completed' => ['color' => 'bg-blue-500', 'text' => 'مكتمل', 'icon' => 'check-circle']
    ];

    $config = $statusConfig[$status] ?? ['color' => 'bg-gray-500', 'text' => $status, 'icon' => 'activity'];
@endphp

<span class="{{ $config['color'] }} text-white px-2 py-1 rounded-full text-xs flex items-center gap-1">
    <i data-lucide="{{ $config['icon'] }}" class="w-3 h-3"></i>
    {{ $config['text'] }}
</span>
