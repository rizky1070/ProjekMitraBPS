<?php

namespace App\Http\Controllers;

use App\Models\Link;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\categoryUser;
use App\Models\CategoryUser as ModelsCategoryUser;
use Illuminate\Validation\Rule;

class PribadiController extends Controller
{

    public function index()
    {
        $links = Link::with('categoryUser')
            ->where('status', 1)
            ->where('user_id', Auth::id())
            ->get(); // hanya ambil data dengan status = 1
        return view('setape.pribadi.index', compact('links'));
    }

    public function daftarLink(Request $request)
    {
        // Query dasar
        $baseQuery = Link::with('categoryUser')
            ->where('user_id', Auth::id())
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc');

        // Filter kategori
        if ($request->filled('category') && $request->category != 'all') {
            $baseQuery->where('category_user_id', $request->category);
        }

        // Filter pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $baseQuery->where('name', 'like', '%' . $search . '%');
        }

        // Hitung total link yang difilter (tanpa pagination)
        $totalLink = $baseQuery->count();

        // Ambil data dengan pagination
        $links = $baseQuery->paginate(10);

        // Ambil SEMUA kategori milik user (tidak peduli apakah punya link atau tidak)
        $categories = CategoryUser::where('user_id', Auth::id())->get();

        // Ambil nama link hanya dari hasil yang difilter
        $linkNames = $baseQuery->clone()
            ->pluck('name')
            ->unique()
            ->values()
            ->all();

        return view('setape.pribadi.daftarLink', compact('links', 'categories', 'linkNames', 'totalLink'));
    }

    public function togglePin(Request $request, $id)
    {
        try {
            $link = Link::where('id', $id)
                ->where('user_id', Auth::id())
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
        // 1. Validasi data yang masuk
        $validatedData = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                // Validasi kustom untuk memastikan nama unik per kategori
                function ($attribute, $value, $fail) use ($request) {
                    $exists = Link::where('name', $value)
                        ->where('category_user_id', $request->category_user_id)
                        ->exists();
                    if ($exists) {
                        $fail('Nama link sudah digunakan untuk kategori ini.');
                    }
                }
            ],
            // Aturan validasi link menggunakan regex yang fleksibel
            'link' => [
                'required',
                'max:255',
                'regex:/^((https?:\/\/)?[\w\.-]+\.[a-z]{2,6})(\/.*)?$/i'
            ],
            'category_user_id' => 'required|exists:category_users,id',
            'status' => 'required|boolean'
        ], [
            // Pesan-pesan error kustom
            'name.required' => 'Nama harus diisi.',
            'name.string' => 'Nama harus berupa teks.',
            'name.max' => 'Nama tidak boleh lebih dari 255 karakter.',
            'link.required' => 'Link harus diisi.',
            'link.regex' => 'Format link yang Anda masukkan tidak valid.',
            'link.max' => 'Link tidak boleh lebih dari 255 karakter.',
            'category_user_id.required' => 'Kategori harus dipilih.',
            'category_user_id.exists' => 'Kategori yang dipilih tidak valid.',
            'status.required' => 'Status harus dipilih.',
            'status.boolean' => 'Status harus berupa nilai benar atau salah.'
        ]);

        try {
            // 2. Tambahkan user_id yang sedang login ke data
            $validatedData['user_id'] = Auth::id();

            // 3. Buat dan simpan data ke database
            Link::create($validatedData);

            // 4. Kembalikan respons sukses dalam format JSON
            return response()->json([
                'success' => true,
                'message' => 'Link berhasil ditambahkan.'
            ], 201); // 201 Created

        } catch (\Exception $e) {
            // Jika terjadi error, kembalikan respons gagal
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan Link: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $link = Link::findOrFail($id);

        $request->validate([
            'name' => [
                'sometimes',
                'string',
                'max:255',
                // Validasi kustom untuk memeriksa kombinasi name dan category_user_id
                // Validasi nama unik yang mengabaikan ID link saat ini
                Rule::unique('links')->where(function ($query) use ($request, $link) {
                    $categoryUserId = $request->input('category_user_id', $link->category_user_id);
                    return $query->where('category_user_id', $categoryUserId);
                })->ignore($link->id),
            ],

            'link' => [
                'required',
                'max:255',
                'regex:/^((https?:\/\/)?[\w\.-]+\.[a-z]{2,6})(\/.*)?$/i'
            ],
            'category_user_id' => 'sometimes|exists:category_users,id',
            'status' => 'sometimes|boolean'
        ], [
            'name.string' => 'Nama harus berupa teks.',
            'name.max' => 'Nama tidak boleh lebih dari 255 karakter.',
            'link.regex' => 'Format link yang Anda masukkan tidak valid.',
            'link.max' => 'Link tidak boleh lebih dari 255 karakter.',
            'category_user_id.exists' => 'Kategori yang dipilih tidak valid.',
            'status.boolean' => 'Status harus berupa nilai benar atau salah.'
        ]);

        try {
            $link = Link::findOrFail($id);

            $link->update([
                'name' => $request->name ?? $link->name,
                'link' => $request->link ?? $link->link,
                'category_user_id' => $request->category_user_id ?? $link->category_user_id,
                'status' => $request->status ?? $link->status
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Link berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui Link: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $link = Link::where('id', $id)
                ->where('user_id', Auth::id()) // Pastikan hanya pemilik yang bisa hapus
                ->firstOrFail();

            $link->delete();

            return response()->json([
                'success' => true,
                'message' => 'Link berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus Link: ' . $e->getMessage()
            ], 500);
        }
    }
}
