<?php

namespace App\Imports;

use Illuminate\Support\Facades\Log;
use App\Models\Mitra;
use App\Models\MitraSurvei;
use App\Models\Survei;
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
    protected $survei;

    public function __construct($id_survei)
    {
        $this->id_survei = $id_survei;
        $this->survei = Survei::find($id_survei);
    }

    public function model(array $row)
    {
        $tahunMasuk = $this->parseDate($row['tgl_mitra_diterima']);
        $tglIkutSurvei = $this->parseDate($row['tgl_ikut_survei']);

        // Cari mitra berdasarkan sobat_id bulan dan tahun
        $mitra = Mitra::where('sobat_id', $row['sobat_id'])
                    ->whereMonth('tahun', Carbon::parse($tahunMasuk)->month)
                    ->whereYear('tahun', Carbon::parse($tahunMasuk)->year)
                    ->first();

        if (!$mitra) {
            throw new \Exception("Mitra dengan SOBAT ID {$row['sobat_id']} pada bulan " . Carbon::parse($tahunMasuk)->month . " dan tahun masuk " . Carbon::parse($tahunMasuk)->year . " tidak ditemukan");
        }

        // Cek status pekerjaan mitra
        if ($mitra->status_pekerjaan == 1) {
            throw new \Exception("Mitra dengan SOBAT ID {$row['sobat_id']} tidak dapat ditambahkan karena status pekerjaan bernilai 1");
        }

        // Pengecekan periode aktif mitra dengan periode survei
        $jadwalMulaiSurvei = Carbon::parse($this->survei->jadwal_kegiatan);
        $jadwalBerakhirSurvei = Carbon::parse($this->survei->jadwal_berakhir_kegiatan);
        $tahunMasukMitra = Carbon::parse($tahunMasuk);
        $tahunBerakhirMitra = Carbon::parse($mitra->tahun_selesai);

        // Cek apakah periode aktif mitra overlap dengan periode survei
        if ($tahunBerakhirMitra < $jadwalMulaiSurvei || $tahunMasukMitra > $jadwalBerakhirSurvei) {
            throw new \Exception("Mitra dengan SOBAT ID {$row['sobat_id']} tidak aktif pada periode survei ({$jadwalMulaiSurvei->format('d-m-Y')} sampai {$jadwalBerakhirSurvei->format('d-m-Y')})");
        }

        // Cek apakah tgl ikut survei berada dalam periode survei
        $tglIkut = Carbon::parse($tglIkutSurvei);
        if ($tglIkut > $jadwalBerakhirSurvei) {
            throw new \Exception("Tanggal ikut survei {$tglIkut->format('d-m-Y')} melebihi jadwal berakhir survei : {$jadwalBerakhirSurvei->format('d-m-Y')})");
        }

        // Cek apakah mitra sudah terdaftar di survei lain dengan periode yang sama
        $existingSurvei = MitraSurvei::with('survei')
        ->where('id_mitra', $mitra->id_mitra)
        ->whereHas('survei', function($query) use ($jadwalMulaiSurvei, $jadwalBerakhirSurvei) {
            $query->where(function($q) use ($jadwalMulaiSurvei, $jadwalBerakhirSurvei) {
                $q->whereBetween('jadwal_kegiatan', [$jadwalMulaiSurvei, $jadwalBerakhirSurvei])
                ->orWhereBetween('jadwal_berakhir_kegiatan', [$jadwalMulaiSurvei, $jadwalBerakhirSurvei])
                ->orWhere(function($q2) use ($jadwalMulaiSurvei, $jadwalBerakhirSurvei) {
                    $q2->where('jadwal_kegiatan', '<=', $jadwalMulaiSurvei)
                        ->where('jadwal_berakhir_kegiatan', '>=', $jadwalBerakhirSurvei);
                });
            });
        })
        ->where('id_survei', '!=', $this->id_survei)
        ->first();

        if ($existingSurvei) {
        $surveiName = $existingSurvei->survei->nama_survei ?? 'Survei Tanpa Nama';

        throw new \Exception("Mitra dengan SOBAT ID {$row['sobat_id']} sudah terdaftar di survei berikut dengan jadwal yang tumpang tindih : {$surveiName}");
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
            return null;
        }

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
            'tgl_mitra_diterima' => 'required',
            'tgl_ikut_survei' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!$this->survei) {
                        $fail("Data survei tidak ditemukan");
                        return;
                    }
                    
                    try {
                        $tglIkut = Carbon::parse($this->parseDate($value));
                        $jadwalMulai = Carbon::parse($this->survei->jadwal_kegiatan);
                        $jadwalBerakhir = Carbon::parse($this->survei->jadwal_berakhir_kegiatan);
                        
                        if ($tglIkut > $jadwalBerakhir) {
                            $fail("Tanggal ikut survei tidak boleh melebihi {$jadwalBerakhir->format('d-m-Y')}");
                        }
                    } catch (\Exception $e) {
                        $fail("Format tanggal tidak valid: {$value}");
                    }
                }
            ],
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

    public function onError(Throwable $e)
    {
        $this->errors[] = $e->getMessage();
    }
    
    public function getErrors()
    {
        return $this->errors;
    }
}