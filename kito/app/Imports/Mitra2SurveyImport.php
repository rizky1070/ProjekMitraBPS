<?php

namespace App\Imports;

use App\Models\Mitra;
use App\Models\MitraSurvei;
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

    public function __construct($id_survei)
    {
        $this->id_survei = $id_survei;
    }

    public function model(array $row)
    {
        // Ambil tahun dari Excel dan konversi ke format Y-m-d (1 Januari tahun tersebut)
        $tahun = $row['tahun_mitra'];
        $tahunDate = Carbon::createFromDate($tahun, 1, 1)->format('Y-m-d');

        // Cari mitra berdasarkan sobat_id dan tahun (hanya bandingkan tahunnya saja)
        $mitra = Mitra::where('sobat_id', $row['sobat_id'])
                     ->whereYear('tahun', $tahun)
                     ->first();

        if (!$mitra) {
            throw new \Exception("Mitra dengan SOBAT ID {$row['sobat_id']} dan tahun {$tahun} tidak ditemukan");
        }

        // Cek apakah kombinasi id_mitra dan id_survei sudah ada
        $existingMitra = MitraSurvei::where('id_mitra', $mitra->id_mitra)
                                ->where('id_survei', $this->id_survei)
                                ->first();

        if ($existingMitra) {
            // Jika sudah ada, lakukan update posisi_mitra
            $existingMitra->update([
                'posisi_mitra' => $row['posisi']
            ]);
            return null; // Tidak perlu menambah data baru
        }

        // Jika belum ada, buat data baru
        return new MitraSurvei([
            'id_mitra' => $mitra->id_mitra,
            'id_survei' => $this->id_survei,
            'posisi_mitra' => $row['posisi'],
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
            'tahun_mitra' => [
                'required',
                'numeric',
                'digits:4',
                function ($attribute, $value, $fail) {
                    $sobatId = request()->input('sobat_id');
                    if (!Mitra::where('sobat_id', $sobatId)
                            ->whereYear('tahun', $value)
                            ->exists()) {
                        $fail("Mitra dengan SOBAT ID pada baris ini dan tahun {$value} tidak ditemukan");
                    }
                }
            ],
            'posisi' => 'required|string',
        ];
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