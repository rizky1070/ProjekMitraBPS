<?php

namespace App\Imports;

use Illuminate\Support\Facades\Log;
use App\Models\Mitra;
use App\Models\Provinsi;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Desa;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Carbon\Carbon;

class MitraImport implements ToModel, WithHeadingRow, WithValidation
{
    private $errors = [];
    private $defaultProvinsi = '35';
    private $defaultKabupaten = '16';
    
    public function model(array $row)
    {
        static $rowNumber = 1;
        $row['__row__'] = $rowNumber++;
        
        try {
            // Skip baris yang benar-benar kosong
            if ($this->isEmptyRow($row)) {
                return null;
            }
            
            Log::info('Importing row: ', $row);
            
            // Validasi ketat sobat_id harus angka semua
            $sobatId = $this->ensurePureNumeric($row['sobat_id'], 'sobat_id');
                    
            // Konversi nilai lainnya
            $jenisKelamin = $this->ensurePureNumeric($row['jenis_kelamin'], 'jenis_kelamin');
            $statusPekerjaan = $this->ensurePureNumeric($row['status_pekerjaan'], 'status_pekerjaan');

            // Validasi minimal
            if (empty($sobatId)) {
                throw new \Exception("Baris tidak valid: sobat_id harus diisi");
            }

            $this->validatePhoneNumber($row['no_hp_mitra']);

            // Dapatkan data wilayah
            $provinsi = $this->getProvinsi();
            $kabupaten = $this->getKabupaten($provinsi);
            $kecamatan = $this->getKecamatan($row, $kabupaten);
            $desa = $this->getDesa($row, $kecamatan);

            // Parse tanggal
            $tahunMulai = $this->parseTanggal($row['tgl_mitra_diterima'] ?? null);
            // Jika tahun kosong, log informasi
            if (empty($row['tgl_mitra_diterima'])) {
                Log::info("Data baris ke-{$row['__row__']}: Kolom tahun kosong, menggunakan tanggal sekarang");
            }
            
            // Set tahun selesai otomatis 1 bulan setelah tahun mulai
            $tahunSelesai = $tahunMulai ? $tahunMulai->copy()->addMonth() : null;

            // Validasi tanggal
            $this->validateDates($tahunMulai);

            // Cek duplikasi
            $this->checkDuplicate($sobatId, $tahunMulai);
            
            return new Mitra([
                'nama_lengkap' => $row['nama_lengkap'],
                'sobat_id' => $sobatId,
                'alamat_mitra' => $row['alamat_mitra'],
                'id_desa' => $desa->id_desa,
                'id_kecamatan' => $kecamatan->id_kecamatan,
                'id_kabupaten' => $kabupaten->id_kabupaten,
                'id_provinsi' => $provinsi->id_provinsi,
                'jenis_kelamin' => $jenisKelamin,
                'status_pekerjaan' => $statusPekerjaan,
                'detail_pekerjaan' => empty($row['detail_pekerjaan']) ? '-' : $row['detail_pekerjaan'],
                'no_hp_mitra' => $row['no_hp_mitra'],
                'email_mitra' => $row['email_mitra'],
                'tahun' => $tahunMulai,
                'tahun_selesai' => $tahunSelesai
            ]);
        } catch (\Exception $e) {
            $this->errors[] = "Baris {$row['__row__']} : " . $e->getMessage();
            return null;
        }
    }
    
    /**
     * Konversi berbagai format input ke numerik
     */
    private function isPureNumeric($value): bool
    {
        // Cek jika nilai sudah numerik (integer/float)
        if (is_numeric($value)) {
            return true;
        }

        // Cek jika string hanya berisi angka
        if (is_string($value) && preg_match('/^\d+$/', $value)) {
            return true;
        }

        return false;
    }

    private function ensurePureNumeric($value, $fieldName)
    {
        if (!$this->isPureNumeric($value)) {
            throw new \Exception("{$fieldName} harus berupa angka semua (tidak boleh mengandung karakter non-numerik)");
        }
        return $value;
    }
    
    private function isEmptyRow(array $row): bool
    {
        return empty($row['sobat_id']) && empty($row['nama_lengkap']) && empty($row['alamat_mitra']);
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
            throw new \Exception("Kabupaten default (kode: {$this->defaultKabupaten}) tidak ditemukan di provinsi {$provinsi->nama_provinsi}.");
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
    
    private function validateDates($tahunMulai)
    {
        if (!$tahunMulai) {
            throw new \Exception("tgl_mitra_diterima tidak valid");
        }
        
        // Validasi tahun masuk dalam range wajar (misal 2000-2100)
        $currentYear = date('Y');
        if ($tahunMulai->year < 2000 || $tahunMulai->year > $currentYear + 10) {
            throw new \Exception("tgl_mitra_diterima tidak valid (harus antara 2000-".($currentYear + 10).")");
        }
    }
    
    private function checkDuplicate($sobatId, $tahunMulai)
    {
        $existingMitra = Mitra::where('sobat_id', $sobatId)
            ->whereMonth('tahun', $tahunMulai->month)
            ->whereYear('tahun', $tahunMulai->year)
            ->first();

        if ($existingMitra) {
            throw new \Exception("Sobat ID {$sobatId} sudah terdaftar pada bulan {$tahunMulai->month} tahun {$tahunMulai->year}");
        }
    }

    private function validatePhoneNumber($phoneNumber)
    {
        if (empty($phoneNumber)) {
            throw new \Exception("Nomor HP harus diisi");
        }

        // Hilangkan spasi dan karakter khusus jika ada
        $cleanedPhone = preg_replace('/[^0-9+]/', '', $phoneNumber);

        // Cek apakah diawali dengan +62
        if (!preg_match('/^\+62/', $cleanedPhone)) {
            throw new \Exception("Nomor HP harus diawali dengan +62");
        }
        // Validasi panjang minimal (contoh: +628123456789 = 12 digit)
        // if (strlen($cleanedPhone) < 12) {
        //     throw new \Exception("Nomor HP terlalu pendek");
        // }
    }
    
    public function rules(): array
    {
        return [
            'sobat_id' => [
            'required',
                function ($attribute, $value, $fail) {
                    if (!$this->isPureNumeric($value)) {
                        $fail("SOBAT ID harus berupa angka semua (tidak boleh mengandung karakter non-numerik)");
                    }
                },
                'max:12'
            ],
            'nama_lengkap' => 'required|string|max:255',
            'alamat_mitra' => 'required|string',
            'kode_desa' => 'required|string|max:3',
            'kode_kecamatan' => 'required|string|max:3',
            'jenis_kelamin' => [
                'required',
                function ($attribute, $value, $fail) {
                    $jenisKelamin = $this->isPureNumeric($value);
                    if (!in_array($jenisKelamin, [1, 2])) {
                        $fail("Jenis kelamin harus 1 atau 2");
                    }
                }
            ],
            'status_pekerjaan' => [
                'required',
                function ($attribute, $value, $fail) {
                    $statusPekerjaan = $this->isPureNumeric($value);
                    if (!in_array($statusPekerjaan, [0, 1])) {
                        $fail("Status pekerjaan harus 0 atau 1");
                    }
                }
            ],
            'detail_pekerjaan' => 'nullable',
            'no_hp_mitra' => [
                'required',
                'string',
                'max:20',
                function ($attribute, $value, $fail) {
                    $cleanedPhone = preg_replace('/[^0-9+]/', '', $value);
                    if (!preg_match('/^\+62/', $cleanedPhone)) {
                        $fail('Nomor HP harus diawali dengan +62');
                    }
                    // if (strlen($cleanedPhone) < 12) {
                    //     $fail('Nomor HP terlalu pendek');
                    // }
                },
            ],
            'email_mitra' => 'required|email|max:255',
            'tgl_mitra_diterima' => 'nullable'
        ];
    }

    private function parseTanggal($tanggal)
    {
        try {
            if (empty($tanggal)) {
                return Carbon::now(); // Return current date if empty
            }

            if ($tanggal instanceof \DateTimeInterface) {
                return Carbon::instance($tanggal);
            }

            if (is_numeric($tanggal)) {
                $unixDate = ($tanggal - 25569) * 86400;
                return Carbon::createFromTimestamp($unixDate);
            }

            if (is_string($tanggal)) {
                if (preg_match('/^\d+$/', $tanggal)) {
                    $unixDate = ($tanggal - 25569) * 86400;
                    return Carbon::createFromTimestamp($unixDate);
                }
                
                return Carbon::parse($tanggal);
            }

            throw new \Exception("Format tanggal tidak dikenali");
            
        } catch (\Exception $e) {
            Log::error("Gagal parsing tanggal: {$tanggal} - Error: " . $e->getMessage());
            throw new \Exception("Format tanggal tidak valid: {$tanggal}");
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