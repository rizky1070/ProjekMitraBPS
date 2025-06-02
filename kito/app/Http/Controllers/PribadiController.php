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
        return view('Setape.pribadi.index', compact('links'));
    }

    public function daftarLink(Request $request)
    {
        $query = Link::with('categoryUser')
            ->where('user_id', Auth::id())
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc');

        // Filter kategori
        if ($request->filled('category') && $request->category != 'all') {
            $query->where('category_user_id', $request->category);
        }

        // Filter pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', '%' . $search . '%');
        }

        $links = $query->paginate(10);

        // Ambil SEMUA kategori milik user (tidak peduli apakah punya link atau tidak)
        $categories = CategoryUser::where('user_id', Auth::id())->get();

        // Ambil nama link hanya dari hasil yang difilter
        $linkNames = $query->clone()
            ->pluck('name')
            ->unique()
            ->values()
            ->all();

        return view('Setape.pribadi.daftarLink', compact('links', 'categories', 'linkNames'));
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
            'name' => 'required|string|max:255',
            'link' => 'required|url|max:255',
            'category_user_id' => 'required|exists:category_users,id', // Diubah dari nullable ke required
            'status' => 'required'
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
            'name' => 'sometimes|string|max:255',
            'link' => 'sometimes|url|max:255',
            'category_user_id' => 'sometimes|nullable|exists:category_users,id',
            'status' => 'sometimes|boolean'
        ]);

        try {
            $link = Link::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            // Ambil hanya data yang ada di request
            $updateData = $request->only(['name', 'link', 'category_user_id', 'status']);

            // Jika 'status' ada di request, pastikan boolean
            if ($request->has('status')) {
                $updateData['status'] = (bool)$request->status;
            }

            // Hapus key yang bernilai null (jika tidak ingin mengubah field tertentu)
            $updateData = array_filter($updateData, function ($value) {
                return $value !== null; // Hanya update jika bukan null
            });

            $link->update($updateData);

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
