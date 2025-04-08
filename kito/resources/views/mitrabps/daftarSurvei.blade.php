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

    <title>Daftar Survei BPS</title>
</head>
<body class="h-full">
    @if (session('success'))
    <script>
    swal("Success!", "{{ session('success') }}", "success");
    </script>
    @endif

    @if ($errors->any())
    <script>
    swal("Error!", "{{ implode(', ', $errors->all()) }}", "error");
    </script>
    @endif

        <!-- component -->
        <div x-data="{ sidebarOpen: false }" class="flex h-screen">
            <x-sidebar></x-sidebar>
            <div class="flex flex-col flex-1 overflow-hidden">
                <x-navbar></x-navbar>
                <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200">
                    <div class="container px-6 py-8 mx-auto">
                        <h3 class="text-3xl font-medium text-black">Daftar Survei</h3>
                        <div class="flex justify-end">
                            <a href="{{ route('inputSurvei') }}" class="px-4 py-2 bg-orange text-black rounded-md hover:bg-green-600 transition duration-300">
                                + Tambah Survei
                            </a>
                        </div>
                        <div class="p-4">
                            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                                <!-- Header dengan tombol Tambah Survei -->
                                <div class="items-center mb-4">
                                    <h2 class="text-lg font-semibold text-gray-800">Filter Survei</h2>
                                </div>
                                <!-- Form Filter -->
                                <form action="{{ route('surveys.filter') }}" method="GET" class="space-y-4" id="filterForm">
                                    <!-- Year Row -->
                                    <div class="flex items-center">
                                        <label for="tahun" class="w-32 text-sm font-medium text-gray-700">Tahun</label>
                                        <select name="tahun" id="tahun" class="w-64 border rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 ml-2">
                                            <option value="">Semua Tahun</option>
                                            @foreach($tahunOptions as $year => $yearLabel)
                                                <option value="{{ $year }}" @if(request('tahun') == $year) selected @endif>{{ $yearLabel }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                        <!-- Month Row -->
                                    <div class="flex items-center">
                                        <label for="bulan" class="w-32 text-sm font-medium text-gray-700">Bulan</label>
                                        <select name="bulan" id="bulan" class="w-64 border rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 ml-2" {{ empty($bulanOptions) ? 'disabled' : '' }}>
                                            <option value="">Semua Bulan</option>
                                            @foreach($bulanOptions as $month => $monthName)
                                                <option value="{{ $month }}" @if(request('bulan') == $month) selected @endif>{{ $monthName }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                        <!-- District Row -->
                                    <div class="flex items-center">
                                        <label for="kecamatan" class="w-32 text-sm font-medium text-gray-700">Kecamatan</label>
                                        <select name="kecamatan" id="kecamatan" class="w-64 border rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 ml-2" {{ empty($kecamatanOptions) ? 'disabled' : '' }}>
                                            <option value="">Semua Kecamatan</option>
                                            @foreach($kecamatanOptions as $kecam)
                                                <option value="{{ $kecam->id_kecamatan }}" @if(request('kecamatan') == $kecam->id_kecamatan) selected @endif>
                                                    [{{ $kecam->kode_kecamatan }}] {{ $kecam->nama_kecamatan }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                        <!-- Survey Name Row -->
                                    <div class="flex items-center">
                                        <label for="nama_survei" class="w-32 text-sm font-medium text-gray-700">Nama Survei</label>
                                        <select name="nama_survei" id="nama_survei" class="w-64 border rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 ml-2" {{ empty($namaSurveiOptions) ? 'disabled' : '' }}>
                                            <option value="">Semua Survei</option>
                                            @foreach($namaSurveiOptions as $nama => $label)
                                                <option value="{{ $nama }}" @if(request('nama_survei') == $nama) selected @endif>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- JavaScript Tom Select -->
                        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
                        <!-- Inisialisasi Tom Select -->
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                new TomSelect('#nama_survei', {
                                    placeholder: 'Pilih Survei',
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
                                const surveiSelect = document.getElementById('nama_survei');

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
                                surveiSelect.addEventListener('change', submitForm);
                            });
                        </script>
                        <!-- List of Survei -->
                        <div class="flex overflow-x-auto space-x-6 p-4">
                        @foreach($surveys as $survey)
                            <div class="bg-white h-[600px] min-w-[350px] p-6 border border-gray-300 rounded-lg shadow-md flex-shrink-0 space-y-4 flex flex-col">
                                <!-- Informasi Survei -->
                                <div class="flex-grow">
                                    <h3 class="text-2xl font-bold text-gray-800">{{ $survey->nama_survei }}</h3>
                                    <!-- Status -->
                                    <div class="mt-auto">
                                        <div class="text-lg font-semibold mb-2">
                                            @if($survey->status_survei == 1)
                                                <span class="text-red-500">Belum Dikerjakan</span>
                                            @elseif($survey->status_survei == 2)
                                                <span class="text-yellow-500">Sedang Dikerjakan</span>
                                            @elseif($survey->status_survei == 3)
                                                <span class="text-green-600">Sudah Dikerjakan</span>
                                            @else
                                                <span class="text-gray-500">Status Tidak Diketahui</span>
                                            @endif
                                        </div>
                                    </div>
                                    <span class="text-gray-600">
                                        Kecamatan : {{ $survey->kecamatan->nama_kecamatan ?? 'Tidak Tersedia' }}
                                    </span> <br>
                                    <span class="text-gray-600">
                                        Jadwal Kegiatan : {{ \Carbon\Carbon::parse($survey->jadwal_kegiatan)->translatedFormat('j F Y') }}
                                    </span> <br>
                                    <span class="text-gray-600">
                                        Jumlah Mitra:
                                        @if($survey->mitraSurvei->isNotEmpty())
                                            {{ $survey->mitraSurvei->count() }}<br>

                                            <div class="mt-2 max-h-[350px] overflow-y-auto pr-2">
                                                @foreach($survey->mitraSurvei as $mitraName)
                                                    <div class="text-gray-600">- {{ $mitraName->nama_lengkap }}</div>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-red-500 font-semibold">Tidak ada mitra</span>
                                        @endif
                                    </span>
                                </div>
                                <div class="mt-4">
                                    <a href="/editSurvei/{{ $survey->id_survei }}" class="px-4 py-2 bg-orange text-black rounded-md">Pilih</a>
                                </div>
                            </div>
                        @endforeach
                        </div>
                    @include('components.pagination', ['paginator' => $surveys])
                    </div>
                </main>
            </div>
        </div>
</body>
</html>