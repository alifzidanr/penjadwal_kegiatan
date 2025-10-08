<?php
// app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kegiatan;
use App\Models\UnitKerja;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;

class DashboardController extends Controller
{
    public function index()
    {
        // Auto-archive outdated schedules before showing the dashboard
        $this->autoArchiveOutdatedSchedules();
        
        $unitKerja = UnitKerja::all();
        return view('dashboard.index', compact('unitKerja'));
    }

    public function fullscreen()
    {
        // Auto-archive outdated schedules before showing fullscreen view
        $this->autoArchiveOutdatedSchedules();
        
        $unitKerja = UnitKerja::all();
        return view('dashboard.fullscreen', compact('unitKerja'));
    }

    public function getData()
    {
        try {
            // Auto-archive outdated schedules first
            $this->autoArchiveOutdatedSchedules();
            
            // Get active (non-archived) jadwal with unit kerja relationship
            $query = Kegiatan::with('unitKerja')->where('is_archived', false);
            
            // Apply filters if provided
            if (request('unit_kerja')) {
                $query->where('id_unit_kerja', request('unit_kerja'));
            }
            
            if (request('tanggal_mulai')) {
                $query->where('tanggal_selesai', '>=', request('tanggal_mulai'));
            }
            
            if (request('tanggal_selesai')) {
                $query->where('tanggal_mulai', '<=', request('tanggal_selesai'));
            }
            
            // Order by tanggal_mulai ASC (earliest dates first), then by jam_mulai
            $kegiatan = $query->orderBy('tanggal_mulai', 'asc')
                           ->orderBy('jam_mulai', 'asc')
                           ->get();
            
            return response()->json([
                'draw' => request()->get('draw'),
                'recordsTotal' => $kegiatan->count(),
                'recordsFiltered' => $kegiatan->count(),
                'data' => $kegiatan->map(function($item, $index) {
                    return [
                        'DT_RowIndex' => $index + 1,
                        // Send both formatted and raw dates
                        'tanggal_mulai' => $item->tanggal_mulai ? $item->tanggal_mulai->format('Y-m-d') : null,
                        'tanggal_selesai' => $item->tanggal_selesai ? $item->tanggal_selesai->format('Y-m-d') : null,
                        'tanggal_mulai_formatted' => $item->tanggal_mulai ? $item->tanggal_mulai->format('d/m/Y') : '-',
                        'tanggal_selesai_formatted' => $item->tanggal_selesai ? $item->tanggal_selesai->format('d/m/Y') : '-',
                        'nama_kegiatan' => $item->nama_kegiatan,
                        'nama_tempat' => $item->nama_tempat ?? '-',
                        'jam_formatted' => ($item->jam_mulai && $item->jam_selesai) ? 
                            $item->jam_mulai . ' - ' . $item->jam_selesai : '-',
                        'person_in_charge' => $item->person_in_charge ?? '-',
                        'anggota' => $item->anggota ?? '-',
                        'duration_days' => $item->duration_days,
                        'is_multi_day' => $item->isMultiDay(),
                        'is_ongoing' => $item->isOngoing(),
                        'action' => '<button class="btn-detail px-3 py-1 bg-blue-500 text-white rounded text-sm hover:bg-blue-600 transition-colors" data-id="'.$item->id_kegiatan.'">
                                        <i data-lucide="eye" class="w-4 h-4 inline mr-1"></i>Detail
                                     </button>'
                    ];
                })
            ]);

        } catch (\Exception $e) {
            \Log::error('Dashboard DataTables Error: ' . $e->getMessage());
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $kegiatan = Kegiatan::with('unitKerja')->findOrFail($id);
            
            // Return the data in a format that matches what the frontend expects
            $response = [
                'id_kegiatan' => $kegiatan->id_kegiatan,
                'nama_kegiatan' => $kegiatan->nama_kegiatan,
                'person_in_charge' => $kegiatan->person_in_charge,
                'anggota' => $kegiatan->anggota,
                'tanggal_mulai' => $kegiatan->tanggal_mulai ? $kegiatan->tanggal_mulai->format('Y-m-d') : null,
                'tanggal_selesai' => $kegiatan->tanggal_selesai ? $kegiatan->tanggal_selesai->format('Y-m-d') : null,
                'tanggal_formatted' => $kegiatan->tanggal_formatted,
                'jam_mulai' => $kegiatan->jam_mulai,
                'jam_selesai' => $kegiatan->jam_selesai,
                'nama_tempat' => $kegiatan->nama_tempat,
                'id_unit_kerja' => $kegiatan->id_unit_kerja,
                'is_archived' => $kegiatan->is_archived,
                'archived_at' => $kegiatan->archived_at,
                'duration_days' => $kegiatan->duration_days,
                'is_multi_day' => $kegiatan->isMultiDay(),
                'is_ongoing' => $kegiatan->isOngoing(),
                'unit_kerja' => $kegiatan->unitKerja ? [
                    'id_unit_kerja' => $kegiatan->unitKerja->id_unit_kerja,
                    'nama_unit_kerja' => $kegiatan->unitKerja->nama_unit_kerja
                ] : null
            ];
            
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Jadwal tidak ditemukan'], 404);
        }
    }

    public function manualArchive($id)
    {
        try {
            $kegiatan = Kegiatan::findOrFail($id);
            $kegiatan->update([
                'is_archived' => true,
                'archived_at' => Carbon::now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Jadwal berhasil diarsipkan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengarsipkan jadwal'
            ], 500);
        }
    }

    /**
     * Auto-archive schedules that are outdated (past their end date)
     */
    private function autoArchiveOutdatedSchedules()
    {
        try {
            $yesterday = Carbon::yesterday()->format('Y-m-d');
            
            // Archive schedules where tanggal_selesai is before today
            $outdatedSchedules = Kegiatan::where('is_archived', false)
                ->where('tanggal_selesai', '<', $yesterday)
                ->get();

            $archivedCount = 0;
            foreach ($outdatedSchedules as $schedule) {
                $schedule->update([
                    'is_archived' => true,
                    'archived_at' => Carbon::now()
                ]);
                $archivedCount++;
            }

            if ($archivedCount > 0) {
                \Log::info("Auto-archived {$archivedCount} outdated schedules in DashboardController");
            }

            return $archivedCount;

        } catch (\Exception $e) {
            \Log::error('Auto-archive Error in DashboardController: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Manual trigger for auto-archive via API
     */
    public function triggerAutoArchive()
    {
        try {
            $archivedCount = $this->autoArchiveOutdatedSchedules();
            
            return response()->json([
                'success' => true,
                'message' => "Successfully archived {$archivedCount} outdated schedules",
                'archived_count' => $archivedCount
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to auto-archive schedules'
            ], 500);
        }
    }
}