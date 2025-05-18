<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Všetky tieto endpointy budú dostupné pod "/api/..."
|
*/

// Prihlásenie (vytvorenie tokenu)
Route::post('/login', function (Request $request) {
    $credentials = $request->only('email', 'password');

    if (!Auth::attempt($credentials)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    $user = Auth::user();
    $token = $user->createToken('api_token')->plainTextToken;

    return response()->json([
        'token' => $token,
        'user' => $user,
    ]);
});

// Získanie informácií o používateľovi
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Odhlásenie (zmazanie tokenu)
Route::middleware('auth:sanctum')->post('/logout', function (Request $request) {
    $request->user()->currentAccessToken()?->delete();

    return response()->json([
        'message' => 'Token successfully revoked (logged out)',
    ]);
});

// Obnovenie tokenu (zmazanie starého, vytvorenie nového)
Route::middleware('auth:sanctum')->post('/token/refresh', function (Request $request) {
    $request->user()->currentAccessToken()?->delete();

    $newToken = $request->user()->createToken('refreshed_token')->plainTextToken;

    return response()->json([
        'token' => $newToken,
        'message' => 'Token successfully refreshed',
    ]);
});

// Chránené API routes pre aktivity používateľov
Route::middleware('auth:sanctum')->group(function () {
    // Zobrazenie aktivít (dostupné pre všetkých prihlásených používateľov)
    Route::get('/user-activities', [\App\Http\Controllers\API\UserActivityController::class, 'index']);

    // Admin API routes (vyžadujú admin práva)
    Route::middleware([\App\Http\Middleware\IsAdmin::class])->group(function () {
        Route::get('/user-activities/export', [\App\Http\Controllers\API\UserActivityController::class, 'export']);
        Route::post('/user-activities/clear', [\App\Http\Controllers\API\UserActivityController::class, 'clear']);
    });
});