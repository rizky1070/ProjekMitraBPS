<?php

namespace App\Http\Controllers;

use App\Models\PosisiMitra;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PosisiMitraController extends Controller
{
    /**
     * Menampilkan daftar posisi mitra dengan filter pencarian dan paginasi.
     */
    public function index(Request $request)
    {
        $query = PosisiMitra::query();

        if ($request->filled('search')) {
            $query->where('nama_posisi', 'like', '%' . $request->search . '%');
        }

        $posisiMitra = $query->orderBy('nama_posisi')->paginate(10);
        $posisiNames = PosisiMitra::pluck('nama_posisi')->unique()->values()->all();

        return view('mitrabps.crudPosisiMitra', compact('posisiMitra', 'posisiNames'));
    }

    /**
     * Menyimpan posisi mitra baru ke database.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nama_posisi' => 'required|string|max:255|unique:posisi_mitra,nama_posisi',
            ], [
                'nama_posisi.required' => 'Nama posisi wajib diisi.',
                'nama_posisi.unique' => 'Nama posisi sudah digunakan.',
            ]);

            $posisi = PosisiMitra::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Posisi berhasil ditambahkan.',
                'data' => $posisi
            ]);
        } catch (ValidationException $e) {
            $errorMsg = collect($e->errors())->first()[0];
            return response()->json(['success' => false, 'message' => $errorMsg], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menambahkan posisi: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Memperbarui nama posisi mitra yang ada.
     */
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'nama_posisi' => 'required|string|max:255|unique:posisi_mitra,nama_posisi,' . $id . ',id_posisi_mitra',
            ], [
                'nama_posisi.required' => 'Nama posisi wajib diisi.',
                'nama_posisi.unique' => 'Nama posisi sudah digunakan.',
            ]);

            $posisi = PosisiMitra::findOrFail($id);
            $posisi->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Posisi berhasil diperbarui.',
                'data' => $posisi
            ]);
        } catch (ValidationException $e) {
            $errorMessage = collect($e->errors())->first()[0];
            return response()->json(['success' => false, 'message' => $errorMessage], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui posisi: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Menghapus posisi mitra dari database.
     */
    public function destroy($id)
    {
        try {
            $posisi = PosisiMitra::findOrFail($id);

            // Tambahkan pengecekan jika posisi masih digunakan
            if ($posisi->mitraSurvei()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus: Posisi ini masih digunakan oleh mitra dalam survei.'
                ], 409); // 409 Conflict
            }

            $posisi->delete();

            return response()->json([
                'success' => true,
                'message' => 'Posisi berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus posisi: ' . $e->getMessage()
            ], 500);
        }
    }
}
