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
    
        // OPTION FILTER TAHUN
        $tahunOptions = Mitra::selectRaw('YEAR(tahun) as tahun')
            ->union(Mitra::query()->selectRaw('YEAR(tahun_selesai) as tahun'))
            ->orderByDesc('tahun')
            ->pluck('tahun', 'tahun');
    
        // OPTION FILTER BULAN (hanya muncul jika tahun dipilih)
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
                'total_honor' => MitraSurvei::selectRaw('COALESCE(SUM(vol * honor), 0)')
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
                    })
            ])
            // Modified sorting logic
            ->when($request->filled('bulan'), function ($query) {
                // If month filter is applied, sort by total_honor
                $query->orderByDesc('total_honor');
            }, function ($query) {
                // Default sorting by total_survei
                $query->orderByDesc('total_survei');
            })
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
        return view('mitrabps.daftarMitra', compact(
            'mitras',
            'tahunOptions',
            'bulanOptions',
            'kecamatanOptions',
            'namaMitraOptions',
            'totalHonor',
            'request'
        ));
    }




    public function profilMitra(Request $request, $id_mitra)
{
    \Carbon\Carbon::setLocale('id');
    
    $mits = Mitra::with(['kecamatan', 'desa'])->findOrFail($id_mitra);

    // Generate GitHub profile image URL
    $githubBaseUrl = 'https://raw.githubusercontent.com/mainchar42/assetgambar/main/myGambar/';
    $profileImage = $githubBaseUrl . $mits->sobat_id . '.jpg';
        
    // Daftar tahun yang tersedia untuk mitra ini
    $tahunOptions = Survei::selectRaw('DISTINCT YEAR(bulan_dominan) as tahun')
        ->join('mitra_survei', 'mitra_survei.id_survei', '=', 'survei.id_survei')
        ->where('mitra_survei.id_mitra', $id_mitra)
        ->orderByDesc('tahun')
        ->pluck('tahun', 'tahun');
    
    // Daftar bulan berdasarkan tahun yang dipilih untuk mitra ini
    $bulanOptions = [];
    if ($request->filled('tahun')) {
        $bulanOptions = Survei::selectRaw('DISTINCT MONTH(bulan_dominan) as bulan')
            ->join('mitra_survei', 'mitra_survei.id_survei', '=', 'survei.id_survei')
            ->where('mitra_survei.id_mitra', $id_mitra)
            ->whereYear('bulan_dominan', $request->tahun)
            ->orderBy('bulan')
            ->pluck('bulan', 'bulan')
            ->mapWithKeys(function($month) {
                $monthNumber = str_pad($month, 2, '0', STR_PAD_LEFT);
                return [
                    $monthNumber => \Carbon\Carbon::create()->month($month)->translatedFormat('F')
                ];
            });
    }
    
    // Daftar nama survei untuk mitra ini
    $namaSurveiOptions = Survei::select('nama_survei')
        ->distinct()
        ->join('mitra_survei', 'mitra_survei.id_survei', '=', 'survei.id_survei')
        ->where('mitra_survei.id_mitra', $id_mitra)
        ->when($request->filled('tahun'), function($query) use ($request) {
            $query->whereYear('bulan_dominan', $request->tahun);
        })
        ->when($request->filled('bulan'), function($query) use ($request) {
            $query->whereMonth('bulan_dominan', $request->bulan);
        })
        ->orderBy('nama_survei')
        ->pluck('nama_survei', 'nama_survei');
    
    // Query survei mitra dengan filter
    $query = MitraSurvei::with(['survei'])->where('id_mitra', $id_mitra);
    
    // Filter nama survei
    if ($request->filled('nama_survei')) {
        $query->whereHas('survei', function ($q) use ($request) {
            $q->where('nama_survei', $request->nama_survei);
        });
    }
    
    // Filter bulan
    if ($request->filled('bulan')) {
        $query->whereHas('survei', function ($q) use ($request) {
            $q->whereMonth('bulan_dominan', $request->bulan);
        });
    }
    
    // Filter tahun
    if ($request->filled('tahun')) {
        $query->whereHas('survei', function ($q) use ($request) {
            $q->whereYear('bulan_dominan', $request->tahun);
        });
    }
    
    $survei = $query->get()
        ->sortByDesc(fn($item) => optional($item->survei)->bulan_dominan)
        ->sortByDesc(fn($item) => is_null($item->nilai));
    
    // Hitung total gaji HANYA jika filter bulan diisi
    $totalGaji = 0;
    $showTotalGaji = $request->filled('bulan'); // Flag untuk menentukan apakah menampilkan total gaji
    
    if ($showTotalGaji) {
        foreach ($survei as $item) {
            if ($item->survei && $item->vol && $item->honor) {
                $totalGaji += $item->vol * $item->honor;
            }
        }
    }

    return view('mitrabps.profilMitra', compact(
        'mits', 
        'survei',
        'tahunOptions',
        'bulanOptions',
        'namaSurveiOptions',
        'request',
        'totalGaji',
        'showTotalGaji', // Kirim flag ini ke view
        'profileImage'
    ));
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

    
    public function penilaianMitra($id_survei)
    {
        $surMit = MitraSurvei::with(['survei','mitra']) // Menarik data survei dan mitra
            ->where('id_survei', $id_survei)
            ->first();

        return view('mitrabps.penilaianMitra', compact('surMit'));
    }

    public function simpanPenilaian(Request $request)
    {
        $request->validate([
            'id_mitra_survei' => 'required|exists:mitra_survei,id_mitra_survei',
            'nilai' => 'required|integer|min:1|max:5',
            'catatan' => 'nullable|string'
        ]);

        // Simpan ke database
        MitraSurvei::where('id_mitra_survei', $request->id_mitra_survei)
            ->update([
                'nilai' => $request->nilai,
                'catatan' => $request->catatan,
            ]);

        return redirect()->back()->with('success', 'Penilaian berhasil disimpan!');
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
            $successMessage = "Import berhasil! {$successCount} data mitra berhasil diproses.";
            
            if ($failedCount > 0) {
                // Format error lebih terstruktur
                $formattedErrors = [];
                foreach ($rowErrors as $row => $errors) {
                    // Pastikan $errors adalah array
                    $errorList = is_array($errors) ? $errors : [$errors];
                    
                    foreach ($errorList as $error) {
                        $formattedErrors[] = "Baris {$row}: {$error}";
                    }
                }
                
                return redirect()->back()
                    ->with('success', $successMessage)
                    ->with('warning', "{$failedCount} data mitra gagal diproses. Silakan perbaiki dan coba lagi.")
                    ->with('import_errors', $formattedErrors);
            }

            return redirect()->back()->with('success', $successMessage);

        } catch (\Exception $e) {
            Log::error('Error importing mitra: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', "Terjadi kesalahan saat mengimpor data: " . $e->getMessage())
                ->withInput();
        }
    }
}
