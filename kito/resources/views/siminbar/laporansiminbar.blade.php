<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> --}}
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table, th, td {
            border: 2px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f9fafb;
        }
        img {
            width: 100px;
            height: auto;
        }
        h2 {
            text-align: center;
        }
    </style>
    <title>Document</title>
</head>
<body>
    {{-- <div class="flex items-center">
        <span class="mx-2 text-2xl font-semibold text-white">Absensi Rapat</span>
    </div> --}}
    <div>
        <h2>Laporan SIMINBAR</h2>
    </div>
    <table class="min-w-full">
        <tr>
            <th class="px-6 py-5 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">No.</th>
            <th class="px-6 py-5 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">Nama Barang</th>
            <th class="px-6 py-5 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">Nama</th>
            <th class="px-6 py-5 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">Status</th>
            <th class="px-6 py-5 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">Catatan</th>
            <th class="px-6 py-5 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">Stok Permintaan</th>
            <th class="px-6 py-5 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">Stok tersedia</th>
            <th class="px-6 py-5 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">Tanggal Pemesanan</th>
        </tr>
        @foreach($datas as $data)
        <tr>
            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                <div class="text-sm font-medium leading-5 text-gray-900 whitespace-nowrap">
                    {{ $loop->iteration }} <!-- Menampilkan nomor urut -->
                </div>
            </td>
            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200"><div class="text-sm font-medium leading-5 text-gray-900 whitespace-nowrap">
                {{ $data['namabarang'] }}
            </div>
            </td>
            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200"><div class="text-sm font-medium leading-5 text-gray-900 whitespace-nowrap">
                {{ $data['name'] }}
            </div>
            </td>
            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200"><div class="text-sm font-medium leading-5 text-gray-900 whitespace-nowrap">
                {{ $data['status'] }}
            </div>
            </td>
            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200"><div class="text-sm font-medium leading-5 text-gray-900 whitespace-nowrap">
                {{ $data['catatan'] }}
            </div>
            </td>
            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200"><div class="text-sm font-medium leading-5 text-gray-900 whitespace-nowrap">
                {{ $data['stokpermintaan'] }}
            </div>
            </td>
            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200"><div class="text-sm font-medium leading-5 text-gray-900 whitespace-nowrap">
                {{ $data['stoktersedia'] }}
            </div>
            </td>
            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200"><div class="text-sm font-medium leading-5 text-gray-900 whitespace-nowrap">
                {{ $data['orderdate'] }}
            </div>
            </td>
        </tr>
        {{-- masalahnya ada di versi pdfnya, pdfnya nggak bisa membaca gambar jadi harus pakai public_path bukan asset --}}
        
        @endforeach
    </table>

</body>
</html>