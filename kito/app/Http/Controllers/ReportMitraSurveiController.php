<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Survei;
use App\Models\Mitra;
use App\Models\Provinsi;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Desa;
use App\Models\MitraSurvei;
use App\Imports\MitraImport;
use Maatwebsite\Excel\Facades\Excel;  

class ReportMitraSurveiController extends Controller
{
    public function SurveiReport(Request $request)
    {
        // Daftar kecamatan untuk dropdown filter
        $kecamatans = Kecamatan::pluck('nama_kecamatan', 'id_kecamatan');

        // Daftar mitra untuk dropdown filter
        $surveisForDropdown = Survei::select('id_survei', 'nama_survei')
            ->orderBy('nama_survei', 'asc')
            ->get();

        // Mengambil daftar tahun yang unik dari kolom tahun pada tabel mitra
        $tahunOptions = Survei::selectRaw('DISTINCT YEAR(jadwal_kegiatan) as tahun')
            ->orderByDesc('jadwal_kegiatan')
            ->pluck('tahun', 'tahun');

        // Mengambil daftar bulan berdasarkan tahun yang dipilih (jika ada)
        $bulanOptions = [];
        if ($request->filled('tahun')) {
            $bulanOptions = Survei::selectRaw('DISTINCT MONTH(jadwal_kegiatan) as bulan')
                ->whereYear('jadwal_kegiatan', $request->tahun)
                ->orderBy('bulan')
                ->pluck('bulan', 'bulan');
        }

        // Query awal dengan relasi dan count mitra survei
        $surveis = Survei::with('kecamatan')
            ->withCount('mitraSurvei');

        // Filter berdasarkan status partisipasi survei
        if ($request->filled('status_survei')) {
            if ($request->status_survei == 'aktif') {
                $surveis->has('mitraSurvei');
            } elseif ($request->status_survei == 'tidak_aktif') {
                $surveis->doesntHave('mitraSurvei');
            }
        }

        // Filter berdasarkan tahun (bila ada)
        if ($request->filled('tahun')) {
            $surveis->whereYear('tahun', $request->tahun);
        }

        // Filter berdasarkan bulan (bila ada)
        if ($request->filled('bulan')) {
            $surveis->whereMonth('tahun', $request->bulan);
        }

        // Filter nama mitra
        if ($request->filled('search')) {
            $surveis->where('nama_lengkap', 'like', '%' . $request->search . '%');
        }

        // Filter kecamatan
        if ($request->filled('kecamatan')) {
            $surveis->whereHas('kecamatan', function ($query) use ($request) {
                $query->where('nama_kecamatan', 'like', '%' . $request->kecamatan . '%');
            });
        }

        // Filter berdasarkan mitra yang dipilih dari dropdown
        if ($request->filled('mitra')) {
            $surveis->where('id_mitra', $request->mitra);
        }

        // Query untuk menghitung total dengan filter yang sama
        $totalQuery = Survei::query();
        $SurveiAktifQuery = Survei::has('mitraSurvei');
        $SurveiTidakAktifQuery = Survei::doesntHave('mitraSurvei');

        // Terapkan filter yang sama ke semua query
        $applyFilters = function($query) use ($request) {
            if ($request->filled('tahun')) {
                $query->whereYear('jadwal_kegiatan', $request->tahun);
            }
            if ($request->filled('bulan')) {
                $query->whereMonth('jadwal_kegiatan', $request->bulan);
            }
            if ($request->filled('search')) {
                $query->where('nama_lengkap', 'like', '%' . $request->search . '%');
            }
            if ($request->filled('kecamatan')) {
                $query->whereHas('kecamatan', function ($q) use ($request) {
                    $q->where('nama_kecamatan', 'like', '%' . $request->kecamatan . '%');
                });
            }
            if ($request->filled('mitra')) {
                $query->where('id_mitra', $request->mitra);
            }
        };

        // Terapkan filter ke semua query
        $applyFilters($totalQuery);
        $applyFilters($SurveiAktifQuery);
        $applyFilters($SurveiTidakAktifQuery);

        // Hitung total
        $totalSurvei = $totalQuery->count();
        $totalSurveiAktif = $SurveiAktifQuery->count();
        $totalSurveiTidakAktif = $SurveiTidakAktifQuery->count();

        // Query untuk data Survei (dengan pagination)
        $surveisQuery = Survei::with('kecamatan')->withCount('mitraSurvei');
        $applyFilters($surveisQuery);
        
        // Filter partisipasi khusus untuk query utama
        if ($request->filled('status_survei')) {
            if ($request->status_survei == 'aktif') {
                $surveisQuery->has('mitraSurvei');
            } elseif ($request->status_survei == 'tidak_aktif') {
                $surveisQuery->doesntHave('mitraSurvei');
            }
        }

        $surveis = $surveisQuery->paginate(10);


        // Return view dengan data filter dan hasil query
        return view('mitrabps.reportSurvei', compact(
            'surveis', 
            'kecamatans', 
            'surveisForDropdown', 
            'tahunOptions', 
            'bulanOptions',
            'totalSurvei',
            'totalSurveiAktif',
            'totalSurveiTidakAktif'
        ));
    }

    public function MitraReport(Request $request)
    {
        // Daftar kecamatan untuk dropdown filter
        $kecamatans = Kecamatan::pluck('nama_kecamatan', 'id_kecamatan');

        // Daftar mitra untuk dropdown filter
        $mitrasForDropdown = Mitra::select('id_mitra', 'nama_lengkap')
            ->orderBy('nama_lengkap', 'asc')
            ->get();

        // Mengambil daftar tahun yang unik dari kolom tahun pada tabel mitra
        $tahunOptions = Mitra::selectRaw('DISTINCT YEAR(tahun) as tahun')
            ->orderByDesc('tahun')
            ->pluck('tahun', 'tahun');

        // Mengambil daftar bulan berdasarkan tahun yang dipilih (jika ada)
        $bulanOptions = [];
        if ($request->filled('tahun')) {
            $bulanOptions = Mitra::selectRaw('DISTINCT MONTH(tahun) as bulan')
                ->whereYear('tahun', $request->tahun)
                ->orderBy('bulan')
                ->pluck('bulan', 'bulan');
        }

        // Query awal dengan relasi dan count mitra survei
        $mitras = Mitra::with('kecamatan')
            ->withCount('mitraSurvei');

        // Filter berdasarkan status partisipasi survei
        if ($request->filled('status_mitra')) {
            if ($request->status_mitra == 'ikut') {
                $mitras->has('mitraSurvei');
            } elseif ($request->status_mitra == 'tidak_ikut') {
                $mitras->doesntHave('mitraSurvei');
            }
        }

        // Filter berdasarkan tahun (bila ada)
        if ($request->filled('tahun')) {
            $mitras->whereYear('tahun', $request->tahun);
        }

        // Filter berdasarkan bulan (bila ada)
        if ($request->filled('bulan')) {
            $mitras->whereMonth('tahun', $request->bulan);
        }

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

        // Filter berdasarkan mitra yang dipilih dari dropdown
        if ($request->filled('mitra')) {
            $mitras->where('id_mitra', $request->mitra);
        }

        // Query untuk menghitung total dengan filter yang sama
        $totalQuery = Mitra::query();
        $ikutSurveiQuery = Mitra::has('mitraSurvei');
        $tidakIkutSurveiQuery = Mitra::doesntHave('mitraSurvei');

        // Terapkan filter yang sama ke semua query
        $applyFilters = function($query) use ($request) {
            if ($request->filled('tahun')) {
                $query->whereYear('tahun', $request->tahun);
            }
            if ($request->filled('bulan')) {
                $query->whereMonth('tahun', $request->bulan);
            }
            if ($request->filled('search')) {
                $query->where('nama_lengkap', 'like', '%' . $request->search . '%');
            }
            if ($request->filled('kecamatan')) {
                $query->whereHas('kecamatan', function ($q) use ($request) {
                    $q->where('nama_kecamatan', 'like', '%' . $request->kecamatan . '%');
                });
            }
            if ($request->filled('mitra')) {
                $query->where('id_mitra', $request->mitra);
            }
        };

        // Terapkan filter ke semua query
        $applyFilters($totalQuery);
        $applyFilters($ikutSurveiQuery);
        $applyFilters($tidakIkutSurveiQuery);

        // Hitung total
        $totalMitra = $totalQuery->count();
        $totalIkutSurvei = $ikutSurveiQuery->count();
        $totalTidakIkutSurvei = $tidakIkutSurveiQuery->count();

        // Query untuk data mitra (dengan pagination)
        $mitrasQuery = Mitra::with('kecamatan')->withCount('mitraSurvei');
        $applyFilters($mitrasQuery);
        
        // Filter partisipasi khusus untuk query utama
        if ($request->filled('status_mitra')) {
            if ($request->status_mitra == 'ikut') {
                $mitrasQuery->has('mitraSurvei');
            } elseif ($request->status_mitra == 'tidak_ikut') {
                $mitrasQuery->doesntHave('mitraSurvei');
            }
        }

        $mitras = $mitrasQuery->paginate(10);


        // Return view dengan data filter dan hasil query
        return view('mitrabps.reportMitra', compact(
            'mitras', 
            'kecamatans', 
            'mitrasForDropdown', 
            'tahunOptions', 
            'bulanOptions',
            'totalMitra',
            'totalIkutSurvei',
            'totalTidakIkutSurvei'
        ));
    }

}
