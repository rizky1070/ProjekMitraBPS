<?php
$title = 'Profil Mitra';
?>
@include('mitrabps.headerTemp')
    @include('mitrabps.cuScroll')
</head>

<body class="h-full bg-gray-200">
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
    <main class="cuScrollGlobalY flex-1 overflow-x-hidden bg-gray-200">
        <a href="{{ url('/daftarMitra') }}"
            class="inline-flex items-center gap-2 px-4 py-2 bg-oren hover:bg-orange-500 text-black font-semibold rounded-br-md transition-all duration-200 shadow-md">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <div class="max-w-4xl mx-auto mt-4">
            <h1 class="text-2xl font-bold">Profil Mitra</h1>
            <div class="flex flex-col md:flex-row items-center bg-white my-4 px-6 py-5 rounded-lg shadow">
                <div class="flex flex-col justify-center items-center text-center mb-4 md:mb-0">
                    <img alt="Profile picture" class="w-32 rounded-full border-4 object-cover {{ $mits->jenis_kelamin == 2 ? 'border-pink-500' : 'border-blue-500' }}"
                        src="{{ $profileImage }}"
                        onerror="this.onerror=null;this.src='{{ asset('person.png') }}'"
                        width="100" height="100">

                    <h2 class="text-xl font-bold mt-2">{{ $mits->nama_lengkap }}</h2>
                    <h5 class=" my-2">{{ $mits->sobat_id }}</h5>
                    <!-- Delete Button -->
                    <form action="{{ route('mitra.destroy', $mits->id_mitra) }}" method="POST"
                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus mitra {{ $mits->nama_lengkap }}? SEMUA DATA YANG TERKAIT AKAN DIHAPUS PERMANEN.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="bg-red-500 text-white font-medium px-3 py-1 rounded-md hover:bg-red-600 transition-all duration-200"
                            aria-label="Hapus mitra" title="Hapus permanen mitra ini">
                            Hapus
                        </button>
                    </form>
                </div>
                <div class="md:pl-6 w-full">
                    <div class="flex justify-between w-full border-b py-1">
                        <strong>Domisili :</strong>
                        <span class="text-right">{{ $mits->kecamatan->nama_kecamatan }}, {{ $mits->kabupaten->nama_kabupaten }}</span>
                    </div>
                    <div class="flex justify-between w-full border-b py-1">
                        <strong>Alamat Detail :</strong>
                        <span class="text-right">{{ $mits->alamat_mitra }}</span>
                    </div>
                    <div class="flex justify-between w-full border-b py-1">
                        <strong>Nomor Handphone :</strong>
                        <span class="text-right">{{ $mits->no_hp_mitra }}</span>
                    </div>
                    <div class="flex justify-between w-full border-b py-1">
                        <strong>Email :</strong>
                        <span class="text-right">{{ $mits->email_mitra }}</span>
                    </div>
                    <div class="flex justify-between w-full border-b py-1">
                        <strong>Masa Kontrak :</strong>
                        <span class="text-right">{{ \Carbon\Carbon::parse($mits->tahun)->translatedFormat('j F Y') }} -
                            {{ \Carbon\Carbon::parse($mits->tahun_selesai)->translatedFormat('j F Y') }}</span>
                    </div>
                    <div class="flex justify-between w-full border-b py-1">
                        <strong>Pekerjaan :</strong>
                        <div class="flex items-center gap-2">
                            <!-- Form untuk update detail pekerjaan -->
                            <form action="{{ route('mitra.updateDetailPekerjaan', $mits->id_mitra) }}" method="POST"
                                class="flex flex-col md:flex-row items-stretch md:items-center gap-2 w-full">
                                @csrf
                                @method('PUT')
                                <!-- Input detail pekerjaan -->
                                <div class="relative flex-1">
                                    <input type="text" name="detail_pekerjaan" value="{{ $mits->detail_pekerjaan }}"
                                        class="border-0 px-2 py-1 text-right w-full md:w-64 focus:outline-none focus:ring-2 focus:ring-orange-500"
                                        placeholder="Masukkan detail pekerjaan" title="Ubah detail pekerjaan">
                                </div>

                                <!-- Button submit untuk detail pekerjaan -->
                                <button type="submit"
                                    class="bg-oren text-white px-3 py-1 rounded-md font-medium hover:bg-orange-500 hover:shadow-lg transition-all duration-300 w-full md:w-auto"
                                    aria-label="Simpan detail pekerjaan" title="Klik untuk menyimpan detail pekerjaan">
                                    Simpan
                                </button>
                            </form>

                            <!-- Form untuk update status -->
                        </div>
                    </div>
                    <div class="flex justify-between w-full border-b py-1">
                        <strong>Status :</strong>
                        <form action="{{ route('mitra.updateStatus', $mits->id_mitra) }}" method="POST">
                            @csrf
                            @method('PUT')
                            @php
                                $isActive = $mits->status_pekerjaan == 1;
                                $colorClasses = $isActive
                                    ? 'bg-red-500'
                                    : 'bg-green-500';
                                $statusText = $isActive ? 'Non-Aktif' : 'Aktif';
                            @endphp

                            <!-- Button untuk status -->
                            <button type="submit"
                                class="{{ $colorClasses }} hover:{{ $isActive ? 'bg-red-600' : 'bg-green-600' }} transition-all duration-300 relative group mt-4 px-4 py-2 text-white font-medium rounded-md"
                                aria-label="Ubah status pekerjaan" title="Klik untuk mengubah status">
                                {{ $statusText }}
                            </button>

                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Survei -->
        <div class="max-w-4xl mx-auto">
            <h2 class="text-xl font-bold mb-4">Survei yang diikuti mitra</h2>
            <div id="survei-dikerjakan" class="cuScrollFilter bg-white rounded-lg shadow-sm p-6 mb-6">
                <!-- Form Filter -->
                <form method="GET" action="{{ route('profilMitra.filter', ['id_mitra' => $mits->id_mitra, 'scroll_to' => request('scroll_to')]) }}" 
                    class="flex flex-wrap gap-4 items-center mb-2" id="filterForm">
                    <!-- Survey Name Row -->
                    <div class="flex flex-col md:flex-row items-start md:items-center">
                        <label for="nama_survei" class="w-full md:w-32 text-sm md:text-lg font-semibold text-gray-800 mb-1 md:mb-0">Cari Survei</label>
                        <select name="nama_survei" id="nama_survei"
                            class="w-full md:w-64 border rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 md:ml-2"
                            {{ empty($namaSurveiOptions) ? 'disabled' : '' }}>
                            <option value="">Semua Survei</option>
                            @foreach($namaSurveiOptions as $nama => $label)
                                <option value="{{ $nama }}" @if(request('nama_survei') == $nama) selected @endif>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-lg font-semibold text-gray-800">Filter Survei</h2>
                        </div>
                        <div class="flex">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-x-6 gap-y-4 w-full">
                                <!-- Year Row -->
                                <div class="flex items-center">
                                    <label for="tahun"
                                        class="w-full md:w-32 text-sm font-medium text-gray-700">Tahun</label>
                                    <select name="tahun" id="tahun"
                                        class="w-64 border rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 ml-2">
                                        <option value="">Semua Tahun</option>
                                        @foreach($tahunOptions as $year => $yearLabel)
                                            <option value="{{ $year }}" @if(request('tahun') == $year) selected @endif>
                                                {{ $yearLabel }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Month Row -->
                                <div class="flex items-center">
                                    <label for="bulan"
                                        class="w-full md:w-32 text-sm font-medium text-gray-700">Bulan</label>
                                    <select name="bulan" id="bulan"
                                        class="w-64 border rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500 ml-2"
                                        {{ empty($bulanOptions) ? 'disabled' : '' }}>
                                        <option value="">Semua Bulan</option>
                                        @foreach($bulanOptions as $month => $monthName)
                                            <option value="{{ $month }}" @if(request('bulan') == $month) selected @endif>
                                                {{ $monthName }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="pl-4 mb-2">
                    @if($showTotalGaji)
                        <p class="font-bold">Total Gaji Mitra Bulan ini:</p>
                        <p class="text-xl font-bold">Rp {{ number_format($totalGaji, 0, ',', '.') }},00</p>
                    @else
                        <p class="font-sm text-gray-500">*Aktifkan filter bulan untuk melihat total gaji</p>
                    @endif
                </div>
                <div class="bg-white p-4 border border-gray-300 rounded-lg shadow-lg">
                <!-- Survei yang sudah dikerjakan -->
                <div class="overflow-x-auto mb-4 pb-4">
                    <h2 class="text-lg font-semibold text-gray-800">Survei yang sudah dikerjakan:</h2>
                    @php
                        $survei_dikerjakan = $survei->filter(fn($s) => $s->survei->status_survei == 3);
                    @endphp
                    @if($survei_dikerjakan->isEmpty())
                        <h2 class="text-l text-gray-600 pl-4">Tidak ada survei yang sudah dikerjakan</h2>
                    @else
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Survei</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Jadwal Survei</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Vol</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Rate Honor</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($survei_dikerjakan as $sur)
                                <tr class="hover:bg-gray-50" style="border-top-width: 2px; border-color: #D1D5DB;">
                                    <td class="text-sm font-medium text-gray-900 whitespace-normal break-words" style="max-width: 120px;">
                                        <div class="ml-3 flex justify-center items-center text-center">
                                            <a href="/editSurvei/{{ $sur->survei->id_survei }}" class="hover:underline transition duration-300 ease-in-out" style="text-decoration-color: #FFA500; text-decoration-thickness: 3px;" >
                                                {{ $sur->survei->nama_survei }}
                                            </a>
                                        </div>
                                    </td>
                                    <td class="text-center whitespace-normal break-words" style="max-width: 200px;">
                                        {{ \Carbon\Carbon::parse($sur->survei->jadwal_kegiatan)->translatedFormat('j F Y') }} -
                                        {{ \Carbon\Carbon::parse($sur->survei->jadwal_berakhir_kegiatan)->translatedFormat('j F Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">{{ $sur->vol ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">Rp{{ number_format($sur->posisiMitra->rate_honor ?? 0, 0, ',', '.') }}</td>
                                    @if($sur->catatan == null && $sur->nilai == null)
                                        <td class="p-2 text-center text-red-700 font-bold">Tidak ada catatan</td>
                                        <td class="p-2 text-center text-red-700 font-bold">Belum dinilai</td>
                                    @else
                                        <td class="px-6 py-4 whitespace-nowrap text-center">{{ $sur->catatan }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">{{ str_repeat('⭐', $sur->nilai) }}</td>
                                    @endif
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <a href="/penilaianMitra/{{ $sur->mitra->id_mitra }}/{{ $sur->survei->id_survei }}" class="px-4 py-1 bg-oren rounded-md text-white font-medium hover:bg-orange-500 hover:shadow-lg transition-all duration-300">Edit</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>

                <!-- Survei yang belum/sedang dikerjakan -->
                <div class="overflow-x-auto pt-4" style="border-top-width: 2px; border-color: #9CA3AF;">
                    <h2 class="text-lg font-semibold text-gray-800">Survei yang belum/sedang dikerjakan:</h2>
                    @php
                        $survei_belum = $survei->filter(fn($s) => $s->survei->status_survei != 3);
                    @endphp
                    @if($survei_belum->isEmpty())
                        <h2 class="text-l text-gray-600 pl-5">Tidak ada survei yang belum/sedang dikerjakan</h2>
                    @else
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Survei</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Jadwal Survei</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Vol</th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Rate Honor</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($survei_belum as $sur)
                                <tr class="hover:bg-gray-50" style="border-top-width: 2px; border-color: #D1D5DB;">
                                    <td class="text-sm font-medium text-gray-900 whitespace-normal break-words" style="max-width: 120px;">
                                        <div class="ml-3 flex justify-center items-center text-center">
                                            <a href="/editSurvei/{{ $sur->survei->id_survei }}" class="hover:underline transition duration-300 ease-in-out" style="text-decoration-color: #FFA500; text-decoration-thickness: 3px;">
                                                {{ $sur->survei->nama_survei }}
                                            </a>
                                        </div>
                                    </td>
                                    <td class="text-center whitespace-normal break-words" style="max-width: 200px;">
                                        {{ \Carbon\Carbon::parse($sur->survei->jadwal_kegiatan)->translatedFormat('j F Y') }} -
                                        {{ \Carbon\Carbon::parse($sur->survei->jadwal_berakhir_kegiatan)->translatedFormat('j F Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">{{ $sur->vol ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">Rp{{ number_format($sur->posisiMitra->rate_honor ?? 0, 0, ',', '.') }}</td>
                                   
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

            </div>
        </div>

        <!-- JavaScript Tom Select -->
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
        <!-- Inisialisasi Tom Select -->
        <script>
            document.addEventListener('DOMContentLoaded', function () {
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

        </div>
    </main>


</body>
@if(request('scroll_to') == 'survei-dikerjakan')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Cari elemen yang ingin di-scroll
        const element = document.querySelector('.cuScrollFilter');
        if (element) {
            // Scroll ke elemen dengan offset untuk header
            window.scrollTo({
                top: element.offsetTop - 100,
                behavior: 'smooth'
            });
        }
    });
</script>
@endif
<!-- ⭐⭐⭐⭐⭐ -->

</html>