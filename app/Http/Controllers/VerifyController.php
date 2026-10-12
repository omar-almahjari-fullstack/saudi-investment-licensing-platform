<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class VerifyController extends Controller
{
    public function show()
    {
        return view('auth.verify');
    }

    public function verify(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|digits:6',
        ]);

        $user = User::where('verification_code', $data['code'])->first();
        if(!$user){
            return back()->withErrors(['code' => 'رمز التفعيل غير صالح أو منتهي الصلاحية.']);
        }

        // check expiry: we stored sent timestamp in verification_token
        if($user->verification_token){
            try{
                $sent = \Carbon\Carbon::parse($user->verification_token);
                if(\Carbon\Carbon::now()->diffInMinutes($sent) > 15){
                    return back()->withErrors(['code' => 'انتهت صلاحية رمز التفعيل. الرجاء طلب رمز جديد.']);
                }
            } catch (\Throwable $e){
                // if parsing fails, continue and allow verification
            }
        }

        $user->email_verified_at = now();
        $user->verification_code = null;
        $user->verification_token = null;
        $user->save();

        // log the user in via session used in the app
        session()->invalidate();
        session()->regenerateToken();
        session(['user_id' => $user->id]);

        return redirect('/')->with('status', 'تم تفعيل الحساب بنجاح. مرحباً بك!');
    }

    public function resend(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $data['email'])->first();
        if(!$user) return back()->withErrors(['email' => 'لم نعثر على حساب بهذا البريد.']);
        if($user->email_verified_at) return back()->with('status', 'الحساب مفعل بالفعل. يمكنك تسجيل الدخول.');

        // rate limit (simple cache-based): max 3 resends per hour per email
        $key = 'resend_otp:' . sha1($user->email);
        $count = \Illuminate\Support\Facades\Cache::get($key, 0);
        if($count >= 3){
            return back()->withErrors(['email' => 'تم تجاوز الحد المسموح لطلب الرموز. حاول لاحقًا.']);
        }

        // generate new OTP and update timestamp
        $otp = random_int(100000, 999999);
        $user->verification_code = $otp;
        $user->verification_token = now()->toDateTimeString();
        $user->save();

        try{
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\VerificationCodeMail($user, $otp));
        } catch (\Throwable $e){
            report($e);
            return back()->withErrors(['email' => 'حدث خطأ أثناء إرسال رمز التفعيل. حاول مرة أخرى.']);
        }

        \Illuminate\Support\Facades\Cache::put($key, $count + 1, 60 * 60);

        return back()->with('status', 'تم إرسال رمز جديد إلى بريدك الإلكتروني.');
    }
}
