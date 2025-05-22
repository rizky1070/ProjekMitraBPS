<?php

namespace App\Http\Controllers;

use App\Models\CategoryUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KategoriPribadiController extends Controller
{
    public function index()
    {
        // Ambil data CategoryUser berdasarkan user yang sedang login
        $categoryuser = CategoryUser::where('user_id', Auth::id())->get();
        
        return view('Setape.kategoriPribadi.daftar', compact('categoryuser'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:category_users,name,NULL,id,user_id,'.Auth::id()
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.string' => 'Nama kategori harus berupa teks.',
            'name.max' => 'Nama kategori maksimal 255 karakter.',
            'name.unique' => 'Nama kategori sudah digunakan untuk akun Anda.'
        ]);

        try {
            CategoryUser::create([
                'name' => $request->name,
                'user_id' => Auth::id()
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
            'name' => 'required|string|max:255|unique:category_users,name,'.$id.',id,user_id,'.Auth::id()
        ], [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.string' => 'Nama kategori harus berupa teks.',
            'name.max' => 'Nama kategori maksimal 255 karakter.',
            'name.unique' => 'Nama kategori sudah digunakan untuk akun Anda.'
        ]);

        try {
            $category = CategoryUser::where('user_id', Auth::id())->findOrFail($id);
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
            $category = CategoryUser::where('user_id', Auth::id())->findOrFail($id);
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