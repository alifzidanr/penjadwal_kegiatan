<?php
// app/Http/Controllers/JadwalController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kegiatan;
use App\Models\UnitKerja;

class JadwalController extends Controller
{
    public function index()
    {
        $unitKerja = UnitKerja::all();
        return view('jadwal.index', compact('unitKerja'));
    }

    public function getData()
    {
        try {
            // Include unit kerja relationship
            $kegiatan = Kegiatan::with('unitKerja')->get();
            
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
            'tanggal' => 'nullable|date',
            'jam_mulai' => 'nullable|date_format:H:i',
            'jam_selesai' => 'nullable|date_format:H:i|after:jam_mulai',
            'nama_tempat' => 'nullable|string|max:255',
            'id_unit_kerja' => 'nullable|exists:t_ref_unit_kerja,id_unit_kerja'
        ]);

        Kegiatan::create($request->all());

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
                'tanggal' => $kegiatan->tanggal,
                'jam_mulai' => $kegiatan->jam_mulai,
                'jam_selesai' => $kegiatan->jam_selesai,
                'nama_tempat' => $kegiatan->nama_tempat,
                'id_unit_kerja' => $kegiatan->id_unit_kerja,
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
            'tanggal' => 'nullable|date',
            'jam_mulai' => 'nullable|date_format:H:i',
            'jam_selesai' => 'nullable|date_format:H:i|after:jam_mulai',
            'nama_tempat' => 'nullable|string|max:255',
            'id_unit_kerja' => 'nullable|exists:t_ref_unit_kerja,id_unit_kerja'
        ]);

        $kegiatan = Kegiatan::findOrFail($id);
        $kegiatan->update($request->all());

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
}