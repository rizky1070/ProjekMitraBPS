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
            ->orderByDesc('total_honor')
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

        // Generate GitHub profile image URL based on sobatid
        $githubBaseUrl = 'https://raw.githubusercontent.com/mainchar42/assetgambar/main/myGambar/';
        $profileImage = $githubBaseUrl . $mits->sobat_id . '.jpg'; // asumsi format gambar adalah .jpg
            
        // Daftar tahun yang tersedia untuk mitra ini
        $tahunOptions = Survei::selectRaw('DISTINCT YEAR(jadwal_kegiatan) as tahun')
            ->join('mitra_survei', 'mitra_survei.id_survei', '=', 'survei.id_survei')
            ->where('mitra_survei.id_mitra', $id_mitra)
            ->orderByDesc('tahun')
            ->pluck('tahun', 'tahun');
        
        // Daftar bulan berdasarkan tahun yang dipilih untuk mitra ini
        $bulanOptions = [];
        if ($request->filled('tahun')) {
            $bulanOptions = Survei::selectRaw('DISTINCT MONTH(jadwal_kegiatan) as bulan')
                ->join('mitra_survei', 'mitra_survei.id_survei', '=', 'survei.id_survei')
                ->where('mitra_survei.id_mitra', $id_mitra)
                ->whereYear('jadwal_kegiatan', $request->tahun)
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
                $query->whereYear('jadwal_kegiatan', $request->tahun);
            })
            ->when($request->filled('bulan'), function($query) use ($request) {
                $query->whereMonth('jadwal_kegiatan', $request->bulan);
            })
            ->orderBy('nama_survei')
            ->pluck('nama_survei', 'nama_survei');
        
        // Query survei mitra dengan filter
        $query = MitraSurvei::with(['survei' => function($query) {
            $query->with('kecamatan');
        }])->where('id_mitra', $id_mitra);
        
        // Filter nama survei
        if ($request->filled('nama_survei')) {
            $query->whereHas('survei', function ($q) use ($request) {
                $q->where('nama_survei', $request->nama_survei);
            });
        }
        
        // Filter bulan
        if ($request->filled('bulan')) {
            $query->whereHas('survei', function ($q) use ($request) {
                $q->whereMonth('jadwal_kegiatan', $request->bulan);
            });
        }
        
        // Filter tahun
        if ($request->filled('tahun')) {
            $query->whereHas('survei', function ($q) use ($request) {
                $q->whereYear('jadwal_kegiatan', $request->tahun);
            });
        }
        
        $survei = $query->get()
            ->sortByDesc(fn($item) => optional($item->survei)->jadwal_kegiatan)
            ->sortByDesc(fn($item) => is_null($item->nilai)); // Prioritaskan yang belum dinilai
        
        // Hitung total gaji
        $totalGaji = 0;
        foreach ($survei as $item) {
            if ($item->survei && $item->vol && $item->honor) {
                $totalGaji += $item->vol * $item->honor;
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
            'profileImage' // Tambahkan ini
        ));
    }

    public function updateStatus(Request $request, $id_mitra)
    {
        $mitra = Mitra::findOrFail($id_mitra);
        
        // Toggle status pekerjaan (0 menjadi 1, 1 menjadi 0)
        $mitra->status_pekerjaan = $mitra->status_pekerjaan == 1 ? 0 : 1;
        $mitra->save();
        
        return redirect()->back()->with('success', 'Status pekerjaan berhasil diubah');
    }
    
    public function penilaianMitra($id_survei)
    {
        $surMit = MitraSurvei::with(['survei.kecamatan','mitra']) // Menarik data survei dan kecamatan
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

    public function upExcelMitra(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xls,xlsx|max:2048'
        ]);

        $import = new MitraImport();
        
        try {
            Excel::import($import, $request->file('file'));
            
            // Gabungkan semua error (baik dari validasi Laravel maupun custom)
            $allErrors = [];
            
            // Tangkap error dari ValidationException
            if (!empty($import->getErrors())) {
                $allErrors = array_merge($allErrors, $import->getErrors());
            }
            
            if (!empty($allErrors)) {
                return redirect()->back()
                    ->withErrors(['file' => $allErrors])
                    ->withInput();
            }

            return redirect()->back()
                ->with('success', 'Data Mitra berhasil diimport!');
                
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $errorMessages = collect($e->failures())
                ->map(function($failure) {
                    return 'Baris ' . $failure->row() . ' : ' . implode(', ', $failure->errors());
                })
                ->toArray();
                
            return redirect()->back()
                ->withErrors(['file' => $errorMessages])
                ->withInput();
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['file' => ['Terjadi kesalahan sistem: ' . $e->getMessage()]])
                ->withInput();
        }
    }








    //     public function index(Request $request)
// {
//     $query = Mitra::with('kecamatan')->withCount('survei');

    //     // Filter berdasarkan kata kunci pencarian jika ada
//     if ($request->filled('search')) {
//         $query->where('nama_lengkap', 'like', '%' . $request->search . '%');
//     }

    //     $mitras = $query->paginate(10); // Jalankan query di sini

    //     return view('mitrabps.daftarmitrabps', compact('mitras'));
// }

}
