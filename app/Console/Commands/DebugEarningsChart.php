<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;

class DebugEarningsChart extends Command
{
    protected $signature = 'debug:earnings {--email= : user email}';
    protected $description = 'Debug printed monthlyLabels and monthlyValues for an investor by email';

    public function handle()
    {
        $email = $this->option('email') ?? 'ashrf_tk@gmail.com';

        $user = DB::table('users')->where('email', $email)->first();
        if (!$user) { $this->error('User not found: ' . $email); return 1; }

        $investor = DB::table('investors')->where('user_id', $user->id)->first();
        if (!$investor) { $this->error('Investor profile not found for user: ' . $email); return 1; }

        if (!Schema::hasTable('investor_earnings')) { $this->error('investor_earnings table missing'); return 1; }

        $earnings = DB::table('investor_earnings')->where('investor_id', $investor->id)->orderBy('investment_id')->orderBy('month_number')->get();
        $this->info('earnings_count: ' . $earnings->count());

        // Build last 12 months labels and aggregated values like the controller
        $now = Carbon::now();
        $start = (clone $now)->startOfMonth()->subMonths(11);
        $period = [];
        for ($i = 0; $i < 12; $i++) { $m = (clone $start)->addMonths($i); $period[] = $m; }

        $monthlyMap = [];
        $monthlyLabels = [];
        foreach ($period as $m) { $k = $m->format('Y-m'); $monthlyMap[$k] = 0.0; $monthlyLabels[] = $m->format('M Y'); }

        foreach ($earnings as $e) {
            try { $dt = Carbon::parse($e->created_at); } catch (\Throwable $ex) { $dt = Carbon::now(); }
            $k = $dt->format('Y-m'); if (isset($monthlyMap[$k])) { $monthlyMap[$k] += (float) $e->profit_amount; }
        }

        $monthlyValues = array_values(array_map(function($v){ return round($v,2); }, $monthlyMap));

        $this->line('monthlyLabels: ' . json_encode($monthlyLabels, JSON_UNESCAPED_UNICODE));
        $this->line('monthlyValues: ' . json_encode($monthlyValues));

        // Also show a small sample of earnings rows
        $sample = $earnings->take(6)->map(function($r){ return ['id'=>$r->id,'investment_id'=>$r->investment_id,'month'=>$r->month_number,'amt'=> (float)$r->profit_amount,'created_at'=>$r->created_at]; });
        $this->line('sample_rows: ' . json_encode($sample));

        return 0;
    }
}
