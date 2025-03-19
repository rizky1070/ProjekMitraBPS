<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" href="/Logo BPS.png" type="image/png">
    <title>Profil Mitra</title>
</head>
<body>
    <a href="{{ url('/daftarMitra') }}"  class="px-4 py-2 bg-orange text-black rounded-bl-none rounded-br-md">
        <
    </a>
    <div class="flex items-center my-4 pl-10">
        <div class="flex flex-col justify-center items-center text-center">
            <h1 class="text-2xl font-bold">Profil Mitra</h1>
            <img alt="Profile picture" class="w-24 h-24 rounded-full border-4 border-gray-500 mr-4" src="proflie.png" width="100" height="100">
            <h2 class="text-xl">{{ $mits->nama_lengkap }}</h2>
        </div>
        <div class="pl-5"> 
            <p><strong>Domisili :</strong> {{ $mits->kecamatan->nama_kecamatan }}</p>
            <p><strong>Alamat Detail :</strong> {{ $mits->alamat_mitra }}, {{ $mits->desa->nama_desa }}</p>
            <p><strong>Nomor Handphone :</strong> +62</p>
            <p><strong>Email :</strong> www.email.com</p>       
        </div>
    </div>



    <!-- Tabel Survei -->
    <div class="max-w-4xl mx-auto p-4">
        <h2 class="text-xl font-bold mb-4">Survei yang sudah dikerjakan</h2>
        <input type="text" placeholder="Search..." class="border p-2 w-full mb-2">
        <table class="w-full border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border border-gray-300 p-2">Nama Survei</th>
                    <th class="border border-gray-300 p-2">Tahun</th>
                    <th class="border border-gray-300 p-2">Catatan</th>
                    <th class="border border-gray-300 p-2">Nilai</th>
                    <th class="border border-gray-300 p-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($survei as $sur)
                <tr class="border border-gray-300">
                    <td class="p-2">{{ $sur->survei->nama_survei }}</td> <!-- Perbaikan di sini -->
                    <td class="p-2">{{ $sur->survei->jadwal_kegiatan }}</td>
                    <td class="p-2">{{ $sur->catatan }}</td>
                    <?php
                    $nilai = $sur->nilai;
                    $nilaiOutput = str_repeat('⭐', $nilai);
                    ?>
                    <td class="p-2">{{ $nilaiOutput }}</td>
                    <td class="p-2">
                        <a href="/penilaianMitra/{{ $sur->survei->id_survei }}"  class="px-4 py-1 bg-orange text-white rounded-md">Edit</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</body>
<!-- ⭐⭐⭐⭐⭐ -->
</html>
