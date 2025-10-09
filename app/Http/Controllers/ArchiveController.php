<?php
// app/Http/Controllers/ArchiveController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kegiatan;
use App\Models\UnitKerja;
use Carbon\Carbon;

class ArchiveController extends Controller
{
    public function index()
    {
        // Auto-archive outdated schedules before showing archive
        $this->autoArchiveOutdatedSchedules();
        
        $unitKerja = UnitKerja::all();
        return view('archive.index', compact('unitKerja'));
    }

    public function getData()
    {
        try {
            // Get archived jadwal with unit kerja relationship
            $query = Kegiatan::with('unitKerja')->where('is_archived', true);
            
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
            
            // Order by tanggal_mulai DESC (latest dates first), then by jam_mulai
            $kegiatan = $query->orderBy('tanggal_mulai', 'desc')
                           ->orderBy('jam_mulai', 'desc')
                           ->get();
            
            return response()->json([
                'draw' => request()->get('draw'),
                'recordsTotal' => $kegiatan->count(),
                'recordsFiltered' => $kegiatan->count(),
                'data' => $kegiatan->map(function($item, $index) {
                    return [
                        'DT_RowIndex' => $index + 1,
                        'tanggal_mulai_formatted' => $item->tanggal_mulai ? $item->tanggal_mulai->format('d/m/Y') : '-',
                        'tanggal_selesai_formatted' => $item->tanggal_selesai ? $item->tanggal_selesai->format('d/m/Y') : '-',
                        'nama_kegiatan' => $item->nama_kegiatan,
                        'nama_tempat' => $item->nama_tempat ?? '-',
                        'jam_formatted' => ($item->jam_mulai && $item->jam_selesai) ? 
                            $item->jam_mulai . ' - ' . $item->jam_selesai : '-',
                        'person_in_charge' => $item->person_in_charge ?? '-',
                        'anggota' => $item->anggota ?? '-',
                        'archived_at' => $item->archived_at ? $item->archived_at->format('d/m/Y H:i') : '-',
                        'action' => '
                            <div class="flex space-x-1">
                                <button class="btn-detail px-2 py-1 bg-blue-500 text-white rounded text-xs hover:bg-blue-600 transition-colors" data-id="'.$item->id_kegiatan.'" title="Lihat Detail">
                                    <i data-lucide="eye" class="w-3 h-3"></i>
                                </button>
                                <button class="btn-restore px-2 py-1 bg-green-500 text-white rounded text-xs hover:bg-green-600 transition-colors" data-id="'.$item->id_kegiatan.'" title="Restore">
                                    <i data-lucide="rotate-ccw" class="w-3 h-3"></i>
                                </button>
                                <button class="btn-delete-permanent px-2 py-1 bg-red-500 text-white rounded text-xs hover:bg-red-600 transition-colors" data-id="'.$item->id_kegiatan.'" title="Hapus Permanen">
                                    <i data-lucide="trash-2" class="w-3 h-3"></i>
                                </button>
                            </div>
                        '
                    ];
                })
            ]);

        } catch (\Exception $e) {
            \Log::error('Archive DataTables Error: ' . $e->getMessage());
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

    public function restore($id)
    {
        try {
            $kegiatan = Kegiatan::findOrFail($id);
            $kegiatan->update([
                'is_archived' => false,
                'archived_at' => null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Jadwal berhasil direstore'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal restore jadwal'
            ], 500);
        }
    }

    public function destroyPermanent($id)
    {
        try {
            $kegiatan = Kegiatan::findOrFail($id);
            $kegiatan->delete();

            return response()->json([
                'success' => true,
                'message' => 'Jadwal berhasil dihapus permanen'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus jadwal'
            ], 500);
        }
    }

    public function bulkRestore(Request $request)
    {
        try {
            $ids = $request->input('ids', []);
            
            if (empty($ids)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No items selected'
                ], 400);
            }

            $count = Kegiatan::whereIn('id_kegiatan', $ids)
                ->where('is_archived', true)
                ->update([
                    'is_archived' => false,
                    'archived_at' => null
                ]);

            return response()->json([
                'success' => true,
                'message' => "Successfully restored {$count} schedules",
                'count' => $count
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to restore schedules'
            ], 500);
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
     * FIXED: Now archives schedules where tanggal_selesai < today (not yesterday)
     */
    private function autoArchiveOutdatedSchedules()
    {
        try {
            $today = Carbon::today()->format('Y-m-d');
            
            // Archive schedules where tanggal_selesai is before today
            // This means: if event ended yesterday or earlier, archive it
            $outdatedSchedules = Kegiatan::where('is_archived', false)
                ->where('tanggal_selesai', '<', $today)
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
                \Log::info("Auto-archived {$archivedCount} outdated schedules in ArchiveController at " . Carbon::now()->toDateTimeString());
            }

            return $archivedCount;

        } catch (\Exception $e) {
            \Log::error('Auto-archive Error in ArchiveController: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get statistics for archive
     */
    public function getStats()
    {
        try {
            $today = Carbon::today();
            $thisWeek = Carbon::now()->startOfWeek();
            $thisMonth = Carbon::now()->startOfMonth();

            $stats = [
                'total_active' => Kegiatan::where('is_archived', false)->count(),
                'total_archived' => Kegiatan::where('is_archived', true)->count(),
                'today' => Kegiatan::where('is_archived', false)
                    ->where('tanggal_mulai', '<=', $today)
                    ->where('tanggal_selesai', '>=', $today)
                    ->count(),
                'this_week' => Kegiatan::where('is_archived', false)
                    ->where(function($query) use ($thisWeek) {
                        $query->whereBetween('tanggal_mulai', [$thisWeek, $thisWeek->copy()->endOfWeek()])
                              ->orWhereBetween('tanggal_selesai', [$thisWeek, $thisWeek->copy()->endOfWeek()]);
                    })
                    ->count(),
                'this_month' => Kegiatan::where('is_archived', false)
                    ->where(function($query) use ($thisMonth) {
                        $query->whereBetween('tanggal_mulai', [$thisMonth, $thisMonth->copy()->endOfMonth()])
                              ->orWhereBetween('tanggal_selesai', [$thisMonth, $thisMonth->copy()->endOfMonth()]);
                    })
                    ->count(),
            ];

            return response()->json($stats);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}