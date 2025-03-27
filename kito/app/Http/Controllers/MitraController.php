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
        
        // Daftar tahun yang tersedia
        $tahunOptions = Mitra::selectRaw('DISTINCT YEAR(tahun) as tahun')
            ->orderByDesc('tahun')
            ->pluck('tahun', 'tahun');

        // Daftar bulan berdasarkan tahun yang dipilih
        $bulanOptions = [];
        if ($request->filled('tahun')) {
            $bulanOptions = Mitra::selectRaw('DISTINCT MONTH(tahun) as bulan')
                ->whereYear('tahun', $request->tahun)
                ->orderBy('bulan')
                ->pluck('bulan', 'bulan')
                ->mapWithKeys(function($month) {
                    $monthNumber = str_pad($month, 2, '0', STR_PAD_LEFT);
                    return [
                        $monthNumber => \Carbon\Carbon::create()->month($month)->translatedFormat('F')
                    ];
                });
        }

        // Daftar kecamatan berdasarkan tahun dan bulan yang dipilih
        $kecamatanOptions = Kecamatan::query()
            ->when($request->filled('tahun') || $request->filled('bulan'), function($query) use ($request) {
                $query->whereHas('mitras', function($q) use ($request) {
                    if ($request->filled('tahun')) {
                        $q->whereYear('tahun', $request->tahun);
                    }
                    if ($request->filled('bulan')) {
                        $q->whereMonth('tahun', $request->bulan);
                    }
                });
            })
            ->orderBy('nama_kecamatan')
            ->get(['nama_kecamatan', 'id_kecamatan', 'kode_kecamatan']);

        // Daftar nama survei berdasarkan filter
        $namaMitraOptions = Mitra::select('nama_lengkap')
            ->distinct()
            ->when($request->filled('tahun'), function($query) use ($request) {
                $query->whereYear('tahun', $request->tahun);
            })
            ->when($request->filled('bulan'), function($query) use ($request) {
                $query->whereMonth('tahun', $request->bulan);
            })
            ->when($request->filled('kecamatan'), function($query) use ($request) {
                $query->where('id_kecamatan', $request->kecamatan);
            })
            ->orderBy('nama_lengkap')
            ->pluck('nama_lengkap', 'nama_lengkap');

        // Query utama
        $mitras = Mitra::with(['kecamatan', 'mitraSurvei'])
            ->withCount('mitraSurvei')
            ->when($request->filled('tahun'), function($query) use ($request) {
                $query->whereYear('tahun', $request->tahun);
            })
            ->when($request->filled('bulan'), function($query) use ($request) {
                $query->whereMonth('tahun', $request->bulan);
            })
            ->when($request->filled('kecamatan'), function($query) use ($request) {
                $query->where('id_kecamatan', $request->kecamatan);
            })
            ->when($request->filled('nama_lengkap'), function($query) use ($request) {
                $query->where('nama_lengkap', $request->nama_lengkap);
            })
            ->paginate(10);

        return view('mitrabps.daftarMitra', compact(
            'mitras',
            'tahunOptions',
            'bulanOptions',
            'kecamatanOptions',
            'namaMitraOptions',
            'request'
        ));
    }

    public function profilMitra(Request $request, $id_mitra)
    {
        \Carbon\Carbon::setLocale('id');
        
        $mits = Mitra::with(['kecamatan', 'desa'])->findOrFail($id_mitra);
        
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
        
        return view('mitrabps.profilMitra', compact(
            'mits', 
            'survei',
            'tahunOptions',
            'bulanOptions',
            'namaSurveiOptions',
            'request'
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
            'file' => 'required|file|mimes:xls,xlsx'
        ]);

        $import = new MitraImport();
        
        try {
            Excel::import($import, $request->file('file'));
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];
            foreach ($failures as $failure) {
                $errorMessages[] = 'Baris ' . $failure->row() . ': ' . implode(', ', $failure->errors());
            }
            return redirect()->back()->withErrors(['file' => $errorMessages]);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['file' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
        
        if (!empty($import->getErrors())) {
            return redirect()->back()
                ->withErrors(['file' => $import->getErrors()]);
        }

        return redirect()->back()->with('success', 'Data Mitra berhasil diimport!');
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
