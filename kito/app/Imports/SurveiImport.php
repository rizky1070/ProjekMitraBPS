<?php

namespace App\Imports;

use App\Models\Survei;
use App\Models\Desa;         // Import model Desa
use App\Models\Kecamatan;    // Import model Kecamatan
use App\Models\Kabupaten;    // Import model Kabupaten
use App\Models\Provinsi;     // Import model Provinsi
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class SurveiImport implements ToModel, WithStartRow

{
    public function model(array $row)
    {
        return new survei([
           'nama_survei' => $row[4], // Nama survei
            'id_desa' => $row[3],
            'id_kecamatan' => $row[2],
            'id_kabupaten' => $row[0],
            'id_provinsi' => $row[1],
            'lokasi_survei' => $row[5],
            'kro' => $row[6], 
            'jadwal_kegiatan' => $row[7],
            'status_survei' => $row[8],
        ]);
    }

    public function startRow(): int
    {
        return 2;
    }
    // public function model(array $row)
    // {

    //     dd($row);
    //     // Mencocokkan kode_desa, kode_kecamatan, kode_kabupaten, dan kode_provinsi
    //     $desa = Desa::where('kode_desa', $row[0])->first(); // Sesuaikan dengan indeks kolom di Excel
    //     $kecamatan = Kecamatan::where('kode_kecamatan', $row[1])->first(); // Sesuaikan dengan indeks kolom di Excel
    //     $kabupaten = Kabupaten::where('kode_kabupaten', $row[2])->first(); // Sesuaikan dengan indeks kolom di Excel
    //     $provinsi = Provinsi::where('kode_provinsi', $row[3])->first(); // Sesuaikan dengan indeks kolom di Excel
        
    //     // Memasukkan data ke tabel Survei
    //     return new Survei([
    //         'nama_survei' => $row[4], // Nama survei
    //         'id_desa' => $desa ? $desa->id_desa : null,  // Mencocokkan dan memasukkan id_desa
    //         'id_kecamatan' => $kecamatan ? $kecamatan->id_kecamatan : null, // Mencocokkan dan memasukkan id_kecamatan
    //         'id_kabupaten' => $kabupaten ? $kabupaten->id_kabupaten : null, // Mencocokkan dan memasukkan id_kabupaten
    //         'id_provinsi' => $provinsi ? $provinsi->id_provinsi : null,  // Mencocokkan dan memasukkan id_provinsi
    //         'lokasi_survei' => $row[5],
    //         'kro' => $row[6],
    //         'jadwal_kegiatan' => $row[7],
    //         'status_survei' => $row[8],
    //     ]);
    // }
}

