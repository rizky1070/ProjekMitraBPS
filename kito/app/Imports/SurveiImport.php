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
            'nama_survei' => $row['survei'] ?? null, 
            // 'id_desa' => $row['kode_desa'] ?? null,
            'id_kecamatan' => $row['kecamatan'] ?? null,
            // 'id_kabupaten' => $row['kode_kabupaten'] ?? null,
            // 'id_provinsi' => $row['kode_provinsi'] ?? null,
            'lokasi_survei' => $row['lokasi'] ?? null,
            'kro' => $row['kro'] ?? null,
            'jadwal_kegiatan' => isset($row['jadwal']) ? $this->parseDate($row['jadwal']) : null,
            'status_survei' => 1, 
            'tim' => $row['tim'] ?? null
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
