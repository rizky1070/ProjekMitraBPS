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


class DaftarSurveiBpsController extends Controller
{
    public function index(Request $request)
    {
        \Carbon\Carbon::setLocale('id');
        
        // Daftar tahun yang tersedia
        $tahunOptions = Survei::selectRaw('DISTINCT YEAR(jadwal_kegiatan) as tahun')
            ->orderByDesc('tahun')
            ->pluck('tahun', 'tahun');

        // Daftar bulan berdasarkan tahun yang dipilih
        $bulanOptions = [];
        if ($request->filled('tahun')) {
            $bulanOptions = Survei::selectRaw('DISTINCT MONTH(jadwal_kegiatan) as bulan')
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

        // Daftar kecamatan berdasarkan tahun dan bulan yang dipilih
        $kecamatanOptions = Kecamatan::query()
            ->when($request->filled('tahun') || $request->filled('bulan'), function($query) use ($request) {
                $query->whereHas('surveis', function($q) use ($request) {
                    if ($request->filled('tahun')) {
                        $q->whereYear('jadwal_kegiatan', $request->tahun);
                    }
                    if ($request->filled('bulan')) {
                        $q->whereMonth('jadwal_kegiatan', $request->bulan);
                    }
                });
            })
            ->orderBy('nama_kecamatan')
            ->get(['nama_kecamatan', 'kode_kecamatan', 'id_kecamatan']);

        // Daftar nama survei berdasarkan filter
        $namaSurveiOptions = Survei::select('nama_survei')
            ->distinct()
            ->when($request->filled('tahun'), function($query) use ($request) {
                $query->whereYear('jadwal_kegiatan', $request->tahun);
            })
            ->when($request->filled('bulan'), function($query) use ($request) {
                $query->whereMonth('jadwal_kegiatan', $request->bulan);
            })
            ->when($request->filled('kecamatan'), function($query) use ($request) {
                $query->where('id_kecamatan', $request->kecamatan);
            })
            ->orderBy('nama_survei')
            ->pluck('nama_survei', 'nama_survei');

        // Query utama
        $surveys = Survei::with([
                'kecamatan',
                'mitraSurvei' => function ($query) {
                    $query->whereNotNull('mitra_survei.posisi_mitra') // ini penting!
                        ->withPivot('posisi_mitra');
                }
            ])
            ->withCount([
                'mitraSurvei as mitra_survei_count' => function ($query) {
                    $query->whereNotNull('mitra_survei.posisi_mitra');
                }
            ])
            ->when($request->filled('tahun'), function ($query) use ($request) {
                $query->whereYear('jadwal_kegiatan', $request->tahun);
            })
            ->when($request->filled('bulan'), function ($query) use ($request) {
                $query->whereMonth('jadwal_kegiatan', $request->bulan);
            })
            ->when($request->filled('kecamatan'), function ($query) use ($request) {
                $query->where('id_kecamatan', $request->kecamatan);
            })
            ->when($request->filled('nama_survei'), function ($query) use ($request) {
                $query->where('nama_survei', $request->nama_survei);
            })
            ->orderBy('status_survei')
            ->paginate(10);
    
        // Hitung total survei yang pernah diikuti oleh setiap mitra (secara global, tidak hanya di bulan yang dipilih)
        $mitraHighlight = collect();

    if ($request->filled('tahun') || $request->filled('bulan')) {
        $mitraHighlight = DB::table('mitra_survei')
            ->join('survei', 'mitra_survei.id_survei', '=', 'survei.id_survei')
            ->select('mitra_survei.id_mitra', DB::raw('COUNT(DISTINCT mitra_survei.id_survei) as total'))
            ->whereNotNull('mitra_survei.posisi_mitra')
            ->when($request->filled('tahun'), function ($query) use ($request) {
                $query->whereYear('survei.jadwal_kegiatan', $request->tahun);
            })
            ->when($request->filled('bulan'), function ($query) use ($request) {
                $query->whereMonth('survei.jadwal_kegiatan', $request->bulan);
            })
            ->groupBy('mitra_survei.id_mitra')
            ->pluck('total', 'mitra_survei.id_mitra');
    }


        return view('mitrabps.daftarSurvei', compact(
            'surveys',
            'tahunOptions',
            'bulanOptions',
            'kecamatanOptions',
            'namaSurveiOptions',
            'mitraHighlight', 
            'request'
        ));
    }
    

    
    public function editSurvei(Request $request, $id_survei)
    {
        // Ambil data survei beserta relasi mitra + posisi_mitra dari pivot
        $survey = Survei::with([
            'kecamatan',
            'mitra' => function ($query) {
                $query->withPivot('posisi_mitra');
            }
        ])
        ->select('id_survei', 'status_survei', 'nama_survei', 'jadwal_kegiatan', 'jadwal_berakhir_kegiatan', 'kro', 'id_kecamatan', 'tim')
        ->where('id_survei', $id_survei)
        ->firstOrFail();

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
            ->get(['id_kecamatan', 'kode_kecamatan', 'nama_kecamatan']);

        // Daftar nama mitra (nama lengkap)
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

        // Query daftar mitra
        $mitras = Mitra::with([
            'kecamatan',
            'mitraSurvei' => function($query) use ($id_survei) {
                $query->where('mitra_survei.id_survei', $id_survei);
            }
        ])
        ->leftJoin('mitra_survei', 'mitra.id_mitra', '=', 'mitra_survei.id_mitra')
        ->select('mitra.*')
        ->selectRaw('COUNT(mitra_survei.id_survei) as mitra_survei_count')
        ->selectRaw('SUM(mitra_survei.id_survei = ?) as isFollowingSurvey', [$id_survei])
        ->selectRaw('MAX(CASE WHEN mitra_survei.id_survei = ? THEN mitra_survei.vol ELSE NULL END) as vol', [$id_survei])
        ->selectRaw('MAX(CASE WHEN mitra_survei.id_survei = ? THEN mitra_survei.honor ELSE NULL END) as honor', [$id_survei])
        ->selectRaw('MAX(CASE WHEN mitra_survei.id_survei = ? THEN mitra_survei.posisi_mitra ELSE NULL END) as posisi_mitra', [$id_survei])
        ->groupBy('mitra.id_mitra')
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
        ->whereDate('tahun', '<=', $survey->jadwal_kegiatan)
        ->whereDate('tahun_selesai', '>=', $survey->jadwal_berakhir_kegiatan)
        ->orderByDesc('posisi_mitra')
        ->orderByRaw('mitra.id_kecamatan = ? DESC', [$survey->id_kecamatan])
        ->paginate(10);

        return view('mitrabps.editSurvei', compact(
            'survey',
            'mitras',
            'tahunOptions',
            'bulanOptions',
            'kecamatanOptions',
            'namaMitraOptions',
            'request'
        ));
    }



    public function updateMitraOnSurvei(Request $request, $id_survei, $id_mitra)
    {
        $request->validate([
            'vol' => 'required|string|max:255',
            'honor' => 'required|integer',
            'posisi_mitra' => 'required|string|max:255'
        ]);

        $mitraSurvei = MitraSurvei::where('id_survei', $id_survei)
            ->where('id_mitra', $id_mitra)
            ->firstOrFail();

        $mitraSurvei->vol = $request->input('vol');
        $mitraSurvei->honor = $request->input('honor');
        $mitraSurvei->posisi_mitra = $request->input('posisi_mitra');
        $mitraSurvei->tgl_ikut_survei = now();
        $mitraSurvei->save();

        return redirect()->back()->with('success', 'Mitra berhasil diperbarui!');
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
        $request->validate([
            'vol' => 'required|string|max:255',
            'honor' => 'required|integer',
            'posisi_mitra' => 'required|string|max:255'
        ]);

        $survey = Survei::findOrFail($id_survei);
        $mitra = Mitra::findOrFail($id_mitra);

        // Cek apakah mitra sudah terdaftar di survei ini
        $mitra_survei = MitraSurvei::where('id_survei', $id_survei)
            ->where('id_mitra', $id_mitra)
            ->first();

        if ($mitra_survei) {
            // Jika sudah ada, update data
            $mitra_survei->update([
                'vol' => $request->vol,
                'honor' => $request->honor,
                'posisi_mitra' => $request->posisi_mitra
            ]);
        } else {
            // Cek jadwal tumpang tindih dan dapatkan data survei yang bertabrakan
            $conflictingSurveys = $mitra->survei()
                ->where(function($query) use ($survey) {
                    $query->where(function($q) use ($survey) {
                        $q->where('jadwal_kegiatan', '<', $survey->jadwal_berakhir_kegiatan)
                        ->where('jadwal_berakhir_kegiatan', '>', $survey->jadwal_kegiatan);
                    });
                })
                ->get();

            if ($conflictingSurveys->isNotEmpty()) {
                $conflictingNames = $conflictingSurveys->pluck('nama_survei')->implode(', ');
                return redirect()->back()
                    ->with('error', "Mitra sudah terdaftar di survei berikut dengan jadwal yang tumpang tindih : $conflictingNames")
                    ->withInput();
            }

            // Tentukan tgl_ikut_survei berdasarkan kondisi tanggal hari ini
            $today = now()->toDateString(); // Format YYYY-MM-DD
            $start = $survey->jadwal_kegiatan;
            $end = $survey->jadwal_berakhir_kegiatan;

            $tgl_ikut_survei = ($today >= $start && $today <= $end) ? $today : $start;

            // Jika tidak ada konflik, tambahkan mitra ke survei
            MitraSurvei::create([
                'id_mitra' => $id_mitra,
                'id_survei' => $id_survei,
                'vol' => $request->vol,
                'honor' => $request->honor,
                'posisi_mitra' => $request->posisi_mitra,
                'tgl_ikut_survei' => $tgl_ikut_survei
            ]);
        }

        return redirect()->back()->with('success', 'Mitra berhasil ditambahkan ke survei!');
    }



    public function upExcelMitra2Survey(Request $request, $id_survei)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls'
        ]);
    
        $import = new mitra2SurveyImport($id_survei);
    
        try {
            Excel::import($import, $request->file('file'));
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];
            
            foreach ($failures as $failure) {
                $errorMessages[] = "Baris {$failure->row()}: {$failure->errors()[0]}";
            }
            
            return redirect()->back()
                ->withErrors(['file' => $errorMessages]);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['file' => $e->getMessage()]);
        }
    
        // Tampilkan pesan sukses dengan info baris yang di-skip
        $message = 'Mitra berhasil diimport ke survei';
        
        if (count($import->failures()) > 0) {
            $message .= '. Beberapa data gagal: ' . count($import->failures()) . ' baris';
        }
        
        if (count($import->errors()) > 0) {
            $message .= '. Terdapat ' . count($import->errors()) . ' error';
        }
    
        return redirect()->back()->with('success', $message);
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
        $provinsi = Provinsi::where('id_provinsi', 35)->get();// Ambil semua data provinsi
        $kabupaten = Kabupaten::where('id_kabupaten', 16)->get(); // Ambil semua data kabupaten
        $kecamatan = Kecamatan::all(); // Ambil semua data kecamatan
        $desa = Desa::all(); // Ambil semua data desa


        return view('mitrabps.inputSurvei', compact('provinsi', 'kabupaten', 'kecamatan', 'desa'));
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

    public function getDesa($id_kecamatan)
    {
        $desa = Desa::where('id_kecamatan', $id_kecamatan)->get();
        return response()->json($desa);
    }

    // Method untuk menyimpan data survei
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'id_kecamatan' => 'required|exists:kecamatan,id_kecamatan',
            'id_desa' => 'required|exists:desa,id_desa',
            'nama_survei' => 'required|string|max:1024',
            'lokasi_survei' => 'required|string|max:1024',
            'kro' => 'required|string|max:1024',
            'jadwal_kegiatan' => 'required|date',
            'jadwal_berakhir_kegiatan' => 'required|date',
            'status_survei' => 'required|integer',
            'tim' => 'required|string|max:1024',
        ]);

        // Tambahkan nilai default
        $validated['id_provinsi'] = 35; // Jatim
        $validated['id_kabupaten'] = 16; // mojokerto

        // Simpan data ke database
        Survei::create($validated);

        return redirect()->back()->with('success', 'Survei berhasil ditambahkan!');
    }


    public function upExcelSurvei(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xls,xlsx|max:2048'
        ]);

        $import = new SurveiImport();
        
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

}