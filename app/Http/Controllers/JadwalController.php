<?php
// app/Http/Controllers/JadwalController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kegiatan;
use App\Models\UnitKerja;
use Carbon\Carbon;

class JadwalController extends Controller
{
    public function index()
    {
        // Auto-archive outdated schedules before showing the page
        $this->autoArchiveOutdatedSchedules();
        
        $unitKerja = UnitKerja::all();
        return view('jadwal.index', compact('unitKerja'));
    }

    public function getData()
    {
        try {
            // Auto-archive outdated schedules first
            $this->autoArchiveOutdatedSchedules();
            
            // Include unit kerja relationship, only show active (non-archived) schedules
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
                    // Create status indicators
                    $statusBadge = '';
                    if ($item->isOngoing()) {
                        $statusBadge = '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 ml-2">Berlangsung</span>';
                    } elseif ($item->isMultiDay()) {
                        $statusBadge = '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 ml-2">' . $item->duration_days . ' Hari</span>';
                    }
                    
                   return [
                        'DT_RowIndex' => $index + 1,
                        // Separate formatted dates for each column
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
                        'action' => '
                            <div class="flex space-x-1">
                                <button class="btn-detail inline-flex items-center px-2 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700 transition-colors" data-id="'.$item->id_kegiatan.'" title="Lihat Detail">
                                    <i data-lucide="eye" class="w-3 h-3"></i>
                                </button>
                                <button class="btn-edit inline-flex items-center px-2 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700 transition-colors" data-id="'.$item->id_kegiatan.'" title="Edit">
                                    <i data-lucide="edit" class="w-3 h-3"></i>
                                </button>
                                <button class="btn-delete inline-flex items-center px-2 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700 transition-colors" data-id="'.$item->id_kegiatan.'" title="Hapus">
                                    <i data-lucide="trash-2" class="w-3 h-3"></i>
                                </button>
                            </div>
                        '
                    ];
                })
            ]);

        } catch (\Exception $e) {
            \Log::error('DataTables Error: ' . $e->getMessage());
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'person_in_charge' => 'nullable|string|max:255',
            'anggota' => 'nullable|string',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'jam_mulai' => 'nullable|date_format:H:i',
            'jam_selesai' => 'nullable|date_format:H:i|after:jam_mulai',
            'nama_tempat' => 'nullable|string|max:255',
            'id_unit_kerja' => 'nullable|exists:t_ref_unit_kerja,id_unit_kerja'
        ]);

        // Prepare data
        $data = $request->all();
        
        // If tanggal_selesai is not provided, set it same as tanggal_mulai
        if (!$data['tanggal_selesai'] && $data['tanggal_mulai']) {
            $data['tanggal_selesai'] = $data['tanggal_mulai'];
        }

        Kegiatan::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Jadwal kegiatan berhasil ditambahkan'
        ]);
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
            \Log::error('Show Kegiatan Error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Kegiatan tidak ditemukan',
                'message' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'person_in_charge' => 'nullable|string|max:255',
            'anggota' => 'nullable|string',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'jam_mulai' => 'nullable|date_format:H:i',
            'jam_selesai' => 'nullable|date_format:H:i|after:jam_mulai',
            'nama_tempat' => 'nullable|string|max:255',
            'id_unit_kerja' => 'nullable|exists:t_ref_unit_kerja,id_unit_kerja'
        ]);

        $kegiatan = Kegiatan::findOrFail($id);
        
        // Prepare data
        $data = $request->all();
        
        // If tanggal_selesai is not provided, set it same as tanggal_mulai
        if (!$data['tanggal_selesai'] && $data['tanggal_mulai']) {
            $data['tanggal_selesai'] = $data['tanggal_mulai'];
        }
        
        $kegiatan->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Jadwal kegiatan berhasil diupdate'
        ]);
    }

    public function destroy($id)
    {
        $kegiatan = Kegiatan::findOrFail($id);
        $kegiatan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Jadwal kegiatan berhasil dihapus'
        ]);
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
                \Log::info("Auto-archived {$archivedCount} outdated schedules in JadwalController at " . Carbon::now()->toDateTimeString());
            }

            return $archivedCount;

        } catch (\Exception $e) {
            \Log::error('Auto-archive Error in JadwalController: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get schedule statistics
     */
    public function getStats()
    {
        try {
            $today = Carbon::today();
            $thisWeek = Carbon::now()->startOfWeek();
            $thisMonth = Carbon::now()->startOfMonth();

            $stats = [
                'total_active' => Kegiatan::where('is_archived', false)->count(),
                'ongoing' => Kegiatan::where('is_archived', false)
                    ->where('tanggal_mulai', '<=', $today)
                    ->where('tanggal_selesai', '>=', $today)
                    ->count(),
                'upcoming' => Kegiatan::where('is_archived', false)
                    ->where('tanggal_mulai', '>', $today)
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
                'multi_day' => Kegiatan::where('is_archived', false)
                    ->whereRaw('tanggal_mulai != tanggal_selesai')
                    ->count(),
            ];

            return response()->json($stats);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}