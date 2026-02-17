<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardAnalyticsController;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    
    // Role-based Home Redirector
    Route::get('/home', function () {
        if (auth()->user()->role === 'admin') {
            return redirect()->route('dashboard');
        }
        return redirect()->route('client.portal');
    })->name('home');

    // --- Admin Only Routes ---
    Route::middleware([\App\Http\Middleware\AdminMiddleware::class])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('/dashboard', [DashboardController::class, 'store'])->name('dashboard.store');
        Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions');
        Route::post('/transactions/{id}/stop', [TransactionController::class, 'stopSession'])->name('transactions.stop');
        Route::get('/transactions/{id}/invoice', [TransactionController::class, 'downloadInvoice'])->name('transactions.invoice');
        
        // Analytics API
        Route::get('/api/admin/analytics', [DashboardAnalyticsController::class, 'index'])->name('admin.analytics');
        
        Route::get('/analytics', function () {
            $analytics = app(\App\Http\Controllers\DashboardAnalyticsController::class)->index()->getData(true);
            return Inertia::render('Analytics', [
                'analytics' => $analytics
            ]);
        })->name('analytics');

        // User Management
        Route::get('/users', [\App\Http\Controllers\UserController::class, 'index'])->name('users');
        Route::post('/users', [\App\Http\Controllers\UserController::class, 'store'])->name('users.store');
        Route::delete('/users/{id}', [\App\Http\Controllers\UserController::class, 'destroy'])->name('users.destroy');

        // Admin Billing
        Route::get('/admin/billing', [\App\Http\Controllers\BillingController::class, 'index'])->name('admin.billing');
        Route::post('/admin/billing/generate', [\App\Http\Controllers\BillingController::class, 'generateReport'])->name('admin.billing.generate');
    });

    // --- Client Only Routes ---
    Route::middleware([\App\Http\Middleware\ClientMiddleware::class])->group(function () {
        Route::get('/client/portal', function () {
            return Inertia::render('ClientPortal');
        })->name('client.portal');

        Route::get('/api/client/stations', [\App\Http\Controllers\ClientApiController::class, 'stations'])->name('api.client.stations');
        Route::post('/api/client/stations/favorite', [\App\Http\Controllers\ClientApiController::class, 'toggleFavorite'])->name('api.client.stations.favorite');
        Route::get('/api/client/sessions', [\App\Http\Controllers\ClientApiController::class, 'sessions'])->name('api.client.sessions');
        Route::post('/api/client/sessions', [\App\Http\Controllers\ClientApiController::class, 'startSession'])->name('api.client.sessions.start');
        Route::post('/api/client/sessions/feedback', [\App\Http\Controllers\ClientApiController::class, 'submitFeedback'])->name('api.client.sessions.feedback');
        Route::post('/api/client/sessions/{id}/stop', [\App\Http\Controllers\ClientApiController::class, 'stopSession'])->name('api.client.sessions.stop');
        Route::get('/api/client/sessions/{id}/invoice', [\App\Http\Controllers\ClientApiController::class, 'downloadInvoice'])->name('api.client.sessions.invoice');

        Route::get('/api/client/wallet', [\App\Http\Controllers\ClientApiController::class, 'wallet'])->name('api.client.wallet');
        Route::post('/api/client/wallet/top-up', [\App\Http\Controllers\ClientApiController::class, 'topUpWallet'])->name('api.client.wallet.topup');

        Route::get('/api/client/vehicles', [\App\Http\Controllers\ClientApiController::class, 'vehicles'])->name('api.client.vehicles');
        Route::post('/api/client/vehicles', [\App\Http\Controllers\ClientApiController::class, 'storeVehicle'])->name('api.client.vehicles.store');
        Route::post('/api/client/vehicles/{id}/default', [\App\Http\Controllers\ClientApiController::class, 'setDefaultVehicle'])->name('api.client.vehicles.default');
        Route::delete('/api/client/vehicles/{id}', [\App\Http\Controllers\ClientApiController::class, 'destroyVehicle'])->name('api.client.vehicles.destroy');

        Route::post('/api/client/reservations', [\App\Http\Controllers\ClientApiController::class, 'createReservation'])->name('api.client.reservations');

        Route::get('/api/client/notifications', [\App\Http\Controllers\ClientApiController::class, 'notifications'])->name('api.client.notifications');
        Route::post('/api/client/notifications/read', [\App\Http\Controllers\ClientApiController::class, 'readNotifications'])->name('api.client.notifications.read');

        // Billing & Subscriptions
        Route::get('/api/client/billing/summary', [\App\Http\Controllers\ClientApiController::class, 'billingSummary'])->name('api.client.billing.summary');
        Route::post('/api/client/billing/subscribe', [\App\Http\Controllers\ClientApiController::class, 'subscribe'])->name('api.client.billing.subscribe');
        Route::post('/api/client/billing/promo', [\App\Http\Controllers\ClientApiController::class, 'applyPromo'])->name('api.client.billing.promo');
    });

    // --- Shared Routes ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
