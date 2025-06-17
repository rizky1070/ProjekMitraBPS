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
    @if (session('success'))
        <script>
            swal("Success!", "{{ session('success') }}", "success");
        </script>
    @endif

    @if (session('error'))
        <script>
            swal("Error!", "{!! session('error') !!}", "error");
        </script>
    @endif
    @include('mitrabps.reportSweetAlert')
    <div x-data="{ sidebarOpen: false }" class="flex h-screen">
        <x-sidebar></x-sidebar>
        <div class="flex flex-col flex-1 overflow-hidden">
            <x-navbar></x-navbar>
            <main class="cuScrollFilter flex-1 overflow-x-hidden bg-gray-50">
                <div class="container px-4 py-6 mx-auto">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                        <div>
                            <button
                                class="px-2 py-1 bg-oren rounded-md no-print text-white font-medium hover:bg-orange-500 hover:shadow-lg transition-all duration-300"><a
                                    href="/ReportSurvei">Report Survei</a></button>
                            <h1 class="text-2xl font-bold text-gray-800">Report Mitra</h1>
                            <p class="text-gray-600 no-print">Data partisipasi mitra dalam survei BPS</p>
                        </div>
                        <div class="mt-4 md:mt-0">
                            <button onclick="exportData()"
                                class="px-4 py-2 bg-green-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 no-print">
                                <i class="fas fa-file-excel mr-2"></i>Export Excel
                            </button>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow-sm p-6 mb-6 no-print">
                        <form id="filterForm" action="{{ route('reports.Mitra.filter') }}" method="GET"
                            class="cuScrollFilter space-y-4">
                            <div class="flex items-center relative">
                                <label for="nama_lengkap" class="w-32 text-lg font-semibold text-gray-800">Cari
                                    Mitra</label>
                                <select name="nama_lengkap" id="nama_mitra"
                                    class="w-full md:w-64
                                border rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 ml-2"
                                    {{ empty($namaMitraOptions) ? 'disabled' : '' }}>
                                    <option value="">Semua Mitra</option>
                                    @foreach ($namaMitraOptions as $nama => $label)
                                        <option value="{{ $nama }}"
                                            @if (request('nama_lengkap') == $nama) selected @endif>{{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-gray-800 mb-4">Filter Data</h2>
                            </div>
                            <div class="flex flex-col space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 w-full">

                                    <div class="flex flex-col">
                                        <label for="mode_export"
                                            class="block text-sm font-medium text-gray-700 mb-1">Mode Export</label>
                                        <select id="mode_export" name="mode_export"
                                            class="w-full border rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                                            <option value="detail"
                                                {{ request('mode_export', 'detail') == 'detail' ? 'selected' : '' }}>
                                                Export Detail
                                            </option>
                                            <option value="per_bulan"
                                                {{ request('mode_export') == 'per_bulan' ? 'selected' : '' }}>
                                                Export per Bulan
                                            </option>
                                            <option value="detail"
                                                {{ request('mode_export', 'detail') == 'detail' ? 'selected' : '' }}>
                                                Export Detail
                                            </option>
                                            <option value="per_tim"
                                                {{ request('mode_export') == 'per_tim' ? 'selected' : '' }}>
                                                Export per Tim
                                            </option>
                                        </select>
                                    </div>
                                    <div class="flex flex-col">
                                        <label for="tahun"
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

                                    <div class="flex flex-col">
                                        <label for="bulan"
                                            class="block text-sm font-medium text-gray-700 mb-1">Bulan</label>
                                        <select id="bulan" name="bulan"
                                            class="w-full border rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500"
                                            {{ empty($bulanOptions) ? 'disabled' : '' }}>
                                            {{ request('mode_export', 'detail') != 'detail' || !$request->filled('tahun') ? 'disabled' : '' }}>
                                            <option value="">Semua Bulan</option>
                                            @foreach ($bulanOptions as $key => $value)
                                                <option value="{{ $key }}"
                                                    {{ request('bulan') == $key ? 'selected' : '' }}>
                                                    {{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="flex flex-col">
                                        <label for="honor_lebih_dari_4jt"
                                            class="block text-sm font-medium text-gray-700 mb-1">Honor > 4 Juta</label>
                                        <select id="honor_lebih_dari_4jt" name="honor_lebih_dari_4jt"
                                            class="w-full border rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500"
                                            {{ request('mode_export', 'detail') != 'detail' || !($request->filled('tahun') && $request->filled('bulan')) ? 'disabled' : '' }}>
                                            <option value="">Semua</option>
                                            <option value="ya"
                                                {{ request('honor_lebih_dari_4jt') == 'ya' ? 'selected' : '' }}>Ya
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 w-full">
                                    <div class="flex flex-col">
                                        <label for="partisipasi_lebih_dari_satu"
                                            class="block text-sm font-medium text-gray-700 mb-1">Mitra > 1
                                            Survei</label>
                                        <select id="partisipasi_lebih_dari_satu" name="partisipasi_lebih_dari_satu"
                                            class="w-full border rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500"
                                            {{ request('mode_export', 'detail') != 'detail' || !($request->filled('tahun') && $request->filled('bulan')) ? 'disabled' : '' }}>
                                            <option value="">Semua</option>
                                            <option value="ya"
                                                {{ request('partisipasi_lebih_dari_satu') == 'ya' ? 'selected' : '' }}>
                                                Ya</option>
                                        </select>
                                    </div>

                                    <div class="flex flex-col">
                                        <label for="jenis_kelamin"
                                            class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                                        <select id="jenis_kelamin" name="jenis_kelamin"
                                            class="w-full border rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                                            <option value="">Semua</option>
                                            <option value="1"
                                                {{ request('jenis_kelamin') == '1' ? 'selected' : '' }}>Laki-laki
                                            </option>
                                            <option value="2"
                                                {{ request('jenis_kelamin') == '2' ? 'selected' : '' }}>Perempuan
                                            </option>
                                        </select>
                                    </div>
                                    <div class="flex flex-col">
                                        <label for="kecamatan"
                                            class="block text-sm font-medium text-gray-700 mb-1">Kecamatan</label>
                                        <select id="kecamatan" name="kecamatan"
                                            class="w-full border rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500"
                                            {{ empty($kecamatanOptions) ? 'disabled' : '' }}>
                                            <option value="">Semua Kecamatan</option>
                                            @foreach ($kecamatanOptions as $kecam)
                                                <option value="{{ $kecam->id_kecamatan }}"
                                                    @if (request('kecamatan') == $kecam->id_kecamatan) selected @endif>
                                                    [{{ $kecam->kode_kecamatan }}] {{ $kecam->nama_kecamatan }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="flex flex-col">
                                        <label for="status_mitra"
                                            class="block text-sm font-medium text-gray-700 mb-1">Status
                                            Partisipasi</label>
                                        <select id="status_mitra" name="status_mitra"
                                            class="w-full border rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                                            <option value="">Semua Mitra</option>
                                            <option value="ikut"
                                                {{ request('status_mitra') == 'ikut' ? 'selected' : '' }}>Mengikuti
                                                Survei</option>
                                            <option value="tidak_ikut"
                                                {{ request('status_mitra') == 'tidak_ikut' ? 'selected' : '' }}>Tidak
                                                Mengikuti Survei</option>
                                        </select>
                                    </div>

                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 w-full">
                                    <div class="flex flex-col">
                                        <label for="status_pekerjaan"
                                            class="block text-sm font-medium text-gray-700 mb-1">Status
                                            Pekerjaan</label>
                                        <select id="status_pekerjaan" name="status_pekerjaan"
                                            class="w-full border rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                                            <option value="">Semua Status</option>
                                            <option value="0"
                                                {{ request('status_pekerjaan') == '0' ? 'selected' : '' }}>Bisa
                                                Mengikuti Survei</option>
                                            <option value="1"
                                                {{ request('status_pekerjaan') == '1' ? 'selected' : '' }}>Tidak Bisa
                                                Mengikuti Survei</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-blue-500">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4"
                                    style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-users text-lg"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Total Mitra</p>
                                    <p class="text-2xl font-bold text-gray-800">{{ $totalMitra }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- [START] KARTU STATISTIK JENIS KELAMIN -->
                        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-sky-500">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-sky-100 text-sky-600 mr-4"
                                    style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-male text-lg"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Mitra Laki-laki</p>
                                    <p class="text-2xl font-bold text-gray-800">{{ $totalLaki }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ $totalMitra > 0 ? round(($totalLaki / $totalMitra) * 100, 1) : 0 }}% dari
                                        total
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-pink-500">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-pink-100 text-pink-600 mr-4"
                                    style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-female text-lg"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Mitra Perempuan</p>
                                    <p class="text-2xl font-bold text-gray-800">{{ $totalPerempuan }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ $totalMitra > 0 ? round(($totalPerempuan / $totalMitra) * 100, 1) : 0 }}%
                                        dari
                                        total</p>
                                </div>
                            </div>
                        </div>
                        <!-- [END] KARTU STATISTIK JENIS KELAMIN -->

                        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-green-500">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4"
                                    style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-check-circle text-lg"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Aktif Mengikuti Survei</p>
                                    <p class="text-2xl font-bold text-gray-800">{{ $totalIkutSurvei }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ $totalMitra > 0 ? round(($totalIkutSurvei / $totalMitra) * 100, 1) : 0 }}%
                                        dari
                                        total</p>
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
                                    <p class="text-sm font-medium text-gray-500">Tidak Aktif Mengikuti Survei</p>
                                    <p class="text-2xl font-bold text-gray-800">{{ $totalTidakIkutSurvei }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ $totalMitra > 0 ? round(($totalTidakIkutSurvei / $totalMitra) * 100, 1) : 0 }}%
                                        dari total</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-purple-500">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4"
                                    style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-check-circle text-lg"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Bisa Mengikuti Survei</p>
                                    <p class="text-2xl font-bold text-gray-800">{{ $totalBisaIkutSurvei }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ $totalMitra > 0 ? round(($totalBisaIkutSurvei / $totalMitra) * 100, 1) : 0 }}%
                                        dari total</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-yellow-500">
                            <div class="flex items-center">
                                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4"
                                    style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-times-circle text-lg"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Tidak Bisa Mengikuti Survei</p>
                                    <p class="text-2xl font-bold text-gray-800">{{ $totalTidakBisaIkutSurvei }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ $totalMitra > 0 ? round(($totalTidakBisaIkutSurvei / $totalMitra) * 100, 1) : 0 }}%
                                        dari total</p>
                                </div>
                            </div>
                        </div>

                        <!-- [START] KARTU STATISTIK KONDISIONAL -->
                        @if ($request->filled('bulan'))
                            <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-cyan-500">
                                <div class="flex items-center">
                                    <div class="p-3 rounded-full bg-cyan-100 text-cyan-600 mr-4"
                                        style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-layer-group text-lg"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Mitra > 1 Survei</p>
                                        <p class="text-2xl font-bold text-gray-800">
                                            {{ $totalMitraLebihDariSatuSurvei }}</p>
                                        <p class="text-xs text-gray-500">Pada bulan yang difilter</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-orange-500">
                                <div class="flex items-center">
                                    <div class="p-3 rounded-full bg-orange-100 text-orange-600 mr-4"
                                        style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-wallet text-lg"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Honor > 4 Juta</p>
                                        <p class="text-2xl font-bold text-gray-800">{{ $totalMitraHonorLebihDari4Jt }}
                                        </p>
                                        <p class="text-xs text-gray-500">Pada bulan yang difilter</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <!-- [END] KARTU STATISTIK KONDISIONAL -->

                    </div>



                    <!-- Table Section -->
                    <div class="border rounded-lg shadow-sm bg-white p-3">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            No</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Nama Mitra</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Kecamatan</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Survei Diikuti</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Mitra Tahun</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Skor Kinerja</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($mitras as $mitra)
                                        <tr class="hover:bg-gray-50"
                                            style="border-top-width: 2px; border-color: #D1D5DB;">
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-gray-900">
                                                {{ $loop->iteration }}
                                            </td>
                                            <td class="text-sm font-medium text-gray-900 whitespace-normal break-words"
                                                style="max-width: 120px;">
                                                <div class="ml-3 flex justify-left items-left text-left">
                                                    <a href="/profilMitra/{{ $mitra->id_mitra }}">
                                                        {{ $mitra->nama_lengkap }}
                                                    </a>
                                                </div>
                                                <div class="ml-3 text-sm text-gray-500 text-left">
                                                    {{ $mitra->no_hp_mitra }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                {{ $mitra->kecamatan->nama_kecamatan ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                @if ($mitra->total_survei > 0)
                                                    {{ $mitra->total_survei }} survei
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="text-center whitespace-normal break-words"
                                                style="max-width: 120px;">
                                                {{ \Carbon\Carbon::parse($mitra->tahun)->translatedFormat('Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                @if ($mitra->rata_rata_nilai)
                                                    @php
                                                        // Hitung skor kinerja, dengan maksimal nilai 5
                                                        $skor_kinerja = ($mitra->rata_rata_nilai / 5) * 100;
                                                    @endphp
                                                    {{-- Tampilkan skor sebagai persentase --}}
                                                    <span
                                                        class="font-semibold">{{ number_format($skor_kinerja, 1) }}%</span>

                                                    {{-- Tampilkan nilai rata-rata sebagai referensi --}}
                                                    <span class="block text-xs text-gray-500">
                                                        ({{ number_format($mitra->rata_rata_nilai, 1) }}/5)
                                                    </span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                @if ($mitra->total_survei > 0)
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        Aktif Mengikuti Survei
                                                    </span>
                                                @else
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                        Tidak Aktif Mengikuti Survei
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
                    @include('components.pagination', ['paginator' => $mitras])

                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inisialisasi TomSelect untuk semua filter
            const tomSelects = {};
            tomSelects.nama_mitra = new TomSelect('#nama_mitra', {
                placeholder: 'Cari Mitra',
                searchField: 'text'
            });
            tomSelects.bulan = new TomSelect('#bulan', {
                placeholder: 'Pilih Bulan',
                searchField: 'text'
            });
            tomSelects.tahun = new TomSelect('#tahun', {
                placeholder: 'Pilih Tahun',
                searchField: 'text'
            });
            tomSelects.status_mitra = new TomSelect('#status_mitra', {
                placeholder: 'Pilih Status',
                searchField: 'text'
            });
            tomSelects.status_pekerjaan = new TomSelect('#status_pekerjaan', {
                placeholder: 'Pilih Status',
                searchField: 'text'
            });
            tomSelects.kecamatan = new TomSelect('#kecamatan', {
                placeholder: 'Pilih Kecamatan',
                searchField: 'text'
            });
            tomSelects.partisipasi_lebih_dari_satu = new TomSelect('#partisipasi_lebih_dari_satu', {
                placeholder: 'Pilih',
                searchField: 'text'
            });
            tomSelects.honor_lebih_dari_4jt = new TomSelect('#honor_lebih_dari_4jt', {
                placeholder: 'Pilih',
                searchField: 'text'
            });
            tomSelects.jenis_kelamin = new TomSelect('#jenis_kelamin', {
                placeholder: 'Pilih Jenis Kelamin',
                searchField: 'text'
            });
            tomSelects.mode_export = new TomSelect('#mode_export', {
                placeholder: 'Pilih Mode',
                searchField: 'text'
            });

            // Ambil elemen form dan select
            const filterForm = document.getElementById('filterForm');
            const selects = {
                mode_export: document.getElementById('mode_export'),
                tahun: document.getElementById('tahun'),
                bulan: document.getElementById('bulan'),
                status_mitra: document.getElementById('status_mitra'),
                status_pekerjaan: document.getElementById('status_pekerjaan'),
                kecamatan: document.getElementById('kecamatan'),
                nama_mitra: document.getElementById('nama_mitra'),
                partisipasi_lebih_dari_satu: document.getElementById('partisipasi_lebih_dari_satu'),
                honor_lebih_dari_4jt: document.getElementById('honor_lebih_dari_4jt'),
                jenis_kelamin: document.getElementById('jenis_kelamin')
            };

            // Fungsi utama untuk mengatur status aktif/nonaktif filter
            function updateFilterStates() {
                const mode = selects.mode_export.value;
                const tahunSelected = selects.tahun.value !== '';
                const bulanSelected = selects.bulan.value !== '';

                const bulanTomSelect = tomSelects.bulan;
                const partisipasiTomSelect = tomSelects.partisipasi_lebih_dari_satu;
                const honorTomSelect = tomSelects.honor_lebih_dari_4jt;

                if (mode === 'detail') {
                    // Aturan untuk filter Bulan: Aktif jika Tahun dipilih
                    if (tahunSelected) {
                        bulanTomSelect.enable();
                    } else {
                        bulanTomSelect.disable();
                        if (bulanSelected) {
                            bulanTomSelect.clear();
                        }
                    }

                    // Aturan untuk filter Honor & Partisipasi: Aktif jika Tahun DAN Bulan dipilih
                    if (tahunSelected && bulanSelected) {
                        partisipasiTomSelect.enable();
                        honorTomSelect.enable();
                    } else {
                        partisipasiTomSelect.disable();
                        honorTomSelect.disable();
                        if (selects.partisipasi_lebih_dari_satu.value !== '') {
                            partisipasiTomSelect.clear();
                        }
                        if (selects.honor_lebih_dari_4jt.value !== '') {
                            honorTomSelect.clear();
                        }
                    }
                } else {
                    // Mode selain 'detail': Nonaktifkan semua filter yang bergantung
                    bulanTomSelect.disable();
                    partisipasiTomSelect.disable();
                    honorTomSelect.disable();

                    // Kosongkan nilainya jika ada
                    if (bulanSelected) bulanTomSelect.clear();
                    if (selects.partisipasi_lebih_dari_satu.value !== '') partisipasiTomSelect.clear();
                    if (selects.honor_lebih_dari_4jt.value !== '') honorTomSelect.clear();
                }
            }

            // Panggil fungsi saat halaman dimuat untuk mengatur state awal
            updateFilterStates();

            // Tambahkan event listener untuk memanggil updateFilterStates setiap ada perubahan
            selects.mode_export.addEventListener('change', updateFilterStates);
            selects.tahun.addEventListener('change', updateFilterStates);
            selects.bulan.addEventListener('change', updateFilterStates);

            // Logika untuk submit form setelah ada perubahan (dengan delay)
            let timeout;

            function submitForm() {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    filterForm.submit();
                }, 500); // Delay 500ms sebelum submit
            }

            // Tambahkan event listener 'change' untuk semua filter agar men-submit form
            Object.values(selects).forEach(select => {
                select.addEventListener('change', submitForm);
            });
        });

        // Fungsi untuk export data (tidak berubah)
        function exportData() {
            const form = document.getElementById('filterForm');
            const formData = new FormData(form);
            const params = new URLSearchParams(formData).toString();
            window.location.href = `/ReportMitra/export-mitra?${params}`;
        }
    </script>


</body>

</html>
