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
        'Jenis Kelamin',
        'Tanggal Mulai Kontrak',
        'Tanggal Selesai Kontrak',
        'Jumlah Survei Diikuti',
        'Nama Survei',
        'Total Honor',
        'Status Pekerjaan',
        'Detail Pekerjaan',
        'Status',
    ];
    protected $isMonthFilterActive = false;

    public function __construct($query, $filters = [], $totals = [])
    {
        $this->query = $query;
        $this->filters = $filters;
        $this->totals = $totals;

        if (!empty($this->filters['bulan'])) {
            $this->isMonthFilterActive = true;
            $namaSurveiIndex = array_search('Nama Survei', $this->headings);
            if ($namaSurveiIndex !== false) {
                array_splice($this->headings, $namaSurveiIndex + 1, 0, ['Nilai', 'Catatan']);
            } else {
                $this->headings[] = 'Nilai';
                $this->headings[] = 'Catatan';
            }
        }
    }

    public function query()
    {
        return $this->query->with([
            'provinsi',
            'kabupaten',
            'kecamatan',
            'desa',
            'mitraSurveis' => function ($query) {
                $query->with(['survei', 'posisiMitra'])
                    ->when(isset($this->filters['tahun']), function ($q) {
                        $q->whereHas('survei', function ($sq) {
                            $sq->whereYear('bulan_dominan', $this->filters['tahun']);
                        });
                    })
                    ->when(isset($this->filters['bulan']), function ($q) {
                        $monthNumber = is_numeric($this->filters['bulan'])
                            ? $this->filters['bulan']
                            : Carbon::parse($this->filters['bulan'])->month;
                        $q->whereHas('survei', function ($sq) use ($monthNumber) {
                            $sq->whereMonth('bulan_dominan', $monthNumber);
                        });
                    });
            }
        ]);
    }

    public function map($mitra): array
    {
        static $count = 0;
        $count++;

        $jumlahSurvei = $mitra->mitraSurveis->count();

        $namaSurvei = $mitra->mitraSurveis->isNotEmpty()
            ? $mitra->mitraSurveis->map(function ($mitraSurvei) {
                return $mitraSurvei->survei ? $mitraSurvei->survei->nama_survei : null;
            })->filter()->unique()->implode(', ')
            : '-';
        $namaSurvei = empty(trim($namaSurvei)) ? '-' : $namaSurvei;

        $totalHonor = 0;
        foreach ($mitra->mitraSurveis as $mitraSurvei) {
            $rateHonor = $mitraSurvei->posisiMitra ? $mitraSurvei->posisiMitra->rate_honor : 0;
            $vol = $mitraSurvei->vol ?? 0;
            $totalHonor += $rateHonor * $vol;
        }

        $statusPekerjaan = $mitra->status_pekerjaan == 0 ? 'Bisa Ikut Survei' : 'Tidak Bisa Ikut Survei';

        $rowData = [
            $count,
            ' ' . $mitra->sobat_id,
            $mitra->nama_lengkap,
            $mitra->email_mitra ?? '-',
            ' ' . ($mitra->no_hp_mitra ?? ''),
            $mitra->provinsi->nama_provinsi ?? '-',
            $mitra->kabupaten->nama_kabupaten ?? '-',
            $mitra->kecamatan->nama_kecamatan ?? '-',
            $mitra->desa->nama_desa ?? '-',
            $mitra->alamat_mitra ?? '-',
            $mitra->jenis_kelamin == '1' ? 'Lk' : ($mitra->jenis_kelamin == '2' ? 'Pr' : '-'),
            $mitra->tahun ? Carbon::parse($mitra->tahun)->format('d/m/Y') : '-',
            $mitra->tahun_selesai ? Carbon::parse($mitra->tahun_selesai)->format('d/m/Y') : '-',
            $jumlahSurvei,
            $namaSurvei,
        ];

        if ($this->isMonthFilterActive) {
            // [MODIFIED] Menggunakan koma dan spasi sebagai pemisah
            if ($mitra->mitraSurveis->isNotEmpty()) {
                $nilai = $mitra->mitraSurveis->map(function ($ms) {
                    return $ms->nilai ?? '-';
                })->implode(", "); // Menggunakan koma sebagai pemisah

                $catatan = $mitra->mitraSurveis->map(function ($ms) {
                    return $ms->catatan ?? '-';
                })->implode(", "); // Menggunakan koma sebagai pemisah
            } else {
                $nilai = '-';
                $catatan = '-';
            }
            $rowData[] = $nilai;
            $rowData[] = $catatan;
        }

        $rowData = array_merge($rowData, [
            $totalHonor,
            $statusPekerjaan,
            $mitra->detail_pekerjaan ?? '-',
            $jumlahSurvei > 0 ? 'Aktif Mengikuti Survei' : 'Tidak Aktif Mengikuti Survei',
        ]);

        return $rowData;
    }

    private function formatPhoneNumber(?string $number): string
    {
        if (empty($number)) {
            return '-';
        }

        if (!ctype_digit($number)) {
            return '="' . str_replace('"', '""', $number) . '"';
        }

        return $number;
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $row = 1;

                $lastColumn = $this->isMonthFilterActive ? 'U' : 'S';

                // ... (Kode untuk Judul, Filter, dan Total tetap sama)
                $sheet->setCellValue('A' . $row, 'LAPORAN DATA MITRA');
                $sheet->mergeCells('A' . $row . ':' . $lastColumn . $row);
                $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $row++;
                $row++;
                $sheet->setCellValue('A' . $row, 'Tanggal Export: ' . Carbon::now()->format('d/m/Y H:i'));
                $sheet->mergeCells('A' . $row . ':' . $lastColumn . $row);
                $sheet->getStyle('A' . $row)->getFont()->setItalic(true);
                $row++;
                $row++;
                if (!empty($this->filters)) {
                    $sheet->setCellValue('A' . $row, 'Filter yang digunakan:');
                    $sheet->mergeCells('A' . $row . ':' . $lastColumn . $row);
                    $sheet->getStyle('A' . $row)->getFont()->setBold(true);
                    $row++;
                    foreach ($this->filters as $key => $value) {
                        $label = $this->getFilterLabel($key);
                        $sheet->setCellValue('A' . $row, $label . ': ' . $value);
                        $sheet->mergeCells('A' . $row . ':' . $lastColumn . $row);
                        $row++;
                    }
                    $row++;
                }
                $summaryStartRow = $row;
                $sheet->setCellValue('A' . $row++, 'Total Mitra: ' . $this->totals['totalMitra']);
                $sheet->setCellValue('A' . $row++, 'Aktif Mengikuti Survei: ' . $this->totals['totalIkutSurvei']);
                $sheet->setCellValue('A' . $row++, 'Tidak Aktif Mengikuti Survei: ' . $this->totals['totalTidakIkutSurvei']);
                $sheet->setCellValue('A' . $row++, 'Bisa Ikut Survei: ' . $this->totals['totalBisaIkutSurvei']);
                $sheet->setCellValue('A' . $row++, 'Tidak Bisa Ikut Survei: ' . $this->totals['totalTidakBisaIkutSurvei']);
                $sheet->setCellValue('A' . $row++, 'Total Honor: ' . number_format($this->totals['totalHonor'], 0, ',', '.'));
                $summaryEndRow = $row - 1;
                $sheet->getStyle('A' . $summaryStartRow . ':A' . $summaryEndRow)->getFont()->setBold(true);
                $row += 2;
                // ... (Akhir dari kode yang sama)


                $headerRow = $row;
                $sheet->fromArray($this->headings, null, 'A' . $headerRow);

                $headerStyle = [
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFD3D3D3']],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN,],],
                ];
                $sheet->getStyle('A' . $headerRow . ':' . $lastColumn . $headerRow)->applyFromArray($headerStyle);

                if ($this->isMonthFilterActive) {
                    $nilaiColumn = 'P';
                    $catatanColumn = 'Q';
                    $honorColumn = 'R';

                    // Pengaturan wrap text tidak lagi krusial, tapi tidak masalah jika tetap ada
                    $sheet->getStyle($nilaiColumn . ($headerRow + 1) . ':' . $nilaiColumn . ($sheet->getHighestRow()))
                        ->getAlignment()->setWrapText(true);
                    $sheet->getStyle($catatanColumn . ($headerRow + 1) . ':' . $catatanColumn . ($sheet->getHighestRow()))
                        ->getAlignment()->setWrapText(true);
                } else {
                    $honorColumn = 'P';
                }

                $sheet->getStyle($honorColumn)->getNumberFormat()->setFormatCode('#,##0');

                foreach (range('A', $lastColumn) as $column) {
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
            'status_mitra' => 'Status Mitra',
            'status_pekerjaan' => 'Status Pekerjaan'
        ];
        return $labels[$key] ?? ucfirst(str_replace('_', ' ', $key));
    }
}
