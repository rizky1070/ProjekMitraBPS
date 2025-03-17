<?php

namespace App\Http\Controllers;
use App\Models\Survei;
use App\Models\Mitra;
use App\Models\Kecamatan;
use App\Models\MitraSurvei;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SurveiImport;
use Exception; // Untuk menangani error
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DaftarSurveiBpsController extends Controller
{
    public function index(Request $request)
    {
        // Mendapatkan daftar tahun yang tersedia dari tabel survei
        $availableYears = Survei::selectRaw('YEAR(jadwal_kegiatan) as year')  // Sesuaikan nama kolom tanggal
            ->distinct()
            ->orderBy('year', 'desc')  // Mengurutkan tahun dari yang terbaru
            ->pluck('year');

         // Mendapatkan daftar kecamatan
        $kecamatans = Kecamatan::all(); // Mengambil semua kecamatan
        
        // Query untuk mengambil data survei
        $surveys = Survei::with('kecamatan')
                         ->withCount('mitraSurvei'); // Pastikan relasi dengan mitraSurvei sudah benar
    
        // Filter berdasarkan tahun jika ada parameter tahun
        if ($request->filled('tahun')) {
            $surveys->whereYear('jadwal_kegiatan', $request->tahun);  // Sesuaikan dengan kolom tanggal
        }
    
        // Filter berdasarkan kata kunci pencarian jika ada
        if ($request->filled('search')) {
            $surveys->where('nama_survei', 'like', '%' . $request->search . '%');
        }

        // Filter berdasarkan kecamatan jika ada parameter kecamatan
        if ($request->filled('kecamatan')) {
            $surveys->where('id_kecamatan', $request->kecamatan); 
        }
    
        // Menampilkan data survei dengan paginasi
        $surveys = $surveys->paginate(10); // Atur sesuai kebutuhan
    
        return view('mitrabps.daftarsurveibps', compact('surveys', 'availableYears', 'kecamatans'));
    }
    
    public function addSurvey($id_survei)
    {
        // Mengambil data survei berdasarkan id_survei
        $survey = Survei::with('kecamatan')
            ->where('id_survei', $id_survei)
            ->firstOrFail();

        // Mengambil semua mitra dan menghitung jumlah survei yang diikuti oleh setiap mitra
        $mitras = Mitra::withCount('mitraSurvei') // Menggunakan relasi mitraSurvei untuk menghitung survei yang diikuti
            ->get(); // Ambil semua mitra tanpa pagination

        // Menambahkan status apakah mitra sudah mengikuti survei atau tidak
        foreach ($mitras as $mitra) {
            // Periksa apakah mitra ini mengikuti survei yang dimaksud
            $mitra->isFollowingSurvey = $mitra->mitraSurvei->contains('id_survei', $id_survei);
        }

        // Urutkan mitra sehingga yang mengikuti survei berada di teratas
        $mitras = $mitras->sortByDesc(function($mitra) {
            return $mitra->isFollowingSurvey ? 1 : 0;
        });

        // Mengirimkan data survei dan mitra ke view
        return view('mitrabps.selectSurvey', compact('survey', 'mitras'));
    }


    public function toggleMitraSurvey($id_survei, $id_mitra)
    {
        $survey = Survei::findOrFail($id_survei);
        $mitra = Mitra::findOrFail($id_mitra);

        // Jika mitra sudah mengikuti survei, batalkan
        if ($mitra->mitraSurvei->contains('id_survei', $id_survei)) {
            $mitra->mitraSurvei()->detach($id_survei); // Menghapus relasi
        } else {
            $mitra->mitraSurvei()->attach($id_survei); // Menambahkan relasi
        }

        return redirect()->back();
    }


    public function import(Request$request) 
    {
            // Validasi file
        $request->validate([
            'filexls' => 'required|mimes:xlsx,xls',
        ],[
            'filexls.required' => 'File xls tidak boleh kosong',
        ]);

        // Cek apakah file ada
        $file = $request->file('filexls');
        if (!$file) {
            return redirect()->back()->with('error', 'File tidak ditemukan');
        }

        // Buat nama file baru dengan menambahkan timestamp
        $filename = time() . '_' . $file->getClientOriginalName();

        // Simpan file menggunakan Storage ke folder public/files
        Storage::disk('public')->put('files/' . $filename, file_get_contents($file));

        // Log nama file dan path untuk pengecekan
        Log::info('File berhasil di-upload: ' . $filename);
        Log::info('Path file: ' . storage_path("app/public/files/{$filename}"));

        // Import file menggunakan Excel
        try {
            Excel::import(new SurveiImport, storage_path("app/public/files/{$filename}"));
        } catch (\Exception $e) {
            Log::error('Error saat import: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengimpor file');
        }

        return redirect('/survei')->with('success', 'Import Sukses');

        //     // Validasi file
        // $request->validate([
        //     'filexls' => 'required|mimes:xlsx,xls',
        // ],[
        //     'filexls.required' => 'File xls tidak boleh kosong',
        // ]);

        // // Cek apakah file ada
        // $file = $request->file('filexls');
        // if (!$file) {
        //     return redirect()->back()->with('error', 'File tidak ditemukan');
        // }

        // // Log nama file dan path untuk pengecekan
        // $filename = $file->getClientOriginalName();
        // $path = $file->storeAs('files', $filename, 'public');
        // Log::info('File berhasil di-upload: ' . $filename);
        // Log::info('Path file: ' . storage_path("app/public/files/{$filename}"));

        // // Import file
        // try {
        //     Excel::import(new SurveiImport, storage_path("app/public/files/{$filename}"));
        // } catch (\Exception $e) {
        //     Log::error('Error saat import: ' . $e->getMessage());
        //     return redirect()->back()->with('error', 'Gagal mengimpor file');
        // }

        // return redirect('/survei')->with('success', 'Import Sukses');


        // {
        //     $request->validate([
        //         'filexls' => 'required|mimes:xlsx,csv,xls'
        //     ]);
    
        //     try {
        //         Excel::import(new SurveiImport, $request->file('filexls'));
        //         return redirect()->route('mitrabps.daftarsurveibps')->with('success', 'Jadwal berhasil diimport.');
        //     } catch (Exception $e) {
        //         return redirect()->route('mitrabps.daftarsurveibps')->with('error-excel', $e->getMessage());
        //     }
        // }



        // $request->validate([
        //     'filexls' => 'required|mimes:xlsx,xls',
        // ],[
        //     'filexls.required' => 'File xls tidak boleh kosong',
        // ]);

        // $files = $request->file('filexls');
        // if (!$files) {
        //     return back()->with('error', 'File tidak ditemukan!');
        // }
        // $filename = $files->getClientOriginalName();

        // // Simpan file di folder storage yang sesuai
        // $path = $files->storeAs('files', $filename, 'public');

        // // Import file menggunakan path yang benar
        // Excel::import(new SurveiImport, storage_path("app/public/files/{$filename}"));

        
        // return redirect('/survei')->with('success', 'Import Sukses');
    }


    // public function import(Request $request)
    // {
    //     // // Validasi file Excel
    //     // $request->validate([
    //     //     'excel_file' => 'required|file|mimes:xlsx,xls'
    //     // ]);

    //     // dd($request->file('excel_file')); // Ini akan menunjukkan apakah file berhasil diterima
    //     // dd($request->all());

    //     // // Ambil file yang diunggah
    //     // $file = $request->file('excel_file');

    //     // try {
    //     //     // Proses file menggunakan Laravel Excel
    //     //     Excel::import(new SurveiImport, $file);

    //     //     // Jika berhasil
    //     //     return redirect()->route('surveys.filter')->with('success', 'Data berhasil diimpor!');
    //     // } catch (Exception $e) {
    //     //     // Jika gagal, tampilkan pesan error
    //     //     return redirect()->route('surveys.filter')->with('error', 'Gagal mengimpor data. Periksa format file atau data yang tidak valid.');
    //     // }
    // }
}

//         return view('mitrabps.daftarsurveibps', compact('surveys')); // Memanggil view mitrabps.blade.php
//     }

//     public function addSurvey($id_survei)
//     {
//         // Mengambil data survei berdasarkan id_survei
//         $survey = Survei::with('kecamatan')
//             ->where('id_survei', $id_survei)
//             ->firstOrFail();

//         // Mengambil semua mitra dan menghitung jumlah relasi MitraSurvei
//         $mitras = Mitra::paginate(10); // Bisa juga ditambahkan ->get() jika tanpa pagination

//         return view('mitrabps.selectSurvey', compact('survey', 'mitras'));
//     }

// }

