<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureInvestorAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        $sessionUserId = $request->session()->get('user_id');
        if (!$user && !$sessionUserId) {
            // redirect to legacy/general auth login form
            return redirect()->route('auth.login.form');
        }
        // If session-based investor present, ensure it exists and is an investor
        if ($sessionUserId) {
            $record = \Illuminate\Support\Facades\DB::table('users')->where('id', $sessionUserId)->first();
            if (!$record || ($record->role ?? 'investor') !== 'investor') {
                $request->session()->forget('user_id');
                return redirect()->route('auth.login.form');
            }
            return $next($request);
        }

        // If using Laravel Auth, ensure role is investor explicitly (do not allow admin/staff)
        if ($user && isset($user->role) && $user->role === 'investor') {
            return $next($request);
        }

    return redirect()->route('auth.login.form');
    }
}
