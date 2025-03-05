<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulasi Tampilan PDF</title>
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> --}}
    <style>
       body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
            line-height: 1.6;
        }
        
        h1 {
            text-align: center;
            color: #4A4A4A;
            font-size: 1.8rem;
            margin-bottom: 20px;
            text-transform: uppercase;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 0.9rem;
            color: #333;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
            vertical-align: middle;
        }

        th {
            background-color: #f4f4f4;
            font-weight: bold;
            text-transform: uppercase;
        }

        td {
            background-color: #fff;
        }

        tr:nth-child(even) td {
            background-color: #f9f9f9;
        }

        .signature-section {
            text-align: center;
            margin-top: 30px;
            color: #333;
        }

        .signature-section p {
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 0.95rem;
        }

        .signature {
            width: 150px;
            height: auto;
            margin-top: 10px;
            display: inline-block;
        }

        .image-cell img {
            width: 70px;
            height: auto;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .signature-section {
    text-align: left; /* Menempatkan seluruh elemen di kiri */
    margin-top: 30px;
    color: #333;
    width: 100%;
}

.signature-section p {
    margin: 0;
    padding: 0;
}

#ttdumum{
    margin-left: 15%;
}

.signature {

    width: 70px;
    height: auto;
}

    </style>
</head>
<body>

    <h1>SIMINBAR ORDER</h1>
    <table class="min-w-full">
        <tr>
            <th class="px-6 py-5 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">User</th>
            <th class="px-6 py-5 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">Jumlah Pesanan</th>
            <th class="px-6 py-5 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">Tanggal</th>
            <th class="px-6 py-5 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">Nama Barang</th>
            <th class="px-6 py-5 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">Catatan</th>
            <th class="px-6 py-5 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">Bukti Foto</th>
            <th class="px-6 py-5 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">Admin yang Bertugas</th>
            <th class="px-6 py-5 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">Tanda Tangan Admin</th>
            <th class="px-6 py-5 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">Tanda Tangan User</th>
        </tr>
        <tr>
            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200"><div class="text-sm font-medium leading-5 text-gray-900 whitespace-nowrap">
                {{ $permintaanbarangData['name'] }}
            </div>
            </td>
            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200"><div class="text-sm font-medium leading-5 text-gray-900 whitespace-nowrap">
                {{ $permintaanbarangData['stokpermintaan'] }}
            </div>
            </td>
            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200"><div class="text-sm font-medium leading-5 text-gray-900 whitespace-nowrap">
                {{ $permintaanbarangData['orderdate'] }}
            </div>
            </td>
            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200"><div class="text-sm font-medium leading-5 text-gray-900 whitespace-nowrap">
                {{ $permintaanbarangData['namabarang'] }}
            </div>
            </td>
            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200"><div class="text-sm font-medium leading-5 text-gray-900 whitespace-nowrap">
                {{ $permintaanbarangData['catatan'] }}
            </div>
            </td>
            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                <div class="text-sm font-medium leading-5 text-gray-900 whitespace-nowrap">
                @if (!empty($permintaanbarangData['buktifoto']))
                <img src="{{ public_path('storage/uploads/images/' . $permintaanbarangData['buktifoto']) }}" alt="Bukti Foto" style="width: 70px; height: auto;">
                @else
                <p>-</p>
                @endif
                </div>
            </td>
            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200"><div class="text-sm font-medium leading-5 text-gray-900 whitespace-nowrap">
                {{ $permintaanbarangData['name'] }}
            </div>
            </td>
            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200"><div class="text-sm font-medium leading-5 text-gray-900 whitespace-nowrap">
                @if (!empty($permintaanbarangData['ttdadmin']))
                <img src="{{ public_path('storage/uploads/signatures/' . $permintaanbarangData['ttdadmin']) }}" alt="Signature" style="width: 70px; height: auto;">
                {{-- <img src="{{$path_gambar[1]}}" alt="Signature" style="width: 70px; height: auto;">  --}}
                @else
                <p>-</p>
                @endif
            </div>
            </td>
            <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200"><div class="text-sm font-medium leading-5 text-gray-900 whitespace-nowrap">
                <img src="{{ public_path('storage/uploads/signatures/' . $permintaanbarangData['ttduser']) }}" alt="Signature" style="width: 70px; height: auto;">
                {{-- <img src="{{$path_gambar[0]}}" alt="Signature" style="width: 70px; height: auto;">  --}}
            </div>
            </td>
        </tr>
        {{-- masalahnya ada di versi pdfnya, pdfnya nggak bisa membaca gambar jadi harus pakai public_path bukan asset --}}

    </table>

    <div class="signature-section">
   
        <p>MENGETAHUI,</p>
        <p> KASUBAG UMUM BPS KABUPATEN MOJOKERTO</p>
        
        @if (!empty($permintaanbarangData['ttdumum']))
        <img id="ttdumum" src="{{ public_path('storage/uploads/signatures/' . $permintaanbarangData['ttdumum']) }}" alt="Signature" style="width: 70px; height: auto;">
        @else
        <p>-</p>
        @endif
       
    
</div>

</body>
</html>
