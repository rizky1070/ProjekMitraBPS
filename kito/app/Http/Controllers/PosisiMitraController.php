<?php

namespace App\Http\Controllers;

use App\Models\PosisiMitra;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; // Import Rule

class PosisiMitraController extends Controller
{
    /**
     * Menampilkan daftar posisi mitra.
     */
    public function index(Request $request)
    {
        $query = PosisiMitra::query();

        if ($request->filled('search')) {
            $query->where('nama_posisi', 'like', '%' . $request->search . '%');
        }

        $posisiMitra = $query->orderBy('nama_posisi')->paginate(10);
        $posisiNames = PosisiMitra::pluck('nama_posisi')->unique()->sort()->values()->all();

        return view('mitrabps.crudPosisiMitra', compact('posisiMitra', 'posisiNames'));
    }

    /**
     * Menyimpan posisi mitra baru. (Disesuaikan untuk Web Route)
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_posisi' => 'required|string|max:255|unique:posisi_mitra,nama_posisi',
        ], [
            'nama_posisi.required' => 'Nama posisi wajib diisi.',
            'nama_posisi.unique' => 'Nama posisi tersebut sudah ada.',
        ]);

        PosisiMitra::create($request->only('nama_posisi'));

        return redirect()->route('posisi.index')->with('success', 'Posisi mitra baru berhasil ditambahkan.');
    }

    /**
     * Memperbarui nama posisi mitra. (Disesuaikan untuk Web Route)
     */
    public function update(Request $request, $id)
    {
        $posisi = PosisiMitra::findOrFail($id);

        $request->validate([
            'nama_posisi' => [
                'required',
                'string',
                'max:255',
                Rule::unique('posisi_mitra')->ignore($posisi->id_posisi_mitra, 'id_posisi_mitra'),
            ],
        ], [
            'nama_posisi.required' => 'Nama posisi wajib diisi.',
            'nama_posisi.unique' => 'Nama posisi tersebut sudah ada.',
        ]);

        $posisi->update($request->only('nama_posisi'));

        return redirect()->route('posisi.index')->with('success', 'Posisi mitra berhasil diperbarui.');
    }

    /**
     * Menghapus posisi mitra. (Disesuaikan untuk Web Route)
     */
    public function destroy($id)
    {
        $posisi = PosisiMitra::findOrFail($id);

        if ($posisi->mitraSurvei()->exists()) {
            return back()->withErrors(['delete' => 'Gagal menghapus! Posisi ini masih digunakan oleh setidaknya satu mitra dalam survei.']);
        }

        $posisi->delete();

        return redirect()->route('posisi.index')->with('success', 'Posisi mitra berhasil dihapus.');
    }
}
