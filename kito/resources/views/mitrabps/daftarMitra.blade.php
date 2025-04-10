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
    
    @if (session('error'))
    <script>
    swal("Error!", "{{ session('error') }}", "error");
    </script>
    @endif
        <!-- component -->
    <div x-data="{ sidebarOpen: false }" class="flex h-screen">
        <x-sidebar></x-sidebar>
        <div class="flex flex-col flex-1 overflow-hidden">
            <x-navbar></x-navbar>
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200">
                <div class="container px-4 py-8 mx-auto">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-3xl font-medium text-black">Daftar Mitra</h3>
                        <button type="button" class="px-4 py-2 bg-orange rounded-md" onclick="openModal()">+ Tambah</button>
                    </div>
                    <div>
                        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                            <!-- Form Filter -->
                            <form action="{{ route('mitras.filter') }}" method="GET" class="space-y-4" id="filterForm">
                                <div class="flex items-center relative">
                                    <label for="nama_lengkap" class="w-32 text-lg font-semibold text-gray-800">Cari Mitra</label>
                                    <select name="nama_lengkap" id="nama_mitra" class="w-full md:w-64 
                                    border rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 ml-2" {{ empty($namaMitraOptions) ? 'disabled' : '' }}>
                                        <option value="">Semua Mitra</option>
                                        @foreach($namaMitraOptions as $nama => $label)
                                            <option value="{{ $nama }}" @if(request('nama_lengkap') == $nama) selected @endif>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- Year Row -->
                                <div>
                                   <h4 class="text-lg font-semibold text-gray-800">Filter Mitra</h4>
                                </div>
                                <div class="flex">
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-x-6 gap-y-4 w-full">
                                        <div class="flex items-center">
                                            <label for="tahun" class="w-32 text-sm font-medium text-gray-700">Tahun</label>
                                            <select name="tahun" id="tahun" class="w-full md:w-64 border rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 ml-2">
                                                <option value="">Semua Tahun</option>
                                                @foreach($tahunOptions as $year => $yearLabel)
                                                    <option value="{{ $year }}" @if(request('tahun') == $year) selected @endif>{{ $yearLabel }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Month Row -->
                                        <div class="flex items-center">
                                            <label for="bulan" class="w-32 text-sm font-medium text-gray-700">Bulan</label>
                                            <select name="bulan" id="bulan" class="w-full md:w-64 
                                            border rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 ml-2" {{ empty($bulanOptions) ? 'disabled' : '' }}>
                                                <option value="">Semua Bulan</option>
                                                @foreach($bulanOptions as $month => $monthName)
                                                    <option value="{{ $month }}" @if(request('bulan') == $month) selected @endif>{{ $monthName }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- District Row -->
                                        <div class="flex items-center">
                                            <label for="kecamatan" class="w-32 text-sm font-medium text-gray-700">Kecamatan</label>
                                            <select name="kecamatan" id="kecamatan" class="w-full md:w-64 
                                            border rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 ml-2" {{ empty($kecamatanOptions) ? 'disabled' : '' }}>
                                                <option value="">Semua Kecamatan</option>
                                                @foreach($kecamatanOptions as $kecam)
                                                    <option value="{{ $kecam->id_kecamatan }}" @if(request('kecamatan') == $kecam->id_kecamatan) selected @endif>
                                                        [{{ $kecam->kode_kecamatan }}] {{ $kecam->nama_kecamatan }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>                                
                            </form>
                        </div>
                </div>
                <!-- JavaScript Tom Select -->
                <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
                <!-- Inisialisasi Tom Select -->
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        new TomSelect('#nama_mitra', {
                            placeholder: 'Pilih Mitra',
                            searchField: 'text',
                        });
                        
                        new TomSelect('#tahun', {
                            placeholder: 'Pilih Tahun',
                            searchField: 'text',
                        });

                        new TomSelect('#bulan', {
                            placeholder: 'Pilih Bulan',
                            searchField: 'text',
                        });

                        new TomSelect('#kecamatan', {
                            placeholder: 'Pilih Kecamatan',
                            searchField: 'text',
                        });

                        // Auto submit saat filter berubah
                        const filterForm = document.getElementById('filterForm');
                        const tahunSelect = document.getElementById('tahun');
                        const bulanSelect = document.getElementById('bulan');
                        const kecamatanSelect = document.getElementById('kecamatan');
                        const mitraSelect = document.getElementById('nama_mitra');

                        // Ganti fungsi submitForm dengan ini
                        let timeout;
                        function submitForm() {
                            clearTimeout(timeout);
                            timeout = setTimeout(() => {
                                filterForm.submit();
                            }, 500); // Delay 500ms sebelum submit
                        }

                        // Tambahkan event listener untuk setiap select
                        tahunSelect.addEventListener('change', submitForm);
                        bulanSelect.addEventListener('change', submitForm);
                        kecamatanSelect.addEventListener('change', submitForm);
                        mitraSelect.addEventListener('change', submitForm);
                    });
                </script>
                <!-- Table -->
                    <div class="overflow-x-auto p-2 bg-white rounded-lg shadow-md">
                        <table class="w-full border-collapse border border-gray-350">
                            <thead>
                                <tr class="bg-gray-400">
                                    <th class="border border-gray-350 p-2">Nama Mitra</th>
                                    <th class="border border-gray-350 p-2">Kecamatan</th>
                                    <th class="border border-gray-350 p-2">Survei yang Diikuti</th>
                                    <th class="border border-gray-350 p-2">Tahun Mitra diterima</th>
                                    <th class="border border-gray-350 p-2">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($mitras as $mitra)
                                <tr class="bg-white hover:bg-gray-100">
                                    <td class="border border-gray-350 p-2">{{ $mitra->nama_lengkap }}</td>
                                    <td class="border border-gray-350 p-2 text-center">{{ $mitra->kecamatan->nama_kecamatan ?? '-' }}</td>
                                    <td class="border border-gray-350 p-2 text-center">{{ $mitra->mitra_survei_count }}</td>
                                    <td class="border border-gray-350 p-2 text-center">{{ $mitra->tahun }}</td>
                                    <td class="border border-gray-350 p-2 text-center">
                                        <a href="/profilMitra/{{ $mitra->id_mitra }}"  class="px-4 py-1 bg-orange text-black rounded-md">Lihat</a>
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
    <div id="uploadModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 hidden" style="z-index: 50;">
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
                    <button type="button" class="px-4 py-2 bg-gray-500 text-black rounded-md mr-2" onclick="closeModal()">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-orange text-black rounded-md">Unggah</button>
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