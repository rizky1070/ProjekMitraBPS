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

class SurveiImport implements ToModel, WithHeadingRow, WithValidation
{
    private $errors = [];
    
    public function model(array $row)
    {
        Log::info('Importing row: ', $row);
        
        // Tetapkan kode provinsi dan kabupaten secara otomatis
        $kodeProvinsi = '35';  // Default provinsi
        $kodeKabupaten = '16'; // Default kabupaten
        
        // Cari provinsi
        $provinsi = Provinsi::where('id_provinsi', $kodeProvinsi)->first();
        if (!$provinsi) {
            throw new \Exception("Provinsi default (kode: 35) tidak ditemukan di database.");
        }
        
        // Cari kabupaten
        $kabupaten = Kabupaten::where('id_kabupaten', $kodeKabupaten)
            ->where('id_provinsi', $provinsi->id_provinsi)
            ->first();
        if (!$kabupaten) {
            throw new \Exception("Kabupaten default (kode: 16) tidak ditemukan di provinsi {$provinsi->nama}.");
        }
        
        // Cari kecamatan (harus diisi di Excel)
        $kecamatan = Kecamatan::where('kode_kecamatan', $row['kode_kecamatan'])
            ->where('id_kabupaten', $kabupaten->id_kabupaten)
            ->first();
        if (!$kecamatan) {
            throw new \Exception("Kode kecamatan {$row['kode_kecamatan']} tidak ditemukan di kabupaten {$kabupaten->nama_kabupaten}.");
        }
        
        // Cari desa (harus diisi di Excel)
        $desa = Desa::where('kode_desa', $row['kode_desa'])
            ->where('id_kecamatan', $kecamatan->id_kecamatan)
            ->first();
        if (!$desa) {
            throw new \Exception("Kode desa {$row['kode_desa']} tidak ditemukan di kecamatan {$kecamatan->nama_kecamatan}.");
        }
        
        return new Survei([
            'nama_survei' => $row['nama_survei'], 
            'lokasi_survei' => $row['lokasi_survei'] ?? null,
            'id_desa' => $desa->id_desa,
            'id_kecamatan' => $kecamatan->id_kecamatan,
            'id_kabupaten' => $kabupaten->id_kabupaten,
            'id_provinsi' => $provinsi->id_provinsi,
            'kro' => $row['kro'],
            'jadwal_kegiatan' => isset($row['jadwal']) ? $this->parseDate($row['jadwal']) : null,
            'jadwal_berakhir_kegiatan' => isset($row['jadwal_berakhir']) ? $this->parseDate($row['jadwal_berakhir']) : null,
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
            'lokasi_survei' => 'required|string',
            'jadwal' => 'required', // Format: 2024-01-01
            'jadwal_berakhir' => 'required|after:jadwal', // Harus setelah jadwal
            // Tidak perlu validasi kode_provinsi & kode_kabupaten karena sudah otomatis
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