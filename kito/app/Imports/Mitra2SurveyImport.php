<?php

namespace App\Imports;

use App\Models\Mitra;
use App\Models\MitraSurvei;
use App\Models\Survei; // Tambahkan ini untuk mengakses model Survei
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Throwable;

class mitra2SurveyImport implements ToModel, WithHeadingRow, WithValidation
{
    use SkipsErrors, SkipsFailures;

    protected $id_survei;
    protected $survei; // Untuk menyimpan data survei

    public function __construct($id_survei)
    {
        $this->id_survei = $id_survei;
        $this->survei = Survei::find($id_survei); // Ambil data survei
    }

    public function model(array $row)
    {
        // Convert Excel date formats to Y-m-d
        $tahunMasuk = $this->convertExcelDate($row['tahun_masuk_mitra']);
        $tahunBerakhir = $this->convertExcelDate($row['tahun_berakhir_mitra']);
        $tglIkutSurvei = $this->convertExcelDate($row['tgl_ikut_survei']);

        // Cari mitra berdasarkan sobat_id bulan dan tahun
        $mitra = Mitra::where('sobat_id', $row['sobat_id'])
                    ->whereMonth('tahun', Carbon::parse($tahunMasuk)->month)
                    ->whereYear('tahun', Carbon::parse($tahunMasuk)->year)
                    ->first();

        if (!$mitra) {
            throw new \Exception("Mitra dengan SOBAT ID {$row['sobat_id']} pada bulan " . Carbon::parse($tahunMasuk)->month . " dan tahun masuk " . Carbon::parse($tahunMasuk)->year . " tidak ditemukan");
        }

        // Pengecekan periode aktif mitra dengan periode survei
        $jadwalMulaiSurvei = Carbon::parse($this->survei->jadwal_kegiatan);
        $jadwalBerakhirSurvei = Carbon::parse($this->survei->jadwal_berakhir_kegiatan);
        $tahunMasukMitra = Carbon::parse($tahunMasuk);
        $tahunBerakhirMitra = Carbon::parse($tahunBerakhir);

        // Cek apakah periode aktif mitra overlap dengan periode survei
        if ($tahunBerakhirMitra < $jadwalMulaiSurvei || $tahunMasukMitra > $jadwalBerakhirSurvei) {
            throw new \Exception("Mitra dengan SOBAT ID {$row['sobat_id']} tidak aktif pada periode survei ({$jadwalMulaiSurvei->format('d-m-Y')} sampai {$jadwalBerakhirSurvei->format('d-m-Y')})");
        }

        // Cek apakah tgl ikut survei berada dalam periode survei
        $tglIkut = Carbon::parse($tglIkutSurvei);
        if ($tglIkut < $jadwalMulaiSurvei || $tglIkut > $jadwalBerakhirSurvei) {
            throw new \Exception("Tanggal ikut survei {$tglIkut->format('d-m-Y')} tidak berada dalam periode survei ({$jadwalMulaiSurvei->format('d-m-Y')} sampai {$jadwalBerakhirSurvei->format('d-m-Y')})");
        }

        // Cek apakah kombinasi id_mitra dan id_survei sudah ada
        $existingMitra = MitraSurvei::where('id_mitra', $mitra->id_mitra)
                                ->where('id_survei', $this->id_survei)
                                ->first();

        if ($existingMitra) {
            // Jika sudah ada, lakukan update
            $existingMitra->update([
                'posisi_mitra' => $row['posisi'],
                'vol' => $row['vol'],
                'honor' => $row['rate_honor'],
                'catatan' => $row['catatan'],
                'nilai' => $row['nilai'],
                'tgl_ikut_survei' => $tglIkutSurvei,
            ]);
            return null; // Tidak perlu menambah data baru
        }

        // Jika belum ada, buat data baru
        return new MitraSurvei([
            'id_mitra' => $mitra->id_mitra,
            'id_survei' => $this->id_survei,
            'posisi_mitra' => $row['posisi'],
            'vol' => $row['vol'],
            'honor' => $row['rate_honor'],
            'catatan' => $row['catatan'],
            'nilai' => $row['nilai'],
            'tgl_ikut_survei' => $tglIkutSurvei,
        ]);
    }

    public function rules(): array
    {
        return [
            'sobat_id' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (!Mitra::where('sobat_id', $value)->exists()) {
                        $fail("Mitra dengan SOBAT ID {$value} tidak ditemukan");
                    }
                }
            ],
            'posisi' => 'required|string',
            'vol' => 'required|string',
            'rate_honor' => 'required|string',
            'catatan' => 'nullable|string',
            'nilai' => 'nullable|string|min:1|max:5',
            'tahun_masuk_mitra' => 'required|date',
            'tahun_berakhir_mitra' => 'required|date',
            'tgl_ikut_survei' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    if (!$this->survei) {
                        $fail("Data survei tidak ditemukan");
                        return;
                    }
                    
                    try {
                        $tglIkut = Carbon::parse($this->convertExcelDate($value));
                        $jadwalMulai = Carbon::parse($this->survei->jadwal_kegiatan);
                        $jadwalBerakhir = Carbon::parse($this->survei->jadwal_berakhir_kegiatan);
                        
                        if ($tglIkut < $jadwalMulai || $tglIkut > $jadwalBerakhir) {
                            $fail("Tanggal ikut survei harus berada antara {$jadwalMulai->format('d-m-Y')} sampai {$jadwalBerakhir->format('d-m-Y')}");
                        }
                    } catch (\Exception $e) {
                        $fail("Format tanggal tidak valid: {$value}");
                    }
                }
            ],
        ];
    }

    protected function convertExcelDate($date)
    {
        // Try to parse as Excel date (serial number)
        if (is_numeric($date)) {
            return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date))->format('Y-m-d');
        }
        
        // Try to parse as string date
        try {
            return Carbon::createFromFormat('d-m-Y', $date)->format('Y-m-d');
        } catch (\Exception $e) {
            try {
                return Carbon::parse($date)->format('Y-m-d');
            } catch (\Exception $e) {
                throw new \Exception("Format tanggal tidak valid: {$date}");
            }
        }
    }

    public function onError(Throwable $e)
    {
        $this->errors[] = $e->getMessage();
    }
    
    public function getErrors()
    {
        return $this->errors;
    }
}