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
    private $currentDate;
    private $jenisKelaminMap = [
        1 => 'Laki-laki',
        2 => 'Perempuan'
    ];

    public function __construct()
    {
        $this->currentDate = Carbon::now();
    }

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
                    
            // Validasi dan konversi jenis kelamin
            $jenisKelamin = $this->validateJenisKelamin($row['jenis_kelamin']);
            
            // Konversi nilai lainnya
            $statusPekerjaan = $this->ensurePureNumeric($row['status_pekerjaan'], 'status_pekerjaan');

            // Validasi minimal
            if (empty($sobatId)) {
                throw new \Exception("Baris tidak valid: sobat_id harus diisi");
            }

            // Validasi dan konversi nomor HP
            $validatedPhoneNumber = $this->validatePhoneNumber($row['no_hp_mitra']);

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

            // Cek duplikasi dan update jika data sudah ada
            $existingMitra = Mitra::where('sobat_id', $sobatId)
                ->whereDate('tahun', $tahunMulai->toDateString())
                ->whereDate('tahun_selesai', $tahunSelesai->toDateString())
                ->first();

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
                    'status_pekerjaan' => $statusPekerjaan,
                    'detail_pekerjaan' => empty($row['detail_pekerjaan']) ? '-' : $row['detail_pekerjaan'],
                    'no_hp_mitra' => $validatedPhoneNumber,
                    'email_mitra' => $row['email_mitra'],
                    'updated_at' => now()
                ]);
                
                Log::info('Data duplikat ditemukan dan diupdate: ', [
                    'id' => $existingMitra->id,
                    'data' => $row
                ]);
                
                return null;
            }

            // Buat data baru jika tidak ada duplikat
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
                'no_hp_mitra' => $validatedPhoneNumber,
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
     * Validasi jenis kelamin dengan input 'laki-laki' atau 'perempuan'
     * dan konversi ke nilai numerik (1 untuk laki-laki, 2 untuk perempuan)
     */
    private function validateJenisKelamin($value)
    {
        if (empty($value)) {
            throw new \Exception("Jenis kelamin harus diisi");
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
    
    private function validatePhoneNumber($phoneNumber)
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
    
    public function rules(): array
    {
        return [
            'sobat_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!$this->isPureNumeric($value)) {
                        $fail("SOBAT ID harus berupa angka semua");
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
                    try {
                        $value = strtolower(trim($value));
                        $validValues = ['laki-laki', 'laki laki', 'laki', 'perempuan', '1', '2'];
                        
                        if (!in_array($value, $validValues)) {
                            $fail("Jenis kelamin harus laki-laki atau perempuan");
                        }
                    } catch (\Exception $e) {
                        $fail($e->getMessage());
                    }
                }
            ],
            'status_pekerjaan' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!$this->isPureNumeric($value)) {
                        $fail("Status pekerjaan harus berupa angka");
                    }
                }
            ],
            'no_hp_mitra' => [
                'required',
                'string',
                'max:20',
                function ($attribute, $value, $fail) {
                    try {
                        $this->validatePhoneNumber($value);
                    } catch (\Exception $e) {
                        $fail($e->getMessage());
                    }
                },
            ],
            'email_mitra' => 'required|email|max:255',
            'tgl_mitra_diterima' => 'nullable|string'
        ];
    }

    private function parseTanggal($tanggal)
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