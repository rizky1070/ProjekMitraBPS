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
use App\Exports\MitraPerBulanExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

class ReportMitraSurveiController extends Controller
{

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
            ->orderBy('kode_kecamatan')
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
                    }),
                'rata_rata_nilai' => MitraSurvei::selectRaw('AVG(nilai)')
                    ->whereColumn('mitra_survei.id_mitra', 'mitra.id_mitra')
                    ->whereNotNull('nilai') // Hanya menghitung yang memiliki nilai
                    ->whereHas('survei', function ($q) use ($request) {
                        if ($request->filled('bulan')) $q->whereMonth('bulan_dominan', $request->bulan);
                        if ($request->filled('tahun')) $q->whereYear('bulan_dominan', $request->tahun);
                    }),
            ])
            ->orderByDesc('total_survei')
            ->when($request->filled('tahun'), fn($q) => $q->whereYear('tahun', '<=', $request->tahun)->whereYear('tahun_selesai', '>=', $request->tahun))
            ->when($request->filled('bulan'), fn($q) => $q->whereMonth('tahun', '<=', $request->bulan)->whereMonth('tahun_selesai', '>=', $request->bulan))
            ->when($request->filled('kecamatan'), fn($q) => $q->where('id_kecamatan', $request->kecamatan))
            ->when($request->filled('nama_lengkap'), fn($q) => $q->where('nama_lengkap', $request->nama_lengkap))
            ->when($request->filled('status_pekerjaan'), fn($q) => $q->where('status_pekerjaan', $request->status_pekerjaan))
            // [START] PENAMBAHAN FILTER JENIS KELAMIN
            ->when($request->filled('jenis_kelamin'), fn($q) => $q->where('jenis_kelamin', $request->jenis_kelamin));
        // [END] PENAMBAHAN FILTER JENIS KELAMIN

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

        // [START] HITUNG TOTAL BERDASARKAN JENIS KELAMIN
        $totalLaki = (clone $mitrasQuery)->where('jenis_kelamin', 1)->count();
        $totalPerempuan = (clone $mitrasQuery)->where('jenis_kelamin', 2)->count();
        // [END] HITUNG TOTAL BERDASARKAN JENIS KELAMIN

        $totalMitraKecamatan = 0;
        if ($request->filled('kecamatan')) {
            $totalMitraKecamatan = (clone $mitrasQuery)->where('id_kecamatan', $request->kecamatan)->count();
        }

        // [START] PENAMBAHAN LOGIKA BARU
        $totalMitraLebihDariSatuSurvei = 0;
        $totalMitraHonorLebihDari4Jt = 0;

        if ($request->filled('bulan')) {
            $baseQueryForCounts = (clone $mitrasQuery)->toBase();
            $subQuery = DB::table($baseQueryForCounts)->select(
                'total_survei',
                'total_honor_per_mitra'
            );

            $totalMitraLebihDariSatuSurvei = (clone $subQuery)->where('total_survei', '>', 1)->count();
            $totalMitraHonorLebihDari4Jt = (clone $subQuery)->where('total_honor_per_mitra', '>', 4000000)->count();
        }
        // [END] PENAMBAHAN LOGIKA BARU

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
            'totalMitraLebihDariSatuSurvei', // Kirim ke view
            'totalMitraHonorLebihDari4Jt',   // Kirim ke view
            'totalLaki',                     // Kirim ke view
            'totalPerempuan',                // Kirim ke view
            'request'
        ));
    }


    public function exportMitra(Request $request)
    {
        \carbon\Carbon::setLocale('id');
        $mode = $request->input('mode_export', 'detail');

        if ($mode === 'per_bulan') {
            return $this->exportPerBulan($request);
        } else {
            return $this->exportDetail($request);
        }
    }

    private function applyBaseFilters($query, Request $request, $isPerBulanMode = false)
    {
        $tahun = $request->input('tahun');

        // Tambahkan subquery untuk agregasi tahunan jika dalam mode 'per bulan' atau jika filter partisipasi/honor digunakan
        $needsYearlyAggregation = $isPerBulanMode || $request->input('partisipasi_lebih_dari_satu') == 'ya' || $request->input('honor_lebih_dari_4jt') == 'ya';

        if ($needsYearlyAggregation && $request->filled('tahun')) {
            $query->addSelect([
                'total_survei_tahunan' => MitraSurvei::selectRaw('COUNT(*)')
                    ->whereColumn('mitra_survei.id_mitra', 'mitra.id_mitra')
                    ->whereHas('survei', fn($q) => $q->whereYear('bulan_dominan', $tahun)),

                'total_honor_tahunan' => MitraSurvei::selectRaw('SUM(vol * rate_honor)')
                    ->whereColumn('mitra_survei.id_mitra', 'mitra.id_mitra')
                    ->whereHas('survei', fn($q) => $q->whereYear('bulan_dominan', $tahun)),
            ]);
        }


        // === Filter Dasar Mitra ===
        $query->when($request->filled('tahun'), fn($q) => $q->whereYear('tahun', '<=', $request->tahun)->whereYear('tahun_selesai', '>=', $request->tahun))
            ->when($request->filled('kecamatan'), fn($q) => $q->where('id_kecamatan', $request->kecamatan))
            ->when($request->filled('nama_lengkap'), fn($q) => $q->where('nama_lengkap', 'like', '%' . $request->nama_lengkap . '%'))
            ->when($request->filled('status_pekerjaan'), fn($q) => $q->where('status_pekerjaan', $request->status_pekerjaan))
            ->when($request->filled('jenis_kelamin'), fn($q) => $q->where('jenis_kelamin', $request->jenis_kelamin));

        // === Filter Status Partisipasi (berdasarkan tahun yang dipilih) ===
        if ($request->filled('status_mitra') && $request->filled('tahun')) {
            $query->where(function ($q) use ($request, $tahun) {
                if ($request->status_mitra == 'ikut') {
                    $q->whereHas('mitraSurveis.survei', fn($sq) => $sq->whereYear('bulan_dominan', $tahun));
                } elseif ($request->status_mitra == 'tidak_ikut') {
                    $q->whereDoesntHave('mitraSurveis.survei', fn($sq) => $sq->whereYear('bulan_dominan', $tahun));
                }
            });
        }

        // === Filter Lanjutan (berdasarkan agregasi tahunan) ===
        if ($needsYearlyAggregation && $request->filled('tahun')) {
            if ($request->input('partisipasi_lebih_dari_satu') == 'ya') {
                $query->having('total_survei_tahunan', '>', 1);
            }
            if ($request->input('honor_lebih_dari_4jt') == 'ya') {
                $query->having('total_honor_tahunan', '>', 4000000);
            }
        }

        return $query;
    }


    private function exportDetail(Request $request)
    {
        // Gunakan fungsi applyBaseFiltersMitra untuk menerapkan filter dasar
        $mitrasQuery = $this->applyBaseFilters(Mitra::query(), $request, false);

        // Tambahkan subquery spesifik untuk detail (yang bergantung pada bulan)
        $mitrasQuery->with(['kecamatan', 'provinsi', 'kabupaten', 'desa'])
            ->addSelect([
                'total_survei' => MitraSurvei::selectRaw('COUNT(*)')
                    ->whereColumn('mitra_survei.id_mitra', 'mitra.id_mitra')
                    ->whereHas('survei', function ($q) use ($request) {
                        if ($request->filled('bulan')) $q->whereMonth('bulan_dominan', $request->bulan);
                        if ($request->filled('tahun')) $q->whereYear('bulan_dominan', $request->tahun);
                    }),

                'total_honor_per_mitra' => MitraSurvei::selectRaw('SUM(vol * rate_honor)')
                    ->whereColumn('mitra_survei.id_mitra', 'mitra.id_mitra')
                    ->whereHas('survei', function ($q) use ($request) {
                        if ($request->filled('bulan')) $q->whereMonth('bulan_dominan', $request->bulan);
                        if ($request->filled('tahun')) $q->whereYear('bulan_dominan', $request->tahun);
                    }),

                'rata_rata_nilai' => MitraSurvei::selectRaw('AVG(nilai)')
                    ->whereColumn('mitra_survei.id_mitra', 'mitra.id_mitra')
                    ->whereNotNull('nilai')
                    ->whereHas('survei', function ($q) use ($request) {
                        if ($request->filled('bulan')) $q->whereMonth('bulan_dominan', $request->bulan);
                        if ($request->filled('tahun')) $q->whereYear('bulan_dominan', $request->tahun);
                    }),
            ]);

        // Filter `having` yang spesifik untuk `exportDetail` karena bergantung pada `total_survei` bulanan.
        if ($request->filled('tahun') && $request->filled('bulan')) {
            if ($request->input('partisipasi_lebih_dari_satu') == 'ya') {
                $mitrasQuery->having('total_survei', '>', 1);
            }
            if ($request->input('honor_lebih_dari_4jt') == 'ya') {
                $mitrasQuery->having('total_honor_per_mitra', '>', 4000000);
            }
        }

        // Ambil data dan siapkan untuk export
        $mitrasData = $mitrasQuery->orderBy('nama_lengkap')->get();

        // Kumpulkan informasi filter untuk ditampilkan di Excel (sama seperti sebelumnya)
        $filters = $this->getAppliedFilters($request);

        // Data total untuk ringkasan (sama seperti sebelumnya)
        $totals = $this->calculateTotals($mitrasData, $request);

        return Excel::download(new MitraExport($mitrasData, $filters, $totals), 'laporan_mitra_detail_' . now()->format('Ymd_His') . '.xlsx');
    }

    /**
     * Export Laporan Honor Mitra Per Bulan.
     * Fungsi ini sekarang menggunakan filter yang sama dengan exportDetail, kecuali filter bulan.
     */
    private function exportPerBulan(Request $request)
    {
        $tahun = $request->input('tahun');
        if (!$tahun) {
            return redirect()->back()->with('error', 'Silakan pilih tahun untuk mode export per bulan.');
        }

        // 1. Terapkan filter dasar untuk mendapatkan daftar mitra yang relevan.
        $mitras = $this->applyBaseFilters(Mitra::query(), $request, true)
            ->select('id_mitra', 'nama_lengkap', 'sobat_id', 'jenis_kelamin', 'status_pekerjaan') // Ambil kolom yang dibutuhkan
            ->orderBy('nama_lengkap')
            ->get();

        // 2. Proses data dan hitung total menggunakan fungsi helper.
        // Ini membuat strukturnya sama dengan `exportDetail`.
        list($exportData, $totals) = $this->processAndCalculatePerBulanTotals($mitras, $tahun);

        // 3. Siapkan header bulan.
        $monthHeaders = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthHeaders[$m] = \carbon\Carbon::create()->month($m)->translatedFormat('F');
        }

        // 4. Kumpulkan filter yang diterapkan untuk ditampilkan di file Excel.
        $filters = $this->getAppliedFilters($request, true);

        // 5. Kirim semua data yang sudah siap ke kelas Export.
        return Excel::download(
            new MitraPerBulanExport($exportData, $monthHeaders, $filters, $totals),
            'laporan_honor_mitra_per_bulan_' . $tahun . '_' . now()->format('Ymd_His') . '.xlsx'
        );
    }

    /**
     * Helper untuk memproses data export per bulan dan menghitung totalnya.
     * Menggantikan perulangan tidak efisien yang ada sebelumnya.
     *
     * @param \Illuminate\Support\Collection $mitras
     * @param int $tahun
     * @return array
     */
    private function processAndCalculatePerBulanTotals($mitras, $tahun)
    {
        if ($mitras->isEmpty()) {
            return [[], ['totalMitra' => 0, 'totalLaki' => 0, 'totalPerempuan' => 0, 'totalHonor' => 0, 'totalIkutSurvei' => 0, 'totalTidakIkutSurvei' => 0, 'totalBisaIkutSurvei' => 0, 'totalTidakBisaIkutSurvei' => 0]];
        }

        $mitraIds = $mitras->pluck('id_mitra');

        $honorData = MitraSurvei::whereIn('mitra_survei.id_mitra', $mitraIds)
            ->join('survei', 'mitra_survei.id_survei', '=', 'survei.id_survei')
            ->whereYear('survei.bulan_dominan', $tahun)
            ->selectRaw('mitra_survei.id_mitra, MONTH(survei.bulan_dominan) as bulan, SUM(mitra_survei.vol * mitra_survei.rate_honor) as total_honor')
            ->groupBy('mitra_survei.id_mitra', 'bulan')
            ->get()
            ->groupBy('id_mitra');

        $exportData = [];
        $grandTotalHonor = 0;

        foreach ($mitras as $mitra) {
            $monthlyHonors = array_fill(1, 12, 0);
            $totalYearlyHonor = 0;

            if (isset($honorData[$mitra->id_mitra])) {
                foreach ($honorData[$mitra->id_mitra] as $honor) {
                    $monthlyHonors[$honor->bulan] = (float) $honor->total_honor;
                }
            }

            $totalYearlyHonor = array_sum($monthlyHonors);
            $grandTotalHonor += $totalYearlyHonor;

            // Tambahkan sobat_id ke data yang akan diexport
            $exportData[] = [
                'sobat_id'   => $mitra->sobat_id,
                'nama_mitra' => $mitra->nama_lengkap,
                'honors'     => $monthlyHonors,
                'total'      => $totalYearlyHonor,
            ];
        }

        $totalIkutSurvei = collect($exportData)->where('total', '>', 0)->count();

        $totals = [
            'totalMitra'               => $mitras->count(),
            'totalLaki'                => $mitras->where('jenis_kelamin', 1)->count(),
            'totalPerempuan'           => $mitras->where('jenis_kelamin', 2)->count(),
            'totalHonor'               => $grandTotalHonor,
            'totalIkutSurvei'          => $totalIkutSurvei,
            'totalTidakIkutSurvei'     => $mitras->count() - $totalIkutSurvei,
            'totalBisaIkutSurvei'      => $mitras->where('status_pekerjaan', 0)->count(),
            'totalTidakBisaIkutSurvei' => $mitras->where('status_pekerjaan', '!=', 0)->count(),
        ];

        return [$exportData, $totals];
    }

    /**
     * Helper untuk mengumpulkan nama filter yang aktif untuk ditampilkan di file export.
     */
    private function getAppliedFilters(Request $request, $isPerBulanMode = false)
    {
        $filters = [];
        if ($request->filled('tahun')) $filters['Tahun'] = $request->tahun;

        if (!$isPerBulanMode && $request->filled('bulan')) {
            $filters['Bulan'] = \carbon\Carbon::create()->month($request->bulan)->translatedFormat('F');
        }

        if ($request->filled('kecamatan')) {
            $kecamatan = Kecamatan::find($request->kecamatan);
            $filters['Kecamatan'] = $kecamatan ? $kecamatan->nama_kecamatan : 'N/A';
        }
        if ($request->filled('nama_lengkap')) $filters['Nama Mitra'] = $request->nama_lengkap;

        if ($request->filled('status_mitra')) {
            $filters['Status Partisipasi Tahunan'] = $request->status_mitra == 'ikut' ? 'Mengikuti Survei' : 'Tidak Mengikuti Survei';
        }

        if ($request->filled('partisipasi_lebih_dari_satu') && $request->partisipasi_lebih_dari_satu == 'ya') {
            $filters['Partisipasi Tahunan > 1 Survei'] = 'Ya';
        }

        if ($request->filled('honor_lebih_dari_4jt') && $request->honor_lebih_dari_4jt == 'ya') {
            $filters['Honor Tahunan > 4 Juta'] = 'Ya';
        }

        if ($request->filled('status_pekerjaan')) {
            $filters['Status Pekerjaan'] = $request->status_pekerjaan == 0 ? 'Bisa Mengikuti Survei' : 'Tidak Bisa Mengikuti Survei';
        }

        if ($request->filled('jenis_kelamin')) {
            $filters['Jenis Kelamin'] = $request->jenis_kelamin == 1 ? 'Laki-laki' : 'Perempuan';
        }

        return $filters;
    }

    /**
     * Helper untuk menghitung total ringkasan untuk export detail.
     */
    private function calculateTotals($mitrasData, Request $request)
    {
        $totalMitra = $mitrasData->count();
        $totalIkutSurvei = $mitrasData->where('total_survei', '>', 0)->count();
        $totalHonor = $mitrasData->sum('total_honor_per_mitra');

        $totals = [
            'totalMitra' => $totalMitra,
            'totalLaki' => $mitrasData->where('jenis_kelamin', 1)->count(),
            'totalPerempuan' => $mitrasData->where('jenis_kelamin', 2)->count(),
            'totalIkutSurvei' => $totalIkutSurvei,
            'totalTidakIkutSurvei' => $totalMitra - $totalIkutSurvei,
            'totalBisaIkutSurvei' => $mitrasData->where('status_pekerjaan', 0)->count(),
            'totalTidakBisaIkutSurvei' => $mitrasData->where('status_pekerjaan', '!=', 0)->count(),
            'totalHonor' => $totalHonor,
            'totalMitraLebihDariSatuSurvei' => 0,
            'totalMitraHonorLebihDari4Jt' => 0,
        ];

        // Hitung total ini hanya jika filter bulan dan tahun aktif
        if ($request->filled('bulan') && $request->filled('tahun')) {
            $totals['totalMitraLebihDariSatuSurvei'] = $mitrasData->where('total_survei', '>', 1)->count();
            $totals['totalMitraHonorLebihDari4Jt'] = $mitrasData->where('total_honor_per_mitra', '>', 4000000)->count();
        }

        return $totals;
    }


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
