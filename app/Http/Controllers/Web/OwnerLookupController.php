<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OwnerLookupController extends Controller
{
    /**
     * Lookup an owner by national_id (AJAX)
     * Returns JSON: { found: bool, locked: bool, owner: { full_name, email, phone, national_id } }
     * locked = true when the national_id is already used in a previous license OR investment (first submission locks it)
     */
    public function lookup(Request $request)
    {
        $nid = trim((string) $request->input('national_id'));
        if ($nid === '') {
            return response()->json(['found' => false]);
        }

        // Look up user by national_id (users is source of truth for national_id)
        $user = DB::table('users')->where('national_id', $nid)->first();
        $investor = null;
        if ($user) {
            $investor = DB::table('investors')->where('user_id', $user->id)->first();
        } else {
            // fallback: there might be legacy investors with national_id still set
            $investor = DB::table('investors')->where('national_id', $nid)->first();
            if ($investor) {
                $user = DB::table('users')->where('id', $investor->user_id)->first();
            }
        }

        $owner = null;
        if ($user) {
            $owner = [
                'full_name' => $user->full_name ?? ($investor->full_name ?? null),
                'email' => $user->email ?? ($investor->email ?? null),
                'phone' => $user->phone ?? ($investor->phone ?? null),
                'national_id' => $user->national_id ?? ($investor->national_id ?? null),
            ];
        } elseif ($investor) {
            $owner = [
                'full_name' => $investor->full_name ?? null,
                'email' => $investor->email ?? null,
                'phone' => $investor->phone ?? null,
                'national_id' => $investor->national_id ?? null,
            ];
        }

        // Determine if this national_id is already used in a submitted license or investment
        $usedInLicense = DB::table('licenses')->where('national_id', $nid)->exists();
        $usedInInvestment = DB::table('investments')->where('national_id', $nid)->exists();
        $locked = $usedInLicense || $usedInInvestment || ($owner !== null && ($owner['national_id'] ?? null) !== null);

        return response()->json(['found' => (bool) $owner, 'locked' => (bool) $locked, 'owner' => $owner]);
    }
}
