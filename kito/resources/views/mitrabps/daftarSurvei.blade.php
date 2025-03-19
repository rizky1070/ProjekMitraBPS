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
                            <form action="{{ route('surveys.filter') }}" method="GET" class="flex justify-between items-center mb-4">
                                <div class="flex items-center space-x-4">
                                    <!-- Input untuk search -->
                                    <input type="text" name="search" placeholder="Search..." class="px-4 py-2 border border-gray-300 rounded-md">

                                    <!-- Dropdown untuk memilih tahun -->
                                    <select name="tahun" class="px-4 py-2 border border-gray-300 rounded-md">
                                        <option value="">Pilih Tahun</option>
                                        @foreach($availableYears as $year)
                                            <option value="{{ $year }}" @if(request('tahun') == $year) selected @endif>{{ $year }}</option>
                                        @endforeach
                                    </select>

                                    <!-- Dropdown untuk memilih kecamatan -->
                                    <select name="kecamatan" class="px-4 py-2 border border-gray-300 rounded-md">
                                        <option value="">Pilih Kecamatan</option>
                                        @foreach($kecamatans as $kecamatan)
                                            <option value="{{ $kecamatan->id_kecamatan }}" @if(request('kecamatan') == $kecamatan->id_kecamatan) selected @endif>{{ $kecamatan->nama_kecamatan }}</option>
                                        @endforeach
                                    </select>
                            
                                    <!-- Tombol filter -->
                                    <button class="px-4 py-2 bg-orange rounded-md">Filter</button>
                                </div>
                                <button class="px-4 py-2 bg-orange rounded-md">+ Tambah</button>
                                <!-- Menambahkan ml-auto untuk memindahkan tombol Tambah ke kanan -->
                                {{-- <form action="{{ route('surveys.import') }}" method="POST" enctype="multipart/form-data" class="flex justify-between items-center mb-4">
                                    @csrf
                                    <div class="flex items-center space-x-4"> --}}
                                        <!-- Tombol tambah untuk input file excel -->
                                        {{-- <label for="excel_file" class="px-4 py-2 bg-orange text-black rounded-md ml-auto cursor-pointer">+ Tambah</label> --}}
                                        {{-- <input type="file" id="excel_file" name="excel_file" class="hidden" accept=".xlsx, .xls"> --}}
                                        {{-- <input type="file" class="form-control @error('filexls') is-invalid @enderror" name="filexls">
                                        @error('filexls')
                                            <p style="color: red;">{{ $message }}</p>
                                        @enderror --}}
                                        {{-- <button cclass="px-4 py-2 bg-orange rounded-md">tambah</button> --}}
                                    {{-- </div>
                                </form> --}}
                                {{-- <button class="px-4 py-2 bg-orange text-black rounded-md ml-auto">+ Tambah</button> --}}
                            </div>

                            <!-- List of Survei -->
                            <div class="space-y-4">
                                @foreach($surveys as $survey)
                                <div class="flex justify-between  bg-white items-center p-4 border border-gray-300 rounded-md">
                                    <div>
                                        <h3 class="text-xl font-semibold">{{ $survey->nama_survei }}</h3>
                                        <p class="text-gray-700">{{ $survey->kecamatan->nama_kecamatan ?? 'Tidak Tersedia' }}</p>
                                        <p class="text-gray-700">Jadwal Kegiatan : {{ $survey->jadwal_kegiatan }}</p>
                                        <p class="text-gray-700">Jumlah Mitra : {{ $survey->mitra_survei_count }}</p> <!-- Menampilkan jumlah mitra -->
                                    </div>
                                    <div class="flex flex-col items-end space-y-2">
                                        <!-- Menempatkan status survei di atas tombol -->
                                        <h3 class="text-xl font-semibold">
                                            @if($survey->status_survei == 1)
                                                <div class="bg-red-500 text-white rounded-md px-4 py-1">Belum Dikerjakan</div>
                                            @elseif($survey->status_survei == 2)
                                                <div class="bg-yellow-300 text-white rounded-md px-4 py-1">Sedang Dikerjakan</div>
                                            @elseif($survey->status_survei == 3)
                                                <div class="bg-green-500 text-white rounded-md px-4 py-1">Sudah Dikerjakan</div>
                                            @else
                                                Status Tidak Diketahui
                                            @endif
                                        </h3>
                                        <div class="flex space-x-4">
                                            <a href="/editSurvei/{{ $survey->id_survei }}" class="px-4 py-2 bg-orange text-black rounded-md">Pilij</a>
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