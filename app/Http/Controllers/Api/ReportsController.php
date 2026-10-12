<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Investment;
use App\Models\Investor;
use App\Models\InvestorEarning;
use App\Models\RolePermission;
use App\Models\UserPermission;
use Carbon\Carbon;

class ReportsController extends Controller
{
    /**
     * GET /api/admin/reports
     * Filters: from, to (YYYY-MM-DD), status (csv), type (small|large|any), investors (csv of ids)
     * Returns aggregated report and table rows.
     */
    public function index(Request $request)
    {
        // Permission check: require view_reports or manage_reports
        if (!Auth::check()) { return response()->json(['message' => 'Unauthorized'], 401); }
        $user = Auth::user();
        // Admin role shortcut
        if ($user->role !== 'admin') {
            $roleKeys = RolePermission::where('role', $user->role)->pluck('permission_key');
            $userKeys = UserPermission::where('user_id', $user->id)->pluck('permission_key');
            $perms = $roleKeys->merge($userKeys)->unique();
            if (!($perms->contains('view_reports') || $perms->contains('manage_reports'))) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
        }
        $from = $request->query('from');
        $to = $request->query('to');
        $statuses = $this->parseCsv($request->query('status'));
        $type = $request->query('type', 'any');
        $investorIds = $this->parseCsv($request->query('investors'));

        // Base investments query
        $invQ = Investment::query();
        if ($from) { $invQ->whereDate('created_at', '>=', $from); }
        if ($to) { $invQ->whereDate('created_at', '<=', $to); }
        if (!empty($statuses)) { $invQ->whereIn('status', $statuses); }
        if ($type === 'small') { $invQ->whereNotNull('package_id'); }
        elseif ($type === 'large') { $invQ->whereNull('package_id'); }
        if (!empty($investorIds)) { $invQ->whereIn('investor_id', $investorIds); }

        // Select fields needed for table and aggregations
        $investments = $invQ->get();
        $invIds = $investments->pluck('id')->all();
        $invInvestorIds = $investments->pluck('investor_id')->filter()->unique()->values()->all();

        // Prefetch investors
        $investors = Investor::whereIn('id', $invInvestorIds)->get()->keyBy('id');

        // Totals
        $totals = [
            'investments_count' => $investments->count(),
            'total_amount' => (float) $investments->sum('amount'),
            'total_expected_return' => (float) $investments->sum('expected_return'),
        ];

        // Earnings within date range (if provided), for selected investments
        $earnQ = InvestorEarning::query()->whereIn('investment_id', $invIds);
        if ($from) { $earnQ->whereDate('created_at', '>=', $from); }
        if ($to) { $earnQ->whereDate('created_at', '<=', $to); }
        $earnings = $earnQ->get();

        $totals['total_profit_paid'] = (float) $earnings->sum('profit_amount');

        // Per-investor total profit
        $profitPerInvestor = [];
        foreach ($earnings as $e) {
            $pid = $e->investor_id;
            if (!isset($profitPerInvestor[$pid])) $profitPerInvestor[$pid] = 0.0;
            $profitPerInvestor[$pid] += (float) $e->profit_amount;
        }

        // Per-investment monthly series
        $seriesByInvestment = [];
        foreach ($earnings as $e) {
            $iid = $e->investment_id;
            if (!isset($seriesByInvestment[$iid])) $seriesByInvestment[$iid] = [];
            $seriesByInvestment[$iid][] = [
                'month_number' => (int) $e->month_number,
                'profit_amount' => (float) $e->profit_amount,
                'date' => $e->created_at,
            ];
        }
        // sort each series by month_number
        foreach ($seriesByInvestment as $iid => $rows) {
            usort($rows, function($a,$b){ return $a['month_number'] <=> $b['month_number']; });
            $seriesByInvestment[$iid] = $rows;
        }

        // Status by amounts
        $statusAgg = [];
        foreach ($investments as $inv) {
            $st = (string) ($inv->status ?? '');
            if (!isset($statusAgg[$st])) $statusAgg[$st] = ['status' => $st, 'count' => 0, 'total_amount' => 0.0, 'sum_duration' => 0, 'have_duration' => 0];
            $statusAgg[$st]['count'] += 1;
            $statusAgg[$st]['total_amount'] += (float) ($inv->amount ?? 0);
            if (!empty($inv->duration_months)) { $statusAgg[$st]['sum_duration'] += (int) $inv->duration_months; $statusAgg[$st]['have_duration'] += 1; }
        }
        $statusByAmount = array_values(array_map(function($row){
            $row['avg_duration'] = $row['have_duration'] > 0 ? round($row['sum_duration'] / $row['have_duration'], 2) : null;
            unset($row['sum_duration'], $row['have_duration']);
            return $row;
        }, $statusAgg));

        // Table rows
        $earnCountByInv = [];
        $lastEarnAtByInv = [];
        foreach ($earnings as $e) {
            $iid = $e->investment_id;
            if (!isset($earnCountByInv[$iid])) $earnCountByInv[$iid] = 0;
            $earnCountByInv[$iid] += 1;
            $last = $lastEarnAtByInv[$iid] ?? null;
            if (!$last || strtotime($e->created_at) > strtotime($last)) { $lastEarnAtByInv[$iid] = $e->created_at; }
        }

        $rows = $investments->map(function($inv) use ($investors, $earnCountByInv, $lastEarnAtByInv) {
            $invName = optional($investors->get($inv->investor_id))->full_name;
            return [
                'id' => $inv->id,
                'investor_id' => $inv->investor_id,
                'investor_name' => $invName,
                'amount' => (float) $inv->amount,
                'profit_rate' => isset($inv->profit_rate) ? (float) $inv->profit_rate : null,
                'expected_return' => isset($inv->expected_return) ? (float) $inv->expected_return : null,
                'duration_months' => isset($inv->duration_months) ? (int) $inv->duration_months : null,
                'created_at' => $inv->created_at,
                'approved_at' => $inv->approved_at ?? null,
                'status' => $inv->status ?? null,
                'package_id' => $inv->package_id ?? null,
                'months_earned' => $earnCountByInv[$inv->id] ?? 0,
                'last_earning_at' => $lastEarnAtByInv[$inv->id] ?? null,
            ];
        })->values();

        // Investors list for filters (limited to those in the current selection for performance)
        $investorList = Investor::whereIn('id', $invInvestorIds)->orderBy('full_name')->get(['id','full_name','email']);

        return response()->json([
            'filtersEcho' => [
                'from' => $from,
                'to' => $to,
                'status' => $statuses,
                'type' => $type,
                'investors' => $investorIds,
            ],
            'totals' => $totals,
            'chart' => [
                'perInvestmentMonthly' => $seriesByInvestment,
                'perInvestorTotalProfit' => array_values(array_map(function($pid) use ($profitPerInvestor, $investors){
                    $name = optional($investors->get($pid))->full_name;
                    return ['investor_id' => $pid, 'investor_name' => $name, 'total_profit' => (float) ($profitPerInvestor[$pid] ?? 0) ];
                }, array_keys($profitPerInvestor))),
                'statusByAmount' => $statusByAmount,
            ],
            'table' => $rows,
            'investors' => $investorList,
        ]);
    }

    private function parseCsv($val): array
    {
        if (!$val) return [];
        if (is_array($val)) return array_values(array_filter($val));
        return array_values(array_filter(array_map('trim', explode(',', (string) $val))));
    }
}
