<?php

namespace App\Http\Controllers;
use App\Models\Survei;
use App\Models\Mitra;
use App\Models\Kecamatan;
use App\Models\MitraSurvei;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\Mitra2SurveyImport;
use App\Imports\SurveiImport;
use Exception; // Untuk menangani error
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Provinsi; // Model untuk Provinsi
use App\Models\Kabupaten; // Model untuk Kabupaten
use App\Models\Desa; // Model untuk Desa

class DaftarSurveiBpsController extends Controller
{
    public function index(Request $request)
    {
        // Set locale ke Indonesia
        \Carbon\Carbon::setLocale('id');
        
        // Mendapatkan daftar tahun yang tersedia dari tabel survei
        $availableYears = Survei::selectRaw('YEAR(jadwal_kegiatan) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');
    
        // Mendapatkan daftar kecamatan
        $kecamatans = Kecamatan::all(); 

        // Mendapatkan daftar nama survei yang unik
        $namaSurvei = Survei::select('nama_survei')
            ->distinct()
            ->orderBy('nama_survei', 'asc')
            ->get();
        
        // Mendapatkan bulan yang dipilih dari request
        $bulan = $request->filled('bulan') ? $request->bulan : null;
    
        // Query untuk mengambil data survei dan filter berdasarkan bulan
        $surveys = Survei::with('kecamatan', 'mitraSurvei')
                        ->withCount('mitraSurvei')
                        ->when($bulan, function ($query) use ($bulan) {
                            return $query->whereMonth('jadwal_kegiatan', $bulan);
                        });
    
        // Filter berdasarkan tahun jika ada parameter tahun
        if ($request->filled('tahun')) {
            $surveys->whereYear('jadwal_kegiatan', $request->tahun); 
        }
    
        // Filter berdasarkan bulan jika ada parameter bulan
        if ($request->filled('bulan')) {
            $surveys->whereMonth('jadwal_kegiatan', $request->bulan);  
        }
    
        // Filter berdasarkan kecamatan jika ada parameter kecamatan
        if ($request->filled('kecamatan')) {
            $surveys->where('id_kecamatan', $request->kecamatan); 
        }

        // Filter berdasarkan nama survei jika ada parameter nama_survei
        if ($request->filled('nama_survei')) {
            $surveys->where('nama_survei', $request->nama_survei); 
        }
    
        // Mendapatkan mitra yang terlibat lebih dari satu survei di bulan yang dipilih
        $mitraWithMultipleSurveysInMonth = Mitra::select('mitra.id_mitra', 'mitra.nama_lengkap')
            ->join('mitra_survei', 'mitra_survei.id_mitra', '=', 'mitra.id_mitra')
            ->join('survei', 'survei.id_survei', '=', 'mitra_survei.id_survei')
            ->whereMonth('survei.jadwal_kegiatan', $bulan)
            ->groupBy('mitra.id_mitra', 'mitra.nama_lengkap')
            ->havingRaw('COUNT(DISTINCT mitra_survei.id_survei) > 1')
            ->get();
    
        // Urutkan berdasarkan status_survei DESC sebelum melakukan paginasi
        $surveys = $surveys->orderBy('status_survei')->paginate(10);
    
        return view('mitrabps.daftarSurvei', compact('surveys', 'availableYears', 'kecamatans', 'mitraWithMultipleSurveysInMonth', 'bulan', 'namaSurvei'));
    }
    


    public function editSurvei(Request $request, $id_survei)
    {
        // Ambil data survei berdasarkan ID
        $survey = Survei::with('kecamatan')
            ->select('id_survei', 'status_survei', 'nama_survei', 'jadwal_kegiatan', 'kro', 'id_kecamatan')
            ->where('id_survei', $id_survei)
            ->firstOrFail();

        // Query daftar mitra untuk dropdown
        $mitrasForDropdown = Mitra::select('id_mitra', 'nama_lengkap')
        ->orderBy('nama_lengkap', 'asc')
        ->get();

        // Query daftar mitra
        $mitras = Mitra::with('kecamatan')
            ->leftJoin('mitra_survei', function ($join) use ($id_survei) {
                $join->on('mitra.id_mitra', '=', 'mitra_survei.id_mitra');
            })
            ->select('mitra.*')
            ->selectRaw('COUNT(mitra_survei.id_survei) as mitra_survei_count') // Hitung jumlah survei
            ->selectRaw('IF(SUM(mitra_survei.id_survei = ?), 1, 0) as isFollowingSurvey', [$id_survei]) // Cek apakah mitra mengikuti survei tertentu
            ->groupBy('mitra.id_mitra') // Diperlukan agar COUNT() berfungsi
            ->orderByDesc('isFollowingSurvey') // Prioritaskan mitra yang mengikuti survei
            ->orderByRaw('mitra.id_kecamatan = ? DESC', [$survey->id_kecamatan]); // Lalu prioritaskan mitra dari kecamatan survei

        // Filter berdasarkan kecamatan jika dipilih
        if ($request->filled('kecamatan')) {
            $mitras->where('mitra.id_kecamatan', $request->kecamatan);
        }

        // Filter berdasarkan pencarian nama mitra
        if ($request->filled('search')) {
            $mitras->where('mitra.nama_lengkap', 'like', '%' . $request->search . '%');
        }

        // Filter berdasarkan mitra yang dipilih dari dropdown
        if ($request->filled('mitra')) {
            $mitras->where('mitra.id_mitra', $request->mitra);
        }

        // Pagination langsung di query
        $mitras = $mitras->paginate(10);

        // Ambil daftar kecamatan untuk dropdown
        $kecamatans = Kecamatan::select('id_kecamatan', 'nama_kecamatan')->get();

        return view('mitrabps.editSurvei', compact('survey', 'mitras', 'kecamatans', 'mitrasForDropdown'));
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

    public function upExcelMitra2Survey(Request $request, $id_survei)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);    
    
        Excel::import(new mitra2SurveyImport($id_survei), $request->file('file'));
    
        return redirect()->back()->with('success', 'Mitra berhasil diimport ke survei');
    }

        public function updateStatus(Request $request, $id_survei)
    {
        $survey = Survei::findOrFail($id_survei);
        $survey->status_survei = $request->status_survei;
        $survey->save();

        return redirect()->back()->with('success', 'Status survei berhasil diperbarui!');
    }
    
    

    // Method untuk menampilkan halaman input survei
    public function create()
    {
        $provinsi = Provinsi::all(); // Ambil semua data provinsi
        $kabupaten = Kabupaten::all(); // Ambil semua data kabupaten
        $kecamatan = Kecamatan::all(); // Ambil semua data kecamatan
        $desa = Desa::all(); // Ambil semua data desa

        

        return view('mitrabps.inputSurvei', compact('provinsi', 'kabupaten', 'kecamatan', 'desa'));
    }

    // Method untuk menyimpan data survei
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'id_provinsi' => 'nullable|exists:provinsi,id_provinsi',
            'id_kabupaten' => 'nullable|exists:kabupaten,id_kabupaten',
            'id_kecamatan' => 'nullable|exists:kecamatan,id_kecamatan',
            'id_desa' => 'nullable|exists:desa,id_desa',
            'nama_survei' => 'nullable|string|max:1024',
            'lokasi_survei' => 'nullable|string|max:1024',
            'kro' => 'nullable|string|max:1024',
            'jadwal_kegiatan' => 'nullable|date',
            'status_survei' => 'nullable|integer',
            'tim' => 'nullable|string|max:1024',
        ]);

        // Simpan data ke database
        Survei::create($request->all());

        // Redirect ke halaman daftar survei dengan pesan sukses
        return redirect()->back()->with('success', 'Survei berhasil ditambahkan!');
    }


    public function upExcelSurvei(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xls,xlsx'
        ]);

        Excel::import(new SurveiImport, $request->file('file'));

        return redirect()->back()->with('success', 'Data mitra berhasil diimport!');
    }

}