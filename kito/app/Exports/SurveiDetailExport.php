<?php

namespace App\Exports;

use App\Models\Survei;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell; // DIKEMBALIKAN: Untuk memperbaiki bug
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Carbon\Carbon;

class SurveiDetailExport implements FromCollection, WithMapping, WithEvents, WithCustomStartCell
{
    protected $survey;
    protected $rowNumber = 0;
    protected $headings = [
        'No',
        'Sobat ID',
        'Nama Mitra',
        'Kecamatan',
        'Posisi',
        'Vol',
        'Rate Honor',
        'Total Honor',
    ];

    // DIKEMBALIKAN: Menentukan baris tempat header tabel dimulai.
    // Dihitung manual dari struktur di registerEvents().
    protected $headerRow = 15;

    public function __construct(Survei $survey)
    {
        $this->survey = $survey;
    }

    /**
     * Metode ini memberitahu Excel untuk mulai menulis data DARI BAWAH HEADER.
     * Ini adalah kunci untuk memperbaiki bug baris kosong.
     */
    public function startCell(): string
    {
        // Data akan dimulai satu baris setelah header tabel.
        return 'A' . ($this->headerRow + 1);
    }

    public function collection()
    {
        return $this->survey->mitraSurveis()->with(['mitra.kecamatan', 'posisiMitra'])->get();
    }

    public function map($mitraSurvei): array
    {
        $this->rowNumber++;
        $totalHonor = ($mitraSurvei->vol ?? 0) * ($mitraSurvei->rate_honor ?? 0);

        return [
            $this->rowNumber,
            ' ' . ($mitraSurvei->mitra->sobat_id ?? 'N/A'),
            $mitraSurvei->mitra->nama_lengkap ?? 'N/A',
            $mitraSurvei->mitra->kecamatan->nama_kecamatan ?? 'N/A',
            $mitraSurvei->posisiMitra->nama_posisi ?? 'N/A',
            $mitraSurvei->vol,
            $mitraSurvei->rate_honor,
            $totalHonor,
        ];
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $row = 1;
                $lastColumn = 'H';

                // 1. Judul Laporan Utama (Baris 1)
                $sheet->setCellValue('A' . $row, 'DETAIL INFORMASI SURVEI');
                $sheet->mergeCells('A' . $row . ':' . $lastColumn . $row);
                $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $row += 2; // Pindah ke baris 3 (beri spasi 1 baris)

                // 2. Tanggal Export (Baris 3)
                Carbon::setLocale('id');
                $sheet->setCellValue('A' . $row, 'Tanggal Export: ' . Carbon::now()->translatedFormat('d F Y H:i'));
                $sheet->mergeCells('A' . $row . ':' . $lastColumn . $row);
                $sheet->getStyle('A' . $row)->getFont()->setItalic(true);
                $row += 2; // Pindah ke baris 5

                // 3. Informasi Detail Survei (Mulai Baris 5)
                $sheet->setCellValue('A' . $row, 'Informasi Survei:');
                $sheet->getStyle('A' . $row)->getFont()->setBold(true);
                $row++; // Pindah ke baris 6

                $jadwalMulai = Carbon::parse($this->survey->jadwal_kegiatan)->translatedFormat('j F Y');
                $jadwalSelesai = Carbon::parse($this->survey->jadwal_berakhir_kegiatan)->translatedFormat('j F Y');

                $sheet->setCellValue('A' . $row++, 'Nama Survei: ' . $this->survey->nama_survei);
                $sheet->setCellValue('A' . $row++, 'Jadwal Kegiatan: ' . $jadwalMulai . ' - ' . $jadwalSelesai);
                $sheet->setCellValue('A' . $row++, 'Tim: ' . $this->survey->tim);
                $sheet->setCellValue('A' . $row++, 'KRO: ' . $this->survey->kro);
                $row++; // Pindah ke baris 11 (beri spasi 1 baris)

                // 4. Ringkasan Total (Mulai Baris 11)
                $collection = $this->collection();
                $totalMitra = $collection->count();
                $totalHonor = $collection->sum(function ($mitraSurvei) {
                    return ($mitraSurvei->vol ?? 0) * ($mitraSurvei->rate_honor ?? 0);
                });

                $summaryStartRow = $row;
                $sheet->setCellValue('A' . $row++, 'Total Mitra: ' . $totalMitra . ' Orang');
                $sheet->setCellValue('A' . $row++, 'Total Honor Keseluruhan: Rp ' . number_format($totalHonor, 0, ',', '.'));
                $sheet->getStyle('A' . $summaryStartRow . ':A' . ($row - 1))->getFont()->setBold(true);
                $row += 2; // Pindah ke baris 15 (beri spasi 2 baris)

                // --- BAGIAN TABEL DATA ---

                // 5. Header Tabel (di baris ke-15, sesuai properti $this->headerRow)
                $sheet->fromArray($this->headings, null, 'A' . $this->headerRow);

                $headerStyle = [
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFD3D3D3']],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'horizontal' => Alignment::HORIZONTAL_CENTER],
                ];
                $sheet->getStyle('A' . $this->headerRow . ':' . $lastColumn . $this->headerRow)->applyFromArray($headerStyle);
                $sheet->getRowDimension($this->headerRow)->setRowHeight(20);

                // 6. Formatting Kolom Data
                $firstDataRow = $this->headerRow + 1;
                $lastDataRow = $this->headerRow + $totalMitra;

                if ($totalMitra > 0) {
                    $sheet->getStyle('G' . $firstDataRow . ':H' . $lastDataRow)->getNumberFormat()->setFormatCode('#,##0');
                    $sheet->getStyle('B' . $firstDataRow . ':B' . $lastDataRow)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
                    $sheet->getStyle('C' . $firstDataRow . ':F' . $lastDataRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    $sheet->getStyle('G' . $firstDataRow . ':H' . $lastDataRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                }

                // 7. Auto-size semua kolom
                foreach (range('A', $lastColumn) as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }
            },
        ];
    }
}
