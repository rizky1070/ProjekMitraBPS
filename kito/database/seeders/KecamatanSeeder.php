<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KecamatanSeeder extends Seeder
{
    public function run()
    {
        $kecamatanData = [
            ['010', 'JATIREJO', '16'],
            ['020', 'GONDANG', '16'],
            ['030', 'PACET', '16'],
            ['040', 'TRAWAS', '16'],
            ['050', 'NGORO', '16'],
            ['060', 'PUNGGING', '16', '16'],
            ['070', 'KUTOREJO', '16'],
            ['080', 'MOJOSARI', '16'],
            ['090', 'BANGSAL', '16'],
            ['091', 'MOJOANYAR', '16'],
            ['100', 'DLANGGU', '16'],
            ['110', 'PURI', '16'],
            ['120', 'TROWULAN', '16'],
            ['130', 'SOOKO', '16'],
            ['140', 'GEDEK', '16'],
            ['150', 'KEMLAGI', '16'],
            ['160', 'JETIS', '16'],
            ['170', 'DAWAR BLANDONG', '16'],
            ['010', '-', '76'],
            ['020', '-', '76'],
            ['021', '-', '76'],

        ];

        DB::table('kecamatan')->insert(
            array_map(fn($item) => [
                'kode_kecamatan' => $item[0],
                'nama_kecamatan' => $item[1],
                'id_kabupaten' => $item[2],
            ], $kecamatanData)
        );
    }
}