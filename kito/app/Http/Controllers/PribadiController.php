<?php

namespace App\Http\Controllers;

use App\Models\Link;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\categoryUser;
use App\Models\CategoryUser as ModelsCategoryUser;

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
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                // Validasi kustom untuk memeriksa kombinasi name dan category_user_id
                function ($attribute, $value, $fail) use ($request) {
                    $exists = Link::where('name', $value)
                        ->where('category_user_id', $request->category_user_id)
                        ->exists();
                    if ($exists) {
                        $fail('Nama sudah digunakan untuk kategori ini. Silakan pilih nama lain atau kategori lain.');
                    }
                }
            ],
            'link' => 'required|url|max:255',
            'category_user_id' => 'required|exists:category_users,id',
            'status' => 'required|boolean'
        ], [
            'name.required' => 'Nama harus diisi.',
            'name.string' => 'Nama harus berupa teks.',
            'name.max' => 'Nama tidak boleh lebih dari 255 karakter.',
            'link.required' => 'Link harus diisi.',
            'link.url' => 'Link harus berupa URL yang valid.',
            'link.max' => 'Link tidak boleh lebih dari 255 karakter.',
            'category_user_id.required' => 'Kategori harus dipilih.',
            'category_user_id.exists' => 'Kategori yang dipilih tidak valid.',
            'status.required' => 'Status harus dipilih.',
            'status.boolean' => 'Status harus berupa nilai benar atau salah.'
        ]);

        try {
            Link::create([
                'name' => $request->name,
                'link' => $request->link,
                'category_user_id' => $request->category_user_id,
                'status' => $request->status,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Link berhasil ditambahkan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan Link: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => [
                'sometimes',
                'string',
                'max:255',
                // Validasi kustom untuk memeriksa kombinasi name dan category_user_id
                function ($attribute, $value, $fail) use ($request) {
                    $exists = Link::where('name', $value)
                        ->where('category_user_id', $request->category_user_id)
                        ->exists();
                    if ($exists) {
                        $fail('Nama sudah digunakan untuk kategori ini. Silakan pilih nama lain atau kategori lain.');
                    }
                }
            ],
            'link' => 'sometimes|url|max:255',
            'category_user_id' => 'sometimes|exists:category_users,id',
            'status' => 'sometimes|boolean'
        ], [
            'name.string' => 'Nama harus berupa teks.',
            'name.max' => 'Nama tidak boleh lebih dari 255 karakter.',
            'link.url' => 'Link harus berupa URL yang valid.',
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