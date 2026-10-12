<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\VerificationCodeMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class PasswordResetController extends Controller
{
    // Show forgot password form
    public function showForgot()
    {
        return view('auth.forgot-password');
    }

    // Handle sending verification code
    public function sendForgot(Request $request)
    {
        $request->validate([ 'email' => 'required|email' ]);
        $email = $request->input('email');

        $throttleKey = 'forgot-send:' . Str::lower($email);
        if (Cache::has($throttleKey)) {
            return back()->withErrors(['email' => 'لقد أرسلت طلبًا مؤخرًا. الرجاء الانتظار قبل المحاولة مجددًا.']);
        }

        $user = User::where('email', $email)->first();
        if (! $user) {
            // Do not reveal existence
            return back()->with('status', 'لقد أرسلنا رمز التحقق إذا كان البريد مرتبطًا بحساب.');
        }

        $code = random_int(100000, 999999);
        $user->verification_code = (string)$code;
        $user->verification_token = now();
        $user->save();

        try {
            Mail::to($user->email)->send(new VerificationCodeMail($user, $code));
    } catch (\Exception $e) {
            Log::error('Failed to send verification code: ' . $e->getMessage());
            // Fall back to log and still show same UX
            Log::info("Verification code for {$user->email}: {$code}");
        }

        // Throttle for 60 seconds
        Cache::put($throttleKey, true, 60);

        return redirect()->route('auth.verify-reset.form', ['email' => $email])->with('status', 'تم إرسال رمز التحقق.');
    }

    // Show verify code page
    public function showVerify(Request $request)
    {
        $email = $request->query('email', '');
        return view('auth.verify-reset', ['email' => $email]);
    }

    // Handle verify code submission
    public function postVerify(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|digits:6'
        ]);

        $user = User::where('email', $request->input('email'))->first();
        if (! $user) {
            return back()->withErrors(['email' => 'البريد غير مرتبط بحساب.']);
        }

        // Check code and expiry (15 minutes)
        if (! $user->verification_code || $user->verification_code !== $request->input('code')) {
            return back()->withErrors(['code' => 'رمز التحقق غير صحيح.']);
        }

        if (! $user->verification_token || now()->diffInMinutes($user->verification_token) > 15) {
            return back()->withErrors(['code' => 'انتهت صلاحية رمز التحقق. الرجاء طلب رمز جديد.']);
        }

        // Mark session to allow password reset
        session(['password_reset_allowed' => $user->id]);

        return redirect()->route('auth.reset-password.new');
    }

    // Show new password form (guarded by session)
    public function showNewPassword(Request $request)
    {
        $allowed = session('password_reset_allowed');
        if (! $allowed) {
            return redirect()->route('auth.forgot-password')->withErrors(['email' => 'الرجاء إدخال الرمز أولاً.']);
        }
        // render the reset form view (file exists at resources/views/auth/reset-password.blade.php)
        return view('auth.reset-password');
    }

    // Handle final password reset
    public function postNewPassword(Request $request)
    {
        $messages = [
            'password.required' => 'حقل كلمة المرور مطلوب.',
            'password.string' => 'حقل كلمة المرور يجب أن يكون نصًا.',
            'password.min' => 'يجب أن تتكون كلمة المرور من :min أحرف على الأقل.',
            'password.confirmed' => 'تأكيد كلمة المرور لا يتطابق.'
        ];

        $request->validate([
            'password' => 'required|string|min:8|confirmed'
        ], $messages);

        $userId = session('password_reset_allowed');
        if (! $userId) {
            return redirect()->route('auth.forgot-password')->withErrors(['email' => 'الرجاء إدخال الرمز أولاً.']);
        }

        $user = User::find($userId);
        if (! $user) {
            return redirect()->route('auth.forgot-password')->withErrors(['email' => 'حصل خطأ. الرجاء المحاولة مجددًا.']);
        }

        $user->password = Hash::make($request->input('password'));
        // clear verification code
        $user->verification_code = null;
        $user->verification_token = null;
        $user->save();

        // Remove session guard
        session()->forget('password_reset_allowed');

        return redirect()->route('auth.login.form')->with('status', 'تم تحديث كلمة المرور. يمكنك الآن تسجيل الدخول.');
    }
}
