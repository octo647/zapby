<?php

use App\Http\Controllers\AsaasWebhookController;
use App\Http\Controllers\Auth\ProfessionalAuthController;
use App\Http\Controllers\ProfessionalDashboardController;
use App\Http\Controllers\PublicAgendaController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\WhatsAppController;
use Illuminate\Support\Facades\Route;

// Landing page
Route::get('/', function () {
    return view('welcome');
})->name('home');

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
    });
});

// Agenda pública - profissional por slug
Route::get('/{professional:slug}', [PublicAgendaController::class, 'show'])->name('agenda.show');
Route::post('/{professional:slug}/agendar', [PublicAgendaController::class, 'store'])->name('agenda.store');

// Webhook WhatsApp (aqui o profissional gerencia tudo)
Route::post('/webhook/whatsapp', [WhatsAppController::class, 'webhook'])->name('whatsapp.webhook');

