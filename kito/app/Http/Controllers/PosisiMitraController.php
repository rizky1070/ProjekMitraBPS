<?php

namespace App\Http\Controllers;

use App\Models\PosisiMitra;
use Illuminate\Http\Request;

class PosisiMitraController extends Controller
{
    public function index(Request $request)
    {
        $query = PosisiMitra::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nama_posisi', 'like', '%' . $search . '%');
        }

        $posisiMitra = $query->get();
        $posisiNames = PosisiMitra::pluck('nama_posisi')->unique()->values()->all();

        return view('mitrabps.crudPosisiMitra', compact('posisiMitra', 'posisiNames'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'nama_posisi' => 'required|string|max:255|unique:posisi_mitra,nama_posisi',
                'rate_honor' => 'required|numeric|min:0',
            ]);

            $posisi = PosisiMitra::create([
                'nama_posisi' => $request->nama_posisi,
                'rate_honor' => $request->rate_honor
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Posisi berhasil ditambahkan',
                'data' => $posisi
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan posisi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'nama_posisi' => 'required|string|max:255|unique:posisi_mitra,nama_posisi,' . $id . ',id_posisi_mitra',
                'rate_honor' => 'required|numeric|min:0',
            ]);

            $posisi = PosisiMitra::findOrFail($id);
            $posisi->update([
                'nama_posisi' => $request->nama_posisi,
                'rate_honor' => $request->rate_honor,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Posisi berhasil diperbarui',
                'data' => $posisi
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui posisi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $posisi = PosisiMitra::findOrFail($id);
            $posisi->delete();

            return response()->json([
                'success' => true,
                'message' => 'Posisi berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus posisi: ' . $e->getMessage()
            ], 500);
        }
    }
}
