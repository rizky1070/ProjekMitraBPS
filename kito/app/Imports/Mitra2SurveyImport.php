<?php

namespace App\Imports;

use Illuminate\Support\Facades\Log;
use App\Models\Mitra;
use App\Models\MitraSurvei;
use App\Models\Survei;
use App\Models\PosisiMitra;
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
    private $rowErrors = [];
    private $successCount = 0;
    private $honorWarnings = [];
    private $surveyWarnings = [];
    private $currentRow = [];
    private $excelRowNumber = 2; // Data starts from row 2 (header at row 1)

    public function __construct($id_survei)
    {
        $this->id_survei = $id_survei;
        $this->survei = Survei::find($id_survei);
    }

    public function model(array $row)
    {
        $this->currentRow = $row;
        $currentRowNum = $this->excelRowNumber;
        $sobatId = $this->validateSobatId($row['sobat_id']);

        // Fetch mitra name from database immediately
        $mitraName = Mitra::where('sobat_id', $sobatId)->value('nama_lengkap') ?? 'Tidak diketahui';

        try {
            // Skip empty rows
            if ($this->isEmptyRow($row)) {
                $this->excelRowNumber++;
                return null;
            }

            Log::info('Processing Excel row: ' . $currentRowNum, $row);

            // Validate and process data
            $vol = $this->validateVol($row['vol']);
            $nilai = isset($row['nilai']) ? $this->validateNilai($row['nilai']) : null;
            $posisi = $this->validatePosisi($row['posisi']);
            $tahunMasuk = $this->validateAndParseDate($row['tgl_mitra_diterima'], 'tgl_mitra_diterima');

            // Jika tgl_ikut_survei kosong, isi dengan jadwal_kegiatan dari tabel survei
            $tglIkutSurvei = empty($row['tgl_ikut_survei'])
                ? Carbon::parse($this->survei->jadwal_kegiatan)
                : $this->validateAndParseDate($row['tgl_ikut_survei'], 'tgl_ikut_survei');

            // Validate mitra exists
            $mitra = $this->validateMitraExists($sobatId, $tahunMasuk);

            // Validate survey period
            $this->validateSurveyPeriod($tahunMasuk, $mitra->tahun_selesai, $tglIkutSurvei);

            // Cek apakah survei telah selesai (tanggal saat ini > jadwal_berakhir_kegiatan)
            $now = Carbon::now();
            $jadwalBerakhir = Carbon::parse($this->survei->jadwal_berakhir_kegiatan);
            if ($now->gt($jadwalBerakhir)) {
                $warningMessage = "Peringatan: Survei dengan ID {$this->id_survei} telah selesai pada {$jadwalBerakhir->format('d-m-Y')}. Data yang diimpor mungkin tidak relevan.";
                $this->surveyWarnings[] = $warningMessage; // Simpan di surveyWarnings
            }

            // Get position data
            $posisiMitra = $this->getPosisiMitra($posisi);

            // Check for existing record
            $existingMitra = MitraSurvei::where('id_mitra', $mitra->id_mitra)
                ->where('id_survei', $this->id_survei)
                ->first();

            // Prepare data
            $data = [
                'id_mitra' => $mitra->id_mitra,
                'id_survei' => $this->id_survei,
                'id_posisi_mitra' => $posisiMitra->id_posisi_mitra,
                'vol' => $vol,
                'catatan' => $row['catatan'] ?? null,
                'nilai' => $nilai,
                'tgl_ikut_survei' => $tglIkutSurvei,
            ];

            // Update or create
            if ($existingMitra) {
                $existingMitra->update($data);
            } else {
                MitraSurvei::create($data);
            }

            // Check honor limit
            $this->checkHonorLimit($mitra, $posisiMitra, $vol);

            $this->successCount++;
            $this->excelRowNumber++;
            return null;
        } catch (\Exception $e) {
            $this->logError($currentRowNum, $mitraName, $e->getMessage());
            $this->excelRowNumber++;
            return null;
        }
    }

    /**
     * Validation rules
     */
    public function rules(): array
    {
        return [
            'sobat_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!is_numeric($this->convertToNumeric($value))) {
                        $fail("SOBAT ID harus berupa angka");
                    }
                }
            ],
            'posisi' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (!PosisiMitra::where('nama_posisi', $value)->exists()) {
                        $fail("Posisi '{$value}' tidak terdaftar");
                    }
                },
            ],
            'vol' => [
                'required',
                function ($attribute, $value, $fail) {
                    $numericValue = $this->convertToNumeric($value);
                    if (!is_numeric($numericValue) || $numericValue <= 0) {
                        $fail("Volume harus angka positif");
                    }
                }
            ],
            'catatan' => 'nullable|string|max:255',
            'nilai' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if ($value !== null) {
                        $numericValue = $this->convertToNumeric($value);
                        if (!is_numeric($numericValue)) {
                            $fail("Nilai harus angka");
                        } elseif ($numericValue < 1 || $numericValue > 5) {
                            $fail("Nilai harus antara 1-5");
                        }
                    }
                }
            ],
            'tgl_mitra_diterima' => [
                'required',
                function ($attribute, $value, $fail) {
                    try {
                        $this->parseDate($value);
                    } catch (\Exception $e) {
                        $fail("Format tanggal tidak valid");
                    }
                }
            ],
            'tgl_ikut_survei' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    try {
                        $this->parseDate($value);
                    } catch (\Exception $e) {
                        $fail("Format tanggal tidak valid");
                    }
                }
            ],
        ];
    }

    /**
     * Custom validation messages
     */
    public function customValidationMessages()
    {
        return [
            'sobat_id.required' => 'SOBAT ID harus diisi',
            'posisi.required' => 'Posisi harus diisi',
            'vol.required' => 'Volume harus diisi',
            'tgl_mitra_diterima.required' => 'Tanggal diterima mitra harus diisi',
        ];
    }

    /**
     * Handle validation failures
     */
    public function onFailure(\Maatwebsite\Excel\Validators\Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $rowNum = $failure->row();
            $sobatId = $failure->values()['sobat_id'] ?? null;

            // Fetch name from database if available
            $mitraName = $sobatId ?
                Mitra::where('sobat_id', $sobatId)->value('nama_lengkap') ?? 'Tidak diketahui' :
                'Tidak diketahui';

            if (!isset($this->rowErrors[$rowNum])) {
                $this->rowErrors[$rowNum] = [
                    'mitra' => $mitraName,
                    'errors' => []
                ];
            }

            foreach ($failure->errors() as $error) {
                if (!in_array($error, $this->rowErrors[$rowNum]['errors'])) {
                    $this->rowErrors[$rowNum]['errors'][] = $error;
                }
            }
        }
    }

    /**
     * Helper methods
     */
    private function getMitraNameFromDatabase($sobatId): string
    {
        return Mitra::where('sobat_id', $sobatId)->value('nama_lengkap') ?? 'Tidak diketahui';
    }

    private function isEmptyRow(array $row): bool
    {
        return empty($row['sobat_id']) && empty($row['nama_lengkap']) && empty($row['posisi']);
    }

    private function convertToNumeric($value)
    {
        if (is_null($value)) {
            return null;
        }

        if (is_numeric($value)) {
            return $value;
        }

        $cleaned = preg_replace('/[^0-9,.-]/', '', $value);
        $cleaned = str_replace(',', '.', $cleaned);

        return is_numeric($cleaned) ? $cleaned : null;
    }

    private function parseDate($date)
    {
        try {
            if (empty($date)) {
                throw new \Exception("Tanggal kosong");
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
            throw new \Exception("Format tanggal tidak valid: {$date}");
        }
    }

    private function validateAndParseDate($date, $fieldName)
    {
        try {
            return $this->parseDate($date);
        } catch (\Exception $e) {
            throw new \Exception("{$fieldName}: {$e->getMessage()}");
        }
    }

    private function validateSobatId($sobatId)
    {
        $numericId = $this->convertToNumeric($sobatId);
        if (!is_numeric($numericId)) {
            throw new \Exception("SOBAT ID harus berupa angka");
        }

        return $numericId;
    }

    private function validateVol($vol)
    {
        $numericVol = $this->convertToNumeric($vol);
        if (!is_numeric($numericVol) || $numericVol <= 0) {
            throw new \Exception("Volume harus angka positif");
        }

        return $numericVol;
    }

    private function validateNilai($nilai)
    {
        $numericNilai = $this->convertToNumeric($nilai);
        if (!is_null($numericNilai)) {
            if (!is_numeric($numericNilai)) {
                throw new \Exception("Nilai harus berupa angka");
            }

            if ($numericNilai < 1 || $numericNilai > 5) {
                throw new \Exception("Nilai harus antara 1 dan 5");
            }
        }

        return $numericNilai;
    }

    private function validatePosisi($posisi)
    {
        if (empty($posisi)) {
            throw new \Exception("Posisi harus diisi");
        }

        if (!PosisiMitra::where('nama_posisi', $posisi)->exists()) {
            throw new \Exception("Posisi '{$posisi}' tidak terdaftar");
        }

        return $posisi;
    }

    private function validateMitraExists($sobatId, $tahunMasuk)
    {
        $mitra = Mitra::where('sobat_id', $sobatId)
            ->whereMonth('tahun', $tahunMasuk->month)
            ->whereYear('tahun', $tahunMasuk->year)
            ->first();

        if (!$mitra) {
            throw new \Exception("Mitra dengan SOBAT ID {$sobatId} tidak ditemukan");
        }

        if ($mitra->status_pekerjaan == 1) {
            throw new \Exception("Mitra tidak aktif (status pekerjaan 1)");
        }

        return $mitra;
    }

    private function validateSurveyPeriod($tahunMasuk, $tahunBerakhir, $tglIkutSurvei)
    {
        $periodeMulai = Carbon::parse($this->survei->jadwal_kegiatan);
        $periodeBerakhir = Carbon::parse($this->survei->jadwal_berakhir_kegiatan);

        if ($tahunBerakhir < $periodeMulai && $tahunMasuk > $periodeBerakhir) {
            throw new \Exception("Mitra tidak aktif pada periode survei");
        }

        if ($tglIkutSurvei > $periodeBerakhir) {
            throw new \Exception("Tanggal ikut survei melebihi jadwal berakhir survei");
        }
    }

    private function getPosisiMitra($posisiName)
    {
        $posisi = PosisiMitra::where('nama_posisi', $posisiName)->first();
        if (!$posisi) {
            throw new \Exception("Posisi '{$posisiName}' tidak ditemukan");
        }

        return $posisi;
    }

    private function checkHonorLimit($mitra, $posisiMitra, $vol)
    {
        $totalHonorBulanIni = MitraSurvei::join('survei', 'mitra_survei.id_survei', '=', 'survei.id_survei')
            ->join('posisi_mitra', 'mitra_survei.id_posisi_mitra', '=', 'posisi_mitra.id_posisi_mitra')
            ->where('mitra_survei.id_mitra', $mitra->id_mitra)
            ->where('survei.bulan_dominan', $this->survei->bulan_dominan)
            ->sum(DB::raw('mitra_survei.vol * posisi_mitra.rate_honor'));

        $honorYangAkanDitambahkan = $posisiMitra->rate_honor * $vol;
        $totalHonorSetelahDitambah = $totalHonorBulanIni + $honorYangAkanDitambahkan;

        if ($totalHonorSetelahDitambah > 4000000) {
            $warningMessage = "Baris {$this->excelRowNumber}: Mitra {$mitra->nama_lengkap} - Total honor melebihi Rp 4.000.000 (Total: Rp " .
                number_format($totalHonorSetelahDitambah, 0, ',', '.') . ")";
            $this->honorWarnings[] = $warningMessage;
        }
    }

    private function logError($rowNumber, $mitraName, $errorMessage)
    {
        $sobatId = $this->currentRow['sobat_id'] ?? null;

        // Always try to get name from database first
        $dbMitraName = $sobatId ?
            Mitra::where('sobat_id', $sobatId)->value('nama_lengkap') ?? $mitraName :
            $mitraName;

        if (!isset($this->rowErrors[$rowNumber])) {
            $this->rowErrors[$rowNumber] = [
                'mitra' => $dbMitraName,
                'errors' => []
            ];
        }

        $errorParts = explode("; ", $errorMessage);
        foreach ($errorParts as $part) {
            if (!in_array($part, $this->rowErrors[$rowNumber]['errors'])) {
                $this->rowErrors[$rowNumber]['errors'][] = $part;
            }
        }

        Log::error("Import error on row {$rowNumber}: {$dbMitraName} - {$errorMessage}");
    }

    /**
     * Result reporting methods
     */
    public function getErrorMessages()
    {
        $formattedErrors = [];
        ksort($this->rowErrors);

        foreach ($this->rowErrors as $rowNum => $errorData) {
            $formattedMessage = "Baris {$rowNum}: {$errorData['mitra']} - " .
                implode("; ", array_unique($errorData['errors']));

            if (strpos($formattedMessage, 'Total honor melebihi') === false) {
                $formattedErrors[] = $formattedMessage;
            }
        }

        return $formattedErrors;
    }

    public function getHonorWarningMessages()
    {
        return $this->honorWarnings;
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
        return count($this->getErrorMessages());
    }

    public function getHonorWarningsCount()
    {
        return count($this->honorWarnings);
    }

    public function getSurveyWarningMessages()
    {
        // Hapus duplikat pesan peringatan
        return array_unique($this->surveyWarnings);
    }

    public function getSurveyWarningsCount()
    {
        // Hitung jumlah pesan peringatan setelah menghapus duplikat
        return count(array_unique($this->surveyWarnings));
    }
}
