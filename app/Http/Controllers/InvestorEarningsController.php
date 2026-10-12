<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InvestorEarning;
use Illuminate\Support\Facades\Auth;
use App\Models\Investment;
use Carbon\Carbon;

/**
 * شاشة أرباح المستثمر الشهرية.
 *
 * تربط المستخدم المصادق عليه بـ investor.id ثم تجلب الأرباح على investor_id الصحيح،
 * وتعرضها مجمعة حسب investment_id لسهولة القراءة.
 */
class InvestorEarningsController extends Controller
{
    public function index(Request $request)
    {
    $user = Auth::user();
    if (! $user) return redirect()->route('auth.login.form');

        // Map authenticated user -> investor.id (string) to query earnings correctly
        $investor = \App\Models\Investor::where('user_id', $user->id)->first();
        if (!$investor) {
            return view('investor.earnings', ['summary' => []]);
        }

    // Fetch earnings for this investor and group by investment
    $earnings = InvestorEarning::where('investor_id', $investor->id)
                    ->orderBy('created_at', 'desc')
                    ->get()
                    ->groupBy('investment_id');

        // gather related investments in one query
        $investmentIds = $earnings->keys()->all();
        $investments = Investment::whereIn('id', $investmentIds)->get()->keyBy('id');

        // build rich summary per investment
        $summary = [];
        foreach ($earnings as $invId => $rows) {
            $inv = $investments->get($invId);
            $total = $rows->sum('profit_amount');

            // compute expected completion date (approved_at + duration_months months)
            $approvedAt = $inv && $inv->approved_at ? Carbon::parse($inv->approved_at) : null;
            $durationMonths = $inv && $inv->duration_months ? (int) $inv->duration_months : null;
            $expectedEnd = null;
            if ($approvedAt && $durationMonths) {
                $expectedEnd = $approvedAt->copy()->addMonths($durationMonths);
            }

            $summary[$invId] = [
                'rows' => $rows,
                'total' => $total,
                'meta' => [
                    'id' => $invId,
                    'status' => $inv->status ?? null,
                    'amount' => $inv->amount ?? null,
                    'profit_rate' => $inv->profit_rate ?? null,
                    'expected_return' => $inv->expected_return ?? null,
                    'created_at' => $inv->created_at ?? null,
                    'approved_at' => $inv->approved_at ?? null,
                    'duration_months' => $durationMonths,
                    'expected_end_at' => $expectedEnd,
                ],
            ];
        }

        return view('investor.earnings', [
            'summary' => $summary,
        ]);
    }
}
