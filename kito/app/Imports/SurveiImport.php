<?php

namespace App\Imports;

use Illuminate\Support\Facades\Log;
use App\Models\Survei;
use App\Models\Provinsi;
use App\Models\Kabupaten;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Carbon\Carbon;
use Throwable;

class SurveiImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    use SkipsErrors, SkipsFailures;

    private $rowErrors = [];
    private $successCount = 0;
    private $defaultProvinsi = '35';
    private $defaultKabupaten = '16';
    private $currentRow = [];
    private $excelRowNumber = 2; // Data dimulai dari baris 2 (header di baris 1)

    public function model(array $row)
    {
        $errors = [];
        $this->currentRow = $row;
        $currentRowNum = $this->excelRowNumber;
        $surveyName = $row['nama_survei'] ?? '(Tanpa Nama)';

        try {
            // Skip empty rows
            if ($this->isEmptyRow($row)) {
                $this->excelRowNumber++;
                return null;
            }

            Log::info('Processing Excel row: ' . $currentRowNum, $row);

            // Validasi field wajib
            foreach ($this->requiredFields() as $field => $label) {
                if (!array_key_exists($field, $row)) {
                    throw new \Exception("Kolom {$label} tidak ditemukan");
                }
                if (empty(trim($row[$field]))) {
                    throw new \Exception("Kolom {$label} harus diisi");
                }
            }

            // Proses data wilayah
            $provinsi = $this->getProvinsi($surveyName);
            $kabupaten = $this->getKabupaten($provinsi, $surveyName);

            // Proses tanggal
            $jadwalMulai = $this->parseTanggal($row['jadwal'] ?? null, $surveyName);
            $jadwalBerakhir = $this->parseTanggal($row['jadwal_berakhir'] ?? null, $surveyName);
            
            // Validasi tanggal
            $this->validateDates($jadwalMulai, $jadwalBerakhir, $surveyName);

            // Hitung bulan dominan
            $bulanDominan = $this->calculateDominantMonth($jadwalMulai, $jadwalBerakhir);

            // Tentukan status survei
            $statusSurvei = $this->determineSurveyStatus(now(), $jadwalMulai, $jadwalBerakhir);

            // Cek duplikasi data
            $existingSurvei = $this->checkForDuplicate($row, $jadwalMulai, $jadwalBerakhir);

            if ($existingSurvei) {
                $this->updateExistingSurvey(
                    $existingSurvei, 
                    $row, 
                    $kabupaten, 
                    $provinsi, 
                    $bulanDominan, 
                    $statusSurvei,
                    $surveyName
                );
                $this->successCount++;
                $this->excelRowNumber++;
                return null;
            }

            if (!empty($errors)) {
                throw new \Exception(implode("; ", $errors));
            }

            $this->successCount++;
            $this->excelRowNumber++;
            return new Survei([
                'nama_survei' => $row['nama_survei'],
                'id_kabupaten' => $kabupaten->id_kabupaten,
                'id_provinsi' => $provinsi->id_provinsi,
                'kro' => $row['kro'],
                'jadwal_kegiatan' => $jadwalMulai,
                'jadwal_berakhir_kegiatan' => $jadwalBerakhir,
                'bulan_dominan' => $bulanDominan,
                'status_survei' => $statusSurvei,
                'tim' => $row['tim']
            ]);

        } catch (\Exception $e) {
            if (!isset($this->rowErrors[$currentRowNum])) {
                $this->rowErrors[$currentRowNum] = [
                    'survey' => $surveyName,
                    'errors' => []
                ];
            }

            $errorParts = explode("; ", $e->getMessage());
            foreach ($errorParts as $part) {
                if (!in_array($part, $this->rowErrors[$currentRowNum]['errors'])) {
                    $this->rowErrors[$currentRowNum]['errors'][] = $part;
                }
            }

            $this->excelRowNumber++;
            return null;
        }
    }

    /**
     * Aturan validasi untuk data survei
     */
    public function rules(): array
    {
        $this->currentRow['nama_survei'] = $this->currentRow['nama_survei'] ?? 'Tidak diketahui';
        $surveyName = $this->currentRow['nama_survei'];

        return [
            'nama_survei' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($surveyName) {
                    if (empty($value)) {
                        $fail("Nama Survei harus diisi");
                    }
                }
            ],
            'kro' => [
                'required',
                'string',
                'max:100',
                function ($attribute, $value, $fail) {
                    if (empty($value)) {
                        $fail("KRO harus diisi");
                    }
                }
            ],
            'tim' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    if (empty($value)) {
                        $fail("Tim harus diisi");
                    }
                }
            ],
            'jadwal' => [
                'required',
                function ($attribute, $value, $fail) use ($surveyName) {
                    if (empty($value)) {
                        $fail("Jadwal Mulai harus diisi");
                    }
                }
            ],
            'jadwal_berakhir' => [
                'required',
                function ($attribute, $value, $fail) use ($surveyName) {
                    if (empty($value)) {
                        $fail("Jadwal Berakhir harus diisi");
                    }
                }
            ]
        ];
    }

    /**
     * Daftar field yang wajib diisi
     */
    private function requiredFields(): array
    {
        return [
            'nama_survei' => 'Nama Survei',
            'kro' => 'KRO',
            'jadwal' => 'Jadwal Mulai',
            'jadwal_berakhir' => 'Jadwal Berakhir',
            'tim' => 'Tim'
        ];
    }

    /**
     * Cek apakah baris kosong
     */
    private function isEmptyRow(array $row): bool
    {
        foreach (array_keys($this->requiredFields()) as $field) {
            if (isset($row[$field]) && !empty(trim($row[$field]))) {
                return false;
            }
        }
        return true;
    }

    /**
     * Parse tanggal dari berbagai format
     */
    private function parseTanggal($tanggal, string $surveyName): Carbon
    {
        try {
            if (empty($tanggal)) {
                throw new \Exception("Tanggal tidak boleh kosong");
            }

            if ($tanggal instanceof \DateTimeInterface) {
                return Carbon::instance($tanggal);
            }

            if (is_numeric($tanggal) && $tanggal > 1000) {
                $unixDate = ($tanggal - 25569) * 86400;
                return Carbon::createFromTimestamp($unixDate);
            }

            if (is_string($tanggal)) {
                $normalized = str_replace(['/', '.'], '-', $tanggal);
                foreach (['d-m-Y', 'm-d-Y', 'Y-m-d'] as $format) {
                    try {
                        return Carbon::createFromFormat($format, $normalized);
                    } catch (\Exception $e) {
                        continue;
                    }
                }
                return Carbon::parse($normalized);
            }

            throw new \Exception("Format tanggal tidak dikenali");
        } catch (\Exception $e) {
            Log::error("Gagal parsing tanggal untuk survei '{$surveyName}': {$tanggal}");
            throw new \Exception("Format tanggal tidak valid ({$tanggal})");
        }
    }

    /**
     * Validasi tanggal
     */
    private function validateDates(Carbon $start, Carbon $end, string $surveyName): void
    {
        if (!$start) {
            throw new \Exception("Tanggal mulai tidak valid");
        }

        if (!$end) {
            throw new \Exception("Tanggal berakhir tidak valid");
        }

        if ($end->lt($start)) {
            throw new \Exception("Tanggal berakhir harus setelah tanggal mulai");
        }

        $currentYear = date('Y');
        if ($start->year < 2000 || $start->year > $currentYear + 5) {
            throw new \Exception("Tahun jadwal tidak valid (harus antara 2000-" . ($currentYear + 5) . ")");
        }
    }

    /**
     * Hitung bulan dominan
     */
    private function calculateDominantMonth(Carbon $start, Carbon $end): string
    {
        $months = collect();
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $months->push($date->format('m-Y'));
        }
        $mostFrequentMonth = $months->countBy()->sortDesc()->keys()->first();
        [$bulan, $tahun] = explode('-', $mostFrequentMonth);
        return Carbon::createFromDate($tahun, $bulan, 1)->toDateString();
    }

    /**
     * Cek duplikasi data
     */
    private function checkForDuplicate(array $row, Carbon $jadwalMulai, Carbon $jadwalBerakhir)
    {
        return Survei::where('nama_survei', $row['nama_survei'])
            ->whereDate('jadwal_kegiatan', $jadwalMulai->toDateString())
            ->whereDate('jadwal_berakhir_kegiatan', $jadwalBerakhir->toDateString())
            ->first();
    }

    /**
     * Update data yang sudah ada
     */
    private function updateExistingSurvey(
        Survei $existingSurvei,
        array $row,
        Kabupaten $kabupaten,
        Provinsi $provinsi,
        string $bulanDominan,
        int $statusSurvei,
        string $surveyName
    ): void {
        $existingSurvei->update([
            'id_kabupaten' => $kabupaten->id_kabupaten,
            'id_provinsi' => $provinsi->id_provinsi,
            'kro' => $row['kro'],
            'bulan_dominan' => $bulanDominan,
            'status_survei' => $statusSurvei,
            'tim' => $row['tim'],
            'updated_at' => now()
        ]);

        Log::info("Data survei {$surveyName} diupdate", [
            'id' => $existingSurvei->id,
            'data' => $row
        ]);
    }

    /**
     * Tentukan status survei
     */
    private function determineSurveyStatus(Carbon $today, Carbon $startDate, Carbon $endDate): int
    {
        if ($today->lt($startDate)) {
            return 1; // Belum dimulai
        } elseif ($today->gt($endDate)) {
            return 3; // Sudah selesai
        }
        return 2; // Sedang berjalan
    }

    /**
     * Dapatkan data provinsi
     */
    private function getProvinsi(string $surveyName): Provinsi
    {
        $provinsi = Provinsi::where('id_provinsi', $this->defaultProvinsi)->first();
        if (!$provinsi) {
            throw new \Exception("Provinsi default (kode: {$this->defaultProvinsi}) tidak ditemukan");
        }
        return $provinsi;
    }

    /**
     * Dapatkan data kabupaten
     */
    private function getKabupaten(Provinsi $provinsi, string $surveyName): Kabupaten
    {
        $kabupaten = Kabupaten::where('id_kabupaten', $this->defaultKabupaten)
            ->where('id_provinsi', $provinsi->id_provinsi)
            ->first();

        if (!$kabupaten) {
            throw new \Exception("Kabupaten default (kode: {$this->defaultKabupaten}) tidak ditemukan di provinsi {$provinsi->nama}");
        }
        return $kabupaten;
    }

    /**
     * Tangani error validasi
     */
    public function onFailure(\Maatwebsite\Excel\Validators\Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $rowNum = $failure->row();
            $surveyName = $failure->values()['nama_survei'] ?? '(Tanpa Nama)';

            if (!isset($this->rowErrors[$rowNum])) {
                $this->rowErrors[$rowNum] = [
                    'survey' => $surveyName,
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
     * Dapatkan daftar error per baris
     */
    public function getRowErrors(): array
    {
        $formattedErrors = [];
        ksort($this->rowErrors);

        foreach ($this->rowErrors as $rowNum => $errorData) {
            $formattedErrors[] = "Baris {$rowNum} - {$errorData['survey']}: " .
                implode(", ", array_unique($errorData['errors']));
        }

        return $formattedErrors;
    }

    /**
     * Jumlah total baris yang diproses
     */
    public function getTotalProcessed(): int
    {
        return count($this->rowErrors) + $this->successCount;
    }

    /**
     * Jumlah baris yang sukses diimpor
     */
    public function getSuccessCount(): int
    {
        return $this->successCount;
    }

    /**
     * Jumlah baris yang gagal
     */
    public function getFailedCount(): int
    {
        return count($this->rowErrors);
    }

    /**
     * Pesan validasi kustom
     */
    public function customValidationMessages()
    {
        return [
            'nama_survei.required' => 'Nama Survei harus diisi',
            'kro.required' => 'KRO harus diisi',
            'tim.required' => 'Tim harus diisi',
            'jadwal.required' => 'Jadwal Mulai harus diisi',
            'jadwal_berakhir.required' => 'Jadwal Berakhir harus diisi'
        ];
    }
}