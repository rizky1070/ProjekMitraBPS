<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MitraSurveiSeeder extends Seeder
{
    public function run()
    {
        DB::table('mitra_survei')->insert([
            ['id_mitra' => 1, 'id_survei' => 1, 'posisi_mitra' => 'Posisi A'],
            ['id_mitra' => 2, 'id_survei' => 2, 'posisi_mitra' => 'Posisi B'],
            ['id_mitra' => 3, 'id_survei' => 3, 'posisi_mitra' => 'Posisi C'],
        ]);
    }
}
