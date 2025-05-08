<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpWord\TemplateProcessor;
use App\Models\MitraSurvei;
use App\Models\Survei;
use App\Models\Mitra;
use Illuminate\Support\Facades\File;
use Response;
use ZipArchive;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
    // Validasi input
    $request->validate([
        'file' => 'required|mimes:docx|max:10240',
        'nomor_sk' => 'required|string|max:255',
        'nama' => 'required|string|max:255',
        'denda' => 'required|numeric',
        'id_survei' => 'required|exists:survei,id_survei',
    ]);

    try {
        // Ambil data survei dengan relasi
        $survey = Survei::with(['mitraSurvei.mitra.kecamatan'])
                    ->findOrFail($request->id_survei);

        // Validasi jika tidak ada mitra
        if ($survey->mitraSurvei->isEmpty()) {
            throw new \Exception('Tidak ada mitra terkait dengan survei ini');
        }

        // Simpan file template sementara
        $templatePath = $request->file('file')->storeAs(
            'temp/templates', 
            'template_' . now()->timestamp . '.docx'
        );
        $fullTemplatePath = storage_path('app/' . $templatePath);

        // Siapkan direktori output
        $outputDir = storage_path('app/temp/sk_documents_' . now()->timestamp);
        if (!File::exists($outputDir)) {
            File::makeDirectory($outputDir, 0755, true);
        }

        // Format tanggal
        $hariIni = now()->locale('id')->translatedFormat('l');
        $tanggalHariIni = now()->locale('id')->translatedFormat('d');
        $bulanHariIni = now()->locale('id')->translatedFormat('F');
        $tahunHariIni = now()->locale('id')->translatedFormat('Y');
        $jadwalKegiatan = \Carbon\Carbon::parse($survey->jadwal_kegiatan)
                            ->locale('id')->translatedFormat('d-F-Y');
        $jadwalBerakhirKegiatan = \Carbon\Carbon::parse($survey->jadwal_berakhir_kegiatan)
                                    ->locale('id')->translatedFormat('d-F-Y');

        // Proses setiap mitra
        $zip = new ZipArchive();
        $zipFileName = 'SK_Mitra_' . Str::slug($survey->nama_survei) . '_' . now()->timestamp . '.zip';
        $zipFilePath = storage_path('app/public/' . $zipFileName);

        // Buka file ZIP dengan mode CREATE dan OVERWRITE
        if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
            throw new \Exception("Gagal membuat file ZIP");
        }

        $processedCount = 0;
        foreach ($survey->mitraSurvei as $mitraSurvei) {
            try {
                if (!$mitraSurvei->mitra) {
                    Log::warning('Mitra tidak ditemukan untuk MitraSurvei ID: '.$mitraSurvei->id);
                    continue;
                }

                $mitra = $mitraSurvei->mitra;
                
                // Hitung total honor
                $vol = $mitraSurvei->vol ?? 0;
                $honor = $mitraSurvei->honor ?? 0;
                $totalHonor = $vol * $honor;
                
                // Konversi ke teks
                $denda_teks = $this->angkaToTeks($request->denda);
                $total_honor_teks = $this->angkaToTeks($totalHonor);
                
                // Proses template
                $templateProcessor = new TemplateProcessor($fullTemplatePath);
                
                // Set nilai placeholder
                $templateProcessor->setValues([
                    '{{nomor_sk}}' => $request->nomor_sk,
                    '{{nama}}' => $request->nama,
                    '{{denda}}' => number_format($request->denda, 0, ',', '.'),
                    '{{denda_teks}}' => $denda_teks,
                    '{{nama_lengkap}}' => $mitra->nama_lengkap ?? 'Nama Tidak Tersedia',
                    '{{nama_kecamatan}}' => optional($mitra->kecamatan)->nama_kecamatan ?? 'Kecamatan Tidak Tersedia',
                    '{{jadwal_kegiatan}}' => $jadwalKegiatan, 
                    '{{jadwal_berakhir_kegiatan}}' => $jadwalBerakhirKegiatan,
                    '{{vol}}' => $vol,
                    '{{honor}}' => number_format($honor, 0, ',', '.'),
                    '{{total_honor}}' => number_format($totalHonor, 0, ',', '.'),
                    '{{total_honor_teks}}' => $total_honor_teks,
                    '{{hari}}' => $hariIni,
                    '{{tanggal}}' => $tanggalHariIni,
                    '{{bulan}}' => $bulanHariIni,
                    '{{tahun}}' => $tahunHariIni,
                    '{{posisi_mitra}}' => $mitraSurvei->posisi_mitra ?? 'Posisi Tidak Tersedia',
                ]);

                // Simpan dokumen
                $outputFileName = 'SK_' . Str::slug($mitra->nama_lengkap) . '.docx';
                $outputFilePath = $outputDir . '/' . $outputFileName;
                $templateProcessor->saveAs($outputFilePath);
                
                // Tambahkan ke ZIP
                if (!$zip->addFile($outputFilePath, $outputFileName)) {
                    Log::error("Gagal menambahkan file ke ZIP: " . $outputFileName);
                    continue;
                }
                
                $processedCount++;
                
            } catch (\Exception $e) {
                Log::error('Error memproses mitra ID '.$mitraSurvei->id.': '.$e->getMessage());
                continue;
            }
        }

        // Tutup ZIP
        if (!$zip->close()) {
            throw new \Exception("Gagal menutup file ZIP");
        }

        // Hapus file temporary
        File::deleteDirectory($outputDir);
        unlink($fullTemplatePath);

        // Validasi jika tidak ada file yang diproses
        if ($processedCount === 0) {
            unlink($zipFilePath);
            throw new \Exception('Tidak ada dokumen yang berhasil diproses');
        }

        // Download file ZIP
        return response()->download($zipFilePath, $zipFileName, [
            'Content-Type' => 'application/zip',
            'Content-Disposition' => 'attachment; filename="' . $zipFileName . '"',
        ])->deleteFileAfterSend(true);

    } catch (\Exception $e) {
        // Cleanup jika ada error
        if (isset($outputDir) && File::exists($outputDir)) {
            File::deleteDirectory($outputDir);
        }
        if (isset($fullTemplatePath) && file_exists($fullTemplatePath)) {
            unlink($fullTemplatePath);
        }
        if (isset($zipFilePath) && file_exists($zipFilePath)) {
            unlink($zipFilePath);
        }

        Log::error('Error dalam handleUpload: ' . $e->getMessage());
        return back()
            ->withInput()
            ->with('error', 'Gagal membuat dokumen SK: ' . $e->getMessage());
    }
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

}
