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

        $offices = $query->paginate(10); // Pagination dengan 10 item per halaman

        // Hanya ambil kategori yang memiliki relasi dengan office yang aktif
        $categories = Category::whereHas('offices', function ($q) {
            $q->where('status', 1); // Hanya kategori dengan office aktif
        })->get();

        // Hanya ambil nama office yang aktif
        $officeNames = Office::where('status', 1)
            ->pluck('name')
            ->unique()
            ->values()
            ->all();

        return view('setape.superTim.index', compact('offices', 'categories', 'officeNames'));
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

        $offices = $query->paginate(10); // Pagination dengan 10 item per halaman

        // Ambil SEMUA kategori tanpa filter apapun
        $categories = Category::all();

        // Ambil nama hanya dari hasil yang difilter
        $officeNames = $query->clone()
            ->pluck('name')
            ->unique()
            ->values()
            ->all();

        return view('setape.superTim.daftarLink', compact('offices', 'categories', 'officeNames'));
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
            'name' => [
                'required',
                'string',
                'max:255',
                // Validasi kustom untuk memeriksa kombinasi name dan category_id
                function ($attribute, $value, $fail) use ($request) {
                    $exists = Office::where('name', $value)
                        ->where('category_id', $request->category_id)
                        ->exists();
                    if ($exists) {
                        $fail('Nama sudah digunakan untuk kategori ini. Silakan pilih nama lain atau kategori lain.');
                    }
                }
            ],
            'link' => 'required|url|max:255',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|boolean'
        ], [
            'name.required' => 'Nama harus diisi.',
            'name.string' => 'Nama harus berupa teks.',
            'name.max' => 'Nama tidak boleh lebih dari 255 karakter.',
            'link.required' => 'Link harus diisi.',
            'link.url' => 'Link harus berupa URL yang valid.',
            'link.max' => 'Link tidak boleh lebih dari 255 karakter.',
            'category_id.required' => 'Kategori harus dipilih.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'status.required' => 'Status harus dipilih.',
            'status.boolean' => 'Status harus berupa nilai benar atau salah.'
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
            'name' => [
                'required',
                'string',
                'max:255',
                // Validasi kustom untuk memeriksa kombinasi name dan category_id
                function ($attribute, $value, $fail) use ($request) {
                    $exists = Office::where('name', $value)
                        ->where('category_id', $request->category_id)
                        ->exists();
                    if ($exists) {
                        $fail('Nama sudah digunakan untuk kategori ini. Silakan pilih nama lain atau kategori lain.');
                    }
                }
            ],
            'link' => 'required|url|max:255',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|boolean'
        ], [
            'name.required' => 'Nama harus diisi.',
            'name.string' => 'Nama harus berupa teks.',
            'name.max' => 'Nama tidak boleh lebih dari 255 karakter.',
            'link.required' => 'Link harus diisi.',
            'link.url' => 'Link harus berupa URL yang valid.',
            'link.max' => 'Link tidak boleh lebih dari 255 karakter.',
            'category_id.required' => 'Kategori harus dipilih.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'status.required' => 'Status harus dipilih.',
            'status.boolean' => 'Status harus berupa nilai benar atau salah.'
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

            $office = Office::findOrFail($request->office_id);
            $office->update([
                'status' => $statusValue
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diperbarui',
                'new_status' => $statusValue
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

    public function keepLink(Request $request, $id)
    {
        try {
            // Dapatkan user yang sedang login
            $userId = Auth::id();

            // Dapatkan data Office yang akan disalin
            $office = Office::findOrFail($id);

            // Dapatkan atau buat CategoryUser 'Kelompok Kerja' untuk user ini
            $categoryUser = \DB::table('category_users')
                ->where('name', 'Kelompok Kerja')
                ->where('user_id', $userId)
                ->first();

            // Jika kategori tidak ada, buat baru
            if (!$categoryUser) {
                $categoryUserId = \DB::table('category_users')->insertGetId([
                    'name' => 'Kelompok Kerja',
                    'user_id' => $userId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $categoryUserId = $categoryUser->id;
            }

            // Simpan data ke tabel Link
            \DB::table('links')->insert([
                'name' => $office->name,
                'category_user_id' => $categoryUserId,
                'link' => $office->link,
                'status' => 1, // Status aktif
                'priority' => 0, // Tidak disematkan
                'created_at' => now(),
                'updated_at' => now(),
                'user_id' => $userId,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Link berhasil disimpan ke koleksi pribadi'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan link: ' . $e->getMessage()
            ], 500);
        }
    }
}