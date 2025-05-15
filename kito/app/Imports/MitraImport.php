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

            // Parse tanggal berakhir (jika ada)
            $tahunSelesai = null;
            if (!empty($row['tgl_berakhir_mitra'])) {
                $tahunSelesai = $this->parseTanggal($row['tgl_berakhir_mitra']);
                // Validasi bahwa tanggal berakhir tidak sebelum tanggal mulai
                if ($tahunSelesai->lt($tahunMulai)) {
                    throw new \Exception("Tanggal berakhir mitra tidak boleh sebelum tanggal mulai");
                }
            } else {
                // Jika tanggal berakhir kosong, set otomatis 1 bulan setelah tanggal mulai
                $tahunSelesai = $tahunMulai->copy()->addMonth();
                Log::info("Data baris ke-{$row['__row__']}: Kolom tgl_berakhir_mitra kosong, menggunakan 1 bulan setelah tanggal mulai");
            }
            
            // Validasi tanggal
            $this->validateDates($tahunMulai, $tahunSelesai);

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
                'no_hp_mitra' => $validatedPhoneNumber, // Gunakan nomor HP yang sudah divalidasi dan dikonversi
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

        throw new \Exception("Jenis kelamin harus 'laki-laki' atau 'perempuan'");
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
    
    private function validateDates($tahunMulai, $tahunSelesai)
    {
        if (!$tahunMulai) {
            throw new \Exception("tgl_mitra_diterima tidak valid");
        }
        
        if (!$tahunSelesai) {
            throw new \Exception("tgl_berakhir_mitra tidak valid");
        }
        
        // Validasi tahun masuk dalam range wajar (misal 2000-2100)
        $currentYear = date('Y');
        if ($tahunMulai->year < 2000 || $tahunMulai->year > $currentYear + 10) {
            throw new \Exception("tgl_mitra_diterima tidak valid (harus antara 2000-".($currentYear + 10).")");
        }
        
        if ($tahunSelesai->year < 2000 || $tahunSelesai->year > $currentYear + 10) {
            throw new \Exception("tgl_berakhir_mitra tidak valid (harus antara 2000-".($currentYear + 10).")");
        }
        
        if ($tahunSelesai->lt($tahunMulai)) {
            throw new \Exception("Tanggal berakhir tidak boleh sebelum tanggal mulai");
        }
    }
    
    private function checkDuplicate($sobatId, $tahunMulai)
    {
        // Pastikan $tahunMulai adalah objek Carbon yang valid
        $tahunMulai = $tahunMulai ?: $this->currentDate;
        
        if (!($tahunMulai instanceof Carbon)) {
            $tahunMulai = $this->parseTanggal($tahunMulai);
        }

        Log::debug("Checking duplicate for:", [
            'sobat_id' => $sobatId,
            'month' => $tahunMulai->month,
            'year' => $tahunMulai->year
        ]);

        $existing = Mitra::where('sobat_id', $sobatId)
            ->whereMonth('tahun', $tahunMulai->month)
            ->whereYear('tahun', $tahunMulai->year)
            ->exists();

        if ($existing) {
            throw new \Exception("Data dengan SOBAT ID {$sobatId} sudah terdaftar untuk periode {$tahunMulai->format('m/Y')}");
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
        // Handle jika input kosong atau bukan string/numeric
        if (empty($cleanedPhone) || (!is_string($cleanedPhone) && !is_numeric($cleanedPhone))) {
            throw new \Exception("Nomor HP tidak valid atau kosong");
        }

        // Jika diawali dengan 0, ubah menjadi +62 dan ambil digit setelah 0
        if (preg_match('/^0/', $cleanedPhone)) {
            // Pastikan setelah 0 ada digit, dan tidak hanya 0 saja
            if (strlen($cleanedPhone) > 1 && preg_match('/^0\d+$/', $cleanedPhone)) {
                $cleanedPhone = '+62' . substr($cleanedPhone, 1);
            } else {
                throw new \Exception("Nomor HP tidak valid. Setelah 0 harus diikuti digit angka");
            }
        } elseif (preg_match('/^\+?0+$/', $cleanedPhone)) {
            // Kasus hanya 0 atau +0
            throw new \Exception("Nomor HP tidak valid. Tidak boleh hanya 0");
        } elseif (!preg_match('/^\+62/', $cleanedPhone)) {
            // Jika tidak diawali dengan 0 atau +62, tolak
            throw new \Exception("Nomor HP tidak valid. Harus diawali dengan 0 atau +62");
        }

        // Validasi panjang (minimal 11 digit setelah +62, maksimal 15 digit)
        $digits = substr($cleanedPhone, 3); // Hapus +62
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
                function ($attribute, $value, $fail) {
                    try {
                        $tahunMulai = $this->parseTanggal($this->row['tgl_mitra_diterima'] ?? null);
                        
                        Log::debug("Rules validation checking:", [
                            'sobat_id' => $value,
                            'month' => $tahunMulai->month,
                            'year' => $tahunMulai->year
                        ]);
                        
                        $exists = Mitra::where('sobat_id', $value)
                            ->whereMonth('tahun', $tahunMulai->month)
                            ->whereYear('tahun', $tahunMulai->year)
                            ->exists();
                            
                        if ($exists) {
                            $fail("SOBAT ID {$value} sudah terdaftar untuk periode {$tahunMulai->format('m/Y')}");
                        }
                    } catch (\Exception $e) {
                        $fail("Gagal validasi tanggal: " . $e->getMessage());
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
                            $fail("Jenis kelamin harus 'laki-laki' atau 'perempuan'");
                        }
                    } catch (\Exception $e) {
                        $fail($e->getMessage());
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
            'no_hp_mitra' => [
                'required',
                'string',
                'max:20',
                function ($attribute, $value, $fail) {
                    try {
                        $cleanedPhone = preg_replace('/[^0-9+]/', '', $value);
                        
                        if (preg_match('/^0/', $cleanedPhone)) {
                            $cleanedPhone = '+62' . substr($cleanedPhone, 1);
                        }
                        
                        if (!preg_match('/^\+62/', $cleanedPhone)) {
                            $fail('Nomor HP harus diawali dengan 0 atau +62');
                        }
                        
                        $digits = substr($cleanedPhone, 3);
                        if (strlen($digits) < 9 || strlen($digits) > 13) {
                            $fail('Nomor HP harus 11-15 digit (contoh: +628123456789)');
                        }
                    } catch (\Exception $e) {
                        $fail('Format nomor HP tidak valid');
                    }
                },
            ],
            'email_mitra' => 'required|email|max:255',
            'tgl_mitra_diterima' => 'nullable|string',
            'tgl_berakhir_mitra' => 'nullable|string'
        ];
    }

    private function parseTanggal($tanggal)
    {
        try {
            if (empty($tanggal)) {
                Log::info("Tanggal kosong, menggunakan tanggal sekarang");
                return $this->currentDate;
            }

            // Jika sudah objek Carbon/DateTime
            if ($tanggal instanceof \DateTimeInterface) {
                return Carbon::instance($tanggal);
            }

            // Handle jika tanggal dalam format Y-m-d (format date dari database)
            if (is_string($tanggal) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
                return Carbon::createFromFormat('Y-m-d', $tanggal);
            }

            // Handle Excel numeric date (angka hari sejak 1900)
            if (is_numeric($tanggal) && $tanggal > 1000) {
                $unixDate = ($tanggal - 25569) * 86400; // Konversi dari Excel date ke Unix timestamp
                $parsed = Carbon::createFromTimestamp($unixDate);
                Log::info("Parsed Excel date {$tanggal} to: " . $parsed->format('Y-m-d'));
                return $parsed;
            }

            // Handle string date (01/01/2024)
            if (is_string($tanggal)) {
                // Normalisasi pemisah tanggal (ganti / atau - dengan -)
                $normalized = str_replace(['/', '.'], '-', $tanggal);
                
                // Coba format yang mungkin
                foreach (['d-m-Y', 'm-d-Y', 'Y-m-d'] as $format) {
                    try {
                        $parsed = Carbon::createFromFormat($format, $normalized);
                        Log::info("Parsed string date {$tanggal} as {$format} to: " . $parsed->format('Y-m-d'));
                        return $parsed;
                    } catch (\Exception $e) {
                        continue;
                    }
                }
                
                // Fallback ke parsing loose
                $parsed = Carbon::parse($normalized);
                Log::info("Loose parsed string date {$tanggal} to: " . $parsed->format('Y-m-d'));
                return $parsed;
            }

            throw new \Exception("Format tanggal tidak dikenali: " . print_r($tanggal, true));
            
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