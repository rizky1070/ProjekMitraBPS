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
    private $failedCount = 0;
    private $processedRows = 0;
    private $defaultProvinsi = '35';
    private $defaultKabupaten = '16';
    
    private $requiredFields = [
        'nama_survei' => 'Nama Survei',
        'kro' => 'KRO',
        'jadwal' => 'Jadwal Mulai',
        'jadwal_berakhir' => 'Jadwal Berakhir',
        'tim' => 'Tim'
    ];

    public function model(array $row)
    {
        $this->processedRows++;
        $currentRow = $this->processedRows + 1; // +1 karena heading row
        $errorsInRow = [];
        $surveyName = $row['nama_survei'] ?? '(Tanpa Nama)';

        try {
            // Cek jika baris benar-benar kosong
            if ($this->isRowCompletelyEmpty($row)) {
                throw new \Exception("Data survei kosong - semua kolom wajib tidak diisi");
            }

            // Validasi field wajib dan tipe data
            $this->validateRow($row, $surveyName, $errorsInRow);

            // Jika ada error validasi, lempar exception
            if (!empty($errorsInRow)) {
                throw new \Exception(implode("; ", $errorsInRow));
            }

            Log::info('Memproses import survei: ', ['nama_survei' => $surveyName, 'data' => $row]);

            // Proses data wilayah
            $provinsi = $this->getProvinsi();
            $kabupaten = $this->getKabupaten($provinsi);

            // Proses tanggal
            $jadwalMulai = $this->parseDate($row['jadwal'] ?? null, $surveyName, $errorsInRow);
            $jadwalBerakhir = $this->parseDate($row['jadwal_berakhir'] ?? null, $surveyName, $errorsInRow);

            // Validasi tanggal
            $this->validateDates($jadwalMulai, $jadwalBerakhir, $surveyName, $errorsInRow);

            // Jika ada error tanggal, lempar exception
            if (!empty($errorsInRow)) {
                throw new \Exception(implode("; ", $errorsInRow));
            }

            // Hitung bulan dominan
            $bulanDominan = $this->calculateDominantMonth($jadwalMulai, $jadwalBerakhir);

            // Tentukan status survei
            $statusSurvei = $this->determineSurveyStatus(now(), $jadwalMulai, $jadwalBerakhir);

            // Cek duplikasi data
            $existingSurvei = $this->checkForDuplicate($row, $jadwalMulai, $jadwalBerakhir);

            if ($existingSurvei) {
                $this->updateExistingSurvey($existingSurvei, $row, $kabupaten, $provinsi, $bulanDominan, $statusSurvei);
                return null;
            }

            // Buat data baru
            $this->successCount++;
            return $this->createNewSurvey($row, $kabupaten, $provinsi, $jadwalMulai, $jadwalBerakhir, $bulanDominan, $statusSurvei);

        } catch (\Exception $e) {
            $this->rowErrors[$surveyName] = "Survei '{$surveyName}': " . $e->getMessage();
            $this->failedCount++;
            return null;
        }
    }

    private function validateRow(array $row, string $surveyName, array &$errors): void
    {
        // Validasi field wajib
        foreach ($this->requiredFields as $field => $label) {
            if (!array_key_exists($field, $row)) {
                $errors[] = "Kolom {$label} tidak ditemukan";
                continue;
            }
            
            if (empty(trim($row[$field]))) {
                $errors[] = "Kolom {$label} harus diisi";
            }
        }

        // Validasi tipe data
        if (isset($row['nama_survei']) && !is_string($row['nama_survei'])) {
            $errors[] = "Nama Survei harus berupa teks";
        }

        if (isset($row['kro']) && !is_string($row['kro'])) {
            $errors[] = "KRO harus berupa teks";
        }

        if (isset($row['tim']) && !is_string($row['tim'])) {
            $errors[] = "Tim harus berupa teks";
        }
    }

    private function parseDate($date, string $surveyName, array &$errors): ?Carbon
    {
        if (empty($date)) {
            $errors[] = "Tanggal tidak boleh kosong";
            return null;
        }

        try {
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

            $errors[] = "Format tanggal tidak dikenali";
            return null;
            
        } catch (\Exception $e) {
            Log::error("Gagal parsing tanggal untuk survei '{$surveyName}': {$date} - Error: " . $e->getMessage());
            return null;
        }
    }

    private function validateDates(?Carbon $start, ?Carbon $end, string $surveyName, array &$errors): void
    {
        if (!$start) {
            $errors[] = "Tanggal mulai tidak valid";
        }
        
        if (!$end) {
            $errors[] = "Tanggal berakhir tidak valid";
        }
        
        if ($start && $end && $end->lt($start)) {
            $errors[] = "Tanggal berakhir harus setelah tanggal mulai";
        }
        
        if ($start) {
            $currentYear = date('Y');
            if ($start->year < 2000 || $start->year > $currentYear + 5) {
                $errors[] = "Tahun jadwal tidak valid (harus antara 2000-".($currentYear + 5).")";
            }
        }
    }

    private function isRowCompletelyEmpty(array $row): bool
    {
        foreach (array_keys($this->requiredFields) as $field) {
            if (isset($row[$field]) && !empty(trim($row[$field]))) {
                return false;
            }
        }
        return true;
    }

    private function checkForDuplicate(array $row, Carbon $jadwalMulai, Carbon $jadwalBerakhir)
    {
        return Survei::where('nama_survei', $row['nama_survei'])
            ->whereDate('jadwal_kegiatan', $jadwalMulai->toDateString())
            ->whereDate('jadwal_berakhir_kegiatan', $jadwalBerakhir->toDateString())
            ->first();
    }

    private function updateExistingSurvey(
        Survei $existingSurvei,
        array $row,
        Kabupaten $kabupaten,
        Provinsi $provinsi,
        string $bulanDominan,
        int $statusSurvei
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
        
        Log::info('Data survei diupdate: ' . $row['nama_survei'], [
            'id' => $existingSurvei->id,
            'data' => $row
        ]);
        
        $this->successCount++;
    }

    private function createNewSurvey(
        array $row,
        Kabupaten $kabupaten,
        Provinsi $provinsi,
        Carbon $jadwalMulai,
        Carbon $jadwalBerakhir,
        string $bulanDominan,
        int $statusSurvei
    ): Survei {
        $this->successCount++;
        
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
    }

    private function determineSurveyStatus(Carbon $today, Carbon $startDate, Carbon $endDate): int
    {
        if ($today->lt($startDate)) {
            return 1; // Belum dimulai
        } elseif ($today->gt($endDate)) {
            return 3; // Sudah selesai
        }
        return 2; // Sedang berjalan
    }
    
    private function getProvinsi(): Provinsi
    {
        $provinsi = Provinsi::where('id_provinsi', $this->defaultProvinsi)->first();
        if (!$provinsi) {
            throw new \Exception("Provinsi default (kode: {$this->defaultProvinsi}) tidak ditemukan");
        }
        return $provinsi;
    }
    
    private function getKabupaten(Provinsi $provinsi): Kabupaten
    {
        $kabupaten = Kabupaten::where('id_kabupaten', $this->defaultKabupaten)
            ->where('id_provinsi', $provinsi->id_provinsi)
            ->first();
        if (!$kabupaten) {
            throw new \Exception("Kabupaten default (kode: {$this->defaultKabupaten}) tidak ditemukan di provinsi {$provinsi->nama}");
        }
        return $kabupaten;
    }

    public function rules(): array
    {
        return [
            'nama_survei' => 'required|string|max:255',
            'kro' => 'required|string|max:100',
            'tim' => 'required|string|max:255',
            'jadwal' => 'required',
            'jadwal_berakhir' => 'required'
        ];   
    }
    
    public function getRowErrors(): array
    {
        return $this->rowErrors;
    }

    public function getTotalProcessed(): int
    {
        return $this->processedRows;
    }

    public function getSuccessCount(): int
    {
        return $this->successCount;
    }

    public function getFailedCount(): int
    {
        return $this->failedCount;
    }
}