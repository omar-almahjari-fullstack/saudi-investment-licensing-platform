<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;

class SeedThreeInvestments extends Command
{
    protected $signature = 'seed:three-investments {--email= : User email to seed investments for} {--months=6 : How many months of earnings to create per investment}';
    protected $description = 'Create three demo investments for a user and seed monthly earnings for each (demo only).';

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
            $this->error('User not found: ' . $email);
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
            $investor = DB::table('investors')->where('id', $investorId)->first();
            $this->info('Created investor: ' . $investorId);
        }

        // create up to 3 investments (if less than 3 exist)
        $existing = DB::table('investments')->where('investor_id', $investor->id)->count();
        $needed = max(0, 3 - $existing);

        $created = 0;
        $amounts = [5000, 15000, 30000];
        $rates = [6.0, 8.5, 12.0];
        $durations = [6, 12, 12];

        for ($i = 0; $i < 3; $i++) {
            // if an investment exists at this index, skip creating
            $invAtIndex = DB::table('investments')->where('investor_id', $investor->id)->skip($i)->take(1)->first();
            if ($invAtIndex) { continue; }

            $invId = (string) Str::uuid();
            $now = Carbon::now()->subMonths(3); // set created 3 months ago so chart shows historical data
            $amount = $amounts[$i] ?? 10000;
            $duration = $durations[$i] ?? 12;
            $profitRate = $rates[$i] ?? 10.0;
            $expectedReturn = round($amount * ($profitRate / 100), 2);

            DB::table('investments')->insert([
                'id' => $invId,
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

            // seed monthly earnings for this investment starting at approved_at
            $perMonth = round($expectedReturn / max(1, $duration), 2);
            for ($m = 1; $m <= $months; $m++) {
                $when = Carbon::parse($now)->addMonths($m - 1);
                DB::table('investor_earnings')->insert([
                    'id' => (string) Str::uuid(),
                    'investment_id' => $invId,
                    'investor_id' => $investor->id,
                    'month_number' => $m,
                    'profit_amount' => $perMonth,
                    'created_at' => $when,
                ]);
            }
            $created++;
            $this->info('Created investment '.$invId.' with amount '.$amount.' and seeded '.$months.' months of earnings');
        }

        if ($created === 0) {
            $this->info('No new investments needed; existing investments are 3 or more.');
        } else {
            $this->info("Created {$created} investments and seeded earnings for user {$email}.");
        }

        return 0;
    }
}
