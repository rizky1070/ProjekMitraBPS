<?php

namespace App\Http\Controllers;
use App\Models\Ketua;
use Illuminate\Http\Request;
use App\Models\Category;

class SekretariatController extends Controller
{

    public function index(Request $request)
    {
        $query = Ketua::with('category')->where('status', 1); // Hanya ambil yang aktif

        // Filter kategori - hanya jika ada dan tidak kosong dan bukan 'all'
        if ($request->filled('category') && $request->category != 'all') {
            $query->where('category_id', $request->category);
        }

        // Filter pencarian - hanya jika ada dan tidak kosong
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', '%' . $search . '%');
        }

        $ketuas = $query->get();
        
        // Hanya ambil kategori yang memiliki relasi dengan ketua yang aktif
        $categories = Category::whereHas('ketuas', function($q) {
            $q->where('status', 1); // Hanya kategori dengan ketua aktif
        })->get();
        
        // Hanya ambil nama ketua yang aktif
        $ketuaNames = Ketua::where('status', 1)
                            ->pluck('name')
                            ->unique()
                            ->values()
                            ->all();

        return view('Setape.sekretariat.index', compact('ketuas', 'categories', 'ketuaNames'));
    }

    public function daftarLink(Request $request)
    {
        $query = Ketua::with('category');

        // Filter kategori
        if ($request->filled('category') && $request->category != 'all') {
            $query->where('category_id', $request->category);
        }

        // Filter pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', '%' . $search . '%');
        }

        // Filter status
        if ($request->filled('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        $ketuas = $query->get();
        
        $categories = Category::whereHas('ketuas')->get();
        
        $ketuaNames = Ketua::pluck('name')
                            ->unique()
                            ->values()
                            ->all();

        return view('Setape.sekretariat.daftarLink', compact('ketuas', 'categories', 'ketuaNames'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'link' => 'required|url|max:255',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|boolean'
        ]);

        try {
            ketua::create([
                'name' => $request->name,
                'link' => $request->link,
                'category_id' => $request->category_id,
                'status' => $request->status,
                'user_id' => Auth::id()
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

        public function updateStatus(Request $request)
    {
        $request->validate([
            'ketua_id' => 'required|exists:ketuas,id',
            'status' => 'required|boolean'
        ]);

        try {
            $ketua = Ketua::findOrFail($request->ketua_id);
            $ketua->update(['status' => $request->status]);

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui status: ' . $e->getMessage()
            ], 500);
        }
    }
}
