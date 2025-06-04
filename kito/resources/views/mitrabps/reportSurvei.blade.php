<?php
$title = 'Report Survei';
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
                display: block;
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
                            <button class="px-2 py-1 bg-oren rounded-md no-print text-white font-medium hover:bg-orange-500 hover:shadow-lg transition-all duration-300"><a href="/ReportMitra" class=" no-print">Report Mitra</a></button>
                            <h1 class="text-2xl font-bold text-gray-800">Report Survei</h1>
                            <p class="text-gray-600 no-print">Data survei</p>
                        </div>
                        <div class="mt-4 md:mt-0">
                            <button onclick="exportData()" class="px-4 py-2 bg-green-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 no-print">
                                <i class="fas fa-file-excel mr-2"></i>Export Excel
                            </button>
                        </div>
                    </div>

                    <!-- Filter Section -->
                    <div class="bg-white rounded-lg shadow-sm p-6 mb-6 no-print">
                        <form id="filterForm" action="{{ route('reports.survei.filter') }}" method="GET" class="space-y-4">
                            <div>
                                <h2 class="text-lg font-semibold text-gray-800 mb-4">Filter Data</h2>
                            </div>
                            <!-- Tahun Filter -->
                            <div class="flex flex-col space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 w-full">
                                    <!-- Tahun Filter -->
                                    <div class="flex flex-col">
                                        <label for="jadwal_kegiatan" class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                                        <select id="tahun" name="tahun" class="w-full border rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                                            <option value="">Semua Tahun</option>
                                            @foreach($tahunOptions as $tahun)
                                                <option value="{{ $tahun }}" {{ request('tahun') == $tahun ? 'selected' : '' }}>{{ $tahun }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Bulan Filter -->
                                    <div class="flex flex-col">
                                        <label for="bulan" class="block text-sm font-medium text-gray-700 mb-1">Bulan</label>
                                        <select id="bulan" name="bulan" class="w-full border rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500" {{ empty($bulanOptions) ? 'disabled' : '' }}>
                                            <option value="">Pilih Bulan</option>
                                            @foreach($bulanOptions as $key => $bulan)
                                                <option value="{{ $key }}" {{ request('bulan') == $key ? 'selected' : '' }}>{{ $bulan }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Status Survei Filter -->
                                    <div class="flex flex-col">
                                        <label for="status_survei" class="block text-sm font-medium text-gray-700 mb-1">Status Survei</label>
                                        <select id="status_survei" name="status_survei" class="w-full border rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                                            <option value="">Semua Survei</option>
                                            <option value="aktif" {{ request('status_survei') == 'aktif' ? 'selected' : '' }}>Survei Aktif</option>
                                            <option value="tidak_aktif" {{ request('status_survei') == 'tidak_aktif' ? 'selected' : '' }}>Survei Tidak Aktif</option>
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
                                <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-users text-lg"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Total Survei</p>
                                    <p class="text-2xl font-bold text-gray-800">{{ $totalSurvei }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-green-500">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-check-circle text-lg"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Survei Aktif Di Ikuti Mitra</p>
                                    <p class="text-2xl font-bold text-gray-800">{{ $totalSurveiAktif }}</p>
                                    <p class="text-xs text-gray-500">{{ $totalSurvei > 0 ? round(($totalSurveiAktif/$totalSurvei)*100, 1) : 0 }}% dari total</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-red-500">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-red-100 text-red-600 mr-4" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-times-circle text-lg"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Survei Tidak Aktif Di Ikuti Mitra</p>
                                    <p class="text-2xl font-bold text-gray-800">{{ $totalSurveiTidakAktif }}</p>
                                    <p class="text-xs text-gray-500">{{ $totalSurvei > 0 ? round(($totalSurveiTidakAktif/$totalSurvei)*100, 1) : 0 }}% dari total</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Table Section -->
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Survei</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Mitra</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Jadwal Kegiatan</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($surveis as $survei)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="text-sm font-medium text-gray-900 whitespace-normal break-words">{{ $survei->nama_survei }}</div>
                                            <div class="text-sm text-gray-500">{{ $survei->tim }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $survei->mitra_survei_count > 0 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $survei->total_mitra }} mitra
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center whitespace-normal break-words">
                                        <div class="flex flex-col">
                                            <span>{{ \Carbon\Carbon::parse($survei->jadwal_kegiatan)->translatedFormat('j F Y') }} - {{ \Carbon\Carbon::parse($survei->jadwal_berakhir_kegiatan)->translatedFormat('j F Y') }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($survei->total_mitra > 0)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Aktif Di Ikuti Mitra
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                Tidak Aktif Di Ikuti Mitra
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
                            @include('components.pagination', ['paginator' => $surveis])
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            new TomSelect('#bulan', {
                placeholder: 'Pilih Bulan',
                searchField: 'text',
            });

            new TomSelect('#tahun', {
                placeholder: 'Pilih Tahun',
                searchField: 'text',
            });

            new TomSelect('#status_survei', {
                placeholder: 'Pilih Status',
                searchField: 'text',
            });

            // Ambil elemen form dan select
            const filterForm = document.getElementById('filterForm');
            const tahunSelect = document.getElementById('tahun');
            const bulanSelect = document.getElementById('bulan');
            const statusSelect = document.getElementById('status_survei');

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
        });

        function exportData() {
            // Ambil parameter filter dari form
            const form = document.getElementById('filterForm');
            const formData = new FormData(form);
            const params = new URLSearchParams(formData).toString();
            
            // Redirect ke route export dengan parameter filter
            window.location.href = `/ReportSurvei/export-survei?${params}`;
        }
    </script>
</body>
</html>