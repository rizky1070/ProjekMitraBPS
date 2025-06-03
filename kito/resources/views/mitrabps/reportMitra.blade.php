<?php
$title = 'Report Mitra';
?>
@include('mitrabps.headerTemp')
    <style>
        .only-print {
            display: none;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            .only-print {
                display: flex;
            }
        }
    </style>
    @include('mitrabps.cuScroll')
</head>
<body class="h-full bg-gray-50">
    @include('mitrabps.reportSweetAlert')
    <div x-data="{ sidebarOpen: false }" class="flex h-screen">
        <x-sidebar></x-sidebar>
        <div class="flex flex-col flex-1 overflow-hidden">
            <x-navbar></x-navbar>
            <main class="cuScrollFilter flex-1 overflow-x-hidden bg-gray-50">
                <div class="container px-4 py-6 mx-auto">
                    <!-- Title and Header -->
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                        <div>
                            <button class="px-2 py-1 bg-oren rounded-md no-print text-white font-medium hover:bg-orange-500 hover:shadow-lg transition-all duration-300" ><a href="/ReportSurvei">Report Survei</a></button>
                            <h1 class="text-2xl font-bold text-gray-800">Report Mitra</h1>
                            <p class="text-gray-600 no-print">Data partisipasi mitra dalam survei BPS</p>
                        </div>
                        <div class="mt-4 md:mt-0">
                            <button onclick="exportData()" class="px-4 py-2 bg-green-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 no-print">
                                <i class="fas fa-file-excel mr-2"></i>Export Excel
                            </button>
                        </div>
                    </div>
                    <!-- Filter Section -->
                    <div class="bg-white rounded-lg shadow-sm p-6 mb-6 no-print">
                        <form id="filterForm" action="{{ route('reports.Mitra.filter') }}" method="GET" class="cuScrollFilter space-y-4">
                            <!-- Tahun Filter -->
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
                            <div>
                                <h2 class="text-lg font-semibold text-gray-800 mb-4">Filter Data</h2>
                            </div>
                            <div class="flex">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-x-6 gap-y-4 w-full">
                                    <div class="flex items-center">
                                        <label for="tahun" class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                                        <select id="tahun" name="tahun" class="w-full md:w-64 border rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 ml-2">
                                            <option value="">Semua Tahun</option>
                                            @foreach($tahunOptions as $tahun)
                                                <option value="{{ $tahun }}" {{ request('tahun') == $tahun ? 'selected' : '' }}>
                                                    {{ $tahun }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Bulan Filter -->
                                    <div class="flex items-center">
                                        <label for="bulan" class="block text-sm font-medium text-gray-700 mb-1">Bulan</label>
                                        <select id="bulan" name="bulan" class="w-full md:w-64 border rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 ml-2" {{ empty($bulanOptions) ? 'disabled' : '' }}>
                                            <option value="">Semua Bulan</option>
                                            @foreach($bulanOptions as $key => $value)
                                                <option value="{{ $key }}" {{ request('bulan') == $key ? 'selected' : '' }}>
                                                    {{ $value }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
        
                                    <!-- Partisipasi Filter -->
                                    <div class="flex items-center">
                                        <label for="status_mitra" class="block text-sm font-medium text-gray-700 mb-1">Status Partisipasi</label>
                                        <select id="status_mitra" name="status_mitra" class="w-full md:w-64 border rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 ml-2">
                                            <option value="">Semua Mitra</option>
                                            <option value="ikut" {{ request('status_mitra') == 'ikut' ? 'selected' : '' }}>Mengikuti Survei</option>
                                            <option value="tidak_ikut" {{ request('status_mitra') == 'tidak_ikut' ? 'selected' : '' }}>Tidak Mengikuti Survei</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                        </form>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-blue-500">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                                    <i class="fas fa-users text-lg"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Total Mitra</p>
                                    <p class="text-2xl font-bold text-gray-800">{{ $totalMitra }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-green-500">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                                    <i class="fas fa-check-circle text-lg"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Mengikuti Survei</p>
                                    <p class="text-2xl font-bold text-gray-800">{{ $totalIkutSurvei }}</p>
                                    <p class="text-xs text-gray-500">{{ $totalMitra > 0 ? round(($totalIkutSurvei/$totalMitra)*100, 1) : 0 }}% dari total</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-red-500">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-red-100 text-red-600 mr-4">
                                    <i class="fas fa-times-circle text-lg"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Tidak Mengikuti</p>
                                    <p class="text-2xl font-bold text-gray-800">{{ $totalTidakIkutSurvei }}</p>
                                    <p class="text-xs text-gray-500">{{ $totalMitra > 0 ? round(($totalTidakIkutSurvei/$totalMitra)*100, 1) : 0 }}% dari total</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Table Section -->
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr class="text-center">
                                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Mitra</th>
                                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Kecamatan</th>
                                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Survei Diikuti</th>
                                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Masa kontrak</th>
                                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($mitras as $mitra)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-center">
                                            <div class="flex flex-col items-center justify-center">
                                                <div class="text-sm font-medium text-gray-900 whitespace-normal break-words"><a href="/profilMitra/{{ $mitra->id_mitra }}">{{ $mitra->nama_lengkap }}</a></div>
                                                <div class="text-sm text-gray-500">{{ $mitra->email_mitra }}</div>
                                                <div class="text-sm text-gray-500">{{ $mitra->no_hp_mitra }}</div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <div class="text-sm text-gray-900">{{ $mitra->kecamatan->nama_kecamatan ?? '-' }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $mitra->mitra_survei_count > 0 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                {{ $mitra->total_survei }} survei
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center whitespace-normal break-words">
                                            {{ \Carbon\Carbon::parse($mitra->tahun)->translatedFormat('j F Y') }} - 
                                            {{ \Carbon\Carbon::parse($mitra->tahun_selesai)->translatedFormat('j F Y') }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @if($mitra->total_survei > 0)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Aktif
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    Tidak Aktif
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- Pagination -->
                        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                            @include('components.pagination', ['paginator' => $mitras])
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            new TomSelect('#nama_mitra', {
                placeholder: 'Cari Mitra',
                searchField: 'text',
            });

            new TomSelect('#bulan', {
                placeholder: 'Pilih Bulan',
                searchField: 'text',
            });

            new TomSelect('#tahun', {
                placeholder: 'Pilih Tahun',
                searchField: 'text',
            });

            new TomSelect('#status_mitra', {
                placeholder: 'Pilih Status',
                searchField: 'text',
            });
               // Ambil elemen form dan select
            const filterForm = document.getElementById('filterForm');
            const tahunSelect = document.getElementById('tahun');
            const bulanSelect = document.getElementById('bulan');
            const statusSelect = document.getElementById('status_mitra');
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
            statusSelect.addEventListener('change', submitForm);
            mitraSelect.addEventListener('change', submitForm);
        });
    
        function exportData() {
            // Ambil parameter filter dari form
            const form = document.getElementById('filterForm');
            const formData = new FormData(form);
            const params = new URLSearchParams(formData).toString();
            
            // Redirect ke route export dengan parameter filter
            window.location.href = `/ReportMitra/export-mitra?${params}`;
        }
    </script>
</body>
</html>