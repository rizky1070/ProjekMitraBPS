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
    <title>Report Mitra BPS</title>
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
</head>
<body class="h-full bg-gray-50">
    <!-- SweetAlert Logic -->
    @if (session('success'))
    <script>
    swal("Success!", "{{ session('success') }}", "success");
    </script>
    @endif

    @if ($errors->any()))
    <script>
    swal("Error!", "{{ $errors->first() }}", "error");
    </script>
    @endif
    
    <div x-data="{ sidebarOpen: false }" class="flex h-screen">
        <x-sidebar></x-sidebar>
        <div class="flex flex-col flex-1 overflow-hidden">
            <x-navbar></x-navbar>
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50">
                <div class="container px-4 py-6 mx-auto">
                    <!-- Title and Header -->
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                        <div>
                            <button class="px-2 py-1 bg-orange text-black rounded-md no-print"><a href="/ReportMitra" class=" no-print">Report Mitra</a></button>
                            <h1 class="text-2xl font-bold text-gray-800">Report Survei</h1>
                            <p class="text-gray-600 no-print">Data survei</p>
                            <h4 class="text-1xl font-bold text-gray-800 only-print">
                                Status: 
                                @if(request('status_survei')) {{ request('status_survei') == 'aktif' ? 'Survei Aktif' : 'Survei Tidak Aktif' }} @endif
                            </h4>
                            <h4 class="text-1xl font-bold text-gray-800 only-print">
                                Tahun: 
                                @if(request('tahun')) {{ request('tahun') }} @endif
                            </h4>
                            <h4 class="text-1xl font-bold text-gray-800 only-print">
                                Bulan: 
                                @if(request('bulan')) {{ \Carbon\Carbon::create()->month(request('bulan'))->format('F') }} @endif
                            </h4>
                        </div>
                        <div class="mt-4 md:mt-0">
                            <button onclick="window.print()" class="px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-500 no-print">
                                <i class="fas fa-print mr-2"></i>Print Report
                            </button>
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
                            <div class="flex">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-x-6 gap-y-4 w-full">
                                    <div class="flex items-center">
                                        <label for="jadwal_kegiatan" class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
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
                                        <select id="bulan" name="bulan" class="w-full md:w-64 border rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 ml-2"{{ empty($bulanOptions) ? 'disabled' : '' }}>
                                            <option value="">Pilih Bulan</option>
                                            @foreach($bulanOptions as $key => $bulan)
                                                <option value="{{ $key }}" {{ request('bulan') == $key ? 'selected' : '' }}>
                                                    {{ $bulan }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <!-- Partisipasi Filter -->
                                    <div class="flex items-center">
                                        <label for="status_survei" class="block text-sm font-medium text-gray-700 mb-1">Status Survei</label>
                                        <select id="status_survei" name="status_survei" class="w-full md:w-64 border rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 ml-2">
                                            <option value="">Semua Survei</option>
                                            <option value="aktif" {{ request('status_survei') == 'aktif' ? 'selected' : '' }}>Survei Aktif</option>
                                            <option value="tidak_aktif" {{ request('status_survei') == 'tidak_aktif' ? 'selected' : '' }}>Survei Tidak aktif</option>
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
                                    <p class="text-sm font-medium text-gray-500">Total Survei</p>
                                    <p class="text-2xl font-bold text-gray-800">{{ $totalSurvei }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-green-500">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                                    <i class="fas fa-check-circle text-lg"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Survei Aktif</p>
                                    <p class="text-2xl font-bold text-gray-800">{{ $totalSurveiAktif }}</p>
                                    <p class="text-xs text-gray-500">{{ $totalSurvei > 0 ? round(($totalSurveiAktif/$totalSurvei)*100, 1) : 0 }}% dari total</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-red-500">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-red-100 text-red-600 mr-4">
                                    <i class="fas fa-times-circle text-lg"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Survei Tidak Aktif</p>
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
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Survei</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kecamatan</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Mitra</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jadwal Kegiatan</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($surveis as $survei)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900 whitespace-normal break-words" style="max-width: 120px;">{{ $survei->nama_survei }}</div>
                                                    <div class="text-sm text-gray-500">{{ $survei->tim }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $survei->kecamatan->nama_kecamatan ?? '-' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $survei->mitra_survei_count > 0 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                {{ $survei->total_mitra }} mitra
                                            </span>
                                        </td>

                                        <td class="text-center whitespace-normal break-words" style="max-width: 120px;">{{ \Carbon\Carbon::parse($survei->jadwal_kegiatan )->translatedFormat('j F Y') }} - {{ \Carbon\Carbon::parse($survei->jadwal_berakhir_kegiatan )->translatedFormat('j F Y') }}</td>

                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($survei->total_mitra > 0)
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