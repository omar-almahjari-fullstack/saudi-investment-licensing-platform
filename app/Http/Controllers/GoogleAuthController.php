<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class GoogleAuthController extends Controller
{
	public function redirectToGoogle()
	{
		// Build a callback URL that matches the current request host/port so Google returns to the same host
		$redirect = request()->getSchemeAndHttpHost() . '/auth/google/callback';
		// Temporarily override the configured redirect so Socialite sends the correct callback URL
		config(['services.google.redirect' => $redirect]);
		return Socialite::driver('google')->redirect();
	}

	public function handleGoogleCallback(Request $request)
	{
		try{
			// Ensure we use the same redirect URL here as we used when redirecting
			$redirect = request()->getSchemeAndHttpHost() . '/auth/google/callback';
			// Ensure Socialite uses the same redirect when exchanging the code
			config(['services.google.redirect' => $redirect]);
			$gUser = Socialite::driver('google')->stateless()->user();
		} catch (\Throwable $e){
			report($e);
			return redirect()->route('auth.login.form')->withErrors(['email' => 'فشل تسجيل الدخول عبر Google']);
		}

		$user = User::where('email', $gUser->getEmail())->first();
		if(!$user){
			// create verified user
			$uuid = (string) \Illuminate\Support\Str::uuid();
			// For Google sign-ins: store the OAuth-provided display name in `username` (DB-only),
			// and intentionally leave `full_name` empty so it can be provided later by the user.
			$user = User::create([
				'id' => $uuid,
				// some DB schemas disallow NULL for full_name; use empty string to avoid constraint errors
				'full_name' => '',
				'username' => $gUser->getName() ?? $gUser->getNickname() ?? 'google_user',
				'email' => $gUser->getEmail(),
				'password' => null,
				'role' => 'investor',
				'is_active' => true,
				'email_verified_at' => now(),
			]);
		} else {
			// ensure verified
			if(is_null($user->email_verified_at)){
				$user->email_verified_at = now();
				$user->save();
			}
		}

		// login via session
		session()->invalidate();
		session()->regenerateToken();
		session(['user_id' => $user->id]);

		// store oauth-provided info in session (pre-fill for completion form)
		// Keep OAuth-provided values in session for the completion UI, but do not
		// write the OAuth display name into the public `full_name` column.
		session(['oauth_user' => [
			'username' => $gUser->getName() ?? $gUser->getNickname() ?? null,
			'email' => $gUser->getEmail(),
			'avatar' => $gUser->getAvatar() ?? null,
		]]);

		// redirect to profile completion page where user supplies phone/national id and optional password
		return redirect()->route('auth.complete_profile');
	}
}

