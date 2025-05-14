<?php

namespace App\Imports;

use Illuminate\Support\Facades\Log;
use App\Models\Survei;
use App\Models\Provinsi;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Desa;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Carbon\Carbon;

class SurveiImport implements ToModel, WithHeadingRow, WithValidation
{
    private $errors = [];
    private $defaultProvinsi = '35';
    private $defaultKabupaten = '16';
    
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
            $kecamatan = $this->getKecamatan($row, $kabupaten);
            $desa = $this->getDesa($row, $kecamatan);

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

            // Cek duplikasi data
            $existingSurvei = Survei::where('nama_survei', $row['nama_survei'])
                ->where('jadwal_kegiatan', $jadwalMulai->toDateString())
                ->where('jadwal_berakhir_kegiatan', $jadwalBerakhir->toDateString())
                ->where('id_kecamatan', $kecamatan->id_kecamatan)
                ->where('bulan_dominan', $bulanDominan)
                ->first();

            if ($existingSurvei) {
                // Update data yang sudah ada
                $existingSurvei->update([
                    'lokasi_survei' => $row['lokasi_survei'] ?? null,
                    'id_desa' => $desa->id_desa,
                    'id_kabupaten' => $kabupaten->id_kabupaten,
                    'id_provinsi' => $provinsi->id_provinsi,
                    'kro' => $row['kro'],
                    'status_survei' => $statusSurvei,
                    'tim' => $row['tim'],
                    'updated_at' => now()
                ]);
                
                Log::info('Data duplikat ditemukan dan diupdate: ', $row);
                return null;
            }

            // Buat data baru jika tidak ada duplikat
            return new Survei([
                'nama_survei' => $row['nama_survei'],
                'lokasi_survei' => $row['lokasi_survei'] ?? null,
                'id_desa' => $desa->id_desa,
                'id_kecamatan' => $kecamatan->id_kecamatan,
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

    // Fungsi baru untuk menentukan status survei
    private function determineSurveyStatus(Carbon $today, Carbon $startDate, Carbon $endDate): int
    {
        if ($today->lt($startDate)) {
            return 0; // Belum dimulai
        } elseif ($today->gt($endDate)) {
            return 2; // Sudah selesai
        } else {
            return 1; // Sedang berjalan
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
    
    private function getKecamatan(array $row, $kabupaten)
    {
        if (empty($row['kode_kecamatan'])) {
            throw new \Exception("Kode kecamatan harus diisi");
        }
        
        $kecamatan = Kecamatan::where('kode_kecamatan', $row['kode_kecamatan'])
            ->where('id_kabupaten', $kabupaten->id_kabupaten)
            ->first();
        if (!$kecamatan) {
            throw new \Exception("Kode kecamatan {$row['kode_kecamatan']} tidak ditemukan di kabupaten {$kabupaten->nama_kabupaten}.");
        }
        return $kecamatan;
    }
    
    private function getDesa(array $row, $kecamatan)
    {
        if (empty($row['kode_desa'])) {
            throw new \Exception("Kode desa harus diisi");
        }
        
        $desa = Desa::where('kode_desa', $row['kode_desa'])
            ->where('id_kecamatan', $kecamatan->id_kecamatan)
            ->first();
        if (!$desa) {
            throw new \Exception("Kode desa {$row['kode_desa']} tidak ditemukan di kecamatan {$kecamatan->nama_kecamatan}.");
        }
        return $desa;
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
        
        // Validasi tahun masuk dalam range wajar
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

        $mostFrequentMonth = $months->countBy()->sortDesc()->keys()->first(); // contoh: "04-2029"
        [$bulan, $tahun] = explode('-', $mostFrequentMonth);
        return Carbon::createFromDate($tahun, $bulan, 1)->toDateString(); // hasil akhir: "2029-04-01"
    }

    public function rules(): array
    {
        return [
            'nama_survei' => 'required|string|max:255',
            'kode_desa' => 'required|string|max:3',
            'kode_kecamatan' => 'required|string|max:3',
            'kro' => 'required|string|max:100',
            'tim' => 'required|string|max:255',
            'lokasi_survei' => 'required|string|max:255',
            'jadwal' => 'required',
            'jadwal_berakhir' => 'required|after:jadwal'
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
    
    public function onError(\Throwable $e)
    {
        $this->errors[] = $e->getMessage();
    }
    
    public function getErrors()
    {
        return $this->errors;
    }
}