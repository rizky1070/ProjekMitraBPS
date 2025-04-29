<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use App\Models\Survei;
use App\Models\MitraSurvei;

class SurveiExport implements FromQuery, WithHeadings, WithMapping
{
    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function query()
    {
        return $this->query->withCount('mitra')->with('mitra');
    }

    public function headings(): array
    {
        return [
            'Nama Survei',
            'Provinsi',
            'Kabupaten',
            'Kecamatan',
            'Desa',
            'Lokasi Survei',
            'KRO',
            'Tim',
            'Tanggal Mulai Survei',
            'Tanggal Selesai Survei',
            'Jumlah Responden',
            'Sobat ID Responden',
            'Status'
        ];
    }

    public function map($survei): array
    {
        // Handle ketika tidak ada mitra
        $jumlahResponden = $survei->mitra_count ?? 0;
        
        // Ambil nama-nama mitra/responden, beri '-' jika kosong
        $namaResponden = $survei->mitra->isNotEmpty() 
            ? $survei->mitra->pluck('sobat_id')->filter()->implode(', ') 
            : '-';
        
        // Pastikan tidak ada string kosong jika nama_lengkap null
        $namaResponden = empty(trim($namaResponden)) ? '-' : $namaResponden;
        
        return [
            $survei->nama_survei,
            $survei->provinsi->kode_provinsi?? '-',
            $survei->kabupaten->kode_kabupaten?? '-',
            $survei->kecamatan->kode_kecamatan?? '-',
            $survei->desa->kode_desa?? '-',
            $survei->lokasi_survei,
            $survei->kro,
            $survei->tim,
            $survei->jadwal_kegiatan,
            $survei->jadwal_berakhir_kegiatan,
            $jumlahResponden, // Jumlah responden (0 jika kosong)
            $namaResponden,   // Nama responden ('-' jika kosong)
            $jumlahResponden > 0 ? 'Aktif' : 'Tidak Aktif'
        ];
    }
}