<?php
// app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kegiatan;
use App\Models\UnitKerja;

class DashboardController extends Controller
{
    public function index()
    {
        $unitKerja = UnitKerja::all();
        return view('dashboard.index', compact('unitKerja'));
    }

    public function fullscreen()
    {
        $unitKerja = UnitKerja::all();
        return view('dashboard.fullscreen', compact('unitKerja'));
    }

    public function getData()
    {
        try {
            // Get jadwal with unit kerja relationship
            $query = Kegiatan::with('unitKerja');
            
            // Apply filters if provided
            if (request('unit_kerja')) {
                $query->where('id_unit_kerja', request('unit_kerja'));
            }
            
            if (request('tanggal_mulai')) {
                $query->whereDate('tanggal', '>=', request('tanggal_mulai'));
            }
            
            if (request('tanggal_selesai')) {
                $query->whereDate('tanggal', '<=', request('tanggal_selesai'));
            }
            
            $kegiatan = $query->orderBy('tanggal', 'asc')
                           ->orderBy('jam_mulai', 'asc')
                           ->get();
            
            return response()->json([
                'draw' => request()->get('draw'),
                'recordsTotal' => $kegiatan->count(),
                'recordsFiltered' => $kegiatan->count(),
                'data' => $kegiatan->map(function($item, $index) {
                    return [
                        'DT_RowIndex' => $index + 1,
                        'nama_kegiatan' => $item->nama_kegiatan,
                        'person_in_charge' => $item->person_in_charge ?? '-',
                        'unit_kerja' => $item->unitKerja ? $item->unitKerja->nama_unit_kerja : '-',
                        'tanggal_formatted' => $item->tanggal ? date('d/m/Y', strtotime($item->tanggal)) : '-',
                        'jam_formatted' => ($item->jam_mulai && $item->jam_selesai) ? 
                            date('H:i', strtotime($item->jam_mulai)) . ' - ' . date('H:i', strtotime($item->jam_selesai)) : '-',
                        'nama_tempat' => $item->nama_tempat ?? '-',
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
            return response()->json($kegiatan);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Jadwal tidak ditemukan'], 404);
        }
    }
}