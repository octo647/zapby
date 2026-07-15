<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AsaasWebhookController;
use App\Http\Controllers\Auth\ProfessionalAuthController;
use App\Http\Controllers\ProfessionalDashboardController;
use App\Http\Controllers\PublicAgendaController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\WhatsAppController;
use App\Models\Professional;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;

// Landing page
Route::get('/', function () {
    return view('welcome');
})->name('home');


Route::get('/teste-xml', function () {
    return response('<?xml version="1.0"?><ok/>')
        ->header('Content-Type', 'application/xml');
});
Route::get('/teste-bruto', function () {
    return 'ABC';
});
// SEO sitemap
Route::get('/sitemap.xml', function () {
    $urls = [
        [
            'loc' => route('home'),
            'lastmod' => now()->toDateString(),
            'changefreq' => 'weekly',
            'priority' => '1.0',
        ],
    ];

    Professional::active()
        ->whereNotNull('slug')
        ->orderBy('updated_at', 'desc')
        ->get()
        ->each(function (Professional $professional) use (&$urls) {
            $urls[] = [
                'loc' => route('agenda.show', ['professional' => $professional->slug]),
                'lastmod' => optional($professional->updated_at)->toDateString() ?? now()->toDateString(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ];
        });

    $xml = '<?xml version="1.0" encoding="UTF-8"?>';
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

    foreach ($urls as $url) {
        $xml .= '<url>';
        $xml .= '<loc>' . htmlspecialchars($url['loc'], ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</loc>';
        $xml .= '<lastmod>' . $url['lastmod'] . '</lastmod>';
        $xml .= '<changefreq>' . $url['changefreq'] . '</changefreq>';
        $xml .= '<priority>' . $url['priority'] . '</priority>';
        $xml .= '</url>';
    }

    $xml .= '</urlset>';

    return response($xml, 200)
        ->header('Content-Type', 'application/xml; charset=UTF-8')
        ->header('Cache-Control', 'public, max-age=3600, s-maxage=3600');
})->withoutMiddleware([
    EncryptCookies::class,
    AddQueuedCookiesToResponse::class,
    StartSession::class,
    VerifyCsrfToken::class,
    ShareErrorsFromSession::class,
])->name('sitemap');

// Asaas Webhook (without auth middleware)
Route::post('/webhooks/asaas', [AsaasWebhookController::class, 'handle'])->name('webhooks.asaas');

// Professional Auth Routes
Route::prefix('professional')->group(function () {
    // Login
    Route::get('/login', [ProfessionalAuthController::class, 'showLoginForm'])->name('professional.login');
    Route::post('/login', [ProfessionalAuthController::class, 'login']);
    
    // Register
    Route::get('/register', [ProfessionalAuthController::class, 'showRegisterForm'])->name('professional.register');
    Route::post('/register', [ProfessionalAuthController::class, 'register']);
    Route::get('/verify-registration', [ProfessionalAuthController::class, 'showVerifyRegistrationForm'])->name('professional.verify-registration');
    Route::post('/verify-registration', [ProfessionalAuthController::class, 'verifyRegistration']);
    
    // Password Reset
    Route::get('/password/request', [ProfessionalAuthController::class, 'showForgotPasswordForm'])->name('professional.password.request');
    Route::post('/password/send-code', [ProfessionalAuthController::class, 'sendResetCode'])->name('professional.password.send');
    Route::get('/password/reset', [ProfessionalAuthController::class, 'showResetPasswordForm'])->name('professional.password.reset');
    Route::post('/password/reset', [ProfessionalAuthController::class, 'resetPassword']);
    
    Route::post('/logout', [ProfessionalAuthController::class, 'logout'])->name('professional.logout');
    
    // Protected routes (without subscription check - allow access to subscription management)
    Route::middleware(['auth:professional', 'prevent.back'])->group(function () {
        // Subscription routes (accessible even with expired subscription)
        Route::get('/subscription', [SubscriptionController::class, 'index'])->name('professional.subscription');
        Route::post('/subscription/activate', [SubscriptionController::class, 'activate'])->name('professional.subscription.activate');
        Route::post('/subscription/cancel', [SubscriptionController::class, 'cancel'])->name('professional.subscription.cancel');
        Route::get('/subscription/payment/{paymentId}', [SubscriptionController::class, 'payment'])->name('professional.subscription.payment');
    });
    
    // Protected routes (with subscription check)
    Route::middleware(['auth:professional', 'check.subscription', 'prevent.back'])->group(function () {
        Route::get('/dashboard', [ProfessionalDashboardController::class, 'dashboard'])->name('professional.dashboard');
        Route::get('/settings', [ProfessionalDashboardController::class, 'settings'])->name('professional.settings');
        Route::post('/settings', [ProfessionalDashboardController::class, 'updateSettings'])->name('professional.settings.update');
        Route::post('/password', [ProfessionalDashboardController::class, 'updatePassword'])->name('professional.password');
        Route::get('/appointments', [ProfessionalDashboardController::class, 'appointments'])->name('professional.appointments');
        Route::post('/appointments/{id}/cancel', [ProfessionalDashboardController::class, 'cancelAppointment'])->name('professional.appointments.cancel');
        Route::get('/time-blocks', [ProfessionalDashboardController::class, 'timeBlocks'])->name('professional.time-blocks');
        Route::post('/time-blocks', [ProfessionalDashboardController::class, 'storeTimeBlock'])->name('professional.time-blocks.store');
        Route::delete('/time-blocks/{id}', [ProfessionalDashboardController::class, 'destroyTimeBlock'])->name('professional.time-blocks.destroy');
        Route::post('/onboarding/complete', [ProfessionalDashboardController::class, 'completeOnboarding'])->name('professional.onboarding.complete');
    });
});

// Admin Routes
Route::prefix('admin')->middleware(['admin'])->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    });
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/professionals/{professional}', [AdminDashboardController::class, 'show'])->name('admin.professionals.show');
    Route::post('/professionals/{professional}/toggle-admin', [AdminDashboardController::class, 'toggleAdmin'])->name('admin.professionals.toggle-admin');
    Route::post('/professionals/{professional}/update-status', [AdminDashboardController::class, 'updateSubscriptionStatus'])->name('admin.professionals.update-status');
    Route::post('/professionals/{professional}/extend-trial', [AdminDashboardController::class, 'extendTrial'])->name('admin.professionals.extend-trial');
});

// Agenda pública - profissional por slug
Route::get('/{professional:slug}', [PublicAgendaController::class, 'show'])->name('agenda.show');
Route::post('/{professional:slug}/agendar', [PublicAgendaController::class, 'store'])->name('agenda.store');

// Webhook WhatsApp (aqui o profissional gerencia tudo)
Route::post('/webhook/whatsapp', [WhatsAppController::class, 'webhook'])->name('whatsapp.webhook');
