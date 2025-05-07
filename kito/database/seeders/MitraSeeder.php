<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MitraSeeder extends Seeder
{
    public function run()
    {
        $mitraData = [
            // Format: [id_kecamatan, id_desa, sobat_id_suffix, nama_suffix, jenis_kelamin, no_hp_suffix, tahun, status_pekerjaan]
            [10, 1, '01', 'A', 1, '1234567890', '2018-03-15', 0],
            [20, 20, '02', 'B', 2, '2345678901', '2019-07-10', 0],
            [30, 38, '03', 'C', 1, '3456789012', '2020-11-05', 0],
            [40, 58, '04', 'D', 2, '4567890123', '2021-02-20', 0],
            [50, 71, '05', 'E', 1, '5678901234', '2022-05-12', 0],
            [60, 90, '06', 'F', 2, '6789012345', '2023-09-08', 0],
            [70, 109, '07', 'G', 1, '7890123456', '2024-01-20', 0],
            [80, 126, '08', 'H', 2, '8901234567', '2025-04-10', 0],
            [90, 145, '09', 'I', 2, '901234569', '2025-04-15', 0],
            [91, 162, '10', 'J', 2, '01234510', '2025-04-10', 0],
            [100, 174, '11', 'K', 1, '012345678', '2025-05-01', 1],
            [110, 190, '12', 'L', 2, '123456789', '2025-06-15', 0],
            [120, 206, '13', 'M', 1, '234567890', '2025-07-10', 1],
            [130, 222, '14', 'N', 2, '345678901', '2025-08-05', 0],
            [140, 237, '15', 'O', 1, '456789012', '2025-09-20', 1],
            [150, 251, '16', 'P', 2, '567890123', '2025-10-15', 0],
            [160, 271, '17', 'Q', 1, '678901234', '2020-11-10', 1],
            [170, 287, '18', 'R', 2, '789012345', '2020-12-05', 0],
            [170, 288, '19', 'S', 1, '890123456', '2020-01-20', 1],
            [170, 289, '20', 'T', 2, '901234567', '2020-02-15', 0],
            [170, 290, '21', 'anomali mitra', 2, '1238888', '2025-01-15', 0],
        ];

        DB::table('mitra')->insert(
            array_map(function ($item) {
                $tahun = Carbon::parse($item[6]);
                $tahunSelesai = $item[5] === '1238888' ? // Special case for anomaly
                    Carbon::parse('2025-12-15') : 
                    (clone $tahun)->addMonth();
                
                return [
                    'id_kecamatan' => $item[0],
                    'id_kabupaten' => '16',
                    'id_provinsi' => '35',
                    'id_desa' => $item[1],
                    'sobat_id' => 'S' . str_pad($item[2], 3, '0', STR_PAD_LEFT),
                    'nama_lengkap' => 'Mitra ' . $item[3],
                    'alamat_mitra' => 'Alamat Mitra ' . $item[3],
                    'jenis_kelamin' => $item[4],
                    'no_hp_mitra' => '08' . $item[5],
                    'email_mitra' => 'mitra' . strtolower($item[3]) . '@example.com',
                    'tahun' => $tahun->format('Y-m-d'),
                    'tahun_selesai' => $tahunSelesai->format('Y-m-d'),
                    'status_pekerjaan' => $item[7],
                    'detail_pekerjaan' => 'Pekerjaan Mitra ' . $item[3],
                ];
            }, $mitraData)
        );
    }
}