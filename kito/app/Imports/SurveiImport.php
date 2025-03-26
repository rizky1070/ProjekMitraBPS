<?php

namespace App\Imports;

use Illuminate\Support\Facades\Log;
use App\Models\Survei;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class SurveiImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        Log::info('Importing row: ', $row);
        return new Survei([
            'nama_survei' => $row['nama_survei'], 
            'lokasi_survei' => $row['lokasi_survei'] ?? null,
            'id_desa' => $row['kode_desa'],
            'id_kecamatan' => $row['kode_kecamatan'],
            'id_kabupaten' => $row['kode_kabupaten'] ?? '1', // default kabupaten 16
            'id_provinsi' => $row['kode_provinsi'] ?? '3', // default provinsi 35
            'kro' => $row['kro'],
            'jadwal_kegiatan' => isset($row['jadwal']) ? $this->parseDate($row['jadwal']) : null,
            'status_survei' => 1, 
            'tim' => $row['tim']
        ]);
    }

    private function parseDate($date)
    {
        try {
            return Carbon::parse($date);
        } catch (\Exception $e) {
            return null; // Jika format tanggal salah, jadikan null
        }
    }
}
