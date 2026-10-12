<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        $totalUsers = DB::table('users')->count();
        $totalLicenses = DB::table('licenses')->count();
        $totalInvestments = DB::table('investments')->count();
        $approvedLicenses = DB::table('licenses')->where('status','approved')->count();
        $successRate = $totalLicenses > 0 ? round(($approvedLicenses / max($totalLicenses,1)) * 100) : 0;

        $stats = [
            'total_users' => $totalUsers,
            'total_licenses' => $totalLicenses,
            'total_investments' => $totalInvestments,
            'success_rate' => $successRate ?: 98,
        ];

        $packages = DB::table('investment_packages')->select('*')->orderBy('amount')->get();

        // جلب أبرز قصص النجاح المميزة (مثلاً: where is_top = 1 أو limit 2-3)
      /*  $topStories = DB::table('success_stories')
            ->select('id', 'name', 'location', 'investment_amount', 'returns', 'profit_rate', 'testimonial')
            ->orderByDesc('created_at')
            ->limit(2)
            ->get();*/
      
      $topStories = DB::table('success_stories')
    ->select('id', 'name', 'location', 'investment_amount', 'returns', 'profit_rate', 'testimonial')
    ->where('featured', 1)        // ✔ فقط المفعّلة
    ->where('is_top', 1)
    ->orderByDesc('created_at')   // اختياري
    ->limit(2)
    ->get();                      // ✔ يجلب الكل


            ///////////////////////////////////////////////////////////////
                    $indicators = DB::table('market_indicators')->orderByDesc('created_at')->limit(10)->get();
        $sectors = DB::table('market_sectors')->orderByDesc('updated_at')->limit(12)->get();
        // only show news that haven't expired (expires_at is null or in the future)
        $news = DB::table('market_news')
            ->where(function($q){
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();
        $trends = DB::table('market_trends')->orderByDesc('created_at')->limit(10)->get();
        
            ///////////////////////////////////////////////////////////////
            
            //////////////////////////////مركز الثقه والامتثال/////////////////////////////////
                   $regulations = DB::table('tc_regulations')->orderBy('id','asc')->get();
        $security = DB::table('tc_security_features')->orderBy('id','asc')->get();
        $risks = DB::table('tc_risk_levels')->orderBy('level','asc')->get();
        $certs = DB::table('tc_certificates')->orderBy('issued_at','desc')->get();
        $audits = DB::table('tc_audit_reports')->orderBy('audit_date','desc')->get();
        // Load metrics from tc_metrics as associative array for view
        $metricsRows = DB::table('tc_metrics')->get();
        $indicators_tr_com = [];
        foreach ($metricsRows as $m) {
            $indicators_tr_com[$m->key] = [
                'label' => $m->label,
                'value' => $m->value,
                'icon' => $m->icon,
            ];
        }
            ///////////////////////////////////////////////////////////////


        return view('home', compact('stats','packages','topStories','indicators','sectors','news','trends','regulations','security','risks','certs','audits','indicators_tr_com'));
    }

    // Read-only page: return 405 for write ops
    public function store() { abort(405); }
    public function update() { abort(405); }
    public function destroy() { abort(405); }
}
