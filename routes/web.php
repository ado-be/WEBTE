<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserActivityController;
use App\Http\Controllers\TokenController;
use App\Http\Middleware\Localization;
use App\Http\Controllers\LocalizationController;


Route::get('/localization/{locale}', LocalizationController::class)->name('localization');

Route::middleware(Localization::class)->group(function () {
    Route::view('/', 'welcome');

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware(['auth', 'verified'])->name('dashboard');

    Route::middleware('auth')->group(function () {
        // Testovacia cesta
        Route::get('/test', function () {
            return view('test');
        });

        // API Token Management routes
        Route::get('/tokens', [TokenController::class, 'index'])->name('tokens.index');
        Route::post('/tokens', [TokenController::class, 'create'])->name('tokens.create');
        Route::delete('/tokens/{token}', [TokenController::class, 'destroy'])->name('tokens.destroy');
    });

    // Admin sekcia
    Route::middleware(['auth', \App\Http\Middleware\IsAdmin::class])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/user-activities', [UserActivityController::class, 'index'])->name('user-activities.index');
        Route::get('/user-activities/export', [UserActivityController::class, 'export'])->name('user-activities.export');
        Route::post('/user-activities/clear', [UserActivityController::class, 'clear'])->name('user-activities.clear');
    });

    //api test
    Route::middleware('auth')->get('/api-test', function () {
        return view('api-test');
    })->name('api.test');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    require __DIR__.'/auth.php';
});