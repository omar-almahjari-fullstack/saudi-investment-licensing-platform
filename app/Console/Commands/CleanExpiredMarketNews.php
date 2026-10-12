<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CleanExpiredMarketNews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'market-insights:clean-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Permanently delete market_news rows whose expires_at is in the past';

    public function handle()
    {
        try {
            $now = Carbon::now();
            $count = DB::table('market_news')->whereNotNull('expires_at')->where('expires_at', '<=', $now)->delete();
            $this->info("Deleted {$count} expired market_news items.");
            Log::info("CleanExpiredMarketNews: deleted {$count} expired market_news items.");
        } catch (\Exception $e) {
            $this->error('Failed to clean expired market news: ' . $e->getMessage());
            Log::error('CleanExpiredMarketNews error: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
