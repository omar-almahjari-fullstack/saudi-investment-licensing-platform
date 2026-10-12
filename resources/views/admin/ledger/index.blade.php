@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h1 class="mb-0">دفتر المنصة (Ledger)</h1>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">رجوع للوحة</a>
    </div>

    <div class="card mb-4">
        <div class="card-header">الحسابات</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                      <tr>
                        <th>الاسم</th>
                        <th>النوع</th>
                        <th>الرصيد</th>
                        <th>العملة</th>
                        <th>نشط</th>
                      </tr>
                    </thead>
                    <tbody>
                    @foreach($accounts as $a)
                      <tr>
                        <td>{{ $a->name }}</td>
                        <td>{{ $a->type }}</td>
                        <td>{{ number_format($a->balance, 2) }}</td>
                        <td>{{ $a->currency }}</td>
                        <td>{{ $a->is_active ? 'نعم' : 'لا' }}</td>
                      </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">آخر 50 حركة</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                      <tr>
                        <th>التاريخ</th>
                        <th>النوع</th>
                        <th>المبلغ</th>
                        <th>قبل</th>
                        <th>بعد</th>
                        <th>مرجع</th>
                        <th>الوصف</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($transactions as $t)
                        <tr>
                          <td>{{ $t->transaction_date }}</td>
                          <td>{{ $t->type }}</td>
                          <td>{{ number_format($t->amount,2) }}</td>
                          <td>{{ number_format($t->balance_before,2) }}</td>
                          <td>{{ number_format($t->balance_after,2) }}</td>
                          <td>{{ $t->payment_ref ?? '-' }}</td>
                          <td>{{ $t->description }}</td>
                        </tr>
                      @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
