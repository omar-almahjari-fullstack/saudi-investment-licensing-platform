<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TempInvestmentTransactionsSeeder extends Seeder
{
    public function run()
    {
        $invId = '8a3adb2c-eadc-47e2-8064-a46660a43a64';
        $now = Carbon::now();
        // pick an existing user id if available to satisfy DB constraints
        $userId = DB::table('users')->value('id') ?? 1;

        $rows = [
            [
                'id' => (string) Str::uuid(),
                'investment_id' => $invId,
                'user_id' => $userId,
                'type' => 'credit',
                'amount' => 5000.00,
                'note' => 'ايداع أولي من المستثمر',
                'occurred_at' => $now->copy()->subDays(10)->toDateTimeString(),
                'created_at' => $now->copy()->subDays(10)->toDateTimeString(),
                'updated_at' => $now->copy()->subDays(10)->toDateTimeString(),
            ],
            [
                'id' => (string) Str::uuid(),
                'investment_id' => $invId,
                'user_id' => $userId,
                'type' => 'credit',
                'amount' => 2000.00,
                'note' => 'دفعة إضافية',
                'occurred_at' => $now->copy()->subDays(5)->toDateTimeString(),
                'created_at' => $now->copy()->subDays(5)->toDateTimeString(),
                'updated_at' => $now->copy()->subDays(5)->toDateTimeString(),
            ],
            [
                'id' => (string) Str::uuid(),
                'investment_id' => $invId,
                'user_id' => $userId,
                'type' => 'debit',
                'amount' => 1500.00,
                'note' => 'سحب جزئي',
                'occurred_at' => $now->copy()->subDays(2)->toDateTimeString(),
                'created_at' => $now->copy()->subDays(2)->toDateTimeString(),
                'updated_at' => $now->copy()->subDays(2)->toDateTimeString(),
            ],
            [
                'id' => (string) Str::uuid(),
                'investment_id' => $invId,
                'user_id' => $userId,
                'type' => 'credit',
                'amount' => 1000.00,
                'note' => 'عوائد شهرية',
                'occurred_at' => $now->copy()->subDay()->toDateTimeString(),
                'created_at' => $now->copy()->subDay()->toDateTimeString(),
                'updated_at' => $now->copy()->subDay()->toDateTimeString(),
            ],
        ];

        foreach ($rows as $r) {
            // avoid duplicate id if exists
            if (!DB::table('investment_transactions')->where('id', $r['id'])->exists()) {
                DB::table('investment_transactions')->insert($r);
                $this->command->info('Inserted transaction: ' . $r['id'] . ' amount: ' . $r['amount']);
            }
        }

        $this->command->info('TempInvestmentTransactionsSeeder complete.');
    }
}
