<?php
namespace App\Http\Controllers;

// app/Http/Controllers/DocumentController.php

use Illuminate\Http\Request;
use PhpOffice\PhpWord\TemplateProcessor;
use App\Models\MitraSurvei; // Pastikan Anda menggunakan model yang sesuai  
use App\Models\Survei;
use App\Models\Mitra;
use File;
use Response;

class SKMitraController extends Controller
{
    public function showUploadForm($id_survei, $id_mitra)
    {
        // Fetch the mitra and survei data from the database based on the provided IDs
        $mitra = Mitra::find($id_mitra); // Replace Mitra with your model name
        $survei = Survei::find($id_survei); // Replace Survei with your model name
    
        // Ensure the mitra and survei are found, otherwise return an error
        if (!$mitra || !$survei) {
            return redirect()->route('someErrorRoute'); // Redirect to an error page if not found
        }
    
        // Pass the mitra and survei to the view
        return view('mitrabps.editSk', compact('mitra', 'survei'));
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
    
        // Ambil id_mitra dan id_survei dari URL
        $surveyId = $request->id_survei;
        $mitraId = $request->id_mitra;
    
        // Ambil data survei, mitra, dan mitra_survei dari database
        $survey = Survei::findOrFail($surveyId);
        $mitra = Mitra::findOrFail($mitraId);
        $mitraSurvei = MitraSurvei::where('id_survei', $surveyId)->where('id_mitra', $mitraId)->firstOrFail();
    
        // Ambil data input dari form
        $nomorSk = $request->input('nomor_sk');
        $nama = $request->input('nama');
        $denda = $request->input('denda');
    
        // Ambil data lainnya dari database
        $namaLengkapMitra = $mitra->nama_lengkap;
        $namaKecamatan = $mitra->kecamatan->nama_kecamatan;
        $jadwalKegiatan = \Carbon\Carbon::parse($survey->jadwal_kegiatan)->locale('id')->translatedFormat('d-F-Y');
        $jadwalBerakhirKegiatan = \Carbon\Carbon::parse($survey->jadwal_berakhir_kegiatan)->locale('id')->translatedFormat('d-F-Y');
        $vol = $mitraSurvei->vol;
        $honor = $mitraSurvei->honor;
        $totalHonor = $vol * $honor;
        
        // Konversi total honor dan denda ke teks
        $denda_teks = $this->angkaToTeks($denda);
        $total_honor_teks = $this->angkaToTeks($totalHonor);
    
        // Tanggal hari ini
        $hariIni = now()->locale('id')->translatedFormat('l');
        $tanggalHariIni = now()->locale('id')->translatedFormat('d');
        $bulanHariIni = now()->locale('id')->translatedFormat('F');
        $tahunHariIni = now()->locale('id')->translatedFormat('Y');
    
        // Simpan file yang diunggah ke direktori sementara
        $filePath = $request->file('file')->storeAs('temp', 'uploaded_template.docx');
        $filePath = storage_path('app/' . $filePath);
    
        // Load template menggunakan PHPWord
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($filePath);
    
        // Ganti placeholder dalam template dengan data yang diambil
        $templateProcessor->setValue('{{nomor_sk}}', $nomorSk);
        $templateProcessor->setValue('{{nama}}', $nama);
        $templateProcessor->setValue('{{denda}}', $denda);
        $templateProcessor->setValue('{{denda_teks}}', $denda_teks);
        $templateProcessor->setValue('{{nama_lengkap}}', $namaLengkapMitra);
        $templateProcessor->setValue('{{nama_kecamatan}}', $namaKecamatan);
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
    
        // Simpan file yang sudah diubah
        $outputFile = storage_path('app/temp/edited_template.docx');
        $templateProcessor->saveAs($outputFile);
    
        // Kembalikan file yang sudah diubah untuk diunduh
        return response()->download($outputFile);
    }
    
    /**
     * Fungsi untuk mengkonversi angka ke teks dalam bahasa Indonesia
     */
    private function angkaToTeks($angka) {
        $angka = (float)$angka;
        if ($angka < 0) return "minus " . $this->angkaToTeks(abs($angka));
        
        $satuan = ['', 'satu', 'dua', 'tiga', 'empat', 'lima', 'enam', 'tujuh', 'delapan', 'sembilan'];
        $belasan = ['sepuluh', 'sebelas', 'dua belas', 'tiga belas', 'empat belas', 'lima belas', 'enam belas', 'tujuh belas', 'delapan belas', 'sembilan belas'];
        $puluhan = ['', 'sepuluh', 'dua puluh', 'tiga puluh', 'empat puluh', 'lima puluh', 'enam puluh', 'tujuh puluh', 'delapan puluh', 'sembilan puluh'];
        
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
                $hasil = 'seratus';
            } else {
                $hasil = $satuan[floor($angka / 100)] . ' ratus';
            }
            if ($angka % 100 > 0) {
                $hasil .= ' ' . $this->angkaToTeks($angka % 100);
            }
            return $hasil;
        } elseif ($angka < 1000000) {
            if (floor($angka / 1000) == 1) {
                $hasil = 'seribu';
            } else {
                $hasil = $this->angkaToTeks(floor($angka / 1000)) . ' ribu';
            }
            if ($angka % 1000 > 0) {
                $hasil .= ' ' . $this->angkaToTeks($angka % 1000);
            }
            return $hasil;
        } elseif ($angka < 1000000000) {
            $hasil = $this->angkaToTeks(floor($angka / 1000000)) . ' juta';
            if ($angka % 1000000 > 0) {
                $hasil .= ' ' . $this->angkaToTeks($angka % 1000000);
            }
            return $hasil;
        } else {
            return 'angka terlalu besar';
        }
    }
    
    
    
    

    public function downloadFile($filename)
    {
        $file = storage_path("app/temp/$filename");
        if (File::exists($file)) {
            return response()->download($file);
        }

        return redirect()->back()->with('error', 'File not found!');
    }
}
