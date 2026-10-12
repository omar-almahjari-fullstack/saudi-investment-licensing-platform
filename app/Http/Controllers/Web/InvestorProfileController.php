<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Schema;

class InvestorProfileController extends Controller
{
    /**
     * Save investor profile fields from investment page (AJAX JSON endpoint).
     */
    public function saveFromInvestment(Request $request)
    {
        $user = Auth::user();
        $uid = $user ? $user->id : $request->session()->get('user_id');
        if (!$uid) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $data = $request->validate([
            'full_name' => 'sometimes|nullable|string|max:255',
            'email' => 'sometimes|nullable|email|max:255',
            'phone' => 'sometimes|nullable|string|max:50',
            'national_id' => 'sometimes|nullable|string|max:100',
            'bank_account' => 'sometimes|nullable|string|max:64',
            'identity_image' => 'sometimes|file|mimes:jpg,jpeg,png,webp,pdf|max:5120',
            'mudat_alaistithmar' => 'sometimes|nullable|string|max:50',
            'isLarge' => 'sometimes', // boolean-like
            'notes' => 'sometimes|nullable|string',
        ]);

        $now = now();

        // Ensure an investor row exists
        $investor = DB::table('investors')->where('user_id', $uid)->first();
        if (!$investor) {
            DB::table('investors')->insert([
                'id' => (string) Str::uuid(),
                'user_id' => $uid,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $investor = DB::table('investors')->where('user_id', $uid)->first();
        }

        $updates = [ 'updated_at' => $now ];
        foreach (['full_name','email','phone','national_id','bank_account','notes'] as $f) {
            if ($request->has($f)) { $updates[$f] = $data[$f] ?? null; }
        }

        if ($request->has('mudat_alaistithmar')) {
            $updates['investment_duration'] = $data['mudat_alaistithmar'] ?? null;
        }
        if ($request->has('isLarge')) {
            $bool = filter_var($request->input('isLarge'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($bool === null) {
                // accept strings 'large'/'small' as well
                $val = strtolower((string) $request->input('isLarge'));
                $updates['investment_size'] = in_array($val, ['large','big']) ? 'large' : (in_array($val,['small','little']) ? 'small' : null);
            } else {
                $updates['investment_size'] = $bool ? 'large' : 'small';
            }
        }

        if ($request->hasFile('identity_image')) {
            $path = Storage::disk('public')->putFile('identities', $request->file('identity_image'));
            $updates['identity_image'] = $path;
        }

        DB::table('investors')->where('id', $investor->id)->update($updates);
        // If national_id was provided, enforce immutability on users.national_id
        if (isset($updates['national_id']) && Schema::hasColumn('users', 'national_id')) {
            $existing = DB::table('users')->where('id', $uid)->value('national_id');
            if (!empty($existing) && trim($existing) !== trim($updates['national_id'])) {
                return response()->json(['ok' => false, 'errors' => ['national_id' => ['رقم الهوية مسجّل مسبقاً ولا يمكن تغييره.']]], 422);
            }
            if (empty($existing) && !empty($updates['national_id'])) {
                DB::table('users')->where('id', $uid)->update(['national_id' => $updates['national_id']]);
            }
        }

        $fresh = DB::table('investors')->where('id', $investor->id)->first();
        // Prefer returning users.national_id if present
        if (Schema::hasColumn('users', 'national_id')) {
            $userNid = DB::table('users')->where('id', $uid)->value('national_id');
            if ($userNid) { $fresh->national_id = $userNid; }
        }

        return response()->json([
            'status' => 'ok',
            'investor' => $fresh,
        ]);
    }

    /**
     * Serve investor image file from public disk safely.
     * URL: /investor/{investorId}/image/{side}
     */
    public function serveImage($investorId, $side)
    {
        // resolve investor by id or by user_id
        $inv = DB::table('investors')
            ->where('id', $investorId)
            ->orWhere('user_id', $investorId)
            ->first();
        // If no investor row found, or investor doesn't have the requested field,
        // attempt to fall back to the users table (some flows persist images on users).
        $path = null;
        $field = $side === 'back' ? 'identity_image_back' : 'identity_image_front';

        if ($inv) {
            $path = $inv->{$field} ?? $inv->identity_image ?? null;
        }

        // If path still empty, try to locate on the users table.
        try {
            // If $investorId was actually a user id, this will find the user row.
            $user = DB::table('users')->where('id', $investorId)->first();
            if (!$user && $inv && !empty($inv->user_id)) {
                // If we found an investor but it didn't have the image, check the related user
                $user = DB::table('users')->where('id', $inv->user_id)->first();
            }
            if ($user) {
                if ($side === 'back') {
                    if (Schema::hasColumn('users', 'identity_image_back') && !empty($user->identity_image_back)) {
                        $path = $user->identity_image_back;
                    }
                } else {
                    if (Schema::hasColumn('users', 'identity_image_front') && !empty($user->identity_image_front)) {
                        $path = $user->identity_image_front;
                    } elseif (!empty($user->identity_image)) {
                        $path = $user->identity_image;
                    }
                }
            }
        } catch (\Throwable $e) {
            // ignore and continue to 404 below if not found
        }

        if (empty($path) || !Storage::disk('public')->exists(ltrim($path, '/'))) {
            abort(404);
        }

        $full = Storage::disk('public')->path(ltrim($path, '/'));
        return response()->file($full);
    }
}
