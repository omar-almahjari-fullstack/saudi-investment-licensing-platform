<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\PaymentConfirmed;

class InvestmentPageController extends Controller
{
    public function index($type = null)
    {
        // Show packages (if any) and pass type
        $packages = DB::table('investment_packages')->orderBy('amount')->get();

        // Provide any existing identity images for the current visitor (prefer users.*, fall back to investors)
        $existingDocs = [];
        $uid = \Illuminate\Support\Facades\Auth::id() ?: session('user_id');
        if ($uid) {
            $user = DB::table('users')->where('id', $uid)->first();
            $inv = DB::table('investors')->where('user_id', $uid)->first();

            $front = null;
            $back = null;
            if ($user) {
                if (Schema::hasColumn('users', 'identity_image_front') && !empty($user->identity_image_front)) {
                    $front = $user->identity_image_front;
                } elseif (!empty($user->identity_image)) {
                    $front = $user->identity_image;
                }
                if (Schema::hasColumn('users', 'identity_image_back') && !empty($user->identity_image_back)) {
                    $back = $user->identity_image_back;
                }
            }
            if (empty($front) && $inv && !empty($inv->identity_image_front)) { $front = $inv->identity_image_front; }
            if (empty($back)  && $inv && !empty($inv->identity_image_back))  { $back  = $inv->identity_image_back; }

            if (!empty($front)) {
                try { $existingDocs['identity_front'] = [ 'url' => Storage::url($front), 'filename' => basename($front), 'path' => $front ]; } catch (\Throwable $e) { /* ignore */ }
            }
            if (!empty($back)) {
                try { $existingDocs['identity_back'] = [ 'url' => Storage::url($back), 'filename' => basename($back), 'path' => $back ]; } catch (\Throwable $e) { /* ignore */ }
            }
        }

        // Special case: quick-start link should ensure the visitor is a registered investor
        if (strtolower($type) === 'quick') {
            // If visitor is not authenticated, send them to the login page first.
            $user = \Illuminate\Support\Facades\Auth::user();
            if (!$user) {
                return redirect()->guest(route('auth.login.form'));
            }

            // For authenticated users, require an investor profile and prior investments.
            $inv = DB::table('investors')->where('user_id', $user->id)->first();
            if (!$inv) {
                return redirect()->route('register.investor');
            }
            $hasInv = DB::table('investments')->where('investor_id', $inv->id)->exists();
            if (!$hasInv) {
                return redirect()->route('register.investor');
            }
        }

        return view('investment', ['type' => $type, 'packages' => $packages, 'existingDocs' => $existingDocs]);
    }

    public function confirm(Request $request)
    {
        // Require: amount, rate, level (optional), duration (optional)
        $data = [
            'level' => $request->query('level'),
            'amount' => (int) $request->query('amount', 0),
            'rate' => (int) $request->query('rate', 0),
            'duration' => $request->query('duration', '1 شهر'),
        ];
        // Resolve investor and basic details
        $user = \Illuminate\Support\Facades\Auth::user();
        if (!$user) { $uid = session('user_id'); if ($uid) { $user = \App\Models\User::find($uid); } }
        // If no authenticated user, require login before showing confirm page.
        if (!$user) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['ok' => false, 'message' => 'غير مسجل الدخول'], 401);
            }
            // Use guest redirect so Laravel stores intended URL and sends user back after login
            return redirect()->guest(route('auth.login.form'))
                ->with('status', 'الرجاء تسجيل الدخول لإكمال الطلب');
        }

        $investor = \App\Models\Investor::where('user_id', $user?->id)->first();

        return view('investment-confirm', compact('data','user','investor'));
    }

    public function submit(Request $request)
    {
        $request->validate([
            'amount' => 'required|integer|min:1',
            'rate' => 'required|integer|min:1',
            'bank_account' => 'nullable|string|max:128',
            'duration_months' => 'nullable|integer|min:1',
            'risk_level' => 'nullable|string',
            'notes' => 'nullable|string',
            // optional owner national id (may be submitted on investment forms)
            'national_id' => 'nullable|string|max:50|regex:/^[0-9]{6,20}$/',
        ]);
        // Resolve user
        $user = \Illuminate\Support\Facades\Auth::user();
        if (!$user) { $uid = session('user_id'); if ($uid) { $user = \App\Models\User::find($uid); } }
        if (!$user) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['ok' => false, 'message' => 'غير مسجل الدخول'], 401);
            }
            return redirect()->guest(route('auth.login.form'))
                ->with('status', 'الرجاء تسجيل الدخول لإكمال الطلب');
        }

        $amount = (int) $request->input('amount');
        $rate = (int) $request->input('rate');
        $durationMonths = (int) ($request->input('duration_months') ?: 1);
        $expectedReturn = (int) round($amount * $rate / 100);
        $riskLevel = $request->input('risk_level') ?: 'منخفضة';

        // Determine isLarge from submitted investment_type or heuristics (amount-based)
        $submittedType = $request->input('investment_type');
        $isLarge = false;
        if ($submittedType && in_array($submittedType, ['large','small'])) {
            $isLarge = $submittedType === 'large';
        } else {
            // fallback: treat >= 50000 as large
            $isLarge = $amount >= 50000;
        }

        // Resolve investor
        $investor = \App\Models\Investor::where('user_id', $user->id)->first();
        if (!$investor) {
            return redirect()->route('register.investor')->with('status','الرجاء إكمال تسجيل المستثمر أولاً');
        }

        // Optional package mapping by amount & rate (if such package exists)
        $packageId = null;
        if (Schema::hasTable('investment_packages')) {
            $pkg = DB::table('investment_packages')
                ->whereRaw('CAST(amount AS DECIMAL(15,2)) = ?', [number_format($amount, 2, '.', '')])
                ->whereRaw('CAST(profit_rate AS DECIMAL(5,2)) = ?', [number_format($rate, 2, '.', '')])
                ->select('id')
                ->first();
            if ($pkg) { $packageId = $pkg->id; }
        }

        $id = (string) \Illuminate\Support\Str::uuid();
        $row = [
            'id' => $id,
            'investor_id' => $investor->id,
            'amount' => $amount,
            'profit_rate' => $rate,
            'expected_return' => $expectedReturn,
            'duration_months' => $durationMonths,
            'risk_level' => $riskLevel,
            'notes' => $request->input('notes'),
            'investment_type' => $request->input('investment_type') ?: ($isLarge ? 'large' : 'small'),
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ];
        if (Schema::hasColumn('investments','package_id') && $packageId) {
            $row['package_id'] = $packageId;
        }
        // Do not persist bank account on investments table. Bank account lives on investor profile.

        DB::table('investments')->insert($row);

        // If the user submitted national_id here and users.national_id is empty, persist it (first-write only)
        try {
            if (!empty($request->input('national_id')) && Schema::hasColumn('users', 'national_id')) {
                $userRow = DB::table('users')->where('id', $user->id)->first();
                if (!empty($userRow->national_id) && trim($userRow->national_id) !== trim($request->input('national_id'))) {
                    // Do not overwrite existing national_id
                    return redirect()->back()->withInput()->withErrors(['national_id' => 'رقم الهوية مسجّل مسبقاً ولا يمكن تغييره.']);
                }
                if ($userRow && empty($userRow->national_id)) {
                    DB::table('users')->where('id', $user->id)->update(['national_id' => $request->input('national_id')]);
                }
            }
        } catch (\Throwable $e) {
            report($e);
        }
        // If AJAX/JSON request, return JSON instead of redirect
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'ok' => true,
                'message' => 'تم حفظ الاستثمار بنجاح. سنقوم بمراجعة طلبك وإتمام الإجراءات قريبًا.',
                'investment_id' => $id,
            ]);
        }

        return redirect()->route('investor.dashboard')->with('status','investment_submitted');
    }

    public function store(Request $request) { abort(405); }
    public function update(Request $request, $id = null) { abort(405); }
    public function destroy($id = null) { abort(405); }

    /**
     * Decide where to send the visitor when they click the public "create investment" button.
     * - If not authenticated, redirect to login with intended target set to the quick flow.
     * - If authenticated (or session user_id), and the user has existing investments, send them
     *   to their investor investments page (detail of most recent investment).
     * - Otherwise send them to the quick investment page or investor onboarding as appropriate.
     */
    public function checkRedirect(Request $request)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        if (!$user && session()->has('user_id')) {
            $user = \App\Models\User::find(session('user_id'));
        }

        // Not signed in: send to login and preserve intended destination
        if (!$user) {
            // Use the login form route used elsewhere in the app and provide a redirect param
            return redirect()->guest(route('auth.login.form') . '?redirect=' . urlencode('/investment/quick'));
        }

        // If the user already has any investments, send them to the investment levels center
        // Note: investments are stored against an Investor record (investor_id), so detect via Investor
        try {
            $invProfile = \App\Models\Investor::where('user_id', $user->id)->first();
            if ($invProfile) {
                $hasInv = \Illuminate\Support\Facades\DB::table('investments')->where('investor_id', $invProfile->id)->exists();
                if ($hasInv) {
                    return redirect()->route('investment.levels');
                }
            }
        } catch (\Throwable $e) {
            // on any DB error, fall back to the quick flow
            report($e);
        }

        // If the user doesn't have an investor profile, send them to investor onboarding
        $invProfile = \App\Models\Investor::where('user_id', $user->id)->first();
        if (!$invProfile) {
            return redirect()->route('register.investor');
        }

        // Otherwise continue to the quick investment page
        return redirect('/investment/quick');
    }

    public function pay(Request $request)
    {
        // This endpoint records a payment for an investment by the current investor
        $request->validate([
            'method' => 'required|string|in:card,apple,bank',
            'amount' => 'nullable|numeric',
            'investment_id' => 'nullable|string',
            // Bank transfer optional fields
            'sender_name' => 'nullable|string',
            'transfer_at' => 'nullable|date',
            'note' => 'nullable|string',
            'reference' => 'nullable|string',
            // Allow receipts up to 10MB to accommodate larger camera images
            'receipt' => 'nullable|file|max:10240', // up to 10MB
        ]);

        // Resolve user
        $user = \Illuminate\Support\Facades\Auth::user();
        if (!$user) { $uid = session('user_id'); if ($uid) { $user = \App\Models\User::find($uid); } }
        if (!$user) { return response()->json(['ok'=>false,'message'=>'غير مسجل الدخول'], 401); }

        // Resolve investor and investment (use provided investment_id if present)
        $investor = \App\Models\Investor::where('user_id', $user->id)->first();
        if (!$investor) { return response()->json(['ok'=>false,'message'=>'لا يوجد ملف مستثمر'], 422); }
        $investmentId = $request->input('investment_id');
        if ($investmentId) {
            $investment = DB::table('investments')
                ->where('id', $investmentId)
                ->where('investor_id', $investor->id)
                ->first();
        } else {
            $investment = DB::table('investments')
                ->where('investor_id', $investor->id)
                ->orderByDesc('created_at')
                ->first();
        }
        if (!$investment) { return response()->json(['ok'=>false,'message'=>'لا يوجد استثمار مسجل'], 422); }

        $amount = $request->input('amount');
        if (!$amount) { $amount = (float) $investment->amount; }

        // map UI method -> payment_methods.name
        $map = [ 'card' => 'Credit Card', 'apple' => 'Apple Pay', 'bank' => 'Bank Transfer' ];
        $methodName = $map[$request->input('method')] ?? 'Credit Card';
        $methodRow = DB::table('payment_methods')->where('name',$methodName)->first();
        $paymentMethodId = $methodRow->id ?? null;

        $now = now();
        $paymentId = (string) \Illuminate\Support\Str::uuid();
        // Prefer provided bank reference if present, otherwise generate
        $providedRef = trim((string) $request->input('reference', ''));
        // Treat placeholder or obvious dummy references (like the UI default 'INV-XXXXXX') as empty
        if ($providedRef === '' || preg_match('/^INV-?X+$/i', $providedRef) || strtoupper($providedRef) === 'INV-XXXXXX') {
            $providedRef = '';
        }

        // If a client provided a reference, prefer it but ensure uniqueness. Otherwise generate a TX-... reference.
        $reference = $providedRef !== '' ? $providedRef : ('TX-' . strtoupper(\Illuminate\Support\Str::random(10)));

        // Ensure the reference is unique in payments table; if collision, append random suffix or regenerate
        $attempt = 0;
        while (DB::table('payments')->where('transaction_reference', $reference)->exists() && $attempt < 6) {
            $attempt++;
            if ($providedRef !== '') {
                $reference = $providedRef . '-' . strtoupper(\Illuminate\Support\Str::random(4));
            } else {
                $reference = 'TX-' . strtoupper(\Illuminate\Support\Str::random(10 + $attempt));
            }
        }

        // Determine statuses and optional bank metadata
    $isBank = $request->input('method') === 'bank';
        $status = $isBank ? 'pending' : 'completed';
        $processedAt = $isBank ? null : $now;
        $senderName = $request->input('sender_name');
        $transferAt = $request->input('transfer_at');
        $userNote = $request->input('note');
        $receiptPath = null;
        // Read bank account from the investor profile (do not rely on investments.user_bank_account)
        $invBankAccount = $investor->bank_account ?? null;
        if ($isBank && $request->hasFile('receipt')) {
            try {
                // store on the public disk so Storage::url() resolves to /storage/...
                $receiptPath = $request->file('receipt')->store('receipts', 'public');
                // Ensure the file is available under public/storage when storage:link is not present
                try {
                    $storageFull = storage_path('app/public/' . ltrim($receiptPath, '/'));
                    $publicFull = public_path('storage/' . ltrim($receiptPath, '/'));
                    if (file_exists($storageFull) && !file_exists($publicFull)) {
                        @mkdir(dirname($publicFull), 0755, true);
                        @copy($storageFull, $publicFull);
                    }
                } catch (\Throwable $_e) {
                    // non-fatal mirror failure; continue — admin UI will still work if storage:link exists
                    report($_e);
                }
            } catch (\Throwable $e) {
                // return an error so uploader knows the receipt wasn't saved
                return response()->json(['ok' => false, 'message' => 'فشل حفظ الإيصال على الخادم. حاول مرة أخرى.'], 500);
            }
        }

    DB::transaction(function() use ($user, $investment, $amount, $paymentMethodId, $now, $paymentId, $reference, $status, $processedAt, $isBank, $senderName, $transferAt, $userNote, $receiptPath, $invBankAccount) {
            // Insert payment as completed (demo flow)
            DB::table('payments')->insert([
                'id' => $paymentId,
                'user_id' => $user->id,
                'investment_id' => $investment->id,
                'withdrawal_request_id' => null,
                'payment_method_id' => $paymentMethodId,
                'amount' => $amount,
                'currency' => 'SAR',
                'status' => $status,
                'transaction_reference' => $reference,
                'processed_at' => $processedAt,
                'note' => $isBank
                    ? json_encode(array_filter([
                        'type' => 'bank_transfer',
                        'sender_name' => $senderName,
                        'transfer_at' => $transferAt,
                        'reference' => $reference,
                        'receipt_path' => $receiptPath,
                        'note' => $userNote,
                        'user_bank_account' => $invBankAccount,
                    ]))
                    : 'Investment payment',
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // Insert investment transaction (credit) only when payment is completed
            if ($status === 'completed') {
                DB::table('investment_transactions')->insert([
                    'id' => (string) \Illuminate\Support\Str::uuid(),
                    'investment_id' => $investment->id,
                    'user_id' => $user->id,
                    'type' => 'credit',
                    'amount' => $amount,
                    'note' => 'Initial investment funding',
                    'occurred_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            // Update investment status only when payment is completed
            if ($status === 'completed') {
                DB::table('investments')->where('id',$investment->id)->update([
                    'status' => 'active',
                    'updated_at' => $now,
                ]);
            }
        });

        // Send confirmation email for instant payments
        if (!$isBank) {
            try {
                $userName = $user->full_name ?? ($user->username ?? 'عميل');
                Mail::to($user->email)->send(new PaymentConfirmed($userName, (float)$amount, 'SAR', $reference, $investment->id));
            } catch (\Throwable $e) {
                // swallow mail errors in demo
            }

            // Record platform transaction and update main account balance
            try {
                $account = DB::table('platform_accounts')
                    ->where('type','main')->where('is_active',1)
                    ->orderBy('created_at','asc')
                    ->first();
                if ($account) {
                    $before = (float) $account->balance;
                    $after = $before + (float) $amount;
                    DB::table('platform_accounts')->where('id',$account->id)->update([
                        'balance' => $after,
                        'updated_at' => now(),
                    ]);
                    DB::table('platform_transactions')->insert([
                        'id' => (string) \Illuminate\Support\Str::uuid(),
                        'platform_account_id' => $account->id,
                        'payment_id' => $paymentId,
                        'withdrawal_request_id' => null,
                        'investment_transaction_id' => null,
                        'type' => 'credit',
                        'amount' => (float) $amount,
                        'balance_before' => $before,
                        'balance_after' => $after,
                        'description' => 'User payment received',
                        'transaction_date' => now(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            } catch (\Throwable $e) {
                // ignore ledger errors in demo
            }
        }

        $message = $isBank
            ? 'تم استلام تفاصيل التحويل البنكي. سيتم تأكيد العملية خلال 24 ساعة.'
            : 'تم استلام الدفع وتفعيل الاستثمار. شكراً لك!';

        return response()->json([
            'ok' => true,
            'message' => $message,
            'payment_reference' => $reference,
            'status' => $status,
        ]);
    }
}
