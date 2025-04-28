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
use App\Exports\MitraExport;
use Maatwebsite\Excel\Facades\Excel;  
use Illuminate\Support\Facades\DB; 

class ReportMitraSurveiController extends Controller
{
    public function SurveiReport(Request $request)
    {
        \Carbon\Carbon::setLocale('id');
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
    \Carbon\Carbon::setLocale('id');

    // OPTION FILTER TAHUN
    $tahunOptions = Mitra::selectRaw('YEAR(tahun) as tahun')
        ->union(Mitra::query()->selectRaw('YEAR(tahun_selesai) as tahun'))
        ->orderByDesc('tahun')
        ->pluck('tahun', 'tahun');

    // OPTION FILTER BULAN (hanya muncul jika tahun dipilih)
    $bulanOptions = [];
    if ($request->filled('tahun')) {
        $bulanAwal = Mitra::query()
            ->selectRaw('MONTH(tahun) as bulan')
            ->whereYear('tahun', '<=', $request->tahun)
            ->whereYear('tahun_selesai', '>=', $request->tahun);

        $bulanAkhir = Mitra::query()
            ->selectRaw('MONTH(tahun_selesai) as bulan')
            ->whereYear('tahun', '<=', $request->tahun)
            ->whereYear('tahun_selesai', '>=', $request->tahun);

        $bulanOptions = $bulanAwal->union($bulanAkhir->toBase())
            ->orderBy('bulan')
            ->distinct()
            ->pluck('bulan')
            ->mapWithKeys(function ($month) {
                return [
                    str_pad($month, 2, '0', STR_PAD_LEFT) => 
                    \Carbon\Carbon::create()->month($month)->translatedFormat('F')
                ];
            });
    }

    // FILTER KECAMATAN
    $kecamatanOptions = Kecamatan::query()
        ->when($request->filled('tahun') || $request->filled('bulan'), function ($query) use ($request) {
            $query->whereHas('mitras', function ($q) use ($request) {
                if ($request->filled('tahun')) {
                    $q->whereYear('tahun', '<=', $request->tahun)
                      ->whereYear('tahun_selesai', '>=', $request->tahun);
                }
                if ($request->filled('bulan')) {
                    $q->whereMonth('tahun', '<=', $request->bulan)
                      ->whereMonth('tahun_selesai', '>=', $request->bulan);
                }
            });
        })
        ->orderBy('nama_kecamatan')
        ->get(['nama_kecamatan', 'id_kecamatan', 'kode_kecamatan']);


    // Filter Nama Mitra (hanya yang ada di tahun & bulan yang dipilih)
    $namaMitraOptions = Mitra::select('nama_lengkap')
    ->distinct()
    ->when($request->filled('tahun'), function ($query) use ($request) {
        $query->whereYear('tahun', '<=', $request->tahun)
              ->whereYear('tahun_selesai', '>=', $request->tahun);
    })
    ->when($request->filled('bulan'), function ($query) use ($request) {
        $query->whereMonth('tahun', '<=', $request->bulan)
              ->whereMonth('tahun_selesai', '>=', $request->bulan);
    })
    ->when($request->filled('kecamatan'), function ($query) use ($request) {
        $query->where('id_kecamatan', $request->kecamatan);
    })
    ->orderBy('nama_lengkap')
    ->pluck('nama_lengkap', 'nama_lengkap');

    // QUERY UTAMA DENGAN SUBCUERY
    $mitrasQuery = Mitra::with(['kecamatan'])
        ->addSelect([
            'total_survei' => MitraSurvei::selectRaw('COUNT(*)')
                ->whereColumn('mitra_survei.id_mitra', 'mitra.id_mitra')
                ->whereHas('survei', function($q) use ($request) {
                    $q->whereDate('jadwal_kegiatan', '>=', DB::raw('mitra.tahun'))
                      ->whereDate('jadwal_kegiatan', '<=', DB::raw('mitra.tahun_selesai'));
                    
                    if ($request->filled('bulan')) {
                        $q->whereMonth('bulan_dominan', $request->bulan);
                    }
                    if ($request->filled('tahun')) {
                        $q->whereYear('bulan_dominan', $request->tahun);
                    }
                }),
            
        ])
        
        ->when($request->filled('tahun'), function ($query) use ($request) {
            $query->whereYear('tahun', '<=', $request->tahun)
                  ->whereYear('tahun_selesai', '>=', $request->tahun);
        })
        ->when($request->filled('bulan'), function ($query) use ($request) {
            $query->whereMonth('tahun', '<=', $request->bulan)
                  ->whereMonth('tahun_selesai', '>=', $request->bulan);
        })
        ->when($request->filled('kecamatan'), function ($query) use ($request) {
            $query->where('id_kecamatan', $request->kecamatan);
        })
        ->when($request->filled('nama_lengkap'), function ($query) use ($request) {
            $query->where('nama_lengkap', $request->nama_lengkap);
        });

    // FILTER STATUS PARTISIPASI
    if ($request->filled('status_mitra')) {
        if ($request->status_mitra == 'ikut') {
            $mitrasQuery->whereHas('mitraSurvei', function ($query) use ($request) {
                if ($request->filled('bulan')) {
                    $query->whereHas('survei', function ($q) use ($request) {
                        $q->where('bulan_dominan', $request->bulan);
                    });
                }
                if ($request->filled('tahun')) {
                    $query->whereHas('survei', function ($q) use ($request) {
                        $q->whereYear('jadwal_kegiatan', $request->tahun);
                    });
                }
            });
        } elseif ($request->status_mitra == 'tidak_ikut') {
            $mitrasQuery->whereDoesntHave('mitraSurvei', function ($query) use ($request) {
                if ($request->filled('bulan')) {
                    $query->whereHas('survei', function ($q) use ($request) {
                        $q->where('bulan_dominan', $request->bulan);
                    });
                }
                if ($request->filled('tahun')) {
                    $query->whereHas('survei', function ($q) use ($request) {
                        $q->whereYear('jadwal_kegiatan', $request->tahun);
                    });
                }
            });
        }
    }

    // HITUNG TOTAL-TOTAL
    $totalMitra = $mitrasQuery->count();
    
    $totalIkutSurvei = clone $mitrasQuery;
    $totalIkutSurvei = $totalIkutSurvei->whereHas('mitraSurvei', function ($query) use ($request) {
        if ($request->filled('bulan')) {
            $query->whereHas('survei', function ($q) use ($request) {
                $q->whereMonth('bulan_dominan', $request->bulan);
            });
        }
        if ($request->filled('tahun')) {
            $query->whereHas('survei', function ($q) use ($request) {
                $q->whereYear('bulan_dominan', $request->tahun);
            });
        }
    })->count();

    $totalTidakIkutSurvei = $totalMitra - $totalIkutSurvei;

    // HITUNG TOTAL HONOR
    $totalHonor = MitraSurvei::whereHas('mitra', function($q) use ($request, $mitrasQuery) {
            $q->whereIn('id_mitra', $mitrasQuery->pluck('id_mitra'));
        })
        ->whereHas('survei', function($q) use ($request) {
            if ($request->filled('bulan')) {
                $q->whereMonth('bulan_dominan', $request->bulan);
            }
            if ($request->filled('tahun')) {
                $q->whereYear('bulan_dominan', $request->tahun);
            }
        })
        ->sum(DB::raw('vol * honor'));

    // PAGINASI
    $mitras = $mitrasQuery->paginate(10);

    // RETURN VIEW
    return view('mitrabps.reportMitra', compact(
        'mitras',
        'tahunOptions',
        'bulanOptions',
        'kecamatanOptions',
        'namaMitraOptions',
        'totalMitra',
        'totalIkutSurvei',
        'totalTidakIkutSurvei',
        'totalHonor',
        'request'
    ));
}

public function exportMitra(Request $request)
{
    // Gunakan filter yang sama dengan report
    $mitrasQuery = Mitra::with(['kecamatan'])
        ->when($request->filled('tahun'), function ($query) use ($request) {
            $query->whereYear('tahun', $request->tahun);
        })
        ->when($request->filled('bulan'), function ($query) use ($request) {
            $query->whereMonth('tahun', $request->bulan);
        })
        ->when($request->filled('nama_lengkap'), function ($query) use ($request) {
            $query->where('nama_lengkap', $request->nama_lengkap);
        })
        ->when($request->filled('status_mitra'), function ($query) use ($request) {
            if ($request->status_mitra == 'ikut') {
                $query->whereHas('mitraSurvei');
            } elseif ($request->status_mitra == 'tidak_ikut') {
                $query->whereDoesntHave('mitraSurvei');
            }
        });

    return Excel::download(new MitraExport($mitrasQuery), 'data_mitra.xlsx');
}


}
