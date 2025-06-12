<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;

class MitraExport implements FromCollection, WithMapping, WithEvents
{
    protected $data;
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

    public function __construct(Collection $data, $filters = [], $totals = [])
    {
        $this->data = $data;
        $this->filters = $filters;
        $this->totals = $totals;

        // Cek jika filter 'Bulan' ada dan aktif
        if (!empty($this->filters['Bulan']) || !empty($this->filters['bulan'])) {
            $this->isMonthFilterActive = true;
            // Sisipkan kolom 'Nilai' dan 'Catatan' setelah 'Nama Survei'
            $namaSurveiIndex = array_search('Nama Survei', $this->headings);
            if ($namaSurveiIndex !== false) {
                array_splice($this->headings, $namaSurveiIndex + 1, 0, ['Nilai', 'Catatan']);
            } else {
                $this->headings[] = 'Nilai';
                $this->headings[] = 'Catatan';
            }
        }
    }

    public function collection()
    {
        // Jika filter bulan aktif, muat relasi yang diperlukan untuk kolom tambahan
        if ($this->isMonthFilterActive) {
            Carbon::setLocale('id');

            $tahun = $this->filters['Tahun'] ?? $this->filters['tahun'];

            // Dapatkan nomor bulan dari nama bulan yang diformat atau nomor bulan langsung
            $bulanValue = $this->filters['Bulan'] ?? $this->filters['bulan'];
            $bulan = is_numeric($bulanValue) ? $bulanValue : Carbon::parse($bulanValue)->month;

            // Muat data survei mitra berdasarkan filter tahun dan bulan
            $this->data->load(['mitraSurveis' => function ($query) use ($tahun, $bulan) {
                $query->with('survei')
                    ->whereHas('survei', function ($sq) use ($tahun, $bulan) {
                        $sq->whereYear('bulan_dominan', $tahun)
                            ->whereMonth('bulan_dominan', $bulan);
                    });
            }]);
        }
        return $this->data;
    }

    public function map($mitra): array
    {
        static $count = 0;
        $count++;

        // Ambil data agregat dari query controller
        $jumlahSurvei = $mitra->total_survei ?? 0;
        $totalHonor = $mitra->total_honor_per_mitra ?? 0;
        $namaSurvei = '-';

        // Jika ada survei dan relasi sudah dimuat, dapatkan nama survei
        if ($jumlahSurvei > 0 && $mitra->relationLoaded('mitraSurveis')) {
            $namaSurvei = $mitra->mitraSurveis->map(function ($mitraSurvei) {
                return $mitraSurvei->survei ? $mitraSurvei->survei->nama_survei : null;
            })->filter()->unique()->implode(', ');
        }

        $statusPekerjaan = $mitra->status_pekerjaan == 0 ? 'Bisa Ikut Survei' : 'Tidak Bisa Ikut Survei';

        // Data baris awal
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
            empty(trim($namaSurvei)) ? '-' : $namaSurvei,
        ];

        // Jika filter bulan aktif, tambahkan kolom 'Nilai' dan 'Catatan'
        if ($this->isMonthFilterActive) {
            $nilai = '-';
            $catatan = '-';
            if ($mitra->relationLoaded('mitraSurveis') && $mitra->mitraSurveis->isNotEmpty()) {
                $nilai = $mitra->mitraSurveis->map(fn($ms) => $ms->nilai ?? '-')->implode(", ");
                $catatan = $mitra->mitraSurveis->map(fn($ms) => $ms->catatan ?? '-')->implode(", ");
            }
            // Cari posisi 'Nama Survei' untuk menyisipkan data baru
            $namaSurveiIndex = array_search('Nama Survei', $this->headings);
            if ($namaSurveiIndex !== false) {
                array_splice($rowData, $namaSurveiIndex + 1, 0, [$nilai, $catatan]);
            } else {
                $rowData[] = $nilai;
                $rowData[] = $catatan;
            }
        }

        // Gabungkan dengan sisa data baris
        $rowData = array_merge($rowData, [
            $totalHonor,
            $statusPekerjaan,
            $mitra->detail_pekerjaan ?? '-',
            $jumlahSurvei > 0 ? 'Aktif Mengikuti Survei' : 'Tidak Aktif Mengikuti Survei',
        ]);

        return $rowData;
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $row = 1;

                // Tentukan kolom terakhir berdasarkan apakah filter bulan aktif atau tidak
                $lastColumn = $this->isMonthFilterActive ? 'U' : 'S';

                // Judul Laporan
                $sheet->setCellValue('A' . $row, 'LAPORAN DATA MITRA');
                $sheet->mergeCells('A' . $row . ':' . $lastColumn . $row);
                $sheet->getStyle('A' . $row)->getFont()->setBold(true)->setSize(14);
                $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $row += 2; // Beri spasi

                // Tanggal Export
                $sheet->setCellValue('A' . $row, 'Tanggal Export: ' . Carbon::now()->format('d/m/Y H:i'));
                $sheet->mergeCells('A' . $row . ':' . $lastColumn . $row);
                $sheet->getStyle('A' . $row)->getFont()->setItalic(true);
                $row += 2; // Beri spasi

                // Informasi Filter
                if (!empty($this->filters)) {
                    $sheet->setCellValue('A' . $row, 'Filter yang digunakan:');
                    $sheet->mergeCells('A' . $row . ':' . $lastColumn . $row);
                    $sheet->getStyle('A' . $row)->getFont()->setBold(true);
                    $row++;

                    foreach ($this->filters as $key => $value) {
                        $label = $this->getFilterLabel($key);
                        $displayValue = $value;

                        // Logika untuk memastikan nama bulan selalu dalam Bahasa Indonesia
                        // Menggunakan strtolower untuk perbandingan case-insensitive
                        if (in_array(strtolower($key), ['bulan', 'Bulan'])) {
                            Carbon::setLocale('id'); // Atur lokal ke Indonesia
                            // Buat objek Carbon dari nomor bulan (contoh: '06') atau nama bulan
                            $displayValue = is_numeric($value) ? Carbon::create()->month($value)->translatedFormat('F') : Carbon::parse($value)->translatedFormat('F');
                        }

                        $sheet->setCellValue('A' . $row, $label . ': ' . $displayValue);
                        $sheet->mergeCells('A' . $row . ':' . $lastColumn . $row);
                        $row++;
                    }
                    $row++; // Beri spasi
                }

                // Ringkasan Total
                $summaryStartRow = $row;
                $sheet->setCellValue('A' . $row++, 'Total Mitra: ' . $this->totals['totalMitra']);
                $sheet->setCellValue('A' . $row++, 'Total Mitra Laki-laki: ' . $this->totals['totalLaki']);
                $sheet->setCellValue('A' . $row++, 'Total Mitra Perempuan: ' . $this->totals['totalPerempuan']);
                $sheet->setCellValue('A' . $row++, 'Aktif Mengikuti Survei: ' . $this->totals['totalIkutSurvei']);
                $sheet->setCellValue('A' . $row++, 'Tidak Aktif Mengikuti Survei: ' . $this->totals['totalTidakIkutSurvei']);
                $sheet->setCellValue('A' . $row++, 'Bisa Ikut Survei: ' . $this->totals['totalBisaIkutSurvei']);
                $sheet->setCellValue('A' . $row++, 'Tidak Bisa Ikut Survei: ' . $this->totals['totalTidakBisaIkutSurvei']);

                if ($this->isMonthFilterActive) {
                    $sheet->setCellValue('A' . $row++, 'Total Mitra Partisipasi > 1 Survei: ' . ($this->totals['totalMitraLebihDariSatuSurvei'] ?? 0));
                    $sheet->setCellValue('A' . $row++, 'Total Mitra Honor > 4 Juta: ' . ($this->totals['totalMitraHonorLebihDari4Jt'] ?? 0));
                }

                $sheet->setCellValue('A' . $row++, 'Total Honor: ' . number_format($this->totals['totalHonor'], 0, ',', '.'));
                $sheet->getStyle('A' . $summaryStartRow . ':A' . ($row - 1))->getFont()->setBold(true);
                $row += 2; // Beri spasi

                // Header Tabel
                $headerRow = $row;
                $sheet->fromArray($this->headings, null, 'A' . $headerRow);

                $headerStyle = [
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFD3D3D3']],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                ];
                $sheet->getStyle('A' . $headerRow . ':' . $lastColumn . $headerRow)->applyFromArray($headerStyle);
                $sheet->getRowDimension($headerRow)->setRowHeight(20);

                // Format Kolom Honor
                $honorColumnIndex = array_search('Total Honor', $this->headings);
                if ($honorColumnIndex !== false) {
                    $honorColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($honorColumnIndex + 1);
                    $sheet->getStyle($honorColumn)->getNumberFormat()->setFormatCode('#,##0');
                }

                // Auto-size semua kolom
                foreach (range('A', $lastColumn) as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }
            },
        ];
    }

    protected function getFilterLabel($key)
    {
        // Peta untuk label filter yang lebih ramah pengguna
        $labels = [
            'Tahun' => 'Tahun',
            'tahun' => 'Tahun',
            'Bulan' => 'Bulan',
            'bulan' => 'Bulan',
            'Kecamatan' => 'Kecamatan',
            'kecamatan' => 'Kecamatan',
            'Nama Mitra' => 'Nama Mitra',
            'nama_lengkap' => 'Nama Mitra',
            'Status Partisipasi' => 'Status Partisipasi',
            'status_mitra' => 'Status Partisipasi',
            'Status Pekerjaan' => 'Status Pekerjaan',
            'status_pekerjaan' => 'Status Pekerjaan',
            'Partisipasi > 1 Survei' => 'Partisipasi > 1 Survei',
            'partisipasi_lebih_dari_satu' => 'Partisipasi > 1 Survei',
            'Honor > 4 Juta' => 'Honor > 4 Juta',
            'honor_lebih_dari_4jt' => 'Honor > 4 Juta',
            'Jenis Kelamin' => 'Jenis Kelamin',
            'jenis_kelamin' => 'Jenis Kelamin',
        ];
        return $labels[$key] ?? ucfirst(str_replace('_', ' ', $key));
    }
}
