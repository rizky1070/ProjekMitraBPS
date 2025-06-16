<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;

class MitraPerBulanExport implements FromCollection, WithMapping, WithEvents
{
    protected $data;
    protected $monthHeaders;
    protected $filters;
    protected $totals;
    protected $headings;

    /**
     * @param array $data Data mitra yang sudah diproses
     * @param array $monthHeaders Header untuk setiap bulan (e.g., [1 => 'Januari', 2 => 'Februari'])
     * @param array $filters Filter yang aktif untuk ditampilkan di report
     * @param array $totals Data ringkasan total (total mitra, dll)
     */
    public function __construct(array $data, array $monthHeaders, array $filters = [], array $totals = [])
    {
        $this->data = new Collection($data);
        $this->monthHeaders = $monthHeaders;
        $this->filters = $filters;
        $this->totals = $totals;

        // Membangun header tabel, tambahkan 'Sobat ID'
        $this->headings = ['NO', 'Sobat ID', 'Nama Mitra'];
        foreach ($this->monthHeaders as $monthName) {
            $this->headings[] = $monthName;
        }
        $this->headings[] = 'Total Honor';
    }

    /**
     * @return Collection
     */
    public function collection(): Collection
    {
        return $this->data;
    }

    /**
     * Memetakan setiap baris data ke format yang diinginkan di Excel.
     *
     * @param mixed $row
     * @return array
     */
    public function map($row): array
    {
        static $count = 0;
        $count++;

        // Baris data yang akan diexport, tambahkan sobat_id
        $mappedRow = [
            $count,
            ' ' . ($row['sobat_id'] ?? '-'), // Tambahkan spasi agar dianggap teks
            $row['nama_mitra'],
        ];

        // Menambahkan data honor per bulan ke baris
        foreach ($row['honors'] as $honor) {
            $mappedRow[] = $honor > 0 ? $honor : 0;
        }

        // Menambahkan total honor tahunan di akhir baris
        $mappedRow[] = $row['total'] > 0 ? $row['total'] : 0;

        return $mappedRow;
    }

    /**
     * Mendaftarkan event untuk memformat sheet sebelum data diisi.
     *
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $row = 1;

                $lastColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($this->headings));

                // Judul Laporan
                $sheet->setCellValue('A' . $row, 'LAPORAN HONOR MITRA PER BULAN');
                $sheet->mergeCells('A' . $row . ':' . $lastColumn . $row);
                $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $row += 2;

                // Tanggal Export
                $sheet->setCellValue('A' . $row, 'Tanggal Export: ' . Carbon::now()->format('d/m/Y H:i'));
                $sheet->mergeCells('A' . $row . ':' . $lastColumn . $row);
                $sheet->getStyle('A' . $row)->getFont()->setItalic(true);
                $row += 2;

                // Informasi Filter
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

                // Ringkasan Total
                if (!empty($this->totals)) {
                    $summaryStartRow = $row;
                    $sheet->setCellValue('A' . $row++, 'Total Mitra: ' . ($this->totals['totalMitra'] ?? 0));
                    $sheet->setCellValue('A' . $row++, 'Total Mitra Laki-laki: ' . ($this->totals['totalLaki'] ?? 0));
                    $sheet->setCellValue('A' . $row++, 'Total Mitra Perempuan: ' . ($this->totals['totalPerempuan'] ?? 0));
                    $sheet->setCellValue('A' . $row++, 'Aktif Mengikuti Survei (Tahunan): ' . ($this->totals['totalIkutSurvei'] ?? 0));
                    $sheet->setCellValue('A' . $row++, 'Tidak Aktif Mengikuti Survei (Tahunan): ' . ($this->totals['totalTidakIkutSurvei'] ?? 0));
                    $sheet->setCellValue('A' . $row++, 'Bisa Ikut Survei: ' . ($this->totals['totalBisaIkutSurvei'] ?? 0));
                    $sheet->setCellValue('A' . $row++, 'Tidak Bisa Ikut Survei: ' . ($this->totals['totalTidakBisaIkutSurvei'] ?? 0));
                    $sheet->setCellValue('A' . $row++, 'Total Honor Keseluruhan: ' . number_format($this->totals['totalHonor'] ?? 0, 0, ',', '.'));

                    $sheet->getStyle('A' . $summaryStartRow . ':A' . ($row - 1))->getFont()->setBold(true);
                    $row += 2;
                }

                // Header Tabel
                $headerRow = $row;
                $sheet->fromArray($this->headings, null, 'A' . $headerRow);

                $headerStyle = [
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFD3D3D3']],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'horizontal' => Alignment::HORIZONTAL_CENTER],
                ];
                $sheet->getStyle('A' . $headerRow . ':' . $lastColumn . $headerRow)->applyFromArray($headerStyle);
                $sheet->getRowDimension($headerRow)->setRowHeight(20);

                // Format Kolom Honor (dimulai dari kolom 'D' sekarang)
                $honorStartColumn = 'D';
                for ($col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($honorStartColumn); $col <= count($this->headings); $col++) {
                    $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                    $sheet->getStyle($columnLetter)->getNumberFormat()->setFormatCode('#,##0');
                }

                // Auto-size semua kolom
                foreach (range('A', $lastColumn) as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }
            },
        ];
    }

    protected function getFilterLabel(string $key): string
    {
        $labels = [
            'Tahun' => 'Tahun',
            'tahun' => 'Tahun',
            'Kecamatan' => 'Kecamatan',
            'kecamatan' => 'Kecamatan',
            'Nama Mitra' => 'Nama Mitra',
            'nama_lengkap' => 'Nama Mitra',
            'Status Pekerjaan' => 'Status Pekerjaan',
            'status_pekerjaan' => 'Status Pekerjaan',
            'Jenis Kelamin' => 'Jenis Kelamin',
            'jenis_kelamin' => 'Jenis Kelamin',
            'Status Partisipasi Tahunan' => 'Status Partisipasi (Tahunan)',
            'Partisipasi Tahunan > 1 Survei' => 'Partisipasi > 1 Survei (Tahunan)',
            'Honor Tahunan > 4 Juta' => 'Honor > 4 Juta (Tahunan)',
        ];
        return $labels[$key] ?? ucfirst(str_replace('_', ' ', $key));
    }
}
