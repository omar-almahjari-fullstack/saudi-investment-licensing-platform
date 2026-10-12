<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;

class SeedFakeInvestorEarnings extends Command
{
    protected $signature = 'seed:fake-earnings {--email= : User email to seed earnings for} {--months=6 : Number of months to seed}';
    protected $description = 'Insert fake investor earnings for a user (testing/demo only)';

    public function handle()
    {
        $email = $this->option('email') ?? 'ashrf_tk@gmail.com';
        $months = (int) $this->option('months');

        if (!Schema::hasTable('investor_earnings')) {
            $this->error('Table investor_earnings does not exist. Run migrations first.');
            return 1;
        }

        $user = DB::table('users')->where('email', $email)->first();
        if (!$user) {
            $this->error('User not found with email: ' . $email);
            return 1;
        }

        $investor = DB::table('investors')->where('user_id', $user->id)->first();
        if (!$investor) {
            $investorId = (string) Str::uuid();
            DB::table('investors')->insert([
                'id' => $investorId,
                'user_id' => $user->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            $this->info('Created investor profile: ' . $investorId);
            $investor = DB::table('investors')->where('id', $investorId)->first();
        }

        // Try to find an existing approved investment, otherwise create a dummy one
        $investment = DB::table('investments')->where('investor_id', $investor->id)->orderByDesc('created_at')->first();
        if (!$investment) {
            $investmentId = (string) Str::uuid();
            $amount = 10000;
            $duration = 12;
            $profitRate = 10.0;
            $expectedReturn = round($amount * ($profitRate / 100), 2);
            $now = Carbon::now();
            DB::table('investments')->insert([
                'id' => $investmentId,
                'investor_id' => $investor->id,
                'amount' => $amount,
                'duration_months' => $duration,
                'profit_rate' => $profitRate,
                'expected_return' => $expectedReturn,
                'status' => 'approved',
                'approved_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $this->info('Created dummy investment: ' . $investmentId);
            $investment = DB::table('investments')->where('id', $investmentId)->first();
        }

        // Insert earnings for the requested months starting from approved_at or now
        $approvedAt = $investment->approved_at ?? Carbon::now();
        $perMonth = round((float)($investment->expected_return ?? 0) / max(1, (int)($investment->duration_months ?? 12)), 2);

        $inserted = 0;
        for ($m = 1; $m <= $months; $m++) {
            $when = Carbon::parse($approvedAt)->addMonths($m - 1);
            $id = (string) Str::uuid();
            DB::table('investor_earnings')->insert([
                'id' => $id,
                'investment_id' => $investment->id,
                'investor_id' => $investor->id,
                'month_number' => $m,
                'profit_amount' => $perMonth,
                'created_at' => $when,
            ]);
            $inserted++;
        }

        $this->info("Inserted {$inserted} investor_earnings rows for user {$email} (investor {$investor->id})");
        return 0;
    }
}
