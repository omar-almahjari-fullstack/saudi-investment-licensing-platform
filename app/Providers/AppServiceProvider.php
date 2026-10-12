<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Login;
use App\Models\User;
use App\Models\Investment;
use App\Models\PlatformSetting;
use App\Models\Setting;
use App\Models\ContactInfo;
use Illuminate\Support\Facades\View;
use App\Observers\InvestmentObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register observer for investments to create earnings on approval
        Investment::observe(InvestmentObserver::class);

        // Ensure per-user public storage folder exists when a user registers or logs in.
        // Folder path: storage/app/public/users/{username_slug} (falls back to id when username missing)
        $createFolder = function (User $user) {
            $slug = null;
            if (! empty($user->username)) {
                // slugify username to safe folder name
                $slug = Str::slug($user->username);
            }
            if (empty($slug)) { $slug = $user->id; }

            $dir = 'users/' . $slug;
            try {
                if (! Storage::disk('public')->exists($dir)) {
                    Storage::disk('public')->makeDirectory($dir);
                    // create a .gitkeep so directory is not empty
                    Storage::disk('public')->put($dir . '/.gitkeep', "");
                }
            } catch (\Throwable $e) {
                // don't break boot if storage disk not writable; just log to laravel log
                logger()->warning('Could not create user storage folder', ['user_id' => $user->id, 'dir' => $dir, 'error' => $e->getMessage()]);
            }
        };

        // On registration create folder
        Event::listen(Registered::class, function (Registered $event) use ($createFolder) {
            if ($event->user instanceof User) { $createFolder($event->user); }
        });

        // On login create folder (some logins may be via guard that provides user model)
        Event::listen(Login::class, function (Login $event) use ($createFolder) {
            $user = $event->user ?? null;
            if ($user instanceof User) { $createFolder($user); }
        });
        
        // Load platform settings (DB-backed) and expose to views and config
        try {
            $platform = PlatformSetting::orderByDesc('id')->first();
            $platformArr = $platform ? $platform->toArray() : [];

            // Load admin_settings (legacy) to support per-page overrides under 'pages'
            try {
                $s = Setting::where('key', 'admin_settings')->first();
                if ($s && $s->value) {
                    $adminSettings = json_decode($s->value, true) ?: [];
                    $pages = $adminSettings['pages'] ?? [];
                    // If there's a home page platform override, merge it
                    if (!empty($pages['home']['platform']) && is_array($pages['home']['platform'])) {
                        $platformArr = array_merge($platformArr, $pages['home']['platform']);
                    }
                    // Admin dashboard overrides
                    if (!empty($pages['admin']['platform']) && is_array($pages['admin']['platform'])) {
                        // If current request is admin area, prefer admin override
                        if (request()->is('admin') || request()->is('admin/*')) {
                            $platformArr = array_merge($platformArr, $pages['admin']['platform']);
                        }
                    }
                    // Navbar-only override: keep separate so navbar changes don't affect other areas
                    $navbarOverride = [];
                    if (!empty($pages['navbar']['platform']) && is_array($pages['navbar']['platform'])) {
                        $navbarOverride = $pages['navbar']['platform'];
                    }
                    // Title-only override: used only for HTML <title> and similar page title usage
                    $titleOverride = [];
                    if (!empty($pages['title']['platform']) && is_array($pages['title']['platform'])) {
                        $titleOverride = $pages['title']['platform'];
                    }
                }
            } catch (\Throwable $e) {
                // ignore admin settings read errors
            }

            if ($platformArr) {
                if (! empty($platformArr['name'])) {
                    config(['app.name' => $platformArr['name']]);
                }
                if (! empty($platformArr['locale'])) {
                    app()->setLocale($platformArr['locale']);
                }
            }

            // Build a navbar-specific platform array by merging the DB row (base) with navbar override
            try {
                $basePlatform = $platform ? $platform->toArray() : [];
                $platform_navbar = $basePlatform;
                if (!empty($navbarOverride) && is_array($navbarOverride)) {
                    $platform_navbar = array_merge($platform_navbar, $navbarOverride);
                }
                // Build a title-specific platform array by merging DB row with title override
                $platform_title = $basePlatform;
                if (!empty($titleOverride) && is_array($titleOverride)) {
                    $platform_title = array_merge($platform_title, $titleOverride);
                }
            } catch (\Throwable $e) {
                $platform_navbar = $platformArr ?: [];
                $platform_title = $platformArr ?: [];
            }

            // Share merged platform data with views
            View::share('platform_setting', $platformArr ?: []);
            View::share('platform_navbar_setting', $platform_navbar ?: []);
            View::share('platform_title_setting', $platform_title ?: []);
            // Build and share site_contact: prioritize dedicated ContactInfo table, fallback to admin_settings.contact, then env/config
            try {
                $contactRow = ContactInfo::orderByDesc('id')->first();
                $adminContact = [];
                if (!empty($s) && !empty($s->value)) {
                    $v = json_decode($s->value, true) ?: [];
                    if (!empty($v['contact']) && is_array($v['contact'])) {
                        $adminContact = $v['contact'];
                    }
                }

                $site_contact = [
                    'email' => $contactRow && $contactRow->email ? $contactRow->email : ($adminContact['email'] ?? config('app.contact.email', '')),
                    'phone' => $contactRow && $contactRow->phone ? $contactRow->phone : ($adminContact['phone'] ?? config('app.contact.phone', '')),
                    'whatsapp' => $contactRow && $contactRow->whatsapp ? $contactRow->whatsapp : ($adminContact['whatsapp'] ?? ($adminContact['phone'] ?? '')),
                    'address' => $contactRow && $contactRow->address ? $contactRow->address : ($adminContact['address'] ?? ''),
                ];

                View::share('site_contact', $site_contact);
            } catch (\Throwable $e) {
                // silently continue if contact info can't be loaded
            }
        } catch (\Throwable $e) {
            logger()->debug('PlatformSetting load skipped: ' . $e->getMessage());
        }
    }
}
