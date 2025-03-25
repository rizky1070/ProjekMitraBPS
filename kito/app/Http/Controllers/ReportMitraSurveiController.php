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

        // Filter berdasarkan mitra yang dipilih dari dropdown
        if ($request->filled('mitra')) {
            $mitras->where('id_mitra', $request->mitra);
        }

        // Pagination
        $mitras = $mitras->paginate(10);
    
        // Daftar kecamatan untuk dropdown filter
        $kecamatans = Kecamatan::pluck('nama_kecamatan', 'id_kecamatan');

        // Daftar mitra untuk dropdown filter
        $mitrasForDropdown = Mitra::select('id_mitra', 'nama_lengkap')
        ->orderBy('nama_lengkap', 'asc')
        ->get();

        return view('mitrabps.reportSurvei',compact('mitras', 'kecamatans', 'mitrasForDropdown'));
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
        if ($request->filled('partisipasi')) {
            if ($request->partisipasi == 'ikut') {
                $mitras->has('mitraSurvei');
            } elseif ($request->partisipasi == 'tidak_ikut') {
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
        if ($request->filled('partisipasi')) {
            if ($request->partisipasi == 'ikut') {
                $mitrasQuery->has('mitraSurvei');
            } elseif ($request->partisipasi == 'tidak_ikut') {
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
