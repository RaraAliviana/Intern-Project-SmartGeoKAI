
<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AssetController;
use App\Http\Controllers\Api\RegionController;
use Illuminate\Support\Facades\Route;

// ==========================================
// ROUTE PUBLIK
// ==========================================

Route::post('/auth/login', [AuthController::class, 'login']);


// ==========================================
// ROUTE YANG MEMERLUKAN LOGIN
// ==========================================

Route::middleware('auth:sanctum')->group(function () {

    // Autentikasi
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Profil pengguna
    Route::get('/profile', [AuthController::class, 'me']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);

    // Ganti password
    Route::put(
        '/profile/password',
        [AuthController::class, 'changePassword']
    );

    // CRUD aset
    Route::apiResource('assets', AssetController::class);

    // Data wilayah
    Route::get(
        '/regions/provinces',
        [RegionController::class, 'provinces']
    );

    Route::get(
        '/regions/regencies',
        [RegionController::class, 'regencies']
    );

    Route::get(
        '/regions/districts',
        [RegionController::class, 'districts']
    );
});
