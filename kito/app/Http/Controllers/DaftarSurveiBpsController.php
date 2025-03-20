<?php

namespace App\Http\Controllers;
use App\Models\Survei;
use App\Models\Mitra;
use App\Models\Kecamatan;
use App\Models\MitraSurvei;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SurveiImport;
use Exception; // Untuk menangani error
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DaftarSurveiBpsController extends Controller
{
    public function index(Request $request)
    {
        // Mendapatkan daftar tahun yang tersedia dari tabel survei
        $availableYears = Survei::selectRaw('YEAR(jadwal_kegiatan) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        // Mendapatkan daftar kecamatan
        $kecamatans = Kecamatan::all(); 
        
            // Mendapatkan bulan yang dipilih dari request
        $bulan = $request->filled('bulan') ? $request->bulan : null;

        // Query untuk mengambil data survei dan filter berdasarkan bulan
        $surveys = Survei::with('kecamatan', 'mitraSurvei')
                        ->withCount('mitraSurvei')
                        ->when($bulan, function ($query) use ($bulan) {
                            return $query->whereMonth('jadwal_kegiatan', $bulan); // Filter berdasarkan bulan
                        });

        // Filter berdasarkan tahun jika ada parameter tahun
        if ($request->filled('tahun')) {
            $surveys->whereYear('jadwal_kegiatan', $request->tahun); 
        }

        // Filter berdasarkan bulan jika ada parameter bulan
        if ($request->filled('bulan')) {
            $surveys->whereMonth('jadwal_kegiatan', $request->bulan);  
        }

        // Filter berdasarkan kata kunci pencarian jika ada
        if ($request->filled('search')) {
            $surveys->where('nama_survei', 'like', '%' . $request->search . '%');
        }

        // Filter berdasarkan kecamatan jika ada parameter kecamatan
        if ($request->filled('kecamatan')) {
            $surveys->where('id_kecamatan', $request->kecamatan); 
        }

        // Mendapatkan mitra yang terlibat lebih dari satu survei di bulan yang dipilih
        $mitraWithMultipleSurveysInMonth = Mitra::select('mitra.id_mitra', 'mitra.nama_lengkap')
            ->join('mitra_survei', 'mitra_survei.id_mitra', '=', 'mitra.id_mitra')
            ->join('survei', 'survei.id_survei', '=', 'mitra_survei.id_survei')
            ->whereMonth('survei.jadwal_kegiatan', $bulan) // Filter berdasarkan bulan survei
            ->groupBy('mitra.id_mitra', 'mitra.nama_lengkap')
            ->havingRaw('COUNT(DISTINCT mitra_survei.id_survei) > 1')
            ->get(); // Dapatkan semua data mitra yang mengikuti lebih dari satu survei pada bulan ini

        // Menampilkan data survei dengan paginasi
        $surveys = $surveys->paginate(10); 

        return view('mitrabps.daftarSurvei', compact('surveys', 'availableYears', 'kecamatans', 'mitraWithMultipleSurveysInMonth', 'bulan'));
    }

    
    public function tambahKeSurvei(Request $request, $id_survei)
    {
        // Ambil data survei berdasarkan ID
        $survey = Survei::with('kecamatan')
            ->where('id_survei', $id_survei)
            ->firstOrFail();

        // Query untuk daftar mitra dengan relasi jumlah survei
        $mitras = Mitra::with('kecamatan')
            ->withCount('mitraSurvei');

        // Filter berdasarkan kecamatan jika dipilih
        if ($request->filled('kecamatan')) {
            $mitras->where('id_kecamatan', $request->kecamatan);
        }

        // Filter berdasarkan pencarian nama mitra
        if ($request->filled('search')) {
            $mitras->where('nama_lengkap', 'like', '%' . $request->search . '%');
        }

        // Eksekusi query
        $mitras = $mitras->get();

        // Ambil daftar kecamatan untuk dropdown
        $kecamatans = Kecamatan::select('id_kecamatan', 'nama_kecamatan')->get();

        foreach ($mitras as $mitra) {
            // Menambahkan properti isFollowingSurvey secara dinamis
            $mitra->setAttribute('isFollowingSurvey', $mitra->mitraSurvei->contains('id_survei', $id_survei));
        }

        // Urutkan agar mitra yang mengikuti survei tampil di atas
        $mitras = $mitras->sortByDesc(fn($mitra) => $mitra->isFollowingSurvey ? 1 : 0);

        return view('mitrabps.pilihSurvei', compact('survey', 'mitras', 'kecamatans'));
    }



    public function editSurvei(Request $request, $id_survei)
    {
        // Ambil data survei berdasarkan ID
        $survey = Survei::with('kecamatan')
            ->where('id_survei', $id_survei)
            ->firstOrFail();

        // Query untuk daftar mitra dengan relasi jumlah survei
        $mitras = Mitra::with('kecamatan')
            ->withCount('mitraSurvei');

        // Filter berdasarkan kecamatan jika dipilih
        if ($request->filled('kecamatan')) {
            $mitras->where('id_kecamatan', $request->kecamatan);
        }

        // Filter berdasarkan pencarian nama mitra
        if ($request->filled('search')) {
            $mitras->where('nama_lengkap', 'like', '%' . $request->search . '%');
        }

        // Eksekusi query
        $mitras = $mitras->get();

        // Ambil daftar kecamatan untuk dropdown
        $kecamatans = Kecamatan::select('id_kecamatan', 'nama_kecamatan')->get();

        // Tambahkan status apakah mitra sudah mengikuti survei
        foreach ($mitras as $mitra) {
            // Menambahkan properti isFollowingSurvey secara dinamis
            $mitra->setAttribute('isFollowingSurvey', $mitra->mitraSurvei->contains('id_survei', $id_survei));
        }
        

        // Urutkan agar mitra yang mengikuti survei tampil di atas
        $mitras = $mitras->sortByDesc(fn($mitra) => $mitra->isFollowingSurvey ? 1 : 0);

        return view('mitrabps.editSurvei', compact('survey', 'mitras', 'kecamatans'));
    }


    public function toggleMitraSurvey($id_survei, $id_mitra)
    {
        $survey = Survei::findOrFail($id_survei);
        $mitra = Mitra::findOrFail($id_mitra);

        // Jika mitra sudah mengikuti survei, batalkan
        if ($mitra->mitraSurvei->contains('id_survei', $id_survei)) {
            $mitra->mitraSurvei()->detach($id_survei); // Menghapus relasi
        } else {
            $mitra->mitraSurvei()->attach($id_survei); // Menambahkan relasi
        }

        return redirect()->back();
    }

    public function uploadExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);

        try {
            Excel::import(new SurveiImport, $request->file('file'));
            return redirect()->back()->with('success', 'Data berhasil diunggah!');
        } catch (Exception $e) {
            Log::error('Error saat mengunggah file: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengunggah file.');
        }
    }








    public function import(Request$request) 
    {
            // Validasi file
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls',
        ],[
            'excel_file.required' => 'File xls tidak boleh kosong',
        ]);

        Excel::import(new SurveiImport, $request->file('excel_file'));

        return redirect()->route('mitrabps.daftarsurveibps')->with('success', 'Import Sukses');

    }       
}


