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
        @media print {
            .no-print {
                display: none !important;
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
                            <h1 class="text-2xl font-bold text-gray-800">Report Mitra</h1>
                            <p class="text-gray-600">Data partisipasi mitra dalam survei BPS</p>
                        </div>
                        <div class="mt-4 md:mt-0">
                            <button onclick="window.print()" class="px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-orange-500 no-print">
                                <i class="fas fa-print mr-2"></i>Print Report
                            </button>
                        </div>
                    </div>

                    <!-- Filter Section -->
                    <div class="bg-white rounded-lg shadow-sm p-6 mb-6 no-print">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Filter Data</h2>
                        <form id="filterForm" action="{{ route('reports.Mitra.filter') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <!-- Tahun Filter -->
                            <div>
                                <label for="tahun" class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                                <select id="tahun" name="tahun" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500">
                                    <option value="">Semua Tahun</option>
                                    @foreach($tahunOptions as $tahun)
                                        <option value="{{ $tahun }}" {{ request('tahun') == $tahun ? 'selected' : '' }}>
                                            {{ $tahun }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Bulan Filter -->
                            <div>
                                <label for="bulan" class="block text-sm font-medium text-gray-700 mb-1">Bulan</label>
                                <select id="bulan" name="bulan" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500">
                                    <option value="">Semua Bulan</option>
                                    @foreach($bulanOptions as $bulan)
                                        <option value="{{ $bulan }}" {{ request('bulan') == $bulan ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create()->month($bulan)->format('F') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Partisipasi Filter -->
                            <div>
                                <label for="status_mitra" class="block text-sm font-medium text-gray-700 mb-1">Status Partisipasi</label>
                                <select id="status_mitra" name="status_mitra" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500">
                                    <option value="">Semua Mitra</option>
                                    <option value="ikut" {{ request('status_mitra') == 'ikut' ? 'selected' : '' }}>Mengikuti Survei</option>
                                    <option value="tidak_ikut" {{ request('status_mitra') == 'tidak_ikut' ? 'selected' : '' }}>Tidak Mengikuti Survei</option>
                                </select>
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
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Mitra</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Domisili</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Survei Diikuti</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tahun</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($mitras as $mitra)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-orange-100 flex items-center justify-center">
                                                    <span class="text-orange-600 font-medium">{{ strtoupper(substr($mitra->nama_lengkap, 0, 1)) }}</span>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">{{ $mitra->nama_lengkap }}</div>
                                                    <div class="text-sm text-gray-500">{{ $mitra->email_mitra }}</div>
                                                    <div class="text-sm text-gray-500">{{ $mitra->no_hp_mitra }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $mitra->kecamatan->nama_kecamatan ?? '-' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $mitra->mitra_survei_count > 0 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                {{ $mitra->mitra_survei_count }} survei
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $mitra->tahun }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($mitra->mitra_survei_count > 0)
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
    </script>
</body>
</html>