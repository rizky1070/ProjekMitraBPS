<?php

namespace App\Exports;

use App\Models\Survei;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithCustomStartCell; // 1. IMPORT CONCERN BARU

// 2. IMPLEMENTASIKAN CONCERN BARU
class SurveiDetailExport implements FromCollection, WithMapping, WithEvents, WithCustomStartCell
{
    protected $survey;
    protected $rowNumber = 0;
    protected $headings = [
        'No',
        'Nama Mitra',
        'Domisili',
        'Posisi',
        'Vol',
        'Rate Honor',
    ];
    // Menentukan jumlah baris yang digunakan untuk header, spasi, dan info
    protected $headerRowCount = 11;

    public function __construct(Survei $survey)
    {
        $this->survey = $survey;
    }

    public function collection()
    {
        return $this->survey->mitraSurveis()->with('mitra.kecamatan', 'posisiMitra')->get();
    }

    public function map($mitraSurvei): array
    {
        $this->rowNumber++;
        return [
            $this->rowNumber,
            $mitraSurvei->mitra->nama_lengkap ?? 'N/A',
            $mitraSurvei->mitra->kecamatan->nama_kecamatan ?? 'N/A',
            $mitraSurvei->posisiMitra->nama_posisi ?? 'N/A',
            $mitraSurvei->vol,
            $mitraSurvei->rate_honor,
        ];
    }

    // 3. TAMBAHKAN METHOD INI
    /**
     * Menentukan sel di mana data tabel akan mulai ditulis.
     * @return string
     */
    public function startCell(): string
    {
        // Data akan mulai ditulis pada baris setelah header tabel.
        // Jika header tabel ada di baris 11, data dimulai di A12.
        return 'A' . ($this->headerRowCount + 1);
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $row = 1;
                $lastColumn = 'F';

                Carbon::setLocale('id');

                // Judul Laporan Utama
                $sheet->setCellValue('A' . $row, 'DETAIL INFORMASI SURVEI');
                $sheet->mergeCells('A' . $row . ':' . $lastColumn . $row);
                $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $row += 2;

                // Tanggal Export
                $sheet->setCellValue('A' . $row, 'Tanggal Export: ' . Carbon::now()->translatedFormat('d F Y H:i'));
                $sheet->mergeCells('A' . $row . ':' . $lastColumn . $row);
                $sheet->getStyle('A' . $row)->getFont()->setItalic(true);
                $row += 2;

                // Informasi Detail Survei
                $jadwalMulai = Carbon::parse($this->survey->jadwal_kegiatan)->translatedFormat('j F Y');
                $jadwalSelesai = Carbon::parse($this->survey->jadwal_berakhir_kegiatan)->translatedFormat('j F Y');

                $sheet->setCellValue('A' . $row, 'Nama Survei:')->getStyle('A' . $row)->getFont()->setBold(true);
                $sheet->setCellValue('B' . $row, $this->survey->nama_survei);
                $row++;

                $sheet->setCellValue('A' . $row, 'Jadwal Kegiatan:')->getStyle('A' . $row)->getFont()->setBold(true);
                $sheet->setCellValue('B' . $row, $jadwalMulai . ' - ' . $jadwalSelesai);
                $row++;

                $sheet->setCellValue('A' . $row, 'Tim:')->getStyle('A' . $row)->getFont()->setBold(true);
                $sheet->setCellValue('B' . $row, $this->survey->tim);
                $row++;

                $sheet->setCellValue('A' . $row, 'KRO:')->getStyle('A' . $row)->getFont()->setBold(true);
                $sheet->setCellValue('B' . $row, $this->survey->kro);
                $row++;

                $sheet->setCellValue('A' . $row, 'Jumlah Mitra:')->getStyle('A' . $row)->getFont()->setBold(true);
                $sheet->setCellValue('B' . $row, $this->survey->mitraSurveis()->count() . ' Orang');
                $row += 2;

                // Header Tabel
                // Pastikan $headerRow sesuai dengan nilai di properti $headerRowCount
                $headerRow = $this->headerRowCount;
                $sheet->fromArray($this->headings, null, 'A' . $headerRow);

                $headerStyle = [
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFD3D3D3']],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                ];
                $sheet->getStyle('A' . $headerRow . ':' . $lastColumn . $headerRow)->applyFromArray($headerStyle);
                $sheet->getRowDimension($headerRow)->setRowHeight(20);

                // Formatting Kolom
                $honorColumnIndex = array_search('Rate Honor', $this->headings);
                if ($honorColumnIndex !== false) {
                    $honorColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($honorColumnIndex + 1);
                    $lastDataRow = $headerRow + $this->survey->mitraSurveis()->count();
                    $sheet->getStyle($honorColumn . ($headerRow + 1) . ':' . $honorColumn . $lastDataRow)
                        ->getNumberFormat()
                        ->setFormatCode('#,##0');
                }

                // Auto-size semua kolom
                foreach (range('A', $lastColumn) as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }
            },
        ];
    }
}
