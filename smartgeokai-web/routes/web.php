
<?php

use Illuminate\Support\Facades\Route;

// Authentication Controller
use App\Http\Controllers\Auth\AuthController;

// Admin Controllers
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AssetController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ImportAssetController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\TrashController;

/*
|--------------------------------------------------------------------------
| SmartGeoKAI Web Routes
|--------------------------------------------------------------------------
|
| Aplikasi web Laravel digunakan oleh Admin dan Super Admin.
| Petugas menggunakan aplikasi mobile Flutter.
|
*/

// ========================================================
// ROOT
// ========================================================

Route::get('/', function () {
    return redirect()->route('login');
});


// ========================================================
// AUTHENTICATION
// ========================================================

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.process');
});


// ========================================================
// LOGOUT
// ========================================================

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');


// ========================================================
// SUPER ADMIN AREA
// ========================================================

Route::middleware(['auth', 'superadmin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // ------------------------------------------------
        // TRASH / SAMPAH
        // ------------------------------------------------

        Route::prefix('trash')
            ->name('trash.')
            ->group(function () {

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


// ========================================================
// ADMIN AREA
// ========================================================

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // ------------------------------------------------
        // DASHBOARD
        // ------------------------------------------------

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');


        // ------------------------------------------------
        // PROFILE
        // ------------------------------------------------

        Route::get('/profile', [ProfileController::class, 'index'])
            ->name('profile.index');

        Route::put('/profile', [ProfileController::class, 'update'])
            ->name('profile.update');

        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])
            ->name('profile.password');


        // ------------------------------------------------
        // USER MANAGEMENT
        // ------------------------------------------------

        Route::resource('users', UserController::class)
            ->except(['show']);

        Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])
            ->name('users.toggle-status');


        // ------------------------------------------------
        // PETA & ASET
        // ------------------------------------------------

        Route::prefix('assets')
            ->name('assets.')
            ->group(function () {

                // Peta aset
                Route::get('/map', [AssetController::class, 'map'])
                    ->name('map');

                // Daftar aset
                Route::get('/', [AssetController::class, 'index'])
                    ->name('index');

                // Form tambah aset
                Route::get('/create', [AssetController::class, 'create'])
                    ->name('create');

                // Simpan aset
                Route::post('/', [AssetController::class, 'store'])
                    ->name('store');

                // Detail aset
                Route::get('/{asset}', [AssetController::class, 'show'])
                    ->name('show');

                // Form edit aset
                Route::get('/{asset}/edit', [AssetController::class, 'edit'])
                    ->name('edit');

                // Perbarui aset
                Route::put('/{asset}', [AssetController::class, 'update'])
                    ->name('update');

                // Hapus aset
                Route::delete('/{asset}', [AssetController::class, 'destroy'])
                    ->name('destroy');
            });


        // ------------------------------------------------
        // AJAX DROPDOWNS
        // ------------------------------------------------

        // Mengambil kabupaten/kota berdasarkan provinsi
        Route::get('/api/regencies', [AssetController::class, 'getRegencies'])
            ->name('api.regencies');

        // Mengambil kecamatan berdasarkan kabupaten/kota
        Route::get('/api/districts', [AssetController::class, 'getDistricts'])
            ->name('api.districts');


        // Alias untuk kompatibilitas halaman laporan dan aset
        // Nama route: admin.assets.get-regencies
        Route::get('/assets/get-regencies', [AssetController::class, 'getRegencies'])
            ->name('assets.get-regencies');

        // Nama route: admin.assets.get-districts
        Route::get('/assets/get-districts', [AssetController::class, 'getDistricts'])
            ->name('assets.get-districts');


        // ------------------------------------------------
        // REPORTS / LAPORAN
        // ------------------------------------------------

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


        // ------------------------------------------------
        // IMPORT DATA ASET
        // ------------------------------------------------

        Route::prefix('import')
            ->name('import.')
            ->group(function () {

                // Halaman import
                Route::get('/', [ImportAssetController::class, 'index'])
                    ->name('index');

                // Proses import
                Route::post('/', [ImportAssetController::class, 'store'])
                    ->name('store');

                // Download template
                Route::get('/template', [ImportAssetController::class, 'downloadTemplate'])
                    ->name('template');
            });


        // ------------------------------------------------
        // NOTIFICATIONS / NOTIFIKASI
        // ------------------------------------------------

        Route::get('/notifications', function () {
            return view('admin.notifications.index');
        })->name('notifications.index');


        // ------------------------------------------------
        // AUDIT LOG
        // ------------------------------------------------

        Route::get('/log-audit', [AuditLogController::class, 'index'])
            ->name('log-audit.index');
    });
