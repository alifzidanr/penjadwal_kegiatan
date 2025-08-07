<?php
// routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\UnitKerjaController;
use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\DashboardController;

// Auth routes
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware('auth')->group(function () {
    // Dashboard routes
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/dashboard/data', [DashboardController::class, 'getData'])->name('dashboard.data');
    Route::get('/dashboard/fullscreen', [DashboardController::class, 'fullscreen'])->name('dashboard.fullscreen'); // NEW ROUTE
    Route::get('/dashboard/{id}', [DashboardController::class, 'show'])->name('dashboard.show');
    
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
    
    // Debug routes
    Route::get('/debug-data', function() {
        try {
            // Test basic query
            $kegiatan = \App\Models\Kegiatan::all();
            return response()->json([
                'success' => true,
                'count' => $kegiatan->count(),
                'data' => $kegiatan->take(2)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
        }
    });
    
    Route::get('/debug-anggota', function() {
        try {
            // Check if anggota table exists and has data
            $anggota = \App\Models\Anggota::all();
            return response()->json([
                'success' => true,
                'count' => $anggota->count(),
                'data' => $anggota->take(3)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
        }
    });
});