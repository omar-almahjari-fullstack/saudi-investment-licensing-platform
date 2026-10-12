<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class TrustComplianceController extends Controller
{
    public function index()
    {
        $regulations = DB::table('tc_regulations')->orderBy('id','asc')->get();
        $security = DB::table('tc_security_features')->orderBy('id','asc')->get();
        $risks = DB::table('tc_risk_levels')->orderBy('level','asc')->get();
        $certs = DB::table('tc_certificates')->orderBy('issued_at','desc')->get();
        $audits = DB::table('tc_audit_reports')->orderBy('audit_date','desc')->get();
        // Load metrics from tc_metrics as associative array for view
        $metricsRows = DB::table('tc_metrics')->get();
        $indicators = [];
        foreach ($metricsRows as $m) {
            $indicators[$m->key] = [
                'label' => $m->label,
                'value' => $m->value,
                'icon' => $m->icon,
            ];
        }
        return view('trust-compliance', compact('regulations','security','risks','certs','audits','indicators'));
    }

    /**
     * Return metrics rows as JSON for admin UI.
     * Safe: returns empty array if table is missing or on error.
     */
    public function metrics(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([], 401);
        }
        try {
            if (!Schema::hasTable('tc_metrics')) {
                return response()->json([]);
            }
            $rows = DB::table('tc_metrics')->orderBy('key')->get();
            return response()->json($rows);
        } catch (\Throwable $e) {
            Log::warning('tc.metrics fetch error: '.$e->getMessage());
            return response()->json([]);
        }
    }

    public function store(Request $request) { abort(405); }
    public function update(Request $request, $id = null) { abort(405); }
    public function destroy($id = null) { abort(405); }
}
