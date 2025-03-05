<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    <style>
     body {
            font-family: Misans, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 5px 0;
        }

        th,
        td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #ac8bf3;
        }

        /* Warna baris ganjil dan genap */
        tr:nth-child(even) {
            background-color: #ffffff;
        }

        tr:nth-child(odd) {
            background-color: #f5f5f5;
        }

        /* Styling untuk header */
        /* #judul {
            position: fixed;
            Pastikan judul tetap di tempatnya
            top: 0;
            width: 100%;
            height: 0px;
            Sesuaikan tinggi header padding: 0;
            margin: 0;
        } */

        #judulgambar {
            position: absolute;
            top: -35px;
            left: 10px;
            width: 200px;
            height: auto;
        }

        h2 {
            text-align: center;
            position: absolute;
            /* top: 50%; */
            left: 50%;
            transform: translate(-50%, -50%);
            margin: 0;
            font-size: 30px;
        }

        /* Pengaturan margin untuk seluruh halaman */
        @page {
            margin-top: 150px;
            /* Memberikan ruang untuk judul */
            margin-bottom: 50px;
            margin-left: 50px;
            margin-right: 50px;
        }

        /* Header tetap berada di atas */
        .header {
            position: fixed;
            top: -70px;
            left: 0;
            width: 100%;
            height: 60px;
            /* Sesuaikan dengan tinggi header */
            text-align: center;
            z-index: 10;
            padding: 0;
        }

        /* Agar konten memiliki ruang yang cukup */
        .content {
            margin-top: 10px;
            /* Menambah ruang di bawah judul */
            margin-bottom: 50px;
        }

        /* Agar header tabel diulang pada setiap halaman */
        thead {
            display: table-header-group;
        }

        /* Agar baris tabel tidak terpotong ketika halaman baru */
        tr {
            page-break-inside: avoid;
        }

        /* Agar tabel tidak terpisah */
        table {
            page-break-inside: auto;
        }
    </style>
    <title>Document</title>
</head>

<body>
    <div id="judul" class="header">
        <img id="judulgambar" src="daftarhadir.jpeg" alt="Judul Gambar">
        <h2>Rekap Presensi {{ $actt }}</h2>
    </div>
    <div class="content">
        <!-- Tabel dengan data -->
        <table>
            <thead>
                <tr>
                    <th
                        class="px-6 py-5 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                        No.
                    </th>
                    <th
                        class="px-6 py-5 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                        Jam Presensi
                    </th>
                    <th
                        class="px-6 py-5 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                        Nama
                    </th>
                    <th
                        class="px-6 py-5 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                        Kegiatan
                    </th>
                    <th
                        class="px-6 py-5 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                        Jabatan
                    </th>
                    <th
                        class="px-6 py-5 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase border-b border-gray-200 bg-gray-50">
                        Tanda tangan
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($datas as $data)
                    <tr>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                            <div class="text-sm font-medium leading-5 text-gray-900 whitespace-nowrap">
                                {{ $loop->iteration }} <!-- Menampilkan nomor urut -->
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                            <div class="text-sm font-medium leading-5 text-gray-900 whitespace-nowrap">
                                {{ $data['absen'] }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                            <div class="text-sm font-medium leading-5 text-gray-900 whitespace-nowrap">
                                {{ $data['nama'] }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                            <div class="text-sm font-medium leading-5 text-gray-900 whitespace-nowrap">
                                {{ $data['kegiatan'] }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                            <div class="text-sm font-medium leading-5 text-gray-900 whitespace-nowrap">
                                {{ $data['jabatan'] }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200">
                            <div class="text-sm font-medium leading-5 text-gray-900 whitespace-nowrap">
                                <img src="{{ public_path('storage/uploads/signatures/' . $data['signature']) }}"
                                    alt="Signature" style="width: 70px; height: auto;">
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>

</html>
