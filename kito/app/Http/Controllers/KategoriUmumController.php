<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Link; // Pastikan model Link di-import
use Illuminate\Http\Request;

class KategoriUmumController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::query();

        // Filter pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', '%' . $search . '%');
        }

        $categories = $query->paginate(10);

        $kategoriNames = Category::pluck('name')
                    ->unique()
                    ->values()
                    ->all();

        return view('setape.kategoriUmum.daftar', compact('categories', 'kategoriNames'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name'
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.string' => 'Nama kategori harus berupa teks.',
            'name.max' => 'Nama kategori maksimal 255 karakter.',
            'name.unique' => 'Nama kategori sudah digunakan.'
        ]);

        try {
            Category::create([
                'name' => $request->name
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil ditambahkan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan kategori: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $id
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.string' => 'Nama kategori harus berupa teks.',
            'name.max' => 'Nama kategori maksimal 255 karakter.',
            'name.unique' => 'Nama kategori sudah digunakan.'
        ]);

        try {
            $category = Category::findOrFail($id);
            $category->update([
                'name' => $request->name
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui kategori: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $category = Category::withCount(['ketuas', 'offices'])->findOrFail($id);

            // Cek apakah kategori digunakan di tabel lain
            if ($category->ketuas_count > 0 || $category->offices_count > 0) {
                $usedIn = [];
                if ($category->ketuas_count > 0) {
                    $usedIn[] = $category->ketuas_count . ' ketua';
                }
                if ($category->offices_count > 0) {
                    $usedIn[] = $category->offices_count . ' office';
                }

                return response()->json([
                    'success' => false,
                    'message' => 'Kategori tidak dapat dihapus karena digunakan pada: ' . implode(', ', $usedIn)
                ], 422);
            }

            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus kategori: ' . $e->getMessage()
            ], 500);
        }
    }
}