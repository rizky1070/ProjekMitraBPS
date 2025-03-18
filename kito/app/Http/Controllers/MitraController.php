<?php

namespace App\Http\Controllers;
use App\Models\Survei;
use App\Models\Mitra;
use Illuminate\Http\Request;
use App\Models\Provinsi;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Desa;


class MitraController extends Controller
{
   
    public function index(Request $request)
    {
        // Query awal dengan relasi
        $mitras = Mitra::with('kecamatan')
                       ->withCount('mitraSurvei');
    
        // Filter nama mitra
        if ($request->filled('search')) {
            $mitras->where('nama_lengkap', 'like', '%' . $request->search . '%');
        }
    
        // Filter kecamatan
        if ($request->filled('kecamatan')) {
            $mitras->whereHas('kecamatan', function ($query) use ($request) {
                $query->where('nama_kecamatan', 'like', '%' . $request->kecamatan . '%');
            });
        }
    
        // Pagination
        $mitras = $mitras->paginate(10);
    
        // Daftar kecamatan untuk dropdown filter
        $kecamatans = Kecamatan::pluck('nama_kecamatan', 'id_kecamatan');
    
        return view('mitrabps.daftarMitra', compact('mitras', 'kecamatans'));
    }
    


    public function profilMitra($id_mitra)
    {
        $mits = Mitra::with(['desa.kecamatan.kabupaten.provinsi']) // Pastikan relasi berurutan
                    ->withCount('mitraSurvei')
                    ->where('id_mitra', $id_mitra)
                    ->get();

        if ($mits->isEmpty()) {
            abort(404, 'Mitra tidak ditemukan');
        }

        return view('mitrabps.profilMitra', compact('mits'));
    }



//     public function index(Request $request)
// {
//     $query = Mitra::with('kecamatan')->withCount('survei');

//     // Filter berdasarkan kata kunci pencarian jika ada
//     if ($request->filled('search')) {
//         $query->where('nama_lengkap', 'like', '%' . $request->search . '%');
//     }

//     $mitras = $query->paginate(10); // Jalankan query di sini

//     return view('mitrabps.daftarmitrabps', compact('mitras'));
// }

}
