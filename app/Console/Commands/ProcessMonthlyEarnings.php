<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Investment;
use App\Models\InvestorEarning;
use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * Daily processor for monthly investor earnings.
 *
 * Contract:
 * - Inputs: Investments with status='approved' and approved_at set.
 * - For each investment, compute nextMonth = count(earnings)+1 and dueDate = approved_at + (nextMonth-1) months.
 * - If now >= dueDate and nextMonth <= duration_months: insert earning (no duplicates), notify investor.
 * - If nextMonth == duration_months after insert: mark investment as 'completed' and notify completion.
 */
class ProcessMonthlyEarnings extends Command
{
    protected $signature = 'earnings:process-monthly';
    protected $description = 'Process monthly investor earnings and insert due payments for approved investments';

    public function handle()
    {
        $this->info('Processing monthly earnings...');

        $now = Carbon::now();
        // Only process investments that are currently approved (active accruals)
        $investments = Investment::where('status', 'approved')->get();
        foreach ($investments as $inv) {
            // approved_at is the anchor for monthly accrual schedule
            if (empty($inv->approved_at)) continue;
            $approvedAt = Carbon::parse($inv->approved_at);
            $duration = max(1, (int) $inv->duration_months);

            // Count existing earnings for this investment
            $count = InvestorEarning::where('investment_id', $inv->id)->count();
            $nextMonth = $count + 1;
            if ($nextMonth > $duration) continue; // already completed

            // Determine due date for nextMonth
            // Normal mode: approved_at + (nextMonth-1) months
            // Test mode: if EARNINGS_SIM_MINUTES_PER_MONTH is set (e.g., 10), treat each "month" as N minutes
            $simMinutes = (int) (config('earnings.sim_minutes', 0) ?: env('EARNINGS_SIM_MINUTES_PER_MONTH', 0));
            if ($simMinutes > 0) {
                // In test mode treat each "month" as simMinutes, but count from
                // one full unit after approval. So month 1 is approved_at + simMinutes
                $dueDate = (clone $approvedAt)->addMinutes($simMinutes * ($nextMonth));
            } else {
                // Make month 1 due one month after approved_at (approved_at + 1 month),
                // month N due approved_at + N months.
                $dueDate = (clone $approvedAt)->addMonths($nextMonth);
            }

            // if now is on or after dueDate (by date), create record
            if ($now->gte($dueDate)) {
                $expected = (float) ($inv->expected_return ?: 0);
                $perMonth = round($expected / $duration, 2);
                $exists = InvestorEarning::where('investment_id', $inv->id)->where('month_number', $nextMonth)->exists();
                if (!$exists) {
                    $earning = InvestorEarning::create([
                        'id' => (string) Str::uuid(),
                        'investment_id' => $inv->id,
                        'investor_id' => $inv->investor_id,
                        'month_number' => $nextMonth,
                        'profit_amount' => $perMonth,
                        'created_at' => $dueDate->toDateTimeString(),
                    ]);
                    $this->info('Inserted earning for investment ' . $inv->id . ' month ' . $nextMonth);

                    // Notify investor by email (if possible)
                    try {
                        $investor = $inv->investor;
                        if ($investor && !empty($investor->user_id)) {
                            $user = \App\Models\User::find($investor->user_id);
                            if ($user && !empty($user->email) && class_exists(\App\Notifications\InvestorProfitAddedNotification::class)) {
                                $user->notify(new \App\Notifications\InvestorProfitAddedNotification($inv, (int)$nextMonth, (float)$perMonth));
                            }
                        }
                    } catch (\Throwable $e) { /* ignore notify errors */ }

                    // If this was the final month, auto-complete the investment
                    if ($nextMonth >= $duration) {
                        try {
                            $inv->status = 'completed';
                            $inv->save();
                            // Optionally notify investor about completion via existing status notification
                            try {
                                $investor = $inv->investor;
                                if ($investor && !empty($investor->user_id)) {
                                    $user = \App\Models\User::find($investor->user_id);
                                    if ($user && !empty($user->email)) {
                                        // Send status-changed (generic) notification if available
                                        if (class_exists(\App\Notifications\InvestmentStatusChangedNotification::class)) {
                                            $user->notify(new \App\Notifications\InvestmentStatusChangedNotification($inv, 'approved', 'completed'));
                                        }
                                        // Send completion-specific notification with total return
                                        $totalReturn = (float) ($inv->expected_return ?: 0.0);
                                        if (class_exists(\App\Notifications\InvestorInvestmentCompletedNotification::class)) {
                                            $user->notify(new \App\Notifications\InvestorInvestmentCompletedNotification($inv, $totalReturn));
                                        }
                                    }
                                }
                            } catch (\Throwable $e) {}
                        } catch (\Throwable $e) {}
                    }
                }
            }
        }

        $this->info('Done.');
        return 0;
    }
}
