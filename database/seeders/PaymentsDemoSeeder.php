<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentsDemoSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        // get any active user and a payment method
        $user = DB::table('users')->orderBy('created_at','asc')->first();
        $method = DB::table('payment_methods')->orderBy('created_at','asc')->first();
        if (!$user || !$method) { return; }

        $existing = DB::table('payments')->count();
        if ($existing > 0) { return; }

        // select a main active platform account for balance updates
        $account = DB::table('platform_accounts')
            ->where('type','main')->where('is_active',1)
            ->orderBy('created_at','asc')->first();

        $rows = [
            [ 'amount' => 1500.00, 'status' => 'completed' ],
            [ 'amount' => 250.00, 'status' => 'pending' ],
        ];

        foreach ($rows as $r) {
            $paymentId = (string) Str::uuid();
            DB::table('payments')->insert([
                'id' => $paymentId,
                'user_id' => $user->id,
                'investment_id' => null,
                'withdrawal_request_id' => null,
                'payment_method_id' => $method->id,
                'amount' => $r['amount'],
                'currency' => 'USD',
                'status' => $r['status'],
                'transaction_reference' => 'SEED-' . strtoupper(Str::random(8)),
                'processed_at' => $r['status'] === 'completed' ? $now : null,
                'note' => 'Seeded payment',
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            if ($account && $r['status'] === 'completed') {
                // credit the platform main account and store a transaction
                $before = (float) $account->balance;
                $after = $before + (float) $r['amount'];
                DB::table('platform_accounts')->where('id',$account->id)->update([
                    'balance' => $after,
                    'updated_at' => $now,
                ]);
                DB::table('platform_transactions')->insert([
                    'id' => (string) Str::uuid(),
                    'platform_account_id' => $account->id,
                    'payment_id' => $paymentId,
                    'withdrawal_request_id' => null,
                    'investment_transaction_id' => null,
                    'type' => 'credit',
                    'amount' => $r['amount'],
                    'balance_before' => $before,
                    'balance_after' => $after,
                    'description' => 'Seeded user payment received',
                    'transaction_date' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                // update local account object balance for next iteration
                $account->balance = $after;
            }
        }
    }
}
