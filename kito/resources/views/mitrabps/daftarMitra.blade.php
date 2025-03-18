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
    <title>Input Mitra BPS</title>
</head>
<body class="h-full">
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
        <div x-data="{ sidebarOpen: false }" class="flex h-screen">
            <x-sidebar></x-sidebar>
            <div class="flex flex-col flex-1 overflow-hidden">
                <x-navbar></x-navbar>
                <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200">
                    <div class="container px-6 py-8 mx-auto">
                        <!-- Title -->
                        <h2 class="text-2xl font-bold mb-6">Daftar Mitra</h2>

                        <!-- Search Bar -->
                            <form action="{{ route('mitras.filter') }}" method="GET" class="flex justify-between items-center mb-4">
                                <div class="flex items-center space-x-4">
                                    <!-- Input untuk search -->
                                    <input type="text" name="search" placeholder="Search..." class="px-4 py-2 border border-gray-300 rounded-md">
                            <!-- Filter Kecamatan -->
                                    <select name="kecamatan" class="px-4 py-2 border border-gray-300 rounded-md">
                                        <option value="">Semua Kecamatan</option>
                                        @foreach($kecamatans as $id => $nama)
                                        <option value="{{ $nama }}" {{ request('kecamatan') == $nama ? 'selected' : '' }}>
                                            {{ $nama }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <!-- Tombol Filter -->
                                    <button type="submit" class="px-4 py-2 bg-orange text-white rounded-md">Filter</button>
                                </div>
                                <!-- Menambahkan ml-auto untuk memindahkan tombol Tambah ke kanan -->
                                <div class="flex items-center space-x-4">
                                    <button class="btn btn-info" type="submit">tambah</button>
                                </div>
                            </form>
                        <!-- Table -->
                                <div class="overflow-x-auto">
                                    <table class="w-full table-auto border-collapse border border-gray-300">
                                        <thead class="bg-orange text-white">
                                            <tr>
                                                <th class="border border-gray-300 p-2 text-left">Nama Mitra</th>
                                                <th class="border border-gray-300 p-2 text-left">Domisili</th>
                                                <th class="border border-gray-300 p-2 text-left">Survei yang Diikuti</th>
                                                <th class="border border-gray-300 p-2 text-left">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($mitras as $mitra)
                                            <tr class="bg-white hover:bg-gray-100">
                                                <td class="border border-gray-300 p-2">{{ $mitra->nama_lengkap }}</td>
                                                <td class="border border-gray-300 p-2">{{ $mitra->kecamatan->nama_kecamatan ?? '-' }}</td>
                                                <td class="border border-gray-300 p-2">{{ $mitra->mitra_survei_count }}</td>
                                                <td class="border border-gray-300 p-2">
                                                    <a class="px-4 py-2 bg-orange text-black rounded-md">{{ $mitra->id_mitra }}</a>
                                                    <a href="/profilMitra/{{ $mitra->id_mitra }}"  class="px-4 py-2 bg-orange text-black rounded-md">Lihat</a>
                                                </td>
                                            </tr>
                                            @endforeach
                                            
                                        </tbody>
                                    </table>
                                </div>

                        <!-- Pagination -->
                        <div class="flex justify-center mt-6">
                            <button class="bg-orange text-white px-3 py-1 rounded-md hover:bg-orange-600">1</button>
                            <button class="bg-gray-300 text-gray-700 px-3 py-1 rounded-md ml-2">2</button>
                            <button class="bg-gray-300 text-gray-700 px-3 py-1 rounded-md ml-2">3</button>
                            <button class="bg-gray-300 text-gray-700 px-3 py-1 rounded-md ml-2">4</button>
                            <button class="bg-gray-300 text-gray-700 px-3 py-1 rounded-md ml-2">5</button>
                        </div>

                    </div>
                </main>
            </div>
        </div>
</body>
</html>