<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ProfileController;
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
        
        Route::get('/analytics', function () {
            // Sample data for analytics (Admin only)
            return Inertia::render('Analytics', [
                'analytics' => [
                    'totalRevenue' => 3890,
                    'avgUtilization' => 65,
                    'totalEnergy' => 1634,
                    'revenuePerStation' => ['labels' => ['CS-001', 'CS-002'], 'data' => [1200, 800]],
                    'utilizationData' => ['labels' => ['High', 'Low'], 'data' => [4, 1]],
                    'energyConsumption' => ['labels' => ['Mon', 'Tue'], 'data' => [150, 200]],
                    'peakUsage' => ['labels' => ['08:00', '16:00'], 'data' => [20, 35]],
                    'profitability' => [['name' => 'Site A', 'revenue' => 1000, 'sessions' => 20, 'energy' => 300, 'margin' => 30]]
                ]
            ]);
        })->name('analytics');
    });

    // --- Client Only Routes ---
    Route::get('/client/portal', function () {
        return Inertia::render('ClientPortal');
    })->name('client.portal');

    Route::get('/api/client/sessions', [\App\Http\Controllers\ClientApiController::class, 'sessions'])->name('api.client.sessions');
    Route::post('/api/client/sessions', [\App\Http\Controllers\ClientApiController::class, 'startSession'])->name('api.client.sessions.start');
    Route::post('/api/client/sessions/{id}/stop', [\App\Http\Controllers\ClientApiController::class, 'stopSession'])->name('api.client.sessions.stop');
    Route::get('/api/client/sessions/{id}/invoice', [\App\Http\Controllers\ClientApiController::class, 'downloadInvoice'])->name('api.client.sessions.invoice');

    Route::get('/api/client/wallet', [\App\Http\Controllers\ClientApiController::class, 'wallet'])->name('api.client.wallet');
    Route::post('/api/client/wallet/top-up', [\App\Http\Controllers\ClientApiController::class, 'topUpWallet'])->name('api.client.wallet.topup');

    Route::get('/api/client/vehicles', [\App\Http\Controllers\ClientApiController::class, 'vehicles'])->name('api.client.vehicles');
    Route::post('/api/client/vehicles', [\App\Http\Controllers\ClientApiController::class, 'storeVehicle'])->name('api.client.vehicles.store');
    Route::post('/api/client/vehicles/{id}/default', [\App\Http\Controllers\ClientApiController::class, 'setDefaultVehicle'])->name('api.client.vehicles.default');
    Route::delete('/api/client/vehicles/{id}', [\App\Http\Controllers\ClientApiController::class, 'destroyVehicle'])->name('api.client.vehicles.destroy');

    Route::post('/api/client/reservations', [\App\Http\Controllers\ClientApiController::class, 'createReservation'])->name('api.client.reservations');

    // --- Shared Routes ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
