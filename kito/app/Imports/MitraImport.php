<?php

namespace App\Imports;

use Illuminate\Support\Facades\Log;
use App\Models\Mitra;
use App\Models\Provinsi;
use App\Models\Kabupaten;
use App\Models\Kecamatan;
use App\Models\Desa;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class MitraImport implements ToModel, WithHeadingRow, WithValidation
{
    private $errors = [];
    
    public function model(array $row)
    {
        Log::info('Importing row: ', $row);
        
        // Set default values jika kosong
        $kodeProvinsi = empty($row['kode_provinsi']) ? 'P001' : $row['kode_provinsi'];
        $kodeKabupaten = empty($row['kode_kabupaten']) ? 'K001' : $row['kode_kabupaten'];
        
        // Cari provinsi
        $provinsi = Provinsi::where('kode_provinsi', $kodeProvinsi)->first();
        if (!$provinsi) {
            throw new \Exception("Kode provinsi {$kodeProvinsi} tidak ditemukan");
        }
        
        // Cari kabupaten
        $kabupaten = Kabupaten::where('kode_kabupaten', $kodeKabupaten)
            ->where('id_provinsi', $provinsi->id_provinsi)
            ->first();
        if (!$kabupaten) {
            throw new \Exception("Kode kabupaten {$kodeKabupaten} tidak ditemukan di provinsi {$provinsi->nama_provinsi}");
        }
        
        // Cari kecamatan
        $kecamatan = Kecamatan::where('kode_kecamatan', $row['kode_kecamatan'])
            ->where('id_kabupaten', $kabupaten->id_kabupaten)
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
        
        // Parse tanggal tahun
        $tanggal = $this->parseTanggal($row['tahun'] ?? null);
        $tahun = $tanggal->year;
        
        // Cek duplikasi sobat_id dalam tahun yang sama
        $existingMitra = Mitra::where('sobat_id', $row['sobat_id'])
            ->whereYear('tahun', $tahun)
            ->first();
            
        if ($existingMitra) {
            throw new \Exception("Sobat ID {$row['sobat_id']} sudah terdaftar pada tahun {$tahun}");
        }
        
        return new Mitra([
            'nama_lengkap' => $row['nama_lengkap'],
            'sobat_id' => $row['sobat_id'],
            'alamat_mitra' => $row['alamat_mitra'],
            'id_desa' => $desa->id_desa,
            'id_kecamatan' => $kecamatan->id_kecamatan,
            'id_kabupaten' => $kabupaten->id_kabupaten,
            'id_provinsi' => $provinsi->id_provinsi,
            'jenis_kelamin' => $row['jenis_kelamin'],
            'no_hp_mitra' => $row['no_hp_mitra'],
            'email_mitra' => $row['email_mitra'],
            'tahun' => $tanggal
        ]);
    }
    
    public function rules(): array
    {
        return [
            'nama_lengkap' => 'required|string|max:255',
            'sobat_id' => 'required|string|max:50',
            'alamat_mitra' => 'required|string',
            'kode_desa' => 'required|string',
            'kode_kecamatan' => 'required|string',
            'jenis_kelamin' => 'required|in:1,2',
            'no_hp_mitra' => 'required|string|max:20',
            'email_mitra' => 'required|email|max:255',
            'tahun' => 'nullable|date'
        ];
    }
    
    private function parseTanggal($tanggal)
    {
        try {
            // Jika kosong, gunakan tanggal hari ini
            if (empty($tanggal)) {
                return Carbon::now();
            }
            
            // Coba parse tanggal dari berbagai format
            return Carbon::parse($tanggal);
        } catch (\Exception $e) {
            Log::error("Gagal parsing tanggal: {$tanggal} - Error: " . $e->getMessage());
            throw new \Exception("Format tanggal tidak valid: {$tanggal}");
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