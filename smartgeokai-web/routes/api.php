<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AssetController;
use App\Http\Controllers\Api\RegionController;
use Illuminate\Support\Facades\Route;

// ===== Publik (belum login) =====
Route::post('/auth/login', [AuthController::class, 'login']);

// ===== Wajib login (Sanctum token) =====
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']); // Ganti dari /me ke /profile
    Route::put('/profile',          [AuthController::class, 'updateProfile']);
    Route::put('/profile/password', [AuthController::class, 'changePassword']);

    // CRUD Aset — otomatis discope ke province_id milik petugas
    Route::apiResource('assets', AssetController::class);

    // Wilayah (dropdown provinsi/kabupaten/kecamatan di form mobile)
    Route::get('/regions/provinces', [RegionController::class, 'provinces']);
    Route::get('/regions/regencies', [RegionController::class, 'regencies']);
    Route::get('/regions/districts', [RegionController::class, 'districts']);

});