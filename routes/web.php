<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserActivityController;
use App\Http\Controllers\TokenController;
use App\Http\Controllers\LanguageController;



// ✅ Verejná časť
Route::get('/', function () {
    return view('welcome');
});

// ✅ Dashboard pre prihlásených
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// ✅ Routy pre bežného používateľa
Route::middleware('auth')->group(function () {
    Route::get('/test', function () {
        return view('test');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/tokens', [TokenController::class, 'index'])->name('tokens.index');
    Route::post('/tokens', [TokenController::class, 'create'])->name('tokens.create');
    Route::delete('/tokens/{token}', [TokenController::class, 'destroy'])->name('tokens.destroy');
});

// ✅ Admin sekcia
Route::middleware(['auth', \App\Http\Middleware\IsAdmin::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/user-activities', [UserActivityController::class, 'index'])->name('user-activities.index');
        Route::get('/user-activities/export', [UserActivityController::class, 'export'])->name('user-activities.export');
        Route::post('/user-activities/clear', [UserActivityController::class, 'clear'])->name('user-activities.clear');
    });

// ✅ API test (prihlásení)
Route::middleware('auth')->get('/api-test', function () {
    return view('api-test');
})->name('api.test');

Route::get('language/{lang}', [App\Http\Controllers\LanguageController::class, 'switchLang'])->name('lang.switch');


// ✅ Auth routy (login, register atď.)
require __DIR__.'/auth.php';