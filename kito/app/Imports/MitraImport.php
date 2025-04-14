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

class MitraImport implements ToModel, WithHeadingRow, WithValidation
{
    private $errors = [];
    
    public function model(array $row)
    {
        // Skip baris yang benar-benar kosong
        if (empty($row['sobat_id']) && empty($row['nama_lengkap']) && empty($row['alamat_mitra'])) {
            return null;
        }
        
        // Validasi minimal untuk baris yang dianggap tidak kosong
        if (empty($row['sobat_id'])) {
            throw new \Exception("Baris tidak valid: sobat_id harus diisi");
        }
        
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
            throw new \Exception("Kabupaten default (kode: 16) tidak ditemukan di provinsi {$provinsi->nama_provinsi}.");
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
        
        // Parse tanggal tahun
        $tahunMulai = $this->parseTanggal($row['tahun'] ?? null);
        $tahunSelesai = $this->parseTanggal($row['tahun_selesai'] ?? null);

        // Cek duplikasi sobat_id dalam bulan dan tahun yang sama
        $existingMitra = Mitra::where('sobat_id', $row['sobat_id'])
            ->whereMonth('tahun', $tahunMulai->month)
            ->whereYear('tahun', $tahunMulai->year)
            ->first();

        if ($existingMitra) {
            throw new \Exception("Sobat ID {$row['sobat_id']} sudah terdaftar pada bulan {$tahunMulai->month} tahun {$tahunMulai->year}");
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
            'tahun' => $tahunMulai,
            'tahun_selesai' => $tahunSelesai
        ]);
    }
    
    public function rules(): array
    {
        return [
            'sobat_id' => 'required|string|max:12',
            'nama_lengkap' => 'required|string|max:255',
            'alamat_mitra' => 'required|string',
            'kode_desa' => 'required|string|max:3',
            'kode_kecamatan' => 'required|string|max:3',
            'jenis_kelamin' => 'required|in:1,2',
            'no_hp_mitra' => 'required|string|max:20',
            'email_mitra' => 'required|email|max:255',
            'tahun' => 'required|date',
            'tahun_selesai' => 'required|date'
        ];
    }

    private function parseTanggal($tanggal)
    {
        try {
            // Jika kosong, return null
            if (empty($tanggal)) {
                return null;
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