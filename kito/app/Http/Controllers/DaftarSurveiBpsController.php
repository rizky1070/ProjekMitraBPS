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

        // Daftar tahun yang tersedia
        $tahunOptions = Survei::selectRaw('DISTINCT YEAR(bulan_dominan) as tahun')
            ->orderByDesc('tahun')
            ->pluck('tahun', 'tahun');

        // Daftar bulan berdasarkan tahun yang dipilih
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

        // Daftar nama survei berdasarkan filter
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

        // Query utama
        $surveys = Survei::with([
            'mitraSurveis' => function ($query) {
                $query->whereNotNull('mitra_survei.id_posisi_mitra')
                    ->with(['mitra', 'posisiMitra']); // Eager load relasi mitra dan posisiMitra
            }
        ])
            ->withCount([
                'mitraSurveis as mitra_survei_count' => function ($query) {
                    $query->whereNotNull('mitra_survei.id_posisi_mitra');
                }
            ])
            ->when($request->filled('tahun'), function ($query) use ($request) {
                $query->whereYear('bulan_dominan', $request->tahun);
            })
            ->when($request->filled('bulan'), function ($query) use ($request) {
                $query->whereMonth('bulan_dominan', $request->bulan);
            })
            ->when($request->filled('nama_survei'), function ($query) use ($request) {
                $query->where('nama_survei', $request->nama_survei);
            })
            ->orderBy('status_survei')
            ->paginate(10);

        // Hitung total survei yang pernah diikuti oleh setiap mitra
        $mitraHighlight = collect();

        if ($request->filled('tahun') || $request->filled('bulan')) {
            $mitraHighlight = DB::table('mitra_survei')
                ->join('survei', 'mitra_survei.id_survei', '=', 'survei.id_survei')
                ->select('mitra_survei.id_mitra', DB::raw('COUNT(DISTINCT mitra_survei.id_survei) as total'))
                ->whereNotNull('mitra_survei.id_posisi_mitra') // Ubah dari posisi_mitra ke id_posisi_mitra
                ->when($request->filled('tahun'), function ($query) use ($request) {
                    $query->whereYear('survei.bulan_dominan', $request->tahun);
                })
                ->when($request->filled('bulan'), function ($query) use ($request) {
                    $query->whereMonth('survei.bulan_dominan', $request->bulan);
                })
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
        // Ambil data survei beserta relasi mitra + posisi_mitra melalui mitraSurvei
        $survey = Survei::with([
            'mitraSurveis' => function ($query) {
                $query->with(['mitra', 'posisiMitra']);
            }
        ])
            ->select('id_survei', 'status_survei', 'nama_survei', 'jadwal_kegiatan', 'jadwal_berakhir_kegiatan', 'kro', 'tim')
            ->where('id_survei', $id_survei)
            ->firstOrFail();
        \Carbon\Carbon::setLocale('id');

        // OPTION FILTER TAHUN
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

        // OPTION FILTER BULAN
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
            ->whereHas('mitras', function ($q) use ($survey) {
                $q->whereDate('tahun', '<=', $survey->jadwal_kegiatan)
                    ->whereDate('tahun_selesai', '>=', $survey->jadwal_berakhir_kegiatan)
                    ->where('status_pekerjaan', 0);
            })
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
            ->get(['id_kecamatan', 'kode_kecamatan', 'nama_kecamatan']);

        // FILTER NAMA MITRA
        $namaMitraOptions = Mitra::whereDate('tahun', '<=', $survey->jadwal_kegiatan)
            ->whereDate('tahun_selesai', '>=', $survey->jadwal_berakhir_kegiatan)
            ->where('status_pekerjaan', 0)
            ->select('nama_lengkap')
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

        // QUERY UTAMA MITRA dengan total_survei menggunakan subquery
        $mitrasQuery = Mitra::with([
            'kecamatan',
            'mitraSurveis' => function ($query) use ($id_survei) {
                $query->where('mitra_survei.id_survei', $id_survei)
                    ->with('posisiMitra'); // Ini yang benar karena posisiMitra adalah relasi dari MitraSurvei
            }
        ])
            ->leftJoin('mitra_survei', function ($join) use ($id_survei) {
                $join->on('mitra.id_mitra', '=', 'mitra_survei.id_mitra')
                    ->where('mitra_survei.id_survei', '=', $id_survei);
            })
            ->leftJoin('posisi_mitra', 'mitra_survei.id_posisi_mitra', '=', 'posisi_mitra.id_posisi_mitra')
            ->select('mitra.*')
            ->selectRaw('SUM(mitra_survei.id_survei = ?) as isFollowingSurvey', [$id_survei])
            ->selectRaw('MAX(CASE WHEN mitra_survei.id_survei = ? THEN mitra_survei.vol ELSE NULL END) as vol', [$id_survei])
            ->selectRaw('MAX(posisi_mitra.rate_honor) as rate_honor')
            ->selectRaw('MAX(CASE WHEN mitra_survei.id_survei = ? THEN mitra_survei.id_posisi_mitra ELSE NULL END) as id_posisi_mitra', [$id_survei])
            ->selectRaw('MAX(CASE WHEN mitra_survei.id_survei = ? THEN posisi_mitra.nama_posisi ELSE NULL END) as nama_posisi', [$id_survei])
            // Subquery untuk total_survei
            ->addSelect([
                'total_survei' => MitraSurvei::selectRaw('COUNT(*)')
                    ->whereColumn('mitra_survei.id_mitra', 'mitra.id_mitra')
                    ->whereHas('survei', function ($q) use ($request) {
                        $q->whereDate('jadwal_kegiatan', '>=', DB::raw('mitra.tahun'))
                            ->whereDate('jadwal_berakhir_kegiatan', '<=', DB::raw('mitra.tahun_selesai'));
                        if ($request->filled('bulan')) {
                            $q->whereMonth('bulan_dominan', $request->bulan);
                        }
                        if ($request->filled('tahun')) {
                            $q->whereYear('bulan_dominan', $request->tahun);
                        }
                    })
            ])
            ->groupBy('mitra.id_mitra')
            // Filter utama berdasarkan jadwal survei
            ->whereDate('mitra.tahun', '<=', $survey->jadwal_kegiatan)
            ->whereDate('mitra.tahun_selesai', '>=', $survey->jadwal_berakhir_kegiatan)
            ->where('mitra.status_pekerjaan', 0);

        // Tambahkan filter berdasarkan request
        $mitrasQuery->when($request->filled('tahun'), function ($query) use ($request) {
            $query->whereYear('mitra.tahun', '<=', $request->tahun)
                ->whereYear('mitra.tahun_selesai', '>=', $request->tahun);
        })
            ->when($request->filled('bulan'), function ($query) use ($request) {
                $query->whereMonth('mitra.tahun', '<=', $request->bulan)
                    ->whereMonth('mitra.tahun_selesai', '>=', $request->bulan);
            })
            ->when($request->filled('kecamatan'), function ($query) use ($request) {
                $query->where('mitra.id_kecamatan', $request->kecamatan);
            })
            ->when($request->filled('nama_lengkap'), function ($query) use ($request) {
                $query->where('mitra.nama_lengkap', $request->nama_lengkap);
            });

        // Filter status partisipasi
        if ($request->filled('status_mitra')) {
            if ($request->status_mitra == 'ikut') {
                $mitrasQuery->whereHas('mitraSurvei', function ($q) use ($id_survei) {
                    $q->where('mitra_survei.id_survei', $id_survei);
                });
            } elseif ($request->status_mitra == 'tidak_ikut') {
                $mitrasQuery->whereDoesntHave('mitraSurvei', function ($q) use ($id_survei) {
                    $q->where('mitra_survei.id_survei', $id_survei);
                });
            }
        }

        $posisiMitraOptions = PosisiMitra::all();

        // Paginasi dan order by
        $mitras = $mitrasQuery->orderByDesc('isFollowingSurvey')->paginate(10);

        return view('mitrabps.editSurvei', compact(
            'survey',
            'mitras',
            'tahunOptions',
            'bulanOptions',
            'kecamatanOptions',
            'namaMitraOptions',
            'posisiMitraOptions',
            'request'
        ));
    }



    public function updateMitraOnSurvei(Request $request, $id_survei, $id_mitra)
    {
        // Validasi input dari form
        $request->validate([
            'vol' => 'required|string|max:255', // Volume harus diisi
            'id_posisi_mitra' => 'required|exists:posisi_mitra,id_posisi_mitra' // ID posisi harus ada di tabel posisi_mitra
        ]);

        // Ambil data survei
        $survey = Survei::findOrFail($id_survei);

        // Ambil data MitraSurvei yang akan diupdate
        $mitraSurvei = MitraSurvei::where('id_survei', $id_survei)
            ->where('id_mitra', $id_mitra)
            ->firstOrFail();

        // Ambil data posisi mitra yang dipilih
        $posisi = PosisiMitra::findOrFail($request->id_posisi_mitra);

        // Hitung total honor mitra pada bulan survei (selain data yang sedang diedit)
        $totalHonorBulanIni = MitraSurvei::join('survei', 'mitra_survei.id_survei', '=', 'survei.id_survei')
            ->join('posisi_mitra', 'mitra_survei.id_posisi_mitra', '=', 'posisi_mitra.id_posisi_mitra')
            ->where('mitra_survei.id_mitra', $id_mitra)
            ->where('survei.bulan_dominan', $survey->bulan_dominan)
            ->where(function ($query) use ($id_survei) {
                $query->where('mitra_survei.id_survei', '!=', $id_survei); // Tidak termasuk data yang sedang diedit
            })
            ->sum(DB::raw('posisi_mitra.rate_honor * mitra_survei.vol')); // Hitung total honor

        // Hitung honor yang akan ditambahkan dari data yang diupdate
        $honorYangAkanDitambahkan = $posisi->rate_honor * $request->input('vol');

        // Hitung total honor setelah update
        $totalHonorSetelahUpdate = $totalHonorBulanIni + $honorYangAkanDitambahkan;

        // Validasi apakah total honor melebihi batas maksimum
        if ($totalHonorSetelahUpdate > 4000000 && !$request->has('force_add')) {
            return redirect()->back()
                ->with('confirm', [
                    'message' => "Mitra tidak bisa diperbarui karena total honor di bulan " .
                        \Carbon\Carbon::parse($survey->bulan_dominan)->locale('id')->translatedFormat('F Y') .
                        " akan melebihi Rp 4.000.000 (Total saat ini: Rp " . number_format($totalHonorBulanIni, 0, ',', '.') .
                        "). Tetap ingin menyimpan perubahan mitra ini?",
                    'data' => $request->all()
                ])
                ->with('id_mitra', $id_mitra);
        }

        // Update data mitra survei
        $mitraSurvei->vol = $request->input('vol'); // Update volume
        $mitraSurvei->id_posisi_mitra = $posisi->id_posisi_mitra; // Update ID posisi mitra
        $mitraSurvei->tgl_ikut_survei = now(); // Update tanggal ikut survei
        $mitraSurvei->save();

        $message = 'Mitra berhasil diperbarui!';
        if ($totalHonorSetelahUpdate > 4000000) {
            $message .= ' Perhatian: Total honor mitra melebihi batas Rp 4.000.000';
        }

        return redirect()->back()->with('success', $message);
    }


    public function deleteMitraFromSurvei($id_survei, $id_mitra)
    {
        $mitraSurvei = MitraSurvei::where('id_survei', $id_survei)
            ->where('id_mitra', $id_mitra)
            ->firstOrFail();

        $mitraSurvei->delete();

        return redirect()->back()->with('success', 'Mitra berhasil dihapus dari survei!');
    }

    public function toggleMitraSurvey(Request $request, $id_survei, $id_mitra)
    {
        // Validasi input dari form
        $request->validate([
            'vol' => 'required|numeric|min:1', // Volume harus angka dan minimal 1
            'id_posisi_mitra' => 'required|exists:posisi_mitra,id_posisi_mitra' // ID posisi harus ada di tabel posisi_mitra
        ], [
            'vol.required' => 'Volume harus diisi',
            'vol.numeric' => 'Volume harus berupa angka',
            'vol.min' => 'Volume minimal harus 1',
            'id_posisi_mitra.required' => 'Posisi mitra harus dipilih',
            'id_posisi_mitra.exists' => 'Posisi mitra yang dipilih tidak valid'
        ]);

        // Ambil data survei
        $survey = Survei::findOrFail($id_survei);

        // Ambil data mitra
        $mitra = Mitra::findOrFail($id_mitra);

        // Cek apakah tanggal hari ini masih dalam periode survei
        $today = now()->toDateString();
        if ($today > $survey->jadwal_berakhir_kegiatan) {
            return redirect()->back()
                ->with('error', "Tidak bisa menambahkan mitra karena survei sudah berakhir pada {$survey->jadwal_berakhir_kegiatan}")
                ->withInput();
        }

        // Ambil data posisi mitra yang dipilih
        $posisi = PosisiMitra::findOrFail($request->id_posisi_mitra);
        $rateHonor = $posisi->rate_honor; // Ambil rate honor dari posisi mitra
        $honorYangAkanDitambahkan = $rateHonor * $request->vol; // Hitung honor yang akan ditambahkan

        // Hitung total honor mitra pada bulan survei ini
        $totalHonorBulanIni = MitraSurvei::join('survei', 'mitra_survei.id_survei', '=', 'survei.id_survei')
            ->join('posisi_mitra', 'mitra_survei.id_posisi_mitra', '=', 'posisi_mitra.id_posisi_mitra')
            ->where('mitra_survei.id_mitra', $id_mitra)
            ->where('survei.bulan_dominan', $survey->bulan_dominan)
            ->sum(DB::raw('posisi_mitra.rate_honor * mitra_survei.vol')); // Hitung total honor

        // Hitung total honor setelah ditambah dengan honor survei ini
        $totalHonorSetelahDitambah = $totalHonorBulanIni + $honorYangAkanDitambahkan;

        // Validasi apakah total honor melebihi batas maksimum
        if ($totalHonorSetelahDitambah > 4000000 && !$request->has('force_add')) {
            return redirect()->back()
                ->with('confirm', [
                    'message' => "Mitra tidak bisa ditambahkan karena total honor di bulan " .
                        \Carbon\Carbon::parse($survey->bulan_dominan)->locale('id')->translatedFormat('F Y') .
                        " akan melebihi Rp 4.000.000 (Total saat ini: Rp " . number_format($totalHonorBulanIni, 0, ',', '.') .
                        "). Tetap ingin menambahkan mitra ini?",
                    'data' => array_merge($request->all(), ['rate_honor' => $rateHonor])
                ])
                ->with('id_mitra', $id_mitra);
        }

        // Cek apakah mitra sudah terdaftar di survei ini
        $mitra_survei = MitraSurvei::where('id_survei', $id_survei)
            ->where('id_mitra', $id_mitra)
            ->first();

        // Data yang akan disimpan/diupdate
        $data = [
            'vol' => $request->vol, // Volume
            'id_posisi_mitra' => $request->id_posisi_mitra, // ID posisi mitra
            'nilai' => null, // Bisa disesuaikan jika ada nilai lain
            'catatan' => null // Bisa disesuaikan jika ada catatan
        ];

        // Jika mitra sudah ada di survei, update datanya
        if ($mitra_survei) {
            $mitra_survei->update($data);
        } else { // Jika mitra belum ada, buat data baru
            $data['id_mitra'] = $id_mitra;
            $data['id_survei'] = $id_survei;
            $data['tgl_ikut_survei'] = ($today >= $survey->jadwal_kegiatan && $today <= $survey->jadwal_berakhir_kegiatan)
                ? $today
                : $survey->jadwal_kegiatan; // Tanggal ikut survei
            MitraSurvei::create($data);
        }

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

            $errorMessages = $import->getErrorMessages();
            $honorWarningMessages = $import->getHonorWarningMessages();

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
        // Validasi input (hapus 'bulan_dominan' dan 'status_survei')
        $validated = $request->validate([
            'nama_survei' => 'required|string|max:1024',
            'kro' => 'required|string|max:1024',
            'jadwal_kegiatan' => 'required|date',
            'jadwal_berakhir_kegiatan' => 'required|date',
            'tim' => 'required|string|max:1024',
        ]);

        // Fungsi cari bulan dominan
        $getDominantMonthYear = function ($startDate, $endDate) {
            $start = \Carbon\Carbon::parse($startDate);
            $end = \Carbon\Carbon::parse($endDate);

            $months = collect();
            for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                $months->push($date->format('m-Y'));
            }

            return $months->countBy()->sortDesc()->keys()->first(); // e.g. "04-2029"
        };

        // Hitung dan set nilai bulan_dominan
        $dominantMonthYear = $getDominantMonthYear($validated['jadwal_kegiatan'], $validated['jadwal_berakhir_kegiatan']);
        [$bulan, $tahun] = explode('-', $dominantMonthYear);
        $validated['bulan_dominan'] = \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->toDateString();

        // Set status_survei berdasarkan tanggal hari ini
        $today = now();
        $startDate = \Carbon\Carbon::parse($validated['jadwal_kegiatan']);
        $endDate = \Carbon\Carbon::parse($validated['jadwal_berakhir_kegiatan']);

        if ($today->lt($startDate)) {
            $validated['status_survei'] = 1; // Belum dimulai
        } elseif ($today->gt($endDate)) {
            $validated['status_survei'] = 3; // Sudah selesai
        } else {
            $validated['status_survei'] = 2; // Sedang berjalan
        }

        // Tambahkan nilai default
        $validated['id_provinsi'] = 35; // Jatim
        $validated['id_kabupaten'] = 16; // Mojokerto

        // Cek duplikasi data
        $existingSurvei = Survei::where('nama_survei', $validated['nama_survei'])
            ->where('jadwal_kegiatan', $startDate->toDateString())
            ->where('jadwal_berakhir_kegiatan', $endDate->toDateString())
            ->where('bulan_dominan', $validated['bulan_dominan'])
            ->first();

        if ($existingSurvei) {
            // Update data yang sudah ada
            $existingSurvei->update([
                'kro' => $validated['kro'],
                'status_survei' => $validated['status_survei'],
                'tim' => $validated['tim'],
                'updated_at' => now()
            ]);

            return redirect()->back()->with('info', 'Data survei sudah ada dan telah diperbarui!');
        }

        // Simpan data ke database jika tidak ada duplikat
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
