<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use App\Models\Mitra;
use Carbon\Carbon;

class MitraExport implements FromQuery, WithMapping, WithEvents
{
    protected $query;
    protected $filters;
    protected $totals;

    protected $headings = [
        'No',
        'Sobat ID',
        'Nama Mitra',
        'Email',
        'Nomor HP',
        'Provinsi',
        'Kabupaten',
        'Kecamatan',
        'Desa',
        'Alamat Lengkap',
        'Tanggal Mulai Kontrak',
        'Tanggal Selesai Kontrak',
        'Jumlah Survei Diikuti',
        'Nama Survei',
        'Total Honor',
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
    return $this->query->with(['provinsi', 'kabupaten', 'kecamatan', 'desa', 
        'survei' => function($query) {
            $query->withPivot('honor', 'vol')
                ->when(isset($this->filters['tahun']), function($q) {
                    $q->whereYear('bulan_dominan', $this->filters['tahun']);
                })
                ->when(isset($this->filters['bulan']), function($q) {
                    $monthNumber = Carbon::parse($this->filters['bulan'])->month;
                    $q->whereMonth('bulan_dominan', $monthNumber);
                });
        }]);
}

public function map($mitra): array
{
    static $count = 0;
    $count++;

    // Langsung gunakan survei yang sudah difilter dari query
    $jumlahSurvei = $mitra->survei->count();
    
    $namaSurvei = $mitra->survei->isNotEmpty() 
        ? $mitra->survei->pluck('nama_survei')->filter()->implode(', ') 
        : '-';
    
    $namaSurvei = empty(trim($namaSurvei)) ? '-' : $namaSurvei;

    $totalHonor = 0;
    foreach ($mitra->survei as $survei) {
        $honor = $survei->pivot->honor ?? 0;
        $vol = $survei->pivot->vol ?? 0;
        $totalHonor += $honor * $vol;
    }

    return [
        $count,
        $mitra->sobat_id,
        $mitra->nama_lengkap,
        $mitra->email_mitra ?? '-',
        $mitra->no_hp_mitra ?? '-',
        $mitra->provinsi->nama_provinsi ?? '-',
        $mitra->kabupaten->nama_kabupaten ?? '-',
        $mitra->kecamatan->nama_kecamatan ?? '-',
        $mitra->desa->nama_desa ?? '-',
        $mitra->alamat_mitra ?? '-',
        $mitra->tahun ? Carbon::parse($mitra->tahun)->format('d/m/Y') : '-',
        $mitra->tahun_selesai ? Carbon::parse($mitra->tahun_selesai)->format('d/m/Y') : '-',
        $jumlahSurvei,
        $namaSurvei,
        $totalHonor,
        $jumlahSurvei > 0 ? 'Aktif' : 'Tidak Aktif'
    ];
}

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function(BeforeSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $row = 1;

                // Judul Laporan
                $sheet->setCellValue('A'.$row, 'LAPORAN DATA MITRA');
                $sheet->mergeCells('A'.$row.':P'.$row);
                $sheet->getStyle('A'.$row)->getFont()
                    ->setBold(true)
                    ->setSize(14);
                $sheet->getStyle('A'.$row)->getAlignment()
                    ->setHorizontal('center');
                $row++;

                // Spasi kosong setelah judul
                $row++;

                // Tanggal Export
                $sheet->setCellValue('A'.$row, 'Tanggal Export: ' . Carbon::now()->format('d/m/Y H:i'));
                $sheet->mergeCells('A'.$row.':P'.$row);
                $sheet->getStyle('A'.$row)->getFont()->setItalic(true);
                $row++;

                // Spasi kosong setelah tanggal export
                $row++;

                // Informasi Filter
                if (!empty($this->filters)) {
                    $sheet->setCellValue('A'.$row, 'Filter yang digunakan:');
                    $sheet->mergeCells('A'.$row.':P'.$row);
                    $sheet->getStyle('A'.$row)->getFont()->setBold(true);
                    $row++;

                    foreach ($this->filters as $key => $value) {
                        $label = $this->getFilterLabel($key);
                        $sheet->setCellValue('A'.$row, $label.': '.$value);
                        $sheet->mergeCells('A'.$row.':P'.$row);
                        $row++;
                    }

                    // Spasi kosong setelah filter
                    $row++;
                }

                // Informasi Total
                $sheet->setCellValue('A'.$row, 'Total Mitra: '.$this->totals['totalMitra']);
                $sheet->mergeCells('A'.$row.':D'.$row);
                $sheet->getStyle('A'.$row)->getFont()->setBold(true);

                $sheet->setCellValue('E'.$row, 'Aktif: '.$this->totals['totalIkutSurvei']);
                $sheet->mergeCells('E'.$row.':H'.$row);
                $sheet->getStyle('E'.$row)->getFont()->setBold(true);

                $sheet->setCellValue('I'.$row, 'Tidak Aktif: '.$this->totals['totalTidakIkutSurvei']);
                $sheet->mergeCells('I'.$row.':L'.$row);
                $sheet->getStyle('I'.$row)->getFont()->setBold(true);

                $sheet->setCellValue('M'.$row, 'Total Honor: '.number_format($this->totals['totalHonor'], 0, ',', '.'));
                $sheet->mergeCells('M'.$row.':P'.$row);
                $sheet->getStyle('M'.$row)->getFont()->setBold(true);

                // Spasi kosong sebelum header
                $row += 2;

                // Header
                $sheet->fromArray($this->headings, null, 'A'.$row);

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
                $sheet->getStyle('A'.$row.':P'.$row)->applyFromArray($headerStyle);

                // Format kolom Total Honor
                $sheet->getStyle('O')->getNumberFormat()->setFormatCode('#,##0');

                // Set kolom auto-size
                foreach (range('A', 'P') as $column) {
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
            'kecamatan' => 'Kecamatan',
            'nama_lengkap' => 'Nama Mitra',
            'status_mitra' => 'Status Mitra'
        ];

        return $labels[$key] ?? ucfirst(str_replace('_', ' ', $key));
    }
}