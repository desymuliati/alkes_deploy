<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\Admin\BarangController as AdminBarangController;
use App\Http\Controllers\User\BarangController as UserBarangController;
use App\Http\Controllers\Admin\PenjualanController as AdminPenjualanController;
use App\Http\Controllers\Admin\LaporanController as AdminLaporanController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Front\LandingController;

// --- Halaman Depan ---
Route::name('front.')->group(function () {
    Route::get('/', [LandingController::class, 'index'])->name('landing');
});

// --- Login & Logout ---
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// --- Halaman Admin (butuh login dan role ADMIN) ---
Route::prefix('admin')->name('admin.')->middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'admin' // Pastikan middleware 'admin' ini mengecek Auth::user()->roles === 'admin'
])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('users', AdminUserController::class);
    Route::resource('barangs', AdminBarangController::class);
    Route::resource('penjualans', AdminPenjualanController::class);
    // Rute laporan perlu didefinisikan sebelum resource agar tidak tertimpa
    Route::get('/laporans/get-barang-by-penjualan/{penjualanId}', [AdminLaporanController::class, 'getBarangByPenjualan'])->name('laporans.getBarangByPenjualan');
    Route::get('/laporans/export', [AdminLaporanController::class, 'export'])->name('laporans.export');
    Route::resource('laporans', AdminLaporanController::class);
});

// --- Halaman User (butuh login dan role USER) ---
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'user' // Pastikan middleware 'user' ini mengecek Auth::user()->roles === 'user' (atau bukan 'admin')
])->name('user.')->group(function () { // <-- PENTING: Tambahkan .name('user.') di sini
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard'); // <-- Ini akan menjadi 'user.dashboard'
    Route::resource('barangs', UserBarangController::class); // <-- Ini akan menjadi 'user.barangs.index', 'user.barangs.show', dst.
    // Tambahkan rute user lain di sini jika ada
});