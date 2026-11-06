
<?php

use App\Http\Controllers\PasswordController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PetaController;
use App\Http\Controllers\SekolahController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfilSekolahController;
use Illuminate\Support\Facades\Route;

// Halaman peta publik
Route::get('/', [PetaController::class, 'index'])
    ->name('peta.index');

// Data GeoJSON sekolah
Route::get('/data/sekolah', [PetaController::class, 'geojson'])
    ->name('peta.data');

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
            ->except('show');
    });
