<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class SeedHistoricalEarnings extends Command
{
    protected $signature = 'seed:historical-earnings {--email= : user email} {--months=12 : number of months to seed} {--amt=1000 : amount per month}';
    protected $description = 'Seed historical monthly earnings for the last N months for a user (demo)';

    public function handle()
    {
        $email = $this->option('email') ?? 'ashrf_tk@gmail.com';
        $months = (int) $this->option('months');
        $amt = (float) $this->option('amt');

        if (!Schema::hasTable('investor_earnings')) { $this->error('investor_earnings table missing'); return 1; }

        $user = DB::table('users')->where('email', $email)->first();
        if (!$user) { $this->error('User not found: ' . $email); return 1; }

        $investor = DB::table('investors')->where('user_id', $user->id)->first();
        if (!$investor) { $this->error('Investor profile not found; creating one.'); $investorId = (string) Str::uuid(); DB::table('investors')->insert(['id'=>$investorId,'user_id'=>$user->id,'created_at'=>Carbon::now(),'updated_at'=>Carbon::now()]); $investor = DB::table('investors')->where('id',$investorId)->first(); }

        // pick an existing investment id if available, otherwise use a generated id
        $inv = DB::table('investments')->where('investor_id', $investor->id)->first();
        $investmentId = $inv ? $inv->id : (string) Str::uuid();

        $now = Carbon::now();
        $start = (clone $now)->startOfMonth()->subMonths($months - 1);

        $inserted = 0;
        for ($i = 0; $i < $months; $i++) {
            $when = (clone $start)->addMonths($i)->addDays(1); // day 1+ to avoid timezone edges
            DB::table('investor_earnings')->insert([
                'id' => (string) Str::uuid(),
                'investment_id' => $investmentId,
                'investor_id' => $investor->id,
                'month_number' => $i + 1,
                'profit_amount' => $amt,
                'created_at' => $when,
            ]);
            $inserted++;
        }

        $this->info("Inserted {$inserted} historical earnings for {$email} starting {$start->toDateString()}");
        return 0;
    }
}
