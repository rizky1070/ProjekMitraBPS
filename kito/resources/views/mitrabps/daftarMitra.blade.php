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
                    <h3 class="text-3xl font-medium text-black">Daftar Mitra</h3>
                    <div class="p-6">
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
                            <button type="submit" class="px-4 py-2 bg-orange text-black rounded-md">Filter</button>
                            </div>
                                <!-- Menambahkan ml-auto untuk memindahkan tombol Tambah ke kanan -->
                            <div class="flex items-center space-x-4">
                            <button type="button" class="px-4 py-2 bg-orange rounded-md" onclick="openModal()">+ Tambah</button>
                            </div>
                        </form>
                    </div>  
            <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse border border-gray-300">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border border-gray-300 p-2">Nama Mitra</th>
                                    <th class="border border-gray-300 p-2">Domisili</th>
                                    <th class="border border-gray-300 p-2">Survei yang Diikuti</th>
                                    <th class="border border-gray-300 p-2">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($mitras as $mitra)
                                <tr class="bg-white hover:bg-gray-100">
                                    <td class="border border-gray-300 p-2">{{ $mitra->nama_lengkap }}</td>
                                    <td class="border border-gray-300 p-2 text-center">{{ $mitra->kecamatan->nama_kecamatan ?? '-' }}</td>
                                    <td class="border border-gray-300 p-2 text-center">{{ $mitra->mitra_survei_count }}</td>
                                    <td class="border border-gray-300 p-2 text-center">
                                        <a href="/profilMitra/{{ $mitra->id_mitra }}"  class="px-4 py-1 bg-orange text-white rounded-md">Lihat</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @include('components.pagination', ['paginator' => $mitras])
                </div>
            </main>
        </div>
    </div>
    <!-- Modal Upload Excel -->
    <div id="uploadModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg w-1/3">
            <h2 class="text-xl font-bold mb-2">Unggah File Excel</h2>
            <p class="mb-2">Tambahkan mitra dengan file excel.</p>
            <form action="{{ route('upload.excelMitra') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="file" name="file" accept=".xlsx, .xls" class="border p-2 w-full">
                <div class="flex justify-end mt-4">
                    <button type="button" class="px-4 py-2 bg-gray-500 text-white rounded-md mr-2" onclick="closeModal()">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-orange text-white rounded-md">Unggah</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('uploadModal').classList.remove('hidden');
        }
        function closeModal() {
            document.getElementById('uploadModal').classList.add('hidden');
        }
    </script>
</body>
</html>