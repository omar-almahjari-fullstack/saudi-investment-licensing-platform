<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use App\Models\Investor;
use App\Models\User;

class CompleteProfileController extends Controller
{
    /**
     * Display the completion form populated from session oauth_user
     */
    public function show(Request $request)
    {
        $oauth = session('oauth_user');
        $userId = session('user_id');
        if (!$oauth || !$userId) {
            return redirect('/')->with('status', 'الرجاء تسجيل الدخول أولاً باستخدام Google.');
        }

        // Do not prefill the public full_name field from OAuth. The investor should
        // enter their full name manually here. Keep oauth display name available
        // as 'username' (stored separately) if needed.
        return view('auth.complete-profile', [
            'full_name' => '',
            'username' => $oauth['username'] ?? $oauth['full_name'] ?? '',
            'email' => $oauth['email'] ?? '',
        ]);
    }

    /**
     * Handle completion form submission: create/update investor and optionally set password or send reset link
     */
    public function submit(Request $request)
    {
        $userId = session('user_id');
        if (!$userId) {
            return redirect('/');
        }

        // Password is required on this page. national_id is collected later when the user registers as investor or requests a license.
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'mobile_number' => 'required|string|max:50',
            'password' => 'required|confirmed|min:8',
        ], [
            'full_name.required' => 'الاسم الكامل مطلوب',
            'full_name.max' => 'الاسم الكامل يجب أن لا يتجاوز 255 حرفاً',
            'mobile_number.required' => 'رقم الجوال مطلوب',
            'password.required' => 'كلمة المرور مطلوبة',
            'password.min' => 'كلمة المرور يجب أن تكون على الأقل 8 أحرف',
            'password.confirmed' => 'تأكيد كلمة المرور غير مطابق',
        ]);

        $user = User::find($userId);
        if (!$user) {
            return redirect('/');
        }

    // Pull oauth display name from session to store in `users.username` (DB-only)
    $oauth = session('oauth_user', []);
    $oauthDisplay = trim($oauth['username'] ?? $oauth['full_name'] ?? '');

        DB::beginTransaction();
        try {
            // update users table columns when present
            $userUpdates = [];
            if (Schema::hasColumn('users', 'phone')) { $userUpdates['phone'] = $validated['mobile_number']; }
            // national_id is intentionally NOT updated here per requirement
            if (!empty($userUpdates)) { DB::table('users')->where('id', $userId)->update($userUpdates); }

            // set full_name on users table when column exists
            if (!empty($validated['full_name']) && Schema::hasColumn('users', 'full_name')) {
                $user->full_name = $validated['full_name'];
            }

            // set username from oauth display name if available (store only in DB)
            if (!empty($oauthDisplay) && Schema::hasColumn('users', 'username')) {
                $user->username = $oauthDisplay;
            }

            // set password (required)
            if (!empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }
            $user->save();

            // Per requirement: do NOT persist these completion fields into the `investors` table.
            // Save all provided profile fields into the `users` table only (handled above).

            DB::commit();
            session()->forget('oauth_user');
            return redirect('/')->with('status', 'تم تحديث ملفك الشخصي وحفظ كلمة المرور بنجاح.');

    } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return back()->withInput()->withErrors(['submit' => 'حدث خطأ أثناء حفظ البيانات.']);
        }
    }
}

