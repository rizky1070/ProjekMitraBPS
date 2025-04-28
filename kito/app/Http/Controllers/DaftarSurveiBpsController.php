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
                        $q->whereYear('bulan_dominan', $request->tahun);
                    }
                    if ($request->filled('bulan')) {
                        $q->whereMonth('bulan_dominan', $request->bulan);
                    }
                });
            })
            ->orderBy('nama_kecamatan')
            ->get(['nama_kecamatan', 'kode_kecamatan', 'id_kecamatan']);

        // Daftar nama survei berdasarkan filter
        $namaSurveiOptions = Survei::select('nama_survei')
            ->distinct()
            ->when($request->filled('tahun'), function($query) use ($request) {
                $query->whereYear('bulan_dominan', $request->tahun);
            })
            ->when($request->filled('bulan'), function($query) use ($request) {
                $query->whereMonth('bulan_dominan', $request->bulan);
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
                $query->whereYear('bulan_dominan', $request->tahun);
            })
            ->when($request->filled('bulan'), function ($query) use ($request) {
                $query->whereMonth('bulan_dominan', $request->bulan);
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

        // Daftar tahun yang tersedia (filter berdasarkan tanggal survei)
        $tahunOptions = Mitra::whereDate('tahun', '<=', $survey->jadwal_kegiatan)
            ->whereDate('tahun_selesai', '>=', $survey->jadwal_berakhir_kegiatan)
            ->selectRaw('DISTINCT YEAR(tahun) as tahun')
            ->orderByDesc('tahun')
            ->pluck('tahun', 'tahun');

        // Daftar bulan berdasarkan tahun yang dipilih (filter berdasarkan tanggal survei)
        $bulanOptions = [];
        if ($request->filled('tahun')) {
            $bulanOptions = Mitra::whereDate('tahun', '<=', $survey->jadwal_kegiatan)
                ->whereDate('tahun_selesai', '>=', $survey->jadwal_berakhir_kegiatan)
                ->whereYear('tahun', $request->tahun)
                ->selectRaw('DISTINCT MONTH(tahun) as bulan')
                ->orderBy('bulan')
                ->pluck('bulan', 'bulan')
                ->mapWithKeys(function($month) {
                    $monthNumber = str_pad($month, 2, '0', STR_PAD_LEFT);
                    return [
                        $monthNumber => \Carbon\Carbon::create()->month($month)->translatedFormat('F')
                    ];
                });
        }

        // Daftar kecamatan berdasarkan tahun dan bulan yang dipilih (filter berdasarkan tanggal survei)
        $kecamatanOptions = Kecamatan::query()
            ->when($request->filled('tahun') || $request->filled('bulan'), function($query) use ($request, $survey) {
                $query->whereHas('mitras', function($q) use ($request, $survey) {
                    $q->whereDate('tahun', '<=', $survey->jadwal_kegiatan)
                    ->whereDate('tahun_selesai', '>=', $survey->jadwal_berakhir_kegiatan);
                    
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

        // Daftar nama mitra (nama lengkap) (filter berdasarkan tanggal survei)
        $namaMitraOptions = Mitra::whereDate('tahun', '<=', $survey->jadwal_kegiatan)
            ->whereDate('tahun_selesai', '>=', $survey->jadwal_berakhir_kegiatan)
            ->select('nama_lengkap')
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

        // Query daftar mitra (sudah ada filter tanggal survei)
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
        ->whereDate('mitra.tahun', '<=', $survey->jadwal_kegiatan)
        ->whereDate('mitra.tahun_selesai', '>=', $survey->jadwal_berakhir_kegiatan)
        ->when($request->filled('tahun'), function($query) use ($request) {
            $query->whereYear('mitra.tahun', $request->tahun);
        })
        ->when($request->filled('bulan'), function($query) use ($request) {
            $query->whereMonth('mitra.tahun', $request->bulan);
        })
        ->when($request->filled('kecamatan'), function($query) use ($request) {
            $query->where('mitra.id_kecamatan', $request->kecamatan);
        })
        ->when($request->filled('nama_lengkap'), function($query) use ($request) {
            $query->where('mitra.nama_lengkap', $request->nama_lengkap);
        })
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
    
        // Cek apakah survei sudah lewat dari jadwal berakhir
        $today = now()->toDateString();
        if ($today > $survey->jadwal_berakhir_kegiatan) {
            return redirect()->back()
                ->with('error', "Tidak bisa menambahkan mitra karena survei sudah berakhir pada {$survey->jadwal_berakhir_kegiatan}")
                ->withInput();
        }
    
        // Hitung total honor mitra di bulan yang sama dengan survei ini (SEBELUM penambahan)
        $totalHonorBulanIni = MitraSurvei::join('survei', 'mitra_survei.id_survei', '=', 'survei.id_survei')
            ->where('mitra_survei.id_mitra', $id_mitra)
            ->where('survei.bulan_dominan', $survey->bulan_dominan)
            ->sum(DB::raw('mitra_survei.honor * mitra_survei.vol'));
    
        // Cek apakah total honor SEBELUM penambahan sudah mencapai batas
        if ($totalHonorBulanIni >= 4000000) {
            return redirect()->back()
                ->with('error', "Mitra tidak bisa ditambahkan karena total honor di bulan " . 
                \Carbon\Carbon::parse($survey->bulan_dominan)->locale('id')->translatedFormat('F Y') . 
                " sudah mencapai Rp 4.000.000")
                ->withInput();
        }
    
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
            // Tentukan tgl_ikut_survei berdasarkan kondisi tanggal hari ini
            $start = $survey->jadwal_kegiatan;
            $end = $survey->jadwal_berakhir_kegiatan;
    
            $tgl_ikut_survei = ($today >= $start && $today <= $end) ? $today : $start;
    
            // Jika belum ada, tambahkan mitra ke survei
            $mitraSurvei = MitraSurvei::create([
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
        // Validasi input (hapus 'bulan_dominan' dan 'status_survei')
        $validated = $request->validate([
            'id_kecamatan' => 'required|exists:kecamatan,id_kecamatan',
            'id_desa' => 'required|exists:desa,id_desa',
            'nama_survei' => 'required|string|max:1024',
            'lokasi_survei' => 'required|string|max:1024',
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