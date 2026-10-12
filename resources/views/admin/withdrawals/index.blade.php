@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="mb-0">طلبات السحب</h1>
        <div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">رجوع للوحة</a>
        </div>
    </div>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form method="GET" class="mb-3">
        <div class="row g-2">
            <div class="col-auto">
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">كل الحالات</option>
                    @foreach(['pending','approved','rejected','paid'] as $s)
                        <option value="{{ $s }}" @selected($status===$s)>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </form>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>المستخدم</th>
                            <th>المبلغ</th>
                            <th>الحالة</th>
                            <th>تاريخ الطلب</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($withdrawals as $w)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $w->user_full_name ?? $w->user_id }}</div>
                                <div class="text-muted small">{{ $w->user_email }}</div>
                            </td>
                            <td>{{ number_format($w->amount,2) }} USD</td>
                            <td>{{ $w->status }}</td>
                            <td>{{ $w->created_at }}</td>
                            <td>
                                <div class="d-flex gap-2">
                                    <form method="POST" action="{{ route('admin.withdrawals.update',$w->id) }}">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="approved">
                                        <button class="btn btn-sm btn-outline-primary" @disabled($w->status==='approved' || $w->status==='paid')>اعتماد</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.withdrawals.update',$w->id) }}">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="rejected">
                                        <button class="btn btn-sm btn-outline-warning" @disabled($w->status==='rejected' || $w->status==='paid')>رفض</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.withdrawals.pay',$w->id) }}" onsubmit="return confirm('تنفيذ الدفع؟');">
                                        @csrf
                                        <button class="btn btn-sm btn-success" @disabled($w->status==='paid')>دفع</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $withdrawals->links() }}
        </div>
    </div>
    <div class="text-muted small mt-2">ملاحظة: الدفع يخصم من رصيد حساب المنصة الرئيسي ويُسجل قيد محاسبي.</div>
</div>
@endsection
