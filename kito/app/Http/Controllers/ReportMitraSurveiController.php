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
use App\Exports\SurveiExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class ReportMitraSurveiController extends Controller
{
    public function SurveiReport(Request $request)
    {
        \Carbon\Carbon::setLocale('id');

        // OPTION FILTER TAHUN
        $tahunOptions = Survei::selectRaw('YEAR(jadwal_kegiatan) as tahun')
            ->orderByDesc('tahun')
            ->pluck('tahun', 'tahun');

        // OPTION FILTER BULAN (hanya muncul jika tahun dipilih)
        $bulanOptions = [];
        if ($request->filled('tahun')) {
            $bulanOptions = Survei::selectRaw('MONTH(bulan_dominan) as bulan')
                ->whereYear('bulan_dominan', $request->tahun)
                ->whereNotNull('bulan_dominan') // Pastikan bulan_dominan tidak NULL
                ->orderBy('bulan')
                ->distinct()
                ->get()
                ->mapWithKeys(function ($item) {
                    $monthName = \Carbon\Carbon::create()
                        ->month($item->bulan)
                        ->translatedFormat('F');
                    return [
                        str_pad($item->bulan, 2, '0', STR_PAD_LEFT) => $monthName
                    ];
                });
        }

        // Filter Nama Survei (hanya yang ada di tahun & bulan yang dipilih)
        $namaSurveiOptions = Survei::select('nama_survei')
            ->distinct()
            ->when($request->filled('tahun'), function ($query) use ($request) {
                $query->whereYear('bulan_dominan', $request->tahun);
            })
            ->when($request->filled('bulan'), function ($query) use ($request) {
                $query->whereMonth('bulan_dominan', $request->bulan);
            })
            ->orderBy('nama_survei')
            ->pluck('nama_survei', 'nama_survei');

        // QUERY UTAMA
        $surveisQuery = Survei::query()
            ->withCount(['mitraSurveis as total_mitra']) // Disederhanakan untuk efisiensi
            ->when($request->filled('tahun'), function ($query) use ($request) {
                $query->whereYear('bulan_dominan', $request->tahun);
            })
            ->when($request->filled('bulan'), function ($query) use ($request) {
                $query->whereMonth('bulan_dominan', $request->bulan);
            })
            ->when($request->filled('nama_survei'), function ($query) use ($request) {
                $query->where('nama_survei', $request->nama_survei);
            });

        // FILTER STATUS PARTISIPASI
        if ($request->filled('status_survei')) {
            if ($request->status_survei == 'aktif') {
                $surveisQuery->has('mitraSurveis');
            } elseif ($request->status_survei == 'tidak_aktif') {
                $surveisQuery->doesntHave('mitraSurveis');
            }
        }

        // HITUNG TOTAL-TOTAL
        $totalSurveiQuery = clone $surveisQuery; // Gunakan clone untuk perhitungan
        $totalSurvei = $totalSurveiQuery->count();
        $totalSurveiAktif = $totalSurveiQuery->has('mitraSurveis')->count();
        $totalSurveiTidakAktif = $totalSurvei - $totalSurveiAktif;

        // HITUNG TOTAL MITRA YANG IKUT SURVEI (disesuaikan untuk akurasi)
        $totalMitraIkut = 0;
        if ($totalSurvei > 0) {
            $surveiIds = (clone $surveisQuery)->pluck('id_survei');
            $totalMitraIkut = \App\Models\MitraSurvei::whereIn('id_survei', $surveiIds)->count();
        }

        // PAGINASI
        $surveis = $surveisQuery->paginate(10);

        // RETURN VIEW
        return view('mitrabps.reportSurvei', compact(
            'surveis',
            'tahunOptions',
            'bulanOptions',
            'namaSurveiOptions', // Pastikan variabel ini dikirim
            'totalSurvei',
            'totalSurveiAktif',
            'totalSurveiTidakAktif',
            'totalMitraIkut',
            'request'
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

        // OPTION FILTER BULAN
        $bulanOptions = [];
        if ($request->filled('tahun')) {
            $mitrasAktif = Mitra::whereYear('tahun', '<=', $request->tahun)
                ->whereYear('tahun_selesai', '>=', $request->tahun)
                ->get();
            $bulanValid = collect();
            foreach ($mitrasAktif as $mitra) {
                $tahunMulai = \Carbon\Carbon::parse($mitra->tahun);
                $tahunSelesai = \Carbon\Carbon::parse($mitra->tahun_selesai);
                if ($tahunMulai->year == $request->tahun && $tahunSelesai->year == $request->tahun) {
                    for ($month = $tahunMulai->month; $month <= $tahunSelesai->month; $month++) {
                        $bulanValid->push($month);
                    }
                } elseif ($tahunMulai->year < $request->tahun && $tahunSelesai->year == $request->tahun) {
                    for ($month = 1; $month <= $tahunSelesai->month; $month++) {
                        $bulanValid->push($month);
                    }
                } elseif ($tahunMulai->year == $request->tahun && $tahunSelesai->year > $request->tahun) {
                    for ($month = $tahunMulai->month; $month <= 12; $month++) {
                        $bulanValid->push($month);
                    }
                } else {
                    for ($month = 1; $month <= 12; $month++) {
                        $bulanValid->push($month);
                    }
                }
            }
            $bulanOptions = $bulanValid->unique()
                ->sort()
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

        // Filter Nama Mitra
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
                    ->whereHas('survei', function ($q) use ($request) {
                        if ($request->filled('bulan')) $q->whereMonth('bulan_dominan', $request->bulan);
                        if ($request->filled('tahun')) $q->whereYear('bulan_dominan', $request->tahun);
                    }),

                'total_honor_per_mitra' => MitraSurvei::selectRaw('SUM(vol * rate_honor)') // Disederhanakan
                    ->whereColumn('mitra_survei.id_mitra', 'mitra.id_mitra')
                    ->whereHas('survei', function ($q) use ($request) {
                        if ($request->filled('bulan')) $q->whereMonth('bulan_dominan', $request->bulan);
                        if ($request->filled('tahun')) $q->whereYear('bulan_dominan', $request->tahun);
                    })
            ])
            ->orderByDesc('total_survei')
            ->when($request->filled('tahun'), fn($q) => $q->whereYear('tahun', '<=', $request->tahun)->whereYear('tahun_selesai', '>=', $request->tahun))
            ->when($request->filled('bulan'), fn($q) => $q->whereMonth('tahun', '<=', $request->bulan)->whereMonth('tahun_selesai', '>=', $request->bulan))
            ->when($request->filled('kecamatan'), fn($q) => $q->where('id_kecamatan', $request->kecamatan))
            ->when($request->filled('nama_lengkap'), fn($q) => $q->where('nama_lengkap', $request->nama_lengkap))
            ->when($request->filled('status_pekerjaan'), fn($q) => $q->where('status_pekerjaan', $request->status_pekerjaan));

        // FILTER STATUS PARTISIPASI
        if ($request->filled('status_mitra')) {
            if ($request->status_mitra == 'ikut') {
                $mitrasQuery->whereHas('mitraSurveis.survei', function ($q) use ($request) {
                    if ($request->filled('tahun')) $q->whereYear('bulan_dominan', $request->tahun);
                    if ($request->filled('bulan')) $q->whereMonth('bulan_dominan', $request->bulan);
                });
            } elseif ($request->status_mitra == 'tidak_ikut') {
                $mitrasQuery->whereDoesntHave('mitraSurveis.survei', function ($q) use ($request) {
                    if ($request->filled('tahun')) $q->whereYear('bulan_dominan', $request->tahun);
                    if ($request->filled('bulan')) $q->whereMonth('bulan_dominan', $request->bulan);
                });
            }
        }

        // FILTER PARTISIPASI LEBIH DARI 1 (bergantung pada filter tahun dan bulan)
        if ($request->filled('tahun') && $request->filled('bulan') && $request->input('partisipasi_lebih_dari_satu') == 'ya') {
            $mitrasQuery->having('total_survei', '>', 1);
        }

        // FILTER HONOR > 4 JUTA (bergantung pada filter tahun dan bulan)
        if ($request->filled('tahun') && $request->filled('bulan') && $request->input('honor_lebih_dari_4jt') == 'ya') {
            $mitrasQuery->having('total_honor_per_mitra', '>', 4000000);
        }

        // HITUNG TOTAL-TOTAL
        $totalMitra = (clone $mitrasQuery)->count();
        $totalIkutSurvei = (clone $mitrasQuery)->whereHas('mitraSurveis', function ($query) use ($request) {
            if ($request->filled('bulan') || $request->filled('tahun')) {
                $query->whereHas('survei', function ($q) use ($request) {
                    if ($request->filled('bulan')) $q->whereMonth('bulan_dominan', $request->bulan);
                    if ($request->filled('tahun')) $q->whereYear('bulan_dominan', $request->tahun);
                });
            }
        })->count();
        $totalTidakIkutSurvei = $totalMitra - $totalIkutSurvei;

        $totalBisaIkutSurvei = (clone $mitrasQuery)->where('status_pekerjaan', 0)->count();
        $totalTidakBisaIkutSurvei = $totalMitra - $totalBisaIkutSurvei;

        $totalMitraKecamatan = 0;
        if ($request->filled('kecamatan')) {
            $totalMitraKecamatan = (clone $mitrasQuery)->where('id_kecamatan', $request->kecamatan)->count();
        }

        // HITUNG TOTAL HONOR
        $totalHonor = MitraSurvei::whereHas('mitra', function ($q) use ($mitrasQuery) {
            $mitraIds = (clone $mitrasQuery)->pluck('mitra.id_mitra');
            $q->whereIn('id_mitra', $mitraIds);
        })
            ->whereHas('survei', function ($q) use ($request) {
                if ($request->filled('bulan')) $q->whereMonth('bulan_dominan', $request->bulan);
                if ($request->filled('tahun')) $q->whereYear('bulan_dominan', $request->tahun);
            })
            ->sum(DB::raw('vol * rate_honor')); // Kalkulasi langsung dari database

        $mitras = $mitrasQuery->paginate(10)->appends($request->query());

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
            'totalBisaIkutSurvei',
            'totalTidakBisaIkutSurvei',
            'totalMitraKecamatan',
            'totalHonor',
            'request'
        ));
    }



    public function exportMitra(Request $request)
    {
        // Gunakan query yang sama persis dengan report untuk konsistensi data
        $mitrasQuery = Mitra::with(['kecamatan', 'provinsi', 'kabupaten', 'desa'])
            ->addSelect([
                'total_survei' => MitraSurvei::selectRaw('COUNT(*)')
                    ->whereColumn('mitra_survei.id_mitra', 'mitra.id_mitra')
                    ->whereHas('survei', function ($q) use ($request) {
                        if ($request->filled('bulan')) $q->whereMonth('bulan_dominan', $request->bulan);
                        if ($request->filled('tahun')) $q->whereYear('bulan_dominan', $request->tahun);
                    }),

                'total_honor_per_mitra' => MitraSurvei::selectRaw('SUM(vol * rate_honor)') // Disederhanakan
                    ->whereColumn('mitra_survei.id_mitra', 'mitra.id_mitra')
                    ->whereHas('survei', function ($q) use ($request) {
                        if ($request->filled('bulan')) $q->whereMonth('bulan_dominan', $request->bulan);
                        if ($request->filled('tahun')) $q->whereYear('bulan_dominan', $request->tahun);
                    })
            ])
            ->when($request->filled('tahun'), fn($q) => $q->whereYear('tahun', '<=', $request->tahun)->whereYear('tahun_selesai', '>=', $request->tahun))
            ->when($request->filled('bulan'), fn($q) => $q->whereMonth('tahun', '<=', $request->bulan)->whereMonth('tahun_selesai', '>=', $request->bulan))
            ->when($request->filled('kecamatan'), fn($q) => $q->where('id_kecamatan', $request->kecamatan))
            ->when($request->filled('nama_lengkap'), fn($q) => $q->where('nama_lengkap', $request->nama_lengkap))
            ->when($request->filled('status_pekerjaan'), fn($q) => $q->where('status_pekerjaan', $request->status_pekerjaan));


        // Filter Status Partisipasi
        if ($request->filled('status_mitra')) {
            if ($request->status_mitra == 'ikut') {
                $mitrasQuery->whereHas('mitraSurveis.survei', function ($q) use ($request) {
                    if ($request->filled('tahun')) $q->whereYear('bulan_dominan', $request->tahun);
                    if ($request->filled('bulan')) $q->whereMonth('bulan_dominan', $request->bulan);
                });
            } elseif ($request->status_mitra == 'tidak_ikut') {
                $mitrasQuery->whereDoesntHave('mitraSurveis.survei', function ($q) use ($request) {
                    if ($request->filled('tahun')) $q->whereYear('bulan_dominan', $request->tahun);
                    if ($request->filled('bulan')) $q->whereMonth('bulan_dominan', $request->bulan);
                });
            }
        }

        // FILTER PARTISIPASI LEBIH DARI 1 (bergantung pada filter tahun dan bulan)
        if ($request->filled('tahun') && $request->filled('bulan') && $request->input('partisipasi_lebih_dari_satu') == 'ya') {
            $mitrasQuery->having('total_survei', '>', 1);
        }

        // FILTER HONOR > 4 JUTA (bergantung pada filter tahun dan bulan)
        if ($request->filled('tahun') && $request->filled('bulan') && $request->input('honor_lebih_dari_4jt') == 'ya') {
            $mitrasQuery->having('total_honor_per_mitra', '>', 4000000);
        }

        // Kumpulkan informasi filter untuk ditampilkan di Excel
        $filters = [];
        if ($request->filled('tahun')) $filters['Tahun'] = $request->tahun;
        if ($request->filled('bulan')) {
            $monthName = \Carbon\Carbon::create()->month($request->bulan)->translatedFormat('F');
            $filters['Bulan'] = $monthName;
        }
        if ($request->filled('kecamatan')) {
            $kecamatan = Kecamatan::find($request->kecamatan);
            $filters['Kecamatan'] = $kecamatan ? $kecamatan->nama_kecamatan : $request->kecamatan;
        }
        if ($request->filled('nama_lengkap')) $filters['Nama Mitra'] = $request->nama_lengkap;
        if ($request->filled('status_mitra')) {
            $filters['Status Partisipasi'] = $request->status_mitra == 'ikut' ? 'Mengikuti Survei' : 'Tidak Mengikuti Survei';
        }
        if ($request->filled('partisipasi_lebih_dari_satu') && $request->partisipasi_lebih_dari_satu == 'ya') {
            $filters['Partisipasi > 1 Survei'] = 'Ya';
        }
        if ($request->filled('honor_lebih_dari_4jt') && $request->honor_lebih_dari_4jt == 'ya') {
            $filters['Honor > 4 Juta'] = 'Ya';
        }
        if ($request->filled('status_pekerjaan')) {
            $filters['Status Pekerjaan'] = $request->status_pekerjaan == 0 ? 'Bisa Mengikuti Survei' : 'Tidak Bisa Mengikuti Survei';
        }

        // Ambil data yang sudah difilter
        $mitrasData = $mitrasQuery->get();

        // Data total untuk ringkasan di Excel
        $totalMitra = $mitrasData->count();
        $totalIkutSurvei = $mitrasData->where('total_survei', '>', 0)->count();
        $totalTidakIkutSurvei = $totalMitra - $totalIkutSurvei;
        $totalBisaIkutSurvei = $mitrasData->where('status_pekerjaan', 0)->count();
        $totalTidakBisaIkutSurvei = $totalMitra - $totalBisaIkutSurvei;
        $totalHonor = $mitrasData->sum('total_honor_per_mitra');

        $totals = [
            'totalMitra' => $totalMitra,
            'totalIkutSurvei' => $totalIkutSurvei,
            'totalTidakIkutSurvei' => $totalTidakIkutSurvei,
            'totalBisaIkutSurvei' => $totalBisaIkutSurvei,
            'totalTidakBisaIkutSurvei' => $totalTidakBisaIkutSurvei,
            'totalHonor' => $totalHonor,
        ];

        return Excel::download(new MitraExport($mitrasData, $filters, $totals), 'laporan_mitra_' . now()->format('Ymd_His') . '.xlsx');
    }



    public function exportSurvei(Request $request)
    {
        // Gunakan query yang sama dengan report untuk konsistensi
        $surveisQuery = Survei::query()
            ->with(['provinsi', 'kabupaten']) // Eager load relasi
            ->withCount(['mitraSurveis as total_mitra'])
            ->when($request->filled('tahun'), function ($query) use ($request) {
                $query->whereYear('bulan_dominan', $request->tahun);
            })
            ->when($request->filled('bulan'), function ($query) use ($request) {
                $query->whereMonth('bulan_dominan', $request->bulan);
            })
            ->when($request->filled('nama_survei'), function ($query) use ($request) {
                $query->where('nama_survei', $request->nama_survei);
            });

        // Filter Status
        if ($request->filled('status_survei')) {
            if ($request->status_survei == 'aktif') {
                $surveisQuery->has('mitraSurveis');
            } elseif ($request->status_survei == 'tidak_aktif') {
                $surveisQuery->doesntHave('mitraSurveis');
            }
        }

        // Kumpulkan filter yang digunakan untuk ditampilkan di Excel
        $filters = [];
        if ($request->filled('tahun')) $filters['tahun'] = $request->tahun;
        if ($request->filled('bulan')) {
            // Kirim nomor bulan, biarkan kelas export yang format
            $filters['bulan'] = $request->bulan;
        }
        if ($request->filled('nama_survei')) $filters['nama_survei'] = $request->nama_survei;
        if ($request->filled('status_survei')) {
            $filters['status_survei'] = $request->status_survei == 'aktif' ? 'Survei Aktif' : 'Survei Tidak Aktif';
        }

        // Clone query untuk perhitungan total agar tidak mengganggu query utama
        $totalSurveiQuery = clone $surveisQuery;

        // Hitung total-total berdasarkan query yang sudah difilter
        $totalSurvei = $totalSurveiQuery->count();
        $totalSurveiAktif = (clone $totalSurveiQuery)->has('mitraSurveis')->count();
        $totalSurveiTidakAktif = $totalSurvei - $totalSurveiAktif;

        $totals = [
            'totalSurvei' => $totalSurvei,
            'totalSurveiAktif' => $totalSurveiAktif,
            'totalSurveiTidakAktif' => $totalSurveiTidakAktif,
        ];

        // Panggil kelas Export dengan query (bukan data yang sudah di-get)
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\SurveiExport($surveisQuery, $filters, $totals),
            'laporan_survei_' . now()->format('Ymd_His') . '.xlsx'
        );
    }
}
