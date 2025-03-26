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
        // $existingMitra = Mitra::where('nama_lengkap', $row['nama'])
        //                             // ->where('id_survei', $this->id_survei)
        //                             ->first();

        // if ($existingMitra) {
        //     // Jika sudah ada, lakukan update tahun mitra
        //     $existingMitra->update([
        //         'tahun' => $row['tahun']
        //     ]);
        //     return null; // Tidak perlu menambah data baru
        // }

        // Jika belum ada, buat data baru
        return new Mitra([
            'nama_lengkap' => $row['nama_lengkap'],
            // 'sobat_id' => $row['sobat_id'] ?? (isset($row['id_mitra']) ? ('S' . str_pad($row['id_mitra'], 3, '0', STR_PAD_LEFT)) : null),
            'alamat_mitra' => $row['alamat_mitra'],
            'id_desa' => $row['kode_desa'],
            'id_kecamatan' => $row['kode_kecamatan'],
            'id_kabupaten' => $row['kode_kabupaten'] ?? '1', // default kabupaten 16
            'id_provinsi' => $row['kode_provinsi'] ?? '3', // default provinsi 35
            'jenis_kelamin' => $row['jenis_kelamin'],
            'no_hp_mitra' => $row['no_hp_mitra'],
            'email_mitra' => $row['email_mitra'],
            'tahun' => $row['tahun'] ?? now() // default tahun sekarang
        ]);
    }
}

