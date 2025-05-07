<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpWord\TemplateProcessor;
use App\Models\MitraSurvei;
use App\Models\Survei;
use App\Models\Mitra;
use File;
use Response;
use ZipArchive;

class SKMitraController extends Controller
{
    public function showUploadForm($id_survei)
    {
        // Ambil data survei dengan relasi yang benar
        $survei = Survei::with(['mitraSurvei.mitra' => function($query) {
            $query->with('kecamatan');
        }])->findOrFail($id_survei);
    
        // Pastikan data mitra ada
        if ($survei->mitraSurvei->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada mitra untuk survei ini');
        }
    
        return view('mitrabps.editSk', compact('survei'));
    }
    
    public function handleUpload(Request $request)
    {
        // Validasi file yang diunggah
        $request->validate([
            'file' => 'required|mimes:docx|max:10240',
            'nomor_sk' => 'required|string|max:255',
            'nama' => 'required|string|max:255',
            'denda' => 'required|numeric',
        ]);
    
        // Ambil id_survei dari URL
        $surveyId = $request->id_survei;
    
        // Ambil data survei dan semua mitra terkait
        $survey = Survei::with(['mitraSurvei.mitra.kecamatan'])->findOrFail($surveyId);
        
        // Ambil data input dari form
        $nomorSk = $request->input('nomor_sk');
        $nama = $request->input('nama');
        $denda = $request->input('denda');
    
        // Tanggal hari ini
        $hariIni = now()->locale('id')->translatedFormat('l');
        $tanggalHariIni = now()->locale('id')->translatedFormat('d');
        $bulanHariIni = now()->locale('id')->translatedFormat('F');
        $tahunHariIni = now()->locale('id')->translatedFormat('Y');
        
        // Jadwal kegiatan
        $jadwalKegiatan = \Carbon\Carbon::parse($survey->jadwal_kegiatan)->locale('id')->translatedFormat('d-F-Y');
        $jadwalBerakhirKegiatan = \Carbon\Carbon::parse($survey->jadwal_berakhir_kegiatan)->locale('id')->translatedFormat('d-F-Y');
    
        // Simpan file yang diunggah ke direktori sementara
        $filePath = $request->file('file')->storeAs('temp', 'uploaded_template.docx');
        $filePath = storage_path('app/' . $filePath);
        
        // Buat direktori untuk output
        $outputDir = storage_path('app/temp/sk_output');
        if (!File::exists($outputDir)) {
            File::makeDirectory($outputDir, 0755, true);
        }
        
        // Buat ZIP archive
        $zip = new ZipArchive();
        $zipFileName = 'SK_Mitra_' . $survey->nama_survei . '.zip';
        $zipFilePath = storage_path('app/temp/' . $zipFileName);
        
        if ($zip->open($zipFilePath, ZipArchive::CREATE) === TRUE) {
            foreach ($survey->mitraSurvei as $mitraSurvei) {
                $mitra = $mitraSurvei->mitra;
                
                // Hitung total honor
                $vol = $mitraSurvei->vol;
                $honor = $mitraSurvei->honor;
                $totalHonor = $vol * $honor;
                
                // Konversi total honor dan denda ke teks
                $denda_teks = $this->angkaToTeks($denda);
                $total_honor_teks = $this->angkaToTeks($totalHonor);
                
                // Load template untuk setiap mitra
                $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($filePath);
                
                // Ganti placeholder dalam template dengan data yang diambil
                $templateProcessor->setValue('{{nomor_sk}}', $nomorSk);
                $templateProcessor->setValue('{{nama}}', $nama);
                $templateProcessor->setValue('{{denda}}', $denda);
                $templateProcessor->setValue('{{denda_teks}}', $denda_teks);
                $templateProcessor->setValue('{{nama_lengkap}}', $mitra->nama_lengkap);
                $templateProcessor->setValue('{{nama_kecamatan}}', $mitra->kecamatan->nama_kecamatan);
                $templateProcessor->setValue('{{jadwal_kegiatan}}', $jadwalKegiatan);
                $templateProcessor->setValue('{{jadwal_berakhir_kegiatan}}', $jadwalBerakhirKegiatan);
                $templateProcessor->setValue('{{vol}}', $vol);
                $templateProcessor->setValue('{{honor}}', $honor);
                $templateProcessor->setValue('{{total_honor}}', $totalHonor);
                $templateProcessor->setValue('{{total_honor_teks}}', $total_honor_teks);
                $templateProcessor->setValue('{{hari}}', $hariIni);
                $templateProcessor->setValue('{{tanggal}}', $tanggalHariIni);
                $templateProcessor->setValue('{{bulan}}', $bulanHariIni);
                $templateProcessor->setValue('{{tahun}}', $tahunHariIni);
                $templateProcessor->setValue('{{posisi_mitra}}', $mitraSurvei->posisi_mitra);
                
                // Simpan file untuk mitra ini
                $outputFile = $outputDir . '/SK_' . $mitra->nama_lengkap . '_' . $survey->nama_survei . '.docx';
                $templateProcessor->saveAs($outputFile);
                
                // Tambahkan ke ZIP
                $zip->addFile($outputFile, 'SK_' . $survei->nama_survei . '.docx');
            }
            
            $zip->close();
            
            // Hapus file individual
            File::deleteDirectory($outputDir);
            
            // Kembalikan file ZIP untuk diunduh
            return response()->download($zipFilePath)->deleteFileAfterSend(true);
        }
        
        return back()->with('error', 'Gagal membuat file ZIP');
    }
    
        /**
     * Fungsi untuk mengkonversi angka ke teks dalam bahasa Indonesia
     */
    private function angkaToTeks($angka) {
        $angka = (float)$angka;
        if ($angka < 0) return "minus " . $this->angkaToTeks(abs($angka));
        
        $satuan = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan'];
        $belasan = ['Sepuluh', 'Sebelas', 'Dua Belas', 'Tiga Belas', 'Empat Belas', 'Lima Belas', 'Enam Belas', 'Tujuh Belas', 'Delapan Belas', 'Sembilan Belas'];
        $puluhan = ['', 'Sepuluh', 'Dua Puluh', 'Tiga Puluh', 'Empat Puluh', 'Lima Puluh', 'Enam Puluh', 'Tujuh Puluh', 'Delapan Puluh', 'Sembilan Puluh'];
        
        if ($angka < 10) {
            return $satuan[$angka];
        } elseif ($angka < 20) {
            return $belasan[$angka - 10];
        } elseif ($angka < 100) {
            $hasil = $puluhan[floor($angka / 10)];
            if ($angka % 10 > 0) {
                $hasil .= ' ' . $satuan[$angka % 10];
            }
            return $hasil;
        } elseif ($angka < 1000) {
            if (floor($angka / 100) == 1) {
                $hasil = 'Seratus';
            } else {
                $hasil = $satuan[floor($angka / 100)] . ' Ratus';
            }
            if ($angka % 100 > 0) {
                $hasil .= ' ' . $this->angkaToTeks($angka % 100);
            }
            return $hasil;
        } elseif ($angka < 1000000) {
            if (floor($angka / 1000) == 1) {
                $hasil = 'Seribu';
            } else {
                $hasil = $this->angkaToTeks(floor($angka / 1000)) . ' Ribu';
            }
            if ($angka % 1000 > 0) {
                $hasil .= ' ' . $this->angkaToTeks($angka % 1000);
            }
            return $hasil;
        } elseif ($angka < 1000000000) {
            $hasil = $this->angkaToTeks(floor($angka / 1000000)) . ' Juta';
            if ($angka % 1000000 > 0) {
                $hasil .= ' ' . $this->angkaToTeks($angka % 1000000);
            }
            return $hasil;
        } else {
            return 'angka terlalu besar';
        }
    }
    // ... (keep the existing angkaToTeks and downloadFile methods)
}