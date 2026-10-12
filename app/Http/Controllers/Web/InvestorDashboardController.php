<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class InvestorDashboardController extends Controller
{
    private function currentUserId(): ?string
    {
        $u = Auth::user();
        if ($u) { return $u->id; }
        return session('user_id');
    }

    public function index(Request $request)
    {
        $uid = $this->currentUserId();
        if (!$uid) { return redirect()->route('auth.login.form'); }

    $user = DB::table('users')->where('id', $uid)->select('id','full_name','email')->first();
    // Fetch current user's investor profile (need id for filtering investments)
    $investor = DB::table('investors')->where('user_id', $uid)->select('id','phone')->first();
        if (!$user) {
            // Defensive: if session points to non-existent user, clear and force login
            session()->forget('user_id');
            return redirect()->route('auth.login.form');
        }

        // If the user has no investor profile yet, show empty collections gracefully
        $investments = $investor
            ? DB::table('investments')->where('investor_id', $investor->id)->orderByDesc('created_at')->get()
            : collect();
        $applications = DB::table('licenses')->where('user_id',$uid)->orderByDesc('created_at')->get();

        $stats = [
            'total_investments' => $investments->count(),
            'total_invested_amount' => (float) ($investments->sum('amount') ?? 0),
            'total_applications' => $applications->count(),
        ];

        $monthlyReturns = collect();
    $transactions = collect();

    // Load investor earnings summary (grouped by investment)
    $earningsSummary = [];
        if ($investor) {
        // Defensive: don't query the earnings table if migrations haven't been run yet
        if (!\Illuminate\Support\Facades\Schema::hasTable('investor_earnings')) {
            // table not present yet — ensure variables used later are defined to avoid crashing
            $earningsSummary = [];
            $earnings = collect();
            $monthlyLabels = [];
            $monthlyValues = [];
        } else {
            $earnings = \Illuminate\Support\Facades\DB::table('investor_earnings')
                ->where('investor_id', $investor->id)
                ->orderBy('investment_id')
                ->orderBy('month_number')
                ->get();

            $grouped = [];
            foreach ($earnings as $e) {
                $invId = $e->investment_id;
                if (!isset($grouped[$invId])) { $grouped[$invId] = ['rows' => [], 'total' => 0]; }
                $grouped[$invId]['rows'][] = $e;
                $grouped[$invId]['total'] += (float) $e->profit_amount;
            }
            $earningsSummary = $grouped;
        }
    }

    // Build monthly aggregated totals for chart (last 12 months)
    $monthlyLabels = [];
    $monthlyValues = [];
    if ($investor) {
        $now = \Carbon\Carbon::now();
        $start = (clone $now)->startOfMonth()->subMonths(11);
        $period = [];
        for ($i = 0; $i < 12; $i++) {
            $m = (clone $start)->addMonths($i);
            $period[] = $m;
        }

        $monthlyMap = [];
        foreach ($period as $m) {
            $key = $m->format('Y-m');
            $monthlyMap[$key] = 0.0;
            $monthlyLabels[] = $m->format('M Y');
        }

        foreach ($earnings as $e) {
            try {
                $dt = \Carbon\Carbon::parse($e->created_at);
            } catch (\Exception $ex) {
                $dt = \Carbon\Carbon::now();
            }
            $k = $dt->format('Y-m');
            if (isset($monthlyMap[$k])) {
                $monthlyMap[$k] += (float) $e->profit_amount;
            }
        }

        foreach ($monthlyMap as $k => $v) { $monthlyValues[] = round($v, 2); }

        // Also provide a 6-month sparkline dataset for the overview widget
        // Use the last 6 entries from the 12-month aggregated values
        try {
            $lastSix = array_slice($monthlyValues, -6);
            // Ensure shape is always 6 values for consistent UI (pad with zeros if needed)
            if (count($lastSix) < 6) {
                $lastSix = array_pad($lastSix, 6, 0);
            }
            $monthlyReturns = collect($lastSix);
        } catch (\Throwable $e) {
            $monthlyReturns = collect([0,0,0,0,0,0]);
        }

        // Build per-investment monthly series for chart (each investment its own line)
        $chartDatasets = [];
        // prepare per-investment maps keyed by YYYY-MM
        $invMonthlyMaps = [];
        foreach ($earningsSummary as $invId => $g) {
            $invMonthlyMaps[$invId] = $monthlyMap; // copy keys with zeros
            foreach ($invMonthlyMaps[$invId] as $kk => $_) { $invMonthlyMaps[$invId][$kk] = 0.0; }
        }
        foreach ($earnings as $e) {
            try { $dt = \Carbon\Carbon::parse($e->created_at); } catch (\Exception $ex) { $dt = \Carbon\Carbon::now(); }
            $k = $dt->format('Y-m');
            $invId = $e->investment_id;
            if (isset($invMonthlyMaps[$invId]) && isset($invMonthlyMaps[$invId][$k])) {
                $invMonthlyMaps[$invId][$k] += (float) $e->profit_amount;
            }
        }

        // palette for lines
        $palette = ['#06b6d4','#f97316','#ef4444','#a78bfa','#10b981','#f59e0b','#60a5fa','#f472b6'];
        $i = 0;
        // map investments by id for friendly labels
        $invMap = [];
        foreach ($investments as $inv) { $invMap[$inv->id] = $inv; }
        foreach ($invMonthlyMaps as $invId => $map) {
            $vals = [];
            foreach ($monthlyMap as $k => $_) { $vals[] = round(($map[$k] ?? 0), 2); }
            // include investment type and amount in label when available
            if (isset($invMap[$invId])) {
                $invObj = $invMap[$invId];
                $type = $invObj->investment_type ?? null;
                if ($type) {
                    $typeLabel = $type === 'large' ? 'استثمار كبير' : ($type === 'small' ? 'استثمار صغير' : $type);
                } else {
                    $typeLabel = null;
                }
                $invLabel = ($typeLabel ? ($typeLabel . ' - ') : '') . number_format($invObj->amount ?: 0) . ' ر.س';
            } else {
                $invLabel = 'استثمار ' . substr($invId,0,8);
            }
            $color = $palette[$i % count($palette)]; $i++;
            $chartDatasets[] = [
                'label' => $invLabel,
                'data' => $vals,
                'borderColor' => $color,
                'backgroundColor' => $color,
            ];
        }
    }

    if (!isset($chartDatasets)) { $chartDatasets = []; }

    $activeTab = $request->query('tab', 'overview');
    $lastId = $request->query('last_id');

        // Build per-investment diagnostics for investor-facing UI (next payout note)
        $invDiagnostics = [];
        if ($investor) {
            $now = \Carbon\Carbon::now();
            foreach ($investments as $inv) {
                $invId = $inv->id;
                $earningsCount = isset($earningsSummary[$invId]) ? count($earningsSummary[$invId]['rows']) : 0;
                $approvedAt = isset($inv->approved_at) && $inv->approved_at ? \Carbon\Carbon::parse($inv->approved_at) : null;
                if ($approvedAt) {
                    $nextMonth = $earningsCount + 1; // month numbers start at 1
                    $nextPayoutAt = $approvedAt->copy()->addMonths($nextMonth);
                    $overdue = $now->gt($nextPayoutAt) && ($earningsCount < $nextMonth);
                    // human-friendly Arabic note
                    // use ISO date format YYYY-MM-DD for clarity
                    try {
                        $noteDate = $nextPayoutAt->format('Y-m-d');
                    } catch (\Exception $e) {
                        $noteDate = \Carbon\Carbon::now()->format('Y-m-d');
                    }
                    $note = 'سيتم إيداع مبلغك بتاريخ ' . $noteDate;
                } else {
                    $nextPayoutAt = null;
                    $overdue = false;
                    $note = 'سيتم إيداع أرباحك بعد اعتماد الاستثمار';
                }
                $invDiagnostics[$invId] = [
                    'earnings_count' => $earningsCount,
                    'next_payout_at' => $nextPayoutAt ? $nextPayoutAt->toDateTimeString() : null,
                    'note' => $note,
                    'overdue' => $overdue,
                ];
            }
        }

        // Reuse existing Blade expecting variables
        // include monthlyLabels/monthlyValues so the Chart script in the Blade is rendered
        return view('investor.dashboard', compact('user','investor','investments','applications','stats','monthlyReturns','transactions','activeTab','lastId','earningsSummary','monthlyLabels','monthlyValues','chartDatasets','invDiagnostics'));
    }

    public function show($id)
    {
        $uid = $this->currentUserId();
        if (!$uid) { return redirect()->route('auth.login.form'); }

        // Ensure the investment belongs to the current user's investor
        $investor = DB::table('investors')->where('user_id', $uid)->select('id')->first();
        if (!$investor) { abort(404); }
        $inv = DB::table('investments')->where('id',$id)->where('investor_id',$investor->id)->first();
        if (!$inv) { abort(404); }

        $transactions = DB::table('investment_transactions')
            ->where('investment_id',$id)
            ->orderByDesc('occurred_at')
            ->get()
            ->map(function($t){
                // normalize fields expected by Blade
                return (object) [
                    'id' => $t->id,
                    'type' => $t->type,
                    'amount' => $t->amount,
                    'note' => $t->note,
                    'date' => $t->occurred_at,
                ];
            });

        // Load payment reminders for this investment (with admin name if available)
        $reminders = DB::table('payment_reminders as r')
            ->leftJoin('users as u', 'r.admin_id', '=', 'u.id')
            ->where('r.investment_id', $id)
            ->orderByDesc('r.created_at')
            ->select('r.*', 'u.full_name as admin_name')
            ->get();

        return view('investor.investment-detail', ['inv' => (object) $inv, 'transactions' => $transactions, 'reminders' => $reminders]);
    }

    public function licenseShow($id)
    {
        $uid = $this->currentUserId();
        if (!$uid) { return redirect()->route('auth.login.form'); }

        $license = DB::table('licenses')->where('id', $id)->where('user_id', $uid)->first();
        if (!$license) { abort(404); }

        // load applicant user and investor profile (if any)
        // Ensure phone and national_id are selected (when available) so the view can display them.
        $userSelect = ['id', 'full_name', 'email'];
        if (Schema::hasColumn('users', 'phone')) { $userSelect[] = 'phone'; }
        if (Schema::hasColumn('users', 'national_id')) {
            $userSelect[] = 'national_id';
        } else {
            // provide an empty national_id column for consistent access in the view
            $userSelect[] = DB::raw("'' as national_id");
        }
        // identity image columns (optional)
        $userSelect[] = Schema::hasColumn('users','identity_image') ? 'identity_image' : DB::raw('NULL as identity_image');
        $userSelect[] = Schema::hasColumn('users','identity_image_front') ? 'identity_image_front' : DB::raw('NULL as identity_image_front');
        $userSelect[] = Schema::hasColumn('users','identity_image_back') ? 'identity_image_back' : DB::raw('NULL as identity_image_back');

        $user = DB::table('users')->where('id', $license->user_id)->select($userSelect)->first();
            $investorQuery = DB::table('investors')->where('user_id', $license->user_id);

            // select id, phone and prefer users.national_id; only reference investors.national_id if the column exists
            $investorSelect = ['id', 'phone'];

            if (Schema::hasColumn('investors', 'national_id')) {
                $investorSelect[] = DB::raw('COALESCE((SELECT national_id FROM users WHERE users.id = investors.user_id), investors.national_id) as national_id');
            } else {
                $investorSelect[] = DB::raw('COALESCE((SELECT national_id FROM users WHERE users.id = investors.user_id), "") as national_id');
            }

            $investor = $investorQuery->select($investorSelect)->first();

        // Load related uploaded documents (stored in `documents` table). We expect uploads to set the `type` field to one
        // of: identity_front, identity_back, bank_statement, salary_certificate. Map them to public URLs via Storage::url()
        $docs = DB::table('documents')
            ->where('user_id', $license->user_id)
            ->whereIn('type', ['identity_front','identity_back','bank_statement','salary_certificate'])
            ->get();

        $documents = [];
        $hostPrefix = request()->getSchemeAndHttpHost();
        foreach ($docs as $d) {
            // ensure path exists before generating URL
            if (!empty($d->path)) {
                $u = Storage::url($d->path);
                if (is_string($u) && substr($u,0,1) === '/') { $u = $hostPrefix . $u; }
                $documents[$d->type] = [
                    'url' => $u,
                    'filename' => $d->filename,
                    'created_at' => $d->created_at,
                ];
            }
        }

        // If the license row itself stores bank_statement or salary_certificate paths, map them to public URLs
        // This prevents the Blade from rendering raw relative paths which resolve incorrectly relative to the page URL.
        try {
            if (!empty($license->bank_statement) && empty($documents['bank_statement'])) {
                $path = $license->bank_statement;
                if (filter_var($path, FILTER_VALIDATE_URL)) {
                    $u = $path;
                } else {
                    $u = Storage::url($path);
                    if (is_string($u) && substr($u,0,1) === '/') { $u = $hostPrefix . $u; }
                }
                $documents['bank_statement'] = [
                    'url' => $u,
                    'filename' => basename($path),
                    'created_at' => $license->updated_at ?? null,
                ];
            }
            if (!empty($license->salary_certificate) && empty($documents['salary_certificate'])) {
                $path = $license->salary_certificate;
                if (filter_var($path, FILTER_VALIDATE_URL)) {
                    $u = $path;
                } else {
                    $u = Storage::url($path);
                    if (is_string($u) && substr($u,0,1) === '/') { $u = $hostPrefix . $u; }
                }
                $documents['salary_certificate'] = [
                    'url' => $u,
                    'filename' => basename($path),
                    'created_at' => $license->updated_at ?? null,
                ];
            }
        } catch (\Exception $ex) {
            // Defensive: if Storage::url throws or path is malformed, ignore and leave documents as-is
            Log::warning('Failed to normalize license document paths: ' . $ex->getMessage());
        }

        // Prefer user-stored identity images (canonical). Build preview URLs if available.
        $userIdentityPreviews = [];
        if ($user) {
            if (!empty($user->identity_image_front)) {
                $userIdentityPreviews['identity_front'] = ['url' => Storage::url($user->identity_image_front), 'path' => $user->identity_image_front];
            }
            if (!empty($user->identity_image_back)) {
                $userIdentityPreviews['identity_back'] = ['url' => Storage::url($user->identity_image_back), 'path' => $user->identity_image_back];
            }
            if (empty($user->identity_image_front) && !empty($user->identity_image)) {
                $userIdentityPreviews['identity_front'] = ['url' => Storage::url($user->identity_image), 'path' => $user->identity_image];
            }
        }

        // Load payment reminders for this license (stored in payment_reminders.investment_id for backward-compat)
        $reminders = DB::table('payment_reminders as r')
            ->leftJoin('users as u', 'r.admin_id', '=', 'u.id')
            ->where('r.investment_id', $license->id)
            ->orderByDesc('r.created_at')
            ->select('r.*', 'u.full_name as admin_name')
            ->get();

        return view('investor.license-show', [
            'license' => (object) $license,
            'applicant' => $user ? (object) $user : null,
            'investor' => $investor ? (object) $investor : null,
            'documents' => $documents,
            'userIdentityPreviews' => $userIdentityPreviews,
            'reminders' => $reminders,
        ]);
    }

    // Create new data: support creating withdrawal request from dashboard/detail
    public function store(Request $request)
    {
        $uid = $this->currentUserId();
        if (!$uid) { abort(401); }
        $data = $request->validate([
            'type' => 'required|string', // e.g., withdrawal
            'investment_id' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'reason' => 'nullable|string',
        ]);
        if ($data['type'] === 'withdrawal') {
            $now = now();
            DB::table('withdrawal_requests')->insert([
                'id' => (string) Str::uuid(),
                'user_id' => $uid,
                'investment_id' => $data['investment_id'] ?? null,
                'amount' => $data['amount'],
                'status' => 'pending',
                'reason' => $data['reason'] ?? null,
                'requested_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
                
            ]);
            return back()->with('status','withdrawal_requested');
        }
        abort(422, 'Unsupported create type');
    }

    public function update(Request $request, $id)
    {
        // For extensibility: allow updating notes on investment or cancel a withdrawal
        $action = $request->input('action');
        if ($action === 'update_investment_notes') {
            $uid = $this->currentUserId();
            if (!$uid) { abort(401); }
            $data = $request->validate(['notes' => 'nullable|string']);
            // Update notes only if the investment belongs to the current user's investor
            $investor = DB::table('investors')->where('user_id', $uid)->select('id')->first();
            if (!$investor) { abort(404); }
            DB::table('investments')->where('id',$id)->where('investor_id',$investor->id)->update([
                'notes' => $data['notes'] ?? null,
                'updated_at' => now(),
            ]);
            return back()->with('status','investment_updated');
        }
        abort(405);
    }

    public function destroy($id)
    {
        // Optional: allow deleting a pending withdrawal request
        $uid = $this->currentUserId();
        if (!$uid) { abort(401); }
        $deleted = DB::table('withdrawal_requests')
            ->where('id',$id)
            ->where('user_id',$uid)
            ->where('status','pending')
            ->delete();
        return back()->with('status', $deleted ? 'withdrawal_deleted' : 'nothing_deleted');
    }
}
