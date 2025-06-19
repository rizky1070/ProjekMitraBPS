<?php
$title = 'Daftar Survei';
?>
@include('mitrabps.headerTemp')
    @include('mitrabps.cuScroll')
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
                <main class="cuScrollGlobalY flex-1 overflow-x-hidden bg-gray-200">
                    <div class="container px-4 py-4 mx-auto">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-3xl font-medium text-black">Daftar Survei</h3>
                            @if (auth()->user()->is_admin || auth()->user()->is_leader)
                            <button type="button" class="px-4 py-2 bg-oren rounded-md text-white font-medium hover:bg-orange-500 hover:shadow-lg transition-all duration-300"><a href="/inputSurvei"> + Tambah</a></button>
                            @endif
                        </div>
                        <div>
                            <div class="cuScrollFilter bg-white rounded-lg shadow-sm p-6 mb-6">
                                <!-- Form Filter -->
                                <form action="{{ route('surveys.filter') }}" method="GET" class="space-y-4" id="filterForm">
                                    <!-- Survey Name Row -->
                                    <div class="flex items-center">
                                        <label for="nama_survei" class="w-32 text-lg font-semibold text-gray-800">Cari Survei</label>
                                        <select name="nama_survei" id="nama_survei" class="w-64 border rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 ml-2" {{ empty($namaSurveiOptions) ? 'disabled' : '' }}>
                                            <option value="">Semua Survei</option>
                                            @foreach($namaSurveiOptions as $nama => $label)
                                                <option value="{{ $nama }}" @if(request('nama_survei') == $nama) selected @endif>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="items-center mb-4">
                                        <h2 class="text-lg font-semibold text-gray-800">Filter Survei</h2>
                                    </div>
                                    <div class="flex">
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-x-6 gap-y-4 w-full">
                                            <!-- Year Row -->
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
                                                <select name="bulan" id="bulan" class="w-full md:w-64 border rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 ml-2" {{ empty($bulanOptions) ? 'disabled' : '' }}>
                                                    <option value="">Semua Bulan</option>
                                                    @foreach($bulanOptions as $month => $monthName)
                                                        <option value="{{ $month }}" @if(request('bulan') == $month) selected @endif>{{ $monthName }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
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
                                    placeholder: 'Cari Survei',
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

                                // Auto submit saat filter berubah
                                const filterForm = document.getElementById('filterForm');
                                const tahunSelect = document.getElementById('tahun');
                                const bulanSelect = document.getElementById('bulan');
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
                                surveiSelect.addEventListener('change', submitForm);
                            });
                        </script>
                        <!-- List of Survei -->
                        <div class="cuScrollGlobalX flex space-x-6 pb-4">
                        @foreach($surveys as $survey)
                            <div class="bg-white h-[450px] min-w-[350px] max-w-[350px] p-4 border border-gray-300 rounded-lg shadow-md flex-shrink-0 space-y-4 flex flex-col overflow-hidden">
                                <!-- Informasi Survei -->
                                <div class="flex-grow overflow-hidden">
                                    <h3 class="pl-2 text-2xl font-bold text-gray-800 truncate whitespace-nowrap overflow-hidden hover:text-black hover:scale-105 transition-all duration-300 ease-in-out transform">
                                        <a href="/editSurvei/{{ $survey->id_survei }}">{{ $survey->nama_survei }}</a>
                                    </h3>

                                    <!-- Status -->
                                    <div class="ml-2 mt-auto text-lg font-semibold mb-2">
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

                                    <span class="ml-2 text-gray-600 block">
                                        <strong>Jadwal Kegiatan: </strong></br>
                                        {{ \Carbon\Carbon::parse($survey->jadwal_kegiatan)->translatedFormat('j F Y') }} - {{ \Carbon\Carbon::parse($survey->jadwal_berakhir_kegiatan)->translatedFormat('j F Y') }}
                                    </span>

                                    <!-- Jumlah Mitra -->
                                    <span class="ml-2 text-gray-600 block">
                                        @if($survey->mitraSurveis->isNotEmpty())
                                        <strong>Jumlah Mitra: </strong> {{ $survey->mitraSurveis->count() }}<br>
                                        <div class="cuScrollFilter mt-2 max-h-[250px] overflow-y-auto pr-2 space-y-1">
                                            @php
                                                // Urutkan mitra berdasarkan total survei (desc)
                                                $sortedMitras = $survey->mitraSurveis->sortByDesc(function($mitraSurvei) use ($mitraHighlight) {
                                                    return $mitraHighlight[$mitraSurvei->id_mitra] ?? 0;
                                                });
                                            @endphp

                                            @foreach($sortedMitras as $mitraSurvei)
                                                @php
                                                    $totalSurvei = $mitraHighlight[$mitraSurvei->id_mitra] ?? 0;
                                                    $textColor = match(true) {
                                                        $totalSurvei > 3 => 'text-red-600',
                                                        $totalSurvei > 1 => 'text-yellow-600',
                                                        default => 'text-gray-500'
                                                    };
                                                @endphp

                                                <div class="{{ $textColor }} ml-2 pl-1 rounded-md transition-all duration-300 ease-in-out transform hover:bg-oren hover:text-white hover:scale-105 line-clamp-2">
                                                    <a href="/profilMitra/{{ $mitraSurvei->mitra->id_mitra ?? '#' }}" class="truncate inline-block max-w-full align-top">
                                                        - {{ $mitraSurvei->mitra->nama_lengkap ?? 'Nama Tidak Ditemukan' }}
                                                        @if(request()->filled('tahun') || request()->filled('bulan'))
                                                            ({{ $totalSurvei }} survei)
                                                        @endif
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                        @else
                                            <span class="text-red-500 font-semibold">Tidak ada mitra</span>
                                        @endif
                                    </span>
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