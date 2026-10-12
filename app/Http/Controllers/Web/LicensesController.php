<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LicensesController extends Controller
{
    public function index(Request $request)
    {
        $licensesQuery = DB::table('licenses as l')
            ->leftJoin('users as u', 'u.id', '=', 'l.user_id')
            ->orderByDesc('l.created_at')
            ->limit(100);

        // Build select list with optional user identity fields guarded
        $selects = [
            'l.id', 'l.business_name', 'l.business_type', 'l.status', 'l.created_at',
        ];

        // Build a safe COALESCE expression for the user's displayed name using
        // whichever columns actually exist in the users table (avoid referencing
        // non-existent columns which will cause SQL errors).
        $nameParts = [];
        if (Schema::hasColumn('users', 'full_name')) { $nameParts[] = 'u.full_name'; }
        if (Schema::hasColumn('users', 'name')) { $nameParts[] = 'u.name'; }
        if (Schema::hasColumn('users', 'username')) { $nameParts[] = 'u.username'; }
        // email should exist in most installations but guard it as well
        if (Schema::hasColumn('users', 'email')) { $nameParts[] = 'u.email'; }

        if (!empty($nameParts)) {
            $coalesce = 'COALESCE(' . implode(', ', $nameParts) . ') as user_name';
            $selects[] = DB::raw($coalesce);
        } else {
            // fallback literal so query still runs
            $selects[] = DB::raw("'' as user_name");
        }

        // Add user_email only if present
        if (Schema::hasColumn('users', 'email')) {
            $selects[] = 'u.email as user_email';
        }

        // Optional identity image columns (guarded)
        if (Schema::hasColumn('users', 'identity_image_front')) {
            $selects[] = 'u.identity_image_front as user_identity_image_front';
        }
        if (Schema::hasColumn('users', 'identity_image_back')) {
            $selects[] = 'u.identity_image_back as user_identity_image_back';
        }

        $licenses = $licensesQuery->select($selects)->get();

        return view('admin.licenses.index', compact('licenses'));
    }

    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $updated = DB::table('licenses')->where('id', $id)->update([
            'status' => $data['status'],
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.licenses.index')
            ->with('status', $updated ? 'تم تحديث حالة الطلب بنجاح.' : 'لم يتم العثور على الطلب.');
    }
}
