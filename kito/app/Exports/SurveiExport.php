<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use App\Models\Survei;
use Carbon\Carbon;

class SurveiExport implements FromQuery, WithMapping, WithEvents
{
    protected $query;
    protected $filters;
    protected $totals;
    protected $headings = [
        'No',
        'Nama Survei',
        'Provinsi',
        'Kabupaten',
        'KRO',
        'Tim',
        'Tanggal Mulai Survei',
        'Tanggal Selesai Survei',
        'Jumlah Mitra',
        'Sobat ID Mitra',
        'Status'
    ];

    public function __construct($query, $filters = [], $totals = [])
    {
        $this->query = $query;
        $this->filters = $filters;
        $this->totals = $totals;
    }

    public function query()
    {
        // Tambahkan eager loading untuk performa yang lebih baik
        return $this->query->with(['mitraSurveis.mitra', 'provinsi', 'kabupaten']);
    }

    public function map($survei): array
    {
        static $count = 0;
        $count++;

        $jumlahResponden = $survei->total_mitra ?? 0;

        // Perbaikan: akses sobat_id melalui relasi mitra
        $namaResponden = $survei->mitraSurveis->isNotEmpty()
            ? $survei->mitraSurveis->map(function ($mitraSurvei) {
                return $mitraSurvei->mitra->sobat_id ?? null;
            })->filter()->implode(', ')
            : '-';

        $namaResponden = empty(trim($namaResponden)) ? '-' : $namaResponden;

        return [
            $count,
            $survei->nama_survei,
            $survei->provinsi->kode_provinsi ?? '-',
            $survei->kabupaten->kode_kabupaten ?? '-',
            $survei->kro,
            $survei->tim,
            Carbon::parse($survei->jadwal_kegiatan)->format('d/m/Y'),
            Carbon::parse($survei->jadwal_berakhir_kegiatan)->format('d/m/Y'),
            $jumlahResponden,
            $namaResponden,
            $jumlahResponden > 0 ? 'Aktif Di Ikuti Mitra' : 'Tidak Aktif Di Ikuti Mitra'
        ];
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $row = 1;

                // Judul Laporan
                $sheet->setCellValue('A' . $row, 'LAPORAN DATA SURVEI');
                $sheet->mergeCells('A' . $row . ':K' . $row);
                $sheet->getStyle('A' . $row)->getFont()
                    ->setBold(true)
                    ->setSize(14);
                $sheet->getStyle('A' . $row)->getAlignment()
                    ->setHorizontal('center');
                $row++;

                // Spasi kosong setelah judul
                $row++;

                // Tanggal Export
                $sheet->setCellValue('A' . $row, 'Tanggal Export: ' . Carbon::now()->format('d/m/Y H:i'));
                $sheet->mergeCells('A' . $row . ':K' . $row);
                $sheet->getStyle('A' . $row)->getFont()->setItalic(true);
                $row++;

                // Spasi kosong setelah tanggal export
                $row++;

                // Informasi Filter
                if (!empty($this->filters)) {
                    $sheet->setCellValue('A' . $row, 'Filter yang digunakan:');
                    $sheet->mergeCells('A' . $row . ':K' . $row);
                    $sheet->getStyle('A' . $row)->getFont()->setBold(true);
                    $row++;

                    foreach ($this->filters as $key => $value) {
                        $label = $this->getFilterLabel($key);
                        $sheet->setCellValue('A' . $row, $label . ': ' . $value);
                        $sheet->mergeCells('A' . $row . ':K' . $row);
                        $row++;
                    }

                    // Spasi kosong setelah filter
                    $row++;
                }

                // Informasi Total
                $sheet->setCellValue('A' . $row, 'Total Survei: ' . $this->totals['totalSurvei']);
                $sheet->getStyle('A' . $row)->getFont()->setBold(true);
                $row++;

                $sheet->setCellValue('A' . $row, 'Aktif Di Ikuti Mitra: ' . $this->totals['totalSurveiAktif']);
                $sheet->getStyle('A' . $row)->getFont()->setBold(true);
                $row++;

                $sheet->setCellValue('A' . $row, 'Tidak Aktif Di Ikuti Mitra: ' . $this->totals['totalSurveiTidakAktif']);
                $sheet->getStyle('A' . $row)->getFont()->setBold(true);
                $row++;

                // Spasi kosong sebelum header
                $row += 2;

                // Header
                $sheet->fromArray($this->headings, null, 'A' . $row);

                // Style Header
                $headerStyle = [
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFD3D3D3']
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                ];

                $sheet->getStyle('A' . $row . ':K' . $row)->applyFromArray($headerStyle);

                // Set kolom auto-size
                foreach (range('A', 'K') as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }
            },
        ];
    }

    protected function getFilterLabel($key)
    {
        $labels = [
            'tahun' => 'Tahun',
            'bulan' => 'Bulan',
            'nama_survei' => 'Nama Survei',
            'status_survei' => 'Status Survei'
        ];

        return $labels[$key] ?? ucfirst(str_replace('_', ' ', $key));
    }
}
