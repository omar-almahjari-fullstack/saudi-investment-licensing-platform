<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\License;
use App\Models\User;
use Carbon\Carbon;

class LicenseReportsController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->query('from');
        $to = $request->query('to');
        $statusParam = trim((string)$request->query('status', ''));
        $type = trim((string)$request->query('type', ''));
        $ownersParam = trim((string)$request->query('owners', ''));

        $statuses = array_filter(array_map('strtolower', array_map('trim', $statusParam ? explode(',', $statusParam) : [])));
        $owners = array_filter(array_map('trim', $ownersParam ? explode(',', $ownersParam) : []));

        $q = License::query()->with('user');
        if ($from) {
            $q->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $q->whereDate('created_at', '<=', $to);
        }
        if (!empty($statuses)) {
            $q->whereIn('status', $statuses);
        }
        if ($type && $type !== 'any') {
            $q->where('business_type', $type);
        }
        if (!empty($owners)) {
            $q->whereIn('user_id', $owners);
        }

        $licenses = $q->get();

        // Totals
        $totalCount = $licenses->count();
        $totalApproved = $licenses->where('status', 'approved')->count();
        $totalPending = $licenses->where('status', 'pending')->count();
        $totalRejected = $licenses->where('status', 'rejected')->count();

        // Chart: by type
        $byType = [];
        foreach ($licenses as $lic) {
            $t = $lic->business_type ?: 'غير محدد';
            if (!isset($byType[$t])) $byType[$t] = 0;
            $byType[$t] += 1;
        }

        // Chart: by status
        $byStatus = [];
        foreach ($licenses as $lic) {
            $s = strtolower((string)$lic->status);
            if ($s === '') $s = 'unknown';
            if (!isset($byStatus[$s])) $byStatus[$s] = 0;
            $byStatus[$s] += 1;
        }

        // Chart: monthly counts grouped by status
        $monthlyMap = [];
        $statusesKeys = ['approved','pending','rejected'];
        foreach ($licenses as $lic) {
            $dt = $lic->created_at ? Carbon::parse($lic->created_at) : null;
            $key = $dt ? $dt->format('Y-m') : 'unknown';
            if (!isset($monthlyMap[$key])) {
                $monthlyMap[$key] = ['approved'=>0,'pending'=>0,'rejected'=>0,'other'=>0];
            }
            $s = strtolower((string)$lic->status);
            if (!isset($monthlyMap[$key][$s])) {
                $monthlyMap[$key]['other'] += 1;
            } else {
                $monthlyMap[$key][$s] += 1;
            }
        }
        ksort($monthlyMap);
        $monthlyLabels = array_keys($monthlyMap);
        $series = [
            'approved' => [],
            'pending' => [],
            'rejected' => [],
        ];
        foreach ($monthlyLabels as $m) {
            $row = $monthlyMap[$m];
            $series['approved'][] = isset($row['approved']) ? $row['approved'] : 0;
            $series['pending'][] = isset($row['pending']) ? $row['pending'] : 0;
            $series['rejected'][] = isset($row['rejected']) ? $row['rejected'] : 0;
        }

        // Owners list (id, name) present in the (filtered) result
        $ownerIds = $licenses->pluck('user_id')->filter()->unique()->values()->all();
        $ownersList = [];
        if (!empty($ownerIds)) {
            $users = User::whereIn('id', $ownerIds)->get(['id', 'full_name']);
            foreach ($users as $u) {
                $ownersList[] = ['id' => (string)$u->id, 'name' => (string)($u->full_name ?: $u->id)];
            }
        }

        // Types list (distinct business_type in current result)
        $typesList = [];
        $types = $licenses->pluck('business_type')->filter()->unique()->values()->all();
        foreach ($types as $t) { $typesList[] = (string)$t; }

        // Rows
        $rows = [];
        foreach ($licenses as $lic) {
            $rows[] = [
                'id' => (string)$lic->id,
                'name' => (string)($lic->business_name ?: ''),
                'type' => (string)($lic->business_type ?: ''),
                'status' => (string)($lic->status ?: ''),
                'issued_at' => $lic->created_at ? Carbon::parse($lic->created_at)->toDateString() : null,
                'notes' => (string)($lic->activity_description ?: ''),
                'owner_id' => (string)($lic->user_id ?: ''),
                'owner_name' => $lic->user ? (string)($lic->user->full_name ?: $lic->user->id) : '',
            ];
        }

        return response()->json([
            'totals' => [
                'total_count' => $totalCount,
                'total_approved' => $totalApproved,
                'total_pending' => $totalPending,
                'total_rejected' => $totalRejected,
            ],
            'chart' => [
                'byType' => $byType,
                'byStatus' => $byStatus,
                'monthly' => [
                    'labels' => $monthlyLabels,
                    'series' => $series,
                ],
            ],
            'rows' => $rows,
            'owners' => $ownersList,
            'types' => $typesList,
        ]);
    }
}
