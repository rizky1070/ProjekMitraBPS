<?php

namespace App\Imports;

use Illuminate\Support\Facades\Log;
use App\Models\Survei;
use App\Models\Provinsi;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Desa;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class SurveiImport implements ToModel, WithHeadingRow, WithValidation
{
    private $errors = [];
    
    public function model(array $row)
    {
        Log::info('Importing row: ', $row);
        
        // Cari kecamatan
        $kecamatan = Kecamatan::where('kode_kecamatan', $row['kode_kecamatan'])
            // ->where('id_kabupaten', $kabupaten->id_kabupaten)
            ->first();
        if (!$kecamatan) {
            throw new \Exception("Kode kecamatan {$row['kode_kecamatan']} tidak ditemukan di kabupaten {$kabupaten->nama_kabupaten}");
        }
        
        // Cari desa
        $desa = Desa::where('kode_desa', $row['kode_desa'])
            ->where('id_kecamatan', $kecamatan->id_kecamatan)
            ->first();
        if (!$desa) {
            throw new \Exception("Kode desa {$row['kode_desa']} tidak ditemukan di kecamatan {$kecamatan->nama_kecamatan}");
        }
        
        return new Survei([
            'nama_survei' => $row['nama_survei'], 
            'lokasi_survei' => $row['lokasi_survei'] ?? null,
            'id_desa' => $desa->id_desa,
            'id_kecamatan' => $kecamatan->id_kecamatan, 
            'id_kabupaten' => '1', //seharusnya 16
            'id_provinsi' => '3', //seharusnya 35
            'kro' => $row['kro'],
            'jadwal_kegiatan' => isset($row['jadwal']) ? $this->parseDate($row['jadwal']) : null,
            'status_survei' => 1, 
            'tim' => $row['tim']
        ]);
    }
    
    public function rules(): array
    {
        return [
            'nama_survei' => 'required|string',
            'kode_desa' => 'required|string',
            'kode_kecamatan' => 'required|string',
            'kro' => 'required|string',
            'tim' => 'required|string',
        ];
    }
    
    private function parseDate($date)
    {
        try {
            return Carbon::parse($date);
        } catch (\Exception $e) {
            return null;
        }
    }
    
    public function onError(\Throwable $e)
    {
        $this->errors[] = $e->getMessage();
    }
    
    public function getErrors()
    {
        return $this->errors;
    }
}