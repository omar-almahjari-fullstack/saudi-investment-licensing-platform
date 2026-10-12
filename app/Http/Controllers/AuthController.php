<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http as HttpClient;
use App\Mail\VerificationCodeMail;

class AuthController extends Controller
{
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // Allow registering with an email that exists but is not yet verified.
        // We'll handle existing verified emails explicitly below.
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'required|string|min:6|confirmed',
            'accept_terms' => 'accepted',
            'phone' => 'required|string|max:50',
            'g-recaptcha-response' => 'nullable|string',
        ]);

        // Verify Google reCAPTCHA (v2 or v3 token)
        // Verify Google reCAPTCHA (optional if RECAPTCHA_SECRET not configured)
        $recapToken = $request->input('g-recaptcha-response');
        $secret = env('RECAPTCHA_SECRET', '');
        if (!empty($secret)) {
            // If secret is set in env, enforce token presence and verification
            if (!$recapToken) {
                return back()->withErrors(['captcha' => 'التحقق من الإنسان مطلوب (reCAPTCHA).'])->withInput();
            }
            try {
                $resp = HttpClient::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => $secret,
                    'response' => $recapToken,
                    'remoteip' => $request->ip(),
                ]);
                $json = $resp->json();
                if (!($json['success'] ?? false)) {
                    return back()->withErrors(['captcha' => 'فشل التحقق من reCAPTCHA.'])->withInput();
                }
            } catch (\Throwable $e) {
                report($e);
                return back()->withErrors(['captcha' => 'حدث خطأ أثناء التحقق من reCAPTCHA.'])->withInput();
            }
        } else {
            // No secret configured: skip reCAPTCHA enforcement (useful for local/dev).
            // Log a message for visibility but do not block registration.
            logger()->info('RECAPTCHA_SECRET not configured; skipping reCAPTCHA verification for registration.');
        }
        // users table expects: id (UUID), full_name, email, password (nullable), role default investor
        // If a user with this email exists and is not verified, reuse that record and send a fresh OTP.
        // If the email exists and is already verified, block registration and ask the user to login.
        $existing = User::where('email', $data['email'])->first();
        $otp = random_int(100000, 999999);
        if ($existing) {
            if (!is_null($existing->email_verified_at)) {
                return back()->withErrors(['email' => 'هذا البريد مستخدم بالفعل. الرجاء تسجيل الدخول أو استعادة كلمة المرور.'])->withInput();
            }
            // Update existing unverified user with new data and OTP
            $existing->full_name = $data['name'];
            $existing->password = Hash::make($data['password']);
            $existing->verification_code = $otp;
            $existing->verification_token = now()->toDateTimeString();
            $existing->is_active = true;
            $existing->save();
            $user = $existing;
        } else {
            $uuid = (string) \Illuminate\Support\Str::uuid();
            $user = User::create([
                'id' => $uuid,
                'full_name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => 'investor',
                'is_active' => true,
                'verification_code' => $otp,
                // store timestamp of when OTP was sent in verification_token (use existing column)
                'verification_token' => now()->toDateTimeString(),
            ]);
        }

        // store phone if users table has column 'phone' or 'mobile'
        try {
            if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'phone')) {
                $user->phone = $data['phone'];
                $user->save();
            } elseif (\Illuminate\Support\Facades\Schema::hasColumn('users', 'mobile')) {
                $user->mobile = $data['phone'];
                $user->save();
            }
        } catch (\Throwable $e) {
            // ignore schema/save errors to avoid breaking registration
            report($e);
        }

        // send verification OTP email (non-blocking)
        try{
            Mail::to($user->email)->send(new VerificationCodeMail($user, $otp));
        } catch (\Throwable $e){
            report($e);
        }

        // do NOT log the user in yet; require verification
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // ensure public storage directory for this investor exists: public/storage/investors/{id}-{slug(name)}
        try {
            $this->createInvestorPublicDir($user->id, $user->full_name ?? '');
        } catch (\Throwable $e) {
            report($e);
        }

    return redirect()->route('verify.form')->with('status', 'تم إنشاء حسابك. تم إرسال رمز التحقق إلى بريدك الإلكتروني — أدخل الرمز لتفعيل الحساب.');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $data['email'])->first();
        if(!$user || !Hash::check($data['password'], $user->password)){
            return back()->withErrors(['email' => 'بيانات تسجيل الدخول غير صحيحة']);
        }
        // Deny login if email not verified
        if(is_null($user->email_verified_at)){
            return back()->withErrors(['email' => 'يرجى تفعيل حسابك قبل تسجيل الدخول.']);
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        session(['user_id' => $user->id]);
        // ensure public storage directory for this investor exists when they login
        try {
            $this->createInvestorPublicDir($user->id);
        } catch (\Throwable $e) {
            report($e);
        }
        return redirect('/');
    }

    public function logout(Request $request)
    {
        $request->session()->forget('user_id');
        return redirect('/');
    }

    /**
     * Ensure a public storage folder exists for the investor: public/storage/investors/{id}
     * This is intentionally defensive: it won't throw on failure, only reports the error.
     *
     * @param string $userId
     * @return void
     */
    /**
     * Ensure a public storage folder exists for the investor: public/storage/investors/{id}-{slug(name)}
     * If an old folder with just the id exists, attempt to rename it to the new slugged name.
     *
     * @param string $userId
     * @param string $name
     * @return void
     */
    private function createInvestorPublicDir(string $userId, string $name = ''): void
    {
        // create a safe slug from name; fallback to 'user' if slug is empty
        try {
            $slug = '';
            if (!empty($name)) {
                // use Str::slug if available
                if (class_exists('\Illuminate\Support\Str')) {
                    $slug = \Illuminate\Support\Str::slug($name, '-');
                } else {
                    // basic fallback: replace non-alphanum with dash
                    $slug = preg_replace('/[^A-Za-z0-9\-]+/u', '-', $name);
                    $slug = trim($slug, '-');
                }
            }
            if (!$slug) $slug = 'user';

            $base = public_path('storage/investors');
            $target = $base . DIRECTORY_SEPARATOR . $userId . '-' . $slug;
            $legacy = $base . DIRECTORY_SEPARATOR . $userId; // previous id-only folder

            // if legacy exists and target does not, rename it
            if (file_exists($legacy) && !file_exists($target)) {
                try {
                    if (function_exists('rename')) {
                        rename($legacy, $target);
                    } else {
                        // fallback: create target then copy
                        if (!file_exists($target)) {
                            if (class_exists('\Illuminate\Support\Facades\File')) {
                                \Illuminate\Support\Facades\File::moveDirectory($legacy, $target);
                            } else {
                                mkdir($target, 0755, true);
                            }
                        }
                    }
                } catch (\Throwable $e) {
                    // if rename fails, continue and try to create target
                    report($e);
                }
            }

            if (!file_exists($target)) {
                if (!file_exists($base)) {
                    if (class_exists('\Illuminate\Support\Facades\File')) {
                        \Illuminate\Support\Facades\File::makeDirectory($base, 0755, true);
                    } else {
                        mkdir($base, 0755, true);
                    }
                }
                if (class_exists('\Illuminate\Support\Facades\File')) {
                    \Illuminate\Support\Facades\File::makeDirectory($target, 0755, true);
                } else {
                    mkdir($target, 0755, true);
                }
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
