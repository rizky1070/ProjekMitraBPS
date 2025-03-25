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
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
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
                        <div x-data="{ isOpen: false }">
                            <div class="flex justify-between items-center mb-4">
                                <div class="flex items-center space-x-4">
                                    <form action="{{ route('mitras.filter') }}" method="GET" class="flex items-center space-x-4">
                                        <div>
                                            <select id="mitra" name="mitra" class="w-64 px-4 py-2  rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                                                <option value="">Pilih Nama Mitra</option>
                                                @foreach($mitrasForDropdown as $mitra)
                                                    <option value="{{ $mitra->id_mitra }}" {{ request('mitra') == $mitra->id_mitra ? 'selected' : '' }}>
                                                        {{ $mitra->nama_lengkap }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </form>
                                    <button @click="isOpen = true" class="px-4 py-2 bg-orange text-white rounded-md hover:bg-orange-600 transition duration-300">
                                        Filter
                                    </button>
                                </div>
                                <button type="button" class="px-4 py-2 bg-orange rounded-md" onclick="openModal()">+ Tambah</button>
                            </div>
                        
                            <!-- Modal -->
                            <div x-show="isOpen" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
                                <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-lg">
                                    <h2 class="text-xl font-bold mb-6 text-gray-800">Filter</h2>
                        
                                    <!-- Form Filter -->
                                    <form action="{{ route('mitras.filter') }}" method="GET" class="space-y-4">
                        
                                        <!-- Dropdown untuk memilih kecamatan -->
                                        <div>
                                            <select id="kecamatan" name="kecamatan" class="w-full px-4 py-2  rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                                                <option value="">Pilih Kecamatan</option>
                                                @foreach($kecamatans as $id => $nama)
                                                    <option value="{{ $nama }}" {{ request('kecamatan') == $nama ? 'selected' : '' }}>
                                                        {{ $nama }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                        
                                        <!-- Tombol Apply Filter -->
                                        <button type="submit" class="w-full px-4 py-2 bg-orange text-white rounded-md hover:bg-orange-600 transition duration-300">
                                            Apply Filter
                                        </button>
                                    </form>
                        
                                    <!-- Tombol untuk menutup modal -->
                                    <button @click="isOpen = false" class="mt-4 w-full px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition duration-300">
                                        Close
                                    </button>
                                </div>
                            </div>
                        
                            
                        </div>
                    </div>  
                    <!-- JavaScript Tom Select -->
                <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
                <!-- Inisialisasi Tom Select -->
                <script>
                    document.addEventListener('DOMContentLoaded', function() {

                        new TomSelect('#kecamatan', {
                            placeholder: 'Pilih Kecamatan',
                            searchField: 'text',
                        });

                        new TomSelect('#mitra', {
                            placeholder: 'Pilih Mitra',
                            searchField: 'text',
                        });
                    });
                </script>
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
            <h2 class="text-xl font-bold mb-2">Import Mitra</h2>
            <p class="mb-2 text-red-700 text-sm">Pastikan format file excel yang diimport sesuai.</p>
            <form action="{{ route('upload.excelMitra') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="file" name="file" accept=".xlsx, .xls" class="border p-2 w-full">
                    <a href="{{ asset('addMitra.xlsx') }}" class="py-2 text-blue-500 hover:text-blue-600 text-xs">
                        Belum punya file excel? Download template disini.
                    </a>
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