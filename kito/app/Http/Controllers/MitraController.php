<?php

namespace App\Http\Controllers;
use App\Models\Survei;
use App\Models\Mitra;
use Illuminate\Http\Request;
use App\Models\Provinsi;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Desa;
use App\Models\MitraSurvei;


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
        $mitras = $mitras->paginate(3);
    
        // Daftar kecamatan untuk dropdown filter
        $kecamatans = Kecamatan::pluck('nama_kecamatan', 'id_kecamatan');
    
        return view('mitrabps.daftarMitra', compact('mitras', 'kecamatans'));
    }
    


    public function profilMitra($id_mitra)
    {
        $mits = Mitra::with(['kecamatan', 'desa'])->findOrFail($id_mitra);
        $survei = MitraSurvei::with('survei')->where('id_mitra', $id_mitra)->get();

        return view('mitrabps.profilMitra', compact('mits', 'survei'));
    }

    public function penilaianMitra($id_survei)
    {
        $surMit = MitraSurvei::with('survei')->where('id_survei', $id_survei)->first();

        return view('mitrabps.penilaianMitra', compact('surMit'));
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
