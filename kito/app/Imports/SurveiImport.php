<?php

namespace App\Imports;

use Illuminate\Support\Facades\Log;
use App\Models\Survei;
use App\Models\Provinsi;
use App\Models\Kabupaten;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Carbon\Carbon;

class SurveiImport implements ToModel, WithHeadingRow, WithValidation
{
    private $errors = [];
    private $defaultProvinsi = '35';
    private $defaultKabupaten = '16';
    private $currentDate;
    
    public function __construct()
    {
        $this->currentDate = Carbon::now();
    }

    public function model(array $row)
    {
        static $rowNumber = 1;
        $row['__row__'] = $rowNumber++;

        try {
            // Skip baris kosong
            if ($this->isEmptyRow($row)) {
                return null;
            }

            Log::info('Importing row: ', $row);

            // Dapatkan data wilayah
            $provinsi = $this->getProvinsi();
            $kabupaten = $this->getKabupaten($provinsi);

            // Parse tanggal mulai
            $jadwalMulai = $this->parseTanggal($row['jadwal'] ?? null);
            if (empty($row['jadwal'])) {
                throw new \Exception("Jadwal mulai harus diisi");
            }

            // Parse tanggal berakhir
            $jadwalBerakhir = $this->parseTanggal($row['jadwal_berakhir'] ?? null);
            if (empty($row['jadwal_berakhir'])) {
                // Jika tanggal berakhir kosong, gunakan 1 hari setelah tanggal mulai
                $jadwalBerakhir = $jadwalMulai->copy()->addDay();
                Log::info("Data baris ke-{$row['__row__']}: Kolom jadwal_berakhir kosong, menggunakan 1 hari setelah tanggal mulai");
            }

            // Validasi tanggal
            $this->validateDates($jadwalMulai, $jadwalBerakhir);

            // Hitung bulan dominan
            $bulanDominan = $this->calculateDominantMonth($jadwalMulai, $jadwalBerakhir);

            // Set status_survei berdasarkan tanggal hari ini
            $statusSurvei = $this->determineSurveyStatus($this->currentDate, $jadwalMulai, $jadwalBerakhir);

            // Cek duplikasi data
            $existingSurvei = Survei::where('nama_survei', $row['nama_survei'])
                ->whereDate('jadwal_kegiatan', $jadwalMulai->toDateString())
                ->whereDate('jadwal_berakhir_kegiatan', $jadwalBerakhir->toDateString())
                ->first();

            if ($existingSurvei) {
                // Update data yang sudah ada
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
                
                return null;
            }

            // Buat data baru jika tidak ada duplikat
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
            $this->errors[] = "Baris {$row['__row__']} : " . $e->getMessage();
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
            'jadwal' => [
                'required',
                function ($attribute, $value, $fail) {
                    try {
                        $this->parseTanggal($value);
                    } catch (\Exception $e) {
                        $fail("Format tanggal tidak valid. Gunakan format: YYYY-MM-DD, DD-MM-YYYY, MM-DD-YYYY, atau angka serial Excel");
                    }
                }
            ],
            'jadwal_berakhir' => [
                'required',
                function ($attribute, $value, $fail) {
                    try {
                        $this->parseTanggal($value);
                    } catch (\Exception $e) {
                        $fail("Format tanggal tidak valid. Gunakan format: YYYY-MM-DD, DD-MM-YYYY, MM-DD-YYYY, atau angka serial Excel");
                    }
                },
                function ($attribute, $value, $fail) {
                    try {
                        $jadwal = $this->parseTanggal(request()->input('jadwal'));
                        $jadwalBerakhir = $this->parseTanggal($value);
                        
                        if ($jadwalBerakhir->lt($jadwal)) {
                            $fail("Tanggal berakhir harus setelah tanggal mulai");
                        }
                    } catch (\Exception $e) {
                        // Skip jika parsing gagal, sudah dihandle oleh validator sebelumnya
                    }
                }
            ]
        ];   
    }
    
    private function parseTanggal($tanggal)
    {
        try {
            if (empty($tanggal)) {
                return null;
            }

            // Jika sudah dalam format Carbon atau DateTime, langsung return
            if ($tanggal instanceof \DateTimeInterface) {
                return Carbon::instance($tanggal);
            }

            // Handle Excel date serial number (angka)
            if (is_numeric($tanggal)) {
                // Excel date serial number (1 = 1 Jan 1900)
                if ($tanggal > 60) {
                    $tanggal -= 1; // Koreksi bug Excel 1900
                }
                $unixDate = ($tanggal - 25569) * 86400; // 25569 = days between 1970 and 1900
                return Carbon::createFromTimestamp($unixDate);
            }

            // Handle string dates
            if (is_string($tanggal)) {
                // Coba parse dari format Excel string (misal "3/14/2023")
                $normalized = str_replace(['/', '.'], '-', $tanggal);
                
                // Coba berbagai format umum
                $formatsToTry = [
                    'Y-m-d',    // 2023-03-14
                    'm-d-Y',     // 03-14-2023
                    'd-m-Y',      // 14-03-2023
                    'Y/m/d',     // 2023/03/14 (sudah dinormalisasi ke -)
                    'm/d/Y',     // 03/14/2023 (sudah dinormalisasi ke -)
                    'd/m/Y',     // 14/03/2023 (sudah dinormalisasi ke -)
                    'Ymd',        // 20230314
                    'm-d-y',      // 03-14-23
                    'd-m-y',      // 14-03-23
                    'M j, Y',     // Mar 14, 2023
                    'j M Y',      // 14 Mar 2023
                ];
                
                foreach ($formatsToTry as $format) {
                    try {
                        return Carbon::createFromFormat($format, $normalized);
                    } catch (\Exception $e) {
                        continue;
                    }
                }
                
                // Coba parse dengan Carbon secara otomatis
                try {
                    return Carbon::parse($normalized);
                } catch (\Exception $e) {
                    throw new \Exception("Format tanggal tidak dikenali");
                }
            }

            throw new \Exception("Format tanggal tidak dikenali");
            
        } catch (\Exception $e) {
            Log::error("Gagal parsing tanggal: {$tanggal} - Error: " . $e->getMessage());
            throw new \Exception("Format tanggal tidak valid: {$tanggal}. Format yang diterima: YYYY-MM-DD, DD-MM-YYYY, MM-DD-YYYY, atau angka serial Excel");
        }
    }
    
    public function onError(\Throwable $e)
    {
        $this->errors[] = $e->getMessage();
    }
    
    public function getErrors()
    {
        return $this->errors;
    }
}