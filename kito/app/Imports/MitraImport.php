<?php

namespace App\Imports;

use App\Models\Mitra;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\withHeadingRow;


class mitraImport implements ToModel, WithHeadingRow
{

    public function model(array $row)
    {
        // Cek apakah nama sudah ada
        $existingMitra = Mitra::where('nama_lengkap', $row['nama'])
                                    // ->where('id_survei', $this->id_survei)
                                    ->first();

        if ($existingMitra) {
            // Jika sudah ada, lakukan update posisi_mitra
            $existingMitra->update([
                'alamat_mitra' => $row['alamat'],
                'id_desa' => $row['desa'],
                'id_kecamatan' => $row['kecamatan']
            ]);
            return null; // Tidak perlu menambah data baru
        }

        // Jika belum ada, buat data baru
        return new Mitra([
            'nama_lengkap' => $row['nama'],
            'alamat_mitra' => $row['alamat'],
            'id_desa' => $row['desa'],
            'id_kecamatan' => $row['kecamatan'],
            'jenis_kelamin' => $row['kelamin']
        ]);
    }
}

