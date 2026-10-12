<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\MarketInsightsController;
use App\Http\Controllers\Web\SuccessStoriesController;
use App\Http\Controllers\Web\TrustComplianceController;
use App\Http\Controllers\Web\DocumentsController as WebDocumentsController;
use App\Http\Controllers\Web\InvestorDashboardController;
use App\Http\Controllers\InvestorEarningsController;
use App\Http\Controllers\Web\AdminDashboardController as WebAdminDashboardController;
use App\Http\Controllers\Web\PagesController as WebPagesController;
use App\Http\Controllers\Web\ChatPageController as WebChatPageController;
use App\Http\Controllers\Web\InvestmentPageController;
use App\Http\Controllers\Web\LicensePageController;
use App\Http\Controllers\Web\PaymentsController;
use App\Http\Controllers\Web\WithdrawalsController;
use App\Http\Controllers\Web\LedgerController;
use App\Http\Controllers\Web\InvestorOnboardingController;
use App\Http\Controllers\Web\InvestorProfileController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Api\LicenseReportsController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application.
|
*/

Route::middleware('investor.auth')->group(function(){
    Route::get('/investor/dashboard',[\App\Http\Controllers\Web\InvestorDashboardController::class,'index'])->name('investor.dashboard');
    Route::get('/investor/investment/{id}', [\App\Http\Controllers\Web\InvestorDashboardController::class, 'show'])->name('investor.investment.detail');
    // License detail page for investor
    Route::get('/investor/license/{id}', [\App\Http\Controllers\Web\InvestorDashboardController::class, 'licenseShow'])->name('investor.license.show');
    // Investor withdrawal submit (uses Query Builder controller)
    Route::post('/investor/withdrawal', [\App\Http\Controllers\Web\InvestorDashboardController::class, 'store'])->name('investor.withdrawal.store');
    // Save investor profile from investment page (AJAX)
    Route::post('/investor/profile/save-from-investment', [InvestorProfileController::class, 'saveFromInvestment'])->name('investor.profile.saveFromInvestment');
    Route::get('/investor/earnings', [InvestorEarningsController::class, 'index'])->name('investor.earnings');
});
Route::get('/investor/investments',[PageController::class,'investorInvestments'])->name('investor.investments');
Route::middleware('investor.auth')->group(function(){
    Route::get('/investor/profile',[PageController::class,'investorProfile'])->name('investor.profile');
    Route::post('/investor/profile/update',[PageController::class,'updateProfile'])->name('investor.profile.update');
});
// Serve investor images via controller (side = front|back)
Route::get('/investor/{investor}/image/{side}', [InvestorProfileController::class, 'serveImage'])->name('investor.image');
// Demo investor dashboard + investment (for previews)
Route::get('/investor/dashboard/demo', [PageController::class, 'investorDashboardDemo'])->name('investor.dashboard.demo');
Route::get('/investor/investment/demo/{id}', [PageController::class, 'investorInvestmentDemo'])->name('investor.investment.demo');

// Public pages

// Debug helper: expose investment transactions as JSON when not in production (convenience only)
if (!app()->environment() || !app()->environment('production')) {
    Route::get('/debug/inv-trans/{id}', function($id){
        return response()->json(
            \Illuminate\Support\Facades\DB::table('investment_transactions')
                ->where('investment_id', $id)
                ->orderByDesc('created_at')
                ->get()
        );
    });
}


        // Public read-only bank settings endpoint used by investor-facing pages
Route::get('/api/bank-settings', [AdminController::class, 'publicBankSettings'])->name('api.bank.settings');
// New Query Builder-based controllers for pages (non-breaking: keep original, add alternates under /web-pages prefix)
Route::get('/', [\App\Http\Controllers\Web\HomeController::class, 'index'])->name('home');
// Small helper route: check current user/session and redirect to either the quick investment flow
// or to the investor area if they already have investments.
Route::get('/investment/check', [\App\Http\Controllers\Web\InvestmentPageController::class, 'checkRedirect'])->name('investment.check');

Route::get('/investment/{type}', [\App\Http\Controllers\Web\InvestmentPageController::class, 'index'])->name('investment.show');
Route::get('/license', [\App\Http\Controllers\Web\LicensePageController::class, 'index'])->name('license');
Route::post('/license', [\App\Http\Controllers\Web\LicensePageController::class, 'store'])->middleware('investor.auth')->name('license.store');
// AJAX owner lookup by national ID (used by license and investment forms)
Route::post('/owner/lookup', [\App\Http\Controllers\Web\OwnerLookupController::class, 'lookup'])->name('owner.lookup');
Route::get('/dashboard', [\App\Http\Controllers\PageController::class, 'dashboard'])->name('dashboard');
Route::get('/investment-levels', [PageController::class, 'investmentLevels'])->name('investment.levels');
Route::get('/small-investment', [PageController::class, 'smallInvestment'])->name('investment.small');
Route::get('/large-investment', [PageController::class, 'largeInvestment'])->name('investment.large');
// Investment confirmation and submission (requires investor login)
Route::middleware('investor.auth')->group(function(){
    Route::get('/investment/confirm', [\App\Http\Controllers\Web\InvestmentPageController::class, 'confirm'])->name('investment.confirm');
    Route::post('/investment/confirm', [\App\Http\Controllers\Web\InvestmentPageController::class, 'submit'])->name('investment.submit');
    Route::post('/investment/pay', [\App\Http\Controllers\Web\InvestmentPageController::class, 'pay'])->name('investment.pay');
    // License payment endpoint (mirror investment pay)
    Route::post('/license/pay', [\App\Http\Controllers\Web\LicensePageController::class, 'pay'])->name('license.pay');
});
Route::get('/documents', [\App\Http\Controllers\Web\DocumentsController::class, 'index'])->name('documents');
// Upload endpoint (public route expects POST to /documents/upload)
Route::post('/documents/upload', [\App\Http\Controllers\Web\DocumentsController::class, 'upload'])->name('documents.upload');
// Submit uploaded documents for review
Route::post('/documents/submit', [\App\Http\Controllers\Web\DocumentsController::class, 'submit'])->name('documents.submit');
Route::get('/market-insights', [\App\Http\Controllers\Web\MarketInsightsController::class, 'index'])->name('market.insights');
Route::get('/success-stories', [\App\Http\Controllers\Web\SuccessStoriesController::class, 'index'])->name('success.stories');
Route::get('/trust-compliance', [\App\Http\Controllers\Web\TrustComplianceController::class, 'index'])->name('trust.compliance');
// Investor onboarding (requires investor authentication: only logged-in investors may open the onboarding)
Route::get('/register-investor', [PageController::class, 'registerInvestor'])->middleware('investor.auth')->name('register.investor');
// Investor onboarding: submit registration -> checkout -> success (requires investor auth)
Route::post('/register-investor', [InvestorOnboardingController::class, 'submit'])->middleware('investor.auth')->name('investor.register.submit');
Route::get('/checkout', [InvestorOnboardingController::class, 'showCheckout'])->name('checkout.show');
Route::post('/checkout/pay', [InvestorOnboardingController::class, 'pay'])->name('checkout.pay');
Route::get('/register-success', [InvestorOnboardingController::class, 'success'])->name('register.success');

// Auth web routes (simple session-based)
Route::get('/auth/register', [AuthController::class, 'showRegisterForm'])->name('auth.register.form');
Route::post('/auth/register', [AuthController::class, 'register'])->name('auth.register');
Route::get('/auth/login', [AuthController::class, 'showLoginForm'])->name('auth.login.form');
Route::post('/auth/login', [AuthController::class, 'login'])->name('auth.login');
Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');

// Verification routes (OTP)
Route::get('/verify', [\App\Http\Controllers\VerifyController::class, 'show'])->name('verify.form');
Route::post('/verify', [\App\Http\Controllers\VerifyController::class, 'verify'])->name('verify.post');
Route::post('/verify/resend', [\App\Http\Controllers\VerifyController::class, 'resend'])->name('verify.resend');

// Google OAuth (optional)
Route::get('/auth/google', [\App\Http\Controllers\GoogleAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [\App\Http\Controllers\GoogleAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// Completion form after OAuth to collect phone/national_id and optional password
Route::get('/auth/complete-profile', [\App\Http\Controllers\CompleteProfileController::class, 'show'])->name('auth.complete_profile');
Route::post('/auth/complete-profile', [\App\Http\Controllers\CompleteProfileController::class, 'submit'])->name('auth.complete_profile.post');

// Fallback for Laravel auth middleware redirect: name('login') is required
// If the intended URL is an investor page, send to the investor login form; otherwise keep admin login
Route::get('/login', function(\Illuminate\Http\Request $request){
    $intended = $request->session()->get('url.intended');
    $path = $intended ? parse_url($intended, PHP_URL_PATH) : null;
    if ($path && strpos($path, '/investor') === 0) {
        return redirect()->route('auth.login.form');
    }
    return redirect()->route('admin.login');
})->name('login');

// Investor-specific login routes removed; use legacy auth login routes instead

// Forgot/verify/reset password flow handled by PasswordResetController
Route::get('/auth/forgot-password', [PasswordResetController::class, 'showForgot'])->name('auth.forgot-password');
Route::post('/auth/forgot-password', [PasswordResetController::class, 'sendForgot'])->name('auth.forgot-password.send');

Route::get('/auth/verify-reset', [PasswordResetController::class, 'showVerify'])->name('auth.verify-reset.form');
Route::post('/auth/verify-reset', [PasswordResetController::class, 'postVerify'])->name('auth.verify-reset.submit');

// Support GET to show the reset form (some links may point to /auth/reset-password)
Route::get('/auth/reset-password', [PasswordResetController::class, 'showNewPassword'])->name('auth.reset-password.form');
Route::get('/auth/reset-password/new', [PasswordResetController::class, 'showNewPassword'])->name('auth.reset-password.new');
Route::post('/auth/reset-password', [PasswordResetController::class, 'postNewPassword'])->name('auth.reset-password.submit');

// Admin routes
Route::get('/admin/login', [AdminController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login.submit');
// Forgot password (simple pages) -- simulated flows
Route::get('/admin/forgot-password', function(){ return view('admin.forgot-password'); })->name('admin.forgot-password');
Route::post('/admin/forgot-password', function(\Illuminate\Http\Request $request){
    // simulate sending email -- in production, hook into mailer and token generation
    $email = $request->input('email');
    return redirect()->route('admin.forgot-password')->with('status', 'تم إرسال تعليمات إعادة التعيين إلى البريد (تمت المحاكاة).');
})->name('admin.forgot-password.send');
Route::get('/admin/dashboard', [\App\Http\Controllers\Web\AdminDashboardController::class, 'index'])->middleware(['web','auth'])->name('admin.dashboard');
Route::get('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');

// Admin payments management (protected like other admin APIs)
Route::middleware(['web','auth'])->prefix('admin')->group(function(){
    Route::get('/payments', [PaymentsController::class, 'index'])->name('admin.payments.index');
    Route::post('/payments', [PaymentsController::class, 'store'])->name('admin.payments.store');
    Route::put('/payments/{id}', [PaymentsController::class, 'update'])->name('admin.payments.update');
    Route::delete('/payments/{id}', [PaymentsController::class, 'destroy'])->name('admin.payments.destroy');

    // Withdrawals UI
    Route::get('/withdrawals', [WithdrawalsController::class, 'index'])->name('admin.withdrawals.index');
    Route::put('/withdrawals/{id}', [WithdrawalsController::class, 'update'])->name('admin.withdrawals.update');
    Route::post('/withdrawals/{id}/pay', [WithdrawalsController::class, 'pay'])->name('admin.withdrawals.pay');

    // Ledger UI
    Route::get('/ledger', [LedgerController::class, 'index'])->name('admin.ledger.index');

    // Licenses UI
    Route::get('/licenses', [\App\Http\Controllers\Web\LicensesController::class, 'index'])->name('admin.licenses.index');
    Route::put('/licenses/{id}', [\App\Http\Controllers\Web\LicensesController::class, 'update'])->name('admin.licenses.update');
});

// Static pages: Terms & Privacy
Route::view('/terms', 'pages.terms')->name('pages.terms');
Route::view('/privacy', 'pages.privacy')->name('pages.privacy');

// Chat page (public for now; can be restricted later)
Route::get('/chat', [\App\Http\Controllers\Web\ChatPageController::class, 'index'])->name('chat');
Route::post('/chat', [\App\Http\Controllers\Web\ChatPageController::class, 'store'])->name('chat.send');

// Alternate Query Builder-driven endpoints (optional, under /web-pages to avoid breaking existing routes)
Route::prefix('web-pages')->group(function(){
    Route::get('/', [HomeController::class, 'index'])->name('web.home');
    Route::resource('market-insights', MarketInsightsController::class)->only(['index']);
    Route::resource('success-stories', SuccessStoriesController::class)->only(['index']);
    Route::resource('trust-compliance', TrustComplianceController::class)->only(['index']);
    Route::resource('documents', WebDocumentsController::class)->only(['index','store','update','destroy']);

// Upload endpoint for storing document files (multipart/form-data)
// Removed duplicate absolute '/documents/upload' inside the 'web-pages' group to avoid route name conflict
    Route::resource('investor', InvestorDashboardController::class)->only(['index','show','store','update','destroy']);
    Route::resource('admin-dashboard', WebAdminDashboardController::class)->only(['index']);
    Route::resource('pages', WebPagesController::class)->only([]);
    Route::resource('chat', WebChatPageController::class)->only(['index','store','update','destroy']);
    Route::get('investment/{type?}', [InvestmentPageController::class, 'index'])->name('web.investment');
    Route::resource('license-page', LicensePageController::class)->only(['index']);
});

// Fallback: ensure License Reports endpoint is available at /api/admin/license-reports (web middleware)
Route::get('/api/admin/license-reports', [LicenseReportsController::class, 'index']);

// Admin API (session-auth)
Route::middleware(['web', 'auth'])->group(function(){
    Route::get('/api/stats', [AdminController::class, 'stats'])->name('admin.api.stats');
    Route::get('/api/users', [AdminController::class, 'users'])->name('admin.api.users');
    Route::get('/api/investments', [AdminController::class, 'investments'])->name('admin.api.investments');
    Route::get('/api/business-licenses', [AdminController::class, 'licenses'])->name('admin.api.licenses');

    Route::put('/api/admin/investments/{id}/status', [AdminController::class, 'updateInvestmentStatus'])->name('admin.api.investments.updateStatus');
    Route::put('/api/admin/licenses/{id}/status', [AdminController::class, 'updateLicenseStatus'])->name('admin.api.licenses.updateStatus');

    // Settings / Packages / Logs / Backups
    Route::get('/api/admin/settings', [AdminController::class, 'settings'])->name('admin.api.settings.get');
    Route::put('/api/admin/settings', [AdminController::class, 'updateSettings'])->name('admin.api.settings.update');
    // Dedicated contact info update endpoint (creates/updates contact_infos table)
    Route::put('/api/admin/contact-info', [AdminController::class, 'updateContactInfo'])->name('admin.api.contact.update');

    // Dedicated platform settings (DB-backed single row)
    Route::get('/api/admin/platform-settings', [\App\Http\Controllers\PlatformSettingController::class, 'show'])->name('admin.api.platform.get');
    Route::put('/api/admin/platform-settings', [\App\Http\Controllers\PlatformSettingController::class, 'update'])->name('admin.api.platform.update');

    // Bank accounts management (admin)
    Route::get('/api/admin/bank-accounts', [AdminController::class, 'listBankAccounts'])->name('admin.api.bank.list');
    Route::post('/api/admin/bank-accounts', [AdminController::class, 'createBankAccount'])->name('admin.api.bank.create');
    Route::put('/api/admin/bank-accounts/{id}', [AdminController::class, 'updateBankAccount'])->name('admin.api.bank.update');
    Route::delete('/api/admin/bank-accounts/{id}', [AdminController::class, 'deleteBankAccount'])->name('admin.api.bank.delete');

    Route::get('/api/admin/packages', [AdminController::class, 'packages'])->name('admin.api.packages');
    // Comprehensive packages CRUD
    Route::get('/api/admin/investment-packages', [AdminController::class, 'listPackages']);
    Route::post('/api/admin/investment-packages', [AdminController::class, 'createPackage']);
    Route::put('/api/admin/investment-packages/{id}', [AdminController::class, 'updatePackage']);
    Route::delete('/api/admin/investment-packages/{id}', [AdminController::class, 'deletePackage']);
    Route::get('/api/admin/logs', [AdminController::class, 'logs'])->name('admin.api.logs');
    Route::get('/api/admin/analytics', [AdminController::class, 'analytics']);
    // Investments diagnostics: return investments that appear to be missing scheduled earnings
    Route::get('/api/admin/problematic-investments', [AdminController::class, 'problematicInvestments'])->name('admin.api.investments.problematic');

    // Reports API (aggregated analytics for admin reports tab)
    Route::get('/api/admin/reports', [\App\Http\Controllers\Api\ReportsController::class, 'index']);

    Route::get('/api/admin/backups', [AdminController::class, 'backups'])->name('admin.api.backups');
    Route::post('/api/admin/backups/run', [AdminController::class, 'runBackup'])->name('admin.api.backups.run');
    Route::get('/api/admin/backups/download/{file}', [AdminController::class, 'downloadBackup'])->where('file', '.*');
    Route::post('/api/admin/backups/restore', [AdminController::class, 'restoreBackup'])->name('admin.api.backups.restore');

    // Investors admin API (DB-backed list + detail)
    Route::get('/api/admin/investors', [AdminController::class, 'investors'])->name('admin.api.investors');
    Route::get('/api/admin/investors/{id}', [AdminController::class, 'investorDetail'])->name('admin.api.investors.detail');

    // Admin messages: allow admin to send quick message/notification to a specific user
    Route::post('/admin/messages/send', [\App\Http\Controllers\Admin\MessageController::class, 'send'])->name('admin.messages.send');
    Route::get('/admin/messages/conversations', [\App\Http\Controllers\Admin\MessageController::class, 'conversations'])->name('admin.messages.conversations');
    Route::get('/admin/messages/conversations/{id}/messages', [\App\Http\Controllers\Admin\MessageController::class, 'messages'])->name('admin.messages.messages');
    Route::post('/admin/messages/conversations/{id}/mark-read', [\App\Http\Controllers\Admin\MessageController::class, 'markRead'])->name('admin.messages.markread');

    // Admin web search route (AJAX returns Blade partial)
    Route::get('/admin/search/investors', [\App\Http\Controllers\Admin\InvestorController::class, 'search'])->name('admin.search.investors');
    // Admin investor detail page
    Route::get('/admin/investors/{id}', [\App\Http\Controllers\Admin\InvestorController::class, 'show'])->name('admin.investors.show');

    // Generic admin search endpoints returning Blade partials
    Route::get('/admin/search/users', [\App\Http\Controllers\Admin\SearchController::class, 'users'])->name('admin.search.users');
    Route::get('/admin/search/investments', [\App\Http\Controllers\Admin\SearchController::class, 'investments'])->name('admin.search.investments');
    Route::get('/admin/search/licenses', [\App\Http\Controllers\Admin\SearchController::class, 'licenses'])->name('admin.search.licenses');
    Route::get('/admin/search/packages', [\App\Http\Controllers\Admin\SearchController::class, 'packages'])->name('admin.search.packages');
    Route::get('/admin/search/documents', [\App\Http\Controllers\Admin\SearchController::class, 'documents'])->name('admin.search.documents');
    Route::get('/admin/search/banks', [\App\Http\Controllers\Admin\SearchController::class, 'banks'])->name('admin.search.banks');
    // Generic admin search partials
    Route::get('/admin/search/users', [\App\Http\Controllers\Admin\SearchController::class, 'users'])->name('admin.search.users');
    Route::get('/admin/search/investments', [\App\Http\Controllers\Admin\SearchController::class, 'investments'])->name('admin.search.investments');
    Route::get('/admin/search/licenses', [\App\Http\Controllers\Admin\SearchController::class, 'licenses'])->name('admin.search.licenses');
    Route::get('/admin/search/packages', [\App\Http\Controllers\Admin\SearchController::class, 'packages'])->name('admin.search.packages');
    Route::get('/admin/search/documents', [\App\Http\Controllers\Admin\SearchController::class, 'documents'])->name('admin.search.documents');
    Route::get('/admin/search/bank-accounts', [\App\Http\Controllers\Admin\SearchController::class, 'bankAccounts'])->name('admin.search.bank_accounts');

    // Investment details (for admin dashboard modal)
    Route::get('/api/admin/investments/{id}', [AdminController::class, 'investmentDetail'])->name('admin.api.investments.detail');
    // Send payment reminder to investor
    Route::post('/api/admin/investments/{id}/notify-payment', [AdminController::class, 'notifyInvestorPayment'])->name('admin.api.investments.notifyPayment');
    // Register investment rejection as a reminder (visible to investor)
    Route::post('/api/admin/investments/{id}/reject', [AdminController::class, 'rejectInvestment'])->name('admin.api.investments.reject');

    // License detail and notify endpoints (mirror investments)
    Route::get('/api/admin/licenses/{id}', [AdminController::class, 'licenseDetail'])->name('admin.api.licenses.detail');
    Route::post('/api/admin/licenses/{id}/notify-payment', [AdminController::class, 'notifyLicensePayment'])->name('admin.api.licenses.notifyPayment');
    // Notify license owner about rejection (POST)
    Route::post('/api/admin/licenses/{id}/notify-rejection', [AdminController::class, 'notifyLicenseRejection'])->name('admin.api.licenses.notifyRejection');

    // User status update
    Route::put('/api/admin/users/{id}/status', [AdminController::class, 'updateUserStatus']);

    // Navigation CRUD
    Route::get('/api/admin/navigation', [AdminController::class, 'navIndex']);
    Route::post('/api/admin/navigation', [AdminController::class, 'navStore']);
    Route::put('/api/admin/navigation/{id}', [AdminController::class, 'navUpdate']);
    Route::delete('/api/admin/navigation/{id}', [AdminController::class, 'navDestroy']);

    // Success Stories admin CRUD (DB-backed)
    Route::get('/api/admin/success-stories', [AdminController::class, 'successStoriesIndex']);
    Route::post('/api/admin/success-stories', [AdminController::class, 'successStoriesCreate']);
    Route::put('/api/admin/success-stories/{id}', [AdminController::class, 'successStoriesUpdate']);
    Route::delete('/api/admin/success-stories/{id}', [AdminController::class, 'successStoriesDelete']);

    // Market Insights admin API (DB-backed): list and bulk update
    Route::get('/api/admin/market-insights', [AdminController::class, 'marketInsightsIndex']);
    Route::put('/api/admin/market-insights', [AdminController::class, 'marketInsightsBulkUpdate']);
    Route::post('/api/admin/market-insights/{type}', [AdminController::class, 'marketInsightsCreate']);
    Route::put('/api/admin/market-insights/{type}/{id}', [AdminController::class, 'marketInsightsUpdate']);
    Route::delete('/api/admin/market-insights/{type}/{id}', [AdminController::class, 'marketInsightsDelete']);

    // Trust & Compliance admin CRUD (tc_* tables + metrics)
    Route::get('/api/admin/tc/regulations', [AdminController::class, 'tcRegulationsIndex']);
    Route::post('/api/admin/tc/regulations', [AdminController::class, 'tcRegulationsCreate']);
    Route::put('/api/admin/tc/regulations/{id}', [AdminController::class, 'tcRegulationsUpdate']);
    Route::delete('/api/admin/tc/regulations/{id}', [AdminController::class, 'tcRegulationsDelete']);

    Route::get('/api/admin/tc/security-features', [AdminController::class, 'tcSecurityIndex']);
    Route::post('/api/admin/tc/security-features', [AdminController::class, 'tcSecurityCreate']);
    Route::put('/api/admin/tc/security-features/{id}', [AdminController::class, 'tcSecurityUpdate']);
    Route::delete('/api/admin/tc/security-features/{id}', [AdminController::class, 'tcSecurityDelete']);

    Route::get('/api/admin/tc/risk-levels', [AdminController::class, 'tcRiskLevelsIndex']);
    Route::post('/api/admin/tc/risk-levels', [AdminController::class, 'tcRiskLevelsCreate']);
    Route::put('/api/admin/tc/risk-levels/{id}', [AdminController::class, 'tcRiskLevelsUpdate']);
    Route::delete('/api/admin/tc/risk-levels/{id}', [AdminController::class, 'tcRiskLevelsDelete']);

    Route::get('/api/admin/tc/certificates', [AdminController::class, 'tcCertificatesIndex']);
    Route::post('/api/admin/tc/certificates', [AdminController::class, 'tcCertificatesCreate']);
    Route::put('/api/admin/tc/certificates/{id}', [AdminController::class, 'tcCertificatesUpdate']);
    Route::delete('/api/admin/tc/certificates/{id}', [AdminController::class, 'tcCertificatesDelete']);

    Route::get('/api/admin/tc/audit-reports', [AdminController::class, 'tcAuditsIndex']);
    Route::post('/api/admin/tc/audit-reports', [AdminController::class, 'tcAuditsCreate']);
    Route::put('/api/admin/tc/audit-reports/{id}', [AdminController::class, 'tcAuditsUpdate']);
    Route::delete('/api/admin/tc/audit-reports/{id}', [AdminController::class, 'tcAuditsDelete']);

    // Metrics (indicator cards) removed
    Route::get('/api/admin/tc/metrics', [TrustComplianceController::class, 'metrics']);
    // Admin CRUD for metrics (persist indicator cards)
    Route::post('/api/admin/tc/metrics', [AdminController::class, 'tcMetricsCreate']);
    Route::put('/api/admin/tc/metrics/{id}', [AdminController::class, 'tcMetricsUpdate']);
    Route::delete('/api/admin/tc/metrics/{id}', [AdminController::class, 'tcMetricsDelete']);

    // Permissions APIs
    Route::get('/api/admin/me/permissions', [AdminController::class, 'myPermissions']);
    Route::get('/api/admin/permissions', [AdminController::class, 'listPermissions']);
    Route::get('/api/admin/roles/{role}/permissions', [AdminController::class, 'getRolePermissions']);
    Route::put('/api/admin/roles/{role}/permissions', [AdminController::class, 'setRolePermissions']);

    // Documents APIs
    Route::get('/api/admin/documents', [AdminController::class, 'documents']);
    Route::delete('/api/admin/documents/{id}', [AdminController::class, 'deleteDocument']);

    // Users CRUD
    Route::post('/api/admin/users', [AdminController::class, 'createUser']);
    Route::put('/api/admin/users/{id}', [AdminController::class, 'updateUser']);
    Route::delete('/api/admin/users/{id}', [AdminController::class, 'deleteUser']);

    // User-specific permissions
    Route::get('/api/admin/users/{id}/permissions', [AdminController::class, 'getUserPermissions']);
    Route::put('/api/admin/users/{id}/permissions', [AdminController::class, 'setUserPermissions']);

    // Withdrawals management
    Route::get('/api/admin/withdrawals', [AdminController::class, 'withdrawals'])->name('admin.api.withdrawals');
    Route::put('/api/admin/withdrawals/{id}', [AdminController::class, 'updateWithdrawalStatus'])->name('admin.api.withdrawals.update');
    Route::post('/api/admin/withdrawals/{id}/pay', [AdminController::class, 'payWithdrawal'])->name('admin.api.withdrawals.pay');
});



// Legacy placeholder removed; real views are above

// Development-only helper: fetch verification code for an email (only allowed on local/debug and from localhost)
if (config('app.debug') || app()->environment('local')) {
    Route::get('/_dev/verification-code', function (\Illuminate\Http\Request $request) {
        // only allow localhost requests
        $ip = $request->ip();
        if (!in_array($ip, ['127.0.0.1', '::1', 'localhost'])) {
            abort(403, 'Forbidden');
        }
        $email = $request->query('email');
        if (!$email) {
            return response()->json(['error' => 'email query parameter required'], 400);
        }
        $user = \App\Models\User::where('email', $email)->first();
        if (!$user) return response()->json(['code' => null]);
        return response()->json(['code' => $user->verification_code, 'sent_at' => $user->verification_token]);
    });
}
