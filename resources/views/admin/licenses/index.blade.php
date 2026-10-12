@extends('layouts.admin')

@section('content')
<div style="padding:24px;">
  <h1 style="color:#fff;font-size:24px;margin-bottom:12px;">طلبات التراخيص</h1>

  @if (session('status'))
    <div class="glass-card" style="border:1px solid #22c55e;background:rgba(34,197,94,0.08);padding:12px;margin-bottom:12px;color:#d1fae5;">
      {{ session('status') }}
    </div>
  @endif

  <div class="glass-card" style="padding:0;overflow:hidden;">
    <table style="width:100%;border-collapse:collapse;">
      <thead>
        <tr style="background:#0f172a;color:#93c5fd;text-align:right;">
          <th style="padding:10px;border-bottom:1px solid #1f2937;">رقم الطلب</th>
          <th style="padding:10px;border-bottom:1px solid #1f2937;">العميل</th>
          <th style="padding:10px;border-bottom:1px solid #1f2937;">النشاط</th>
          <th style="padding:10px;border-bottom:1px solid #1f2937;">النوع</th>
          <th style="padding:10px;border-bottom:1px solid #1f2937;">الحالة</th>
          <th style="padding:10px;border-bottom:1px solid #1f2937;">التاريخ</th>
          <th style="padding:10px;border-bottom:1px solid #1f2937;">إجراءات</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($licenses as $l)
          <tr style="color:#cbd5e1;border-bottom:1px solid #1f2937;">
            <td style="padding:10px;font-family:monospace;">{{ $l->id }}</td>
            <td style="padding:10px;">
              <div style="font-weight:600;color:#fff;">{{ $l->user_name ?? 'مستخدم' }}</div>
              <div style="font-size:12px;color:#9ca3af;">{{ $l->user_email }}</div>
            </td>
            <td style="padding:10px;">{{ $l->business_name }}</td>
            <td style="padding:10px;">{{ $l->business_type }}</td>
            <td style="padding:10px;">
              @php
                $color = match($l->status){
                  'approved' => '#22c55e',
                  'rejected' => '#ef4444',
                  default => '#f59e0b'
                };
              @endphp
              <span style="padding:2px 8px;border-radius:999px;background:{{ $color }}22;color:{{ $color }};font-size:12px;">{{ $l->status }}</span>
            </td>
            <td style="padding:10px;">{{ \Carbon\Carbon::parse($l->created_at)->format('Y-m-d H:i') }}</td>
            <td style="padding:10px;white-space:nowrap;">
              <form method="POST" action="{{ route('admin.licenses.update', $l->id) }}" style="display:inline-block;margin-left:6px;">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="approved" />
                <button class="btn-primary" style="padding:6px 10px;font-size:12px;">اعتماد</button>
              </form>
              <form method="POST" action="{{ route('admin.licenses.update', $l->id) }}" style="display:inline-block;">
                @csrf
                @method('PUT')
                <input type="hidden" name="status" value="rejected" />
                <button class="btn-secondary" style="padding:6px 10px;font-size:12px;border-color:#ef4444;color:#ef4444;">رفض</button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" style="padding:16px;color:#9ca3af;text-align:center;">لا توجد طلبات حالياً</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
