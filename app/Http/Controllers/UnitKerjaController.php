<?php
// app/Http/Controllers/UnitKerjaController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UnitKerja;

class UnitKerjaController extends Controller
{
    public function index()
    {
        return view('unit-kerja.index');
    }

    public function getData()
    {
        try {
            $unitKerja = UnitKerja::all();
            
            return response()->json([
                'draw' => request()->get('draw'),
                'recordsTotal' => $unitKerja->count(),
                'recordsFiltered' => $unitKerja->count(),
                'data' => $unitKerja->map(function($item, $index) {
                    return [
                        'DT_RowIndex' => $index + 1,
                        'nama_unit_kerja' => $item->nama_unit_kerja,
                        'created_at' => $item->created_at ? $item->created_at->format('d/m/Y H:i') : '-',
                        'action' => '<button class="btn-edit px-3 py-1 bg-blue-500 text-white rounded text-sm" data-id="'.$item->id_unit_kerja.'">Edit</button> <button class="btn-delete px-3 py-1 bg-red-500 text-white rounded text-sm ml-2" data-id="'.$item->id_unit_kerja.'">Delete</button>'
                    ];
                })
            ]);

        } catch (\Exception $e) {
            \Log::error('Unit Kerja DataTables Error: ' . $e->getMessage());
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
            'nama_unit_kerja' => 'required|string|max:255|unique:t_ref_unit_kerja,nama_unit_kerja'
        ]);

        UnitKerja::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Unit kerja berhasil ditambahkan'
        ]);
    }

    public function show($id)
    {
        $unitKerja = UnitKerja::findOrFail($id);
        return response()->json($unitKerja);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_unit_kerja' => 'required|string|max:255|unique:t_ref_unit_kerja,nama_unit_kerja,' . $id . ',id_unit_kerja'
        ]);

        $unitKerja = UnitKerja::findOrFail($id);
        $unitKerja->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Unit kerja berhasil diupdate'
        ]);
    }

    public function destroy($id)
    {
        try {
            $unitKerja = UnitKerja::findOrFail($id);
            
            // Check if unit kerja is being used in kegiatan
            $kegiatanCount = $unitKerja->kegiatan()->count();
            if ($kegiatanCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unit kerja tidak dapat dihapus karena masih digunakan dalam ' . $kegiatanCount . ' kegiatan'
                ], 422);
            }
            
            $unitKerja->delete();

            return response()->json([
                'success' => true,
                'message' => 'Unit kerja berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus unit kerja'
            ], 500);
        }
    }
}