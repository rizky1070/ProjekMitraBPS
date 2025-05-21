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
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Throwable;

class Mitra2SurveyImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    use SkipsErrors, SkipsFailures;

    protected $id_survei;
    protected $survei;
    protected $rowErrors = [];
    protected $successCount = 0;

    public function __construct($id_survei)
    {
        $this->id_survei = $id_survei;
        $this->survei = Survei::find($id_survei);
    }

    public function model(array $row)
    {
        try {
            $tahunMasuk = $this->parseDate($row['tgl_mitra_diterima']);
            $tglIkutSurvei = $this->parseDate($row['tgl_ikut_survei']);

            $sobatId = $this->convertToNumeric($row['sobat_id']);
            $vol = $this->convertToNumeric($row['vol']);
            $honor = $this->convertToNumeric($row['rate_honor']);
            $nilai = isset($row['nilai']) ? $this->convertToNumeric($row['nilai']) : null;

            $mitra = Mitra::where('sobat_id', $sobatId)
                ->whereMonth('tahun', Carbon::parse($tahunMasuk)->month)
                ->whereYear('tahun', Carbon::parse($tahunMasuk)->year)
                ->first();

            if (!$mitra) {
                throw new \Exception("Mitra dengan SOBAT ID {$sobatId} tidak ditemukan");
            }

            if ($mitra->status_pekerjaan == 1) {
                throw new \Exception("Mitra dengan SOBAT ID {$sobatId} tidak dapat ditambahkan karena status pekerjaan bernilai 1");
            }

            // Check survey period
            $jadwalMulaiSurvei = Carbon::parse($this->survei->jadwal_kegiatan);
            $jadwalBerakhirSurvei = Carbon::parse($this->survei->jadwal_berakhir_kegiatan);
            $tahunMasukMitra = Carbon::parse($tahunMasuk);
            $tahunBerakhirMitra = Carbon::parse($mitra->tahun_selesai);

            if ($tahunBerakhirMitra < $jadwalMulaiSurvei || $tahunMasukMitra > $jadwalBerakhirSurvei) {
                throw new \Exception("Mitra tidak aktif pada periode survei");
            }

            $tglIkut = Carbon::parse($tglIkutSurvei);
            if ($tglIkut > $jadwalBerakhirSurvei) {
                throw new \Exception("Tanggal ikut survei melebihi jadwal berakhir survei");
            }

            // Check honor limit
            $totalHonorBulanIni = MitraSurvei::join('survei', 'mitra_survei.id_survei', '=', 'survei.id_survei')
                ->where('mitra_survei.id_mitra', $mitra->id_mitra)
                ->where('survei.bulan_dominan', $this->survei->bulan_dominan)
                ->sum(DB::raw('mitra_survei.honor * mitra_survei.vol'));

            $honorYangAkanDitambahkan = $honor * $vol;
            $totalHonorSetelahDitambah = $totalHonorBulanIni + $honorYangAkanDitambahkan;

            $existingMitra = MitraSurvei::where('id_mitra', $mitra->id_mitra)
                ->where('id_survei', $this->id_survei)
                ->first();

            $data = [
                'id_mitra' => $mitra->id_mitra,
                'id_survei' => $this->id_survei,
                'posisi_mitra' => $row['posisi'],
                'vol' => $vol,
                'honor' => $honor,
                'catatan' => $row['catatan'],
                'nilai' => $nilai,
                'tgl_ikut_survei' => $tglIkutSurvei,
            ];

            if ($existingMitra) {
                $existingMitra->update($data);
            } else {
                MitraSurvei::create($data);
            }

            $this->successCount++;

            // Add warning if honor exceeds limit
            if ($totalHonorSetelahDitambah > 4000000) {
                $rowNumber = $row['row_number'] ?? null;
                $this->rowErrors[$rowNumber] = "Mitra {$mitra->nama_lengkap}: Total honor sudah mencapai Rp 4.000.000 (Total: Rp " . 
                    number_format($totalHonorSetelahDitambah, 0, ',', '.') . ")";
            }

            return null;

        } catch (\Exception $e) {
            $rowNumber = $row['row_number'] ?? null;
            $mitraName = $row['nama_lengkap'] ?? 'Tidak diketahui';
            $this->rowErrors[$rowNumber] = "Mitra {$mitraName} : " . $e->getMessage();
            return null;
        }
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
            'tgl_ikut_survei' => 'required',
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

    public function onFailure(...$failures)
    {
        foreach ($failures as $failure) {
            // $failure is an instance of Maatwebsite\Excel\Validators\Failure
            $rowNumber = $failure->row();
            $rowValues = $failure->values();
            $sobatId = $rowValues['sobat_id'] ?? null;
            $mitraName = null;

            if ($sobatId) {
                $numericSobatId = $this->convertToNumeric($sobatId);
                $mitra = \App\Models\Mitra::where('sobat_id', $numericSobatId)->first();
                if ($mitra) {
                    $mitraName = $mitra->nama_lengkap;
                }
            }

            $errors = implode(', ', $failure->errors());
            if ($mitraName) {
                $this->rowErrors[$rowNumber] = "Mitra {$mitraName} : {$errors}";
            } else {
                $this->rowErrors[$rowNumber] = $errors;
            }
        }
    }
    
    public function getErrors()
    {
        return $this->errors;
    }

    public function getRowErrors()
    {
        return $this->rowErrors;
    }

    public function getTotalProcessed()
    {
        return count($this->rowErrors) + $this->successCount;
    }

    public function getSuccessCount()
    {
        return $this->successCount;
    }

    public function getFailedCount()
    {
        return count($this->rowErrors);
    }
}