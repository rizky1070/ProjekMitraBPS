<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Models\Mitra;
use Carbon\Carbon;

class MitraExport implements FromQuery, WithHeadings, WithMapping, WithColumnFormatting, WithEvents
{
    protected $query;
    protected $filters;
    protected $totals;

    public function __construct($query, $filters = [], $totals = [])
    {
        $this->query = $query;
        $this->filters = $filters;
        $this->totals = $totals;
    }

    public function query()
    {
        // Tambahkan ->get() untuk memastikan data terambil
        return $this->query->with(['provinsi', 'kabupaten', 'kecamatan', 'desa', 
            'survei' => function($query) {
                $query->withPivot('honor', 'vol')
                    ->when(isset($this->filters['tahun']), function($q) {
                        $q->whereYear('bulan_dominan', $this->filters['tahun']);
                    })
                    ->when(isset($this->filters['bulan']), function($q) {
                        $q->whereMonth('bulan_dominan', $this->filters['bulan']);
                    });
            }])
            ->withCount(['survei' => function($query) {
                $query->when(isset($this->filters['tahun']), function($q) {
                    $q->whereYear('bulan_dominan', $this->filters['tahun']);
                })
                ->when(isset($this->filters['bulan']), function($q) {
                    $q->whereMonth('bulan_dominan', $this->filters['bulan']);
                });
            }])
            ->get(); // Tambahkan ini untuk memastikan data terambil
    }

    public function headings(): array
    {
        return [
            'No', // Tambahkan kolom nomor
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
    }

    public function map($mitra): array
    {
        // Filter survei berdasarkan tahun/bulan jika ada
        $filteredSurvei = $mitra->survei->filter(function($survei) {
            $match = true;
            if (isset($this->filters['tahun'])) {
                $match = $match && (Carbon::parse($survei->bulan_dominan)->year == $this->filters['tahun']);
            }
            if (isset($this->filters['bulan'])) {
                $match = $match && (Carbon::parse($survei->bulan_dominan)->month == $this->filters['bulan']);
            }
            return $match;
        });

        $jumlahSurvei = $filteredSurvei->count();
        
        $namaSurvei = $filteredSurvei->isNotEmpty() 
            ? $filteredSurvei->pluck('nama_survei')->filter()->implode(', ') 
            : '-';
        
        $namaSurvei = empty(trim($namaSurvei)) ? '-' : $namaSurvei;

        $totalHonor = 0;
        foreach ($filteredSurvei as $survei) {
            $honor = $survei->pivot->honor ?? 0;
            $vol = $survei->pivot->vol ?? 0;
            $totalHonor += $honor * $vol;
        }

        return [
            '', // Nomor akan diisi di registerEvents
            $mitra->sobat_id,
            $mitra->nama_lengkap,
            $mitra->email_mitra ?? '-',
            $mitra->no_hp_mitra ?? '-',
            $mitra->provinsi->nama_provinsi ?? ($mitra->provinsi->kode_provinsi ?? '-'),
            $mitra->kabupaten->nama_kabupaten ?? ($mitra->kabupaten->kode_kabupaten ?? '-'),
            $mitra->kecamatan->nama_kecamatan ?? ($mitra->kecamatan->kode_kecamatan ?? '-'),
            $mitra->desa->nama_desa ?? ($mitra->desa->kode_desa ?? '-'),
            $mitra->alamat_mitra ?? '-',
            $mitra->tahun ? Carbon::parse($mitra->tahun)->format('d/m/Y') : '-',
            $mitra->tahun_selesai ? Carbon::parse($mitra->tahun_selesai)->format('d/m/Y') : '-',
            $jumlahSurvei,
            $namaSurvei,
            $totalHonor,
            $jumlahSurvei > 0 ? 'Aktif' : 'Tidak Aktif'
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_TEXT, // Sobat ID (sekarang kolom B karena nomor di A)
            'O' => '#,##0', // Total Honor (sekarang kolom O)
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Tambahkan informasi filter di atas header
                $row = 1;
                
                // Judul Laporan
                $sheet->setCellValue('A'.$row, 'LAPORAN DATA MITRA');
                $sheet->mergeCells('A'.$row.':P'.$row); // Diubah dari O ke P karena ada tambahan kolom
                $sheet->getStyle('A'.$row)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);
                $row++;
                
                // Tanggal Export
                $sheet->setCellValue('A'.$row, 'Tanggal Export: '.Carbon::now()->translatedFormat('d F Y H:i:s'));
                $sheet->mergeCells('A'.$row.':P'.$row); // Diubah dari O ke P
                $row++;

                // Informasi Filter
                $filterInfo = [];
                
                if (!empty($this->filters['tahun'])) {
                    $filterInfo[] = "Tahun: " . $this->filters['tahun'];
                }
                if (!empty($this->filters['bulan'])) {
                    $filterInfo[] = "Bulan: " . Carbon::create()->month($this->filters['bulan'])->translatedFormat('F');
                }
                if (!empty($this->filters['kecamatan'])) {
                    $filterInfo[] = "Kecamatan: " . $this->filters['kecamatan'];
                }
                if (!empty($this->filters['nama_lengkap'])) {
                    $filterInfo[] = "Nama Mitra: " . $this->filters['nama_lengkap'];
                }
                if (!empty($this->filters['status_mitra'])) {
                    $status = $this->filters['status_mitra'] == 'ikut' ? 'Ikut Survei' : 'Tidak Ikut Survei';
                    $filterInfo[] = "Status: " . $status;
                }
                
                if (!empty($filterInfo)) {
                    $sheet->setCellValue('A'.$row, 'Filter yang digunakan: ' . implode(', ', $filterInfo));
                    $sheet->mergeCells('A'.$row.':P'.$row); // Diubah dari O ke P
                    $row++;
                }
                
                $row++; // Spasi sebelum header
            
                // Set header row
                $headerRow = $row;
                $sheet->fromArray($this->headings(), null, 'A'.$headerRow);
                
                // Set data rows
                $data = $this->query->get()->map(function($item) {
                    return $this->map($item);
                })->toArray();
                
                if (!empty($data)) {
                    $sheet->fromArray($data, null, 'A'.($headerRow + 1));
                    
                    // Tambahkan nomor urut
                    $startRow = $headerRow + 1;
                    foreach (range($startRow, $startRow + count($data) - 1) as $rowNum) {
                        $sheet->setCellValue('A'.$rowNum, $rowNum - $headerRow);
                    }
                    
                    // Terapkan format number setelah data ditulis
                    $lastDataRow = $headerRow + count($data);
                    $sheet->getStyle('O'.($headerRow + 1).':O'.$lastDataRow) // Diubah dari N ke O
                        ->getNumberFormat()
                        ->setFormatCode('#,##0');
                }

                // Style untuk header
                $sheet->getStyle('A'.$headerRow.':P'.$headerRow)->applyFromArray([ // Diubah dari O ke P
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '4F81BD'],
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                ]);
                
                // Auto size columns - LAKUKAN SETELAH DATA DITULIS
                foreach(range('A','P') as $columnID) { // Diubah dari O ke P
                    $sheet->getColumnDimension($columnID)->setAutoSize(true);
                }
                
                // Tambahkan total di footer
                $lastRow = $sheet->getHighestRow();
                $footerRow = $lastRow + 2;
                
                // Total Mitra
                $sheet->setCellValue('A'.$footerRow, 'Total Mitra:');
                $sheet->setCellValue('B'.$footerRow, $this->totals['totalMitra']);
                $sheet->getStyle('A'.$footerRow.':B'.$footerRow)->applyFromArray([
                    'font' => ['bold' => true],
                ]);
                
                // Total Ikut Survei
                $sheet->setCellValue('E'.$footerRow, 'Total Ikut Survei:'); // Diubah dari D ke E
                $sheet->setCellValue('F'.$footerRow, $this->totals['totalIkutSurvei']); // Diubah dari E ke F
                
                // Total Tidak Ikut Survei
                $sheet->setCellValue('H'.$footerRow, 'Total Tidak Ikut Survei:'); // Diubah dari G ke H
                $sheet->setCellValue('I'.$footerRow, $this->totals['totalTidakIkutSurvei']); // Diubah dari H ke I
                
                // Total Honor
                $sheet->setCellValue('N'.$footerRow, 'Total Honor:'); // Diubah dari M ke N
                $sheet->setCellValue('O'.$footerRow, $this->totals['totalHonor']); // Diubah dari N ke O
                $sheet->getStyle('O'.$footerRow)->getNumberFormat()->setFormatCode('#,##0');
                
                // Style untuk footer
                $sheet->getStyle('A'.$footerRow.':P'.$footerRow)->applyFromArray([ // Diubah dari O ke P
                    'font' => ['bold' => true],
                ]);
            }
        ];
    }
}