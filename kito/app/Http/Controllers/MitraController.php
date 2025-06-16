<?php

namespace App\Http\Controllers;

use App\Models\Survei;
use App\Models\Mitra;
use Illuminate\Http\Request;
use App\Models\Provinsi;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Desa;
use App\Models\MitraSurvei;
use App\Imports\MitraImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class MitraController extends Controller
{

    public function index(Request $request)
    {
        \Carbon\Carbon::setLocale('id');

        // Bagian filter (tahun, bulan, kecamatan, nama) tidak berubah
        // ... (kode filter Anda dari baris 20-134 tetap sama)
        $tahunOptions = Mitra::selectRaw('YEAR(tahun) as tahun')->union(Mitra::query()->selectRaw('YEAR(tahun_selesai) as tahun'))->orderByDesc('tahun')->pluck('tahun', 'tahun');

        $bulanOptions = [];
        if ($request->filled('tahun')) {
            $mitrasAktif = Mitra::whereYear('tahun', '<=', $request->tahun)->whereYear('tahun_selesai', '>=', $request->tahun)->get();
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
            $bulanOptions = $bulanValid->unique()->sort()->mapWithKeys(fn($m) => [str_pad($m, 2, '0', STR_PAD_LEFT) => \Carbon\Carbon::create()->month($m)->translatedFormat('F')]);
        }

        $kecamatanOptions = Kecamatan::query()
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
            ->orderBy('kode_kecamatan')->get(['nama_kecamatan', 'id_kecamatan', 'kode_kecamatan']);

        $namaMitraOptions = Mitra::select('nama_lengkap')->distinct()
            ->when($request->filled('tahun'), fn($q) => $q->whereYear('tahun', '<=', $request->tahun)->whereYear('tahun_selesai', '>=', $request->tahun))
            ->when($request->filled('bulan'), fn($q) => $q->whereMonth('tahun', '<=', $request->bulan)->whereMonth('tahun_selesai', '>=', $request->bulan))
            ->when($request->filled('kecamatan'), fn($q) => $q->where('id_kecamatan', $request->kecamatan))
            ->orderBy('nama_lengkap')->pluck('nama_lengkap', 'nama_lengkap');

        // #################### PERUBAHAN DI SINI ####################
        // QUERY UTAMA DENGAN PERHITUNGAN HONOR YANG DIPERBARUI
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
                    }),

                // Kalkulasi total_honor diubah untuk mengambil rate_honor langsung dari mitra_survei
                'total_honor' => MitraSurvei::selectRaw('COALESCE(SUM(vol * rate_honor), 0)')
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
            ->when($request->filled('bulan'), fn($q) => $q->orderByDesc('total_honor'), fn($q) => $q->orderByDesc('total_survei'))
            ->when($request->filled('tahun'), fn($q) => $q->whereYear('tahun', '<=', $request->tahun)->whereYear('tahun_selesai', '>=', $request->tahun))
            ->when($request->filled('bulan'), fn($q) => $q->whereMonth('tahun', '<=', $request->bulan)->whereMonth('tahun_selesai', '>=', $request->bulan))
            ->when($request->filled('kecamatan'), fn($q) => $q->where('id_kecamatan', $request->kecamatan))
            ->when($request->filled('nama_lengkap'), fn($q) => $q->where('nama_lengkap', $request->nama_lengkap));

        // Filter status partisipasi tidak berubah
        if ($request->filled('status_mitra')) { /* ... */
        }

        // #################### PERUBAHAN DI SINI ####################
        // HITUNG TOTAL HONOR KESELURUHAN DENGAN LOGIKA BARU
        $totalHonor = MitraSurvei::whereHas('mitra', fn($q) => $q->whereIn('id_mitra', (clone $mitrasQuery)->pluck('id_mitra')))
            ->whereHas('survei', function ($q) use ($request) {
                if ($request->filled('bulan')) {
                    $q->whereMonth('bulan_dominan', $request->bulan);
                }
                if ($request->filled('tahun')) {
                    $q->whereYear('bulan_dominan', $request->tahun);
                }
            })
            ->sum(DB::raw('vol * rate_honor')); // Kalkulasi langsung di database

        $mitras = $mitrasQuery->paginate(10);

        return view('mitrabps.daftarMitra', compact('mitras', 'tahunOptions', 'bulanOptions', 'kecamatanOptions', 'namaMitraOptions', 'totalHonor', 'request'));
    }



    public function profilMitra(Request $request, $id_mitra)
    {
        \Carbon\Carbon::setLocale('id');
        $mits = Mitra::with(['kecamatan', 'desa'])->findOrFail($id_mitra);
        $githubBaseUrl = 'https://raw.githubusercontent.com/mainchar42/assetgambar/main/myGambar/';
        $profileImage = $githubBaseUrl . $mits->sobat_id . '.jpg';

        // Bagian filter tidak berubah
        $tahunOptions = Survei::selectRaw('DISTINCT YEAR(bulan_dominan) as tahun')->join('mitra_survei', 'survei.id_survei', '=', 'mitra_survei.id_survei')->where('mitra_survei.id_mitra', $id_mitra)->orderByDesc('tahun')->pluck('tahun', 'tahun');

        $bulanOptions = [];
        if ($request->filled('tahun')) {
            $bulanOptions = Survei::selectRaw('DISTINCT MONTH(bulan_dominan) as bulan')->join('mitra_survei', 'survei.id_survei', '=', 'mitra_survei.id_survei')->where('mitra_survei.id_mitra', $id_mitra)->whereYear('bulan_dominan', $request->tahun)->orderBy('bulan')->pluck('bulan', 'bulan')->mapWithKeys(fn($m) => [str_pad($m, 2, '0', STR_PAD_LEFT) => \Carbon\Carbon::create()->month($m)->translatedFormat('F')]);
        }

        $namaSurveiOptions = Survei::select('nama_survei')->distinct()->join('mitra_survei', 'survei.id_survei', '=', 'mitra_survei.id_survei')->where('mitra_survei.id_mitra', $id_mitra)
            ->when($request->filled('tahun'), fn($q) => $q->whereYear('bulan_dominan', $request->tahun))
            ->when($request->filled('bulan'), fn($q) => $q->whereMonth('bulan_dominan', $request->bulan))
            ->orderBy('nama_survei')->pluck('nama_survei', 'nama_survei');

        // Query survei mitra dengan filter
        $query = MitraSurvei::with(['survei', 'posisiMitra'])->where('id_mitra', $id_mitra);
        if ($request->filled('nama_survei')) {
            $query->whereHas('survei', fn($q) => $q->where('nama_survei', $request->nama_survei));
        }
        if ($request->filled('bulan')) {
            $query->whereHas('survei', fn($q) => $q->whereMonth('bulan_dominan', $request->bulan));
        }
        if ($request->filled('tahun')) {
            $query->whereHas('survei', fn($q) => $q->whereYear('bulan_dominan', $request->tahun));
        }

        $survei = $query->get()->sortByDesc(fn($item) => optional($item->survei)->bulan_dominan)->sortByDesc(fn($item) => is_null($item->nilai));

        // #################### PERUBAHAN DI SINI ####################
        // HITUNG TOTAL GAJI DENGAN LOGIKA BARU
        $totalGaji = 0;
        $showTotalGaji = $request->filled('bulan');
        if ($showTotalGaji) {
            foreach ($survei as $item) {
                // Menggunakan rate_honor langsung dari objek $item (MitraSurvei)
                if ($item->survei && $item->vol && isset($item->rate_honor)) {
                    $totalGaji += $item->vol * $item->rate_honor;
                }
            }
        }

        return view('mitrabps.profilMitra', compact('mits', 'survei', 'tahunOptions', 'bulanOptions', 'namaSurveiOptions', 'request', 'totalGaji', 'showTotalGaji', 'profileImage'));
    }



    public function updateDetailPekerjaan(Request $request, $id_mitra)
    {
        $mitra = Mitra::findOrFail($id_mitra);
        $mitra->detail_pekerjaan = $request->detail_pekerjaan;
        $mitra->save();
        return back()->with('success', 'Detail pekerjaan berhasil diperbarui');
    }

    public function updateStatus($id_mitra)
    {
        $mitra = Mitra::findOrFail($id_mitra);
        $mitra->status_pekerjaan = $mitra->status_pekerjaan == 1 ? 0 : 1;
        $mitra->save();
        return back()->with('success', 'Status pekerjaan berhasil diperbarui');
    }


    public function penilaianMitra($id_mitra, $id_survei)
    {
        \Carbon\Carbon::setLocale('id');
        $surMit = MitraSurvei::with(['survei', 'mitra', 'posisiMitra'])
            ->where('id_mitra', $id_mitra)
            ->where('id_survei', $id_survei)
            ->firstOrFail();
        $githubBaseUrl = 'https://raw.githubusercontent.com/mainchar42/assetgambar/main/myGambar/';
        $profileImage = $githubBaseUrl . $surMit->mitra->sobat_id . '.jpg';
        return view('mitrabps.penilaianMitra', compact('surMit', 'profileImage'));
    }

    public function simpanPenilaian(Request $request)
    {
        // Mendefinisikan pesan error kustom dalam Bahasa Indonesia
        $messages = [
            'id_mitra_survei.required' => 'ID Mitra Survei wajib diisi.',
            'id_mitra_survei.exists'   => 'Mitra Survei yang dipilih tidak valid.',

            'nilai.required' => 'Kolom nilai wajib diisi.',
            'nilai.integer'  => 'Nilai harus berupa angka bulat.',
            'nilai.min'      => 'Nilai minimal :min.',
            'nilai.max'      => 'Nilai maksimal :max.',

            'catatan.string' => 'Catatan harus berupa teks.',
        ];

        // Melakukan validasi dengan pesan kustom
        $request->validate([
            'id_mitra_survei' => 'required|exists:mitra_survei,id_mitra_survei',
            'nilai'           => 'required|integer|min:1|max:5',
            'catatan'         => 'nullable|string'
        ], $messages); // <-- Tambahkan variabel $messages di sini

        // Simpan ke database
        MitraSurvei::where('id_mitra_survei', $request->id_mitra_survei)
            ->update([
                'nilai'   => $request->nilai,
                'catatan' => $request->catatan,
            ]);

        // Ambil id_mitra untuk redirect
        $mitraSurvei = MitraSurvei::find($request->id_mitra_survei);

        return redirect()->route('profilMitra.filter', [
            'id_mitra'  => $mitraSurvei->id_mitra,
            'scroll_to' => 'survei-dikerjakan' // Parameter baru untuk scroll
        ])->with('success', 'Penilaian berhasil disimpan!');
    }

    public function deleteMitra($id_mitra)
    {
        $mitra = Mitra::findOrFail($id_mitra);
        $namaMitra = $mitra->nama_lengkap; // Ambil nama mitra sebelum dihapus

        DB::transaction(function () use ($id_mitra) {
            // 1. Hapus semua relasi di tabel pivot terlebih dahulu
            DB::table('mitra_survei')
                ->where('id_mitra', $id_mitra)
                ->delete();

            // 2. Baru hapus mitranya
            Mitra::findOrFail($id_mitra)->delete();
        });

        return redirect()->route('mitras.filter')
            ->with('success', "Mitra $namaMitra beserta semua relasinya berhasil dihapus");
    }


    public function upExcelMitra(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xls,xlsx|max:2048'
        ]);

        $import = new MitraImport();

        try {
            Excel::import($import, $request->file('file'));

            $successCount = $import->getSuccessCount();
            $failedCount = $import->getFailedCount();
            $rowErrors = $import->getRowErrors();

            // Pesan sukses dasar
            $successMessage = "Import berhasil! {$successCount} data mitra berhasil diproses dan {$failedCount} data mitra gagal diproses.";

            if ($failedCount > 0) {
                // Format error lebih terstruktur
                $formattedErrors = [];
                foreach ($rowErrors as $row => $errors) {
                    // Pastikan $errors adalah array
                    $errorList = is_array($errors) ? $errors : [$errors];

                    foreach ($errorList as $error) {
                        $formattedErrors[] = "{$error}";
                    }
                }

                return redirect()->back()
                    ->with('success', $successMessage)
                    ->with('warning', "{$failedCount} data mitra gagal diproses. Silakan perbaiki dan coba lagi.")
                    ->with('import_errors', $formattedErrors);
            }

            return redirect()->back()->with('success', $successMessage);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', "Terjadi kesalahan saat mengimpor data: " . $e->getMessage())
                ->withInput();
        }
    }
}
