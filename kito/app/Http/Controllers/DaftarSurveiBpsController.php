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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Provinsi; // Model untuk Provinsi
use App\Models\Kabupaten; // Model untuk Kabupaten
use App\Models\Desa; // Model untuk Desa
use App\Models\PosisiMitra; // Model untuk Posisi Mitra


class DaftarSurveiBpsController extends Controller
{
    public function index(Request $request)
    {
        \Carbon\Carbon::setLocale('id');

        // Opsi filter tahun
        $tahunOptions = Survei::selectRaw('DISTINCT YEAR(bulan_dominan) as tahun')
            ->orderByDesc('tahun')
            ->pluck('tahun', 'tahun');

        // Opsi filter bulan berdasarkan tahun
        $bulanOptions = [];
        if ($request->filled('tahun')) {
            $bulanOptions = Survei::selectRaw('DISTINCT MONTH(bulan_dominan) as bulan')
                ->whereYear('bulan_dominan', $request->tahun)
                ->orderBy('bulan')
                ->pluck('bulan', 'bulan')
                ->mapWithKeys(function ($month) {
                    $monthNumber = str_pad($month, 2, '0', STR_PAD_LEFT);
                    return [
                        $monthNumber => \Carbon\Carbon::create()->month($month)->translatedFormat('F')
                    ];
                });
        }

        // Opsi filter nama survei
        $namaSurveiOptions = Survei::select('nama_survei')
            ->distinct()
            ->when($request->filled('tahun'), fn($q) => $q->whereYear('bulan_dominan', $request->tahun))
            ->when($request->filled('bulan'), fn($q) => $q->whereMonth('bulan_dominan', $request->bulan))
            ->orderBy('nama_survei')
            ->pluck('nama_survei', 'nama_survei');

        // Query utama untuk mengambil data survei
        $surveys = Survei::with([
            'mitraSurveis' => function ($query) {
                $query->whereNotNull('mitra_survei.id_posisi_mitra')
                    ->with(['mitra', 'posisiMitra']);
            }
        ])
            ->withCount([
                'mitraSurveis as mitra_survei_count' => function ($query) {
                    $query->whereNotNull('mitra_survei.id_posisi_mitra');
                }
            ])
            ->when($request->filled('tahun'), fn($q) => $q->whereYear('bulan_dominan', $request->tahun))
            ->when($request->filled('bulan'), fn($q) => $q->whereMonth('bulan_dominan', $request->bulan))
            ->when($request->filled('nama_survei'), fn($q) => $q->where('nama_survei', $request->nama_survei))
            ->orderBy('status_survei')
            ->paginate(10);

        // Menghitung total survei untuk highlight mitra (tidak ada perubahan di sini)
        $mitraHighlight = collect();
        if ($request->filled('tahun') || $request->filled('bulan')) {
            $mitraHighlight = DB::table('mitra_survei')
                ->join('survei', 'mitra_survei.id_survei', '=', 'survei.id_survei')
                ->select('mitra_survei.id_mitra', DB::raw('COUNT(DISTINCT mitra_survei.id_survei) as total'))
                ->whereNotNull('mitra_survei.id_posisi_mitra')
                ->when($request->filled('tahun'), fn($q) => $q->whereYear('survei.bulan_dominan', $request->tahun))
                ->when($request->filled('bulan'), fn($q) => $q->whereMonth('survei.bulan_dominan', $request->bulan))
                ->groupBy('mitra_survei.id_mitra')
                ->pluck('total', 'mitra_survei.id_mitra');
        }

        return view('mitrabps.daftarSurvei', compact(
            'surveys',
            'tahunOptions',
            'bulanOptions',
            'namaSurveiOptions',
            'mitraHighlight',
            'request'
        ));
    }



    public function editSurvei(Request $request, $id_survei)
    {
        $survey = Survei::with([
            'mitraSurveis' => fn($q) => $q->with(['mitra', 'posisiMitra'])
        ])
            ->select('id_survei', 'status_survei', 'nama_survei', 'jadwal_kegiatan', 'jadwal_berakhir_kegiatan', 'kro', 'tim', 'bulan_dominan')
            ->where('id_survei', $id_survei)
            ->firstOrFail();

        \Carbon\Carbon::setLocale('id');

        // Bagian filter (tahun, bulan, kecamatan, nama) tidak berubah
        // ... (kode filter Anda dari baris 20-135 tetap sama)
        $tahunOptions = Mitra::selectRaw('YEAR(tahun) as tahun')
            ->whereDate('tahun', '<=', $survey->jadwal_kegiatan)
            ->whereDate('tahun_selesai', '>=', $survey->jadwal_berakhir_kegiatan)
            ->where('status_pekerjaan', 0)
            ->union(Mitra::query()->selectRaw('YEAR(tahun_selesai) as tahun')
                ->whereDate('tahun', '<=', $survey->jadwal_kegiatan)
                ->whereDate('tahun_selesai', '>=', $survey->jadwal_berakhir_kegiatan)
                ->where('status_pekerjaan', 0))
            ->orderByDesc('tahun')
            ->pluck('tahun', 'tahun');

        $bulanOptions = [];
        if ($request->filled('tahun')) {
            $mitrasAktif = Mitra::whereYear('tahun', '<=', $request->tahun)
                ->whereYear('tahun_selesai', '>=', $request->tahun)
                ->whereDate('tahun', '<=', $survey->jadwal_kegiatan)
                ->whereDate('tahun_selesai', '>=', $survey->jadwal_berakhir_kegiatan)
                ->where('status_pekerjaan', 0)
                ->get();
            $bulanValid = collect();
            foreach ($mitrasAktif as $mitra) {
                $tahunMulai = \Carbon\Carbon::parse($mitra->tahun);
                $tahunSelesai = \Carbon\Carbon::parse($mitra->tahun_selesai);
                if ($tahunMulai->year == $request->tahun && $tahunSelesai->year == $request->tahun) {
                    $bulanMulai = max($tahunMulai->month, 1);
                    $bulanSelesai = min($tahunSelesai->month, 12);
                    for ($month = $bulanMulai; $month <= $bulanSelesai; $month++) {
                        $bulanValid->push($month);
                    }
                } elseif ($tahunMulai->year < $request->tahun && $tahunSelesai->year == $request->tahun) {
                    $bulanSelesai = min($tahunSelesai->month, 12);
                    for ($month = 1; $month <= $bulanSelesai; $month++) {
                        $bulanValid->push($month);
                    }
                } elseif ($tahunMulai->year == $request->tahun && $tahunSelesai->year > $request->tahun) {
                    $bulanMulai = max($tahunMulai->month, 1);
                    for ($month = $bulanMulai; $month <= 12; $month++) {
                        $bulanValid->push($month);
                    }
                } else {
                    for ($month = 1; $month <= 12; $month++) {
                        $bulanValid->push($month);
                    }
                }
            }
            $bulanOptions = $bulanValid->unique()->sort()->mapWithKeys(fn($m) => [str_pad($m, 2, '0', STR_PAD_LEFT) => \Carbon\Carbon::create()->month($m)->translatedFormat('F')]);
        }

        $kecamatanOptions = Kecamatan::query()
            ->whereHas('mitras', fn($q) => $q->whereDate('tahun', '<=', $survey->jadwal_kegiatan)->whereDate('tahun_selesai', '>=', $survey->jadwal_berakhir_kegiatan)->where('status_pekerjaan', 0))
            ->when($request->filled('tahun') || $request->filled('bulan'), function ($query) use ($request) {
                $query->whereHas('mitras', function ($q) use ($request) {
                    if ($request->filled('tahun')) {
                        $q->whereYear('tahun', '<=', $request->tahun)->whereYear('tahun_selesai', '>=', $request->tahun);
                    }
                    if ($request->filled('bulan')) {
                        $q->whereMonth('tahun', '<=', $request->bulan)->whereMonth('tahun_selesai', '>=', $request->bulan);
                    }
                });
            })
            ->orderBy('nama_kecamatan')->get(['id_kecamatan', 'kode_kecamatan', 'nama_kecamatan']);

        $namaMitraOptions = Mitra::whereDate('tahun', '<=', $survey->jadwal_kegiatan)
            ->whereDate('tahun_selesai', '>=', $survey->jadwal_berakhir_kegiatan)
            ->where('status_pekerjaan', 0)
            ->select('nama_lengkap')->distinct()
            ->when($request->filled('tahun'), fn($q) => $q->whereYear('tahun', '<=', $request->tahun)->whereYear('tahun_selesai', '>=', $request->tahun))
            ->when($request->filled('bulan'), fn($q) => $q->whereMonth('tahun', '<=', $request->bulan)->whereMonth('tahun_selesai', '>=', $request->bulan))
            ->when($request->filled('kecamatan'), fn($q) => $q->where('id_kecamatan', $request->kecamatan))
            ->orderBy('nama_lengkap')->pluck('nama_lengkap', 'nama_lengkap');

        // Query utama untuk menampilkan mitra, dengan penyesuaian pada 'rate_honor'
        $mitrasQuery = Mitra::with(['kecamatan', 'mitraSurveis' => fn($q) => $q->where('id_survei', $id_survei)->with('posisiMitra')])
            ->leftJoin('mitra_survei', fn($j) => $j->on('mitra.id_mitra', '=', 'mitra_survei.id_mitra')->where('mitra_survei.id_survei', '=', $id_survei))
            ->leftJoin('posisi_mitra', 'mitra_survei.id_posisi_mitra', '=', 'posisi_mitra.id_posisi_mitra')
            ->select('mitra.*')
            ->selectRaw('SUM(CASE WHEN mitra_survei.id_survei = ? THEN 1 ELSE 0 END) as isFollowingSurvey', [$id_survei])
            ->selectRaw('MAX(CASE WHEN mitra_survei.id_survei = ? THEN mitra_survei.vol ELSE NULL END) as vol', [$id_survei])
            ->selectRaw('MAX(CASE WHEN mitra_survei.id_survei = ? THEN mitra_survei.rate_honor ELSE NULL END) as rate_honor', [$id_survei]) // Mengambil rate_honor dari mitra_survei
            ->selectRaw('MAX(CASE WHEN mitra_survei.id_survei = ? THEN mitra_survei.id_posisi_mitra ELSE NULL END) as id_posisi_mitra', [$id_survei])
            ->selectRaw('MAX(CASE WHEN mitra_survei.id_survei = ? THEN posisi_mitra.nama_posisi ELSE NULL END) as nama_posisi', [$id_survei])
            ->addSelect([
                'total_survei' => MitraSurvei::selectRaw('COUNT(*)')->whereColumn('mitra_survei.id_mitra', 'mitra.id_mitra')
                    ->whereHas('survei', function ($q) use ($request) {
                        $q->whereDate('jadwal_kegiatan', '>=', DB::raw('mitra.tahun'))->whereDate('jadwal_berakhir_kegiatan', '<=', DB::raw('mitra.tahun_selesai'));
                        if ($request->filled('bulan')) {
                            $q->whereMonth('bulan_dominan', $request->bulan);
                        }
                        if ($request->filled('tahun')) {
                            $q->whereYear('bulan_dominan', $request->tahun);
                        }
                    })
            ])
            ->groupBy('mitra.id_mitra')
            ->whereDate('mitra.tahun', '<=', $survey->jadwal_kegiatan)
            ->whereDate('mitra.tahun_selesai', '>=', $survey->jadwal_berakhir_kegiatan)
            ->where('mitra.status_pekerjaan', 0);

        // Menerapkan filter
        $mitrasQuery->when($request->filled('tahun'), fn($q) => $q->whereYear('mitra.tahun', '<=', $request->tahun)->whereYear('mitra.tahun_selesai', '>=', $request->tahun))
            ->when($request->filled('bulan'), fn($q) => $q->whereMonth('mitra.tahun', '<=', $request->bulan)->whereMonth('mitra.tahun_selesai', '>=', $request->bulan))
            ->when($request->filled('kecamatan'), fn($q) => $q->where('mitra.id_kecamatan', $request->kecamatan))
            ->when($request->filled('nama_lengkap'), fn($q) => $q->where('mitra.nama_lengkap', $request->nama_lengkap));

        if ($request->filled('status_mitra')) {
            if ($request->status_mitra == 'ikut') {
                $mitrasQuery->whereHas('mitraSurveis', fn($q) => $q->where('id_survei', $id_survei));
            } elseif ($request->status_mitra == 'tidak_ikut') {
                $mitrasQuery->whereDoesntHave('mitraSurveis', fn($q) => $q->where('id_survei', $id_survei));
            }
        }

        $posisiMitraOptions = PosisiMitra::all();
        $mitras = $mitrasQuery->orderByDesc('isFollowingSurvey')->paginate(10);

        return view('mitrabps.editSurvei', compact('survey', 'mitras', 'tahunOptions', 'bulanOptions', 'kecamatanOptions', 'namaMitraOptions', 'posisiMitraOptions', 'request'));
    }


    public function updateMitraOnSurvei(Request $request, $id_survei, $id_mitra)
    {
        $request->validate([
            'vol' => 'required|numeric|min:1',
            'rate_honor' => 'required|numeric|min:0',
            'id_posisi_mitra' => 'required|exists:posisi_mitra,id_posisi_mitra'
        ]);

        $survey = Survei::findOrFail($id_survei);
        $mitraSurvei = MitraSurvei::where('id_survei', $id_survei)->where('id_mitra', $id_mitra)->firstOrFail();

        // Hitung total honor di bulan yang sama, kecuali untuk entri ini
        $totalHonorBulanIni = MitraSurvei::join('survei', 'mitra_survei.id_survei', '=', 'survei.id_survei')
            ->where('mitra_survei.id_mitra', $id_mitra)
            ->where('survei.bulan_dominan', $survey->bulan_dominan)
            ->where('mitra_survei.id_survei', '!=', $id_survei)
            ->sum(DB::raw('mitra_survei.rate_honor * mitra_survei.vol'));

        $honorYangAkanDitambahkan = $request->input('rate_honor') * $request->input('vol');
        $totalHonorSetelahUpdate = $totalHonorBulanIni + $honorYangAkanDitambahkan;

        // Validasi batas honor
        if ($totalHonorSetelahUpdate > 4000000 && !$request->has('force_add')) {
            $totalHonorSebelumUpdateSaatIni = MitraSurvei::join('survei', 'mitra_survei.id_survei', '=', 'survei.id_survei')
                ->where('mitra_survei.id_mitra', $id_mitra)
                ->where('survei.bulan_dominan', $survey->bulan_dominan)
                ->sum(DB::raw('mitra_survei.rate_honor * mitra_survei.vol'));

            return redirect()->back()->with('confirm', [
                'message' => "Total honor mitra di bulan " . \Carbon\Carbon::parse($survey->bulan_dominan)->translatedFormat('F Y') . " akan melebihi Rp 4.000.000 (Total saat ini: Rp " . number_format($totalHonorSebelumUpdateSaatIni, 0, ',', '.') . "). Tetap simpan perubahan?",
                'data' => $request->all()
            ])->with('id_mitra', $id_mitra);
        }

        // Update data
        $mitraSurvei->vol = $request->input('vol');
        $mitraSurvei->rate_honor = $request->input('rate_honor');
        $mitraSurvei->id_posisi_mitra = $request->input('id_posisi_mitra');
        $mitraSurvei->save();

        $message = 'Mitra berhasil diperbarui!';
        if ($totalHonorSetelahUpdate > 4000000) {
            $message .= ' Perhatian: Total honor mitra melebihi batas Rp 4.000.000';
        }

        return redirect()->back()->with('success', $message);
    }


    public function deleteMitraFromSurvei($id_survei, $id_mitra)
    {
        MitraSurvei::where('id_survei', $id_survei)
            ->where('id_mitra', $id_mitra)
            ->firstOrFail()
            ->delete();

        return redirect()->back()->with('success', 'Mitra berhasil dihapus dari survei!');
    }

    public function toggleMitraSurvey(Request $request, $id_survei, $id_mitra)
    {
        $request->validate([
            'vol' => 'required|numeric|min:1',
            'rate_honor' => 'required|numeric|min:0',
            'id_posisi_mitra' => 'required|exists:posisi_mitra,id_posisi_mitra'
        ], [
            'vol.required' => 'Volume harus diisi',
            'rate_honor.required' => 'Rate Honor harus diisi',
            'id_posisi_mitra.required' => 'Posisi mitra harus dipilih',
        ]);

        $survey = Survei::findOrFail($id_survei);

        $today = now()->toDateString();
        if ($today > $survey->jadwal_berakhir_kegiatan) {
            return redirect()->back()->with('error', "Tidak bisa menambah mitra karena survei sudah berakhir.")->withInput();
        }

        $rateHonor = $request->rate_honor;
        $honorYangAkanDitambahkan = $rateHonor * $request->vol;

        $totalHonorBulanIni = MitraSurvei::join('survei', 'mitra_survei.id_survei', '=', 'survei.id_survei')
            ->where('mitra_survei.id_mitra', $id_mitra)
            ->where('survei.bulan_dominan', $survey->bulan_dominan)
            ->sum(DB::raw('mitra_survei.rate_honor * mitra_survei.vol'));

        $totalHonorSetelahDitambah = $totalHonorBulanIni + $honorYangAkanDitambahkan;

        if ($totalHonorSetelahDitambah > 4000000 && !$request->has('force_add')) {
            return redirect()->back()->with('confirm', [
                'message' => "Total honor mitra di bulan " . \Carbon\Carbon::parse($survey->bulan_dominan)->translatedFormat('F Y') . " akan melebihi Rp 4.000.000 (Total saat ini: Rp " . number_format($totalHonorBulanIni, 0, ',', '.') . "). Tetap tambahkan?",
                'data' => $request->all()
            ])->with('id_mitra', $id_mitra);
        }

        MitraSurvei::updateOrCreate(
            ['id_survei' => $id_survei, 'id_mitra' => $id_mitra],
            [
                'vol' => $request->vol,
                'rate_honor' => $rateHonor,
                'id_posisi_mitra' => $request->id_posisi_mitra,
                'tgl_ikut_survei' => ($today >= $survey->jadwal_kegiatan && $today <= $survey->jadwal_berakhir_kegiatan) ? $today : $survey->jadwal_kegiatan
            ]
        );

        $message = 'Mitra berhasil ditambahkan ke survei!';
        if ($totalHonorSetelahDitambah > 4000000) {
            $message .= ' Perhatian: Total honor mitra melebihi batas Rp 4.000.000';
        }

        return redirect()->back()->with('success', $message);
    }

    private function sendWhatsAppNotification($mitra, $survey, $vol, $honor, $posisiMitra)
    {
        $token = "avqc2cbuFymVuKpMW3e2"; // Ganti dengan token Fonnte Anda

        $message = "Halo {$mitra->nama_lengkap},\n\n"
            . "Anda telah ditambahkan ke dalam survei:\n"
            . "Nama Survei: {$survey->nama_survei}\n"
            . "Jadwal Kegiatan: {$survey->jadwal_kegiatan} hingga {$survey->jadwal_berakhir_kegiatan}\n"
            . "Posisi: {$posisiMitra}\n"
            . "Volume: {$vol}\n"
            . "Honor: Rp " . number_format($honor, 0, ',', '.') . "\n\n"
            . "Terima kasih telah berpartisipasi.";

        $data = [
            "target" => $mitra->no_hp_mitra,
            "message" => $message
        ];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.fonnte.com/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'target' => $data["target"],
                'message' => $data["message"],
            ),
            CURLOPT_HTTPHEADER => array(
                'Authorization: ' . $token
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        // Anda bisa log response jika diperlukan
        // \Log::info('Fonnte API Response: ' . $response);
    }



    public function upExcelMitra2Survey(Request $request, $id_survei)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:2048'
        ]);

        $survei = Survei::find($id_survei);
        if (!$survei) {
            return redirect()->back()->with('error', 'Survei tidak ditemukan');
        }

        $import = new Mitra2SurveyImport($id_survei);

        try {
            Excel::import($import, $request->file('file'));

            $successCount = $import->getSuccessCount();
            $failedCount = $import->getFailedCount();
            $honorWarningsCount = $import->getHonorWarningsCount();
            $surveyWarningsCount = $import->getSurveyWarningsCount(); // Ambil peringatan survei


            $errorMessages = $import->getErrorMessages();
            $honorWarningMessages = $import->getHonorWarningMessages();
            $surveyWarnings = $import->getSurveyWarningMessages(); // Ambil peringatan survei

            $message = "Import selesai! ";
            $message .= "{$successCount} data berhasil diproses. ";

            if ($failedCount > 0) {
                $message .= "{$failedCount} data gagal diproses. ";
            }

            if ($honorWarningsCount > 0) {
                $message .= "{$honorWarningsCount} data memiliki peringatan honor. ";
            }

            $response = redirect()->back()
                ->with('success', trim($message));

            if ($failedCount > 0) {
                $response = $response->with('import_errors', $errorMessages);
            }

            if ($honorWarningsCount > 0) {
                $response = $response->with('honor_warnings', $honorWarningMessages);
            }

            if ($surveyWarningsCount > 0) {
                $response = $response->with('survei_warnings', $surveyWarnings);
            }

            return $response;
        } catch (\Exception $e) {
            Log::error('Import Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'file' => $request->file('file')->getClientOriginalName()
            ]);

            return redirect()->back()
                ->with('error', "Gagal mengimpor file: " . $e->getMessage());
        }
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
        $provinsi = Provinsi::where('id_provinsi', 35)->get(); // Ambil semua data provinsi
        $kabupaten = Kabupaten::where('id_kabupaten', 16)->get(); // Ambil semua data kabupaten


        return view('mitrabps.inputSurvei', compact('provinsi', 'kabupaten'));
    }

    public function getKabupaten($id_provinsi)
    {
        $kabupaten = Kabupaten::where('id_provinsi', $id_provinsi)->get();
        return response()->json($kabupaten);
    }

    public function getKecamatan($id_kabupaten)
    {
        $kecamatan = Kecamatan::where('id_kabupaten', $id_kabupaten)->get();
        return response()->json($kecamatan);
    }

    // Method untuk menyimpan data survei
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_survei' => 'required|string|max:1024',
            'kro' => 'required|string|max:1024',
            'jadwal_kegiatan' => 'required|date',
            'jadwal_berakhir_kegiatan' => 'required|date',
            'tim' => 'required|string|max:1024',
        ]);

        $getDominantMonthYear = function ($startDate, $endDate) {
            $start = \Carbon\Carbon::parse($startDate);
            $end = \Carbon\Carbon::parse($endDate);
            $months = collect();
            for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                $months->push($date->format('m-Y'));
            }
            return $months->countBy()->sortDesc()->keys()->first();
        };

        $dominantMonthYear = $getDominantMonthYear($validated['jadwal_kegiatan'], $validated['jadwal_berakhir_kegiatan']);
        [$bulan, $tahun] = explode('-', $dominantMonthYear);
        $validated['bulan_dominan'] = \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->toDateString();

        $today = now();
        $startDate = \Carbon\Carbon::parse($validated['jadwal_kegiatan']);
        $endDate = \Carbon\Carbon::parse($validated['jadwal_berakhir_kegiatan']);

        if ($today->lt($startDate)) {
            $validated['status_survei'] = 1; // Belum dimulai
        } elseif ($today->gt($endDate)) {
            $validated['status_survei'] = 3; // Selesai
        } else {
            $validated['status_survei'] = 2; // Berjalan
        }

        $validated['id_provinsi'] = 35;
        $validated['id_kabupaten'] = 16;

        $existingSurvei = Survei::where('nama_survei', $validated['nama_survei'])
            ->where('jadwal_kegiatan', $startDate->toDateString())
            ->where('jadwal_berakhir_kegiatan', $endDate->toDateString())
            ->where('bulan_dominan', $validated['bulan_dominan'])
            ->first();

        if ($existingSurvei) {
            $existingSurvei->update(['kro' => $validated['kro'], 'status_survei' => $validated['status_survei'], 'tim' => $validated['tim']]);
            return redirect()->back()->with('info', 'Data survei sudah ada dan telah diperbarui!');
        }

        Survei::create($validated);
        return redirect()->back()->with('success', 'Survei berhasil ditambahkan!');
    }

    public function deleteSurvei($id_survei)
    {
        $survei = Survei::findOrFail($id_survei);
        $namaSurvei = $survei->nama_survei;

        DB::transaction(function () use ($id_survei) {
            // 1. Hapus semua relasi di tabel pivot terlebih dahulu
            DB::table('mitra_survei')
                ->where('id_survei', $id_survei)
                ->delete();

            // 2. Baru hapus surveinya
            Survei::findOrFail($id_survei)->delete();
        });

        return redirect()->route('surveys.filter')
            ->with('success', "Survei $namaSurvei beserta relasi mitra berhasil dihapus");
    }

    public function upExcelSurvei(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xls,xlsx|max:2048'
        ]);

        $import = new SurveiImport();

        try {
            Excel::import($import, $request->file('file'));

            $successCount = $import->getSuccessCount();
            $failedCount = $import->getFailedCount();
            $rowErrors = $import->getRowErrors();

            $message = "Import selesai diproses! {$successCount} data berhasil diimport.";
            if ($failedCount > 0) {
                $message .= " {$failedCount} data gagal diimport.";

                // Format error lebih terstruktur
                $formattedErrors = [];
                foreach ($rowErrors as $error) {
                    $formattedErrors[] = $error;
                }

                // Batasi error yang ditampilkan
                $limitedErrors = array_slice($formattedErrors, 0, 10);
                if (count($formattedErrors) > 10) {
                    $limitedErrors[] = "Dan " . (count($formattedErrors) - 10) . " error lainnya...";
                }

                return redirect()->back()
                    ->with('success', $message)
                    ->with('warning', "Beberapa data gagal diimport. Silakan periksa error berikut:")
                    ->with('import_errors', $limitedErrors);
            }

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', "Terjadi kesalahan saat mengimpor data: " . $e->getMessage())
                ->withInput();
        }
    }
}
