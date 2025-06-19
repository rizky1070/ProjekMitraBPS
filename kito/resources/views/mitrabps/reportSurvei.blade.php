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
                            <button
                                class="px-2 py-1 bg-oren rounded-md no-print text-white font-medium hover:bg-orange-500 hover:shadow-lg transition-all duration-300"><a
                                    href="/ReportMitra" class=" no-print">Report Mitra</a></button>

                        </div>
                        <div class="text-center my-4 md:my-0">
                            <h1 class="text-2xl font-bold text-gray-800">Report Survei</h1>
                            <p class="text-gray-600 no-print">Data survei</p>
                        </div>
                        <div class="mt-4 md:mt-0">
                            <button onclick="exportData()"
                                class="px-4 py-2 bg-green-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 no-print">
                                <i class="fas fa-file-excel mr-2"></i>Export Excel
                            </button>
                        </div>
                    </div>

                    <!-- Filter Section -->
                    <div class="bg-white rounded-lg shadow-sm p-6 mb-6 no-print">
                        <form id="filterForm" action="{{ route('reports.survei.filter') }}" method="GET"
                            class="space-y-4">
                            <!-- [START] Filter Cari Survei -->
                            <div class="flex items-center relative">
                                <label for="nama_survei" class="w-32 text-lg font-semibold text-gray-800">Cari
                                    Survei</label>
                                <select name="nama_survei" id="nama_survei"
                                    class="w-full md:w-64 border rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 ml-2"
                                    {{ $namaSurveiOptions->isEmpty() ? 'disabled' : '' }}>
                                    <option value="">Semua Survei</option>
                                    @foreach ($namaSurveiOptions as $nama => $label)
                                        <option value="{{ $nama }}"
                                            @if (request('nama_survei') == $nama) selected @endif>{{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- [END] Filter Cari Survei -->
                            <div>
                                <h2 class="text-lg font-semibold text-gray-800 mb-4">Filter Data</h2>
                            </div>
                            <!-- Tahun Filter -->
                            <div class="flex flex-col space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 w-full">
                                    <!-- Tahun Filter -->
                                    <div class="flex flex-col">
                                        <label for="jadwal_kegiatan"
                                            class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                                        <select id="tahun" name="tahun"
                                            class="w-full border rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                                            <option value="">Semua Tahun</option>
                                            @foreach ($tahunOptions as $tahun)
                                                <option value="{{ $tahun }}"
                                                    {{ request('tahun') == $tahun ? 'selected' : '' }}>
                                                    {{ $tahun }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Bulan Filter -->
                                    <div class="flex flex-col">
                                        <label for="bulan"
                                            class="block text-sm font-medium text-gray-700 mb-1">Bulan</label>
                                        <select id="bulan" name="bulan"
                                            class="w-full border rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500"
                                            {{ empty($bulanOptions) ? 'disabled' : '' }}>
                                            <option value="">Pilih Bulan</option>
                                            @foreach ($bulanOptions as $key => $bulan)
                                                <option value="{{ $key }}"
                                                    {{ request('bulan') == $key ? 'selected' : '' }}>
                                                    {{ $bulan }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="flex flex-col">
                                        <label for="tim"
                                            class="block text-sm font-medium text-gray-700 mb-1">Tim</label>
                                        <select id="tim" name="tim"
                                            class="w-full border rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500"
                                            {{ $timOptions->isEmpty() ? 'disabled' : '' }}>
                                            <option value="">Semua Tim</option>
                                            @foreach ($timOptions as $timValue => $timLabel)
                                                <option value="{{ $timValue }}"
                                                    {{ request('tim') == $timValue ? 'selected' : '' }}>
                                                    {{ $timLabel }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Status Survei Filter -->
                                    <div class="flex flex-col">
                                        <label for="status_survei"
                                            class="block text-sm font-medium text-gray-700 mb-1">Status Survei</label>
                                        <select id="status_survei" name="status_survei"
                                            class="w-full border rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                                            <option value="">Semua Survei</option>
                                            <option value="aktif"
                                                {{ request('status_survei') == 'aktif' ? 'selected' : '' }}>Survei
                                                Aktif</option>
                                            <option value="tidak_aktif"
                                                {{ request('status_survei') == 'tidak_aktif' ? 'selected' : '' }}>
                                                Survei Tidak Aktif</option>
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
                                <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4"
                                    style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-users text-lg"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Total Survei</p>
                                    <p class="text-2xl font-bold text-gray-800">{{ $totalSurvei }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-purple-500">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4"
                                    style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-users-cog text-lg"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Total Tim</p>
                                    <p class="text-2xl font-bold text-gray-800">{{ $totalTim }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-green-500">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4"
                                    style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-check-circle text-lg"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Survei Aktif Di Ikuti Mitra</p>
                                    <p class="text-2xl font-bold text-gray-800">{{ $totalSurveiAktif }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ $totalSurvei > 0 ? round(($totalSurveiAktif / $totalSurvei) * 100, 1) : 0 }}%
                                        dari total</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-red-500">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-red-100 text-red-600 mr-4"
                                    style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-times-circle text-lg"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Survei Tidak Aktif Di Ikuti Mitra</p>
                                    <p class="text-2xl font-bold text-gray-800">{{ $totalSurveiTidakAktif }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ $totalSurvei > 0 ? round(($totalSurveiTidakAktif / $totalSurvei) * 100, 1) : 0 }}%
                                        dari total</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Table Section -->
                    <div class="border rounded-lg shadow-sm bg-white p-3">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col"
                                            class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-1">
                                            No</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Nama Survei</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-20">
                                            Tim</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-10">
                                            Jumlah Mitra</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-20">
                                            Jadwal Kegiatan</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-20">
                                            Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($surveis as $survei)
                                        <tr class="hover:bg-gray-50"
                                            style="border-top-width: 2px; border-color: #D1D5DB;">
                                            <td
                                                class="px-3 py-4 whitespace-nowrap text-center text-sm font-medium text-gray-900 w-1">
                                                {{ ($surveis->currentPage() - 1) * $surveis->perPage() + $loop->iteration }}
                                            </td>
                                            <td class="text-sm font-medium text-gray-900 whitespace-normal">
                                                <div class="ml-3 flex justify-left items-left text-left">
                                                    <a href="/editSurvei/{{ $survei->id_survei }}">
                                                        {{ $survei->nama_survei }}
                                                    </a>
                                                </div>
                                            </td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-700 w-20">
                                                {{ $survei->tim ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center w-10">
                                                @if ($survei->total_mitra > 0)
                                                    {{ $survei->total_mitra }} mitra
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center w-20">
                                                {{ \Carbon\Carbon::parse($survei->jadwal_kegiatan)->translatedFormat('j F Y') }}
                                                -
                                                {{ \Carbon\Carbon::parse($survei->jadwal_berakhir_kegiatan)->translatedFormat('j F Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center w-20">
                                                @if ($survei->total_mitra > 0)
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        Aktif Di Ikuti Mitra
                                                    </span>
                                                @else
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                        Tidak Aktif Di Ikuti Mitra
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- Pagination -->
                    @include('components.pagination', ['paginator' => $surveis])

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

            new TomSelect('#nama_survei', {
                placeholder: 'Cari Survei',
                searchField: 'text',
            });

            new TomSelect('#tim', {
                placeholder: 'Pilih Tim',
                searchField: 'text'
            });


            // Ambil elemen form dan select
            const filterForm = document.getElementById('filterForm');
            const tahunSelect = document.getElementById('tahun');
            const bulanSelect = document.getElementById('bulan');
            const statusSelect = document.getElementById('status_survei');
            const namaSurveiSelect = document.getElementById('nama_survei');
            const timSelect = document.getElementById('tim');

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
            namaSurveiSelect.addEventListener('change', submitForm);
            timSelect.addEventListener('change', submitForm);

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
