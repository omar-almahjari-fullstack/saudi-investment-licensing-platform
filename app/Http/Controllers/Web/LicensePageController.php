<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;

class LicensePageController extends Controller
{
    public function index()
    {
        // If the visitor isn't authenticated, enforce verification:
        // - if a national_id is provided and matches an existing user/investor,
        //   redirect to login so they can authenticate and return to this page.
        // - if a national_id is provided but no user exists, redirect to register
        //   with the national_id prefilled so they can create an account and then
        //   continue the license request.
        // - otherwise default to login with redirect back to this page.
        $authId = Auth::id();
        $sessionUid = session('user_id');
        if (empty($authId) && empty($sessionUid)) {
            $nid = request()->query('national_id') ?: request()->input('national_id');
            if (!empty($nid)) {
                // guard DB lookups in case the column doesn't exist in some envs
                $userRow = Schema::hasColumn('users', 'national_id') ? DB::table('users')->where('national_id', $nid)->first() : null;
                $invRow = Schema::hasColumn('investors', 'national_id') ? DB::table('investors')->where('national_id', $nid)->first() : null;
                if ($userRow || $invRow) {
                    return redirect()->to(route('auth.login.form') . '?redirect=' . urlencode(request()->fullUrl()));
                }
                return redirect()->to(route('auth.register.form') . '?redirect=' . urlencode(request()->fullUrl()) . '&national_id=' . urlencode($nid));
            }
            return redirect()->to(route('auth.login.form') . '?redirect=' . urlencode(request()->fullUrl()));
        }

        // Could show a count or recent licenses as examples
        $count = DB::table('licenses')->count();

        // Provide any existing identity images (prefer users table, fall back to investors)
        $existingDocs = [];
        $uid = Auth::id() ?: session('user_id');
        if ($uid) {
            $user = DB::table('users')->where('id', $uid)->first();
            $inv = DB::table('investors')->where('user_id', $uid)->first();

            $front = null;
            $back = null;
            if ($user) {
                // prefer explicit front/back fields if present, otherwise legacy identity_image
                if (Schema::hasColumn('users', 'identity_image_front') && !empty($user->identity_image_front)) {
                    $front = $user->identity_image_front;
                } elseif (!empty($user->identity_image)) {
                    $front = $user->identity_image;
                }
                if (Schema::hasColumn('users', 'identity_image_back') && !empty($user->identity_image_back)) {
                    $back = $user->identity_image_back;
                }
            }
            // fallback to investors table
            if (empty($front) && $inv && !empty($inv->identity_image_front)) { $front = $inv->identity_image_front; }
            if (empty($back)  && $inv && !empty($inv->identity_image_back))  { $back  = $inv->identity_image_back; }

            if (!empty($front)) {
                $existingDocs['identity_front'] = [
                    'url' => Storage::url($front),
                    'filename' => basename($front),
                    'path' => $front,
                ];
            }
            if (!empty($back)) {
                $existingDocs['identity_back'] = [
                    'url' => Storage::url($back),
                    'filename' => basename($back),
                    'path' => $back,
                ];
            }
        }

        // Provide owner information (prefer investor row, then user)
        $owner = [
            'full_name' => null,
            'national_id' => null,
            'email' => null,
            'phone' => null,
        ];
        if ($uid) {
            if (!empty($inv)) {
                $owner['full_name'] = $inv->full_name ?? null;
                $owner['national_id'] = $inv->national_id ?? null;
                $owner['email'] = $inv->email ?? null;
                $owner['phone'] = $inv->phone ?? null;
            }
            if ($user) {
                // prefer user values when available
                $owner['full_name'] = $user->full_name ?? $owner['full_name'];
                $owner['national_id'] = $user->national_id ?? $owner['national_id'];
                $owner['email'] = $user->email ?? $owner['email'];
                $owner['phone'] = $user->phone ?? $owner['phone'];
            }
        }

        return view('license', compact('count', 'existingDocs', 'owner'));
    }

    public function store(Request $request)
    {
        // If visitor is not authenticated/sessioned, enforce same verification
        // as the GET page: check provided national_id and redirect to login
        // or register accordingly so they return to this flow after auth.
        $authId = Auth::id();
        $sessionUid = session('user_id');
        if (empty($authId) && empty($sessionUid)) {
            $nid = $request->input('national_id') ?: $request->query('national_id');
            if (!empty($nid)) {
                $userRow = Schema::hasColumn('users', 'national_id') ? DB::table('users')->where('national_id', $nid)->first() : null;
                $invRow = Schema::hasColumn('investors', 'national_id') ? DB::table('investors')->where('national_id', $nid)->first() : null;
                if ($userRow || $invRow) {
                    return redirect()->to(route('auth.login.form') . '?redirect=' . urlencode(request()->fullUrl()));
                }
                return redirect()->to(route('auth.register.form') . '?redirect=' . urlencode(request()->fullUrl()) . '&national_id=' . urlencode($nid));
            }
            return redirect()->to(route('auth.login.form') . '?redirect=' . urlencode(request()->fullUrl()));
        }

        $user = Auth::user();
        $uid = $user ? $user->id : session('user_id');
        if (!$uid) { return redirect()->route('auth.login.form'); }

        // Determine existing owner name so we can decide whether full_name must be provided
        $userRow = DB::table('users')->where('id', $uid)->first();
        $invRow = DB::table('investors')->where('user_id', $uid)->first();
        $existingOwnerFull = $invRow->full_name ?? ($userRow->full_name ?? null);

        $fullNameRule = empty($existingOwnerFull) ? 'required|string|max:255' : 'sometimes|nullable|string|max:255';

        // Determine if the user already has identity images so we can require uploads only when missing
        $hasFront = false; $hasBack = false;
        if ($userRow) {
            if (Schema::hasColumn('users', 'identity_image_front') && !empty($userRow->identity_image_front)) { $hasFront = true; }
            if (Schema::hasColumn('users', 'identity_image_back')  && !empty($userRow->identity_image_back))  { $hasBack = true; }
            if (!$hasFront && !empty($userRow->identity_image)) { $hasFront = true; }
        }
        if (!$hasFront && $invRow) {
            if (!empty($invRow->identity_image_front) || !empty($invRow->identity_image)) { $hasFront = true; }
        }
        if (!$hasBack && $invRow) {
            if (!empty($invRow->identity_image_back)) { $hasBack = true; }
        }

        // File validation rules: require identity front/back only when missing for this user
        $identityFrontRule = $hasFront ? 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120' : 'required|file|mimes:jpg,jpeg,png,pdf|max:5120';
        $identityBackRule  = $hasBack  ? 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120' : 'required|file|mimes:jpg,jpeg,png,pdf|max:5120';

        $data = $request->validate([
            'business_name' => 'required|string|max:255',
            'business_type' => 'required|string|max:255',
            'activity_description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            // owner info (stored optionally or for future audit)
            'full_name' => $fullNameRule,
            'national_id' => 'required|string|max:50|regex:/^[0-9]{6,20}$/',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:50',
            // require identity files server-side when missing
            'identity_front' => $identityFrontRule,
            'identity_back' => $identityBackRule,
        ]);

        $now = now();
        $id = (string) Str::uuid();

    DB::beginTransaction();
        try {
            DB::table('licenses')->insert([
                'id' => $id,
                'user_id' => $uid,
                'business_name' => $data['business_name'],
                'business_type' => $data['business_type'],
                'activity_description' => $data['activity_description'] ?? null,
                'location' => $data['location'] ?? null,
                'status' => 'pending',
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // base dir for license attachments
            $baseDir = 'licenses/' . $uid . '/' . $id;

            // Handle identity images: do NOT overwrite existing identity images.
            // Determine if user/investor already has identity images and only accept uploads when missing.
            $hasFront = false; $hasBack = false;
            if ($userRow) {
                if (Schema::hasColumn('users', 'identity_image_front') && !empty($userRow->identity_image_front)) { $hasFront = true; }
                if (Schema::hasColumn('users', 'identity_image_back')  && !empty($userRow->identity_image_back))  { $hasBack = true; }
                if (!$hasFront && !empty($userRow->identity_image)) { $hasFront = true; }
            }
            if (!$hasFront && $invRow) {
                if (!empty($invRow->identity_image_front) || !empty($invRow->identity_image)) { $hasFront = true; }
            }
            if (!$hasBack && $invRow) {
                if (!empty($invRow->identity_image_back)) { $hasBack = true; }
            }

            // Accept uploaded front only when missing
            if (!$hasFront && $request->hasFile('identity_front')) {
                $file = $request->file('identity_front');
                $name = 'identity-front-' . now()->format('YmdHis') . '.' . $file->getClientOriginalExtension();
                // store in investors/{username} to match other flows
                $safeName = preg_replace('/[^\p{L}\p{N}_-]+/u', '-', ($userRow->full_name ?? $uid));
                $baseDir = 'investors/' . ($safeName ?: $uid);
                Storage::disk('public')->makeDirectory($baseDir);
                $path = $file->storeAs($baseDir, $name, 'public');
                // ensure public-facing copy exists
                try {
                    $storageFull = storage_path('app/public/' . ltrim($path, '/'));
                    $publicFull = public_path('storage/' . ltrim($path, '/'));
                    if (file_exists($storageFull) && !file_exists($publicFull)) {
                        @mkdir(dirname($publicFull), 0755, true);
                        @copy($storageFull, $publicFull);
                    }
                } catch (\Throwable $e) { report($e); }

                if (Schema::hasColumn('users', 'identity_image_front')) {
                    DB::table('users')->where('id', $uid)->update(['identity_image_front' => $path]);
                } elseif (Schema::hasColumn('investors', 'identity_image_front')) {
                    DB::table('investors')->where('user_id', $uid)->update(['identity_image_front' => $path]);
                } else {
                    DB::table('documents')->insert([
                        'id' => (string) Str::uuid(),
                        'user_id' => (string) $uid,
                        'filename' => $file->getClientOriginalName(),
                        'path' => $path,
                        'type' => 'identity_front',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }

            // Accept uploaded back only when missing
            if (!$hasBack && $request->hasFile('identity_back')) {
                $file = $request->file('identity_back');
                $name = 'identity-back-' . now()->format('YmdHis') . '.' . $file->getClientOriginalExtension();
                $safeName = preg_replace('/[^\p{L}\p{N}_-]+/u', '-', ($userRow->full_name ?? $uid));
                $baseDir = 'investors/' . ($safeName ?: $uid);
                Storage::disk('public')->makeDirectory($baseDir);
                $path = $file->storeAs($baseDir, $name, 'public');
                try {
                    $storageFullB = storage_path('app/public/' . ltrim($path, '/'));
                    $publicFullB = public_path('storage/' . ltrim($path, '/'));
                    if (file_exists($storageFullB) && !file_exists($publicFullB)) {
                        @mkdir(dirname($publicFullB), 0755, true);
                        @copy($storageFullB, $publicFullB);
                    }
                } catch (\Throwable $e) { report($e); }

                if (Schema::hasColumn('users', 'identity_image_back')) {
                    DB::table('users')->where('id', $uid)->update(['identity_image_back' => $path]);
                } elseif (Schema::hasColumn('investors', 'identity_image_back')) {
                    DB::table('investors')->where('user_id', $uid)->update(['identity_image_back' => $path]);
                } else {
                    DB::table('documents')->insert([
                        'id' => (string) Str::uuid(),
                        'user_id' => (string) $uid,
                        'filename' => $file->getClientOriginalName(),
                        'path' => $path,
                        'type' => 'identity_back',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }

            // License-specific docs — prefer to store paths on licenses table if columns exist, otherwise create documents records
            if ($request->hasFile('bank_statement')) {
                $file = $request->file('bank_statement');
                $name = 'bank_statement_' . now()->format('YmdHis') . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs($baseDir, $name, 'public');
                // ensure public-facing copy exists (mirror storage/app/public -> public/storage)
                try {
                    $storageFull = storage_path('app/public/' . ltrim($path, '/'));
                    $publicFull = public_path('storage/' . ltrim($path, '/'));
                    if (file_exists($storageFull) && !file_exists($publicFull)) {
                        @mkdir(dirname($publicFull), 0755, true);
                        @copy($storageFull, $publicFull);
                    }
                } catch (\Throwable $e) { report($e); }
                if (Schema::hasColumn('licenses', 'bank_statement')) {
                    DB::table('licenses')->where('id', $id)->update(['bank_statement' => $path, 'updated_at' => now()]);
                } else {
                    DB::table('documents')->insert([
                        'id' => (string) Str::uuid(),
                        'user_id' => (string) $uid,
                        'filename' => $file->getClientOriginalName(),
                        'path' => $path,
                        'type' => 'bank_statement',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
            if ($request->hasFile('salary_certificate')) {
                $file = $request->file('salary_certificate');
                $name = 'salary_certificate_' . now()->format('YmdHis') . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs($baseDir, $name, 'public');
                // ensure public-facing copy exists (mirror storage/app/public -> public/storage)
                try {
                    $storageFull = storage_path('app/public/' . ltrim($path, '/'));
                    $publicFull = public_path('storage/' . ltrim($path, '/'));
                    if (file_exists($storageFull) && !file_exists($publicFull)) {
                        @mkdir(dirname($publicFull), 0755, true);
                        @copy($storageFull, $publicFull);
                    }
                } catch (\Throwable $e) { report($e); }
                if (Schema::hasColumn('licenses', 'salary_certificate')) {
                    DB::table('licenses')->where('id', $id)->update(['salary_certificate' => $path, 'updated_at' => now()]);
                } else {
                    DB::table('documents')->insert([
                        'id' => (string) Str::uuid(),
                        'user_id' => (string) $uid,
                        'filename' => $file->getClientOriginalName(),
                        'path' => $path,
                        'type' => 'salary_certificate',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }

            DB::commit();

            // If request expects JSON (AJAX), return minimal payload compatible with existing JS success screen
            if ($request->expectsJson()) {
                return response()->json(['id' => $id, 'status' => 'pending']);
            }

                // Persist national_id to users table on first write (if column present and empty)
                try {
                    if (Schema::hasColumn('users', 'national_id')) {
                        $userRow = DB::table('users')->where('id', $uid)->first();
                        if (!empty($userRow->national_id) && trim($userRow->national_id) !== trim($data['national_id'] ?? '')) {
                            // Do not overwrite an existing national_id
                            DB::rollBack();
                            return back()->withInput()->withErrors(['national_id' => 'رقم الهوية مسجّل مسبقاً ولا يمكن تغييره.']);
                        }
                        if ($userRow && empty($userRow->national_id) && !empty($data['national_id'])) {
                            DB::table('users')->where('id', $uid)->update(['national_id' => $data['national_id']]);
                        }
                    }
                } catch (\Throwable $e) {
                    // Non-fatal: do not block the user if we can't persist national_id; log for later
                    report($e);
                }

                // Persist full_name to users/investors on first write (store once when provided)
                try {
                    if (!empty($data['full_name'])) {
                        if (Schema::hasColumn('users', 'full_name')) {
                            $userRow = DB::table('users')->where('id', $uid)->first();
                            if ($userRow && empty($userRow->full_name)) {
                                DB::table('users')->where('id', $uid)->update(['full_name' => $data['full_name']]);
                            }
                        }
                        if (Schema::hasColumn('investors', 'full_name')) {
                            $invRow = DB::table('investors')->where('user_id', $uid)->first();
                            if ($invRow && empty($invRow->full_name)) {
                                DB::table('investors')->where('user_id', $uid)->update(['full_name' => $data['full_name']]);
                            }
                        }
                    }
                } catch (\Throwable $e) {
                    report($e);
                }

            return redirect()->route('license')->with('status', 'license_submitted:'.$id);
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);
            return back()->withInput()->withErrors(['submit' => 'حدث خطأ أثناء معالجة الطلب.']);
        }
    }
    public function update() { abort(405); }
    public function destroy() { abort(405); }

    public function pay(Request $request)
    {
        $request->validate([
            'method' => 'required|string|in:card,apple,bank',
            'amount' => 'nullable|numeric',
            'license_id' => 'required|string',
            'sender_name' => 'nullable|string',
            'transfer_at' => 'nullable|date',
            'note' => 'nullable|string',
            'reference' => 'nullable|string',
            'receipt' => 'nullable|file|max:10240',
        ]);

        $user = \Illuminate\Support\Facades\Auth::user();
        if (!$user) { $uid = session('user_id'); if ($uid) { $user = \App\Models\User::find($uid); } }
        if (!$user) { return response()->json(['ok'=>false,'message'=>'غير مسجل الدخول'], 401); }

        $licenseId = $request->input('license_id');
        $license = DB::table('licenses')->where('id', $licenseId)->first();
        if (!$license) { return response()->json(['ok'=>false,'message'=>'لا يوجد ترخيص مسجل'], 422); }

        $amount = $request->input('amount') ?: 0;
        $isBank = $request->input('method') === 'bank';
        $status = $isBank ? 'pending' : 'completed';
        $providedRef = trim((string) $request->input('reference',''));
        if ($providedRef === '' || strtoupper($providedRef) === 'INV-XXXXXX') { $providedRef = ''; }
        $reference = $providedRef !== '' ? $providedRef : ('TX-'.strtoupper(Str::random(10)));

        // ensure unique reference
        $attempt = 0;
        while (DB::table('payments')->where('transaction_reference', $reference)->exists() && $attempt < 6) {
            $attempt++; $reference = $providedRef !== '' ? $providedRef . '-' . strtoupper(Str::random(4)) : 'TX-' . strtoupper(Str::random(10+$attempt));
        }

        $receiptPath = null;
        if ($isBank && $request->hasFile('receipt')) {
            try { $receiptPath = $request->file('receipt')->store('receipts', 'public'); } catch (\Throwable $e) { /* ignore */ }
        }

        $now = now();
        $paymentId = (string) Str::uuid();

        DB::table('payments')->insert([
            'id' => $paymentId,
            'user_id' => $user->id,
            'investment_id' => null,
            'withdrawal_request_id' => null,
            'payment_method_id' => null,
            'amount' => $amount,
            'currency' => 'SAR',
            'status' => $status,
            'transaction_reference' => $reference,
            'processed_at' => $isBank ? null : $now,
            'note' => json_encode(array_filter([
                'type' => $isBank ? 'bank_transfer' : 'payment',
                'license_id' => $licenseId,
                'sender_name' => $request->input('sender_name'),
                'transfer_at' => $request->input('transfer_at'),
                'reference' => $reference,
                'receipt_path' => $receiptPath,
                'note' => $request->input('note'),
            ])),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $message = $isBank ? 'تم استلام تفاصيل التحويل البنكي. سيتم تأكيد العملية لاحقاً.' : 'تم استلام الدفع بنجاح.';
        return response()->json(['ok'=>true,'message'=>$message,'payment_reference'=>$reference,'status'=>$status]);
    }
}
