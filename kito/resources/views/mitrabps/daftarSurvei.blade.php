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
                        <h3 class="text-3xl font-medium text-black">Daftar Survei</h3>
                        <div class="p-6">
                            <!-- Search Bar and Filter -->
                            <div x-data="{ isOpen: false }">
                                <!-- Dropdown untuk memilih nama survei di luar modal -->
                                <div class="flex justify-between items-center mb-4">
                                    <div class="flex items-center space-x-4">
                                        <form action="{{ route('surveys.filter') }}" method="GET" class="flex items-center space-x-4">
                                            <div>
                                                <select name="nama_survei" id="nama_survei" class="w-64 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                                                    <option value="">Pilih Nama Survei</option>
                                                    @foreach($namaSurvei as $survei)
                                                        <option value="{{ $survei->nama_survei }}" @if(request('nama_survei') == $survei->nama_survei) selected @endif>{{ $survei->nama_survei }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </form>
                                        <button @click="isOpen = true" class="px-4 py-2 bg-orange text-white rounded-md hover:bg-orange-600 transition duration-300">
                                            Filter
                                        </button>
                                    </div>
                                    <a href="{{ route('inputSurvei') }}" class="px-4 py-2 bg-orange text-white rounded-md hover:bg-orange-600 transition duration-300">
                                        + Tambah Survei
                                    </a>
                                </div>

                                <!-- Modal -->
                                <div x-show="isOpen" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
                                    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-lg">
                                        <h2 class="text-xl font-bold mb-6 text-gray-800">Filter</h2>

                                        <!-- Form Filter -->
                                        <form action="{{ route(name: 'surveys.filter') }}" method="GET" class="space-y-4">
                                            <!-- Dropdown untuk memilih tahun -->
                                            <div>
                                                <select name="tahun" id="tahun" class="w-full px-4 py-2  rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                                                    <option value="">Pilih Tahun</option>
                                                    @foreach($availableYears as $year)
                                                        <option value="{{ $year }}" @if(request('tahun') == $year) selected @endif>{{ $year }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <!-- Dropdown untuk memilih bulan -->
                                            <div>
                                                <select name="bulan" id="bulan" class="w-full px-4 py-2  rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                                                    <option value="">Pilih Bulan</option>
                                                    @foreach(['01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'] as $month => $monthName)
                                                        <option value="{{ $month }}" @if(request('bulan') == $month) selected @endif>{{ $monthName }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <!-- Dropdown untuk memilih kecamatan -->
                                            <div>
                                                <select name="kecamatan" id="kecamatan" class="w-full px-4 py-2  rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                                                    <option value="">Pilih Kecamatan</option>
                                                    @foreach($kecamatans as $kecamatan)
                                                        <option value="{{ $kecamatan->id_kecamatan }}" @if(request('kecamatan') == $kecamatan->id_kecamatan) selected @endif>{{ $kecamatan->nama_kecamatan }}</option>
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

                            });
                        </script>
                            <!-- List of Survei -->
                            <div class="space-y-4">
                                @foreach($surveys as $survey)
                                <div class="flex justify-between  bg-white items-center p-4 border border-gray-300 rounded-md">
                                    <div>
                                        <h3 class="text-xl font-semibold">{{ $survey->nama_survei }}</h3>
                                        <p class="text-gray-700">{{ $survey->kecamatan->nama_kecamatan ?? 'Tidak Tersedia' }}</p>
                                        <p class="text-gray-700">Jadwal Kegiatan : {{ \Carbon\Carbon::parse($survey->jadwal_kegiatan)->translatedFormat('j F Y') }}</p>
                                        <p class="text-gray-700">Jumlah Mitra : 
                                            @if($survey->mitraSurvei->isNotEmpty())
                                                {{ $survey->mitraSurvei->count() }}
                                            @else
                                                <strong class="text-red-500">Tidak ada mitra</strong>
                                            @endif
                                        </p> <!-- Menampilkan jumlah mitra -->
                                        <!-- Menampilkan nama mitra yang mengikuti lebih dari satu survei di bulan yang dipilih -->
                                        @if(isset($mitraWithMultipleSurveysInMonth) && $survey->mitraSurvei->isNotEmpty())
                                            @php
                                                // Ambil semua id_mitra dari $mitraWithMultipleSurveysInMonth
                                                $mitraIds = $mitraWithMultipleSurveysInMonth->pluck('id_mitra');

                                                // Mengumpulkan nama mitra yang mengikuti lebih dari satu survei di bulan ini
                                                $mitraNames = $survey->mitraSurvei->filter(function($mitra) use ($mitraIds) {
                                                    return $mitraIds->contains($mitra->id_mitra);
                                                })->pluck('nama_lengkap')->toArray();

                                                // Gabungkan nama mitra menjadi string, jika ada
                                                $mitraText = count($mitraNames) > 0 ? implode(', ', $mitraNames) : '';
                                            @endphp

                                            @if($mitraText)
                                                <p class="text-gray-700">Mitra yang mengikuti lebih dari 1 survei di bulan ini : {{ $mitraText }}</p>
                                            @endif
                                        @endif


                                    </div>
                                    <div class="flex flex-col items-end space-y-2">
                                        <!-- Menempatkan status survei di atas tombol -->
                                        <h3 class="text-xl font-semibold">
                                            @if($survey->status_survei == 1)
                                                <div class="text-red-500 rounded-md px-4 py-1">Belum Dikerjakan</div>
                                            @elseif($survey->status_survei == 2)
                                                <div class="text-yellow-300 rounded-md px-4 py-1">Sedang Dikerjakan</div>
                                            @elseif($survey->status_survei == 3)
                                                <div class="text-green-500 rounded-md px-4 py-1">Sudah Dikerjakan</div>
                                            @else
                                                Status Tidak Diketahui
                                            @endif
                                        </h3>
                                        <div class="flex space-x-4">
                                            <a href="/editSurvei/{{ $survey->id_survei }}" class="px-4 py-2 bg-orange text-black rounded-md">Pilih</a>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @include('components.pagination', ['paginator' => $surveys])
                        </div>

                    </div>
                </main>
            </div>
        </div>
</body>
</html>