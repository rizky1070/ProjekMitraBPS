<?php

namespace App\Imports;

use Illuminate\Support\Facades\Log;
use App\Models\Mitra;
use App\Models\MitraSurvei;
use App\Models\Survei;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Throwable;

class Mitra2SurveyImport implements ToModel, WithHeadingRow, WithValidation
{
    use SkipsErrors, SkipsFailures;

    protected $id_survei;
    protected $survei;

    public function __construct($id_survei)
    {
        $this->id_survei = $id_survei;
        $this->survei = Survei::find($id_survei);
    }

    public function model(array $row)
    {
        $tahunMasuk = $this->parseDate($row['tgl_mitra_diterima']);
        $tglIkutSurvei = $this->parseDate($row['tgl_ikut_survei']);

        // Konversi nilai numerik dari berbagai format
        $sobatId = $this->convertToNumeric($row['sobat_id']);
        $vol = $this->convertToNumeric($row['vol']);
        $honor = $this->convertToNumeric($row['rate_honor']);
        $nilai = isset($row['nilai']) ? $this->convertToNumeric($row['nilai']) : null;

        // Cari mitra berdasarkan sobat_id bulan dan tahun
        $mitra = Mitra::where('sobat_id', $sobatId)
                    ->whereMonth('tahun', Carbon::parse($tahunMasuk)->month)
                    ->whereYear('tahun', Carbon::parse($tahunMasuk)->year)
                    ->first();

        if (!$mitra) {
            throw new \Exception("Mitra dengan SOBAT ID {$sobatId} pada bulan " . Carbon::parse($tahunMasuk)->month . " dan tahun masuk " . Carbon::parse($tahunMasuk)->year . " tidak ditemukan");
        }

        // Cek status pekerjaan mitra
        if ($mitra->status_pekerjaan == 1) {
            throw new \Exception("Mitra dengan SOBAT ID {$sobatId} tidak dapat ditambahkan karena status pekerjaan bernilai 1");
        }

        // Pengecekan periode aktif mitra dengan periode survei
        $jadwalMulaiSurvei = Carbon::parse($this->survei->jadwal_kegiatan);
        $jadwalBerakhirSurvei = Carbon::parse($this->survei->jadwal_berakhir_kegiatan);
        $tahunMasukMitra = Carbon::parse($tahunMasuk);
        $tahunBerakhirMitra = Carbon::parse($mitra->tahun_selesai);

        // Cek apakah periode aktif mitra overlap dengan periode survei
        if ($tahunBerakhirMitra < $jadwalMulaiSurvei || $tahunMasukMitra > $jadwalBerakhirSurvei) {
            throw new \Exception("Mitra dengan SOBAT ID {$sobatId} tidak aktif pada periode survei ({$jadwalMulaiSurvei->format('d-m-Y')} sampai {$jadwalBerakhirSurvei->format('d-m-Y')})");
        }

        // Cek apakah tgl ikut survei berada dalam periode survei
        $tglIkut = Carbon::parse($tglIkutSurvei);
        if ($tglIkut > $jadwalBerakhirSurvei) {
            throw new \Exception("Tanggal ikut survei {$tglIkut->format('d-m-Y')} melebihi jadwal berakhir survei : {$jadwalBerakhirSurvei->format('d-m-Y')})");
        }


        // Cek apakah kombinasi id_mitra dan id_survei sudah ada
        $existingMitra = MitraSurvei::where('id_mitra', $mitra->id_mitra)
                                ->where('id_survei', $this->id_survei)
                                ->first();

        if ($existingMitra) {
            // Jika sudah ada, lakukan update
            $existingMitra->update([
                'posisi_mitra' => $row['posisi'],
                'vol' => $vol,
                'honor' => $honor,
                'catatan' => $row['catatan'],
                'nilai' => $nilai,
                'tgl_ikut_survei' => $tglIkutSurvei,
            ]);
            return null;
        }

        return new MitraSurvei([
            'id_mitra' => $mitra->id_mitra,
            'id_survei' => $this->id_survei,
            'posisi_mitra' => $row['posisi'],
            'vol' => $vol,
            'honor' => $honor,
            'catatan' => $row['catatan'],
            'nilai' => $nilai,
            'tgl_ikut_survei' => $tglIkutSurvei,
        ]);
    }

    public function rules(): array
    {
        return [
            'sobat_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    // Cek apakah nilai bisa dikonversi ke numerik
                    if (!is_numeric($this->convertToNumeric($value))) {
                        $fail("SOBAT ID harus berupa angka");
                    }
                    
                    // Cek apakah mitra ada di database
                    $sobatId = $this->convertToNumeric($value);
                    if (!Mitra::where('sobat_id', $sobatId)->exists()) {
                        $fail("Mitra dengan SOBAT ID {$sobatId} tidak ditemukan");
                    }
                }
            ],
            'posisi' => 'required|string',
            'vol' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!is_numeric($this->convertToNumeric($value))) {
                        $fail("Volume harus berupa angka");
                    }
                }
            ],
            'rate_honor' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!is_numeric($this->convertToNumeric($value))) {
                        $fail("Honor harus berupa angka");
                    }
                }
            ],
            'catatan' => 'nullable|string',
            'nilai' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if ($value !== null && !is_numeric($this->convertToNumeric($value))) {
                        $fail("Nilai harus berupa angka");
                    }
                },
                function ($attribute, $value, $fail) {
                    $nilai = $this->convertToNumeric($value);
                    if ($nilai !== null && ($nilai < 1 || $nilai > 5)) {
                        $fail("Nilai harus antara 1 dan 5");
                    }
                }
            ],
            'tgl_mitra_diterima' => 'required',
            'tgl_ikut_survei' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!$this->survei) {
                        $fail("Data survei tidak ditemukan");
                        return;
                    }
                    
                    try {
                        $tglIkut = Carbon::parse($this->parseDate($value));
                        $jadwalMulai = Carbon::parse($this->survei->jadwal_kegiatan);
                        $jadwalBerakhir = Carbon::parse($this->survei->jadwal_berakhir_kegiatan);
                        
                        if ($tglIkut > $jadwalBerakhir) {
                            $fail("Tanggal ikut survei tidak boleh melebihi {$jadwalBerakhir->format('d-m-Y')}");
                        }
                    } catch (\Exception $e) {
                        $fail("Format tanggal tidak valid: {$value}");
                    }
                }
            ],
        ];
    }

    /**
     * Konversi berbagai format input ke numerik
     */
    private function convertToNumeric($value)
    {
        if (is_null($value)) {
            return null;
        }

        if (is_numeric($value)) {
            return $value;
        }

        // Handle string dengan karakter non-numerik (seperti koma, titik, dll)
        $cleaned = preg_replace('/[^0-9,.-]/', '', $value);
        $cleaned = str_replace(',', '.', $cleaned); // Ganti koma dengan titik untuk format desimal

        // Jika setelah pembersihan masih berupa angka
        if (is_numeric($cleaned)) {
            return $cleaned;
        }

        return $value; // Kembalikan aslinya jika tidak bisa dikonversi
    }

    private function parseDate($date)
    {
        try {
            if (empty($date)) {
                return null;
            }

            if ($date instanceof \DateTimeInterface) {
                return Carbon::instance($date);
            }

            if (is_numeric($date)) {
                $unixDate = ($date - 25569) * 86400;
                return Carbon::createFromTimestamp($unixDate);
            }

            if (is_string($date)) {
                if (preg_match('/^\d+$/', $date)) {
                    $unixDate = ($date - 25569) * 86400;
                    return Carbon::createFromTimestamp($unixDate);
                }
                
                return Carbon::parse($date);
            }

            throw new \Exception("Format tanggal tidak dikenali");
            
        } catch (\Exception $e) {
            Log::error("Gagal parsing tanggal: {$date} - Error: " . $e->getMessage());
            throw new \Exception("Format tanggal tidak valid: {$date}");
        }
    }

    public function onError(Throwable $e)
    {
        $this->errors[] = $e->getMessage();
    }
    
    public function getErrors()
    {
        return $this->errors;
    }
}