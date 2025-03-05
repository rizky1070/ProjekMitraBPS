<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Barang;
use Illuminate\Http\Request;
use App\Exports\ExportDaftarBarang;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class DaftarBarangController extends Controller
{
    //
    public function index(Request $request)
    {
        // Set the number of items per page
        $perPage = 35; // Change this number as needed

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
        $barangs = $query->orderBy('namabarang', 'asc')->paginate($perPage);


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

        // dd($barangs);

        // Return the view with paginated presences
        return view('siminbar.siminbardaftarbarang', [
            'barangs' => $barangs,
            'pagination' => $barangs // Pass the paginated data correctly to the view
        ]);
    }

    public function getDaftarBarang()
    {
        // Mengambil semua data barang dari database
        $barangs = Barang::all();

        // Jika tidak ada data barang
        if ($barangs->isEmpty()) {
            return response()->json(['message' => 'Barang not found'], 404);
        }

        // Debugging jika ingin melihat data sebelum transform
        // dd($barangs); // Uncomment jika ingin melihat hasil query sebelum transform

        // Mengubah data barang sesuai dengan yang diperlukan
        $barangs = $barangs->transform(function ($barang) {
            return [
                'namabarang' => $barang->namabarang,
                'stoktersedia' => $barang->stoktersedia,
                'gambar' => $barang->gambar,
                'id' => $barang->id,
            ];
        });

        // dd($barangs);

        // Mengembalikan data ke view
        return view('siminbar.siminbarpermintaanbarangform', ['barangs' => $barangs]);
    }

    public function store(Request $request): RedirectResponse
    {
        // Pastikan user sudah login
        if (!auth()->check()) {
            return redirect()->route('/')->with(['error' => 'Anda harus login terlebih dahulu!']);
        }
        // dd($request);
        if ($request->hasFile('gambar')) {
            $image = $request->file('gambar');
            $image->storeAs('public/uploads/images', $image->hashName());
            $image_name = $image->hashName();
        } else {
            $image_name = null;
        }

        // create product
        Barang::create([
            'gambar'         => $image_name,
            'namabarang' => $request->namabarang,
            'deskripsi' => $request->deskripsi,
            'stoktersedia' => $request->stoktersedia,
        ]);
        return redirect()->route('siminbardaftarbarang.index')->with(['success' => 'Data Berhasil Disimpan!']);
    }

    public function update(Request $request, $id)
    {
        $schedule = Barang::findOrFail($id);

        // Proses gambar
        if ($request->hasFile('gambar')) {
            if ($schedule->gambar && Storage::exists('public/uploads/docs/' . $schedule->gambar)) {
                Storage::delete('public/uploads/images/' . $schedule->gambar);
            }
            $image = $request->file('gambar');
            // Upload dan simpan gambar baru
            $image->storeAs('public/uploads/images', $image->hashName());

            // Update kolom gambar dengan gambar baru
            $newImage = $image->hashName();
        } else {
            // Jika tidak ada gambar baru, gunakan gambar lama
            $newImage = $schedule->gambar;
        }


        // Update data schedule
        $schedule->update([
            'gambar'        => $newImage, // Gunakan gambar baru atau yang lama
            'namabarang'      => $request->namabarang,
            'deskripsi'      => $request->deskripsi,
        ]);

        return redirect()->route('siminbardaftarbarang.index')->with(['success' => 'Data Berhasil Diupdate!'], 200);
    }

    public function getDaftarBarangById($id)
    {
        // Mencari presence berdasarkan ID
        $barang = Barang::find($id);

        // Jika event tidak ditemukan
        if (!$barang) {
            return response()->json(['message' => 'Event not found'], 404);
        }

        // Mengembalikan data event dalam format JSON
        $barangData = [
            'namabarang' => $barang->namabarang,
            'stoktersedia' => $barang->stoktersedia,
            'gambar' => $barang->gambar,
            'deskripsi' => $barang->deskripsi,
            'id' => $barang->id,
        ];

        return response()->json($barangData);
    }

    public function daftarBarangFormEdit($id)
    {

        $barangs = Barang::find($id);

        // Jika event tidak ditemukan
        if (!$barangs) {
            return response()->json(['message' => 'Event not found'], 404);
        }
        $barang = [
            'namabarang' => $barangs->namabarang,
            'deskripsi' => $barangs->deskripsi,
            'gambar' => $barangs->gambar,
            'id' => $barangs->id,

        ];

        // Kirim data ke view
        return view('siminbar.siminbardaftarbarangformedit', ['barang' => $barang]);
    }
    public function delete($id)
    {
        $barang = Barang::findOrFail($id);
        $barang->delete();

        return redirect('/siminbardaftarbarang')->with('success', 'Barang berhasil dihapus.');
        // return response()->route('hamuktisuratkeluar.index')->json(['message' => 'Event deleted successfully'], 200);
    }

    public function export_excel()
    {
        $tanggal = Carbon::now()->format('d-m-Y');
        $fileName = 'Daftar Barang ' . $tanggal . '.xlsx';
        return Excel::download(new ExportDaftarBarang, $fileName);
    }
}
