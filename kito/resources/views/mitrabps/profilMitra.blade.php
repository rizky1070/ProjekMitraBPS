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
<body class="h-full bg-gray-200">
        <!-- SweetAlert Logic -->
    @if (session('success'))
    <script>
    swal("Success!", "{{ session('success') }}", "success");
    </script>
    @endif

    @if ($errors->any())
    <script>
    swal("Error!", "{{ $errors->first() }}", "error");
    </script>
    @endif
        <!-- component -->
    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200">
        <a href="{{ url('/daftarMitra') }}"  class="px-4 py-2 bg-orange text-black rounded-bl-none rounded-br-md">
            <
        </a>
        <div class="max-w-4xl mx-auto mt-4">
            <h1 class="text-2xl font-bold">Profil Mitra</h1>
            <div class="flex items-center bg-white my-4 px-10 py-5 rounded-lg shadow">
                <div class="flex flex-col justify-center items-center text-center">
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
        </div>



        <!-- Tabel Survei -->
        <div class="max-w-4xl mx-auto">
            <h2 class="text-xl font-bold mb-4">Survei yang sudah dikerjakan</h2>
            <div class="bg-white p-4 rounded-lg shadow">
                <form method="GET" action="{{ route('profilMitra', ['id_mitra' => $mits->id_mitra]) }}" class="flex justify-between items-center mb-4">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." class="px-4 py-2 border border-gray-300 rounded-md">
                </form>

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
                        @if($sur->survei->status_survei == 3)
                        <tr class="border border-gray-300 hover:bg-gray-100">
                            <td class="p-2">{{ $sur->survei->nama_survei }}</td>
                            <td class="p-2 text-center">{{ $sur->survei->jadwal_kegiatan }}</td>
                            @if($sur->catatan == null & $sur->nilai == null)
                            <td class="p-2 text-center text-red-700 font-bold">Tidak ada catatan</td>
                            <td class="p-2 text-center text-red-700 font-bold">Belum dinilai</td>
                            @elseif($sur->catatan != null & $sur->nilai != null)
                            <td class="p-2 text-center">{{ $sur->catatan }}</td>
                            <?php $nilaiOutput = str_repeat('⭐', $sur->nilai); ?>
                            <td class="p-2 text-center">{{ $nilaiOutput }}</td>
                            @endif
                            <td class="p-2 text-center">
                                <a href="/penilaianMitra/{{ $sur->survei->id_survei }}" class="px-4 py-1 bg-orange text-white rounded-md">Edit</a>
                            </td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </main>


</body>
<!-- ⭐⭐⭐⭐⭐ -->
</html>
