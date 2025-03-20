<?php

namespace App\Imports;

use App\Models\MitraSurvei;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\withHeadingRow;


class mitra2SurveyImport implements ToModel, WithHeadingRow
{
    protected $id_survei;

    public function __construct($id_survei)
    {
        $this->id_survei = $id_survei;
    }

    public function model(array $row)
    {
        // Cek apakah kombinasi id_mitra dan id_survei sudah ada
        $existingMitra = MitraSurvei::where('id_mitra', $row['id_mitra'])
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
            'id_mitra' => (int) $row['id_mitra'],
            'id_survei' => $this->id_survei, // Ambil dari properti class
            'posisi_mitra' => $row['posisi'],
        ]);
    }
}

