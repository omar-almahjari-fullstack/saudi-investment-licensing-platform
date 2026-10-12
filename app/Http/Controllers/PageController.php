<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\License;
use App\Models\Investment;
use App\Models\Investor;
use App\Models\InvestmentPackage;

class PageController extends Controller
{
    public function home()
    {
        // Use actual DB counts and demo packages
        $totalUsers = User::count();
        $totalLicenses = License::count();
        $totalInvestments = Investment::count();

        // simple success rate metric (approved licenses / total licenses) * 100
        $approved = License::where('status', 'approved')->count();
        $successRate = $totalLicenses > 0 ? round(($approved / $totalLicenses) * 100) : 0;

        $stats = [
            'total_users' => $totalUsers,
            'total_licenses' => $totalLicenses,
            'success_rate' => $successRate ?: 98,
        ];

        // packages - keep the original quick packages
        $packages = [
            ['amount' => 1000, 'profit_rate' => 5],
            ['amount' => 5000, 'profit_rate' => 6],
            ['amount' => 10000, 'profit_rate' => 7],
            ['amount' => 20000, 'profit_rate' => 8],
        ];

        return view('home', compact('stats', 'packages'));
    }

    public function investment($type)
    {
        return view('investment', ['type' => $type]);
    }

    public function license()
    {
        return view('license');
    }

    public function dashboard()
    {
        return view('dashboard');
    }

    /**
     * Investor dashboard - shows investments and license applications for the signed-in investor.
     */
    public function investorDashboard()
    {
        // resolve user: prefer Auth facade, fall back to session('user_id') used by simple AuthController
        $user = Auth::user();
        if (!$user) {
            $userId = session('user_id');
            if ($userId) {
                $user = User::find($userId);
            }
        }

        if (!$user) {
            // Redirect to login form if no user present
            return redirect()->route('auth.login.form');
        }

        // map to investor and use investor_id linkage
        $investor = Investor::where('user_id', $user->id)->first();
        $investments = $investor
            ? Investment::where('investor_id', $investor->id)->orderBy('created_at', 'desc')->get()
            : collect();
        $applications = License::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();

        $stats = [
            'total_investments' => $investments->count(),
            'total_invested_amount' => $investments->sum('amount'),
            'total_applications' => $applications->count(),
        ];

        // provide harmless defaults for UI elements (sparkline / transactions)
        $monthlyReturns = collect([]);
        $transactions = collect([]);

        return view('investor.dashboard', compact('user', 'investments', 'applications', 'stats','monthlyReturns','transactions'));
    }

    /**
     * Demo version of investor dashboard for design preview.
     */
    public function investorDashboardDemo()
    {
        // Build demo user and demo collections without touching DB
        $user = (object) [
            'id' => 'demo-1',
            'full_name' => 'مستخدم تجريبي',
            'email' => 'demo@example.com',
            'phone' => '0500000000',
        ];

        $investments = collect([
            (object)[ 'id'=>'inv1','amount'=>10000,'profit_rate'=>33,'expected_return'=>3300,'status'=>'active','created_at'=>now()->subDays(10) ],
            (object)[ 'id'=>'inv2','amount'=>25000,'profit_rate'=>39,'expected_return'=>9750,'status'=>'pending','created_at'=>now()->subDays(30) ],
            (object)[ 'id'=>'inv3','amount'=>5000,'profit_rate'=>27,'expected_return'=>1350,'status'=>'completed','created_at'=>now()->subDays(60) ],
        ]);

        $applications = collect([
            (object)[ 'id'=>'app1','business_name'=>'مقهى نموذجي','business_type'=>'تجاري','status'=>'approved','created_at'=>now()->subDays(20) ],
            (object)[ 'id'=>'app2','business_name'=>'مشروع توصيل','business_type'=>'خدمات','status'=>'pending','created_at'=>now()->subDays(5) ],
        ]);

        // Demo monthly returns (last 6 months) and recent transactions
        $monthlyReturns = collect([1200, 900, 1500, 1100, 1800, 2100]);
        $transactions = collect([
            (object)['id'=>'t1','type'=>'credit','amount'=>2100,'note'=>'عائد استثمار INV1','date'=>now()->subDays(3)],
            (object)['id'=>'t2','type'=>'debit','amount'=>500,'note'=>'رسوم إدارة','date'=>now()->subDays(5)],
            (object)['id'=>'t3','type'=>'credit','amount'=>330,'note'=>'عائد استثمار INV3','date'=>now()->subDays(12)],
            (object)['id'=>'t4','type'=>'credit','amount'=>975,'note'=>'عائد استثمار INV2','date'=>now()->subDays(32)],
        ]);

        $stats = [
            'total_investments' => $investments->count(),
            'total_invested_amount' => $investments->sum('amount'),
            'total_applications' => $applications->count(),
        ];

        return view('investor.dashboard', compact('user','investments','applications','stats','monthlyReturns','transactions'));
    }

    /**
     * Demo view for a single investment detail
     */
    public function investorInvestmentDemo($id)
    {
        // create a demo investment by id (map to one of demo investments)
        $map = [
            'inv1' => (object)['id'=>'inv1','amount'=>10000,'profit_rate'=>33,'expected_return'=>3300,'status'=>'active','duration_months'=>12,'notes'=>'تم الاستثمار في مشروع نموذجي.','created_at'=>now()->subDays(10)],
            'inv2' => (object)['id'=>'inv2','amount'=>25000,'profit_rate'=>39,'expected_return'=>9750,'status'=>'pending','duration_months'=>24,'notes'=>'سلة استثمارية متنوعة.','created_at'=>now()->subDays(30)],
            'inv3' => (object)['id'=>'inv3','amount'=>5000,'profit_rate'=>27,'expected_return'=>1350,'status'=>'completed','duration_months'=>6,'notes'=>'استثمار قصير الأجل.','created_at'=>now()->subDays(60)],
        ];

        $inv = $map[$id] ?? null;
        if(!$inv) abort(404);

        $transactions = collect([
            (object)['id'=>'t1','type'=>'credit','amount'=>2100,'note'=>'عائد شهري','date'=>now()->subDays(3)],
            (object)['id'=>'t2','type'=>'debit','amount'=>100,'note'=>'رسوم','date'=>now()->subDays(20)],
        ]);

        return view('investor.investment-detail', compact('inv','transactions'));
    }

    /**
     * Real DB-backed investment detail for the signed-in investor.
     */
    public function investorInvestmentDetail($id)
    {
        // resolve current user (Auth or session fallback)
        $user = \Illuminate\Support\Facades\Auth::user();
        if (!$user) {
            $uid = session('user_id');
            if ($uid) {
                $user = User::find($uid);
            }
        }

        if (!$user) {
            return redirect()->route('auth.login.form');
        }

        // ensure the investment belongs to this user
    $investor = Investor::where('user_id', $user->id)->firstOrFail();
    $inv = Investment::where('id', $id)->where('investor_id', $investor->id)->firstOrFail();

        // TODO: load real transactions when available
        $transactions = collect([]);

        return view('investor.investment-detail', compact('inv', 'transactions'));
    }

    public function investmentLevels()
    {
        return view('investment-levels');
    }

    public function smallInvestment()
    {
        $level = request()->query('level');

    $query = InvestmentPackage::query()->where('is_active', 1)->orderBy('amount');
        if ($level === 'beginner') {
            $query->whereBetween('amount', [1000, 10000]);
        } elseif ($level === 'intermediate') {
            $query->whereBetween('amount', [15000, 45000]);
        }

        $packages = $query->get();

        // Header stats
        $minAmount = $packages->min('amount') ?? 1000;
        $maxAmount = $packages->max('amount') ?? 45000;
        $maxRate   = $packages->max('profit_rate') ?? 47;

        return view('small-investment', compact('packages', 'level', 'minAmount', 'maxAmount', 'maxRate'));
    }

    public function largeInvestment()
    {
        return view('large-investment');
    }

    public function documents()
    {
        return view('documents');
    }

    public function marketInsights()
    {
        return view('market-insights');
    }

    public function successStories()
    {
        return view('success-stories');
    }

    public function trustCompliance()
    {
        return view('trust-compliance');
    }

    public function registerInvestor()
    {
        // If user is logged in (middleware enforces this), check if they already have an investor profile
        $user = Auth::user();
        if (!$user) {
            $uid = session('user_id');
            if ($uid) { $user = User::find($uid); }
        }

        if ($user) {
            // If an investor row exists for this user, do not allow creating another —
            // redirect to investment levels and show a notice. This prevents duplicate
            // registrations via the "تسجيل مستثمر جديد" entry point.
            $investor = Investor::where('user_id', $user->id)->first();
            if ($investor) {
                return redirect()->route('investment.levels')
                    ->with('status', 'لديك ملف مستثمر مسجل بالفعل. يمكنك تعديل بياناته من صفحة الملف الشخصي أو استكشاف مستويات الاستثمار.');
            }
        }

        // Prepare user data to prefill the registration form (prefer users table values)
        $userData = null;
        if ($user) {
            $userData = [
                'full_name' => $user->full_name ?? '',
                'email' => $user->email ?? '',
                // phone column may not exist in older environments
                'phone' => Schema::hasColumn('users', 'phone') ? ($user->phone ?? '') : ($user->phone ?? ''),
                // national_id may not exist in older environments; guard it
                'national_id' => Schema::hasColumn('users', 'national_id') ? ($user->national_id ?? '') : '',
                // identity image columns — prefer canonical user fields when available
                'identity_image' => Schema::hasColumn('users', 'identity_image') ? ($user->identity_image ?? null) : null,
                'identity_image_front' => Schema::hasColumn('users', 'identity_image_front') ? ($user->identity_image_front ?? null) : null,
                'identity_image_back' => Schema::hasColumn('users', 'identity_image_back') ? ($user->identity_image_back ?? null) : null,
            ];
        }

        return view('register-investor', compact('userData'));
    }

    /**
     * Show investor profile (from investors table) for the signed-in investor.
     */
    public function investorProfile()
    {
        // Resolve user from session (simple auth used in this app)
        $user = \Illuminate\Support\Facades\Auth::user();
        if (!$user) {
            $uid = session('user_id');
            if ($uid) { $user = User::find($uid); }
        }
    if (!$user) { return redirect()->route('auth.login.form'); }

        // Load or create investor row for this user
        $investor = Investor::where('user_id', $user->id)->first();
        if (!$investor) {
            $investor = new Investor([
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'user_id' => $user->id,
            ]);
            $investor->save();
        }

        return view('investor.profile', compact('user', 'investor'));
    }

    /**
     * Update investor profile data (investors table) for the signed-in investor.
     */
    public function updateProfile(\Illuminate\Http\Request $request)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        if (!$user) {
            $uid = session('user_id');
            if ($uid) { $user = User::find($uid); }
        }
    if (!$user) { return redirect()->route('auth.login.form'); }

        $data = $request->validate([
            'nationality' => 'sometimes|nullable|string|max:100',
            'city' => 'sometimes|nullable|string|max:100',
            'investor_type' => 'sometimes|nullable|in:individual,institutional',
            'expected_amount' => 'sometimes|nullable|string|max:50',
            'investment_duration' => 'sometimes|nullable|string|max:50',
            'notes' => 'sometimes|nullable|string|max:1000',
            'bank_account' => 'sometimes|nullable|string|max:100',
            'identity_image_front' => 'sometimes|file|mimes:jpg,jpeg,png|max:5120',
            'identity_image_back' => 'sometimes|file|mimes:jpg,jpeg,png|max:5120',
            'marketing_opt_in' => 'sometimes|boolean',
        ]);

        $investor = Investor::where('user_id', $user->id)->first();
        if (!$investor) {
            $investor = new Investor([
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'user_id' => $user->id,
            ]);
        }

        $investor->nationality = $data['nationality'] ?? $investor->nationality;
        $investor->city = $data['city'] ?? $investor->city;
        $investor->investor_type = $data['investor_type'] ?? $investor->investor_type;
        $investor->expected_amount = $data['expected_amount'] ?? $investor->expected_amount;
        $investor->investment_duration = $data['investment_duration'] ?? $investor->investment_duration;
    $investor->notes = $data['notes'] ?? $investor->notes;
        $investor->bank_account = $data['bank_account'] ?? $investor->bank_account;
        $investor->marketing_opt_in = (bool) ($data['marketing_opt_in'] ?? $investor->marketing_opt_in);

        // Handle identity images upload
        $safeName = preg_replace('/[^\p{L}\p{N}_-]+/u', '-', $user->full_name ?: $user->id);
        $baseDir = 'investors/' . ($safeName ?: $user->id);
        if ($request->hasFile('identity_image_front')) {
            $frontFile = $request->file('identity_image_front');
            $frontName = 'identity-front-' . now()->format('YmdHis') . '.' . $frontFile->getClientOriginalExtension();
            $path = $frontFile->storeAs($baseDir, $frontName, 'public');
            $investor->identity_image_front = $path;
            $investor->identity_image = $path; // keep legacy column in sync
            // mirror to users table canonical fields
            try {
                $userUpdates = [];
                if (Schema::hasColumn('users', 'identity_image_front')) { $userUpdates['identity_image_front'] = $path; }
                if (Schema::hasColumn('users', 'identity_image')) { $userUpdates['identity_image'] = $path; }
                if (!empty($userUpdates)) { DB::table('users')->where('id', $user->id)->update($userUpdates); }
            } catch (\Throwable $e) { report($e); }
        }
        if ($request->hasFile('identity_image_back')) {
            $backFile = $request->file('identity_image_back');
            $backName = 'identity-back-' . now()->format('YmdHis') . '.' . $backFile->getClientOriginalExtension();
            $path = $backFile->storeAs($baseDir, $backName, 'public');
            $investor->identity_image_back = $path;
            try {
                if (Schema::hasColumn('users', 'identity_image_back')) { DB::table('users')->where('id', $user->id)->update(['identity_image_back' => $path]); }
            } catch (\Throwable $e) { report($e); }
        }
        $investor->save();

        return redirect()->route('investor.profile')->with('status', 'تم تحديث الملف الاستثماري بنجاح');
    }
}
