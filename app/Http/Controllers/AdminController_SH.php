<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Investment;
use App\Models\BusinessLicense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController_SH extends Controller
{
    public function dashboard()
    {
        // الحصول على الإحصائيات
        $stats = [
            'total_users' => User::count(),
            'total_investments' => Investment::count(),
            'total_licenses' => BusinessLicense::count(),
            'total_investment_amount' => Investment::sum('amount'),
        ];

        // الحصول على البيانات
        $users = User::latest()->get();
        $investments = Investment::with('user')->latest()->get();
        $licenses = BusinessLicense::with('user')->latest()->get();

        return view('admin.dashboard_admin', compact('stats', 'users', 'investments', 'licenses'));
    }

    public function updateInvestmentStatus(Request $request, $id)
    {
        $investment = Investment::findOrFail($id);
        $investment->update(['status' => $request->status]);

        return back()->with('success', 'تم تحديث حالة الاستثمار بنجاح');
    }

    public function updateLicenseStatus(Request $request, $id)
    {
        $license = BusinessLicense::findOrFail($id);
        $license->update(['status' => $request->status]);

        return back()->with('success', 'تم تحديث حالة الترخيص بنجاح');
    }
}
