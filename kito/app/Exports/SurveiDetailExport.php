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
use Maatwebsite\Excel\Concerns\WithCustomStartCell;

class SurveiDetailExport implements FromCollection, WithMapping, WithEvents, WithCustomStartCell
{
    protected $survey;
    protected $rowNumber = 0;
    // 1. TAMBAHKAN KOLOM 'SOBAT ID' DAN 'TOTAL HONOR'
    protected $headings = [
        'No',
        'Sobat ID', // DITAMBAHKAN
        'Nama Mitra',
        'Kecamatan',
        'Posisi',
        'Vol',
        'Rate Honor',
        'Total Honor', // DITAMBAHKAN
    ];
    protected $headerRowCount = 11;

    public function __construct(Survei $survey)
    {
        $this->survey = $survey;
    }

    public function collection()
    {
        // Relasi `mitra` sudah dimuat, jadi `sobat_id` akan tersedia
        return $this->survey->mitraSurveis()->with('mitra.kecamatan', 'posisiMitra')->get();
    }

    /**
     * @param mixed $mitraSurvei Relasi pivot antara mitra dan survei
     * @return array
     */
    public function map($mitraSurvei): array
    {
        $this->rowNumber++;

        // 2. HITUNG TOTAL HONOR
        $totalHonor = ($mitraSurvei->vol ?? 0) * ($mitraSurvei->rate_honor ?? 0);

        return [
            $this->rowNumber,
            // Tambahkan spasi di depan untuk memastikan formatnya teks
            ' ' . ($mitraSurvei->mitra->sobat_id ?? 'N/A'), // DITAMBAHKAN
            $mitraSurvei->mitra->nama_lengkap ?? 'N/A',
            $mitraSurvei->mitra->kecamatan->nama_kecamatan ?? 'N/A',
            $mitraSurvei->posisiMitra->nama_posisi ?? 'N/A',
            $mitraSurvei->vol,
            $mitraSurvei->rate_honor,
            $totalHonor, // DITAMBAHKAN
        ];
    }

    public function startCell(): string
    {
        return 'A' . ($this->headerRowCount + 1);
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $row = 1;
                // 3. UBAH KOLOM TERAKHIR MENJADI 'H'
                $lastColumn = 'H';

                Carbon::setLocale('id');

                // Judul Laporan Utama (disesuaikan dengan lastColumn baru)
                $sheet->setCellValue('A' . $row, 'DETAIL INFORMASI SURVEI');
                $sheet->mergeCells('A' . $row . ':' . $lastColumn . $row);
                $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $row += 2;

                // Tanggal Export (disesuaikan dengan lastColumn baru)
                $sheet->setCellValue('A' . $row, 'Tanggal Export: ' . Carbon::now()->translatedFormat('d F Y H:i'));
                $sheet->mergeCells('A' . $row . ':' . $lastColumn . $row);
                $sheet->getStyle('A' . $row)->getFont()->setItalic(true);
                $row += 2;

                // --- Informasi Detail Survei (Tetap sama) ---
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
                // --- End of Info ---

                // Header Tabel
                $headerRow = $this->headerRowCount;
                $sheet->fromArray($this->headings, null, 'A' . $headerRow);
                $headerStyle = [
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFD3D3D3']],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                ];
                // Terapkan style ke header yang sudah diperbarui
                $sheet->getStyle('A' . $headerRow . ':' . $lastColumn . $headerRow)->applyFromArray($headerStyle);
                $sheet->getRowDimension($headerRow)->setRowHeight(20);

                // --- Formatting Kolom ---
                $lastDataRow = $headerRow + $this->survey->mitraSurveis()->count();

                // Format Kolom Rate Honor
                $honorColumnIndex = array_search('Rate Honor', $this->headings);
                if ($honorColumnIndex !== false) {
                    $honorColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($honorColumnIndex + 1);
                    $sheet->getStyle($honorColumn . ($headerRow + 1) . ':' . $honorColumn . $lastDataRow)
                        ->getNumberFormat()
                        ->setFormatCode('#,##0');
                }

                // 4. TAMBAHKAN FORMAT UNTUK KOLOM BARU
                // Format Kolom Total Honor
                $totalHonorColumnIndex = array_search('Total Honor', $this->headings);
                if ($totalHonorColumnIndex !== false) {
                    $totalHonorColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalHonorColumnIndex + 1);
                    $sheet->getStyle($totalHonorColumn . ($headerRow + 1) . ':' . $totalHonorColumn . $lastDataRow)
                        ->getNumberFormat()
                        ->setFormatCode('#,##0');
                }

                // Format Kolom Sobat ID sebagai Teks untuk mencegah angka besar menjadi notasi ilmiah
                $sobatIdColumnIndex = array_search('Sobat ID', $this->headings);
                if ($sobatIdColumnIndex !== false) {
                    $sobatIdColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($sobatIdColumnIndex + 1);
                    $sheet->getStyle($sobatIdColumn . ($headerRow + 1) . ':' . $sobatIdColumn . $lastDataRow)
                        ->getNumberFormat()
                        ->setFormatCode(NumberFormat::FORMAT_TEXT);
                }

                // Auto-size semua kolom
                foreach (range('A', $lastColumn) as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }
            },
        ];
    }
}
