<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Investor;

class InvestorOnboardingController extends Controller
{
    /**
     * Handle investor registration submission, create user, and redirect to checkout.
     */
    public function submit(Request $request)
    {
        // Validation with Arabic messages
        $data = $request->validate([
            'investorType' => 'required|in:individual,institutional',
            'full_name' => 'required|string|max:255',
            'national_id' => 'required|string|max:100',
            'nationality' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'mobile_number' => 'required|string|max:50',
            // لا ننشئ مستخدمًا هنا، لذلك لا نفرض فريد في users
            'email' => 'required|email',
            'expected_amount' => 'required|string|max:50',
            // هذه الصفحة فقط: لا نجعل مدة الاستثمار مطلوبة، اتركها اختيارية
            'investment_duration' => 'sometimes|nullable|string|max:50',
            'agree_terms' => 'accepted',
            'agree_offers' => 'sometimes|nullable',
            'bank_account' => 'sometimes|nullable|string|max:100',
            'notes' => 'sometimes|nullable|string|max:1000',
            'identity_image_front' => 'sometimes|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'identity_image_back' => 'sometimes|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ], [
            'investorType.required' => 'يرجى اختيار نوع المستثمر',
            'full_name.required' => 'الاسم الكامل مطلوب',
            'national_id.required' => 'رقم الهوية أو الإقامة مطلوب',
            'nationality.required' => 'الجنسية مطلوبة',
            'city.required' => 'المدينة مطلوبة',
            'mobile_number.required' => 'رقم الجوال مطلوب',
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'صيغة البريد الإلكتروني غير صحيحة',
            'email.unique' => 'هذا البريد الإلكتروني مستخدم بالفعل',
            'expected_amount.required' => 'المبلغ المتوقع مطلوب',
            // لم نعد نطلب مدة الاستثمار في هذه الصفحة
            'notes.max' => 'الملاحظة يجب ألا تتجاوز 1000 حرف',
            'agree_terms.accepted' => 'يجب الموافقة على الشروط والأحكام',
            'identity_image_front.mimes' => 'صيغة الملف الأمامي يجب أن تكون JPG أو PNG أو PDF',
            'identity_image_front.max' => 'حجم الصورة الأمامية يجب ألا يتجاوز 5MB',
            'identity_image_back.mimes' => 'صيغة الملف الخلفي يجب أن تكون JPG أو PNG أو PDF',
            'identity_image_back.max' => 'حجم الصورة الخلفية يجب ألا يتجاوز 5MB',
        ]);

        $now = now();

        try {
            DB::beginTransaction();

            // الحصول على معرف المستخدم الحالي من نظام الدخول
            $userId = Auth::id() ?: session('user_id');
            // If no user is logged in, create a new user record for this investor (test and public flow)
            if (!$userId) {
                $newUserId = (string) Str::uuid();
                $userInsert = [
                    'id' => $newUserId,
                    'full_name' => $data['full_name'],
                    'email' => $data['email'],
                    'role' => 'investor',
                    'is_active' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                if (Schema::hasColumn('users', 'phone')) {
                    $userInsert['phone'] = $data['mobile_number'] ?? null;
                }
                DB::table('users')->insert($userInsert);
                $userId = $newUserId;
                // store in session to allow subsequent flows to reference the user
                session(['user_id' => $userId]);
            }

            // منع تكرار ربط أكثر من مستثمر بنفس المستخدم
            $alreadyHasInvestor = DB::table('investors')->where('user_id', $userId)->exists();
            if ($alreadyHasInvestor) {
                DB::rollBack();
                return back()->withInput()->withErrors([
                    'submit' => 'لا يمكن إنشاء أكثر من ملف مستثمر لنفس المستخدم.'
                ]);
            }

            // Prepare investor identifier
            $investorId = (string) Str::uuid();

            // Ensure users.full_name is set in the users table so folder naming is canonical.
            try {
                if (!empty($data['full_name']) && Schema::hasColumn('users', 'full_name')) {
                    $existingName = DB::table('users')->where('id', $userId)->value('full_name');
                    if (empty($existingName)) {
                        DB::table('users')->where('id', $userId)->update(['full_name' => $data['full_name']]);
                    }
                }
            } catch (\Throwable $e) {
                // Non-fatal, log and continue
                report($e);
            }

            // Derive the base directory strictly from users.full_name (fallback to investor id)
            $userFullName = DB::table('users')->where('id', $userId)->value('full_name') ?? '';
            $safeName = preg_replace('/[^\p{L}\p{N}_-]+/u', '-', $userFullName);
            $baseDir = 'investors/' . ($safeName ?: $investorId);

            // Ensure directory exists even if no files are uploaded yet (required by admin review)
            try {
                Storage::disk('public')->makeDirectory($baseDir);
                // Also ensure a public-facing folder exists under public/storage for admin previews
                try {
                    $publicDir = public_path('storage/' . $baseDir);
                    if (!file_exists($publicDir)) { mkdir($publicDir, 0755, true); }
                } catch (\Throwable $ee) {
                    report($ee);
                }
            } catch (\Throwable $e) {
                // Non-fatal: log and continue
                report($e);
            }

            $frontPath = null;
            $backPath = null;
            if ($request->hasFile('identity_image_front')) {
                $frontFile = $request->file('identity_image_front');
                $frontName = 'identity-front-' . now()->format('YmdHis') . '.' . $frontFile->getClientOriginalExtension();
                // store on public disk
                // ensure directory exists first
                Storage::disk('public')->makeDirectory($baseDir);
                $frontPath = $frontFile->storeAs($baseDir, $frontName, 'public');
                // ensure public-facing copy exists so UI can display immediately (environments without storage:link)
                try {
                    $storageFull = storage_path('app/public/' . ltrim($frontPath, '/'));
                    $publicFull = public_path('storage/' . ltrim($frontPath, '/'));
                    if (file_exists($storageFull) && !file_exists($publicFull)) {
                        @mkdir(dirname($publicFull), 0755, true);
                        @copy($storageFull, $publicFull);
                    }
                } catch (\Throwable $e) {
                    report($e);
                }
            }
            if ($request->hasFile('identity_image_back')) {
                $backFile = $request->file('identity_image_back');
                $backName = 'identity-back-' . now()->format('YmdHis') . '.' . $backFile->getClientOriginalExtension();
                Storage::disk('public')->makeDirectory($baseDir);
                $backPath = $backFile->storeAs($baseDir, $backName, 'public');
                try {
                    $storageFullB = storage_path('app/public/' . ltrim($backPath, '/'));
                    $publicFullB = public_path('storage/' . ltrim($backPath, '/'));
                    if (file_exists($storageFullB) && !file_exists($publicFullB)) {
                        @mkdir(dirname($publicFullB), 0755, true);
                        @copy($storageFullB, $publicFullB);
                    }
                } catch (\Throwable $e) {
                    report($e);
                }
            }

            // Also persist these paths to the users table so the user's profile
            // becomes the canonical location for identity images.
            try {
                $userUpdates = [];
                if (!empty($frontPath)) {
                    if (Schema::hasColumn('users', 'identity_image_front')) { $userUpdates['identity_image_front'] = $frontPath; }
                   
                }
                if (!empty($backPath)) {
                    if (Schema::hasColumn('users', 'identity_image_back')) { $userUpdates['identity_image_back'] = $backPath; }
                }
                if (!empty($userUpdates)) {
                    DB::table('users')->where('id', $userId)->update($userUpdates);
                }
            } catch (\Throwable $e) {
                // Non-fatal: log and continue
                report($e);
            }

            // Persist onboarding details in investors table (keep legacy copy, but users is source of truth)
            $investor = new Investor();
            $investor->id = $investorId;
            $investor->user_id = $userId;

            // Build attributes array guarded by schema checks to avoid inserting into non-existent columns
            $attrs = [];
            if (Schema::hasColumn('investors', 'full_name')) { $attrs['full_name'] = $data['full_name']; }
            if (Schema::hasColumn('investors', 'email')) { $attrs['email'] = $data['email']; }
            if (Schema::hasColumn('investors', 'phone')) { $attrs['phone'] = $data['mobile_number']; }
            if (Schema::hasColumn('investors', 'national_id')) { $attrs['national_id'] = $data['national_id'] ?? null; }
            if (Schema::hasColumn('investors', 'bank_account')) { $attrs['bank_account'] = $data['bank_account'] ?? null; }
            if (Schema::hasColumn('investors', 'identity_image')) { $attrs['identity_image'] = $frontPath ?? null; }
            if (Schema::hasColumn('investors', 'identity_image_front')) { $attrs['identity_image_front'] = $frontPath; }
            if (Schema::hasColumn('investors', 'identity_image_back')) { $attrs['identity_image_back'] = $backPath; }
            if (Schema::hasColumn('investors', 'nationality')) { $attrs['nationality'] = $data['nationality'] ?? null; }
            if (Schema::hasColumn('investors', 'city')) { $attrs['city'] = $data['city'] ?? null; }
            if (Schema::hasColumn('investors', 'investor_type')) { $attrs['investor_type'] = $data['investorType'] ?? null; }
            if (Schema::hasColumn('investors', 'expected_amount')) { $attrs['expected_amount'] = $data['expected_amount'] ?? null; }
            if (Schema::hasColumn('investors', 'investment_duration')) { $attrs['investment_duration'] = $data['investment_duration'] ?? null; }
            if (Schema::hasColumn('investors', 'investment_size')) { $attrs['investment_size'] = null; }
            if (Schema::hasColumn('investors', 'notes')) { $attrs['notes'] = $data['notes'] ?? null; }
            if (Schema::hasColumn('investors', 'marketing_opt_in')) { $attrs['marketing_opt_in'] = isset($data['agree_offers']) ? 1 : 0; }

            // Always set timestamps if columns exist
            if (Schema::hasColumn('investors', 'created_at')) { $attrs['created_at'] = $now; }
            if (Schema::hasColumn('investors', 'updated_at')) { $attrs['updated_at'] = $now; }
            // Fill and save
            $investor->fill($attrs);
            $investor->save();

            // Enforce immutability: if users.national_id exists and differs, reject the change
            if (!empty($data['national_id']) && Schema::hasColumn('users', 'national_id')) {
                $existing = DB::table('users')->where('id', $userId)->value('national_id');
                if (!empty($existing) && trim($existing) !== trim($data['national_id'])) {
                    DB::rollBack();
                    return back()->withInput()->withErrors(['national_id' => 'رقم الهوية مسجّل مسبقاً ولا يمكن تغييره.']);
                }
                if (empty($existing)) {
                    DB::table('users')->where('id', $userId)->update(['national_id' => $data['national_id']]);
                }
            }

            // Persist full_name to users table if it's empty (store once)
            try {
                if (!empty($data['full_name']) && Schema::hasColumn('users', 'full_name')) {
                    $existingName = DB::table('users')->where('id', $userId)->value('full_name');
                    if (empty($existingName)) {
                        DB::table('users')->where('id', $userId)->update(['full_name' => $data['full_name']]);
                    }
                }
            } catch (\Throwable $e) {
                report($e);
            }

            DB::commit();

            // حفظ مُعرّف المستثمر في الجلسة لتسهيل الوصول لاحقًا
            session(['investor_id' => $investorId]);

            session(['onboarding' => [
                'expected_amount' => $data['expected_amount'],
                'investment_duration' => $data['investment_duration'] ?? null,
                'full_name' => $data['full_name'],
            ]]);

            // Decide where to redirect after successful onboarding.
            $redirectAfter = $request->input('redirect_after') ?: $request->query('redirect');
            $preAmount = $request->input('pre_amount') ?: $request->query('amount');
            // If flow requested checkout or amount indicates large investment, redirect to checkout
            if ($redirectAfter === 'checkout' || (is_numeric($preAmount) && (int)$preAmount >= 50000)) {
                return redirect()->route('checkout.show')->with('status', '🎉 تم حفظ بياناتك كمستثمر بنجاح، اكمل الدفع لإتمام طلب الاستثمار.');
            }

            // Default: redirect to investment levels with a success flash
            return redirect()->route('investment.levels')
                ->with('status', '🎉 تم حفظ بياناتك كمستثمر بنجاح، يمكنك الآن استكشاف مستويات الاستثمار.');
        } catch (\Throwable $e) {
            DB::rollBack();
            // Log the exception for debugging while showing a friendly message
            report($e);
            return back()->withInput()->withErrors([
                'submit' => 'حدث خطأ أثناء حفظ البيانات. يرجى المحاولة مرة أخرى خلال لحظات.'
            ]);
        }
    }

    /**
     * Show a simple checkout page to simulate or initiate payment.
     */
    public function showCheckout(Request $request)
    {
        $userId = session('user_id');
        if (!$userId) {
            return redirect()->route('auth.login.form');
        }

        $onboarding = session('onboarding', []);

        // Map expected_amount range to a suggested initial payment (min value or fixed fee)
        $suggestedAmount = $this->mapRangeToAmount($onboarding['expected_amount'] ?? null);

        return view('checkout', [
            'onboarding' => $onboarding,
            'suggestedAmount' => $suggestedAmount,
        ]);
    }

    /**
     * Process the checkout form: create a payment record and mark as completed (simulated), then redirect to success.
     */
    public function pay(Request $request)
    {
        $userId = session('user_id');
        if (!$userId) {
            return redirect()->route('auth.login.form');
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $now = now();
        $paymentId = (string) Str::uuid();
        $reference = 'TX-' . strtoupper(Str::random(10));

        DB::table('payments')->insert([
            'id' => $paymentId,
            'user_id' => $userId,
            'investment_id' => null,
            'withdrawal_request_id' => null,
            'payment_method_id' => null,
            'amount' => $validated['amount'],
            'currency' => 'SAR',
            'status' => 'completed', // simulate successful payment
            'transaction_reference' => $reference,
            'processed_at' => $now,
            'note' => 'Investor onboarding payment',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Optionally, we could update platform ledger here (skipped for simplicity)

        return redirect()->route('register.success');
    }

    /**
     * Success page after registration + payment.
     */
    public function success()
    {
        $onboarding = session('onboarding', []);
        return view('register-success', [
            'onboarding' => $onboarding,
        ]);
    }

    private function mapRangeToAmount(?string $range): float
    {
        if (!$range) { return 100.0; }
        // ranges like "1000-10000", "500000+"
        if (str_contains($range, '+')) {
            $base = (float) str_replace('+','', $range);
            return max(100.0, $base * 0.01); // 1% as a suggested initial deposit
        }
        if (str_contains($range, '-')) {
            [$min, $max] = explode('-', $range);
            $min = (float) preg_replace('/[^0-9.]/', '', $min);
            return max(100.0, $min * 0.05); // 5% of min as suggested deposit
        }
        // fallback fixed
        return 100.0;
    }
}
