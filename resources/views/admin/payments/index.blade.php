@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Payments</h1>
    @if(isset($pendingBank) && $pendingBank->count())
    <div class="card mb-4">
            <div class="card-header">Filter</div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.payments.index') }}" class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All</option>
                            <option value="pending" @selected(($status ?? '')==='pending')>pending</option>
                            <option value="completed" @selected(($status ?? '')==='completed')>completed</option>
                            <option value="failed" @selected(($status ?? '')==='failed')>failed</option>
                            <option value="refunded" @selected(($status ?? '')==='refunded')>refunded</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Method</label>
                        <select name="method" class="form-select">
                            <option value="">All</option>
                            @foreach(($methods ?? []) as $m)
                                <option value="{{ $m }}" @selected(($method ?? '')===$m)>{{ $m }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Search</label>
                        <input type="text" name="q" class="form-control" value="{{ $q ?? '' }}" placeholder="Ref, name, note...">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100">Apply</button>
                    </div>
                    <div class="col-md-2">
                        <div class="form-check mt-4">
                            <input type="checkbox" name="has_receipt" value="1" id="filter-has-receipt" class="form-check-input" @checked(($hasReceipt ?? false))>
                            <label for="filter-has-receipt" class="form-check-label">Has receipt</label>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card-header">Pending Bank Transfers</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Ref</th>
                            <th>User</th>
                            <th>Amount</th>
                            <th>Submitted</th>
                            <th>Receipt</th>
                            <th>Meta</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingBank as $p)
                        <tr>
                            <td>{{ $p->transaction_reference }}</td>
                            <td>{{ $p->user_full_name ?? $p->user_id }}</td>
                            <td>{{ number_format($p->amount, 2) }} {{ $p->currency }}</td>
                            <td>{{ $p->created_at }}</td>
                            <td>
                                @php
                                    $meta = null;
                                    try { $meta = $p->note ? json_decode($p->note, true) : null; } catch (\Throwable $e) { $meta = null; }
                                    $receiptPath = $meta['receipt_path'] ?? null;
                                    $receiptUrl = $receiptPath ? asset('storage/'.ltrim($receiptPath,'/')) : null;
                                    $ext = $receiptPath ? strtolower(pathinfo($receiptPath, PATHINFO_EXTENSION)) : null;
                                    $isImg = in_array($ext, ['jpg','jpeg','png','gif','webp']);
                                @endphp
                                @if($receiptUrl)
                                    <a href="{{ $receiptUrl }}" target="_blank" class="btn btn-sm btn-outline-secondary me-2">View</a>
                                    <a href="{{ $receiptUrl }}" download class="btn btn-sm btn-outline-secondary me-2">Download</a>
                                    @if($isImg)
                                        <a href="{{ $receiptUrl }}" target="_blank"><img src="{{ $receiptUrl }}" alt="receipt" style="width:64px;height:40px;object-fit:cover;border-radius:4px;border:1px solid #ddd"></a>
                                    @endif
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                @if($meta)
                                    <div class="small text-muted">
                                        @if(!empty($meta['sender_name']))<div>Sender: {{ $meta['sender_name'] }}</div>@endif
                                        @if(!empty($meta['transfer_at']))<div>Date: {{ $meta['transfer_at'] }}</div>@endif
                                        @if(!empty($meta['reference']))<div>Ref: {{ $meta['reference'] }}</div>@endif
                                    </div>
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <form method="POST" action="{{ route('admin.payments.update', $p->id) }}" onsubmit="return confirm('Confirm this bank transfer?');">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="completed">
                                        <button class="btn btn-sm btn-success">Mark Completed</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.payments.update', $p->id) }}" onsubmit="return confirm('Reject this bank transfer?');" class="d-flex align-items-center gap-1">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="failed">
                                        <input type="text" name="note" class="form-control form-control-sm" placeholder="Reason" style="width:160px;">
                                        <button class="btn btn-sm btn-outline-danger">Reject</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card mb-4">
        <div class="card-header">Create Payment (demo)</div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.payments.store') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">User ID</label>
                        <input type="text" name="user_id" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Amount</label>
                        <input type="number" step="0.01" name="amount" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Currency</label>
                        <input type="text" name="currency" class="form-control" value="USD">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Payment Method ID</label>
                        <input type="text" name="payment_method_id" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="pending">pending</option>
                            <option value="completed">completed</option>
                            <option value="failed">failed</option>
                            <option value="refunded">refunded</option>
                        </select>
                    </div>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-md-3">
                        <label class="form-label">Investment ID (optional)</label>
                        <input type="text" name="investment_id" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Withdrawal Request ID (optional)</label>
                        <input type="text" name="withdrawal_request_id" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Note</label>
                        <input type="text" name="note" class="form-control">
                    </div>
                </div>
                <div class="mt-3">
                    <button class="btn btn-primary">Create</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Payments List</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Ref</th>
                            <th>User</th>
                            <th>Method</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Receipt</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($payments as $p)
                        <tr>
                            <td>{{ $p->transaction_reference }}</td>
                            <td>{{ $p->user_full_name ?? $p->user_id }}</td>
                            <td>{{ $p->method_name ?? '-' }}</td>
                            <td>{{ number_format($p->amount, 2) }} {{ $p->currency }}</td>
                            <td>
                                <form method="POST" action="{{ route('admin.payments.update', $p->id) }}" class="d-flex align-items-center gap-2">
                                    @csrf
                                    @method('PUT')
                                    <select name="status" class="form-select form-select-sm" style="width: 140px;">
                                        <option value="pending" @selected($p->status==='pending')>pending</option>
                                        <option value="completed" @selected($p->status==='completed')>completed</option>
                                        <option value="failed" @selected($p->status==='failed')>failed</option>
                                        <option value="refunded" @selected($p->status==='refunded')>refunded</option>
                                    </select>
                                    <button class="btn btn-sm btn-outline-primary">Save</button>
                                </form>
                            </td>
                            <td>{{ $p->created_at }}</td>
                            <td>
                                @php
                                    $meta = null;
                                    try { $meta = $p->note ? json_decode($p->note, true) : null; } catch (\Throwable $e) { $meta = null; }
                                    $receiptPath = is_array($meta) ? ($meta['receipt_path'] ?? null) : null;
                                    $receiptUrl = $receiptPath ? asset('storage/'.ltrim($receiptPath,'/')) : null;
                                    $ext = $receiptPath ? strtolower(pathinfo($receiptPath, PATHINFO_EXTENSION)) : null;
                                    $isImg = in_array($ext, ['jpg','jpeg','png','gif','webp']);
                                @endphp
                                @if($receiptUrl)
                                    <a href="{{ $receiptUrl }}" target="_blank" class="btn btn-sm btn-outline-secondary me-2">View</a>
                                    <a href="{{ $receiptUrl }}" download class="btn btn-sm btn-outline-secondary me-2">Download</a>
                                    @if($isImg)
                                        <a href="{{ $receiptUrl }}" target="_blank"><img src="{{ $receiptUrl }}" alt="receipt" style="width:48px;height:32px;object-fit:cover;border-radius:4px;border:1px solid #ddd"></a>
                                    @endif
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                <form method="POST" action="{{ route('admin.payments.destroy', $p->id) }}" onsubmit="return confirm('Delete payment?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $payments->links() }}
        </div>
    </div>
</div>
@endsection
