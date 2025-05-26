<?php

namespace App\Http\Controllers;

use App\Models\Office;
use App\Models\Category;
use Illuminate\Http\Request;

class SuperTimController extends Controller
{
    public function index()
    {
        $offices = Office::with('category') // Pastikan selalu load relasi
            ->where('status', 1)
            ->get();

        $categories = Category::all();

        return view('Setape.superTim.index', compact('offices', 'categories'));
    }
    
    public function daftarLink()
    {
        $offices = Office::with('category')->get();
        $categories = Category::all();
        return view('Setape.superTim.daftarLink', compact('offices', 'categories'));
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
            Office::create([
                'name' => $request->name,
                'link' => $request->link,
                'category_id' => $request->category_id,
                'status' => $request->status
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
}
