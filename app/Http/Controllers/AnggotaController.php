<?php
// app/Http/Controllers/AnggotaController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Anggota;
use App\Models\UnitKerja;

class AnggotaController extends Controller
{
    public function index()
    {
        $unitKerja = UnitKerja::all();
        return view('anggota.index', compact('unitKerja'));
    }

    public function getData()
    {
        try {
            $anggota = Anggota::with('unitKerja')->get();
            
            return response()->json([
                'draw' => request()->get('draw'),
                'recordsTotal' => $anggota->count(),
                'recordsFiltered' => $anggota->count(),
                'data' => $anggota->map(function($item, $index) {
                    return [
                        'DT_RowIndex' => $index + 1,
                        'nama_anggota' => $item->nama_anggota,
                        'email' => $item->email ?? '-',
                        'jabatan' => $item->jabatan ?? '-',
                        'nomor_telepon' => $item->nomor_telepon ?? '-',
                        'unit_kerja' => $item->unitKerja ? $item->unitKerja->nama_unit_kerja : '-',
                        'action' => '<button class="btn-edit px-3 py-1 bg-blue-500 text-white rounded text-sm" data-id="'.$item->id_anggota.'">Edit</button> <button class="btn-delete px-3 py-1 bg-red-500 text-white rounded text-sm ml-2" data-id="'.$item->id_anggota.'">Delete</button>'
                    ];
                })
            ]);

        } catch (\Exception $e) {
            \Log::error('Anggota DataTables Error: ' . $e->getMessage());
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
            'nama_anggota' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'jabatan' => 'nullable|string|max:255',
            'nomor_telepon' => 'nullable|string|max:20',
            'id_unit_kerja' => 'nullable|exists:t_ref_unit_kerja,id_unit_kerja'
        ]);

        Anggota::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Anggota berhasil ditambahkan'
        ]);
    }

    public function show($id)
    {
        $anggota = Anggota::with('unitKerja')->findOrFail($id);
        return response()->json($anggota);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_anggota' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'jabatan' => 'nullable|string|max:255',
            'nomor_telepon' => 'nullable|string|max:20',
            'id_unit_kerja' => 'nullable|exists:t_ref_unit_kerja,id_unit_kerja'
        ]);

        $anggota = Anggota::findOrFail($id);
        $anggota->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Anggota berhasil diupdate'
        ]);
    }

    public function destroy($id)
    {
        try {
            $anggota = Anggota::findOrFail($id);
            $anggota->delete();

            return response()->json([
                'success' => true,
                'message' => 'Anggota berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus anggota'
            ], 500);
        }
    }

    public function getByUnitKerja($unitKerjaId)
    {
        $anggota = Anggota::where('id_unit_kerja', $unitKerjaId)->get();
        return response()->json($anggota);
    }

    public function getAll()
    {
        $anggota = Anggota::with('unitKerja')->get();
        return response()->json($anggota);
    }
}