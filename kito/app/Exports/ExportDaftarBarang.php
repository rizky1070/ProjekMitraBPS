<?php

namespace App\Exports;

use App\Models\Barang;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExportDaftarBarang implements FromCollection, WithMapping, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Barang::select('id', 'namabarang', 'stoktersedia')->get();
    }

    public function map($barang): array{
        return[
            $barang->id,
            $barang->namabarang,
            $barang->stoktersedia,
        ];
    }


    public function headings(): array{
        return [
            'Id Barang',
            'Nama Barang',
            'Stok Barang',
        ];
    }

}
