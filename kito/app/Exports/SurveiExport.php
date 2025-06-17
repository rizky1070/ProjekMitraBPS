<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
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
        'Status Partisipasi'
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

        $jumlahMitra = $survei->total_mitra ?? 0;

        // Akses sobat_id melalui relasi mitra
        $sobatIds = $survei->mitraSurveis->isNotEmpty()
            ? $survei->mitraSurveis->map(function ($mitraSurvei) {
                return $mitraSurvei->mitra->sobat_id ?? null;
            })->filter()->implode(', ')
            : '-';

        $sobatIds = empty(trim($sobatIds)) ? '-' : $sobatIds;

        return [
            $count,
            $survei->nama_survei ?? '-',
            $survei->provinsi->kode_provinsi ?? '-',
            $survei->kabupaten->kode_kabupaten ?? '-',
            $survei->kro ?? '-',
            $survei->tim ?? '-',
            Carbon::parse($survei->jadwal_kegiatan)->format('d/m/Y'),
            Carbon::parse($survei->jadwal_berakhir_kegiatan)->format('d/m/Y'),
            $jumlahMitra,
            $sobatIds,
            // [DIUBAH] Label diubah agar konsisten
            $jumlahMitra > 0 ? 'Diikuti Mitra' : 'Tidak Diikuti Mitra'
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
                $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $row += 2; // Tambah spasi

                // Tanggal Export
                $sheet->setCellValue('A' . $row, 'Tanggal Export: ' . Carbon::now()->format('d/m/Y H:i'));
                $sheet->mergeCells('A' . $row . ':K' . $row);
                $sheet->getStyle('A' . $row)->getFont()->setItalic(true);
                $row += 2; // Tambah spasi

                // [DIUBAH] Informasi Filter (Disederhanakan)
                if (!empty($this->filters)) {
                    $sheet->setCellValue('A' . $row, 'Filter yang digunakan:');
                    $sheet->getStyle('A' . $row)->getFont()->setBold(true);
                    $row++;

                    // 1. Atur lokal Carbon ke Indonesia agar nama bulan diterjemahkan
                    Carbon::setLocale('id');

                    // 2. Loop melalui filter
                    foreach ($this->filters as $label => $value) {
                        $displayValue = $value; // Nilai default adalah nilai asli

                        // 3. Cek jika label adalah 'Bulan' (dibuat case-insensitive)
                        //    dan pastikan nilainya adalah angka.
                        if (strtolower($label) === 'bulan' && is_numeric($value)) {
                            // 4. Ubah angka bulan (misal: 6) menjadi nama bulan ("Juni")
                            $displayValue = Carbon::create()->month($value)->translatedFormat('F');
                        }

                        // 5. Tulis ke sheet dengan nilai yang sudah diubah (jika ada)
                        $sheet->setCellValue('A' . $row, $label . ': ' . $displayValue);
                        $row++;
                    }
                }

                // [DIUBAH] Informasi Total (Ditambahkan Total Tim)
                $sheet->setCellValue('A' . $row, 'Ringkasan Data:');
                $sheet->getStyle('A' . $row)->getFont()->setBold(true);
                $row++;

                $sheet->setCellValue('A' . $row, 'Total Survei: ' . ($this->totals['totalSurvei'] ?? 0));
                $row++;

                $sheet->setCellValue('A' . $row, 'Diikuti Mitra: ' . ($this->totals['totalSurveiAktif'] ?? 0));
                $row++;

                $sheet->setCellValue('A' . $row, 'Tidak Diikuti Mitra: ' . ($this->totals['totalSurveiTidakAktif'] ?? 0));
                $row++;

                // [BARU] Menampilkan Total Tim
                $sheet->setCellValue('A' . $row, 'Total Tim: ' . ($this->totals['totalTim'] ?? 0));
                $row++;

                $row++; // Tambah spasi sebelum header tabel

                // Header Tabel
                $headerRow = $row;
                $sheet->fromArray($this->headings, null, 'A' . $headerRow);
                $sheet->getStyle('A' . $headerRow . ':K' . $headerRow)->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFD3D3D3']],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                // Set kolom auto-size
                foreach (range('A', 'K') as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }
            },
        ];
    }
}