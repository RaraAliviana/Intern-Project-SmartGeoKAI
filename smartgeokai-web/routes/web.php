<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AssetController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ImportAssetController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\TrashController;

// ROOT
Route::get('/', fn () => redirect()->route('login'));

// AUTHENTICATION
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.process');
});

// LOGOUT
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// SUPER ADMIN
Route::middleware(['auth', 'superadmin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // TRASH / SAMPAH DATA
        Route::prefix('trash')->name('trash.')->group(function () {
            Route::get('/', [TrashController::class, 'index'])
                ->name('index');

            Route::post('/{id}/restore', [TrashController::class, 'restore'])
                ->name('restore');

            Route::post('/restore-all', [TrashController::class, 'restoreAll'])
                ->name('restore-all');

            Route::delete('/{id}/force', [TrashController::class, 'forceDelete'])
                ->name('force-delete');
        });
    });

// ADMIN AREA
// Admin dan Superadmin dapat mengakses area ini,
// sesuai konfigurasi middleware 'admin'.
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // DASHBOARD
        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        // PROFILE
        Route::get('/profile', [ProfileController::class, 'index'])
            ->name('profile.index');

        Route::put('/profile', [ProfileController::class, 'update'])
            ->name('profile.update');

        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])
            ->name('profile.password');

        // USER MANAGEMENT
        Route::resource('users', UserController::class)
            ->except(['show']);

        Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])
            ->name('users.toggle-status');

        // ASSETS
        Route::prefix('assets')->name('assets.')->group(function () {

            Route::get('/map', [AssetController::class, 'map'])
                ->name('map');

            // DROPDOWN LOKASI
            Route::get('/get-regencies', [AssetController::class, 'getRegencies'])
                ->name('get-regencies');

            Route::get('/get-districts', [AssetController::class, 'getDistricts'])
                ->name('get-districts');

            Route::get('/', [AssetController::class, 'index'])
                ->name('index');

            Route::get('/create', [AssetController::class, 'create'])
                ->name('create');

            Route::post('/', [AssetController::class, 'store'])
                ->name('store');

            Route::get('/{asset}/edit', [AssetController::class, 'edit'])
                ->name('edit');

            Route::get('/{asset}', [AssetController::class, 'show'])
                ->name('show');

            Route::put('/{asset}', [AssetController::class, 'update'])
                ->name('update');

            Route::delete('/{asset}', [AssetController::class, 'destroy'])
                ->name('destroy');
        });

        // API DROPDOWNS
        Route::get('/api/regencies', [AssetController::class, 'getRegencies'])
            ->name('api.regencies');

        Route::get('/api/districts', [AssetController::class, 'getDistricts'])
            ->name('api.districts');
    });

// KHUSUS ADMIN
// Superadmin tidak boleh mengakses Reports, Import, Notifications, dan Audit Log.
// Middleware 'adminrole' harus memeriksa role admin secara eksklusif.
Route::middleware(['auth', 'admin', 'adminrole'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // REPORTS
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])
                ->name('index');

            Route::get('/export/csv', [ReportController::class, 'exportCsv'])
                ->name('export.csv');

            Route::get('/export/pdf', [ReportController::class, 'exportPdf'])
                ->name('export.pdf');
        });

        // IMPORT ASSETS
        Route::prefix('import')->name('import.')->group(function () {
            Route::get('/', [ImportAssetController::class, 'index'])
                ->name('index');

            Route::post('/', [ImportAssetController::class, 'store'])
                ->name('store');

            Route::get('/template', [ImportAssetController::class, 'downloadTemplate'])
                ->name('template');
        });

        // NOTIFICATIONS
        Route::get('/notifications', function () {
            return view('admin.notifications.index');
        })->name('notifications.index');

        // AUDIT LOG
        Route::get('/log-audit', [AuditLogController::class, 'index'])
            ->name('log-audit.index');

        Route::get('/log-audit/{auditLog}', [AuditLogController::class, 'show'])
            ->name('log-audit.show');
    });
