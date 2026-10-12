<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WithdrawalsController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $q = DB::table('withdrawal_requests')
            ->leftJoin('users','withdrawal_requests.user_id','=','users.id')
            ->select('withdrawal_requests.*','users.full_name as user_full_name','users.email as user_email')
            ->orderByDesc('withdrawal_requests.created_at');
        if ($status) { $q->where('withdrawal_requests.status',$status); }
        $withdrawals = $q->paginate(25)->appends(['status'=>$status]);
        return view('admin.withdrawals.index', compact('withdrawals','status'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'status' => 'required|string|in:pending,approved,rejected,paid',
            'reason' => 'sometimes|nullable|string'
        ]);
        $w = DB::table('withdrawal_requests')->where('id',$id)->first();
        if (!$w) { abort(404); }
        $now = now();
        DB::table('withdrawal_requests')->where('id',$id)->update([
            'status' => $data['status'],
            'reason' => $data['reason'] ?? $w->reason,
            'processed_at' => in_array($data['status'], ['approved','rejected','paid']) ? $now : null,
            'updated_at' => $now,
        ]);
        return back()->with('status','withdrawal_updated');
    }

    public function pay($id)
    {
        $w = DB::table('withdrawal_requests')->where('id',$id)->first();
        if (!$w) { abort(404); }
        if ($w->status === 'paid') { return back()->with('status','already_paid'); }
        if (!in_array($w->status, ['approved','pending'])) { return back()->with('error','invalid_state'); }

        $now = now();
        $paymentId = (string) Str::uuid();
        $reference = 'WD-' . strtoupper(Str::random(10));

        DB::transaction(function() use ($w, $now, $paymentId, $reference, $id) {
            DB::table('payments')->insert([
                'id' => $paymentId,
                'user_id' => $w->user_id,
                'investment_id' => $w->investment_id,
                'withdrawal_request_id' => $w->id,
                'payment_method_id' => null,
                'amount' => $w->amount,
                'currency' => 'USD',
                'status' => 'completed',
                'transaction_reference' => $reference,
                'processed_at' => $now,
                'note' => 'Withdrawal payout (admin UI)',
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $account = DB::table('platform_accounts')
                ->where('type','main')->where('is_active',1)
                ->orderBy('created_at','asc')->first();
            if ($account) {
                $before = (float) $account->balance;
                $after = $before - (float) $w->amount;
                DB::table('platform_accounts')->where('id',$account->id)->update([
                    'balance' => $after,
                    'updated_at' => $now,
                ]);
                DB::table('platform_transactions')->insert([
                    'id' => (string) Str::uuid(),
                    'platform_account_id' => $account->id,
                    'payment_id' => $paymentId,
                    'withdrawal_request_id' => $w->id,
                    'investment_transaction_id' => null,
                    'type' => 'debit',
                    'amount' => $w->amount,
                    'balance_before' => $before,
                    'balance_after' => $after,
                    'description' => 'Withdrawal payout processed (admin UI)',
                    'transaction_date' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            DB::table('withdrawal_requests')->where('id',$id)->update([
                'status' => 'paid',
                'processed_at' => $now,
                'updated_at' => $now,
            ]);
        });

        return back()->with('status','withdrawal_paid');
    }
}
