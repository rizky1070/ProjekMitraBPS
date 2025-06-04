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
            ->withCount(['mitraSurveis as total_mitra' => function ($query) use ($request) {
                if ($request->filled('tahun')) {
                    $query->whereYear('bulan_dominan', $request->tahun);
                }
                if ($request->filled('bulan')) {
                    $query->whereMonth('bulan_dominan', $request->bulan);
                }
            }])
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
        $totalSurvei = $surveisQuery->count();

        $totalSurveiAktif = clone $surveisQuery;
        $totalSurveiAktif = $totalSurveiAktif->has('mitraSurveis')->count();

        $totalSurveiTidakAktif = $totalSurvei - $totalSurveiAktif;

        // HITUNG TOTAL MITRA YANG IKUT SURVEI
        $totalMitraIkut = MitraSurvei::whereHas('survei', function ($q) use ($request, $surveisQuery) {
            if ($request->filled('tahun')) {
                $q->whereYear('bulan_dominan', $request->tahun);
            }
            if ($request->filled('bulan')) {
                $q->whereMonth('bulan_dominan', $request->bulan);
            }
            if ($request->filled('nama_survei')) {
                $q->where('nama_survei', $request->nama_survei);
            }
        })
            ->count();

        // PAGINASI
        $surveis = $surveisQuery->paginate(10);

        // RETURN VIEW
        return view('mitrabps.reportSurvei', compact(
            'surveis',
            'tahunOptions',
            'bulanOptions',
            'namaSurveiOptions',
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

        // OPTION FILTER BULAN (hanya bulan yang valid dalam rentang kontrak mitra di tahun yang dipilih)
        $bulanOptions = [];
        if ($request->filled('tahun')) {
            // Ambil semua mitra yang aktif di tahun tersebut
            $mitrasAktif = Mitra::whereYear('tahun', '<=', $request->tahun)
                ->whereYear('tahun_selesai', '>=', $request->tahun)
                ->get();
            $bulanValid = collect();
            foreach ($mitrasAktif as $mitra) {
                $tahunMulai = \Carbon\Carbon::parse($mitra->tahun);
                $tahunSelesai = \Carbon\Carbon::parse($mitra->tahun_selesai);
                // Jika tahun mulai dan selesai sama dengan tahun filter
                if ($tahunMulai->year == $request->tahun && $tahunSelesai->year == $request->tahun) {
                    // Tambahkan semua bulan dari bulan mulai sampai bulan selesai
                    for ($month = $tahunMulai->month; $month <= $tahunSelesai->month; $month++) {
                        $bulanValid->push($month);
                    }
                }
                // Jika tahun mulai < tahun filter dan tahun selesai = tahun filter
                elseif ($tahunMulai->year < $request->tahun && $tahunSelesai->year == $request->tahun) {
                    // Tambahkan semua bulan dari Januari sampai bulan selesai
                    for ($month = 1; $month <= $tahunSelesai->month; $month++) {
                        $bulanValid->push($month);
                    }
                }
                // Jika tahun mulai = tahun filter dan tahun selesai > tahun filter
                elseif ($tahunMulai->year == $request->tahun && $tahunSelesai->year > $request->tahun) {
                    // Tambahkan semua bulan dari bulan mulai sampai Desember
                    for ($month = $tahunMulai->month; $month <= 12; $month++) {
                        $bulanValid->push($month);
                    }
                }
                // Jika tahun mulai < tahun filter dan tahun selesai > tahun filter
                else {
                    // Tambahkan semua bulan (Jan-Des)
                    for ($month = 1; $month <= 12; $month++) {
                        $bulanValid->push($month);
                    }
                }
            }
            // Buat opsi bulan unik dan terurut
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
                    ->whereHas('survei', function ($q) use ($request) {
                        $q->whereDate('jadwal_kegiatan', '>=', DB::raw('mitra.tahun'))
                            ->whereDate('jadwal_kegiatan', '<=', DB::raw('mitra.tahun_selesai'));
                        if ($request->filled('bulan')) {
                            $q->whereMonth('bulan_dominan', $request->bulan);
                        }
                        if ($request->filled('tahun')) {
                            $q->whereYear('bulan_dominan', $request->tahun);
                        }
                    })
            ])
            ->orderByDesc('total_survei')
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
            })
            ->when($request->filled('status_pekerjaan'), function ($query) use ($request) {
                $query->where('status_pekerjaan', $request->status_pekerjaan);
            });

        // FILTER STATUS PARTISIPASI
        if ($request->filled('status_mitra')) {
            if ($request->status_mitra == 'ikut') {
                $mitrasQuery->whereHas('mitraSurveis.survei', function ($q) use ($request) {
                    if ($request->filled('tahun')) {
                        $q->whereYear('bulan_dominan', $request->tahun);
                    }
                    if ($request->filled('bulan')) {
                        $q->whereMonth('bulan_dominan', $request->bulan);
                    }
                });
            } elseif ($request->status_mitra == 'tidak_ikut') {
                $mitrasQuery->whereDoesntHave('mitraSurveis.survei', function ($q) use ($request) {
                    if ($request->filled('tahun')) {
                        $q->whereYear('bulan_dominan', $request->tahun);
                    }
                    if ($request->filled('bulan')) {
                        $q->whereMonth('bulan_dominan', $request->bulan);
                    }
                });
            }
        }

        // HITUNG TOTAL-TOTAL
        $totalMitra = $mitrasQuery->count();
        $totalIkutSurvei = clone $mitrasQuery;
        $totalIkutSurvei = $totalIkutSurvei->whereHas('mitraSurveis', function ($query) use ($request) {
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

        // HITUNG TOTAL MITRA YANG BISA DAN TIDAK BISA MENGIKUTI SURVEI
        $totalBisaIkutSurvei = clone $mitrasQuery;
        $totalBisaIkutSurvei = $totalBisaIkutSurvei->where('status_pekerjaan', 0)->count();
        $totalTidakBisaIkutSurvei = $totalMitra - $totalBisaIkutSurvei;

        // HITUNG TOTAL MITRA DI KECAMATAN YANG DIPILIH
        $totalMitraKecamatan = 0;
        if ($request->filled('kecamatan')) {
            $totalMitraKecamatan = $mitrasQuery->where('id_kecamatan', $request->kecamatan)->count();
        }
        // HITUNG TOTAL HONOR
        $totalHonor = MitraSurvei::whereHas('mitra', function ($q) use ($request, $mitrasQuery) {
            $q->whereIn('id_mitra', $mitrasQuery->pluck('id_mitra'));
        })
            ->whereHas('survei', function ($q) use ($request) {
                if ($request->filled('bulan')) {
                    $q->whereMonth('bulan_dominan', $request->bulan);
                }
                if ($request->filled('tahun')) {
                    $q->whereYear('bulan_dominan', $request->tahun);
                }
            })
            ->with(['posisiMitra'])
            ->get()
            ->sum(function ($item) {
                return $item->vol * $item->posisiMitra->rate_honor;
            });

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
            'totalBisaIkutSurvei',
            'totalTidakBisaIkutSurvei',
            'totalMitraKecamatan',
            'totalHonor',
            'request'
        ));
    }


    public function exportMitra(Request $request)
    {
        // Gunakan query yang sama persis dengan report
        $mitrasQuery = Mitra::with(['kecamatan', 'provinsi', 'kabupaten', 'desa'])
            ->addSelect([
                'total_survei' => MitraSurvei::selectRaw('COUNT(*)')
                    ->whereColumn('mitra_survei.id_mitra', 'mitra.id_mitra')
                    ->whereHas('survei', function ($q) use ($request) {
                        $q->whereDate('jadwal_kegiatan', '>=', DB::raw('mitra.tahun'))
                            ->whereDate('jadwal_kegiatan', '<=', DB::raw('mitra.tahun_selesai'));
                        if ($request->filled('bulan')) {
                            $q->whereMonth('bulan_dominan', $request->bulan);
                        }
                        if ($request->filled('tahun')) {
                            $q->whereYear('bulan_dominan', $request->tahun);
                        }
                    }),
                'total_honor' => MitraSurvei::selectRaw('COALESCE(SUM(vol * (SELECT rate_honor FROM posisi_mitra WHERE posisi_mitra.id_posisi_mitra = mitra_survei.id_posisi_mitra)), 0)')
                    ->whereColumn('mitra_survei.id_mitra', 'mitra.id_mitra')
                    ->whereHas('survei', function ($q) use ($request) {
                        $q->whereDate('jadwal_kegiatan', '>=', DB::raw('mitra.tahun'))
                            ->whereDate('jadwal_kegiatan', '<=', DB::raw('mitra.tahun_selesai'));
                        if ($request->filled('bulan')) {
                            $q->whereMonth('bulan_dominan', $request->bulan);
                        }
                        if ($request->filled('tahun')) {
                            $q->whereYear('bulan_dominan', $request->tahun);
                        }
                    })
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
            })
            ->when($request->filled('status_pekerjaan'), function ($query) use ($request) {
                $query->where('status_pekerjaan', $request->status_pekerjaan);
            });

        // Filter Status Partisipasi
        if ($request->filled('status_mitra')) {
            if ($request->status_mitra == 'ikut') {
                $mitrasQuery->whereHas('mitraSurveis', function ($query) use ($request) {
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
                });
            } elseif ($request->status_mitra == 'tidak_ikut') {
                $mitrasQuery->whereDoesntHave('mitraSurveis', function ($query) use ($request) {
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
                });
            }
        }

        // Pastikan untuk mengambil data dengan get()
        $mitrasData = $mitrasQuery->get();

        // Hitung total-total
        $totalMitra = $mitrasData->count();
        $totalIkutSurvei = $mitrasData->filter(function ($mitra) {
            return $mitra->total_survei > 0;
        })->count();
        $totalTidakIkutSurvei = $totalMitra - $totalIkutSurvei;

        // Hitung total mitra yang bisa dan tidak bisa ikut survei
        $totalBisaIkutSurvei = $mitrasData->where('status_pekerjaan', 0)->count();
        $totalTidakBisaIkutSurvei = $totalMitra - $totalBisaIkutSurvei;

        // Hitung total honor
        $mitraIds = $mitrasData->pluck('id_mitra');
        $totalHonor = MitraSurvei::join('posisi_mitra', 'mitra_survei.id_posisi_mitra', '=', 'posisi_mitra.id_posisi_mitra')
            ->whereIn('mitra_survei.id_mitra', $mitrasData->pluck('id_mitra'))
            ->whereHas('survei', function ($q) use ($request) {
                if ($request->filled('bulan')) {
                    $q->whereMonth('bulan_dominan', $request->bulan);
                }
                if ($request->filled('tahun')) {
                    $q->whereYear('bulan_dominan', $request->tahun);
                }
            })
            ->sum(DB::raw('mitra_survei.vol * posisi_mitra.rate_honor'));

        // Kumpulkan informasi filter
        $filters = [];
        if ($request->filled('tahun')) $filters['tahun'] = $request->tahun;
        if ($request->filled('bulan')) {
            $monthName = \Carbon\Carbon::create()->month($request->bulan)->translatedFormat('F');
            $filters['bulan'] = $monthName;
        }
        if ($request->filled('kecamatan')) {
            $kecamatan = Kecamatan::find($request->kecamatan);
            $filters['kecamatan'] = $kecamatan ? $kecamatan->nama_kecamatan : $request->kecamatan;
        }
        if ($request->filled('nama_lengkap')) $filters['nama_lengkap'] = $request->nama_lengkap;
        if ($request->filled('status_mitra')) {
            $filters['status_mitra'] = $request->status_mitra == 'ikut' ? 'Ikut Survei' : 'Tidak Ikut Survei';
        }
        if ($request->filled('status_pekerjaan')) {
            $filters['status_pekerjaan'] = $request->status_pekerjaan == 0 ? 'Bisa Ikut Survei' : 'Tidak Bisa Ikut Survei';
        }

        // Data total
        $totals = [
            'totalMitra' => $totalMitra,
            'totalIkutSurvei' => $totalIkutSurvei,
            'totalTidakIkutSurvei' => $totalTidakIkutSurvei,
            'totalBisaIkutSurvei' => $totalBisaIkutSurvei,
            'totalTidakBisaIkutSurvei' => $totalTidakBisaIkutSurvei,
            'totalHonor' => $totalHonor,
        ];

        return Excel::download(new MitraExport($mitrasQuery, $filters, $totals), 'laporan_mitra_' . now()->format('Ymd_His') . '.xlsx');
    }



    public function exportSurvei(Request $request)
    {
        // Gunakan query yang sama dengan report
        $surveisQuery = Survei::with(['provinsi', 'kabupaten', 'mitraSurveis'])
            ->withCount(['mitraSurveis as total_mitra' => function ($query) use ($request) {
                if ($request->filled('tahun')) {
                    $query->whereYear('bulan_dominan', $request->tahun);
                }
                if ($request->filled('bulan')) {
                    $query->whereMonth('bulan_dominan', $request->bulan);
                }
            }])
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

        // Kumpulkan filter yang digunakan
        $filters = [];
        if ($request->filled('tahun')) $filters['tahun'] = $request->tahun;
        if ($request->filled('bulan')) {
            $monthName = \Carbon\Carbon::create()->month($request->bulan)->translatedFormat('F');
            $filters['bulan'] = $monthName;
        }
        if ($request->filled('nama_survei')) $filters['nama_survei'] = $request->nama_survei;
        if ($request->filled('status_survei')) {
            $filters['status_survei'] = $request->status_survei == 'aktif' ? 'Aktif' : 'Tidak Aktif';
        }

        // Hitung total-total
        $totalSurvei = $surveisQuery->count();
        $totalSurveiAktif = clone $surveisQuery;
        $totalSurveiAktif = $totalSurveiAktif->has('mitraSurveis')->count();
        $totalSurveiTidakAktif = $totalSurvei - $totalSurveiAktif;

        $totalMitraIkut = MitraSurvei::whereHas('survei', function ($q) use ($request) {
            if ($request->filled('tahun')) {
                $q->whereYear('bulan_dominan', $request->tahun);
            }
            if ($request->filled('bulan')) {
                $q->whereMonth('bulan_dominan', $request->bulan);
            }
            if ($request->filled('nama_survei')) {
                $q->where('nama_survei', $request->nama_survei);
            }

            // Tambahan filter status survei
            if ($request->filled('status_survei')) {
                if ($request->status_survei == 'aktif') {
                    $q->has('mitraSurveis');
                } elseif ($request->status_survei == 'tidak_aktif') {
                    $q->doesntHave('mitraSurveis');
                }
            }
        })
            ->count();

        $totals = [
            'totalSurvei' => $totalSurvei,
            'totalSurveiAktif' => $totalSurveiAktif,
            'totalSurveiTidakAktif' => $totalSurveiTidakAktif,
            'totalMitraIkut' => $totalMitraIkut
        ];

        return Excel::download(
            new SurveiExport($surveisQuery, $filters, $totals),
            'laporan_survei_' . now()->format('Ymd_His') . '.xlsx'
        );
    }
}
