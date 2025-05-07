<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KecamatanSeeder extends Seeder
{
    public function run()
{
    $kecamatanData = [
        ['010', 'JATIREJO'],
        ['020', 'GONDANG'],
        ['030', 'PACET'],
        ['040', 'TRAWAS'],
        ['050', 'NGORO'],
        ['060', 'PUNGGING'],
        ['070', 'KUTOREJO'],
        ['080', 'MOJOSARI'],
        ['090', 'BANGSAL'],
        ['091', 'MOJOANYAR'],
        ['100', 'DLANGGU'],
        ['110', 'PURI'],
        ['120', 'TROWULAN'],
        ['130', 'SOOKO'],
        ['140', 'GEDEK'],
        ['150', 'KEMLAGI'],
        ['160', 'JETIS'],
        ['170', 'DAWAR BLANDONG'],
        
    ];

    DB::table('kecamatan')->insert(
        array_map(fn($item) => [
            'id_kecamatan' => str_pad($item[0], 3, '0', STR_PAD_LEFT),
            'kode_kecamatan' => $item[0],
            'nama_kecamatan' => $item[1],
            'id_kabupaten' => 16
        ], $kecamatanData)
    );
}
}