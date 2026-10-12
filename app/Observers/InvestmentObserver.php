<?php

namespace App\Observers;

use App\Models\Investment;
use App\Models\InvestorEarning;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Observer لتحديثات الاستثمارات.
 *
 * عند تغير الحالة إلى approved:
 * - توحيد نسبة الربح وحساب العائد المتوقع (إن لزم).
 * - ختم approved_at إذا لم تكن موجودة.
 * - إنشاء قيد أرباح الشهر الأول (month_number=1) فورًا بدون تكرار.
 * - إرسال إشعار بريد/داخل النظام بإضافة ربح الشهر الأول.
 */
class InvestmentObserver
{
    public function updated(Investment $investment)
    {
        $original = $investment->getOriginal();
        $prevStatus = $original['status'] ?? null;
        $newStatus = $investment->status;

        // If status changed to approved
        if ($prevStatus !== 'approved' && $newStatus === 'approved') {
            // Ensure profit_rate matches expected table based on amount (use highest threshold <= amount)
            $amount = (float) $investment->amount;
            $rateTable = [
                1000 => 15,
                2500 => 20,
                3800 => 25,
                5000 => 27,
                7200 => 29,
                9600 => 31,
                10000 => 33,
                15000 => 35,
                20000 => 37,
                25000 => 39,
                30000 => 41,
                35000 => 43,
                40000 => 45,
                45000 => 47,
            ];
            ksort($rateTable);
            $appliedRate = $investment->profit_rate ?: null;
            foreach ($rateTable as $amt => $r) {
                if ($amount >= $amt) { $appliedRate = $r; }
            }
            // if we resolved a rate different from stored, update model and expected_return
            if ($appliedRate !== null && (float)$investment->profit_rate !== (float)$appliedRate) {
                $investment->profit_rate = $appliedRate;
                // recalc expected_return as amount * rate/100 (rounded 2 dec)
                $investment->expected_return = round($amount * ((float)$appliedRate / 100.0), 2);
                $investment->save();
            }
            // set approved_at if empty
            if (empty($investment->approved_at)) {
                $investment->approved_at = Carbon::now();
                // ensure model saves the approved_at column
                $investment->save();
            }

            // compute monthly profit
            $duration = (int) ($investment->duration_months ?: 1);
            $expected = (float) ($investment->expected_return ?: 0);
            if ($duration <= 0) $duration = 1;
            $perMonth = round($expected / $duration, 2);

            // Previously we inserted month-1 immediately on approval. To ensure the
            // accrual schedule always starts one full month after approval, we now
            // rely on the scheduled processor (earnings:process-monthly) to create
            // monthly earnings. That keeps a single place for scheduling logic and
            // prevents the first-month from being paid instantly on approval.
        }

            // Notify investor when status changed to any new value (approved/rejected/payment_requested/etc.)
            try {
                if ($prevStatus !== $newStatus) {
                    $investorModel = $investment->investor;
                    if ($investorModel && !empty($investorModel->user_id)) {
                        $user = \App\Models\User::find($investorModel->user_id);
                        if ($user && !empty($user->email)) {
                            // use a Notification that sends mail + optional database record
                            $user->notify(new \App\Notifications\InvestmentStatusChangedNotification($investment, $prevStatus, $newStatus));
                        }
                    }
                }
            } catch (\Throwable $e) {
                // don't break the update path if notification sending fails
                Log::error('Failed to send investment status notification: ' . $e->getMessage());
            }
    }
}
