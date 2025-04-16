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


class MitraController extends Controller
{

    // public function index(Request $request)
    // {
    //     // Query awal dengan relasi
    //     $mitras = Mitra::with('kecamatan')
    //         ->withCount('mitraSurvei');

    //     // Filter nama mitra
    //     if ($request->filled('search')) {
    //         $mitras->where('nama_lengkap', 'like', '%' . $request->search . '%');
    //     }

    //     // Filter kecamatan
    //     if ($request->filled('kecamatan')) {
    //         $mitras->whereHas('kecamatan', function ($query) use ($request) {
    //             $query->where('nama_kecamatan', 'like', '%' . $request->kecamatan . '%');
    //         });
    //     }

    //     // Filter berdasarkan mitra yang dipilih dari dropdown
    //     if ($request->filled('mitra')) {
    //         $mitras->where('id_mitra', $request->mitra);
    //     }

    //     // Pagination
    //     $mitras = $mitras->paginate(10);
    
    //     // Daftar kecamatan untuk dropdown filter
    //     $kecamatans = Kecamatan::pluck('nama_kecamatan', 'id_kecamatan');

    //     // Daftar mitra untuk dropdown filter
    //     $mitrasForDropdown = Mitra::select('id_mitra', 'nama_lengkap')
    //     ->orderBy('nama_lengkap', 'asc')
    //     ->get();


    //     return view('mitrabps.daftarMitra', compact('mitras', 'kecamatans', 'mitrasForDropdown'));
    // }

    public function index(Request $request)
    {
        \Carbon\Carbon::setLocale('id');
        
        // Ambil daftar tahun dari tabel Mitra dan Survei (DISTINCT)
        $tahunOptions = Mitra::selectRaw('YEAR(tahun) as tahun')
            ->union(
                Survei::query()->selectRaw('YEAR(jadwal_kegiatan) as tahun')
            )
            ->orderByDesc('tahun')
            ->pluck('tahun', 'tahun');

        // Daftar bulan berdasarkan tahun yang dipilih
        $bulanOptions = [];
        if ($request->filled('tahun')) {
            $bulanOptions = Mitra::selectRaw('MONTH(tahun) as bulan')
                ->whereYear('tahun', $request->tahun)
                ->union(
                    Survei::query()->selectRaw('MONTH(jadwal_kegiatan) as bulan')
                        ->whereYear('jadwal_kegiatan', $request->tahun)
                )
                ->orderBy('bulan')
                ->pluck('bulan', 'bulan')
                ->mapWithKeys(function($month) {
                    $monthNumber = str_pad($month, 2, '0', STR_PAD_LEFT);
                    return [
                        $monthNumber => \Carbon\Carbon::create()->month($month)->translatedFormat('F')
                    ];
                });
        }

        // Filter kecamatan berdasarkan tahun/bulan (jika ada)
        $kecamatanOptions = Kecamatan::query()
            ->when($request->filled('tahun') || $request->filled('bulan'), function($query) use ($request) {
                $query->whereHas('mitras', function($q) use ($request) {
                    if ($request->filled('tahun')) {
                        $q->where(function($query) use ($request) {
                            $query->whereYear('tahun', '<=', $request->tahun)
                                ->whereYear('tahun_selesai', '>=', $request->tahun);
                        });
                    }
                    if ($request->filled('bulan')) {
                        $q->where(function($query) use ($request) {
                            $query->whereMonth('tahun', '<=', $request->bulan)
                                ->whereMonth('tahun_selesai', '>=', $request->bulan);
                        });
                    }
                });
            })
            ->orderBy('nama_kecamatan')
            ->get(['nama_kecamatan', 'id_kecamatan', 'kode_kecamatan']);

        // Filter nama mitra berdasarkan input pengguna
        $namaMitraOptions = Mitra::select('nama_lengkap')
            ->distinct()
            ->when($request->filled('tahun'), function($query) use ($request) {
                $query->whereYear('tahun', '<=', $request->tahun)
                      ->whereYear('tahun_selesai', '>=', $request->tahun);
            })
            ->when($request->filled('bulan'), function($query) use ($request) {
                $query->whereMonth('tahun', '<=', $request->bulan)
                      ->whereMonth('tahun_selesai', '>=', $request->bulan);
            })
            ->when($request->filled('kecamatan'), function($query) use ($request) {
                $query->where('id_kecamatan', $request->kecamatan);
            })
            ->pluck('nama_lengkap', 'nama_lengkap');

        // Query utama untuk data mitra (dengan relasi & pagination)
        $mitras = Mitra::with(['kecamatan','mitraSurvei' 
            => function ($query) {$query->select('id_mitra', 'honor', 'vol');}])
            ->withCount('mitraSurvei')
                ->when($request->filled('tahun'), function($query) use ($request) {
                    $query->whereYear('tahun', '<=', $request->tahun)
                        ->whereYear('tahun_selesai', '>=', $request->tahun);
                })
                ->when($request->filled('bulan'), function($query) use ($request) {
                    $query->whereMonth('tahun', '<=', $request->bulan)
                        ->whereMonth('tahun_selesai', '>=', $request->bulan);
                })
                ->when($request->filled('kecamatan'), function($query) use ($request) {
                    $query->where('id_kecamatan', $request->kecamatan);
                })
                ->when($request->filled('nama_lengkap'), function($query) use ($request) {
                    $query->where('nama_lengkap', $request->nama_lengkap);
                })
            ->orderBy('nama_lengkap')
            ->paginate(10);


            $mitras->getCollection()->transform(function ($mitra) {
                $totalHonor = $mitra->mitraSurvei->sum(function ($item) {
                    return $item->honor * $item->vol;
                });
            
                $mitra->total_honor = number_format($totalHonor, 0, ',', '.'); // Format rupiah tanpa Rp
                return $mitra;
            });
            

        return view('mitrabps.daftarMitra', compact(
            'mitras', 
            'tahunOptions', 
            'bulanOptions', 
            'kecamatanOptions', 
            'namaMitraOptions'
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
                // Tambahkan pengecekan status survei
                if ($item->survei->status_survei == 3) { // Misalnya status 3 berarti survei selesai
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
            'profileImage' // Tambahkan ini
        ));
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
                ->with('success', 'Data survei berhasil diimport!');
                
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
