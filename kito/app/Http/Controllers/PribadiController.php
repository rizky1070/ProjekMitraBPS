<?php

namespace App\Http\Controllers;

use App\Models\Link;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\categoryUser;

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
    public function daftarLink()
    {
        // Ambil link yang hanya dimiliki oleh user yang sedang login
        $links = Link::with('categoryUser')
            ->where('user_id', Auth::id())
            ->get();
        $categories = CategoryUser::where('user_id', Auth::id())
            ->get(); // Pastikan menggunakan model yang benar
        return view('Setape.pribadi.daftarLink', compact('links', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'link' => 'required|url|max:255',
            'category_user_id' => 'nullable|exists:category_users,id', // Sesuaikan dengan nama tabel
            'status' => 'required'
        ]);

        try {
            Link::create([
                'name' => $request->name,
                'link' => $request->link,
                'category_user_id' => $request->category_user_id,
                'status' => $request->status,
                'user_id' => Auth::id() // Tambahkan user_id
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
            'name' => 'required|string|max:255',
            'link' => 'required|url|max:255',
            'category_user_id' => 'nullable|exists:category_users,id', // Sesuaikan dengan nama tabel
            'status' => 'required' // Tambahkan validasi boolean
        ]);

        try {
            $link = Link::where('id', $id)
                ->where('user_id', Auth::id()) // Pastikan hanya pemilik yang bisa update
                ->firstOrFail();

            $link->update([
                'name' => $request->name,
                'link' => $request->link,
                'category_user_id' => $request->category_user_id,
                'status' => (bool)$request->status // Pastikan status sebagai boolean
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
