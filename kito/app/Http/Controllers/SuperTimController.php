<?php

namespace App\Http\Controllers;

use App\Models\Office;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperTimController extends Controller
{
    public function index(Request $request)
    {
        // Jalankan addKelompokKerjaCategory untuk user yang sedang login
        if (Auth::check()) {
            $this->addKelompokKerjaCategory(Auth::id());
        }

        $query = Office::with('category')
            ->where('status', 1) // Hanya ambil yang aktif
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc');

        // Filter kategori - hanya jika ada dan tidak kosong dan bukan 'all'
        if ($request->filled('category') && $request->category != 'all') {
            $query->where('category_id', $request->category);
        }

        // Filter pencarian - hanya jika ada dan tidak kosong
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', '%' . $search . '%');
        }

        $offices = $query->get();
        
        // Hanya ambil kategori yang memiliki relasi dengan office yang aktif
        $categories = Category::whereHas('offices', function($q) {
            $q->where('status', 1); // Hanya kategori dengan office aktif
        })->get();
        
        // Hanya ambil nama office yang aktif
        $officeNames = Office::where('status', 1)
                            ->pluck('name')
                            ->unique()
                            ->values()
                            ->all();

        return view('Setape.superTim.index', compact('offices', 'categories', 'officeNames'));
    }

    public function daftarLink(Request $request)
    {
        $query = Office::with('category')
            ->orderBy('priority', 'desc')
            ->orderBy('status', 'desc') // Urutkan berdasarkan status (aktif di atas)
            ->orderBy('created_at', 'desc');
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

        $offices = $query->get();
        
        // Ambil SEMUA kategori tanpa filter apapun
        $categories = Category::all();
        
        // Ambil nama hanya dari hasil yang difilter
        $officeNames = $query->clone()
                        ->pluck('name')
                        ->unique()
                        ->values()
                        ->all();

        return view('Setape.superTim.daftarLink', compact('offices', 'categories', 'officeNames'));
    }
    
    public function togglePin(Request $request, $id)
    {
        try {
            $link = Office::where('id', $id)
                ->firstOrFail();

            $link->update([
                'priority' => !$link->priority
            ]);

            return response()->json([
                'success' => true,
                'priority' => $link->priority,
                'message' => $link->priority ? 'Link disematkan' : 'Link tidak disematkan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status pin: ' . $e->getMessage()
            ], 500);
        }
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
            Office::create([
                'name' => $request->name,
                'link' => $request->link,
                'category_id' => $request->category_id,
                'status' => $request->status,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Super Tim berhasil ditambahkan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan Super Tim: ' . $e->getMessage()
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
            $office = Office::findOrFail($id);
            $office->update([
                'name' => $request->name,
                'link' => $request->link,
                'category_id' => $request->category_id,
                'status' => $request->status
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Super Tim berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui Super Tim: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $office = Office::findOrFail($id);
            $office->delete();

            return response()->json([
                'success' => true,
                'message' => 'Super Tim berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus Super Tim: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'office_id' => 'required|exists:offices,id',
            'status' => 'required|in:active,inactive'
        ]);

        try {
            $statusValue = $request->status === 'active' ? 1 : 0;
            
            Office::where('id', $request->office_id)->update([
                'status' => $statusValue
            ]);

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

    public function addKelompokKerjaCategory($id)
    {
        // Check if the record already exists
        $exists = \DB::table('category_users')
            ->where('name', 'Kelompok Kerja')
            ->where('user_id', $id)
            ->exists();

        if (!$exists) {
            \DB::table('category_users')->insert([
                'id' => 0,
                'name' => 'Kelompok Kerja',
                'user_id' => $id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
