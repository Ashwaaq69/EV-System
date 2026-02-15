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

    // --- Shared Routes ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
