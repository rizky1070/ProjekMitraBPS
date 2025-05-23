<?php

namespace App\Http\Controllers;
use App\Models\Ketua;
use Illuminate\Http\Request;
use App\Models\Category;

class SekretariatController extends Controller
{
    public function index()
    {
        $ketuas = ketua::with('category')
                    ->where('status', 1)
                    ->get(); // assuming you have relation with category
        return view('Setape.sekretariat.index', compact('ketuas'));
    }

    public function daftarLink()
    {
        $ketuas = ketua::with('category')->get(); // assuming you have relation with category
        $categories = Category::all();
        return view('Setape.sekretariat.daftarLink', compact('ketuas','categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'link' => 'required|url|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'status' => 'required|boolean'
        ]);

        try {
            ketua::create([
                'name' => $request->name,
                'link' => $request->link,
                'category_id' => $request->category_id,
                'status' => $request->status
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Sekretariat berhasil ditambahkan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan Sekretariat: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'link' => 'required|url|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'status' => 'required'
        ]);

        try {
            $ketua = ketua::findOrFail($id);
            $ketua->update([
                'name' => $request->name,
                'link' => $request->link,
                'category_id' => $request->category_id,
                'status' => $request->status
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Sekretariat berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui Sekretariat: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $ketua = ketua::findOrFail($id);
            $ketua->delete();

            return response()->json([
                'success' => true,
                'message' => 'Sekretariat berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus Sekretariat: ' . $e->getMessage()
            ], 500);
        }
    }
}
