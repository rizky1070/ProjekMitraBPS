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
            ], [
                'nama_posisi.required' => 'Nama posisi wajib diisi',
                'nama_posisi.string' => 'Nama posisi harus berupa teks',
                'nama_posisi.max' => 'Nama posisi maksimal 255 karakter',
                'nama_posisi.unique' => 'Nama posisi sudah digunakan',
                'rate_honor.required' => 'Rate honor wajib diisi',
                'rate_honor.numeric' => 'Rate honor harus berupa angka',
                'rate_honor.min' => 'Rate honor minimal 0',
            ]);

            $posisi = PosisiMitra::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Posisi berhasil ditambahkan',
                'data' => $posisi
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Ambil pesan error pertama dari validator
            $errorMsg = collect($e->errors())->first()[0];

            return response()->json([
                'success' => false,
                'message' => $errorMsg // Sekarang akan menampilkan misalnya "Nama posisi sudah digunakan"
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
            ], [
                'nama_posisi.required' => 'Nama posisi wajib diisi',
                'nama_posisi.string' => 'Nama posisi harus berupa teks',
                'nama_posisi.max' => 'Nama posisi maksimal 255 karakter',
                'nama_posisi.unique' => 'Nama posisi sudah digunakan',
                'rate_honor.required' => 'Rate honor wajib diisi',
                'rate_honor.numeric' => 'Rate honor harus berupa angka',
                'rate_honor.min' => 'Rate honor minimal 0',
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
            // Ambil pesan error pertama dari validator
            $errorMessage = collect($e->errors())->first()[0];

            return response()->json([
                'success' => false,
                'message' => $errorMessage // Menampilkan pesan error spesifik
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
