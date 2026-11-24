<?php

use App\Http\Controllers\PasswordController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PetaController;
use App\Http\Controllers\SekolahController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfilSekolahController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

// Halaman peta publik dan data GeoJSON
Route::controller(PetaController::class)->group(function () {
    Route::get('/', 'index')->name('peta.index');
    Route::get('/data/sekolah', 'geojson')->name('peta.data');
    Route::get('/pencarian', 'cari')->name('peta.cari');
    Route::get('/klaster', 'klaster')->name('peta.klaster');
    Route::get('/peta-panas', 'panas')->name('peta.panas');
    Route::get('/wilayah', 'wilayah')->name('peta.wilayah');
    Route::get('/data/wilayah', 'geojsonWilayah')->name('peta.data.wilayah');
    Route::get('/rute', 'rute')->name('peta.rute');
    Route::get('/cek-alamat', 'alamat')->name('peta.alamat');

});

// Profil publik sekolah
Route::get('/profil-sekolah/{sekolah}', [ProfilSekolahController::class, 'publicShow'])
    ->name('sekolah.profil.public');

// Route untuk pengguna yang belum login
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.process');
});

// Route untuk pengguna yang sudah login
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

    // Ubah kata sandi pengguna
    Route::get('/password', [PasswordController::class, 'edit'])
        ->name('password.edit');

    Route::put('/password', [PasswordController::class, 'update'])
        ->name('password.update');
});

// Route khusus Dinas Pendidikan
Route::middleware(['auth', 'role:dinas'])
    ->prefix('dinas')
    ->name('dinas.')
    ->group(function () {
        Route::resource('sekolah', SekolahController::class)
            ->except(['show', 'edit', 'update']);
    });

// Route edit, update, dan profil untuk Dinas serta Operator Sekolah
Route::middleware(['auth', 'role:dinas,sekolah'])
    ->group(function () {

        // Edit data sekolah
        Route::get('/sekolah/{sekolah}/edit', [SekolahController::class, 'edit'])
            ->name('sekolah.edit');

        // Update data sekolah
        Route::put('/sekolah/{sekolah}', [SekolahController::class, 'update'])
            ->name('sekolah.update');

        // Menampilkan halaman pengelolaan profil sekolah
        Route::get('/sekolah/{sekolah}/profil', [ProfilSekolahController::class, 'edit'])
            ->name('sekolah.profil.edit');

        // Menyimpan perubahan profil sekolah
        Route::put('/sekolah/{sekolah}/profil', [ProfilSekolahController::class, 'update'])
            ->name('sekolah.profil.update');
    });

// Admin: kelola pengguna
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::resource('users', UserController::class)
            ->except(['show']);
    });

// Latihan Mandiri: Endpoint pemantau kesehatan koneksi database (/health)
Route::get('/health', function () {
    try {
        $startTime = microtime(true);
        DB::connection()->getPdo();
        $responseTime = round((microtime(true) - $startTime) * 1000, 2);

        return response()->json([
            'status' => 'healthy',
            'timestamp' => now()->toIso8601String(),
            'environment' => app()->environment(),
            'database' => [
                'status' => 'connected',
                'driver' => DB::connection()->getDriverName(),
                'response_time_ms' => $responseTime,
            ],
        ], 200);
    } catch (\Throwable $e) {
        return response()->json([
            'status' => 'unhealthy',
            'timestamp' => now()->toIso8601String(),
            'environment' => app()->environment(),
            'database' => [
                'status' => 'disconnected',
                'error' => $e->getMessage(),
            ],
        ], 500);
    }
})->name('health.check');
