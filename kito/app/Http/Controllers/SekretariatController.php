<?php

namespace App\Http\Controllers;

use App\Models\Ketua;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class SekretariatController extends Controller
{

    public function index(Request $request)
    {
        // Jalankan addKelompokKerjaCategory untuk user yang sedang login
        if (Auth::check()) {
            $this->addKelompokKerjaCategory(Auth::id());
        }

        $query = Ketua::with('category')
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

        $ketuas = $query->paginate(10); // Pagination dengan 10 item per halaman

        // Hanya ambil kategori yang memiliki relasi dengan ketua yang aktif
        $categories = Category::whereHas('ketuas', function ($q) {
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
        $query = Ketua::with('category')
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

        $ketuas = $query->paginate(10); // Pagination dengan 10 item per halaman

        // Ambil SEMUA kategori tanpa filter apapun
        $categories = Category::all();

        // Ambil nama ketua hanya dari hasil yang difilter
        $ketuaNames = $query->clone()
            ->pluck('name')
            ->unique()
            ->values()
            ->all();

        return view('Setape.sekretariat.daftarLink', compact('ketuas', 'categories', 'ketuaNames'));
    }

    public function togglePin(Request $request, $id)
    {
        try {
            $link = Ketua::where('id', $id)
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
                    $exists = Ketua::where('name', $value)
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
            'name' => [
                'required',
                'string',
                'max:255',
                // Validasi kustom untuk memeriksa kombinasi name dan category_id
                function ($attribute, $value, $fail) use ($request) {
                    $exists = Ketua::where('name', $value)
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

            // Dapatkan data Ketua yang akan disalin
            $ketua = Ketua::findOrFail($id);

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
                'name' => $ketua->name,
                'category_user_id' => $categoryUserId,
                'link' => $ketua->link,
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