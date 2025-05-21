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
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Carbon\Carbon;
use Throwable;

class MitraImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    use SkipsErrors, SkipsFailures;

    private $rowErrors = [];
    private $successCount = 0;
    private $defaultProvinsi = '35';
    private $defaultKabupaten = '16';
    private $currentDate;
    private $currentRow = [];

    public function __construct()
    {
        $this->currentDate = Carbon::now();
    }

    public function model(array $row)
    {
        static $rowNumber = 1;
        $this->currentRow = $row;
        $row['__row__'] = $rowNumber++;
        $mitraName = $row['nama_lengkap'] ?? 'Tidak diketahui';
        
        try {
            // Skip baris yang benar-benar kosong
            if ($this->isEmptyRow($row)) {
                return null;
            }

            // Validasi akan dilakukan di method rules()
            // Jika sampai di sini, berarti data valid
            
            Log::info('Importing row: ', $row);
            
            $sobatId = $row['sobat_id'];
            $jenisKelamin = $this->convertJenisKelamin($row['jenis_kelamin']);
            $validatedPhoneNumber = $this->formatPhoneNumber($row['no_hp_mitra']);

            // Dapatkan data wilayah
            $provinsi = $this->getProvinsi($mitraName);
            $kabupaten = $this->getKabupaten($provinsi, $mitraName);
            $kecamatan = $this->getKecamatan($row, $kabupaten, $mitraName);
            $desa = $this->getDesa($row, $kecamatan, $mitraName);

            // Parse tanggal
            $tahunMulai = $this->parseTanggal($row['tgl_mitra_diterima'] ?? null, $mitraName);
            if (empty($row['tgl_mitra_diterima'])) {
                Log::info("{$mitraName} : Kolom tahun kosong, menggunakan tanggal sekarang");
            }

            // Parse tanggal berakhir
            $tahunSelesai = null;
            if (!empty($row['tgl_berakhir_mitra'])) {
                $tahunSelesai = $this->parseTanggal($row['tgl_berakhir_mitra'], $mitraName);
                if ($tahunSelesai->lt($tahunMulai)) {
                    throw new \Exception("Tanggal berakhir mitra tidak boleh sebelum tanggal mulai");
                }
            } else {
                $tahunSelesai = $tahunMulai->copy()->addMonth();
                Log::info("{$mitraName} : Kolom tgl_berakhir_mitra kosong, menggunakan 1 bulan setelah tanggal mulai");
            }
            
            // Validasi tanggal
            $this->validateDates($tahunMulai, $tahunSelesai, $mitraName);

            // Cek apakah sobat_id sudah ada di database
            $existingMitra = Mitra::where('sobat_id', $sobatId)->first();

            if ($existingMitra) {
                // Update data yang sudah ada
                $existingMitra->update([
                    'nama_lengkap' => $row['nama_lengkap'],
                    'alamat_mitra' => $row['alamat_mitra'],
                    'id_desa' => $desa->id_desa,
                    'id_kecamatan' => $kecamatan->id_kecamatan,
                    'id_kabupaten' => $kabupaten->id_kabupaten,
                    'id_provinsi' => $provinsi->id_provinsi,
                    'jenis_kelamin' => $jenisKelamin,
                    'detail_pekerjaan' => empty($row['detail_pekerjaan']) ? '-' : $row['detail_pekerjaan'],
                    'no_hp_mitra' => $validatedPhoneNumber,
                    'email_mitra' => $row['email_mitra'],
                    'tahun' => $tahunMulai,
                    'tahun_selesai' => $tahunSelesai,
                    'updated_at' => now()
                ]);
                
                $this->successCount++;
                return null;
            }

            // Buat data baru
            $this->successCount++;
            return new Mitra([
                'nama_lengkap' => $row['nama_lengkap'],
                'sobat_id' => $sobatId,
                'alamat_mitra' => $row['alamat_mitra'],
                'id_desa' => $desa->id_desa,
                'id_kecamatan' => $kecamatan->id_kecamatan,
                'id_kabupaten' => $kabupaten->id_kabupaten,
                'id_provinsi' => $provinsi->id_provinsi,
                'jenis_kelamin' => $jenisKelamin,
                'status_pekerjaan' => 0,
                'detail_pekerjaan' => empty($row['detail_pekerjaan']) ? '-' : $row['detail_pekerjaan'],
                'no_hp_mitra' => $validatedPhoneNumber,
                'email_mitra' => $row['email_mitra'],
                'tahun' => $tahunMulai,
                'tahun_selesai' => $tahunSelesai
            ]);
        } catch (\Exception $e) {
            $this->rowErrors[$row['__row__']] = $e->getMessage();
            return null;
        }
    }

    public function rules(): array
    {
        return [
            'sobat_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    $mitraName = $this->currentRow['nama_lengkap'] ?? 'Tidak diketahui';
                    $errors = [];
                    
                    if (empty($value)) {
                        $errors[] = "SOBAT ID harus diisi";
                    } elseif (!$this->isPureNumeric($value)) {
                        $errors[] = "SOBAT ID harus berupa angka semua";
                    }
                    
                    if (!empty($errors)) {
                        $fail(implode(', ', $errors));
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
                    $mitraName = $this->currentRow['nama_lengkap'] ?? 'Tidak diketahui';
                    $errors = [];
                    
                    if (empty($value)) {
                        $errors[] = "Jenis kelamin harus diisi";
                    } else {
                        $value = strtolower(trim($value));
                        if (!in_array($value, ['laki-laki', 'laki laki', 'laki', 'perempuan', '1', '2'])) {
                            $errors[] = "Jenis kelamin harus laki-laki atau perempuan";
                        }
                    }
                    
                    if (!empty($errors)) {
                        $fail(implode(', ', $errors));
                    }
                }
            ],
            'no_hp_mitra' => [
                'required',
                function ($attribute, $value, $fail) {
                    $mitraName = $this->currentRow['nama_lengkap'] ?? 'Tidak diketahui';
                    $errors = [];
                    
                    if (empty($value)) {
                        $errors[] = "Nomor HP harus diisi";
                        $fail(implode(', ', $errors));
                        return;
                    }

                    $cleanedPhone = preg_replace('/[^0-9+]/', '', $value);

                    if (empty($cleanedPhone)) {
                        $errors[] = "Nomor HP tidak valid";
                    }

                    if (preg_match('/^0/', $cleanedPhone)) {
                        if (!preg_match('/^0\d+$/', $cleanedPhone)) {
                            $errors[] = "Setelah 0 harus diikuti digit angka";
                        }
                    } elseif (!preg_match('/^\+62/', $cleanedPhone)) {
                        $errors[] = "Harus diawali dengan 0 atau +62";
                    }

                    $digits = substr($cleanedPhone, 3);
                    if (strlen($digits) < 9 || strlen($digits) > 13) {
                        $errors[] = "Harus 11-15 digit (contoh: +628123456789)";
                    }
                    
                    if (!empty($errors)) {
                        $fail(implode(', ', $errors));
                    }
                },
                'max:20'
            ],
            'email_mitra' => [
                'required',
                'email',
                'max:255',
                function ($attribute, $value, $fail) {
                    if (empty($value)) {
                        $fail("Email harus diisi");
                    }
                }
            ],
            'tgl_mitra_diterima' => 'nullable|string',
            'tgl_berakhir_mitra' => 'nullable|string'
        ];
    }

    private function convertJenisKelamin($value)
    {
        $value = strtolower(trim($value));
        
        if ($value === 'laki-laki' || $value === 'laki laki' || $value === 'laki' || $value === '1') {
            return 1;
        } elseif ($value === 'perempuan' || $value === '2') {
            return 2;
        }
        
        return 1; // default jika tidak valid
    }

    private function formatPhoneNumber($phoneNumber)
    {
        $cleanedPhone = preg_replace('/[^0-9+]/', '', $phoneNumber);

        if (preg_match('/^0/', $cleanedPhone)) {
            $cleanedPhone = '+62' . substr($cleanedPhone, 1);
        }

        return $cleanedPhone;
    }

    /**
     * Validasi semua kolom yang required dan kumpulkan semua error
     */
    private function validateRequiredFields(array $row, $mitraName, &$errors)
    {
        $requiredFields = [
            'nama_lengkap' => 'Nama Lengkap',
            'sobat_id' => 'Sobat ID',
            'alamat_mitra' => 'Alamat Mitra',
            'kode_desa' => 'Kode Desa',
            'kode_kecamatan' => 'Kode Kecamatan',
            'jenis_kelamin' => 'Jenis Kelamin',
            'no_hp_mitra' => 'Nomor HP',
            'email_mitra' => 'Email'
        ];

        foreach ($requiredFields as $field => $label) {
            if (!isset($row[$field])) {
                $errors[] = "{$mitraName} : Kolom {$label} tidak ditemukan di file Excel";
            } elseif (empty($row[$field])) {
                $errors[] = "{$mitraName} : Kolom {$label} harus diisi";
            }
        }
    }

    /**
     * Validasi jenis kelamin dengan input 'laki-laki' atau 'perempuan'
     * dan konversi ke nilai numerik (1 untuk laki-laki, 2 untuk perempuan)
     */
    private function validateJenisKelamin($value, $mitraName)
    {
        if (empty($value)) {
            throw new \Exception("{$mitraName} : Jenis kelamin harus diisi");
        }

        $value = strtolower(trim($value));
        
        if ($value === 'laki-laki' || $value === 'laki laki' || $value === 'laki') {
            return 1;
        } elseif ($value === 'perempuan') {
            return 2;
        } elseif ($value === '1' || $value === '2') {
            return (int)$value;
        }

        throw new \Exception("Jenis kelamin harus laki-laki atau perempuan");
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

    private function ensurePureNumeric($value, $fieldName, $mitraName = null)
    {
        if (empty($value)) {
            throw new \Exception(($mitraName ? "{$mitraName} : " : "") . "{$fieldName} harus diisi");
        }
        
        if (!$this->isPureNumeric($value)) {
            throw new \Exception(($mitraName ? "{$mitraName} : " : "") . "{$fieldName} harus berupa angka semua (tidak boleh mengandung karakter non-numerik)");
        }
        return $value;
    }
    
    private function isEmptyRow(array $row): bool
    {
        return empty($row['sobat_id']) && empty($row['nama_lengkap']) && empty($row['alamat_mitra']);
    }
    
    private function getProvinsi($mitraName)
    {
        $provinsi = Provinsi::where('id_provinsi', $this->defaultProvinsi)->first();
        if (!$provinsi) {
            throw new \Exception("{$mitraName} : Provinsi default (kode : {$this->defaultProvinsi}) tidak ditemukan di database.");
        }
        return $provinsi;
    }
    
    private function getKabupaten($provinsi, $mitraName)
    {
        $kabupaten = Kabupaten::where('id_kabupaten', $this->defaultKabupaten)
            ->where('id_provinsi', $provinsi->id_provinsi)
            ->first();
        if (!$kabupaten) {
            throw new \Exception("{$mitraName} : Kabupaten default (kode : {$this->defaultKabupaten}) tidak ditemukan di provinsi {$provinsi->nama_provinsi}.");
        }
        return $kabupaten;
    }
    
    private function getKecamatan(array $row, $kabupaten, $mitraName)
    {
        if (empty($row['kode_kecamatan'])) {
            throw new \Exception("{$mitraName} : Kode kecamatan harus diisi");
        }
        
        $kecamatan = Kecamatan::where('kode_kecamatan', $row['kode_kecamatan'])
            ->where('id_kabupaten', $kabupaten->id_kabupaten)
            ->first();
        if (!$kecamatan) {
            throw new \Exception("{$mitraName} : Kode kecamatan {$row['kode_kecamatan']} tidak ditemukan di kabupaten {$kabupaten->nama_kabupaten}.");
        }
        return $kecamatan;
    }
    
    private function getDesa(array $row, $kecamatan, $mitraName)
    {
        if (empty($row['kode_desa'])) {
            throw new \Exception("{$mitraName} : Kode desa harus diisi");
        }
        
        $desa = Desa::where('kode_desa', $row['kode_desa'])
            ->where('id_kecamatan', $kecamatan->id_kecamatan)
            ->first();
        if (!$desa) {
            throw new \Exception("{$mitraName} : Kode desa {$row['kode_desa']} tidak ditemukan di kecamatan {$kecamatan->nama_kecamatan}.");
        }
        return $desa;
    }
    
    private function validateDates($tahunMulai, $tahunSelesai, $mitraName)
    {
        if (!$tahunMulai) {
            throw new \Exception("{$mitraName} : tgl_mitra_diterima tidak valid");
        }
        
        if (!$tahunSelesai) {
            throw new \Exception("{$mitraName} : tgl_berakhir_mitra tidak valid");
        }
        
        // Validasi tahun masuk dalam range wajar (misal 2000-2100)
        $currentYear = date('Y');
        if ($tahunMulai->year < 2000 || $tahunMulai->year > $currentYear + 10) {
            throw new \Exception("{$mitraName} : tgl_mitra_diterima tidak valid (harus antara 2000-".($currentYear + 10).")");
        }
        
        if ($tahunSelesai->year < 2000 || $tahunSelesai->year > $currentYear + 10) {
            throw new \Exception("{$mitraName} : tgl_berakhir_mitra tidak valid (harus antara 2000-".($currentYear + 10).")");
        }
        
        if ($tahunSelesai->lt($tahunMulai)) {
            throw new \Exception("{$mitraName} : Tanggal berakhir tidak boleh sebelum tanggal mulai");
        }
    }
    
    private function validatePhoneNumber($phoneNumber, $mitraName)
    {
        if (empty($phoneNumber)) {
            throw new \Exception("Nomor HP harus diisi");
        }

        // Hilangkan spasi dan karakter khusus jika ada
        $cleanedPhone = preg_replace('/[^0-9+]/', '', $phoneNumber);

        // Cek format nomor HP
        if (empty($cleanedPhone) || (!is_string($cleanedPhone) && !is_numeric($cleanedPhone))) {
            throw new \Exception("Nomor HP tidak valid atau kosong");
        }

        // Jika diawali dengan 0, ubah menjadi +62
        if (preg_match('/^0/', $cleanedPhone)) {
            if (strlen($cleanedPhone) > 1 && preg_match('/^0\d+$/', $cleanedPhone)) {
                $cleanedPhone = '+62' . substr($cleanedPhone, 1);
            } else {
                throw new \Exception("Nomor HP tidak valid. Setelah 0 harus diikuti digit angka");
            }
        } elseif (preg_match('/^\+?0+$/', $cleanedPhone)) {
            throw new \Exception("Nomor HP tidak valid. Tidak boleh hanya 0");
        } elseif (!preg_match('/^\+62/', $cleanedPhone)) {
            throw new \Exception("Nomor HP tidak valid. Harus diawali dengan 0 atau +62");
        }

        // Validasi panjang
        $digits = substr($cleanedPhone, 3);
        if (strlen($digits) < 9 || strlen($digits) > 13) {
            throw new \Exception("Nomor HP harus 11-15 digit (contoh: +628123456789)");
        }

        return $cleanedPhone;
    }

    private function getCurrentRowName()
    {
        return $this->currentRow['nama_lengkap'] ?? 'Tidak diketahui';
    }

    public function customValidationMessages()
    {
        return [
            'sobat_id.required' => ':attribute harus diisi',
            'nama_lengkap.required' => ':attribute harus diisi',
            'alamat_mitra.required' => ':attribute harus diisi',
            'kode_desa.required' => ':attribute harus diisi',
            'kode_kecamatan.required' => ':attribute harus diisi',
            'jenis_kelamin.required' => ':attribute harus diisi',
            'no_hp_mitra.required' => ':attribute harus diisi',
            'email_mitra.required' => ':attribute harus diisi',
            'email_mitra.email' => 'Format E-mail tidak valid',
        ];
    }

    private function parseTanggal($tanggal, $mitraName)
    {
        try {
            if (empty($tanggal)) {
                Log::info("Tanggal kosong, menggunakan tanggal sekarang");
                return $this->currentDate;
            }

            if ($tanggal instanceof \DateTimeInterface) {
                return Carbon::instance($tanggal);
            }

            if (is_string($tanggal) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
                return Carbon::createFromFormat('Y-m-d', $tanggal);
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
            Log::error("Gagal parsing tanggal : {$tanggal} - Error: " . $e->getMessage());
            throw new \Exception("{$mitraName} : Format tanggal tidak valid ({$tanggal})");
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

    public function onFailure(\Maatwebsite\Excel\Validators\Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $row = $failure->row();
            $errors = implode(', ', $failure->errors());
            $mitraName = $failure->values()['nama_lengkap'] ?? 'Tidak diketahui';
            $this->rowErrors[$row] = "{$mitraName} : " . $errors;
        }
    }
}