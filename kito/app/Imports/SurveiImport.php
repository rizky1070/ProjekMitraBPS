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
    
    public function model(array $row)
    {
        static $rowNumber = 1;
        $row['__row__'] = $rowNumber++;

        try {
            // Skip empty rows
            if ($this->isEmptyRow($row)) {
                return null;
            }

        // Validate survey name first
        if (empty($row['nama_survei']) || !is_string($row['nama_survei'])) {
            throw new \Exception("Nama Survei: Format tidak valid");
        }

            Log::info('Importing row: ', $row);

            // Dapatkan data wilayah
            $provinsi = $this->getProvinsi();
            $kabupaten = $this->getKabupaten($provinsi);

            // Parse tanggal
            $jadwalMulai = $this->parseDate($row['jadwal'] ?? null);
            $jadwalBerakhir = $this->parseDate($row['jadwal_berakhir'] ?? null);

            // Validasi tanggal
            $this->validateDates($jadwalMulai, $jadwalBerakhir);

            // Hitung bulan dominan
            $bulanDominan = $this->calculateDominantMonth($jadwalMulai, $jadwalBerakhir);

            // Set status_survei berdasarkan tanggal hari ini
            $today = now();
            $statusSurvei = $this->determineSurveyStatus($today, $jadwalMulai, $jadwalBerakhir);

            // Cek duplikasi data berdasarkan nama_survei, jadwal_kegiatan, dan jadwal_berakhir_kegiatan
            $existingSurvei = Survei::where('nama_survei', $row['nama_survei'])
                ->whereDate('jadwal_kegiatan', $jadwalMulai->toDateString())
                ->whereDate('jadwal_berakhir_kegiatan', $jadwalBerakhir->toDateString())
                ->first();

            if ($existingSurvei) {
                // Update semua field data yang sudah ada kecuali created_at
                $existingSurvei->update([
                    'id_kabupaten' => $kabupaten->id_kabupaten,
                    'id_provinsi' => $provinsi->id_provinsi,
                    'kro' => $row['kro'],
                    'bulan_dominan' => $bulanDominan,
                    'status_survei' => $statusSurvei,
                    'tim' => $row['tim'],
                    'updated_at' => now()
                ]);
                
                Log::info('Data duplikat ditemukan dan diupdate: ', [
                    'id' => $existingSurvei->id,
                    'data' => $row
                ]);
                
                $this->successCount++;
                return null;
            }

            // Buat data baru jika tidak ada duplikat
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
        
        catch (\Exception $e) {
        $surveyName = $row['nama_survei'] ?? 'Tidak diketahui';
        $this->rowErrors[$row['__row__']] = "Survei {$surveyName}: " . $e->getMessage();
        return null;
        }
    }

    private function determineSurveyStatus(Carbon $today, Carbon $startDate, Carbon $endDate): int
    {
        if ($today->lt($startDate)) {
            return 1; // Belum dimulai
        } elseif ($today->gt($endDate)) {
            return 3; // Sudah selesai
        } else {
            return 2; // Sedang berjalan
        }
    }

    private function isEmptyRow(array $row): bool
    {
        return empty($row['nama_survei']) && empty($row['kro']) && empty($row['tim']);
    }
    
    private function getProvinsi()
    {
        $provinsi = Provinsi::where('id_provinsi', $this->defaultProvinsi)->first();
        if (!$provinsi) {
            throw new \Exception("Provinsi default (kode: {$this->defaultProvinsi}) tidak ditemukan di database.");
        }
        return $provinsi;
    }
    
    private function getKabupaten($provinsi)
    {
        $kabupaten = Kabupaten::where('id_kabupaten', $this->defaultKabupaten)
            ->where('id_provinsi', $provinsi->id_provinsi)
            ->first();
        if (!$kabupaten) {
            throw new \Exception("Kabupaten default (kode: {$this->defaultKabupaten}) tidak ditemukan di provinsi {$provinsi->nama}.");
        }
        return $kabupaten;
    }
    
    private function validateDates($jadwalMulai, $jadwalBerakhir)
    {
        if (!$jadwalMulai) {
            throw new \Exception("Tanggal jadwal mulai tidak valid");
        }
        
        if (!$jadwalBerakhir) {
            throw new \Exception("Tanggal jadwal berakhir tidak valid");
        }
        
        if ($jadwalBerakhir->lt($jadwalMulai)) {
            throw new \Exception("Tanggal berakhir harus setelah tanggal mulai");
        }
        
        $currentYear = date('Y');
        if ($jadwalMulai->year < 2000 || $jadwalMulai->year > $currentYear + 5) {
            throw new \Exception("Tahun jadwal tidak valid (harus antara 2000-".($currentYear + 5).")");
        }
    }

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

    public function rules(): array
    {
        return [
            'nama_survei' => 'required|string|max:255',
            'kro' => 'required|string|max:100',
            'tim' => 'required|string|max:255',
            'jadwal' => 'required|string',
            'jadwal_berakhir' => 'required|after:jadwal|string'
        ];   
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