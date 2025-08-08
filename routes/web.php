<?php
// routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\UnitKerjaController;
use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ArchiveController;

// Auth routes
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware('auth')->group(function () {
    // Dashboard routes
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/dashboard/data', [DashboardController::class, 'getData'])->name('dashboard.data');
    Route::get('/dashboard/fullscreen', [DashboardController::class, 'fullscreen'])->name('dashboard.fullscreen');
    Route::get('/dashboard/{id}', [DashboardController::class, 'show'])->name('dashboard.show');
    Route::post('/dashboard/manual-archive/{id}', [DashboardController::class, 'manualArchive'])->name('dashboard.manual.archive');
    Route::post('/dashboard/auto-archive', [DashboardController::class, 'triggerAutoArchive'])->name('dashboard.auto-archive');
    
    // Archive routes (separate menu)
    Route::get('/archive', [ArchiveController::class, 'index'])->name('archive.index');
    Route::get('/archive/data', [ArchiveController::class, 'getData'])->name('archive.data');
    Route::get('/archive/stats', [ArchiveController::class, 'getStats'])->name('archive.stats');
    Route::get('/archive/{id}', [ArchiveController::class, 'show'])->name('archive.show');
    Route::post('/archive/restore/{id}', [ArchiveController::class, 'restore'])->name('archive.restore');
    Route::delete('/archive/destroy-permanent/{id}', [ArchiveController::class, 'destroyPermanent'])->name('archive.destroy.permanent');
    Route::post('/archive/bulk-restore', [ArchiveController::class, 'bulkRestore'])->name('archive.bulk.restore');
    
    // Jadwal routes
    Route::get('/jadwal', [JadwalController::class, 'index'])->name('jadwal.index');
    Route::get('/jadwal/data', [JadwalController::class, 'getData'])->name('jadwal.data');
    Route::post('/jadwal', [JadwalController::class, 'store'])->name('jadwal.store');
    Route::get('/jadwal/{id}', [JadwalController::class, 'show'])->name('jadwal.show');
    Route::put('/jadwal/{id}', [JadwalController::class, 'update'])->name('jadwal.update');
    Route::delete('/jadwal/{id}', [JadwalController::class, 'destroy'])->name('jadwal.destroy');
    
    // Unit Kerja routes
    Route::get('/unit-kerja', [UnitKerjaController::class, 'index'])->name('unit-kerja.index');
    Route::get('/unit-kerja/data', [UnitKerjaController::class, 'getData'])->name('unit-kerja.data');
    Route::post('/unit-kerja', [UnitKerjaController::class, 'store'])->name('unit-kerja.store');
    Route::get('/unit-kerja/{id}', [UnitKerjaController::class, 'show'])->name('unit-kerja.show');
    Route::put('/unit-kerja/{id}', [UnitKerjaController::class, 'update'])->name('unit-kerja.update');
    Route::delete('/unit-kerja/{id}', [UnitKerjaController::class, 'destroy'])->name('unit-kerja.destroy');
    
    // Anggota routes
    Route::get('/anggota', [AnggotaController::class, 'index'])->name('anggota.index');
    Route::get('/anggota/data', [AnggotaController::class, 'getData'])->name('anggota.data');
    Route::get('/anggota/all', [AnggotaController::class, 'getAll'])->name('anggota.all');
    Route::get('/anggota/by-unit/{id}', [AnggotaController::class, 'getByUnitKerja'])->name('anggota.by-unit');
    Route::post('/anggota', [AnggotaController::class, 'store'])->name('anggota.store');
    Route::get('/anggota/{id}', [AnggotaController::class, 'show'])->name('anggota.show');
    Route::put('/anggota/{id}', [AnggotaController::class, 'update'])->name('anggota.update');
    Route::delete('/anggota/{id}', [AnggotaController::class, 'destroy'])->name('anggota.destroy');
});