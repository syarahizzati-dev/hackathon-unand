<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — CAMPUS-E
|--------------------------------------------------------------------------
|
| Mahasiswa routes: middleware ['auth', 'mahasiswa']
| Admin routes:     middleware ['auth', 'admin']
|
*/

// ─── Mahasiswa Routes ──────────────────────────────────────────
Route::middleware(['auth', 'mahasiswa'])->group(function () {
    Route::view('/student-dashboard', 'mahasiswa.dashboard')
        ->name('mahasiswa.dashboard');

    Route::view('/mood', 'mahasiswa.mood')
        ->name('mahasiswa.mood');

    Route::view('/buku-harian', 'mahasiswa.buku-harian')
        ->name('mahasiswa.buku-harian');

    Route::view('/tukar-pikiran', 'mahasiswa.tukar-pikiran')
        ->name('mahasiswa.tukar-pikiran');
});

// ─── Admin Routes ──────────────────────────────────────────────
Route::middleware(['auth', 'admin'])->group(function () {
    Route::view('/admin-dashboard', 'admin.dashboard')
        ->name('admin.dashboard');

    Route::view('/admin-alert', 'admin.alert')
        ->name('admin.alert');

    Route::view('/admin-log', 'admin.log')
        ->name('admin.log');
});

// ─── Logout ────────────────────────────────────────────────────
Route::post('/logout', function (Request $request) {
    Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->middleware('auth')->name('logout');

require __DIR__.'/auth.php';
