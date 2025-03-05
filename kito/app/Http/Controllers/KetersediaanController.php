<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;

class KetersediaanController extends Controller
{
    //
    public function index(Request $request)
    {
        // Set the number of items per page
        $perPage = 10; // Change this number as needed

        // Retrieve the start and end dates from the request
        $searchKeyword = $request->input('search'); // Ambil kata kunci pencarian

        // Initialize the query
        $query = Barang::query();

        // Apply search filter if keyword is provided
        if ($searchKeyword) {
            $query->where(function ($query) use ($searchKeyword) {
                $query->where('namabarang', 'like', '%' . $searchKeyword . '%'); // Mencari berdasarkan kegiatan
            });
        }

        // Execute the query, paginate, and retrieve the results
        $barangs = $query->orderBy('stoktersedia', 'desc')->paginate($perPage);


        // Map the results to transform the 'absen' format
        $barangs->getCollection()->transform(function ($barang) {
            return [ // yg ada di tampilan tabel
                'namabarang' => $barang->namabarang,
                'stoktersedia' => $barang->stoktersedia,
                'gambar' => $barang->gambar,
                'deskripsi' => $barang->deskripsi,
                'id' => $barang->id,
            ];
        });

        // Return the view with paginated presences
        return view('siminbar.siminbarketersediaan', [
            'barangs' => $barangs,
            'pagination' => $barangs // Pass the paginated data correctly to the view
        ]);
    }
}
