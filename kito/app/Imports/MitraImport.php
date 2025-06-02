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
    private $excelRowNumber = 2; // Data dimulai dari baris 2 (header di baris 1)

    public function __construct()
    {
        $this->currentDate = Carbon::now();
    }
    public function model(array $row)
    {
        $errors = [];
        $this->currentRow = $row;
        $currentRowNum = $this->excelRowNumber;
        try {
            // Skip empty rows
            if ($this->isEmptyRow($row)) {
                $this->excelRowNumber++;
                return null;
            }
            Log::info('Processing Excel row: ' . $currentRowNum, $row);

            // Get mitra name with fallback
            $mitraName = $this->getMitraName($row);

            $sobatId = $row['sobat_id'];
            $jenisKelamin = $this->convertJenisKelamin($row['jenis_kelamin']);
            $validatedPhoneNumber = $this->formatPhoneNumber($row['no_hp_mitra']);
            // Get region data
            $provinsi = $this->getProvinsi($mitraName);
            $kabupaten = $this->getKabupaten($provinsi, $mitraName);
            $kecamatan = $this->getKecamatan($row, $kabupaten, $mitraName);
            $desa = $this->getDesa($row, $kecamatan, $mitraName);
            // Parse dates
            $tahunMulai = $this->parseTanggal($row['tgl_mitra_diterima'] ?? null, $mitraName);
            $tahunSelesai = null;

            if (!empty($row['tgl_berakhir_mitra'])) {
                $tahunSelesai = $this->parseTanggal($row['tgl_berakhir_mitra'], $mitraName);
                if ($tahunSelesai->lt($tahunMulai)) {
                    throw new \Exception("Tanggal berakhir mitra tidak boleh sebelum tanggal mulai");
                }
            } else {
                $tahunSelesai = $tahunMulai->copy()->addMonth();
                Log::info("Kolom tgl_berakhir_mitra kosong, menggunakan 1 bulan setelah tanggal mulai");
            }
            $this->validateDates($tahunMulai, $tahunSelesai, $mitraName);
            // Check if sobat_id already exists
            $existingMitra = Mitra::where('sobat_id', $sobatId)->first();
            if ($existingMitra) {
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
                $this->excelRowNumber++;
                return null;
            }
            if (!empty($errors)) {
                throw new \Exception(implode("; ", $errors));
            }
            $this->successCount++;
            $this->excelRowNumber++;

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
            $mitraName = $this->getMitraName($row); // Get current mitra name

            if (!isset($this->rowErrors[$currentRowNum])) {
                $this->rowErrors[$currentRowNum] = [
                    'mitra' => $mitraName,
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
     * Aturan validasi untuk data mitra
     */
    public function rules(): array
    {
        $this->currentRow['nama_lengkap'] = $this->currentRow['nama_lengkap'] ?? 'Tidak diketahui';
        $mitraName = $this->currentRow['nama_lengkap'];

        return [
            'sobat_id' => [
                'required',
                function ($attribute, $value, $fail) use ($mitraName) {
                    if (empty($value)) {
                        $fail("SOBAT ID harus diisi");
                    } elseif (!$this->isPureNumeric($value)) {
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
                function ($attribute, $value, $fail) use ($mitraName) {
                    if (empty($value)) {
                        $fail("Jenis kelamin harus diisi");
                    } else {
                        $value = strtolower(trim($value));
                        if (!in_array($value, ['laki-laki', 'laki laki', 'laki', 'perempuan', '1', '2'])) {
                            $fail("Jenis kelamin harus laki-laki atau perempuan");
                        }
                    }
                }
            ],
            'no_hp_mitra' => [
                'required',
                function ($attribute, $value, $fail) {
                    $mitraName = $this->currentRow['nama_lengkap'] ?? 'Tidak diketahui';

                    if (empty($value)) {
                        $fail("Nomor HP harus diisi");
                        return;
                    }

                    $cleanedPhone = preg_replace('/[^0-9+]/', '', $value);

                    if (empty($cleanedPhone)) {
                        $fail("Nomor HP tidak valid");
                    }

                    if (preg_match('/^0/', $cleanedPhone)) {
                        if (!preg_match('/^0\d+$/', $cleanedPhone)) {
                            $fail("Setelah 0 harus diikuti digit angka");
                        }
                    } elseif (!preg_match('/^\+62/', $cleanedPhone)) {
                        $fail("Harus diawali dengan 0 atau +62");
                    }

                    $digits = substr($cleanedPhone, 3);
                    if (strlen($digits) < 9 || strlen($digits) > 13) {
                        $fail("Harus 11-15 digit (contoh: +628123456789)");
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

    /**
     * Konversi jenis kelamin ke nilai numerik
     */
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

    /**
     * Format nomor HP ke standar internasional
     */
    private function formatPhoneNumber($phoneNumber)
    {
        $cleanedPhone = preg_replace('/[^0-9+]/', '', $phoneNumber);

        if (preg_match('/^0/', $cleanedPhone)) {
            $cleanedPhone = '+62' . substr($cleanedPhone, 1);
        }

        return $cleanedPhone;
    }

    /**
     * Cek apakah nilai murni numerik
     */
    private function isPureNumeric($value): bool
    {
        if (is_numeric($value)) {
            return true;
        }

        if (is_string($value) && preg_match('/^\d+$/', $value)) {
            return true;
        }

        return false;
    }

    /**
     * Cek apakah baris kosong
     */
    private function isEmptyRow(array $row): bool
    {
        return empty($row['sobat_id']) && empty($row['nama_lengkap']) && empty($row['alamat_mitra']);
    }

    /**
     * Dapatkan data provinsi
     */
    private function getProvinsi($mitraName)
    {
        $provinsi = Provinsi::where('id_provinsi', $this->defaultProvinsi)->first();
        if (!$provinsi) {
            throw new \Exception("Provinsi default (kode : {$this->defaultProvinsi}) tidak ditemukan di database.");
        }
        return $provinsi;
    }

    /**
     * Dapatkan data kabupaten
     */
    private function getKabupaten($provinsi, $mitraName)
    {
        $kabupaten = Kabupaten::where('id_kabupaten', $this->defaultKabupaten)
            ->where('id_provinsi', $provinsi->id_provinsi)
            ->first();
        if (!$kabupaten) {
            throw new \Exception("Kabupaten default (kode : {$this->defaultKabupaten}) tidak ditemukan di provinsi {$provinsi->nama_provinsi}.");
        }
        return $kabupaten;
    }

    /**
     * Dapatkan data kecamatan
     */
    private function getKecamatan(array $row, $kabupaten, $mitraName)
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

    /**
     * Dapatkan data desa
     */
    private function getDesa(array $row, $kecamatan, $mitraName)
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

    /**
     * Validasi tanggal
     */
    private function validateDates($tahunMulai, $tahunSelesai, $mitraName)
    {
        if (!$tahunMulai) {
            throw new \Exception("tgl_mitra_diterima tidak valid");
        }

        if (!$tahunSelesai) {
            throw new \Exception("tgl_berakhir_mitra tidak valid");
        }

        $currentYear = date('Y');
        if ($tahunMulai->year < 2000 || $tahunMulai->year > $currentYear + 10) {
            throw new \Exception("tgl_mitra_diterima tidak valid (harus antara 2000-" . ($currentYear + 10) . ")");
        }

        if ($tahunSelesai->year < 2000 || $tahunSelesai->year > $currentYear + 10) {
            throw new \Exception("tgl_berakhir_mitra tidak valid (harus antara 2000-" . ($currentYear + 10) . ")");
        }

        if ($tahunSelesai->lt($tahunMulai)) {
            throw new \Exception("Tanggal berakhir tidak boleh sebelum tanggal mulai");
        }
    }

    /**
     * Tangani error validasi
     */
    public function onFailure(\Maatwebsite\Excel\Validators\Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $rowNum = $failure->row();
            $mitraName = $failure->values()['nama_lengkap'] ?? 'Tidak diketahui';

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

    private function getMitraName(array $row): string
    {
        return $row['nama_lengkap'] ?? $this->currentRow['nama_lengkap'] ?? 'Tidak diketahui';
    }


    /**
     * Dapatkan daftar error per mitra
     */
    public function getRowErrors(): array
    {
        $formattedErrors = [];
        ksort($this->rowErrors);

        foreach ($this->rowErrors as $rowNum => $errorData) {
            $formattedErrors[] = "Baris {$rowNum} - {$errorData['mitra']}: " .
                implode(", ", array_unique($errorData['errors']));
        }

        return $formattedErrors;
    }
    /**
     * Parse tanggal dari berbagai format
     */
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
            throw new \Exception("Format tanggal tidak valid ({$tanggal})");
        }
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
     * Jumlah total error (bisa lebih dari jumlah baris)
     */
    public function getFailedCount(): int
    {
        $count = 0;
        foreach ($this->rowErrors as $errors) {
            $count += is_array($errors) ? count($errors) : 1;
        }
        return $count;
    }

    /**
     * Pesan validasi kustom
     */
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
}
