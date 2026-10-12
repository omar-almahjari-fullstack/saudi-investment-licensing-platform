<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $users = DB::table('users')->count();
        $investments = DB::table('investments')->count();
        $licenses = DB::table('licenses')->count();
        $docs = DB::table('documents')->count();
        $withdrawals = DB::table('withdrawal_requests')->count();
        $payments = DB::table('payments')->count();

        $stats = compact('users','investments','licenses','docs','withdrawals','payments');

        // تمرير بيانات التراخيص الأولية للعرض مباشرة بدون اعتماد على Ajax (تفادي مشاكل الجلسة على بعض البيئات)
        $licenseSelect = [
            'licenses.id',
            'licenses.business_name',
            'licenses.business_type',
            'licenses.activity_description',
            'licenses.location',
            'licenses.status',
            'licenses.created_at',
            // عرض اسم المستثمر أولاً إن وجد، ثم بدائله من المستخدم
            DB::raw('COALESCE(investors.full_name, users.full_name, users.username, users.email) as user_name'),
            'users.email as user_email',
            DB::raw('COALESCE(investors.phone, "") as user_phone'),
            // prefer users.national_id (single source of truth). If investors.national_id still exists use it as fallback.
        ];

        if (Schema::hasColumn('investors', 'national_id')) {
            $licenseSelect[] = DB::raw('COALESCE(users.national_id, investors.national_id, "") as user_national_id');
        } else {
            $licenseSelect[] = DB::raw('COALESCE(users.national_id, "") as user_national_id');
        }

        if (Schema::hasColumn('investors', 'bank_account')) {
            $licenseSelect[] = DB::raw('COALESCE(investors.bank_account, "") as user_bank_account');
        } else {
            $licenseSelect[] = DB::raw('"" as user_bank_account');
        }

        // identity image may live on investors (legacy) or users (canonical). Prefer investors if present, fall back to users or empty.
        if (Schema::hasColumn('investors', 'identity_image')) {
            $licenseSelect[] = 'investors.identity_image as user_identity_image';
        } elseif (Schema::hasColumn('users', 'identity_image')) {
            $licenseSelect[] = 'users.identity_image as user_identity_image';
        } else {
            $licenseSelect[] = DB::raw('"" as user_identity_image');
        }

        if (Schema::hasTable('investors')) {
            $licenseSelect[] = 'investors.id as investor_id';
        } else {
            $licenseSelect[] = DB::raw('"" as investor_id');
        }

        // الجنسية أصبحت في investors
        if (Schema::hasColumn('investors', 'nationality')) {
            $licenseSelect[] = DB::raw('COALESCE(investors.nationality, "") as user_nationality');
        } else {
            $licenseSelect[] = DB::raw('"" as user_nationality');
        }

        $licensesData = DB::table('licenses')
            ->leftJoin('users', 'licenses.user_id', '=', 'users.id')
            ->leftJoin('investors', 'investors.user_id', '=', 'users.id')
            ->orderBy('licenses.created_at', 'desc')
            ->take(50)
            ->get($licenseSelect);

        // Convert possible stored paths into full URLs for display
        $licensesData = $licensesData->map(function($it){
            $it = (array) $it;
            $img = $it['user_identity_image'] ?? null;
            $invId = $it['investor_id'] ?? null;
            if ($img) {
                // if already a full URL (starts with http) keep it
                if (preg_match('#^https?://#', $img)) {
                    $it['user_identity_image'] = $img;
                } else {
                    // If file exists on public disk, use Storage::url (e.g. /storage/..)
                    if (Storage::disk('public')->exists($img)) {
                        $it['user_identity_image'] = Storage::url($img);
                    } else {
                        // fallback to controller route using investor id if available
                        $it['user_identity_image'] = $invId ? route('investor.image', ['investor' => $invId, 'side' => 'front']) : $img;
                    }
                }
            } else {
                $it['user_identity_image'] = null;
            }
            return (object) $it;
        });

        return view('admin.dashboard_admin', compact('stats', 'licensesData'));
    }

    public function store() { abort(405); }
    public function update() { abort(405); }
    public function destroy() { abort(405); }
}
