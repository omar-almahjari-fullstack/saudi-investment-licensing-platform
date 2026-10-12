<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Investment;
use App\Models\License;
use App\Models\Setting;
use App\Models\ContactInfo;
use App\Models\InvestmentPackage;
use App\Models\NavigationItem;
use App\Models\Document;
use App\Models\BankAccount;
use App\Models\Permission;
use App\Models\RolePermission;
use App\Models\UserPermission;
use ZipArchive;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AdminController extends Controller
{
    // Effective permissions for current user (role + user-specific)
    private function effectivePermissions($user)
    {
        if (!$user) { return collect(); }
        $roleKeys = RolePermission::where('role', $user->role)->pluck('permission_key');
        $userKeys = UserPermission::where('user_id', $user->id)->pluck('permission_key');
        return $roleKeys->merge($userKeys)->unique()->values();
    }

    private function hasPerm(string $key): bool
    {
        $u = Auth::user();
        if (!$u) { return false; }
        // Admin role has full access by convention
        if ($u->role === 'admin') { return true; }
        return $this->effectivePermissions($u)->contains($key);
    }

    // Return current user's effective permissions (for UI gating)
    public function myPermissions()
    {
        if (!Auth::check()) { return response()->json([], 401); }
        return response()->json($this->effectivePermissions(Auth::user()));
    }
    // عرض صفحة تسجيل دخول الإدارة
    public function showLogin()
    {
        return view('admin.login');
    }

    // معالجة تسجيل الدخول
    public function login(Request $request)
    {
        // Allow either email or username in a single field
        $request->validate([
            'password' => 'required',
            'email' => 'required_without:username',
            'username' => 'required_without:email',
        ]);

        $identifier = $request->input('email', $request->input('username'));
        // allow login via email or username, for admin or staff
        $user = User::where(function($q) use ($identifier){
                $q->where('email', $identifier)->orWhere('username', $identifier);
            })
            ->whereIn('role', ['admin','staff'])
            ->first();
        // For security: always return a generic error message on failure
        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withInput()->withErrors(['email' => 'بيانات الدخول غير صحيحة']);
        }

        // Success: login
        Auth::login($user);
        $request->session()->regenerate();
        return redirect()->route('admin.dashboard');
    }

    // عرض لوحة تحكم الإدارة (Blade)
    public function dashboard()
    {
        return view('admin.dashboard_admin');
    }

    // Note: the public-facing bank/settings endpoint is implemented later in this
    // controller and returns a unified shape (account details + flags). We remove
    // this earlier helper to avoid duplicate method declarations.

    // Save bank settings from admin UI
    public function saveBankSettings(Request $request)
    {
        if (!Auth::check() || !$this->hasPerm('manage_settings')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $data = $request->validate([
            'ask_sender' => 'sometimes|boolean',
            'ask_datetime' => 'sometimes|boolean',
            'require_receipt' => 'sometimes|boolean',
            'instructions' => 'sometimes|nullable|string'
        ]);

        Setting::updateOrCreate(['key' => 'bank.ask_sender'], ['value' => (!empty($data['ask_sender']) && $data['ask_sender']) ? '1' : '0']);
        Setting::updateOrCreate(['key' => 'bank.ask_datetime'], ['value' => (!empty($data['ask_datetime']) && $data['ask_datetime']) ? '1' : '0']);
        Setting::updateOrCreate(['key' => 'bank.require_receipt'], ['value' => (!empty($data['require_receipt']) && $data['require_receipt']) ? '1' : '0']);
        if (array_key_exists('instructions', $data)) {
            Setting::updateOrCreate(['key' => 'bank.instructions'], ['value' => $data['instructions'] ?? '']);
        }

        return response()->json(['ok' => true]);
    }

    // إحصائيات JSON
    public function stats()
    {
        if (!Auth::check() || !$this->hasPerm('view_dashboard')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $users = User::count();
        $investments = Investment::count();
        $licenses = License::count();
        $totalAmount = Investment::sum('amount') ?: 0;

        return response()->json([
            'users' => $users,
            'investments' => $investments,
            'licenses' => $licenses,
            'total_investment_amount' => $totalAmount,
            'total_users' => $users,
            'total_investments' => $investments,
            'total_licenses' => $licenses,
            'total_investment_amount_formatted' => number_format($totalAmount, 0),
            'total_investment_amount_raw' => $totalAmount,
        ]);
    }

    // قائمة المستخدمين (JSON)
    public function users(Request $request)
    {
        if (!Auth::check() || !$this->hasPerm('view_users')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

    $limit = (int) $request->query('limit', 100);
        $q = trim((string)$request->query('q', ''));
        $usersQuery = User::orderBy('created_at', 'desc');
        if ($q !== '') {
            $usersQuery->where(function($w) use ($q) {
                $w->where('full_name', 'like', '%' . $q . '%')
                  ->orWhere('username', 'like', '%' . $q . '%')
                  ->orWhere('email', 'like', '%' . $q . '%')
                  ->orWhere('id', 'like', '%' . $q . '%');
            });
        }
    $users = $usersQuery->take($limit)->get(['id', 'full_name as full_name', 'username', 'email', 'phone', 'role', 'created_at', 'is_active']);

        return response()->json($users);
    }

    // إنشاء مستخدم
    public function createUser(Request $request)
    {
        if (!Auth::check() || !$this->hasPerm('manage_users')) { return response()->json(['message'=>'Unauthorized'],401); }
        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'username' => 'nullable|string|max:255|unique:users,username',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:255',
            'role' => 'required|string|in:admin,staff,investor',
            'is_active' => 'sometimes|boolean',
            'password' => 'required|string|min:6',
        ]);
        $u = new User();
        $u->id = (string) \Illuminate\Support\Str::uuid();
        $u->full_name = $data['full_name'];
        $u->username = $data['username'] ?? null;
        $u->email = $data['email'];
        // set phone if provided (stored on users table)
        $u->phone = $data['phone'] ?? null;
        $u->role = $data['role'];
        $u->is_active = (bool)($data['is_active'] ?? true);
        $u->password = \Illuminate\Support\Facades\Hash::make($data['password']);
        $u->save();
        return response()->json($u, 201);
    }

    // تعديل مستخدم
    public function updateUser($id, Request $request)
    {
        if (!Auth::check() || !$this->hasPerm('manage_users')) { return response()->json(['message'=>'Unauthorized'],401); }
        $u = User::find($id);
        if(!$u){ return response()->json(['message'=>'Not found'],404); }
        $data = $request->validate([
            'full_name' => 'sometimes|string|max:255',
            'username' => 'sometimes|nullable|string|max:255|unique:users,username,'.$u->id.',id',
            'email' => 'sometimes|email|max:255|unique:users,email,'.$u->id.',id',
            'phone' => 'sometimes|nullable|string|max:255',
            'role' => 'sometimes|string|in:admin,staff,investor',
            'is_active' => 'sometimes|boolean',
            'password' => 'sometimes|nullable|string|min:6',
        ]);

        // Prevent accidental overwrite of existing password when password field is empty.
        // Extract and remove password from $data before filling model attributes.
        $incomingPassword = null;
        if (array_key_exists('password', $data)) {
            $incomingPassword = is_string($data['password']) ? trim($data['password']) : $data['password'];
            unset($data['password']);
        }

        // Fill other attributes
        $u->fill($data);

        // Only hash & set a new password when a non-empty value was provided
        if ($incomingPassword !== null && $incomingPassword !== '') {
            $u->password = \Illuminate\Support\Facades\Hash::make($incomingPassword);
        }

        $u->save();
        return response()->json($u);
    }

    // حذف مستخدم
    public function deleteUser($id)
    {
        if (!Auth::check() || !$this->hasPerm('manage_users')) { return response()->json(['message'=>'Unauthorized'],401); }
        $u = User::find($id);
        if(!$u){ return response()->json(['message'=>'Not found'],404); }
        $u->delete();
        return response()->json(['message'=>'deleted']);
    }

    // قائمة الاستثمارات (JSON)
    public function investments(Request $request)
    {
        if (!Auth::check() || !$this->hasPerm('view_investments')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $q = trim((string)$request->query('q', ''));
        $investQuery = Investment::orderBy('created_at', 'desc');
        if ($q !== '') {
            $investQuery->where(function($w) use ($q) {
                $w->where('id', 'like', '%' . $q . '%')
                  ->orWhere('status', 'like', '%' . $q . '%')
                  ->orWhere('amount', 'like', '%' . $q . '%')
                  ->orWhere('created_at', 'like', '%' . $q . '%');
            });
        }
        $investments = $investQuery->take(50)->get();

        $payload = $investments->map(function($inv){
            // attempt to include basic investor info and public URLs for identity images
            $investor = null;
            $idFrontUrl = null;
            $idBackUrl = null;
            if (!empty($inv->investor_id)) {
                $investor = \App\Models\Investor::find($inv->investor_id);
                if ($investor) {
                    $idFront = $investor->identity_image_front ?: null;
                    $idBack = $investor->identity_image_back ?: null;
                    if ($idFront) {
                        if (preg_match('/^https?:\/\//i', $idFront)) {
                            $idFrontUrl = $idFront;
                        } else {
                            try {
                                $existsInDisk = \Illuminate\Support\Facades\Storage::disk('public')->exists($idFront);
                                $publicPath = public_path('storage/' . ltrim($idFront, '/'));
                                if ($existsInDisk && file_exists($publicPath)) {
                                    // public/storage symlink exists and file is reachable
                                    $idFrontUrl = \Illuminate\Support\Facades\Storage::url($idFront);
                                } elseif ($existsInDisk) {
                                    // file in storage/app/public but public/storage link missing -> use serve route
                                    $idFrontUrl = route('investor.image', ['investor' => $investor->id, 'side' => 'front']);
                                } else {
                                    $idFrontUrl = route('investor.image', ['investor' => $investor->id, 'side' => 'front']);
                                }
                            } catch (\Exception $e) {
                                $idFrontUrl = route('investor.image', ['investor' => $investor->id, 'side' => 'front']);
                            }
                        }
                    }
                    if ($idBack) {
                        if (preg_match('/^https?:\/\//i', $idBack)) {
                            $idBackUrl = $idBack;
                        } else {
                            try {
                                $existsInDisk = \Illuminate\Support\Facades\Storage::disk('public')->exists($idBack);
                                $publicPathB = public_path('storage/' . ltrim($idBack, '/'));
                                if ($existsInDisk && file_exists($publicPathB)) {
                                    $idBackUrl = \Illuminate\Support\Facades\Storage::url($idBack);
                                } elseif ($existsInDisk) {
                                    $idBackUrl = route('investor.image', ['investor' => $investor->id, 'side' => 'back']);
                                } else {
                                    $idBackUrl = route('investor.image', ['investor' => $investor->id, 'side' => 'back']);
                                }
                            } catch (\Exception $e) {
                                $idBackUrl = route('investor.image', ['investor' => $investor->id, 'side' => 'back']);
                            }
                        }
                    }
                }
            }

            return [
                'id' => $inv->id,
                'amount' => (float) $inv->amount,
                'investment_type' => $inv->investment_type,
                'profit_rate' => $inv->profit_rate,
                'expected_return' => $inv->expected_return,
                'status' => $inv->status,
                'created_at' => $inv->created_at,
                'investor' => $investor ? [
                    'id' => $investor->id,
                    'full_name' => $investor->full_name ?? null,
                    'email' => $investor->email ?? null,
                    'phone' => $investor->phone ?? null,
                    'identity_image_front' => $investor->identity_image_front ?? null,
                    'identity_image_back' => $investor->identity_image_back ?? null,
                    'id_front_url' => $idFrontUrl,
                    'id_back_url' => $idBackUrl,
                ] : null,
            ];
        });

        return response()->json($payload);
    }

    // Notify investor about a payment request (called from admin UI)
    public function notifyInvestorPayment($id, Request $request)
    {
        if (!Auth::check() || !$this->hasPerm('manage_investments')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $inv = Investment::find($id);
        if (!$inv) {
            return response()->json(['message' => 'Investment not found'], 404);
        }

        // find investor's user
        $investor = null;
        $user = null;
        if ($inv->investor_id) {
            $investor = \App\Models\Investor::find($inv->investor_id);
            if ($investor && $investor->user_id) {
                $user = \App\Models\User::find($investor->user_id);
            }
        }

        $note = $request->input('note', '');
        $highlight = $request->input('highlight', 0);
        $amount = $request->input('amount', null);
        // Do NOT prepend internal AMOUNT marker into stored note anymore. Keep note text clean.
        if ($highlight && $highlight != 0) {
            // do not inject a raw newline; use a space so the note remains a single clean line
            $note = '[HIGHLIGHT] ' . $note;
        }

        // Do NOT modify $inv->notes here. Only store a dedicated reminder record for querying.
        // Normalize amount input reliably (support Arabic-Indic digits and comma separators)
        $amountRaw = $request->input('amount', null);
        $amountNormalized = null;
        if ($amountRaw !== null && $amountRaw !== '') {
            try {
                $normalized = str_replace([',', '،', ' '], ['.', '.', ''], (string)$amountRaw);
                $arabicIndic = ['٠'=>'0','١'=>'1','٢'=>'2','٣'=>'3','٤'=>'4','٥'=>'5','٦'=>'6','٧'=>'7','٨'=>'8','٩'=>'9'];
                $normalized = strtr($normalized, $arabicIndic);
                $normalized = preg_replace('/[^0-9\.\-]/', '', $normalized);
                if (is_numeric($normalized)) {
                    $amountNormalized = round((float)$normalized, 2);
                }
            } catch (\Throwable $_ex) { $amountNormalized = null; }
        }

        try {
            $createData = [
                'investment_id' => $inv->id,
                'admin_id' => Auth::user() ? Auth::user()->id : null,
                'note' => $note,
            ];
            if (\Illuminate\Support\Facades\Schema::hasColumn('payment_reminders', 'amount') && $amountNormalized !== null) {
                $createData['amount'] = $amountNormalized;
            }

            $reminder = \App\Models\PaymentReminder::create($createData);
            try { \Illuminate\Support\Facades\Log::info('PaymentReminder created', ['id' => $reminder->id ?? null, 'investment_id' => $reminder->investment_id ?? null, 'admin_id' => $reminder->admin_id ?? null, 'amount' => $reminder->amount ?? null]); } catch (\Throwable $_) {}
        } catch (\Throwable $e) {
            // Fallback: try to create without amount to keep reminder functionality working
            try {
                \App\Models\PaymentReminder::create([
                    'investment_id' => $inv->id,
                    'admin_id' => Auth::user() ? Auth::user()->id : null,
                    'note' => $note,
                ]);
            } catch (\Throwable $e2) {
                \Illuminate\Support\Facades\Log::warning('PaymentReminder create failed: ' . $e2->getMessage());
            }
        }

        // send notification to the user if available
        if ($user) {
            try {
                // prepare a cleaned note for the user: remove internal markers and display amount nicely
                $displayNote = $note;
                // prefer explicit $amount parameter (sent separately) over any inline markers
                $amountDisplay = ($amount !== null && $amount !== '') ? trim((string)$amount) : null;
                // remove HIGHLIGHT markers if present
                $displayNote = preg_replace('/\[HIGHLIGHT\]\n?/u', '', $displayNote);
                // prepare final message
                $finalMsg = '';
                if ($amountDisplay !== null && $amountDisplay !== ''){
                    // convert to Arabic-Indic numerals for user-facing notification
                    $fmt = number_format((float)$amountDisplay, 2);
                    $arabic = strtr($fmt, ['0'=>'٠','1'=>'١','2'=>'٢','3'=>'٣','4'=>'٤','5'=>'٥','6'=>'٦','7'=>'٧','8'=>'٨','9'=>'٩']);
                    $finalMsg .= 'يوجد مبلغ مستحق للدفع: ' . $arabic . ' ر.س\n\n';
                }
                $finalMsg .= trim($displayNote);

                if (class_exists(\App\Notifications\PaymentDueNotification::class)) {
                    $user->notify(new \App\Notifications\PaymentDueNotification($inv, $finalMsg, $amount));
                } else {
                    $user->notify(new \Illuminate\Notifications\Notification());
                }
            } catch (\Exception $e){
                // ignore notify errors
            }
        }

        return response()->json(['ok' => true, 'message' => 'تم إرسال التذكير إلى المستثمر']);
    }

    // Register a rejection for an investment as a reminder (visible to investor dashboard)
    public function rejectInvestment($id, Request $request)
    {
        if (!Auth::check() || !$this->hasPerm('manage_investments')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $inv = Investment::find($id);
        if (!$inv) {
            return response()->json(['message' => 'Investment not found'], 404);
        }

        $reason = trim((string) $request->input('reason', ''));
        // Ensure the note contains a clear Arabic rejection keyword so investor UI can highlight it
        $note = 'تم رفض الطلب' . ($reason !== '' ? ' - السبب: ' . $reason : '');

        try {
            \App\Models\PaymentReminder::create([
                'investment_id' => $inv->id,
                'admin_id' => Auth::user() ? Auth::user()->id : null,
                'note' => $note,
            ]);
            // Also set investment status to rejected (admin-facing), investor-facing will be shown as "قيد المراجعة"
            $inv->status = 'rejected';
            $inv->save();
        } catch (\Throwable $e) {
            try { \Illuminate\Support\Facades\Log::warning('Reject reminder create failed: ' . $e->getMessage()); } catch (\Throwable $_) {}
            return response()->json(['ok' => false, 'message' => 'فشل تسجيل الرفض'], 500);
        }

        // By request, do not send email here; only persist reminder for investor visibility.
        return response()->json(['ok' => true, 'message' => 'تم تسجيل الرفض وإظهاره في لوحة المستثمر', 'status' => $inv->status]);
    }

    // قائمة التراخيص (JSON)
    public function licenses(Request $request)
    {
        if (!Auth::check() || !($this->hasPerm('view_licenses') || $this->hasPerm('view_license_requests'))) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $q = trim((string)$request->query('q', ''));
        $licQuery = License::with('user')->orderBy('created_at', 'desc');
        if ($q !== '') {
            $licQuery->where(function($w) use ($q) {
                $w->where('business_name', 'like', '%' . $q . '%')
                  ->orWhere('business_type', 'like', '%' . $q . '%')
                  ->orWhere('activity_description', 'like', '%' . $q . '%')
                  ->orWhere('id', 'like', '%' . $q . '%');
            })->orWhereHas('user', function($u) use ($q) {
                $u->where('full_name', 'like', '%' . $q . '%')
                  ->orWhere('email', 'like', '%' . $q . '%');
            });
        }
        $licenses = $licQuery->take(50)->get();

        $payload = $licenses->map(function($lic){
            // attempt to expose identity image URLs for admin preview
            $userObj = optional($lic->user);
            $investorObj = null;
            try {
                $investorObj = \App\Models\Investor::where('user_id', $lic->user_id)->first();
            } catch (\Throwable $e) { $investorObj = null; }

            // prefer license-level stored files, then user-level, then investor-level (front/back only)
            $idFrontRaw = $lic->identity_image_front ?? ($userObj && $userObj->identity_image_front ? $userObj->identity_image_front : ($investorObj->identity_image_front ?? null));
            $idBackRaw = $lic->identity_image_back ?? ($userObj && $userObj->identity_image_back ? $userObj->identity_image_back : ($investorObj->identity_image_back ?? null));

            $idFrontUrl = null;
            $idBackUrl = null;

            if (!empty($idFrontRaw)) {
                if (preg_match('/^https?:\/\//i', $idFrontRaw)) {
                    $idFrontUrl = $idFrontRaw;
                } else {
                    try {
                        $exists = Storage::disk('public')->exists($idFrontRaw);
                        $publicPath = public_path('storage/' . ltrim($idFrontRaw, '/'));
                        if ($exists && file_exists($publicPath)) {
                            $idFrontUrl = Storage::url($idFrontRaw);
                        } elseif ($exists) {
                            $idFrontUrl = route('investor.image', ['investor' => $investorObj ? $investorObj->id : ($lic->user_id ?? $lic->id), 'side' => 'front']);
                        } else {
                            $idFrontUrl = route('investor.image', ['investor' => $investorObj ? $investorObj->id : ($lic->user_id ?? $lic->id), 'side' => 'front']);
                        }
                    } catch (\Exception $e) {
                        $idFrontUrl = route('investor.image', ['investor' => $investorObj ? $investorObj->id : ($lic->user_id ?? $lic->id), 'side' => 'front']);
                    }
                }
            }

            if (!empty($idBackRaw)) {
                if (preg_match('/^https?:\/\//i', $idBackRaw)) {
                    $idBackUrl = $idBackRaw;
                } else {
                    try {
                        $existsB = Storage::disk('public')->exists($idBackRaw);
                        $publicPathB = public_path('storage/' . ltrim($idBackRaw, '/'));
                        if ($existsB && file_exists($publicPathB)) {
                            $idBackUrl = Storage::url($idBackRaw);
                        } elseif ($existsB) {
                            $idBackUrl = route('investor.image', ['investor' => $investorObj ? $investorObj->id : ($lic->user_id ?? $lic->id), 'side' => 'back']);
                        } else {
                            $idBackUrl = route('investor.image', ['investor' => $investorObj ? $investorObj->id : ($lic->user_id ?? $lic->id), 'side' => 'back']);
                        }
                    } catch (\Exception $e) {
                        $idBackUrl = route('investor.image', ['investor' => $investorObj ? $investorObj->id : ($lic->user_id ?? $lic->id), 'side' => 'back']);
                    }
                }
            }

            return [
                'id' => $lic->id,
                'business_name' => $lic->business_name,
                'business_type' => $lic->business_type,
                'activity_description' => $lic->activity_description,
                'location' => $lic->location,
                'status' => $lic->status,
                'created_at' => $lic->created_at,
                'user_name' => optional($lic->user)->full_name ?? optional($lic->user)->username ?? optional($lic->user)->email,
                'user_email' => optional($lic->user)->email,
                'user_phone' => optional(optional($lic->user)->investor)->phone,
                'id_front_url' => $idFrontUrl,
                'id_back_url' => $idBackUrl,
            ];
        });

        return response()->json($payload);
    }

    /**
     * تحديث حالة استثمار من لوحة المدير.
     *
     * ملاحظات التدفق:
     * - الانتقال إلى approved يقوم بختم approved_at مرة واحدة؛ هذا التاريخ هو مرجع جدولة الأرباح الشهرية.
     * - Observer InvestmentObserver سينشئ ربح الشهر الأول مباشرة.
     * - الأمر المجدول earnings:process-monthly سيولّد الأرباح اللاحقة شهريًا حتى الاكتمال.
     */
    public function updateInvestmentStatus($id, Request $request)
    {
        if (!Auth::check() || !$this->hasPerm('manage_investments')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $inv = Investment::find($id);
        if (!$inv) {
            return response()->json(['message' => 'Investment not found'], 404);
        }

        $newStatus = (string) $request->input('status');
        $prevStatus = (string) $inv->status;
        $inv->status = $newStatus;
        // If moving into approved, stamp approved_at (source of truth for earnings start)
        if ($prevStatus !== 'approved' && $newStatus === 'approved') {
            if (empty($inv->approved_at)) {
                $inv->approved_at = now();
            }
        }
        $inv->save();

        return response()->json(['message' => 'Status updated', 'investment' => $inv]);
    }

    // تفاصيل استثمار مع المستثمر والحزمة (JSON)
    public function investmentDetail($id)
    {
        if (!Auth::check() || !$this->hasPerm('view_investments')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $inv = \App\Models\Investment::find($id);
        if (!$inv) {
            return response()->json(['message' => 'Investment not found'], 404);
        }

        // Load related investor and user
        $investor = null;
        $user = null;
        if ($inv->investor_id) {
            $investor = \App\Models\Investor::find($inv->investor_id);
            if ($investor && $investor->user_id) {
                $user = \App\Models\User::find($investor->user_id);
            }
        }

        // Optional: package info if exists
        $package = null;
        if (!empty($inv->package_id)) {
        $package = DB::table('investment_packages')->where('id', $inv->package_id)->first();
        }

        // Latest payments for this investment with method + parsed meta (receipt)
        $paymentsRaw = DB::table('payments')
            ->leftJoin('payment_methods', 'payments.payment_method_id', '=', 'payment_methods.id')
            ->select('payments.*', 'payment_methods.name as method_name')
            ->where('payments.investment_id', $inv->id)
            ->orderByDesc('payments.created_at')
            ->limit(10)
            ->get();

        $payments = $paymentsRaw->map(function($p){
            $meta = null;
            // Attempt several ways to parse note -> meta (some notes may be double-encoded or plain strings)
            if (is_string($p->note)) {
                // try direct JSON decode
                $decoded = json_decode($p->note, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $meta = $decoded;
                } else {
                    // try un-escaping common double-encoding
                    $try = html_entity_decode($p->note);
                    $try = preg_replace('/\\+"/', '"', $try);
                    $decoded2 = json_decode($try, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded2)) {
                        $meta = $decoded2;
                    }
                }
            } elseif (is_array($p->note)) {
                $meta = $p->note;
            }

            // fallback: if meta is still null, try to extract receipt_path via regex from the raw note string
            $rawNote = is_string($p->note) ? $p->note : (is_array($p->note) ? json_encode($p->note) : null);
            $receiptPath = $meta['receipt_path'] ?? null;
            if (!$receiptPath && $rawNote) {
                if (preg_match('/receipt_path\"?\s*[:=]\s*\"([^\"]+)\"/i', $rawNote, $m)) {
                    $receiptPath = $m[1] ?? null;
                } elseif (preg_match('#(receipts/[A-Za-z0-9_\-\.]+\.(?:jpg|jpeg|png|gif))#i', $rawNote, $m2)) {
                    $receiptPath = $m2[1] ?? null;
                }
            }

            $receiptUrl = null;
            if ($receiptPath) {
                // If already full URL leave it, else prefer Storage::disk('public') existence check then Storage::url
                if (preg_match('/^https?:\/\//i', $receiptPath)) {
                    $receiptUrl = $receiptPath;
                } else {
                    try {
                        $exists = \Illuminate\Support\Facades\Storage::disk('public')->exists($receiptPath);
                        if ($exists) {
                            $receiptUrl = \Illuminate\Support\Facades\Storage::url($receiptPath);
                        } else {
                            // if file not found on disk, still expose a best-effort URL under storage path
                            $receiptUrl = asset('storage/' . ltrim($receiptPath, '/'));
                        }
                    } catch (\Exception $e) {
                        $receiptUrl = asset('storage/' . ltrim($receiptPath, '/'));
                    }
                }
            }

            return [
                'id' => $p->id,
                'amount' => (float) $p->amount,
                'currency' => $p->currency,
                'status' => $p->status,
                'transaction_reference' => $p->transaction_reference,
                'method' => $p->method_name ?? null,
                'created_at' => $p->created_at,
                'processed_at' => $p->processed_at,
                'meta' => $meta,
                'sender_name' => $meta['sender_name'] ?? null,
                'transfer_at' => $meta['transfer_at'] ?? null,
                'reference' => $meta['reference'] ?? $p->transaction_reference,
                'receipt_path' => $receiptPath,
                'receipt_url' => $receiptUrl,
                // legacy compatibility: expose receipt as top-level alias that the JS checks
                'receipt' => $receiptUrl ?? $receiptPath,
                'note' => is_string($p->note) ? $p->note : json_encode($p->note),
            ];
        });

        // Transactions for this investment
        $transactions = DB::table('investment_transactions')
            ->where('investment_id', $inv->id)
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        // Build investor payload with image URLs. Prefer images stored on the related User if available.
        $invPayload = null;
        if ($investor) {
            // load related user if any
            $userObj = null;
            if (!empty($investor->user_id)) {
                $userObj = \App\Models\User::find($investor->user_id);
            }

            // prefer user-level identity fields, fallback to investor fields (front/back only)
            $idFrontRaw = $userObj && $userObj->identity_image_front ? $userObj->identity_image_front : ($investor->identity_image_front ?? null);
            $idBackRaw = $userObj && $userObj->identity_image_back ? $userObj->identity_image_back : ($investor->identity_image_back ?? null);

            $idFrontUrl = $idFrontRaw ? (preg_match('/^https?:\/\//i', $idFrontRaw) ? $idFrontRaw : Storage::url($idFrontRaw)) : null;
            $idBackUrl = $idBackRaw ? (preg_match('/^https?:\/\//i', $idBackRaw) ? $idBackRaw : Storage::url($idBackRaw)) : null;

            $invPayload = [
                'id' => $investor->id,
                'full_name' => $investor->full_name,
                'email' => $investor->email,
                'phone' => $investor->phone,
                'national_id' => $investor->national_id,
                'bank_account' => $investor->bank_account,
                'nationality' => $investor->nationality,
                'city' => $investor->city,
                    'identity_image_front' => $userObj ? ($userObj->identity_image_front ?? $investor->identity_image_front) : $investor->identity_image_front,
                    'identity_image_back' => $userObj ? ($userObj->identity_image_back ?? $investor->identity_image_back) : $investor->identity_image_back,
                'id_front_url' => $idFrontUrl,
                'id_back_url' => $idBackUrl,
            ];
        }

        // Earnings summary & scheduling helpers
        $earningsCount = \App\Models\InvestorEarning::where('investment_id', $inv->id)->count();
        $earningsSum = (float) \App\Models\InvestorEarning::where('investment_id', $inv->id)->sum('profit_amount');
        $approvedAt = $inv->approved_at ? Carbon::parse($inv->approved_at) : null;
        $nextPayoutAt = null;
        $missingCount = 0;
        $overdue = false;
        if ($approvedAt) {
            $nextMonthNumber = $earningsCount + 1;
            // next payout per scheduling policy: approved_at + nextMonthNumber months
            try {
                $nextPayoutAt = (clone $approvedAt)->addMonths($nextMonthNumber);
            } catch (\Exception $_e) { $nextPayoutAt = null; }

            // compute how many months have elapsed since approval (integer)
            try {
                $monthsSince = Carbon::now()->diffInMonths($approvedAt);
            } catch (\Exception $_e) { $monthsSince = 0; }
            // expected number of earnings that should have been created by now (but not exceeding duration)
            $expectedByNow = min((int)$inv->duration_months, max(0, $monthsSince));
            if ($expectedByNow > $earningsCount) {
                $missingCount = $expectedByNow - $earningsCount;
                // if next payout date is in the past, mark overdue
                $overdue = $nextPayoutAt ? Carbon::now()->greaterThan($nextPayoutAt) : false;
            }
        }

        return response()->json([
            'investment' => [
                'id' => $inv->id,
                'amount' => (float) $inv->amount,
                'investment_type' => $inv->investment_type,
                'profit_rate' => $inv->profit_rate,
                'expected_return' => $inv->expected_return,
                'duration_months' => $inv->duration_months,
                'risk_level' => $inv->risk_level,
                'status' => $inv->status,
                'notes' => $inv->notes,
                'created_at' => $inv->created_at,
                'package_id' => $inv->package_id,
                // earnings diagnostics
                'earnings_count' => (int) $earningsCount,
                'earnings_sum' => (float) $earningsSum,
                'approved_at' => $inv->approved_at,
                'next_payout_at' => $nextPayoutAt ? $nextPayoutAt->toDateTimeString() : null,
                'missing_earnings_count' => (int) $missingCount,
                'overdue' => (bool) $overdue,
            ],
            // prefer users.national_id in invPayload when possible
            'investor' => ($invPayload ? array_merge((array)$invPayload, [
                'national_id' => ($user ? ($user->national_id ?? ($invPayload['national_id'] ?? null)) : ($invPayload['national_id'] ?? null))
            ]) : null),
            'user' => $user ? [
                'id' => $user->id,
                'full_name' => $user->full_name,
                'username' => $user->username,
                'email' => $user->email,
            ] : null,
            'package' => $package,
            'payments' => $payments,
            'transactions' => $transactions,
        ]);
    }

    /**
     * Diagnostics: list investments where scheduled earnings appear missing.
     * Useful to detect scheduler failures (approved_at old but earnings not created).
     */
    public function problematicInvestments(Request $request)
    {
        if (!Auth::check() || !$this->hasPerm('view_investments')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $now = Carbon::now();
        $rows = [];
        $investments = \App\Models\Investment::whereNotNull('approved_at')->where('status', 'approved')->get();
        foreach ($investments as $inv) {
            $approvedAt = $inv->approved_at ? Carbon::parse($inv->approved_at) : null;
            if (!$approvedAt) continue;
            $earningsCount = \App\Models\InvestorEarning::where('investment_id', $inv->id)->count();
            $monthsSince = $approvedAt ? $now->diffInMonths($approvedAt) : 0;
            $expectedByNow = min((int)$inv->duration_months, max(0, $monthsSince));
            if ($expectedByNow > $earningsCount) {
                $nextMonth = $earningsCount + 1;
                try { $nextPayoutAt = (clone $approvedAt)->addMonths($nextMonth); } catch (\Exception $_e) { $nextPayoutAt = null; }
                $rows[] = [
                    'id' => $inv->id,
                    'approved_at' => $inv->approved_at,
                    'duration_months' => (int)$inv->duration_months,
                    'earnings_count' => (int)$earningsCount,
                    'expected_by_now' => (int)$expectedByNow,
                    'missing_count' => (int)($expectedByNow - $earningsCount),
                    'next_payout_at' => $nextPayoutAt ? $nextPayoutAt->toDateTimeString() : null,
                ];
            }
        }

        return response()->json($rows);
    }

    /**
     * Return DB-backed market insights for admin UI.
     */
    public function marketInsightsIndex()
    {
        if (!Auth::check() || !$this->hasPerm('view_settings')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $ind = DB::table('market_indicators')->orderBy('created_at', 'asc')->get();
        $sec = DB::table('market_sectors')->orderBy('created_at', 'asc')->get();
        $news = DB::table('market_news')->orderBy('created_at', 'asc')->get();
        $tr = DB::table('market_trends')->orderBy('created_at', 'asc')->get();

        // Normalize DB field names to frontend-friendly camelCase where needed
        $news = $news->map(function($n){
            $arr = (array) $n;
            if (array_key_exists('read_time', $arr) && !array_key_exists('readTime', $arr)) {
                $arr['readTime'] = $arr['read_time'];
            }
            // expose expires_at as camelCase for frontend convenience
            if (array_key_exists('expires_at', $arr) && !array_key_exists('expiresAt', $arr)) {
                $arr['expiresAt'] = $arr['expires_at'];
            }
            return $arr;
        });

        return response()->json([
            'indicators' => $ind,
            'sectors' => $sec,
            'news' => $news,
            'trends' => $tr,
        ]);
    }

    /**
     * Bulk replace market insights with provided arrays.
     * This endpoint receives one or more arrays: indicators, sectors, news, trends.
     * It will replace the contents of the corresponding tables with the supplied items.
     */
    public function marketInsightsBulkUpdate(Request $request)
    {
        if (!Auth::check() || !$this->hasPerm('manage_settings')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $data = $request->only(['indicators', 'sectors', 'news', 'trends']);

        try {
            DB::transaction(function() use ($data) {
                if (array_key_exists('indicators', $data)) {
                    DB::table('market_indicators')->delete();
                    $rows = collect($data['indicators'] ?: [])->map(function($r){
                        return array_merge([
                            'id' => (string) Str::uuid(),
                            'created_at' => now(),
                            'updated_at' => now()
                        ], (array)$r);
                    })->toArray();
                    if (!empty($rows)) DB::table('market_indicators')->insert($rows);
                }
                if (array_key_exists('sectors', $data)) {
                    DB::table('market_sectors')->delete();
                    $rows = collect($data['sectors'] ?: [])->map(function($r){
                        return array_merge([
                            'id' => (string) Str::uuid(),
                            'created_at' => now(),
                            'updated_at' => now()
                        ], (array)$r);
                    })->toArray();
                    if (!empty($rows)) DB::table('market_sectors')->insert($rows);
                }
                if (array_key_exists('news', $data)) {
                    DB::table('market_news')->delete();
                    $rows = collect($data['news'] ?: [])->map(function($r){
                        $r = (array) $r;
                        // map frontend camelCase to DB snake_case (readTime -> read_time)
                        if (array_key_exists('readTime', $r) && !array_key_exists('read_time', $r)) {
                            $r['read_time'] = $r['readTime'];
                            unset($r['readTime']);
                        }
                        // map frontend camelCase expiresAt -> expires_at
                        if (array_key_exists('expiresAt', $r) && !array_key_exists('expires_at', $r)) {
                            $r['expires_at'] = $r['expiresAt'];
                            unset($r['expiresAt']);
                        }
                        return array_merge([
                            'id' => (string) Str::uuid(),
                            'created_at' => now(),
                            'updated_at' => now()
                        ], $r);
                    })->toArray();
                    if (!empty($rows)) DB::table('market_news')->insert($rows);
                }
                if (array_key_exists('trends', $data)) {
                    DB::table('market_trends')->delete();
                    $rows = collect($data['trends'] ?: [])->map(function($r){
                        return array_merge([
                            'id' => (string) Str::uuid(),
                            'created_at' => now(),
                            'updated_at' => now()
                        ], (array)$r);
                    })->toArray();
                    if (!empty($rows)) DB::table('market_trends')->insert($rows);
                }
            });
        } catch (\Exception $e) {
            Log::error('marketInsightsBulkUpdate error: ' . $e->getMessage());
            return response()->json(['message' => 'Update failed', 'error' => $e->getMessage()], 500);
        }

        return response()->json(['ok' => true]);
    }

    /**
     * Create a single market insights item of given type (indicator|sector|news|trend)
     */
    public function marketInsightsCreate($type, Request $request)
    {
        if (!Auth::check() || !$this->hasPerm('manage_settings')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $type = strtolower($type);
        $tables = [
            'indicator' => 'market_indicators',
            'sector' => 'market_sectors',
            'news' => 'market_news',
            'trend' => 'market_trends',
        ];
        if (!isset($tables[$type])) {
            return response()->json(['message' => 'Invalid type'], 400);
        }

        // Allowed fields per type
        $allowed = [
            'indicator' => ['name','value','change','trend'],
            'sector' => ['name','performance','volume','trend','color'],
            'news' => ['title','summary','date','category','readTime'],
            'trend' => ['trend','growth','description'],
        ];

        $data = $request->only($allowed[$type]);
        // Map camelCase frontend keys to DB columns for news
        if ($type === 'news') {
            if (array_key_exists('readTime', $data) && !array_key_exists('read_time', $data)) {
                $data['read_time'] = $data['readTime'];
                unset($data['readTime']);
            }
            // accept expiresAt (camelCase) from frontend and normalize
            if (array_key_exists('expiresAt', $data) && !array_key_exists('expires_at', $data)) {
                $data['expires_at'] = $data['expiresAt'];
                unset($data['expiresAt']);
            }
        }
        $now = now();
        $id = (string) Str::uuid();
        $row = array_merge(['id' => $id, 'created_at' => $now, 'updated_at' => $now], $data);
        // compute expires_at from reading period if provided (reading_period_value + reading_period_unit)
        if ($type === 'news') {
            $rpv = $request->input('reading_period_value');
            $rpu = $request->input('reading_period_unit');
            // If explicit expires_at was provided in $data it is already set; otherwise compute
            if (($rpv !== null && $rpu) || (array_key_exists('reading_period_value', $data) && array_key_exists('reading_period_unit', $data))) {
                $n = (int) $rpv;
                try {
                    $expires = Carbon::now();
                    switch (strtolower($rpu)) {
                        case 'minute': case 'minutes': $expires = $expires->addMinutes($n); break;
                        case 'hour': case 'hours': $expires = $expires->addHours($n); break;
                        case 'day': case 'days': $expires = $expires->addDays($n); break;
                        case 'week': case 'weeks': $expires = $expires->addWeeks($n); break;
                        case 'month': case 'months': $expires = $expires->addMonths($n); break;
                        default: $expires = null; break;
                    }
                    if ($expires) { $row['expires_at'] = $expires; }
                } catch (\Exception $e) { /* ignore invalid duration */ }
            }
            // if request provided explicit expires_at field (ISO string), normalize if not already
            if ($request->filled('expires_at') && empty($row['expires_at'])) {
                try { $row['expires_at'] = Carbon::parse($request->input('expires_at')); } catch (\Exception $e) { /* ignore */ }
            }
        }

        try {
            DB::table($tables[$type])->insert($row);
            $inserted = (array) DB::table($tables[$type])->where('id', $id)->first();
            // normalize for frontend
            if ($type === 'news') {
                if (array_key_exists('read_time', $inserted) && !array_key_exists('readTime', $inserted)) {
                    $inserted['readTime'] = $inserted['read_time'];
                }
                if (array_key_exists('expires_at', $inserted) && !array_key_exists('expiresAt', $inserted)) {
                    $inserted['expiresAt'] = $inserted['expires_at'];
                }
            }
            return response()->json($inserted, 201);
        } catch (\Exception $e) {
            Log::error('marketInsightsCreate error: ' . $e->getMessage());
            return response()->json(['message' => 'Insert failed', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update a single market insights item by id
     */
    public function marketInsightsUpdate($type, $id, Request $request)
    {
        if (!Auth::check() || !$this->hasPerm('manage_settings')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $type = strtolower($type);
        $tables = [
            'indicator' => 'market_indicators',
            'sector' => 'market_sectors',
            'news' => 'market_news',
            'trend' => 'market_trends',
        ];
        if (!isset($tables[$type])) {
            return response()->json(['message' => 'Invalid type'], 400);
        }

        $allowed = [
            'indicator' => ['name','value','change','trend'],
            'sector' => ['name','performance','volume','trend','color'],
            'news' => ['title','summary','date','category','readTime'],
            'trend' => ['trend','growth','description'],
        ];

        $data = $request->only($allowed[$type]);
        // Map frontend camelCase to DB for updates as well
        if ($type === 'news') {
            if (array_key_exists('readTime', $data) && !array_key_exists('read_time', $data)) {
                $data['read_time'] = $data['readTime'];
                unset($data['readTime']);
            }
            // accept expiresAt (camelCase) and normalize
            if (array_key_exists('expiresAt', $data) && !array_key_exists('expires_at', $data)) {
                $data['expires_at'] = $data['expiresAt'];
                unset($data['expiresAt']);
            }
            // support reading_period_value + reading_period_unit to compute new expires_at
            $rpv = $request->input('reading_period_value', null);
            $rpu = $request->input('reading_period_unit', null);
            if ($rpv !== null && $rpu) {
                $n = (int) $rpv;
                try {
                    $expires = Carbon::now();
                    switch (strtolower($rpu)) {
                        case 'minute': case 'minutes': $expires = $expires->addMinutes($n); break;
                        case 'hour': case 'hours': $expires = $expires->addHours($n); break;
                        case 'day': case 'days': $expires = $expires->addDays($n); break;
                        case 'week': case 'weeks': $expires = $expires->addWeeks($n); break;
                        case 'month': case 'months': $expires = $expires->addMonths($n); break;
                        default: $expires = null; break;
                    }
                    if ($expires) { $data['expires_at'] = $expires; }
                } catch (\Exception $e) { /* ignore */ }
            }
        }
        $data['updated_at'] = now();

        try {
            $updated = DB::table($tables[$type])->where('id', $id)->update($data);
            if (!$updated) {
                return response()->json(['message' => 'Not found or not updated'], 404);
            }
            $row = (array) DB::table($tables[$type])->where('id', $id)->first();
            if ($type === 'news') {
                if (array_key_exists('read_time', $row) && !array_key_exists('readTime', $row)) {
                    $row['readTime'] = $row['read_time'];
                }
                if (array_key_exists('expires_at', $row) && !array_key_exists('expiresAt', $row)) {
                    $row['expiresAt'] = $row['expires_at'];
                }
            }
            return response()->json($row);
        } catch (\Exception $e) {
            Log::error('marketInsightsUpdate error: ' . $e->getMessage());
            return response()->json(['message' => 'Update failed', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete a single market insights item by id
     */
    public function marketInsightsDelete($type, $id)
    {
        if (!Auth::check() || !$this->hasPerm('manage_settings')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $type = strtolower($type);
        $tables = [
            'indicator' => 'market_indicators',
            'sector' => 'market_sectors',
            'news' => 'market_news',
            'trend' => 'market_trends',
        ];
        if (!isset($tables[$type])) {
            return response()->json(['message' => 'Invalid type'], 400);
        }

        try {
            $deleted = DB::table($tables[$type])->where('id', $id)->delete();
            if (!$deleted) {
                return response()->json(['message' => 'Not found'], 404);
            }
            return response()->json(['ok' => true]);
        } catch (\Exception $e) {
            Log::error('marketInsightsDelete error: ' . $e->getMessage());
            return response()->json(['message' => 'Delete failed', 'error' => $e->getMessage()], 500);
        }
    }

    // ================= Trust & Compliance admin CRUD =================
    // tc_regulations
    public function tcRegulationsIndex()
    {
        if (!Auth::check() || !$this->hasPerm('view_settings')) { return response()->json(['message'=>'Unauthorized'],401); }
        $rows = DB::table('tc_regulations')->orderByDesc('created_at')->get();
        return response()->json($rows);
    }

    public function tcRegulationsCreate(Request $request)
    {
        if (!Auth::check() || !$this->hasPerm('manage_settings')) { return response()->json(['message'=>'Unauthorized'],401); }
        // Normalize alternative client keys to controller-expected keys
        $in = $request->all();
        if (array_key_exists('name', $in) && !array_key_exists('title', $in)) { $request->merge(['title' => $in['name']]); }
        if (array_key_exists('nameEn', $in) && !array_key_exists('name_en', $in)) { $request->merge(['name_en' => $in['nameEn']]); }
        if (array_key_exists('validUntil', $in) && !array_key_exists('valid_until', $in)) { $request->merge(['valid_until' => $in['validUntil']]); }
        if (array_key_exists('content', $in) && !array_key_exists('content', $in)) { $request->merge(['content' => $in['content']]); }

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'name_en' => 'sometimes|nullable|string|max:255',
            'license' => 'sometimes|nullable|string|max:255',
            'file_path' => 'sometimes|nullable|string',
            'status' => 'sometimes|nullable|string|max:255',
            'valid_until' => 'sometimes|nullable|date',
            'icon' => 'sometimes|nullable|string|max:10',
            'content' => 'sometimes|nullable|string',
        ]);
        $now = now();
        $insert = [
            'id' => (string) Str::uuid(),
            'title' => $data['title'],
            'name_en' => $data['name_en'] ?? null,
            'license' => $data['license'] ?? null,
            'file_path' => $data['file_path'] ?? null,
            'status' => $data['status'] ?? null,
            'valid_until' => $data['valid_until'] ?? null,
            'icon' => $data['icon'] ?? null,
            'content' => $data['content'] ?? null,
            'created_at' => $now,
            'updated_at' => $now,
        ];
        DB::table('tc_regulations')->insert($insert);
        $row = DB::table('tc_regulations')->where('id', $insert['id'])->first();
        return response()->json($row, 201);
    }

    public function tcRegulationsUpdate($id, Request $request)
    {
        if (!Auth::check() || !$this->hasPerm('manage_settings')) { return response()->json(['message'=>'Unauthorized'],401); }
        $exists = DB::table('tc_regulations')->where('id', $id)->exists();
        if (!$exists) return response()->json(['message'=>'Not found'],404);
        // Accept alternate keys from client
        $in = $request->all();
        if (array_key_exists('name', $in) && !array_key_exists('title', $in)) { $request->merge(['title' => $in['name']]); }
        if (array_key_exists('nameEn', $in) && !array_key_exists('name_en', $in)) { $request->merge(['name_en' => $in['nameEn']]); }
        if (array_key_exists('validUntil', $in) && !array_key_exists('valid_until', $in)) { $request->merge(['valid_until' => $in['validUntil']]); }

        $data = $request->validate([
            'title' => 'sometimes|string|max:255',
            'name_en' => 'sometimes|nullable|string|max:255',
            'license' => 'sometimes|nullable|string|max:255',
            'file_path' => 'sometimes|nullable|string',
            'status' => 'sometimes|nullable|string|max:255',
            'valid_until' => 'sometimes|nullable|date',
            'icon' => 'sometimes|nullable|string|max:10',
            'content' => 'sometimes|nullable|string',
        ]);
        $update = [];
        foreach (['title','name_en','license','status','valid_until','icon','content','file_path'] as $k) {
            if (array_key_exists($k, $data)) $update[$k] = $data[$k];
        }
        $update['updated_at'] = now();
        DB::table('tc_regulations')->where('id', $id)->update($update);
        $row = DB::table('tc_regulations')->where('id', $id)->first();
        return response()->json($row);
    }

    public function tcRegulationsDelete($id)
    {
        if (!Auth::check() || !$this->hasPerm('manage_settings')) { return response()->json(['message'=>'Unauthorized'],401); }
        $exists = DB::table('tc_regulations')->where('id', $id)->exists();
        if (!$exists) return response()->json(['message'=>'Not found'],404);
        DB::table('tc_regulations')->where('id', $id)->delete();
        return response()->json(['message'=>'deleted']);
    }

    // tc_security_features
    public function tcSecurityIndex()
    {
        if (!Auth::check() || !$this->hasPerm('view_settings')) { return response()->json(['message'=>'Unauthorized'],401); }
        return response()->json(DB::table('tc_security_features')->orderByDesc('created_at')->get());
    }

    public function tcSecurityCreate(Request $request)
    {
        if (!Auth::check() || !$this->hasPerm('manage_settings')) { return response()->json(['message'=>'Unauthorized'],401); }
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'sometimes|nullable|string',
            'level' => 'sometimes|nullable|string|max:50',
            'icon' => 'sometimes|nullable|string|max:10',
        ]);
        $id = (string) Str::uuid();
        DB::table('tc_security_features')->insert(array_merge($data, ['id'=>$id,'created_at'=>now(),'updated_at'=>now()]));
        $row = DB::table('tc_security_features')->where('id', $id)->first();
        return response()->json($row,201);
    }

    public function tcSecurityUpdate($id, Request $request)
    {
        if (!Auth::check() || !$this->hasPerm('manage_settings')) { return response()->json(['message'=>'Unauthorized'],401); }
        $exists = DB::table('tc_security_features')->where('id',$id)->exists();
        if (!$exists) return response()->json(['message'=>'Not found'],404);
        $data = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|nullable|string',
            'level' => 'sometimes|nullable|string|max:50',
            'icon' => 'sometimes|nullable|string|max:10',
        ]);
        $data['updated_at'] = now();
        DB::table('tc_security_features')->where('id',$id)->update($data);
        $row = DB::table('tc_security_features')->where('id', $id)->first();
        return response()->json($row);
    }

    public function tcSecurityDelete($id)
    {
        if (!Auth::check() || !$this->hasPerm('manage_settings')) { return response()->json(['message'=>'Unauthorized'],401); }
        DB::table('tc_security_features')->where('id',$id)->delete();
        return response()->json(['message'=>'deleted']);
    }

    // tc_risk_levels
    public function tcRiskLevelsIndex()
    {
        if (!Auth::check() || !$this->hasPerm('view_settings')) { return response()->json(['message'=>'Unauthorized'],401); }
        return response()->json(DB::table('tc_risk_levels')->orderByDesc('created_at')->get());
    }

    public function tcRiskLevelsCreate(Request $request)
    {
        if (!Auth::check() || !$this->hasPerm('manage_settings')) { return response()->json(['message'=>'Unauthorized'],401); }
        $data = $request->validate([
            'level' => 'sometimes|nullable|string|max:50',
            'label' => 'required|string|max:255',
            'percentage' => 'sometimes|nullable|numeric',
            'color' => 'sometimes|nullable|string|max:20',
            'percentage_text' => 'sometimes|nullable|string|max:50',
        ]);
        $id = (string) Str::uuid();
        DB::table('tc_risk_levels')->insert(array_merge($data, ['id'=>$id,'created_at'=>now(),'updated_at'=>now()]));
        $row = DB::table('tc_risk_levels')->where('id', $id)->first();
        return response()->json($row,201);
    }

    public function tcRiskLevelsUpdate($id, Request $request)
    {
        if (!Auth::check() || !$this->hasPerm('manage_settings')) { return response()->json(['message'=>'Unauthorized'],401); }
        $exists = DB::table('tc_risk_levels')->where('id',$id)->exists();
        if (!$exists) return response()->json(['message'=>'Not found'],404);
        $data = $request->validate([
            'level' => 'sometimes|nullable|string|max:50',
            'label' => 'sometimes|string|max:255',
            'percentage' => 'sometimes|nullable|numeric',
            'color' => 'sometimes|nullable|string|max:20',
            'percentage_text' => 'sometimes|nullable|string|max:50',
        ]);
        $data['updated_at'] = now();
        DB::table('tc_risk_levels')->where('id',$id)->update($data);
        $row = DB::table('tc_risk_levels')->where('id', $id)->first();
        return response()->json($row);
    }

    public function tcRiskLevelsDelete($id)
    {
        if (!Auth::check() || !$this->hasPerm('manage_settings')) { return response()->json(['message'=>'Unauthorized'],401); }
        DB::table('tc_risk_levels')->where('id',$id)->delete();
        return response()->json(['message'=>'deleted']);
    }

    // tc_certificates
    public function tcCertificatesIndex()
    {
        if (!Auth::check() || !$this->hasPerm('view_settings')) { return response()->json(['message'=>'Unauthorized'],401); }
        return response()->json(DB::table('tc_certificates')->orderByDesc('created_at')->get());
    }

    public function tcCertificatesCreate(Request $request)
    {
        if (!Auth::check() || !$this->hasPerm('manage_settings')) { return response()->json(['message'=>'Unauthorized'],401); }
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'sometimes|nullable|string',
            'issuer' => 'sometimes|nullable|string|max:255',
            'issued_at' => 'sometimes|nullable|date',
            'expires_at' => 'sometimes|nullable|date',
            'status' => 'sometimes|nullable|string|max:50',
            'file_path' => 'sometimes|nullable|string',
        ]);
        $id = (string) Str::uuid();
        $insert = array_merge($data, ['id'=>$id,'created_at'=>now(),'updated_at'=>now()]);
        // ensure only allowed keys are inserted
        $allowed = ['id','name','description','issuer','issued_at','expires_at','status','file_path','created_at','updated_at'];
        $safeInsert = array_intersect_key($insert, array_flip($allowed));
        DB::table('tc_certificates')->insert($safeInsert);
        $row = DB::table('tc_certificates')->where('id', $id)->first();
        return response()->json($row,201);
    }

    public function tcCertificatesUpdate($id, Request $request)
    {
        if (!Auth::check() || !$this->hasPerm('manage_settings')) { return response()->json(['message'=>'Unauthorized'],401); }
        $exists = DB::table('tc_certificates')->where('id',$id)->exists();
        if (!$exists) return response()->json(['message'=>'Not found'],404);
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|nullable|string',
            'issuer' => 'sometimes|nullable|string|max:255',
            'issued_at' => 'sometimes|nullable|date',
            'expires_at' => 'sometimes|nullable|date',
            'status' => 'sometimes|nullable|string|max:50',
            'file_path' => 'sometimes|nullable|string',
        ]);
        $data['updated_at'] = now();
        // only update allowed columns
        $allowed = ['name','description','issuer','issued_at','expires_at','status','file_path','updated_at'];
        $safe = array_intersect_key($data, array_flip($allowed));
        DB::table('tc_certificates')->where('id',$id)->update($safe);
        $row = DB::table('tc_certificates')->where('id', $id)->first();
        return response()->json($row);
    }

    public function tcCertificatesDelete($id)
    {
        if (!Auth::check() || !$this->hasPerm('manage_settings')) { return response()->json(['message'=>'Unauthorized'],401); }
        DB::table('tc_certificates')->where('id',$id)->delete();
        return response()->json(['message'=>'deleted']);
    }

    // tc_audit_reports
    public function tcAuditsIndex()
    {
        if (!Auth::check() || !$this->hasPerm('view_settings')) { return response()->json(['message'=>'Unauthorized'],401); }
        return response()->json(DB::table('tc_audit_reports')->orderByDesc('created_at')->get());
    }

    public function tcAuditsCreate(Request $request)
    {
        if (!Auth::check() || !$this->hasPerm('manage_settings')) { return response()->json(['message'=>'Unauthorized'],401); }
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'summary' => 'sometimes|nullable|string',
            'date' => 'sometimes|nullable|date',
            'audit_date' => 'sometimes|nullable|date',
            'type' => 'sometimes|nullable|string|max:255',
            'status' => 'sometimes|nullable|string|max:50',
            'file_size' => 'sometimes|nullable|numeric',
            'file_path' => 'sometimes|nullable|string',
            'file_name' => 'sometimes|nullable|string',
            'mime_type' => 'sometimes|nullable|string',
        ]);
        $id = (string) Str::uuid();
        $insert = array_merge($data, ['id'=>$id,'created_at'=>now(),'updated_at'=>now()]);

        // populate missing file metadata from storage when possible
        if (!empty($insert['file_path']) && (empty($insert['file_name']) || empty($insert['mime_type']) || empty($insert['file_size']))) {
            try {
                $p = $insert['file_path'];
                    if (!preg_match('/^https?:\/\//i', $p) && Storage::disk('public')->exists($p)) {
                    if (empty($insert['file_name'])) $insert['file_name'] = basename($p);
                    if (empty($insert['mime_type'])) {
                        try {
                            $full = storage_path('app/public/' . ltrim($p, '/'));
                            if (file_exists($full) && function_exists('finfo_open')) {
                                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                                if ($finfo) { $m = finfo_file($finfo, $full); finfo_close($finfo); $insert['mime_type'] = $m; }
                            }
                        } catch (\Throwable $_) { }
                    }
                    if (empty($insert['file_size'])) {
                        try { $insert['file_size'] = (int) Storage::disk('public')->size($p); } catch (\Throwable $_) { }
                    }
                }
            } catch (\Throwable $_e) { }
        }

        DB::table('tc_audit_reports')->insert($insert);
        $row = DB::table('tc_audit_reports')->where('id', $id)->first();
        return response()->json($row,201);
    }

    public function tcAuditsUpdate($id, Request $request)
    {
        if (!Auth::check() || !$this->hasPerm('manage_settings')) { return response()->json(['message'=>'Unauthorized'],401); }
        $exists = DB::table('tc_audit_reports')->where('id',$id)->exists();
        if (!$exists) return response()->json(['message'=>'Not found'],404);
        $data = $request->validate([
            'title' => 'sometimes|string|max:255',
            'summary' => 'sometimes|nullable|string',
            'date' => 'sometimes|nullable|date',
            'audit_date' => 'sometimes|nullable|date',
            'type' => 'sometimes|nullable|string|max:255',
            'status' => 'sometimes|nullable|string|max:50',
            'file_size' => 'sometimes|nullable|numeric',
            'file_path' => 'sometimes|nullable|string',
            'file_name' => 'sometimes|nullable|string',
            'mime_type' => 'sometimes|nullable|string',
        ]);
        $data['updated_at'] = now();

        if (!empty($data['file_path']) && (empty($data['file_name']) || empty($data['mime_type']) || empty($data['file_size']))) {
            try {
                $p = $data['file_path'];
                if (!preg_match('/^https?:\/\//i', $p) && Storage::disk('public')->exists($p)) {
                    if (empty($data['file_name'])) $data['file_name'] = basename($p);
                    if (empty($data['mime_type'])) {
                        try {
                            $full = storage_path('app/public/' . ltrim($p, '/'));
                            if (file_exists($full) && function_exists('finfo_open')) {
                                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                                if ($finfo) { $m = finfo_file($finfo, $full); finfo_close($finfo); $data['mime_type'] = $m; }
                            }
                        } catch (\Throwable $_) { }
                    }
                    if (empty($data['file_size'])) {
                        try { $data['file_size'] = (int) Storage::disk('public')->size($p); } catch (\Throwable $_) { }
                    }
                }
            } catch (\Throwable $_e) { }
        }

        DB::table('tc_audit_reports')->where('id',$id)->update($data);
        $row = DB::table('tc_audit_reports')->where('id', $id)->first();
        return response()->json($row);
    }

    public function tcAuditsDelete($id)
    {
        if (!Auth::check() || !$this->hasPerm('manage_settings')) { return response()->json(['message'=>'Unauthorized'],401); }
        DB::table('tc_audit_reports')->where('id',$id)->delete();
        return response()->json(['message'=>'deleted']);
    }

    // tc_metrics (indicator cards) removed

    // tc_metrics (indicator cards) - admin CRUD
    public function tcMetricsCreate(Request $request)
    {
        if (!Auth::check() || !$this->hasPerm('manage_settings')) { return response()->json(['message'=>'Unauthorized'],401); }
        try {
            $data = $request->validate([
            'key' => 'sometimes|nullable|string|max:255',
            'label' => 'required|string|max:255',
            'value' => 'sometimes|nullable|string|max:255',
            'icon' => 'sometimes|nullable|string|max:255',
            'meta' => 'sometimes|nullable',
            ]);
            $id = (string) Str::uuid();
            $row = [
                'id' => $id,
                'key' => $data['key'] ?? null,
                'label' => $data['label'],
                'value' => $data['value'] ?? null,
                'icon' => $data['icon'] ?? null,
                'meta' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            if (array_key_exists('meta', $data) && $data['meta'] !== null) {
                // Always store JSON in the meta column (MySQL JSON requires valid JSON). If meta is
                // provided as a string encode it so the JSON column receives a valid JSON string.
                // Preserve non-ASCII characters (Arabic) without escaping unicode sequences
                $row['meta'] = json_encode($data['meta'], JSON_UNESCAPED_UNICODE);
            }
            DB::table('tc_metrics')->insert($row);
            $created = DB::table('tc_metrics')->where('id', $id)->first();
            return response()->json($created,201);
        } catch (\Exception $e) {
            // Return error details to help debugging (will be shown in Network tab)
            Log::error('tcMetricsCreate error: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json(['message' => 'Server error creating metric', 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString(), 'input' => $request->all()], 500);
        }
    }

    public function tcMetricsUpdate($id, Request $request)
    {
        if (!Auth::check() || !$this->hasPerm('manage_settings')) { return response()->json(['message'=>'Unauthorized'],401); }
        $exists = DB::table('tc_metrics')->where('id', $id)->exists();
        if (!$exists) return response()->json(['message'=>'Not found'],404);
        $data = $request->validate([
            'key' => 'sometimes|nullable|string|max:255',
            'label' => 'sometimes|string|max:255',
            'value' => 'sometimes|nullable|string|max:255',
            'icon' => 'sometimes|nullable|string|max:255',
            'meta' => 'sometimes|nullable',
        ]);
        $update = [];
        foreach (['key','label','value','icon'] as $k) { if (array_key_exists($k, $data)) $update[$k] = $data[$k]; }
        if (array_key_exists('meta', $data)) {
            // Ensure we always write valid JSON or NULL into the json column.
            $update['meta'] = $data['meta'] === null ? null : json_encode($data['meta'], JSON_UNESCAPED_UNICODE);
        }
        $update['updated_at'] = now();
        DB::table('tc_metrics')->where('id', $id)->update($update);
        $row = DB::table('tc_metrics')->where('id', $id)->first();
        return response()->json($row);
    }

    public function tcMetricsDelete($id)
    {
        if (!Auth::check() || !$this->hasPerm('manage_settings')) { return response()->json(['message'=>'Unauthorized'],401); }
        DB::table('tc_metrics')->where('id', $id)->delete();
        return response()->json(['message'=>'deleted']);
    }

    // تحديث حالة ترخيص
    public function updateLicenseStatus($id, Request $request)
    {
        if (!Auth::check() || !($this->hasPerm('manage_licenses') || $this->hasPerm('manage_license_requests'))) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $lic = License::find($id);
        if (!$lic) {
            return response()->json(['message' => 'License not found'], 404);
        }

        $prev = $lic->status;
        $newStatus = strtolower((string)$request->input('status'));
        $lic->status = $newStatus;
        $lic->save();

        // Notify owner on status change (e.g., approved) if possible
        try {
            $user = null;
            if ($lic->user_id) { $user = \App\Models\User::find($lic->user_id); }
            if ($user && class_exists(\App\Notifications\LicenseStatusChangedNotification::class)) {
                $user->notify(new \App\Notifications\LicenseStatusChangedNotification($lic, $prev, $newStatus));
            }
        } catch (\Throwable $e) { /* ignore notification errors */ }

        return response()->json([
            'message' => 'Status updated',
            'status' => $lic->status,
            'license' => $lic,
        ]);
    }

    // تفاصيل ترخيص مع المالك والوثائق والمدفوعات (JSON)
    public function licenseDetail($id)
    {
        if (!Auth::check() || !($this->hasPerm('view_licenses') || $this->hasPerm('view_license_requests'))) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $lic = License::find($id);
        if (!$lic) {
            return response()->json(['message' => 'License not found'], 404);
        }

        // owner/user
        $owner = null;
        if ($lic->user_id) {
            $owner = \App\Models\User::find($lic->user_id);
        }

        // build owner identity image URLs (prefer explicit front/back fields, fallback to identity_image)
        $ownerPayload = null;
        if ($owner) {
            try {
                $ownerIdFront = $owner->identity_image_front ?: $owner->identity_image ?: null;
                $ownerIdBack = $owner->identity_image_back ?: $owner->identity_image ?: null;
                $ownerFrontUrl = $ownerIdFront ? (preg_match('/^https?:\/\//i', $ownerIdFront) ? $ownerIdFront : Storage::url($ownerIdFront)) : null;
                $ownerBackUrl = $ownerIdBack ? (preg_match('/^https?:\/\//i', $ownerIdBack) ? $ownerIdBack : Storage::url($ownerIdBack)) : null;
            } catch (\Exception $e) {
                Log::warning('AdminController::licenseDetail - owner image resolution failed: ' . $e->getMessage());
                $ownerFrontUrl = null; $ownerBackUrl = null;
            }

            $ownerPayload = [
                'id' => $owner->id,
                'full_name' => $owner->full_name,
                'email' => $owner->email,
                'phone' => $owner->phone,
                'national_id' => $owner->national_id ?? null,
                'identity_image' => $owner->identity_image ?? null,
                'identity_image_front' => $owner->identity_image_front ?? null,
                'identity_image_back' => $owner->identity_image_back ?? null,
                'id_front_url' => $ownerFrontUrl,
                'id_back_url' => $ownerBackUrl,
            ];
        }

        // documents (latest related documents) - only query if `documents.license_id` exists on this schema
        try {
            if (Schema::hasColumn('documents', 'license_id')) {
                $docs = Document::where('license_id', $lic->id)->orderByDesc('created_at')->take(50)->get();
            } else {
                // older schema/version does not have license_id on documents - return empty collection
                $docs = collect();
            }
        } catch (\Exception $e) {
            // Log and fallback to empty collection to avoid 500 when schema differs
            Log::warning('AdminController::licenseDetail - documents query failed: ' . $e->getMessage());
            $docs = collect();
        }

        // payments for this license. Prefer payments.license_id when available, otherwise try fallbacks
        try {
            if (Schema::hasColumn('payments', 'license_id')) {
                $paymentsRaw = DB::table('payments')
                    ->leftJoin('payment_methods', 'payments.payment_method_id', '=', 'payment_methods.id')
                    ->select('payments.*', 'payment_methods.name as method_name')
                    ->where('payments.license_id', $lic->id)
                    ->orderByDesc('payments.created_at')
                    ->limit(20)
                    ->get();
            } else {
                // payments table doesn't track license_id on this schema/version; attempt fallback lookups
                // 1) payments where investment_id is NULL and note JSON contains license_id
                // 2) payments where note contains the license id string (best-effort)
                $paymentsRaw = collect();
                try {
                    $q = DB::table('payments')->leftJoin('payment_methods', 'payments.payment_method_id', '=', 'payment_methods.id')
                        ->select('payments.*', 'payment_methods.name as method_name')
                        ->whereNull('payments.investment_id')
                        ->orderByDesc('payments.created_at')
                        ->limit(20);

                    // Try JSON_EXTRACT approach (MySQL/Postgres json functions may vary). Use raw expression guarded by DB driver.
                    $licenseId = $lic->id;
                    // add where clause that matches JSON license_id or plain text occurrence
                    $q->where(function($sub) use ($licenseId) {
                        // JSON_EXTRACT(...,'$.license_id') = 'id'
                        try {
                            $sub->whereRaw("JSON_EXTRACT(payments.note, '$$.license_id') = ?", [$licenseId]);
                        } catch (\Throwable $ex) {
                            // ignore — driver might not support JSON_EXTRACT
                        }
                        // fallback to simple LIKE match
                        $sub->orWhere('payments.note', 'like', '%"license_id":"' . $licenseId . '"%');
                        $sub->orWhere('payments.note', 'like', '%"license_id":\"' . $licenseId . '\"%');
                        $sub->orWhere('payments.note', 'like', '%"license_id":' . $licenseId . '%');
                    });

                    $paymentsRaw = $q->get();
                } catch (\Throwable $ex) {
                    // fallback to empty collection if anything fails
                    Log::warning('AdminController::licenseDetail - payments fallback query failed: ' . $ex->getMessage());
                    $paymentsRaw = collect();
                }
            }
        } catch (\Exception $e) {
            Log::warning('AdminController::licenseDetail - payments query failed: ' . $e->getMessage());
            $paymentsRaw = collect();
        }

        $payments = $paymentsRaw->map(function($p){
            $receiptPath = null;
            $meta = null;
            if (is_string($p->note)) {
                $decoded = json_decode($p->note, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) { $meta = $decoded; }
            } elseif (is_array($p->note)) { $meta = $p->note; }
            $receiptPath = $meta['receipt_path'] ?? ($p->receipt ?? null);
            $receiptUrl = null;
            if ($receiptPath) {
                if (preg_match('/^https?:\/\//i', $receiptPath)) { $receiptUrl = $receiptPath; }
                else {
                    try { $exists = Storage::disk('public')->exists($receiptPath); if ($exists) $receiptUrl = Storage::url($receiptPath); else $receiptUrl = asset('storage/' . ltrim($receiptPath, '/')); } catch (\Exception $e) { $receiptUrl = asset('storage/' . ltrim($receiptPath, '/')); }
                }
            }
            return [
                'id' => $p->id,
                'amount' => (float) $p->amount,
                'currency' => $p->currency,
                'status' => $p->status,
                'transaction_reference' => $p->transaction_reference,
                'method' => $p->method_name ?? null,
                'created_at' => $p->created_at,
                'processed_at' => $p->processed_at,
                'meta' => $meta,
                'reference' => $meta['reference'] ?? $p->transaction_reference,
                'receipt_path' => $receiptPath,
                'receipt_url' => $receiptUrl,
                'receipt' => $receiptUrl ?? $receiptPath,
                'note' => is_string($p->note) ? $p->note : json_encode($p->note),
            ];
        });

        // transactions related to license (if any stored in a generic transactions table)
        try {
            if (Schema::hasColumn('payments', 'license_id')) {
                $transactions = DB::table('payments')
                    ->where('payments.license_id', $lic->id)
                    ->orderByDesc('payments.created_at')
                    ->limit(20)
                    ->get();
            } else {
                $transactions = collect();
            }
        } catch (\Exception $e) {
            Log::warning('AdminController::licenseDetail - transactions query failed: ' . $e->getMessage());
            $transactions = collect();
        }

        $docsPayload = $docs->map(function($d){ return ['id'=>$d->id,'name'=>$d->filename ?? $d->name,'type'=>$d->type,'path'=>$d->path,'url' => $d->path ? Storage::url($d->path) : null,'created_at'=>$d->created_at]; });

        return response()->json([
            'license' => $lic,
            'owner' => $ownerPayload,
            'documents' => $docsPayload,
            'payments' => $payments,
            'transactions' => $transactions,
        ]);
    }

    // Notify license owner / create payment reminder for license (POST)
    public function notifyLicensePayment($id, Request $request)
    {
        if (!Auth::check() || !($this->hasPerm('manage_licenses') || $this->hasPerm('manage_license_requests'))) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $lic = License::find($id);
        if (!$lic) { return response()->json(['message' => 'License not found'], 404); }

        $note = $request->input('note', '');
        $highlight = $request->input('highlight', 0);
        $amount = $request->input('amount', null);
        // Do NOT inject internal AMOUNT marker into the stored note. Keep note clean.
        if ($highlight && $highlight != 0) {
            // do not inject a raw newline; use a space so the note remains a single clean line
            $note = '[HIGHLIGHT] ' . $note;
        }

        try {
            // Reuse PaymentReminder table but store license_id in note or create record with investment_id=null
            \App\Models\PaymentReminder::create([
                'investment_id' => $lic->id, // store license id for backward-compat; JS treats it generically
                'admin_id' => Auth::user() ? Auth::user()->id : null,
                'note' => $note,
                'amount' => $amount !== null && $amount !== '' ? number_format((float)$amount, 2, '.', '') : null,
            ]);
        } catch (\Throwable $e) {
            try {
                \App\Models\PaymentReminder::create([
                    'investment_id' => $lic->id,
                    'admin_id' => Auth::user() ? Auth::user()->id : null,
                    'note' => $note,
                ]);
            } catch (\Throwable $e2) {
                \Illuminate\Support\Facades\Log::warning('PaymentReminder create failed (license): ' . $e2->getMessage());
            }
        }

        // attempt to notify owner (if user exists)
        $user = null;
        if ($lic->user_id) { $user = \App\Models\User::find($lic->user_id); }
        if ($user) {
            try{
                if (class_exists(\App\Notifications\PaymentDueNotification::class)) {
                    $user->notify(new \App\Notifications\PaymentDueNotification($lic, $note, $amount));
                }
            } catch (\Exception $e) { /* ignore */ }
        }

        return response()->json(['ok'=>true,'message'=>'تم إرسال التذكير لمالك الترخيص']);
    }

    // Notify license owner about rejection (POST)
    public function notifyLicenseRejection($id, Request $request)
    {
        if (!Auth::check() || !($this->hasPerm('manage_licenses') || $this->hasPerm('manage_license_requests'))) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $lic = License::find($id);
        if (!$lic) { return response()->json(['message' => 'License not found'], 404); }

        $note = $request->input('note', '');
        $highlight = $request->input('highlight', 0);

        // For rejection, we don't consider amount, but keep highlight if provided
        if ($highlight && $highlight != 0) {
            // do not inject a raw newline; use a space so the note remains a single clean line
            $note = '[HIGHLIGHT] ' . $note;
        }

        try {
            // store as a PaymentReminder-like record for admin audit (store license id in investment_id for backwards-compat)
            \App\Models\PaymentReminder::create([
                'investment_id' => $lic->id,
                'admin_id' => Auth::user() ? Auth::user()->id : null,
                'note' => $note,
            ]);
        } catch (\Exception $e) { /* ignore */ }

        // Persist status = rejected and optionally notify owner about status change
        $prev = $lic->status;
        $lic->status = 'rejected';
        try { $lic->save(); } catch (\Throwable $_) { /* ignore DB error */ }

        // attempt to notify owner (if user exists)
        $user = null;
        if ($lic->user_id) { $user = \App\Models\User::find($lic->user_id); }
        if ($user) {
            try{
                // Send the status-changed email (mirrors investment version) and keep the simple rejection note for audit
                if (class_exists(\App\Notifications\LicenseStatusChangedNotification::class)) {
                    $user->notify(new \App\Notifications\LicenseStatusChangedNotification($lic, $prev, 'rejected'));
                } elseif (class_exists(\App\Notifications\PaymentDueNotification::class)) {
                    // fallback to prior rejection email path
                    $user->notify(new \App\Notifications\PaymentDueNotification($lic, $note));
                }
            } catch (\Exception $e) { /* ignore */ }
        }

        return response()->json([
            'ok'=>true,
            'message'=>'تم رفض الترخيص وتم إشعار المالك وتحديث الحالة',
            'status' => $lic->status,
            'prev_status' => $prev,
            'license_id' => $lic->id,
        ]);
    }

    // تسجيل الخروج
    public function logout()
    {
        Auth::logout();
        return redirect()->route('admin.login');
    }

    // ================= Extra Admin APIs: Settings / Analytics / Logs / Backups / Packages / User status =================
    public function settings()
    {
        if (!Auth::check() || !$this->hasPerm('view_settings')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $stored = Setting::where('key', 'admin_settings')->first();
        $val = $stored ? (json_decode($stored->value, true) ?: []) : [];

        // prefer contact info from dedicated table if available
        $contactRow = null;
        try { $contactRow = ContactInfo::orderByDesc('id')->first(); } catch (\Throwable $_) { $contactRow = null; }

        $data = [
            'platform' => [
                'name' => $val['platform']['name'] ?? config('app.name', 'منصة الاستثمار'),
                'url' => $val['platform']['url'] ?? config('app.url', url('/')),
                'locale' => $val['platform']['locale'] ?? config('app.locale', 'ar'),
                'description' => $val['platform']['description'] ?? env('APP_DESCRIPTION', 'عوائد الخليج للاستثمار السعودي'),
            ],
            'contact' => [
                'email' => ($contactRow && $contactRow->email) ? $contactRow->email : ($val['contact']['email'] ?? config('mail.from.address', 'support@example.com')),
                'phone' => ($contactRow && $contactRow->phone) ? $contactRow->phone : ($val['contact']['phone'] ?? env('APP_CONTACT_PHONE', '+966-5X-XXX-XXXX')),
                'whatsapp' => ($contactRow && $contactRow->whatsapp) ? $contactRow->whatsapp : ($val['contact']['whatsapp'] ?? env('APP_CONTACT_WHATSAPP', '+966-5X-XXX-XXXX')),
                'address' => ($contactRow && $contactRow->address) ? $contactRow->address : ($val['contact']['address'] ?? env('APP_CONTACT_ADDRESS', 'الرياض، المملكة العربية السعودية')),
            ],
            'advanced' => [
                'maintenance_mode' => (bool)($val['advanced']['maintenance_mode'] ?? false),
                'registration_enabled' => (bool)($val['advanced']['registration_enabled'] ?? true),
                'investment_enabled' => (bool)($val['advanced']['investment_enabled'] ?? true),
                'default_success_rate' => (int)($val['advanced']['default_success_rate'] ?? 98),
            ]
        ];
        return response()->json($data);
    }

    public function updateSettings(Request $request)
    {
        if (!Auth::check() || !$this->hasPerm('manage_settings')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $incoming = $request->validate([
            'platform' => 'sometimes|array',
            'platform.name' => 'sometimes|string|max:255',
            'platform.url' => 'sometimes|string|max:255',
            'platform.locale' => 'sometimes|string|max:10',
            'platform.description' => 'sometimes|string|max:2000',
            'contact' => 'sometimes|array',
            'contact.email' => 'sometimes|string|max:255',
            'contact.phone' => 'sometimes|string|max:255',
            'contact.whatsapp' => 'sometimes|string|max:255',
            'contact.address' => 'sometimes|string|max:1000',
            'advanced' => 'sometimes|array',
            'advanced.maintenance_mode' => 'sometimes|boolean',
            'advanced.registration_enabled' => 'sometimes|boolean',
            'advanced.investment_enabled' => 'sometimes|boolean',
            'advanced.default_success_rate' => 'sometimes|numeric|min:0|max:100',
            // bank fields (moved to dedicated table)
            'bank' => 'sometimes|array',
            'bank.account_name' => 'sometimes|nullable|string|max:255',
            'bank.bank_name' => 'sometimes|nullable|string|max:255',
            'bank.account_number' => 'sometimes|nullable|string|max:255',
            'bank.iban' => 'sometimes|nullable|string|max:255',
            'bank.swift' => 'sometimes|nullable|string|max:255',
            'bank.reference_code' => 'sometimes|nullable|string|max:255',
            'bank.instructions' => 'sometimes|nullable|string|max:2000',
            'bank.ask_sender' => 'sometimes|boolean',
            'bank.ask_datetime' => 'sometimes|boolean',
            'bank.require_receipt' => 'sometimes|boolean',
            'bank.is_active' => 'sometimes|boolean',
        ]);

        $stored = Setting::where('key','admin_settings')->first();
        $current = $stored ? (json_decode($stored->value, true) ?: []) : [];
        $merged = array_replace_recursive($current, $incoming);

        Setting::updateOrCreate(
            ['key' => 'admin_settings'],
            ['value' => json_encode($merged, JSON_UNESCAPED_UNICODE)]
        );

        // Persist bank settings into dedicated bank_accounts table. We keep a single active bank account record.
        if (isset($incoming['bank']) && is_array($incoming['bank'])) {
            $b = $incoming['bank'];
            // attempt to find existing active bank account
            $existing = BankAccount::where('is_active', true)->first();
            if (!$existing) {
                $existing = new BankAccount();
            }
            $existing->account_name = $b['account_name'] ?? $existing->account_name;
            $existing->bank_name = $b['bank_name'] ?? $existing->bank_name;
            $existing->account_number = $b['account_number'] ?? $existing->account_number;
            $existing->iban = $b['iban'] ?? $existing->iban;
            $existing->swift = $b['swift'] ?? $existing->swift;
            $existing->instructions = $b['instructions'] ?? $existing->instructions;
            $existing->ask_sender = isset($b['ask_sender']) ? (bool)$b['ask_sender'] : (bool)$existing->ask_sender;
            $existing->ask_datetime = isset($b['ask_datetime']) ? (bool)$b['ask_datetime'] : (bool)$existing->ask_datetime;
            $existing->require_receipt = isset($b['require_receipt']) ? (bool)$b['require_receipt'] : (bool)$existing->require_receipt;
            $existing->is_active = isset($b['is_active']) ? (bool)$b['is_active'] : true;
            $existing->reference_code = $b['reference_code'] ?? $existing->reference_code;
            $existing->created_by = Auth::check() ? Auth::user()->id : null;
            $existing->save();
        }

        // Persist contact info into dedicated contact_infos table (keep latest row as current)
        if (isset($incoming['contact']) && is_array($incoming['contact'])) {
            try {
                $c = $incoming['contact'];
                $existingContact = ContactInfo::orderByDesc('id')->first();
                if (!$existingContact) {
                    $existingContact = new ContactInfo();
                }
                if (array_key_exists('email', $c)) $existingContact->email = $c['email'];
                if (array_key_exists('phone', $c)) $existingContact->phone = $c['phone'];
                if (array_key_exists('whatsapp', $c)) $existingContact->whatsapp = $c['whatsapp'];
                if (array_key_exists('address', $c)) $existingContact->address = $c['address'];
                // store admin identifier as UUID string in updated_by_uuid to support UUID user ids
                $existingContact->updated_by_uuid = Auth::check() ? (string)Auth::user()->id : null;
                $existingContact->save();
            } catch (\Throwable $e) {
                // non-fatal: log and continue
                try { Log::warning('ContactInfo save failed: ' . $e->getMessage()); } catch (\Throwable $_) {}
            }
        }

        return response()->json(['message' => 'تم حفظ الإعدادات', 'data' => $merged]);
    }

    // Admin-only endpoint to update contact info directly into contact_infos table.
    public function updateContactInfo(Request $request)
    {
        if (!Auth::check() || !$this->hasPerm('manage_settings')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $data = $request->validate([
            'email' => 'sometimes|nullable|string|max:255',
            'phone' => 'sometimes|nullable|string|max:255',
            'whatsapp' => 'sometimes|nullable|string|max:255',
            'address' => 'sometimes|nullable|string|max:1000',
        ]);

        try {
            // Create a new row to keep history of changes - or update latest row if you prefer
            $contact = ContactInfo::orderByDesc('id')->first();
            if (!$contact) {
                $contact = new ContactInfo();
            }
            if (array_key_exists('email', $data)) $contact->email = $data['email'];
            if (array_key_exists('phone', $data)) $contact->phone = $data['phone'];
            if (array_key_exists('whatsapp', $data)) $contact->whatsapp = $data['whatsapp'];
            if (array_key_exists('address', $data)) $contact->address = $data['address'];
            // save the updating admin's UUID into updated_by_uuid (the numeric updated_by column is legacy)
            $contact->updated_by_uuid = Auth::check() ? (string)Auth::user()->id : null;
            $contact->save();

            return response()->json(['ok' => true, 'contact_id' => $contact->id, 'contact' => $contact]);
        } catch (\Throwable $e) {
            try { Log::warning('updateContactInfo failed: ' . $e->getMessage()); } catch (\Throwable $_) {}
            return response()->json(['message' => 'تعذر حفظ معلومات الاتصال'], 500);
        }
    }

    // Public read-only bank settings used by investor-facing pages
    public function publicBankSettings()
    {
        // return the active bank account if exists, else fallback to admin_settings JSON bank section
        $bank = BankAccount::where('is_active', true)->orderByDesc('id')->first();
        if ($bank) {
            return response()->json([
                'account_name' => $bank->account_name,
                'bank_name' => $bank->bank_name,
                'account_number' => $bank->account_number,
                'iban' => $bank->iban,
                'swift' => $bank->swift,
                'reference_code' => $bank->reference_code ?? null,
                'instructions' => $bank->instructions,
                'ask_sender' => (bool)$bank->ask_sender,
                'ask_datetime' => (bool)$bank->ask_datetime,
                'require_receipt' => (bool)$bank->require_receipt,
            ]);
        }

        $settings = Setting::where('key','admin_settings')->first();
        $val = $settings ? (json_decode($settings->value, true) ?: []) : [];
        $b = $val['bank'] ?? [];
        return response()->json([
            'account_name' => $b['account_name'] ?? null,
            'bank_name' => $b['bank_name'] ?? null,
            'account_number' => $b['account_number'] ?? null,
            'iban' => $b['iban'] ?? null,
            'swift' => $b['swift'] ?? null,
            'reference_code' => $b['reference_code'] ?? null,
            'instructions' => $b['instructions'] ?? null,
            'ask_sender' => (bool)($b['ask_sender'] ?? false),
            'ask_datetime' => (bool)($b['ask_datetime'] ?? false),
            'require_receipt' => (bool)($b['require_receipt'] ?? false),
        ]);
    }

    // Admin CRUD for bank accounts
    public function listBankAccounts()
    {
        if (!Auth::check() || !$this->hasPerm('view_settings')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $q = trim((string)request()->query('q', ''));
        $qb = BankAccount::orderByDesc('id');
        if ($q !== '') {
            $qb->where(function($w) use ($q) {
                $w->where('account_name', 'like', '%' . $q . '%')
                  ->orWhere('bank_name', 'like', '%' . $q . '%')
                  ->orWhere('account_number', 'like', '%' . $q . '%')
                  ->orWhere('iban', 'like', '%' . $q . '%');
            });
        }
        $rows = $qb->get();
        return response()->json($rows);
    }

    // ================= Success Stories CRUD (DB-backed) =================
    public function successStoriesIndex()
    {
        if (!Auth::check() || !$this->hasPerm('view_settings')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $rows = DB::table('success_stories')->orderByDesc('created_at')->get();
        $items = $rows->map(function($r){
            return [
                'id' => $r->id,
                'name' => $r->name,
                'avatar' => $r->avatar,
                'location' => $r->location,
                'investmentAmount' => $r->investment_amount ?? $r->amount ?? null,
                'returns' => $r->returns ?? null,
                'duration' => $r->duration ?? null,
                'profitRate' => $r->profit_rate ?? $r->profit ?? null,
                'category' => $r->category ?? null,
                'featured' => (bool)$r->featured,
                'isTop' => (bool)$r->is_top,
                'story' => $r->story ?? null,
                'testimonial' => $r->testimonial ?? null,
            ];
        })->toArray();

        // split into top_featured and stories
        $top = array_values(array_filter($items, function($i){ return !empty($i['isTop']); }));
        $others = array_values(array_filter($items, function($i){ return empty($i['isTop']); }));

        return response()->json(['top_featured' => $top, 'stories' => $others]);
    }

    public function successStoriesCreate(Request $request)
    {
        if (!Auth::check() || !$this->hasPerm('manage_settings')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'avatar' => 'sometimes|nullable|string|max:255',
            'location' => 'sometimes|nullable|string|max:255',
            'investmentAmount' => 'sometimes|nullable|string|max:255',
            'returns' => 'sometimes|nullable|string|max:255',
            'duration' => 'sometimes|nullable|string|max:255',
            'profitRate' => 'sometimes|nullable|string|max:255',
            'category' => 'sometimes|nullable|string|max:50',
            'featured' => 'sometimes|boolean',
            'isTop' => 'sometimes|boolean',
            'story' => 'sometimes|nullable|string|max:5000',
            'testimonial' => 'sometimes|nullable|string|max:2000',
        ]);

        $now = now();
        $insert = [
            'id' => (string) Str::uuid(),
            'name' => $data['name'],
            'avatar' => $data['avatar'] ?? null,
            'location' => $data['location'] ?? null,
            'investment_amount' => $data['investmentAmount'] ?? null,
            'returns' => $data['returns'] ?? null,
            'duration' => $data['duration'] ?? null,
            'profit_rate' => $data['profitRate'] ?? null,
            'category' => $data['category'] ?? null,
            'featured' => isset($data['featured']) ? (int)$data['featured'] : 0,
            'is_top' => isset($data['isTop']) ? (int)$data['isTop'] : 0,
            'story' => $data['story'] ?? null,
            'testimonial' => $data['testimonial'] ?? null,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        DB::table('success_stories')->insert($insert);

        return response()->json(['message' => 'created', 'id' => $insert['id']], 201);
    }

    public function successStoriesUpdate($id, Request $request)
    {
        if (!Auth::check() || !$this->hasPerm('manage_settings')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $exists = DB::table('success_stories')->where('id', $id)->exists();
        if (!$exists) return response()->json(['message' => 'Not found'], 404);

        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'avatar' => 'sometimes|nullable|string|max:255',
            'location' => 'sometimes|nullable|string|max:255',
            'investmentAmount' => 'sometimes|nullable|string|max:255',
            'returns' => 'sometimes|nullable|string|max:255',
            'duration' => 'sometimes|nullable|string|max:255',
            'profitRate' => 'sometimes|nullable|string|max:255',
            'category' => 'sometimes|nullable|string|max:50',
            'featured' => 'sometimes|boolean',
            'isTop' => 'sometimes|boolean',
            'story' => 'sometimes|nullable|string|max:5000',
            'testimonial' => 'sometimes|nullable|string|max:2000',
        ]);

        $update = [];
        if (array_key_exists('name', $data)) $update['name'] = $data['name'];
        if (array_key_exists('avatar', $data)) $update['avatar'] = $data['avatar'];
        if (array_key_exists('location', $data)) $update['location'] = $data['location'];
        if (array_key_exists('investmentAmount', $data)) $update['investment_amount'] = $data['investmentAmount'];
        if (array_key_exists('returns', $data)) $update['returns'] = $data['returns'];
        if (array_key_exists('duration', $data)) $update['duration'] = $data['duration'];
        if (array_key_exists('profitRate', $data)) $update['profit_rate'] = $data['profitRate'];
        if (array_key_exists('category', $data)) $update['category'] = $data['category'];
        if (array_key_exists('featured', $data)) $update['featured'] = (int)$data['featured'];
        if (array_key_exists('isTop', $data)) $update['is_top'] = (int)$data['isTop'];
        if (array_key_exists('story', $data)) $update['story'] = $data['story'];
        if (array_key_exists('testimonial', $data)) $update['testimonial'] = $data['testimonial'];

        $update['updated_at'] = now();
        DB::table('success_stories')->where('id', $id)->update($update);
        return response()->json(['message' => 'updated']);
    }

    public function successStoriesDelete($id)
    {
        if (!Auth::check() || !$this->hasPerm('manage_settings')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $exists = DB::table('success_stories')->where('id', $id)->exists();
        if (!$exists) return response()->json(['message' => 'Not found'], 404);
        DB::table('success_stories')->where('id', $id)->delete();
        return response()->json(['message' => 'deleted']);
    }

    // Admin: list investors (DB-backed) with optional search q and limit
    public function investors(Request $request)
    {
        if (!Auth::check() || !$this->hasPerm('view_investors')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $limit = (int) $request->query('limit', 100);
        $q = trim((string)$request->query('q', ''));
        $qb = \App\Models\Investor::orderByDesc('created_at');
        if ($q !== '') {
            $qb->where(function($w) use ($q) {
                $w->where('full_name', 'like', '%' . $q . '%')
                  ->orWhere('email', 'like', '%' . $q . '%')
                  ->orWhere('phone', 'like', '%' . $q . '%')
                  ->orWhere('national_id', 'like', '%' . $q . '%')
                  ->orWhere('id', 'like', '%' . $q . '%');
            });
        }
        $rows = $qb->take($limit)->get(['id','user_id','full_name as name','email','phone','national_id','identity_image','identity_image_front','identity_image_back']);
        return response()->json($rows);
    }

    // Admin: investor detail (includes recent investments)
    public function investorDetail($id)
    {
        if (!Auth::check() || !$this->hasPerm('view_investors')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $inv = \App\Models\Investor::find($id);
        if (!$inv) { return response()->json(['message' => 'Not found'], 404); }
        // include recent investments for this investor
        $investments = Investment::where('investor_id', $inv->id)
            ->orderByDesc('created_at')
            ->take(50)
            ->get(['id','amount','status','investment_type','profit_rate','expected_return','created_at']);

        return response()->json(['investor' => $inv, 'investments' => $investments]);
    }

    public function createBankAccount(Request $request)
    {
        if (!Auth::check() || !$this->hasPerm('manage_settings')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $data = $request->validate([
            'account_name' => 'sometimes|nullable|string|max:255',
            'bank_name' => 'sometimes|nullable|string|max:255',
            'account_number' => 'sometimes|nullable|string|max:255',
            'iban' => 'sometimes|nullable|string|max:255',
            'swift' => 'sometimes|nullable|string|max:255',
            'reference_code' => 'sometimes|nullable|string|max:255',
            'instructions' => 'sometimes|nullable|string|max:2000',
            'ask_sender' => 'sometimes|boolean',
            'ask_datetime' => 'sometimes|boolean',
            'require_receipt' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean',
        ]);

        // If this one is marked active, deactivate others
        if (isset($data['is_active']) && $data['is_active']) {
            BankAccount::where('is_active', true)->update(['is_active' => false]);
        }

        $data['created_by'] = Auth::user() ? Auth::user()->id : null;
    if (isset($data['reference_code'])) { $data['reference_code'] = $data['reference_code']; }
        $b = BankAccount::create($data);
        return response()->json($b, 201);
    }

    public function updateBankAccount($id, Request $request)
    {
        if (!Auth::check() || !$this->hasPerm('manage_settings')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $b = BankAccount::find($id);
        if (!$b) { return response()->json(['message' => 'Not found'], 404); }

        $data = $request->validate([
            'account_name' => 'sometimes|nullable|string|max:255',
            'bank_name' => 'sometimes|nullable|string|max:255',
            'account_number' => 'sometimes|nullable|string|max:255',
            'iban' => 'sometimes|nullable|string|max:255',
            'swift' => 'sometimes|nullable|string|max:255',
            'reference_code' => 'sometimes|nullable|string|max:255',
            'instructions' => 'sometimes|nullable|string|max:2000',
            'ask_sender' => 'sometimes|boolean',
            'ask_datetime' => 'sometimes|boolean',
            'require_receipt' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean',
        ]);

        if (isset($data['is_active']) && $data['is_active']) {
            BankAccount::where('is_active', true)->where('id', '!=', $b->id)->update(['is_active' => false]);
        }

    // ensure reference_code is applied if provided
    if (isset($data['reference_code'])) { $b->reference_code = $data['reference_code']; }

    // Fill bank account attributes and persist
    $b->fill($data);
    $b->save();

    return response()->json($b);

    }

    public function deleteBankAccount($id)
    {
        if (!Auth::check() || !$this->hasPerm('manage_settings')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $b = BankAccount::find($id);
        if (!$b) { return response()->json(['message' => 'Not found'], 404); }
        $b->delete();
        return response()->json(['message' => 'deleted']);
    }

    public function analytics()
    {
        if (!Auth::check() || (!($this->hasPerm('view_dashboard') || $this->hasPerm('view_analytics')))) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $usersTotal = User::count();
        $usersToday = User::whereDate('created_at', now()->toDateString())->count();
        $usersActive = User::where('is_active',1)->count();

        $invTotal = Investment::count();
        $invActive = Investment::where('status','active')->count();
        $invPending = Investment::where('status','pending')->count();
        $invCompleted = Investment::where('status','completed')->count();

        $licApproved = License::where('status','approved')->count();
        $licPending = License::where('status','pending')->count();

        $settings = Setting::where('key','admin_settings')->first();
        $defRate = 98;
        if($settings){
            $val = json_decode($settings->value, true) ?: [];
            $defRate = (int)($val['advanced']['default_success_rate'] ?? $defRate);
        }
        $successRate = $invTotal > 0 ? round(($invCompleted / max($invTotal,1)) * 100) : $defRate;

        return response()->json([
            'users' => ['total'=>$usersTotal, 'new_today'=>$usersToday, 'active'=>$usersActive],
            'investments' => ['active'=>$invActive, 'pending'=>$invPending, 'completed'=>$invCompleted],
            'licenses' => ['approved'=>$licApproved, 'pending'=>$licPending],
            'success_rate' => $successRate,
        ]);
    }

    public function packages()
    {
        if (!Auth::check() || !$this->hasPerm('view_settings')) { return response()->json(['message' => 'Unauthorized'], 401); }
        return response()->json([
            ['id' => 1, 'name' => 'باقة أساسية', 'profit_rate' => 8, 'min_amount' => 5000],
            ['id' => 2, 'name' => 'باقة متقدمة', 'profit_rate' => 12, 'min_amount' => 20000],
            ['id' => 3, 'name' => 'باقة مميزة', 'profit_rate' => 15, 'min_amount' => 50000],
        ]);
    }

    // ================= Withdrawals Management =================
    public function withdrawals(Request $request)
    {
        if (!Auth::check() || !$this->hasPerm('manage_withdrawals')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $limit = (int) $request->query('limit', 50);
        $rows = DB::table('withdrawal_requests')
            ->leftJoin('users','withdrawal_requests.user_id','=','users.id')
            ->select('withdrawal_requests.*','users.full_name as user_full_name','users.email as user_email')
            ->orderByDesc('withdrawal_requests.created_at')
            ->take($limit)
            ->get();
        return response()->json($rows);
    }

    public function updateWithdrawalStatus($id, Request $request)
    {
        if (!Auth::check() || !$this->hasPerm('manage_withdrawals')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $data = $request->validate([
            'status' => 'required|string|in:pending,approved,rejected,paid',
            'reason' => 'sometimes|nullable|string'
        ]);
        $w = DB::table('withdrawal_requests')->where('id',$id)->first();
        if (!$w) { return response()->json(['message'=>'Not found'],404); }

        $now = now();
        DB::table('withdrawal_requests')->where('id',$id)->update([
            'status' => $data['status'],
            'reason' => $data['reason'] ?? $w->reason,
            'processed_at' => in_array($data['status'], ['approved','rejected','paid']) ? $now : null,
            'updated_at' => $now,
        ]);

        return response()->json(['message'=>'updated']);
    }

    public function payWithdrawal($id)
    {
        if (!Auth::check() || !$this->hasPerm('manage_withdrawals')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $w = DB::table('withdrawal_requests')->where('id',$id)->first();
        if (!$w) { return response()->json(['message'=>'Not found'],404); }
        if ($w->status === 'paid') { return response()->json(['message'=>'Already paid'],409); }
        if (!in_array($w->status, ['approved','pending'])) { return response()->json(['message'=>'Invalid state'],422); }

        $now = now();
        $paymentId = (string) Str::uuid();
        $reference = 'WD-' . strtoupper(Str::random(10));

        DB::transaction(function() use ($w, $now, $paymentId, $reference, $id) {
            // Create a payment record tied to the withdrawal (status completed)
            DB::table('payments')->insert([
                'id' => $paymentId,
                'user_id' => $w->user_id,
                'investment_id' => $w->investment_id,
                'withdrawal_request_id' => $w->id,
                'payment_method_id' => null,
                'amount' => $w->amount,
                'currency' => 'USD',
                'status' => 'completed',
                'transaction_reference' => $reference,
                'processed_at' => $now,
                'note' => 'Withdrawal payout',
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // Post platform transaction (debit main account)
            $account = DB::table('platform_accounts')
                ->where('type','main')->where('is_active',1)
                ->orderBy('created_at','asc')->first();
            if ($account) {
                $before = (float) $account->balance;
                $after = $before - (float) $w->amount;
                DB::table('platform_accounts')->where('id',$account->id)->update([
                    'balance' => $after,
                    'updated_at' => $now,
                ]);
                DB::table('platform_transactions')->insert([
                    'id' => (string) Str::uuid(),
                    'platform_account_id' => $account->id,
                    'payment_id' => $paymentId,
                    'withdrawal_request_id' => $w->id,
                    'investment_transaction_id' => null,
                    'type' => 'debit',
                    'amount' => $w->amount,
                    'balance_before' => $before,
                    'balance_after' => $after,
                    'description' => 'Withdrawal payout processed',
                    'transaction_date' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            // mark withdrawal as paid
            DB::table('withdrawal_requests')->where('id',$id)->update([
                'status' => 'paid',
                'processed_at' => $now,
                'updated_at' => $now,
            ]);
        });

        return response()->json(['message'=>'paid']);
    }

    public function logs()
    {
        if (!Auth::check() || !$this->hasPerm('view_logs')) { return response()->json(['message' => 'Unauthorized'], 401); }

        $events = [];

        // Recent user registrations
        try {
            $users = User::orderBy('created_at', 'desc')->take(8)->get(['id', 'email', 'full_name', 'created_at']);
            foreach ($users as $u) {
                $label = $u->full_name ? $u->full_name : $u->email;
                $events[] = [
                    'id' => 'user_' . $u->id,
                    'type' => 'user_registration',
                    'message' => 'تسجيل مستخدم جديد: ' . $label,
                    'created_at' => $u->created_at,
                ];
            }
        } catch (\Throwable $_) { /* ignore */ }

        // Recent investments
        try {
            $invs = Investment::orderBy('created_at', 'desc')->take(8)->get();
            foreach ($invs as $inv) {
                $amt = is_numeric($inv->amount) ? number_format($inv->amount, 0) . ' ر.س' : (string)$inv->amount;
                $events[] = [
                    'id' => 'inv_' . $inv->id,
                    'type' => 'investment_created',
                    'message' => 'طلب استثمار جديد #' . $inv->id . ' بمبلغ ' . $amt,
                    'created_at' => $inv->created_at,
                ];
            }
        } catch (\Throwable $_) { /* ignore */ }

        // Recent license requests
        try {
            $lics = License::orderBy('created_at', 'desc')->take(8)->get();
            foreach ($lics as $lic) {
                $name = $lic->business_name ?: $lic->id;
                $events[] = [
                    'id' => 'lic_' . $lic->id,
                    'type' => 'license_request',
                    'message' => 'طلب ترخيص جديد: ' . $name,
                    'created_at' => $lic->created_at,
                ];
            }
        } catch (\Throwable $_) { /* ignore */ }

        // Recent payment reminders
        try {
            $rems = \App\Models\PaymentReminder::orderBy('created_at', 'desc')->take(8)->get();
            foreach ($rems as $r) {
                $events[] = [
                    'id' => 'rem_' . $r->id,
                    'type' => 'payment_reminder',
                    'message' => 'تذكير دفع (استثمار: ' . ($r->investment_id ?? '-') . ')',
                    'created_at' => $r->created_at,
                ];
            }
        } catch (\Throwable $_) { /* ignore */ }

        // Sort by created_at desc and limit to 20 items
        usort($events, function ($a, $b) {
            $ta = strtotime((string)$a['created_at']);
            $tb = strtotime((string)$b['created_at']);
            return $tb <=> $ta; // newer first
        });
        $events = array_slice($events, 0, 20);

        // Normalize created_at to string
        $events = array_map(function ($e) {
            if (isset($e['created_at']) && method_exists($e['created_at'], 'toDateTimeString')) {
                $e['created_at'] = $e['created_at']->toDateTimeString();
            } else {
                $e['created_at'] = (string) $e['created_at'];
            }
            return $e;
        }, $events);

        return response()->json($events);
    }

    public function backups()
    {
        // Allow viewing list if user has either view_backups or manage_backups
        if (!Auth::check() || (!($this->hasPerm('view_backups') || $this->hasPerm('manage_backups')))) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        // List files from storage/app/backups
        $files = Storage::disk('local')->files('backups');
        $items = [];
        foreach ($files as $path) {
            if (!str_ends_with($path, '.zip')) { continue; }
            $filename = basename($path);
            $sizeBytes = Storage::disk('local')->size($path);
            $sizeMb = round(($sizeBytes / (1024*1024)), 2);
            $time = Storage::disk('local')->lastModified($path);
            $items[] = [
                'file' => $filename,
                'size_mb' => $sizeMb,
                'created_at' => date('Y-m-d H:i:s', $time),
            ];
        }
        // Sort latest first
        usort($items, fn($a,$b) => strcmp($b['created_at'], $a['created_at']));
        return response()->json($items);
    }

    public function runBackup()
    {
        if (!Auth::check() || !$this->hasPerm('manage_backups')) { return response()->json(['message' => 'Unauthorized'], 401); }
        if (!class_exists(ZipArchive::class)) {
            return response()->json(['message' => 'امتداد ZipArchive غير متوفر على الخادم. فعّل php_zip أولاً.'], 500);
        }
        // Ensure directory exists
        $dir = storage_path('app/backups');
        if (!is_dir($dir)) { @mkdir($dir, 0775, true); }

        $file = 'backup_'.now()->format('Y-m-d_His').'.zip';
        $fullPath = $dir . DIRECTORY_SEPARATOR . $file;

        $zip = new ZipArchive();
        if ($zip->open($fullPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return response()->json(['message' => 'تعذر إنشاء ملف النسخة الاحتياطية'], 500);
        }

        // Export minimal but useful data to JSON inside the ZIP
        $settings = Setting::where('key','admin_settings')->first();
        $settingsArr = $settings ? (json_decode($settings->value, true) ?: []) : [];
        $zip->addFromString('settings.json', json_encode($settingsArr, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        $packages = InvestmentPackage::orderBy('id','asc')->get()->toArray();
        $zip->addFromString('packages.json', json_encode($packages, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        // Metadata
        $zip->addFromString('meta.json', json_encode([
            'app' => config('app.name'),
            'created_at' => now()->toDateTimeString(),
            'version' => '1.0',
            'includes' => ['settings.json', 'packages.json'],
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        $zip->close();

        return response()->json(['message' => 'تم إنشاء النسخة الاحتياطية', 'file' => $file]);
    }

    // Download a backup file
    public function downloadBackup($file)
    {
        if (!Auth::check() || !$this->hasPerm('manage_backups')) { abort(401); }
        $safeFile = basename($file);
        $path = 'backups/' . $safeFile;
        if (!Storage::disk('local')->exists($path)) { abort(404); }
    return response()->download(storage_path('app/'.$path));
    }

    // Restore from backup file (uploaded or by existing filename)
    public function restoreBackup(Request $request)
    {
        if (!Auth::check() || !$this->hasPerm('manage_backups')) { return response()->json(['message' => 'Unauthorized'], 401); }
        if (!class_exists(ZipArchive::class)) {
            return response()->json(['message' => 'امتداد ZipArchive غير متوفر على الخادم. فعّل php_zip أولاً.'], 500);
        }

        $uploaded = $request->file('file');
        $filename = $request->input('filename');

        // Determine source path
        if ($uploaded) {
            $request->validate(['file' => 'required|file|mimes:zip']);
            $stored = $uploaded->storeAs('backups', 'restore_'.now()->format('Ymd_His').'.zip');
            $fullPath = storage_path('app/'.$stored);
        } else if ($filename) {
            $safe = basename($filename);
            $path = 'backups/'.$safe;
            if (!Storage::disk('local')->exists($path)) {
                return response()->json(['message' => 'لم يتم العثور على الملف'], 404);
            }
            $fullPath = storage_path('app/'.$path);
        } else {
            return response()->json(['message' => 'الرجاء تزويد ملف النسخة الاحتياطية أو اسم الملف'], 422);
        }

        // Open ZIP and import known JSON files
        $zip = new ZipArchive();
        if ($zip->open($fullPath) !== true) {
            return response()->json(['message' => 'تعذر فتح ملف النسخة الاحتياطية'], 500);
        }

        // Restore settings.json
        $idx = $zip->locateName('settings.json');
        if ($idx !== false) {
            $content = $zip->getFromIndex($idx);
            $arr = json_decode($content, true);
            if (is_array($arr)) {
                Setting::updateOrCreate(['key'=>'admin_settings'], ['value' => json_encode($arr, JSON_UNESCAPED_UNICODE)]);
            }
        }

        // Restore packages.json (replace all packages with the provided set)
        $idx2 = $zip->locateName('packages.json');
        if ($idx2 !== false) {
            $content2 = $zip->getFromIndex($idx2);
            $list = json_decode($content2, true);
            if (is_array($list)) {
                DB::transaction(function() use ($list) {
                    InvestmentPackage::truncate();
                    foreach ($list as $row) {
                        // Only allow known fields
                        $amount = isset($row['amount']) ? $row['amount'] : 0;
                        $profit = isset($row['profit_rate']) ? $row['profit_rate'] : 0;
                        InvestmentPackage::create(['amount' => $amount, 'profit_rate' => $profit]);
                    }
                });
            }
        }

        $zip->close();

        return response()->json(['message' => 'تمت الاستعادة بنجاح']);
    }

    public function listPackages()
    {
        if (!Auth::check() || !$this->hasPerm('view_settings')) { return response()->json(['message' => 'Unauthorized'], 401); }
        $q = trim((string)request()->query('q', ''));
        $pkgQuery = InvestmentPackage::orderBy('id','desc');
        if ($q !== '') {
            $pkgQuery->where(function($w) use ($q) {
                $w->where('name', 'like', '%' . $q . '%')
                  ->orWhere('id', 'like', '%' . $q . '%')
                  ->orWhere('amount', 'like', '%' . $q . '%')
                  ->orWhere('profit_rate', 'like', '%' . $q . '%');
            });
        }
        return response()->json($pkgQuery->get());
    }

    

    public function createPackage(Request $request)
    {
        if (!Auth::check() || !$this->hasPerm('manage_packages')) { return response()->json(['message' => 'Unauthorized'], 401); }
        $data = $request->validate([
            'amount' => 'required|numeric|min:0',
            'profit_rate' => 'required|numeric|min:0',
            'name' => 'sometimes|nullable|string|max:255',
            'duration_months' => 'sometimes|nullable|integer|min:1',
            'description' => 'sometimes|nullable|string',
            'is_active' => 'sometimes|boolean',
        ]);
        // Provide a default name if DB requires it and none supplied
        if (empty($data['name'])) {
            $data['name'] = 'حزمة ' . (InvestmentPackage::count() + 1);
        }
        $pkg = InvestmentPackage::create($data);
        return response()->json($pkg);
    }

    public function updatePackage($id, Request $request)
    {
        if (!Auth::check() || !$this->hasPerm('manage_packages')) { return response()->json(['message' => 'Unauthorized'], 401); }
        $pkg = InvestmentPackage::find($id);
        if(!$pkg){ return response()->json(['message'=>'Not found'],404); }
        $data = $request->validate([
            'amount' => 'sometimes|numeric|min:0',
            'profit_rate' => 'sometimes|numeric|min:0',
            'name' => 'sometimes|nullable|string|max:255',
            'duration_months' => 'sometimes|nullable|integer|min:1',
            'description' => 'sometimes|nullable|string',
            'is_active' => 'sometimes|boolean',
        ]);
        $pkg->update($data);
        return response()->json($pkg);
    }

    public function deletePackage($id)
    {
        if (!Auth::check() || !$this->hasPerm('manage_packages')) { return response()->json(['message' => 'Unauthorized'], 401); }
        $pkg = InvestmentPackage::find($id);
        if(!$pkg){ return response()->json(['message'=>'Not found'],404); }
        $pkg->delete();
        return response()->json(['message'=>'deleted']);
    }

    public function updateUserStatus($id, Request $request)
    {
        if (!Auth::check() || !$this->hasPerm('manage_users')) { return response()->json(['message' => 'Unauthorized'], 401); }
        $data = $request->validate(['status'=>'required|string|in:active,inactive']);
        $user = User::find($id);
        if(!$user){ return response()->json(['message'=>'Not found'],404); }
        $user->is_active = $data['status'] === 'active' ? 1 : 0;
        $user->save();
        return response()->json(['message'=>'updated']);
    }

    // ================= Navigation CRUD =================
    public function navIndex()
    {
        if (!Auth::check() || !$this->hasPerm('manage_navigation')) { return response()->json(['message' => 'Unauthorized'], 401); }
        return response()->json(NavigationItem::orderBy('order')->get());
    }

    public function navStore(Request $request)
    {
        if (!Auth::check() || !$this->hasPerm('manage_navigation')) { return response()->json(['message' => 'Unauthorized'], 401); }
        $data = $request->validate([
            'path' => 'required|string|max:255',
            'label' => 'required|string|max:255',
            'icon' => 'nullable|string|max:50',
            'order' => 'nullable|integer',
            'active' => 'nullable|boolean',
        ]);
        $item = NavigationItem::create($data + ['active' => $request->boolean('active', true), 'order' => $request->input('order', 0)]);
        return response()->json($item);
    }

    public function navUpdate($id, Request $request)
    {
        if (!Auth::check() || !$this->hasPerm('manage_navigation')) { return response()->json(['message' => 'Unauthorized'], 401); }
        $item = NavigationItem::find($id);
        if(!$item){ return response()->json(['message'=>'Not found'],404); }
        $data = $request->validate([
            'path' => 'sometimes|string|max:255',
            'label' => 'sometimes|string|max:255',
            'icon' => 'sometimes|nullable|string|max:50',
            'order' => 'sometimes|integer',
            'active' => 'sometimes|boolean',
        ]);
        $item->update($data);
        return response()->json($item);
    }

    public function navDestroy($id)
    {
        if (!Auth::check() || !$this->hasPerm('manage_navigation')) { return response()->json(['message' => 'Unauthorized'], 401); }
        $item = NavigationItem::find($id);
        if(!$item){ return response()->json(['message'=>'Not found'],404); }
        $item->delete();
        return response()->json(['message' => 'deleted']);
    }

    // ================= Permissions APIs =================
    public function listPermissions()
    {
        if (!Auth::check() || !($this->hasPerm('view_settings') || $this->hasPerm('manage_roles_permissions') || $this->hasPerm('manage_user_permissions'))) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        return response()->json(Permission::orderBy('category')->orderBy('key')->get());
    }

    public function getRolePermissions($role)
    {
        if (!Auth::check() || !($this->hasPerm('view_settings') || $this->hasPerm('manage_roles_permissions'))) { return response()->json(['message' => 'Unauthorized'], 401); }
        $keys = RolePermission::where('role',$role)->pluck('permission_key')->values();
        return response()->json($keys);
    }

    public function setRolePermissions($role, Request $request)
    {
        if (!Auth::check() || !$this->hasPerm('manage_roles_permissions')) { return response()->json(['message' => 'Unauthorized'], 401); }
        $data = $request->validate(['permissions'=>'array']);
        $perms = collect($data['permissions'] ?? [])->map(fn($v)=> (string)$v)->unique()->values();
        DB::transaction(function() use ($role, $perms){
            RolePermission::where('role',$role)->delete();
            foreach ($perms as $k) {
                RolePermission::create(['role'=>$role,'permission_key'=>$k]);
            }
        });
        return response()->json(['message'=>'saved']);
    }

    // User-specific permissions
    public function getUserPermissions($id)
    {
        if (!Auth::check() || !$this->hasPerm('manage_user_permissions')) { return response()->json(['message'=>'Unauthorized'],401); }
        $u = User::find($id);
        if(!$u){ return response()->json(['message'=>'Not found'],404); }
        $keys = UserPermission::where('user_id',$u->id)->pluck('permission_key')->values();
        return response()->json($keys);
    }

    public function setUserPermissions($id, Request $request)
    {
        if (!Auth::check() || !$this->hasPerm('manage_user_permissions')) { return response()->json(['message'=>'Unauthorized'],401); }
        $u = User::find($id);
        if(!$u){ return response()->json(['message'=>'Not found'],404); }
        $data = $request->validate(['permissions'=>'array']);
        $perms = collect($data['permissions'] ?? [])->map(fn($v)=>(string)$v)->unique()->values();
        DB::transaction(function() use ($u,$perms){
            UserPermission::where('user_id',$u->id)->delete();
            foreach ($perms as $k) { UserPermission::create(['user_id'=>$u->id,'permission_key'=>$k]); }
        });
        return response()->json(['message'=>'saved']);
    }

    // ================ Documents APIs =================
    public function documents()
    {
        if (!Auth::check() || !$this->hasPerm('view_documents')) { return response()->json(['message'=>'Unauthorized'],401); }
        $q = trim((string)request()->query('q', ''));
        $docsQuery = Document::with(['user:id,full_name,email'])->orderBy('created_at','desc');
        if ($q !== '') {
            $docsQuery->where(function($w) use ($q) {
                $w->where('filename', 'like', '%' . $q . '%')
                  ->orWhere('type', 'like', '%' . $q . '%')
                  ->orWhere('id', 'like', '%' . $q . '%')
                  ->orWhere('path', 'like', '%' . $q . '%');
            })->orWhereHas('user', function($u) use ($q) {
                $u->where('full_name', 'like', '%' . $q . '%')
                  ->orWhere('email', 'like', '%' . $q . '%');
            });
        }
        $docs = $docsQuery->take(100)->get()->map(function($d){
                return [
                    'id' => $d->id,
                    'filename' => $d->filename,
                    'path' => $d->path,
                    'type' => $d->type,
                    'user_full_name' => optional($d->user)->full_name,
                    'user_email' => optional($d->user)->email,
                    'created_at' => $d->created_at,
                    'url' => $d->path ? Storage::url($d->path) : null,
                ];
            });
        return response()->json($docs);
    }

    public function deleteDocument($id)
    {
        if (!Auth::check() || !$this->hasPerm('manage_documents')) { return response()->json(['message'=>'Unauthorized'],401); }
        $doc = Document::find($id);
        if(!$doc) { return response()->json(['message'=>'Not found'],404); }
        $doc->delete();
        return response()->json(['message'=>'deleted']);
    }

    

}


