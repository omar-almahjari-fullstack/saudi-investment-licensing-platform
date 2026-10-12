<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\PaymentConfirmed;
use App\Mail\PaymentRejected;

class PaymentsController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $status = $request->query('status');
        $method = $request->query('method');
        $hasReceipt = $request->boolean('has_receipt');

        $paymentsQuery = DB::table('payments')
            ->leftJoin('users','payments.user_id','=','users.id')
            ->leftJoin('payment_methods','payments.payment_method_id','=','payment_methods.id')
            ->select('payments.*','users.full_name as user_full_name','payment_methods.name as method_name')
            ->orderByDesc('payments.created_at');

        if ($q !== '') {
            $paymentsQuery->where(function($w) use ($q){
                $w->where('payments.transaction_reference','like','%'.$q.'%')
                  ->orWhere('users.full_name','like','%'.$q.'%')
                  ->orWhere('payments.note','like','%'.$q.'%');
            });
        }
        if ($status) { $paymentsQuery->where('payments.status', $status); }
        if ($method) { $paymentsQuery->where('payment_methods.name', $method); }
        if ($hasReceipt) {
            // note JSON contains receipt_path when a bank receipt was uploaded
            $paymentsQuery->whereNotNull('payments.note')->where('payments.note','like','%receipt_path%');
        }

        $payments = $paymentsQuery->paginate(25)->appends($request->query());

        // Pending bank transfers (to review quickly)
        $pendingBank = DB::table('payments')
            ->leftJoin('users','payments.user_id','=','users.id')
            ->leftJoin('payment_methods','payments.payment_method_id','=','payment_methods.id')
            ->select('payments.*','users.full_name as user_full_name','payment_methods.name as method_name')
            ->where('payments.status','pending')
            ->where('payment_methods.name','Bank Transfer')
            ->orderBy('payments.created_at','asc')
            ->limit(50)
            ->get();

        // For filter dropdowns
        $methods = DB::table('payment_methods')->orderBy('name')->pluck('name');

        return view('admin.payments.index', compact('payments','pendingBank','methods','status','method','q','hasReceipt'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|string|exists:users,id',
            'amount' => 'required|numeric|min:0',
            'currency' => 'sometimes|string|max:10',
            'status' => 'required|string|in:pending,completed,failed,refunded',
            'payment_method_id' => 'nullable|string|exists:payment_methods,id',
            'investment_id' => 'nullable|string|exists:investments,id',
            'withdrawal_request_id' => 'nullable|string|exists:withdrawal_requests,id',
            'note' => 'nullable|string',
        ]);

        $now = now();
        $paymentId = (string) Str::uuid();
        $reference = 'TX-' . strtoupper(Str::random(10));

        DB::transaction(function() use ($data, $now, $paymentId, $reference) {
            DB::table('payments')->insert([
                'id' => $paymentId,
                'user_id' => $data['user_id'],
                'investment_id' => $data['investment_id'] ?? null,
                'withdrawal_request_id' => $data['withdrawal_request_id'] ?? null,
                'payment_method_id' => $data['payment_method_id'] ?? null,
                'amount' => $data['amount'],
                'currency' => $data['currency'] ?? 'USD',
                'status' => $data['status'],
                'transaction_reference' => $reference,
                'processed_at' => $data['status'] === 'completed' ? $now : null,
                'note' => $data['note'] ?? null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $this->maybePostPlatformTransaction(
                $paymentId,
                $data['status'],
                $data['amount'],
                $data['withdrawal_request_id'] ?? null
            );
        });

        return back()->with('status','payment_created');
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'status' => 'required|string|in:pending,completed,failed,refunded',
            'note' => 'sometimes|nullable|string',
        ]);
        $payment = DB::table('payments')->where('id',$id)->first();
        if (!$payment) { abort(404); }
        $toCompleted = $payment->status !== 'completed' && $data['status'] === 'completed';

    DB::transaction(function() use ($id, $data, $payment, $toCompleted) {
            DB::table('payments')->where('id',$id)->update([
                'status' => $data['status'],
                'note' => $data['note'] ?? $payment->note,
                'processed_at' => $data['status'] === 'completed' ? now() : $payment->processed_at,
                'updated_at' => now(),
            ]);

            $this->maybePostPlatformTransaction(
                $id,
                $data['status'],
                (float) $payment->amount,
                $payment->withdrawal_request_id
            );

            // On completing a payment linked to an investment, post credit transaction and activate investment
            if ($toCompleted && $payment->investment_id) {
                $now = now();
                // Avoid duplicate credit for same payment id
                $exists = DB::table('investment_transactions')
                    ->where('investment_id', $payment->investment_id)
                    ->where('note', 'like', '%payment: '.$id.'%')
                    ->exists();
                if (!$exists) {
                    DB::table('investment_transactions')->insert([
                        'id' => (string) Str::uuid(),
                        'investment_id' => $payment->investment_id,
                        'user_id' => $payment->user_id,
                        'type' => 'credit',
                        'amount' => (float) $payment->amount,
                        'note' => 'Initial investment funding (payment: '.$id.')',
                        'occurred_at' => $now,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
                // Activate investment
                DB::table('investments')->where('id',$payment->investment_id)->update([
                    'status' => 'active',
                    'updated_at' => now(),
                ]);
            }
        });

        // Email user if payment moved to completed or failed
        if ($toCompleted) {
            try {
                $user = DB::table('users')->where('id', $payment->user_id)->first();
                if ($user && $user->email) {
                    $userName = $user->full_name ?? ($user->username ?? 'عميل');
                    Mail::to($user->email)->send(new PaymentConfirmed($userName, (float)$payment->amount, $payment->currency ?? 'SAR', $payment->transaction_reference, $payment->investment_id));
                }
            } catch (\Throwable $e) {}

            // Platform ledger credit for completed payments
            try {
                $account = DB::table('platform_accounts')
                    ->where('type','main')->where('is_active',1)
                    ->orderBy('created_at','asc')
                    ->first();
                if ($account) {
                    $before = (float) $account->balance;
                    $after = $before + (float) $payment->amount;
                    DB::table('platform_accounts')->where('id',$account->id)->update([
                        'balance' => $after,
                        'updated_at' => now(),
                    ]);
                    DB::table('platform_transactions')->insert([
                        'id' => (string) Str::uuid(),
                        'platform_account_id' => $account->id,
                        'payment_id' => $payment->id,
                        'withdrawal_request_id' => null,
                        'investment_transaction_id' => null,
                        'type' => 'credit',
                        'amount' => (float) $payment->amount,
                        'balance_before' => $before,
                        'balance_after' => $after,
                        'description' => 'User payment received',
                        'transaction_date' => now(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            } catch (\Throwable $e) {}
        }
        $toFailed = $payment->status !== 'failed' && $data['status'] === 'failed';
        if ($toFailed) {
            try {
                $user = DB::table('users')->where('id', $payment->user_id)->first();
                if ($user && $user->email) {
                    $userName = $user->full_name ?? ($user->username ?? 'عميل');
                    $reason = $data['note'] ?? null;
                    Mail::to($user->email)->send(new PaymentRejected($userName, (float)$payment->amount, $payment->currency ?? 'SAR', $payment->transaction_reference, $reason));
                }
            } catch (\Throwable $e) {}
        }

        return back()->with('status','payment_updated');
    }

    public function destroy($id)
    {
        // avoid deleting completed payments to keep ledger consistency
        $payment = DB::table('payments')->where('id',$id)->first();
        if (!$payment) { abort(404); }
        if ($payment->status === 'completed') {
            return back()->with('error','cannot_delete_completed_payment');
        }
        DB::table('payments')->where('id',$id)->delete();
        return back()->with('status','payment_deleted');
    }

    private function maybePostPlatformTransaction(string $paymentId, string $status, float $amount, ?string $withdrawalRequestId): void
    {
        if (!in_array($status, ['completed','refunded'])) { return; }

        // idempotency: if a platform transaction already exists for this payment, do nothing
        $exists = DB::table('platform_transactions')->where('payment_id',$paymentId)->exists();
        if ($exists) { return; }

        // Choose main account
        $account = DB::table('platform_accounts')
            ->where('type','main')->where('is_active',1)
            ->orderBy('created_at','asc')
            ->first();
        if (!$account) { return; }

        $type = 'credit';
        if ($withdrawalRequestId && $status === 'completed') {
            // payouts reduce balance
            $type = 'debit';
        } elseif ($status === 'refunded') {
            // refund reduces platform balance
            $type = 'debit';
        }

        $before = (float) $account->balance;
        $after = $type === 'credit' ? $before + $amount : $before - $amount;

        // Update balance and insert transaction atomically
        DB::table('platform_accounts')->where('id',$account->id)->update([
            'balance' => $after,
            'updated_at' => now(),
        ]);

        DB::table('platform_transactions')->insert([
            'id' => (string) Str::uuid(),
            'platform_account_id' => $account->id,
            'payment_id' => $paymentId,
            'withdrawal_request_id' => $withdrawalRequestId,
            'investment_transaction_id' => null,
            'type' => $type,
            'amount' => $amount,
            'balance_before' => $before,
            'balance_after' => $after,
            'description' => $type === 'credit' ? 'User payment received' : 'Payout/refund processed',
            'transaction_date' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
