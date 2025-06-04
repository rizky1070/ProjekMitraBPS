<?php
$title = 'Daftar Mitra';
?>
@include('mitrabps.headerTemp')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" href="/Logo BPS.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    @include('mitrabps.cuScroll')
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
            <main class="cuScrollGlobalY flex-1 overflow-x-hidden bg-gray-200">
                <div class="container px-4 py-4 mx-auto">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-3xl font-medium text-black">Daftar Mitra</h3>
                        <button type="button" class="px-4 py-2 bg-oren rounded-md text-white font-medium hover:bg-orange-500 hover:shadow-lg transition-all duration-300" onclick="openModal()">+ Tambah</button>
                    </div>
                    <div>
                        <div class="cuScrollFilter bg-white rounded-lg shadow-sm p-6 mb-6">
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
                            @if(session('import_errors'))
                            <div class="mb-4 mt-2 p-3 bg-red-100 border-l-4 border-red-500 text-red-700">
                                <h4 class="font-bold">Mitra yang gagal diimport:</h4>
                                <ul class="cuScrollError list-disc pl-5">
                                    @foreach(session('import_errors') as $error)
                                        <li class="text-sm">{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                        </div>
                </div>
                <!-- JavaScript Tom Select -->
                <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
                <!-- Inisialisasi Tom Select -->
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // Fungsi untuk inisialisasi TomSelect
                        function initTomSelect() {
                            new TomSelect('#nama_mitra', {
                                placeholder: 'Cari Mitra',
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
                        }

                        // Inisialisasi pertama kali
                        initTomSelect();

                        // Auto submit saat filter berubah
                        const filterForm = document.getElementById('filterForm');
                        filterForm.addEventListener('change', function() {
                            setTimeout(() => {
                                filterForm.submit();
                            }, 500);
                        });
                    });
                </script>
                <!-- Table -->
                <div class="border rounded-lg shadow-sm bg-white bg-white p-3">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Mitra</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Kecamatan</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Survei yang Diikuti</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Masa Kontrak</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total Honor</th>
                                    
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($mitras as $mitra)
                                    <tr class="hover:bg-gray-50" style=" border-top-width: 2px; border-color: #D1D5DB;">
                                        @php
                                            $bgStatus = $mitra->status_pekerjaan == 1 ? 'bg-red-500' : 'bg-green-500';
                                        @endphp
                                        <td class="text-sm font-medium text-gray-900 whitespace-normal break-words" style="max-width: 120px;">
                                            <div class="ml-3 flex justify-center items-center text-center">
                                                <p class="{{ $bgStatus }} m-1 p-1 border rounded-lg"></p>
                                                <a href="/profilMitra/{{ $mitra->id_mitra }}" class="hover:underline transition duration-300 ease-in-out " style="text-decoration-color: #FFA500; text-decoration-thickness: 3px;">
                                                    {{ $mitra->nama_lengkap }}
                                                </a>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            {{ $mitra->kecamatan->nama_kecamatan ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            {{ $mitra->total_survei }}
                                        </td>
                                        <td class="text-center whitespace-normal break-words" style="max-width: 120px;">
                                            {{ \Carbon\Carbon::parse($mitra->tahun)->translatedFormat('j F Y') }} - 
                                            {{ \Carbon\Carbon::parse($mitra->tahun_selesai)->translatedFormat('j F Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            @if($mitra->total_survei > 0)
                                                Rp {{ number_format($mitra->total_honor, 0, ',', '.') }}
                                            @else
                                                -
                                            @endif                                        
                                        </td>
                                        
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @include('components.pagination', ['paginator' => $mitras])
            </main>
        </div>
    </div>
    <!-- Modal Upload Excel -->
    <div id="uploadModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 hidden" style="z-index: 50;">
        <div class="bg-white p-4 sm:p-6 rounded-lg shadow-lg w-11/12 sm:w-3/4 md:w-2/3 lg:w-1/2 xl:w-1/3 mx-2">
            <h2 class="text-lg sm:text-xl font-bold mb-2">Import Mitra</h2>
            <p class="mb-2 text-red-700 text-xs sm:text-sm font-bold">Pastikan format file excel yang diimport sesuai!</p>
            <form action="{{ route('upload.excelMitra') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="file" name="file" accept=".xlsx, .xls" class="border p-2 w-full text-xs sm:text-sm">
                <p class="py-2 text-xs sm:text-sm">Belum punya file excel?  
                    <a href="{{ asset('addMitra.xlsx') }}" class="text-blue-500 hover:text-blue-600 font-bold">
                        Download template disini.
                    </a>
                </p>
                <div class="flex justify-end mt-4 space-x-2">
                    <button type="button" class="px-3 py-1 sm:px-4 sm:py-2 bg-gray-500 rounded-md text-white text-xs sm:text-sm font-medium hover:bg-gray-600 hover:shadow-lg transition-all duration-300" onclick="closeModal()">Batal</button>
                    <button type="submit" class="px-3 py-1 sm:px-4 sm:py-2 bg-oren rounded-md text-white text-xs sm:text-sm font-medium hover:bg-orange-500 hover:shadow-lg transition-all duration-300">Unggah</button>
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