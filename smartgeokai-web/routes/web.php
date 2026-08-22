<?php

use Illuminate\Support\Facades\Route;

// Authentication
use App\Http\Controllers\Auth\AuthController;

// Admin Controllers
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AssetController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ImportAssetController;


/*
|--------------------------------------------------------------------------
| SmartGeoKAI Web Routes
|--------------------------------------------------------------------------
|
| Web Laravel digunakan oleh ADMIN.
| Petugas menggunakan aplikasi Mobile Flutter.
|
*/


// ============================================================================
// ROOT
// ============================================================================

Route::get('/', function () {
    return redirect()->route('login');
});


// ============================================================================
// AUTHENTICATION
// ============================================================================

// Halaman untuk pengguna yang belum login
Route::middleware('guest')->group(function () {

    Route::get('/login', [AuthController::class, 'showLoginForm'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login']);
});


// ============================================================================
// LOGOUT
// ============================================================================

Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');
});


// ============================================================================
// ADMIN AREA
// ============================================================================

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {


        // ====================================================================
        // DASHBOARD
        // ====================================================================

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');


        // ====================================================================
        // PROFILE
        // ====================================================================

        Route::get('/profile', [ProfileController::class, 'index'])
            ->name('profile.index');

        Route::put('/profile', [ProfileController::class, 'update'])
            ->name('profile.update');

        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])
            ->name('profile.password');


        // ====================================================================
        // MANAJEMEN PETUGAS
        // ====================================================================

        Route::resource('users', UserController::class)
            ->except(['show']);

        Route::patch(
            '/users/{user}/toggle-status',
            [UserController::class, 'toggleStatus']
        )->name('users.toggle-status');


        // ====================================================================
        // PETA & ASET
        // ====================================================================

        Route::prefix('assets')
            ->name('assets.')
            ->group(function () {

                // ------------------------------------------------------------
                // Peta Aset
                // URL: /admin/assets/map
                // Route: admin.assets.map
                // ------------------------------------------------------------

                Route::get('/map', [AssetController::class, 'map'])
                    ->name('map');


                // ------------------------------------------------------------
                // API Dropdown Wilayah
                // Route:
                // admin.assets.get-regencies
                // admin.assets.get-districts
                // ------------------------------------------------------------

                Route::get('/get-regencies', [AssetController::class, 'getRegencies'])
                    ->name('get-regencies');

                Route::get('/get-districts', [AssetController::class, 'getDistricts'])
                    ->name('get-districts');


                // ------------------------------------------------------------
                // Daftar Aset
                // URL: /admin/assets
                // Route: admin.assets.index
                // ------------------------------------------------------------

                Route::get('/', [AssetController::class, 'index'])
                    ->name('index');


                // ------------------------------------------------------------
                // Tambah Aset
                // ------------------------------------------------------------

                Route::get('/create', [AssetController::class, 'create'])
                    ->name('create');

                Route::post('/', [AssetController::class, 'store'])
                    ->name('store');


                // ------------------------------------------------------------
                // Detail Aset
                // ------------------------------------------------------------

                Route::get('/{asset}', [AssetController::class, 'show'])
                    ->name('show');


                // ------------------------------------------------------------
                // Edit Aset
                // ------------------------------------------------------------

                Route::get('/{asset}/edit', [AssetController::class, 'edit'])
                    ->name('edit');

                Route::put('/{asset}', [AssetController::class, 'update'])
                    ->name('update');


                // ------------------------------------------------------------
                // Hapus Aset
                // ------------------------------------------------------------

                Route::delete('/{asset}', [AssetController::class, 'destroy'])
                    ->name('destroy');
            });


        // ====================================================================
        // APPROVAL
        // ====================================================================

        Route::get('/approvals', function () {
            return view('admin.approvals.index');
        })->name('approvals.index');


        // ====================================================================
        // REPORTS / FR-10 & FR-11
        // ====================================================================

        /*
        |--------------------------------------------------------------------------
        | Halaman Laporan & Export Data
        |--------------------------------------------------------------------------
        |
        | FR-10: Filter Laporan Data Aset
        | FR-11: Export Data Laporan ke CSV & PDF
        |
        */

        Route::prefix('reports')
            ->name('reports.')
            ->group(function () {

                // Halaman laporan
                Route::get('/', [ReportController::class, 'index'])
                    ->name('index');

                // Export CSV
                Route::get('/export/csv', [ReportController::class, 'exportCsv'])
                    ->name('export.csv');

                // Export PDF
                Route::get('/export/pdf', [ReportController::class, 'exportPdf'])
                    ->name('export.pdf');
            });


        // ====================================================================
        // IMPORT DATA ASET MASSAL (FR-11)
        // ====================================================================

        Route::prefix('import')
            ->name('import.')
            ->group(function () {

                // Halaman Form Upload Import
                Route::get('/', [ImportAssetController::class, 'index'])
                    ->name('index');

                // Proses Upload & Simpan CSV
                Route::post('/', [ImportAssetController::class, 'store'])
                    ->name('store');

                // Unduh Template CSV Masukan
                Route::get('/template', [ImportAssetController::class, 'downloadTemplate'])
                    ->name('template');
            });


        // ====================================================================
        // NOTIFICATIONS
        // ====================================================================

        Route::get('/notifications', function () {
            return view('admin.notifications.index');
        })->name('notifications.index');


        // ====================================================================
        // AUDIT LOG
        // ====================================================================

        Route::get('/log-audit', function () {
            return view('admin.log-audit.index');
        })->name('log-audit.index');


        // ====================================================================
        // BACKUP
        // ====================================================================

        Route::get('/backup', function () {
            return view('admin.backup.index');
        })->name('backup.index');

    });