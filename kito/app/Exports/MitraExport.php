<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use App\Models\Mitra;

class MitraExport implements FromQuery, WithHeadings, WithMapping, WithColumnFormatting
{
    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function query()
    {
        return $this->query;
    }

    public function headings(): array
    {
        return [
            'Sobat ID',
            'Nama Mitra',
            'Email',
            'Nomor HP',
            'Kecamatan',
            'Alamat',
            'Tanggal Mulai Kontrak',
            'Tanggal Selesai Kontrak',
            'Jumlah Survei Diikuti',
            'Status'
        ];
    }

    public function map($mitra): array
    {
        return [
            $mitra->sobat_id,
            $mitra->nama_lengkap,
            $mitra->email_mitra,
            $mitra->no_hp_mitra,
            $mitra->kecamatan->nama_kecamatan ?? '-',
            $mitra->alamat_mitra,
            $mitra->tahun,
            $mitra->tahun_selesai,
            $mitra->mitra_survei_count,
            $mitra->mitra_survei_count > 0 ? 'Aktif' : 'Tidak Aktif'
        ];
    }
    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT, // Format kolom A (Sobat ID) sebagai teks
        ];
    }
}